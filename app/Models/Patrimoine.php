<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PatrimoineAttributionNotification;

class Patrimoine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code_materiel',
        'designation',
        'description',
        'categorie',
        'marque',
        'modele',
        'numero_serie',
        'prix_achat',
        'date_achat',
        'validateur_id',
        'date_validation',
        'utilisateur_id',
        'date_attribution',
        'etat',
        'statut',
        'localisation',
        'observation',
        'facture',
        'fournisseur',
        'duree_garantie_mois',
        'date_fin_garantie',
        'date_modification',
        'sync_source',
    ];

    protected $casts = [
        'date_achat' => 'date',
        'date_validation' => 'date',
        'date_attribution' => 'date',
        'date_fin_garantie' => 'date',
        'date_modification' => 'datetime',
        'last_synced_at' => 'datetime',
        'prix_achat' => 'decimal:2',
    ];

    /**
     * Relations
     */
    
    // Utilisateur actuel du matériel
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    // Validateur de l'achat
    public function validateur()
    {
        return $this->belongsTo(User::class, 'validateur_id');
    }

    /**
     * Scopes
     */
    
    public function scopeDisponible($query)
    {
        return $query->where('statut', 'disponible');
    }

    public function scopeEnUtilisation($query)
    {
        return $query->where('statut', 'en_utilisation');
    }

    public function scopeParCategorie($query, $categorie)
    {
        return $query->where('categorie', $categorie);
    }

    public function scopeNonSynces($query)
    {
        return $query->where('last_synced_at', '<', now()->subHour());
    }

    /**
     * Accessors & Mutators
     */
    
    public function getEstSousGarantieAttribute()
    {
        if (!$this->date_fin_garantie) {
            return false;
        }
        return $this->date_fin_garantie->isFuture();
    }

    public function getAgeEnAnneesAttribute()
    {
        return $this->date_achat ? now()->diffInYears($this->date_achat) : null;
    }

    /**
     * Méthodes métier
     */
    
    public function attribuerA(User $utilisateur)
    {
        // Sauvegarder l'ancien utilisateur pour notification
        $ancienUtilisateur = $this->utilisateur;
        
        $this->update([
            'utilisateur_id' => $utilisateur->id,
            'date_attribution' => now(),
            'statut' => 'en_utilisation',
            'date_modification' => now(),
            'sync_source' => 'web',
        ]);
        
        // Notifier l'ancien utilisateur que le matériel lui a été retiré
        if ($ancienUtilisateur) {
            Notification::send($ancienUtilisateur, new PatrimoineAttributionNotification($this, 'libere'));
        }
        
        // Notifier le nouvel utilisateur
        Notification::send($utilisateur, new PatrimoineAttributionNotification($this, 'attribue'));
    }

    public function liberer()
    {
        // Sauvegarder l'utilisateur actuel pour notification
        $utilisateur = $this->utilisateur;
        
        $this->update([
            'utilisateur_id' => null,
            'date_attribution' => null,
            'statut' => 'disponible',
            'date_modification' => now(),
            'sync_source' => 'web',
        ]);
        
        // Notifier l'utilisateur que le matériel lui a été retiré
        if ($utilisateur) {
            Notification::send($utilisateur, new PatrimoineAttributionNotification($this, 'libere'));
        }
    }

    public function mettreEnMaintenance()
    {
        $this->update([
            'statut' => 'en_maintenance',
            'date_modification' => now(),
            'sync_source' => 'web',
        ]);
    }

    public function reformer()
    {
        $this->update([
            'statut' => 'reforme',
            'date_modification' => now(),
            'sync_source' => 'web',
        ]);
    }

    /**
     * Marquer comme synchronisé
     */
    public function markAsSynced($source = 'mobile')
    {
        $this->update([
            'last_synced_at' => now(),
            'sync_source' => $source,
        ]);
    }

    /**
     * Génération automatique du code matériel
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($patrimoine) {
            if (!$patrimoine->code_materiel) {
                $patrimoine->code_materiel = self::genererCodeMateriel($patrimoine->categorie);
            }

            // Calculer la date de fin de garantie si durée fournie
            if ($patrimoine->duree_garantie_mois && $patrimoine->date_achat) {
                $patrimoine->date_fin_garantie = $patrimoine->date_achat->copy()->addMonths($patrimoine->duree_garantie_mois);
            }

            // Initialiser date_modification
            if (!$patrimoine->date_modification) {
                $patrimoine->date_modification = now();
            }

            // Initialiser sync_source
            if (!$patrimoine->sync_source) {
                $patrimoine->sync_source = 'web';
            }
        });

        static::updating(function ($patrimoine) {
            $patrimoine->date_modification = now();
        });
    }

    /**
     * Générer un code matériel unique
     */
    private static function genererCodeMateriel($categorie)
    {
        $prefixes = [
            'informatique' => 'INFO',
            'mobilier' => 'MOBI',
            'vehicule' => 'VEH',
            'equipement_bureau' => 'EQUIP',
            'autres' => 'AUTRE',
        ];

        $prefix = $prefixes[$categorie] ?? 'MAT';
        $year = date('Y');
        
        // Trouver le dernier numéro pour cette catégorie
        $dernierPatrimoine = self::where('code_materiel', 'LIKE', "{$prefix}-{$year}-%")
            ->orderBy('code_materiel', 'desc')
            ->first();

        if ($dernierPatrimoine) {
            $dernierNumero = (int) substr($dernierPatrimoine->code_materiel, -4);
            $nouveauNumero = $dernierNumero + 1;
        } else {
            $nouveauNumero = 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $year, $nouveauNumero);
    }

    /**
     * Labels pour les énumérations
     */
    public static function getCategorieLabels()
    {
        return [
            'informatique' => 'Matériel Informatique',
            'mobilier' => 'Mobilier',
            'vehicule' => 'Véhicule',
            'equipement_bureau' => 'Équipement de Bureau',
            'autres' => 'Autres',
        ];
    }

    public static function getEtatLabels()
    {
        return [
            'neuf' => 'Neuf',
            'bon' => 'Bon état',
            'moyen' => 'État moyen',
            'mauvais' => 'Mauvais état',
            'en_reparation' => 'En réparation',
            'hors_service' => 'Hors service',
        ];
    }

    public static function getStatutLabels()
    {
        return [
            'disponible' => 'Disponible',
            'en_utilisation' => 'En utilisation',
            'en_maintenance' => 'En maintenance',
            'reforme' => 'Réformé',
        ];
    }
}

