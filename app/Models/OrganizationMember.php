<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrganizationMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'position_id',
        'user_id',
        'name',
        'status',
        'email',
        'phone',
        'photo',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_VACANT = 'VACANT';
    const STATUS_INTERIM = 'INTERIM';
    const STATUS_LICENCIE = 'LICENCIE';
    const STATUS_DEMISSION = 'DEMISSION';
    const STATUS_RETRAITE = 'RETRAITE';

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function conges(): HasMany
    {
        return $this->hasMany(Conge::class);
    }

    public function demandesAbsence(): HasMany
    {
        return $this->hasMany(DemandeAbsence::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(DocumentEmploye::class);
    }

    public function soldeConges(): HasOne
    {
        return $this->hasOne(SoldeConge::class);
    }

    public function historiqueStatuts(): HasMany
    {
        return $this->hasMany(HistoriqueStatutMembre::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->user ? $this->user->name : $this->name;
    }

    /**
     * Marquer le poste comme vacant lors du licenciement
     */
    public function markAsLicencie(string $commentaire = null, $userId = null): void
    {
        $this->changeStatus(
            self::STATUS_LICENCIE,
            HistoriqueStatutMembre::MOTIF_LICENCIEMENT,
            $commentaire,
            $userId
        );
    }

    /**
     * Marquer comme démission
     */
    public function markAsDemission(string $commentaire = null, $userId = null): void
    {
        $this->changeStatus(
            self::STATUS_DEMISSION,
            HistoriqueStatutMembre::MOTIF_DEMISSION,
            $commentaire,
            $userId
        );
    }

    /**
     * Marquer comme retraite
     */
    public function markAsRetraite(string $commentaire = null, $userId = null): void
    {
        $this->changeStatus(
            self::STATUS_RETRAITE,
            HistoriqueStatutMembre::MOTIF_RETRAITE,
            $commentaire,
            $userId
        );
    }

    /**
     * Réaffecter à un nouveau poste
     */
    public function reaffectToPosition(Position $newPosition, string $motif, string $commentaire = null, $userId = null): void
    {
        $ancienStatut = $this->status;
        $anciennePosition = $this->position;

        // Enregistrer dans l'historique
        HistoriqueStatutMembre::create([
            'organization_member_id' => $this->id,
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => self::STATUS_ACTIVE,
            'motif' => $motif,
            'commentaire' => "Réaffectation de {$anciennePosition->title} vers {$newPosition->title}. " . $commentaire,
            'user_id' => $userId ?? auth()->id(),
            'date_effectif' => now(),
        ]);

        // Libérer l'ancien poste
        $this->status = self::STATUS_VACANT;
        $this->end_date = now();
        $this->save();

        // Créer un nouveau membre pour le nouveau poste
        $newMember = self::create([
            'position_id' => $newPosition->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'status' => self::STATUS_ACTIVE,
            'email' => $this->email,
            'phone' => $this->phone,
            'photo' => $this->photo,
            'start_date' => now(),
        ]);
    }

    /**
     * Affecter un utilisateur à ce poste
     */
    public function assignUser(User $user, string $commentaire = null, $userId = null): void
    {
        $ancienStatut = $this->status;
        
        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->telephone;
        $this->status = self::STATUS_ACTIVE;
        $this->start_date = now();
        $this->end_date = null;
        $this->save();

        // Enregistrer dans l'historique
        HistoriqueStatutMembre::create([
            'organization_member_id' => $this->id,
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => self::STATUS_ACTIVE,
            'motif' => HistoriqueStatutMembre::MOTIF_EMBAUCHE,
            'commentaire' => $commentaire ?? "Affectation de {$user->name} au poste {$this->position->title}",
            'user_id' => $userId ?? auth()->id(),
            'date_effectif' => now(),
        ]);
    }

    /**
     * Libérer le poste (marquer comme vacant)
     */
    public function markAsVacant(string $motif, string $commentaire = null, $userId = null): void
    {
        $this->changeStatus(
            self::STATUS_VACANT,
            $motif,
            $commentaire,
            $userId
        );
    }

    /**
     * Changer le statut d'un membre
     */
    private function changeStatus(string $newStatus, string $motif, string $commentaire = null, $userId = null): void
    {
        $ancienStatut = $this->status;
        $this->status = $newStatus;
        $this->end_date = now();
        $this->save();

        // Enregistrer dans l'historique
        HistoriqueStatutMembre::create([
            'organization_member_id' => $this->id,
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => $newStatus,
            'motif' => $motif,
            'commentaire' => $commentaire,
            'user_id' => $userId ?? auth()->id(),
            'date_effectif' => now(),
        ]);
    }

    /**
     * Vérifier si l'employé est actif
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Vérifier si le poste est vacant
     */
    public function isVacant(): bool
    {
        return $this->status === self::STATUS_VACANT;
    }

    /**
     * Obtenir l'affectation de poste active pour un utilisateur
     */
    public static function getActiveAssignmentForUser(User $user): ?self
    {
        return self::where('user_id', $user->id)
            ->where('status', self::STATUS_ACTIVE)
            ->whereNull('end_date')
            ->orWhere('end_date', '>=', now())
            ->first();
    }

    /**
     * Obtenir tous les postes vacants
     */
    public static function getVacantPositions()
    {
        return self::where('status', self::STATUS_VACANT)
            ->with(['position.department'])
            ->get();
    }
    
    /**
     * Trouver le supérieur hiérarchique
     */
    public function getSuperior()
    {
        if (!$this->position || !$this->position->parent_position_id) {
            return null;
        }
        
        // Trouver le membre actif occupant le poste parent
        return OrganizationMember::where('position_id', $this->position->parent_position_id)
            ->where('status', self::STATUS_ACTIVE)
            ->whereNotNull('user_id')
            ->first();
    }
    
    /**
     * Récupérer les utilisateurs RH
     */
    public static function getRHUsers()
    {
        return User::role(['RH', 'Ressources Humaines'])->get();
    }
}
