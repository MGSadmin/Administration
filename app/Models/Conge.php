<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conge extends Model
{
    use HasFactory;

    protected $table = 'conges';

    protected $fillable = [
        'organization_member_id',
        'user_id',
        'validateur_id',
        'type',
        'date_debut',
        'date_fin',
        'nb_jours',
        'motif',
        'commentaire_rh',
        'statut',
        'fichier_justificatif',
        'date_validation',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_validation' => 'datetime',
    ];

    const TYPE_CONGE_ANNUEL = 'conge_annuel';
    const TYPE_CONGE_MALADIE = 'conge_maladie';
    const TYPE_CONGE_MATERNITE = 'conge_maternite';
    const TYPE_CONGE_PATERNITE = 'conge_paternite';
    const TYPE_CONGE_SANS_SOLDE = 'conge_sans_solde';
    const TYPE_PERMISSION = 'permission';
    const TYPE_AUTRE = 'autre';

    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_APPROUVE = 'approuve';
    const STATUT_REFUSE = 'refuse';
    const STATUT_ANNULE = 'annule';

    public function organizationMember(): BelongsTo
    {
        return $this->belongsTo(OrganizationMember::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function validateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validateur_id');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', self::STATUT_EN_ATTENTE);
    }

    public function scopeApprouve($query)
    {
        return $query->where('statut', self::STATUT_APPROUVE);
    }

    public function getTypeLibelleAttribute(): string
    {
        return match($this->type) {
            self::TYPE_CONGE_ANNUEL => 'Congé annuel',
            self::TYPE_CONGE_MALADIE => 'Congé maladie',
            self::TYPE_CONGE_MATERNITE => 'Congé maternité',
            self::TYPE_CONGE_PATERNITE => 'Congé paternité',
            self::TYPE_CONGE_SANS_SOLDE => 'Congé sans solde',
            self::TYPE_PERMISSION => 'Permission',
            default => 'Autre',
        };
    }

    public function getStatutLibelleAttribute(): string
    {
        return match($this->statut) {
            self::STATUT_EN_ATTENTE => 'En attente',
            self::STATUT_APPROUVE => 'Approuvé',
            self::STATUT_REFUSE => 'Refusé',
            self::STATUT_ANNULE => 'Annulé',
            default => $this->statut,
        };
    }

    public function getStatutColorAttribute(): string
    {
        return match($this->statut) {
            self::STATUT_EN_ATTENTE => 'warning',
            self::STATUT_APPROUVE => 'success',
            self::STATUT_REFUSE => 'danger',
            self::STATUT_ANNULE => 'secondary',
            default => 'primary',
        };
    }
}
