<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'domain',
        'code',
        'api_key',
        'is_active',
        'description',
        'config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($site) {
            if (empty($site->api_key)) {
                $site->api_key = Str::random(64);
            }
        });
    }

    /**
     * Vérifie si une clé API est valide pour ce site
     */
    public function verifyApiKey(string $apiKey): bool
    {
        return $this->is_active && hash_equals($this->api_key, $apiKey);
    }

    /**
     * Récupère toutes les permissions pour ce site
     */
    public function getPermissions()
    {
        return \Spatie\Permission\Models\Permission::where('name', 'like', $this->code . '.%')->get();
    }

    /**
     * Compte les utilisateurs ayant accès à ce site
     */
    public function getUsersCount(): int
    {
        return \App\Models\User::whereHas('permissions', function($q) {
            $q->where('name', 'like', $this->code . '.%');
        })->count();
    }
}
