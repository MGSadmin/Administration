# Système de Rôles et Permissions - Administration MGS

## 📋 Vue d'ensemble

Ce système utilise **Spatie Laravel-Permission** avec des noms de permissions en français faciles à comprendre.

## 🎭 Rôles disponibles

### 1. Super Admin (`super-admin`)
- **Accès**: Total, toutes les permissions
- **Usage**: Compte technique principal

### 2. Administrateur (`administrateur`)
- **Accès**: Gestion complète de tous les modules
- **Peut**:
  - Gérer les utilisateurs et rôles
  - Gérer l'organigramme complet
  - Gérer tous les patrimoines
  - Gérer toutes les fournitures
  - Gérer tous les congés
  - Gérer tout le personnel

### 3. RH (`rh`)
- **Accès**: Ressources humaines et gestion du personnel
- **Peut**:
  - Gérer l'organigramme (sauf suppression département/position)
  - Gérer les patrimoines
  - Traiter les fournitures
  - Approuver/rejeter les congés
  - Gérer les documents personnel

### 4. Direction (`direction`)
- **Accès**: Validation et supervision
- **Peut**:
  - Consulter l'organigramme (lecture seule)
  - Consulter les patrimoines et statistiques
  - Valider/rejeter les demandes de fourniture
  - Approuver/rejeter les congés
  - Consulter les informations du personnel

### 5. Chef de Département (`chef-departement`)
- **Accès**: Gestion d'équipe
- **Peut**:
  - Voir l'organigramme
  - Gérer les patrimoines de son département
  - Gérer les fournitures de son département
  - Approuver les congés de son équipe
  - Consulter son équipe

### 6. Employé (`employe`)
- **Accès**: Basique
- **Peut**:
  - Voir l'organigramme
  - Voir ses patrimoines
  - Créer ses demandes de fourniture
  - Créer ses demandes de congé
  - Télécharger ses documents

## 📝 Permissions par module

### Organigramme
- `Voir Organigramme`
- `Créer Département`
- `Modifier Département`
- `Supprimer Département`
- `Créer Position`
- `Modifier Position`
- `Supprimer Position`
- `Créer Membre`
- `Modifier Membre`
- `Supprimer Membre`
- `Assigner Membre`
- `Gérer Réaffectation`
- `Gérer Démission`
- `Gérer Licenciement`
- `Gérer Retraite`

### Patrimoine
- `Voir Patrimoine` (ses propres patrimoines)
- `Voir Tous Patrimoines`
- `Créer Patrimoine`
- `Modifier Patrimoine`
- `Supprimer Patrimoine`
- `Attribuer Patrimoine`
- `Libérer Patrimoine`
- `Voir Statistiques Patrimoine`

### Fournitures
- `Voir Demande Fourniture` (ses propres demandes)
- `Voir Toutes Demandes Fourniture`
- `Créer Demande Fourniture`
- `Modifier Demande Fourniture`
- `Supprimer Demande Fourniture`
- `Valider Demande Fourniture`
- `Rejeter Demande Fourniture`
- `Commander Fourniture`
- `Marquer Fourniture Reçue`
- `Livrer Fourniture`
- `Voir Statistiques Fourniture`

### Congés/Absences
- `Voir Congé` (ses propres congés)
- `Voir Tous Congés`
- `Créer Congé`
- `Modifier Congé`
- `Supprimer Congé`
- `Approuver Congé`
- `Rejeter Congé`
- `Voir Absence`
- `Voir Toutes Absences`
- `Créer Absence`
- `Approuver Absence`
- `Rejeter Absence`

### Personnel
- `Voir Personnel`
- `Voir Détails Personnel`
- `Modifier Statut Personnel`
- `Voir Historique Personnel`
- `Gérer Documents Personnel`
- `Créer Document Personnel`
- `Télécharger Document Personnel`
- `Archiver Document Personnel`
- `Supprimer Document Personnel`

### Administration
- `Voir Dashboard`
- `Voir Utilisateurs`
- `Créer Utilisateur`
- `Modifier Utilisateur`
- `Supprimer Utilisateur`
- `Révoquer Tokens Utilisateur`
- `Voir Rôles`
- `Créer Rôle`
- `Modifier Rôle`
- `Supprimer Rôle`
- `Gérer Permissions`
- `Voir Notifications`
- `Gérer Notifications`

## 💻 Utilisation dans le code

### Dans les contrôleurs

```php
// Vérifier une permission
public function index()
{
    $this->authorize('viewAny', Patrimoine::class);
    // ou
    if (!auth()->user()->can('Voir Tous Patrimoines')) {
        abort(403);
    }
}

// Avec Policy
public function update(Patrimoine $patrimoine)
{
    $this->authorize('update', $patrimoine);
}
```

### Dans les vues Blade

```blade
{{-- Vérifier une permission --}}
@can('Créer Patrimoine')
    <a href="{{ route('patrimoines.create') }}" class="btn btn-primary">
        Créer Patrimoine
    </a>
@endcan

{{-- Vérifier avec policy --}}
@can('update', $patrimoine)
    <a href="{{ route('patrimoines.edit', $patrimoine) }}" class="btn btn-warning">
        Modifier Patrimoine
    </a>
@endcan

@can('delete', $patrimoine)
    <button class="btn btn-danger">Supprimer Patrimoine</button>
@endcan

{{-- Avec les helpers --}}
@if(can_create_patrimoine())
    <button>Créer</button>
@endif

@if(can_edit_patrimoine())
    <button>Modifier</button>
@endif
```

### Dans les routes

```php
// Middleware de permission
Route::get('/patrimoines', [PatrimoineController::class, 'index'])
    ->middleware('can:Voir Patrimoine');

// Middleware de rôle
Route::prefix('admin')->middleware('role:administrateur')->group(function () {
    // Routes admin
});
```

### Fonctions helpers disponibles

```php
// Organigramme
can_view_organigramme()
can_edit_organigramme()
can_delete_organigramme()

// Patrimoine
can_view_patrimoine()
can_create_patrimoine()
can_edit_patrimoine()
can_delete_patrimoine()

// Fourniture
can_view_fourniture()
can_create_fourniture()
can_edit_fourniture()
can_delete_fourniture()
can_validate_fourniture()

// Congé
can_view_conge()
can_create_conge()
can_edit_conge()
can_delete_conge()
can_approve_conge()

// Personnel
can_view_personnel()
can_manage_personnel()

// Utilisateurs et Rôles
can_view_users()
can_create_user()
can_edit_user()
can_delete_user()
can_view_roles()
can_create_role()
can_edit_role()
can_delete_role()
```

## 🚀 Installation et mise à jour

### 1. Exécuter le seeder

```bash
php artisan db:seed --class=ModernRolesPermissionsSeeder
```

### 2. Mettre à jour l'autoload

```bash
composer dump-autoload
```

### 3. Vider le cache

```bash
php artisan optimize:clear
```

## 📊 Matrice des permissions

| Module | Employé | Chef Dept | Direction | RH | Admin | Super Admin |
|--------|---------|-----------|-----------|-------|-------|-------------|
| **Organigramme** |
| Voir | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Créer/Modifier | ✗ | ✗ | ✗ | ✓ | ✓ | ✓ |
| Supprimer | ✗ | ✗ | ✗ | Limité | ✓ | ✓ |
| **Patrimoine** |
| Voir (propre) | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Voir (tous) | ✗ | Dept | ✓ | ✓ | ✓ | ✓ |
| Créer | ✗ | ✗ | ✗ | ✓ | ✓ | ✓ |
| Modifier | ✗ | ✗ | ✗ | ✓ | ✓ | ✓ |
| Supprimer | ✗ | ✗ | ✗ | ✗ | ✓ | ✓ |
| **Fournitures** |
| Voir (propre) | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Voir (tous) | ✗ | Dept | ✓ | ✓ | ✓ | ✓ |
| Créer | ✓ | ✓ | ✗ | ✓ | ✓ | ✓ |
| Valider | ✗ | ✓ | ✓ | ✗ | ✓ | ✓ |
| Commander | ✗ | ✗ | ✗ | ✓ | ✓ | ✓ |
| **Congés** |
| Voir (propre) | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Voir (tous) | ✗ | Équipe | ✓ | ✓ | ✓ | ✓ |
| Créer | ✓ | ✓ | ✗ | ✓ | ✓ | ✓ |
| Approuver | ✗ | ✓ | ✓ | ✓ | ✓ | ✓ |
| **Personnel** |
| Voir | ✗ | Équipe | ✓ | ✓ | ✓ | ✓ |
| Modifier statut | ✗ | ✗ | ✗ | ✓ | ✓ | ✓ |
| Gérer documents | ✗ | ✗ | ✗ | ✓ | ✓ | ✓ |
| **Administration** |
| Utilisateurs | ✗ | ✗ | ✗ | ✗ | ✓ | ✓ |
| Rôles | ✗ | ✗ | ✗ | ✗ | ✓ | ✓ |

## 🔧 Personnalisation

### Créer un nouveau rôle

```php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

$role = Role::create(['name' => 'nouveau-role']);
$role->givePermissionTo(['Voir Organigramme', 'Créer Demande Fourniture']);
```

### Assigner un rôle à un utilisateur

```php
$user->assignRole('rh');
// ou
$user->syncRoles(['rh', 'chef-departement']);
```

### Donner une permission directement

```php
$user->givePermissionTo('Créer Patrimoine');
```

## 📞 Support

Pour toute question sur le système de permissions :
- Fichier Seeder: `/database/seeders/ModernRolesPermissionsSeeder.php`
- Policies: `/app/Policies/`
- Helpers: `/app/Helpers/PermissionHelpers.php`
- Documentation Spatie: https://spatie.be/docs/laravel-permission

---

**Version**: 1.0  
**Dernière mise à jour**: 9 Décembre 2025
