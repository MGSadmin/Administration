<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PositionAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'position_id',
        'user_id',
        'status',
        'date_debut',
        'date_fin',
        'notes',
        'assigned_by',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_VACANT = 'VACANT';
    const STATUS_PENDING = 'PENDING';

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Vérifier si l'affectation est active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE 
            && $this->date_debut <= now() 
            && ($this->date_fin === null || $this->date_fin >= now());
    }
}
