<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    protected $fillable = [
        'name',
        'prenom',
        'matricule',
        'email',
        'password',
        'telephone',
        'poste',
        'departement',
        'date_embauche',
        'photo',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_embauche' => 'date',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    // Relations
    public function applications()
    {
        return $this->hasMany(UserApplication::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helpers
    public function hasAccessToApplication(string $application): bool
    {
        return $this->applications()
            ->where('application', $application)
            ->where('status', 'active')
            ->exists();
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->name} {$this->prenom}");
    }
}
