<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fournisseur extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nom',
        'contact',
        'telephone',
        'email',
        'adresse',
        'nif',
        'stat',
        'type',
        'specialites',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    /**
     * Scope pour les fournisseurs actifs
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Relation avec les demandes de fourniture
     */
    public function demandesFourniture()
    {
        return $this->hasMany(DemandeFourniture::class);
    }
}
