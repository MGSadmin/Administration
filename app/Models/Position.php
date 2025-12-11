<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'responsibilities',
        'department_id',
        'parent_position_id',
        'level',
        'order',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function parentPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'parent_position_id');
    }

    public function childPositions(): HasMany
    {
        return $this->hasMany(Position::class, 'parent_position_id')->orderBy('order');
    }

    public function member(): HasOne
    {
        return $this->hasOne(OrganizationMember::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(OrganizationMember::class);
    }
}
