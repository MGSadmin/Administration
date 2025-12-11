<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandeAbsence extends Model
{
    use HasFactory;

    protected $table = 'demandes_absence';

    protected $fillable = [
        'organization_member_id',
        'user_id',
        'type',
        'date',
        'heure_debut',
        'heure_fin',
        'motif',
        'commentaire_rh',
        'statut',
        'fichier_justificatif',
        'validateur_id',
        'date_validation',
    ];

    protected $casts = [
        'date' => 'date',
        'date_validation' => 'datetime',
    ];

    const TYPE_ABSENCE_JUSTIFIEE = 'absence_justifiee';
    const TYPE_ABSENCE_NON_JUSTIFIEE = 'absence_non_justifiee';
    const TYPE_RETARD = 'retard';
    const TYPE_SORTIE_ANTICIPEE = 'sortie_anticipee';
    const TYPE_TELETRAVAIL = 'teletravail';
    const TYPE_MISSION_EXTERNE = 'mission_externe';
    const TYPE_FORMATION = 'formation';

    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_APPROUVE = 'approuve';
    const STATUT_REFUSE = 'refuse';

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

    public function getTypeLibelleAttribute(): string
    {
        return match($this->type) {
            self::TYPE_ABSENCE_JUSTIFIEE => 'Absence justifiée',
            self::TYPE_ABSENCE_NON_JUSTIFIEE => 'Absence non justifiée',
            self::TYPE_RETARD => 'Retard',
            self::TYPE_SORTIE_ANTICIPEE => 'Sortie anticipée',
            self::TYPE_TELETRAVAIL => 'Télétravail',
            self::TYPE_MISSION_EXTERNE => 'Mission externe',
            self::TYPE_FORMATION => 'Formation',
            default => $this->type,
        };
    }

    public function getStatutLibelleAttribute(): string
    {
        return match($this->statut) {
            self::STATUT_EN_ATTENTE => 'En attente',
            self::STATUT_APPROUVE => 'Approuvé',
            self::STATUT_REFUSE => 'Refusé',
            default => $this->statut,
        };
    }

    public function getStatutColorAttribute(): string
    {
        return match($this->statut) {
            self::STATUT_EN_ATTENTE => 'warning',
            self::STATUT_APPROUVE => 'success',
            self::STATUT_REFUSE => 'danger',
            default => 'primary',
        };
    }
}
