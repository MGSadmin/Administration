<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\DemandeFournitureNotification;
use Illuminate\Support\Facades\Notification;

class DemandeFourniture extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'demandes_fourniture';

    protected $fillable = [
        'numero_demande',
        'demandeur_id',
        'objet',
        'designation',
        'description',
        'type_fourniture',
        'quantite',
        'priorite',
        'justification',
        'budget_estime',
        'statut',
        'validateur_id',
        'date_validation',
        'motif_rejet',
        'commentaire_validateur',
        'acheteur_id',
        'montant_reel',
        'date_commande',
        'date_reception',
        'date_livraison',
        'fournisseur',
        'fournisseur_id',
        'bon_commande',
        'facture',
        'notifier_user_id',
        'notification_envoyee',
        'date_notification',
        'patrimoine_id',
        'patrimoine_cree',
        'observation',
    ];

    protected $casts = [
        'date_validation' => 'date',
        'date_commande' => 'date',
        'date_reception' => 'date',
        'date_livraison' => 'date',
        'date_notification' => 'datetime',
        'budget_estime' => 'decimal:2',
        'montant_reel' => 'decimal:2',
        'notification_envoyee' => 'boolean',
        'patrimoine_cree' => 'boolean',
    ];

    /**
     * Relations
     */
    
    public function demandeur()
    {
        return $this->belongsTo(User::class, 'demandeur_id');
    }

    public function validateur()
    {
        return $this->belongsTo(User::class, 'validateur_id');
    }

    public function acheteur()
    {
        return $this->belongsTo(User::class, 'acheteur_id');
    }

    public function userANotifier()
    {
        return $this->belongsTo(User::class, 'notifier_user_id');
    }

    public function patrimoine()
    {
        return $this->belongsTo(Patrimoine::class);
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    /**
     * Scopes
     */
    
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeValidees($query)
    {
        return $query->where('statut', 'validee');
    }

    public function scopeUrgentes($query)
    {
        return $query->where('priorite', 'urgente');
    }

    public function scopeParDemandeur($query, $demandeurId)
    {
        return $query->where('demandeur_id', $demandeurId);
    }

    /**
     * Méthodes métier
     */
    
    public function valider(User $validateur, $commentaire = null)
    {
        $this->update([
            'statut' => 'validee',
            'validateur_id' => $validateur->id,
            'date_validation' => now(),
            'commentaire_validateur' => $commentaire,
        ]);

        // Créer automatiquement un patrimoine dans gestion-dossier
        $this->creerPatrimoineGestionDossier($validateur);

        // Envoyer notification automatique
        $this->envoyerNotification('validee');
        
        // Notifier le comptable pour achat
        $this->notifierComptablePourAchat();
    }

    public function rejeter(User $validateur, $motif)
    {
        $this->update([
            'statut' => 'rejetee',
            'validateur_id' => $validateur->id,
            'date_validation' => now(),
            'motif_rejet' => $motif,
        ]);

        // Notifier le demandeur
        $this->envoyerNotification('rejetee');
    }

    public function commander($acheteurId, $fournisseur, $montant)
    {
        $this->update([
            'statut' => 'commandee',
            'acheteur_id' => $acheteurId,
            'fournisseur' => $fournisseur,
            'montant_reel' => $montant,
            'date_commande' => now(),
        ]);

        $this->envoyerNotification('commandee');
    }

    public function marquerRecue()
    {
        $this->update([
            'statut' => 'recue',
            'date_reception' => now(),
        ]);

        $this->envoyerNotification('recue');
    }

    public function livrer()
    {
        $this->update([
            'statut' => 'livree',
            'date_livraison' => now(),
        ]);

        // Mettre à jour le patrimoine dans gestion-dossier si créé
        $this->mettreAJourPatrimoineApresLivraison();

        $this->envoyerNotification('livree');
    }

    /**
     * Envoyer notification automatique
     */
    public function envoyerNotification($evenement)
    {
        $usersANotifier = [];

        // Toujours notifier le demandeur
        $usersANotifier[] = $this->demandeur;

        // Notifier l'utilisateur spécifié si défini
        if ($this->notifier_user_id && $this->userANotifier) {
            $usersANotifier[] = $this->userANotifier;
        }

        // Notifier le validateur si la demande est validée/rejetée
        if (in_array($evenement, ['validee', 'rejetee']) && $this->validateur) {
            $usersANotifier[] = $this->validateur;
        }

        // Envoyer les notifications
        foreach (array_unique($usersANotifier) as $user) {
            if ($user) {
                Notification::send($user, new DemandeFournitureNotification($this, $evenement));
            }
        }

        // Marquer comme envoyée
        $this->update([
            'notification_envoyee' => true,
            'date_notification' => now(),
        ]);
    }

    /**
     * Boot
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($demande) {
            if (!$demande->numero_demande) {
                $demande->numero_demande = self::genererNumeroDemande();
            }
        });

        // Envoyer notification lors de la création
        static::created(function ($demande) {
            $demande->envoyerNotification('creee');
        });
    }

    /**
     * Générer un numéro de demande unique
     */
    private static function genererNumeroDemande()
    {
        $year = date('Y');
        $month = date('m');
        
        $derniereDemande = self::where('numero_demande', 'LIKE', "DF-{$year}{$month}-%")
            ->orderBy('numero_demande', 'desc')
            ->first();

        if ($derniereDemande) {
            $dernierNumero = (int) substr($derniereDemande->numero_demande, -4);
            $nouveauNumero = $dernierNumero + 1;
        } else {
            $nouveauNumero = 1;
        }

        return sprintf('DF-%s%s-%04d', $year, $month, $nouveauNumero);
    }

    /**
     * Labels pour les énumérations
     */
    public static function getTypeFournitureLabels()
    {
        return [
            'materiel_informatique' => 'Matériel Informatique',
            'fourniture_bureau' => 'Fourniture de Bureau',
            'mobilier' => 'Mobilier',
            'equipement' => 'Équipement',
            'consommable' => 'Consommable',
            'autres' => 'Autres',
        ];
    }

    public static function getPrioriteLabels()
    {
        return [
            'faible' => 'Faible',
            'normale' => 'Normale',
            'urgente' => 'Urgente',
        ];
    }

    public static function getStatutLabels()
    {
        return [
            'en_attente' => 'En attente',
            'en_cours_validation' => 'En cours de validation',
            'validee' => 'Validée',
            'rejetee' => 'Rejetée',
            'en_cours_achat' => 'En cours d\'achat',
            'commandee' => 'Commandée',
            'recue' => 'Reçue',
            'livree' => 'Livrée',
            'annulee' => 'Annulée',
        ];
    }

    public static function getStatutColors()
    {
        return [
            'en_attente' => 'warning',
            'en_cours_validation' => 'info',
            'validee' => 'success',
            'rejetee' => 'danger',
            'en_cours_achat' => 'primary',
            'commandee' => 'info',
            'recue' => 'success',
            'livree' => 'success',
            'annulee' => 'secondary',
        ];
    }

    /**
     * Créer un patrimoine dans la base de données gestion-dossier
     */
    private function creerPatrimoineGestionDossier(User $validateur)
    {
        try {
            // Connexion à la base de données gestion-dossier
            $gestionDossierDB = \DB::connection('mysql')->getDatabaseName();
            \DB::purge('mysql');
            config(['database.connections.mysql.database' => 'gestion_dossiers']);
            \DB::reconnect('mysql');

            // Créer le patrimoine
            \DB::table('patrimoines')->insert([
                'designation' => $this->designation,
                'type_fourniture' => $this->type_fourniture,
                'etat' => 'neuf',
                'statut' => 'en_attente_achat',
                'validateur_id' => $validateur->id,
                'validateur_nom' => $validateur->name,
                'date_validation' => now(),
                'demande_fourniture_id' => $this->id,
                'numero_demande' => $this->numero_demande,
                'observation' => 'Créé automatiquement depuis la demande de fourniture ' . $this->numero_demande,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $patrimoineId = \DB::getPdo()->lastInsertId();

            // Revenir à la base de données administration
            \DB::purge('mysql');
            config(['database.connections.mysql.database' => 'mgs_administration']);
            \DB::reconnect('mysql');

            // Marquer que le patrimoine a été créé
            $this->update([
                'patrimoine_id' => $patrimoineId,
                'patrimoine_cree' => true,
            ]);

            \Log::info("Patrimoine créé dans gestion-dossier", [
                'demande_id' => $this->id,
                'patrimoine_id' => $patrimoineId,
            ]);

        } catch (\Exception $e) {
            // Revenir à la base de données administration en cas d'erreur
            \DB::purge('mysql');
            config(['database.connections.mysql.database' => 'mgs_administration']);
            \DB::reconnect('mysql');

            \Log::error("Erreur lors de la création du patrimoine", [
                'demande_id' => $this->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Notifier le comptable pour achat
     */
    private function notifierComptablePourAchat()
    {
        // Trouver les utilisateurs avec le rôle comptable
        $comptables = User::role('comptable')->get();

        foreach ($comptables as $comptable) {
            $comptable->notify(new DemandeFournitureNotification($this, 'validee'));
        }
    }

    /**
     * Mettre à jour le patrimoine après livraison
     */
    private function mettreAJourPatrimoineApresLivraison()
    {
        if (!$this->patrimoine_cree || !$this->patrimoine_id) {
            \Log::warning("Tentative de mise à jour patrimoine mais non créé", [
                'demande_id' => $this->id,
            ]);
            return;
        }

        try {
            // Connexion à la base de données gestion-dossier
            $gestionDossierDB = \DB::connection('mysql')->getDatabaseName();
            \DB::purge('mysql');
            config(['database.connections.mysql.database' => 'gestion_dossiers']);
            \DB::reconnect('mysql');

            // Mettre à jour le patrimoine avec les informations d'achat
            \DB::table('patrimoines')
                ->where('id', $this->patrimoine_id)
                ->update([
                    'statut' => 'disponible',
                    'fournisseur_nom' => $this->fournisseur,
                    'fournisseur_id' => $this->fournisseur_id,
                    'prix_achat' => $this->montant_reel,
                    'date_achat' => $this->date_commande,
                    'date_reception' => $this->date_reception,
                    'bon_commande' => $this->bon_commande,
                    'facture' => $this->facture,
                    'updated_at' => now(),
                ]);

            // Revenir à la base de données administration
            \DB::purge('mysql');
            config(['database.connections.mysql.database' => 'mgs_administration']);
            \DB::reconnect('mysql');

            \Log::info("Patrimoine mis à jour après livraison", [
                'demande_id' => $this->id,
                'patrimoine_id' => $this->patrimoine_id,
            ]);

        } catch (\Exception $e) {
            // Revenir à la base de données administration en cas d'erreur
            \DB::purge('mysql');
            config(['database.connections.mysql.database' => 'mgs_administration']);
            \DB::reconnect('mysql');

            \Log::error("Erreur lors de la mise à jour du patrimoine après livraison", [
                'demande_id' => $this->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

