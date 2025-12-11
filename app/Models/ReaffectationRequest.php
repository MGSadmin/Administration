<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReaffectationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_member_id',
        'current_position_id',
        'new_position_id',
        'requested_by',
        'status',
        'motif',
        'date_souhaite',
        'approved_by',
        'approved_at',
        'commentaire_approbation',
    ];

    protected $casts = [
        'date_souhaite' => 'date',
        'approved_at' => 'datetime',
    ];

    const STATUS_PENDING = 'PENDING';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';

    public function organizationMember(): BelongsTo
    {
        return $this->belongsTo(OrganizationMember::class);
    }

    public function currentPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'current_position_id');
    }

    public function newPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'new_position_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Approuver la demande de réaffectation
     */
    public function approve(User $approver, string $commentaire = null): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'commentaire_approbation' => $commentaire,
        ]);

        // Effectuer la réaffectation
        $this->organizationMember->reaffectToPosition(
            $this->newPosition, 
            HistoriqueStatutMembre::MOTIF_REAFFECTATION,
            "Réaffectation approuvée: " . $commentaire,
            $approver->id
        );
    }

    /**
     * Rejeter la demande de réaffectation
     */
    public function reject(User $approver, string $commentaire = null): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'commentaire_approbation' => $commentaire,
        ]);
    }
}
