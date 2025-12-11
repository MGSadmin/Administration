<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Vérifie si l'utilisateur a accès à un site spécifique
     */
    public function hasAccessToSite(string $siteCode): bool
    {
        return $this->getAllPermissions()
            ->filter(fn($p) => str_starts_with($p->name, $siteCode . '.'))
            ->isNotEmpty();
    }

    /**
     * Récupère tous les sites accessibles par l'utilisateur
     */
    public function getAccessibleSites()
    {
        $sites = Site::where('is_active', true)->get();
        
        return $sites->filter(function($site) {
            return $this->hasAccessToSite($site->code);
        });
    }

    /**
     * Récupère les permissions pour un site spécifique
     */
    public function getPermissionsForSite(string $siteCode)
    {
        return $this->getAllPermissions()
            ->filter(fn($p) => str_starts_with($p->name, $siteCode . '.'))
            ->pluck('name');
    }

    /**
     * Relation avec le membre de l'organisation
     */
    public function organizationMember()
    {
        return $this->hasOne(OrganizationMember::class, 'user_id');
    }

    /**
     * Relation avec les patrimoines dont l'utilisateur est responsable
     */
    public function patrimoines()
    {
        return $this->hasMany(Patrimoine::class, 'utilisateur_id');
    }

    /**
     * Relation avec les demandes de fourniture créées
     */
    public function demandesFourniture()
    {
        return $this->hasMany(DemandeFourniture::class, 'demandeur_id');
    }

    /**
     * Vérifie si l'utilisateur est super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }

    /**
     * Vérifie si l'utilisateur peut gérer les utilisateurs
     */
    public function canManageUsers(): bool
    {
        return $this->hasPermissionTo('admin.manage_users') || $this->isSuperAdmin();
    }

    /**
     * Vérifie si l'utilisateur peut gérer les rôles
     */
    public function canManageRoles(): bool
    {
        return $this->hasPermissionTo('admin.manage_roles') || $this->isSuperAdmin();
    }
}
