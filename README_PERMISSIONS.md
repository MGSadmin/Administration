# Système de Rôles et Permissions - Administration MGS

## 🎯 Présentation

Système complet de gestion des rôles et permissions avec des **noms en français** faciles à comprendre.

## 🚀 Installation Rapide

```bash
./install_permissions.sh
```

Ou manuellement :

```bash
composer dump-autoload
php artisan optimize:clear
php artisan db:seed --class=ModernRolesPermissionsSeeder
```

## 🔐 Connexion Super Admin

- **Email**: `admin@mgs.mg`
- **Mot de passe**: `Admin@2025`

## 📋 Rôles Disponibles

| Rôle | Description |
|------|-------------|
| `super-admin` | Accès total au système |
| `administrateur` | Gestion complète de tous les modules |
| `rh` | Gestion des ressources humaines et personnel |
| `direction` | Validation et supervision |
| `chef-departement` | Gestion d'équipe et département |
| `employe` | Accès de base |

## 🎨 Exemples de Permissions

### Format des permissions
Les permissions utilisent des noms clairs en français :
- ✅ **Voir Organigramme** (au lieu de `organigramme.view`)
- ✅ **Créer Patrimoine** (au lieu de `patrimoine.create`)
- ✅ **Modifier Congé** (au lieu de `conge.edit`)
- ✅ **Supprimer Utilisateur** (au lieu de `user.delete`)

### Dans les vues Blade

```blade
@can('Créer Patrimoine')
    <a href="{{ route('patrimoines.create') }}" class="btn btn-primary">
        Créer Patrimoine
    </a>
@endcan

@can('Modifier Patrimoine')
    <a href="{{ route('patrimoines.edit', $patrimoine) }}" class="btn btn-warning">
        Modifier Patrimoine
    </a>
@endcan

@can('Supprimer Patrimoine')
    <button class="btn btn-danger">Supprimer Patrimoine</button>
@endcan
```

### Dans les contrôleurs

```php
public function index()
{
    $this->authorize('viewAny', Patrimoine::class);
    // ...
}

public function create()
{
    $this->authorize('create', Patrimoine::class);
    // ...
}

public function update(Patrimoine $patrimoine)
{
    $this->authorize('update', $patrimoine);
    // ...
}
```

### Avec les helpers

```php
if (can_create_patrimoine()) {
    // Afficher le bouton
}

if (can_edit_congé()) {
    // Permettre la modification
}
```

## 📊 Modules Couverts

- ✅ **Organigramme** - Départements, positions, membres
- ✅ **Patrimoine** - Matériels et équipements
- ✅ **Fournitures** - Demandes de fourniture
- ✅ **Congés/Absences** - Gestion des congés
- ✅ **Personnel** - Ressources humaines
- ✅ **Administration** - Utilisateurs et rôles

## 📚 Documentation Complète

### Fichiers de documentation
- **[GUIDE_PERMISSIONS.md](GUIDE_PERMISSIONS.md)** - Guide complet du système
- **[EXEMPLES_IMPLEMENTATION_PERMISSIONS.md](EXEMPLES_IMPLEMENTATION_PERMISSIONS.md)** - Exemples de code

### Fichiers techniques
- **Seeder**: `database/seeders/ModernRolesPermissionsSeeder.php`
- **Policies**: `app/Policies/`
- **Helpers**: `app/Helpers/PermissionHelpers.php`

## 🔧 Commandes Utiles

### Gérer les rôles et permissions

```bash
# Lister les rôles
php artisan tinker
>>> \Spatie\Permission\Models\Role::all()

# Lister les permissions
>>> \Spatie\Permission\Models\Permission::all()

# Assigner un rôle à un utilisateur
>>> $user = User::find(1)
>>> $user->assignRole('rh')

# Donner une permission directement
>>> $user->givePermissionTo('Créer Patrimoine')

# Vérifier les permissions d'un utilisateur
>>> $user->getAllPermissions()
```

### Nettoyer le cache

```bash
php artisan optimize:clear
php artisan permission:cache-reset
```

## 🎯 Workflow Typique

1. **Créer un utilisateur**
   ```bash
   php artisan tinker
   >>> $user = User::create([
       'name' => 'Jean Dupont',
       'email' => 'jean@exemple.com',
       'password' => Hash::make('password')
   ]);
   ```

2. **Lui assigner un rôle**
   ```bash
   >>> $user->assignRole('employe')
   ```

3. **Vérifier ses permissions**
   ```bash
   >>> $user->can('Créer Congé')  // true
   >>> $user->can('Voir Tous Congés')  // false
   ```

## 🛡️ Sécurité

### Dans les routes
```php
// Protéger une route avec middleware
Route::get('/patrimoines', [PatrimoineController::class, 'index'])
    ->middleware('can:Voir Patrimoine');

// Protéger par rôle
Route::middleware('role:administrateur')->group(function () {
    // Routes admin
});
```

### Dans les contrôleurs
```php
// Vérification automatique via Policy
$this->authorize('update', $patrimoine);

// Vérification manuelle
if (!auth()->user()->can('Supprimer Patrimoine')) {
    abort(403);
}
```

### Dans les vues
```blade
{{-- Masquer les actions non autorisées --}}
@can('Modifier Patrimoine')
    <button>Modifier</button>
@endcan

@can('update', $patrimoine)
    <button>Modifier ce patrimoine</button>
@endcan
```

## 🎨 Personnalisation

### Créer une nouvelle permission

```php
use Spatie\Permission\Models\Permission;

Permission::create(['name' => 'Ma Nouvelle Permission']);
```

### Créer un nouveau rôle

```php
use Spatie\Permission\Models\Role;

$role = Role::create(['name' => 'mon-role']);
$role->givePermissionTo([
    'Voir Organigramme',
    'Créer Demande Fourniture'
]);
```

## 📞 Support

Pour toute question :
- Documentation Spatie: https://spatie.be/docs/laravel-permission
- Fichiers du projet dans `/var/www/administration`

---

**Version**: 1.0  
**Dernière mise à jour**: 9 Décembre 2025  
**Auteur**: Système MGS
