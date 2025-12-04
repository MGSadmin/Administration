# Documentation des Rôles et Permissions - MGS

## Vue d'ensemble

Ce système de gestion des rôles et permissions est centralisé dans l'application **Administration** et partagé entre toutes les applications MGS (Administration, Gestion-Dossier, Commercial).

## Architecture

### Applications
- **Administration** : Gestion centralisée des utilisateurs, rôles et permissions
- **Gestion-Dossier** : Gestion des dossiers de transit, débours, factures
- **Commercial** : Gestion des devis et clients

### Partage de Session
Toutes les applications partagent :
- La même base de données de sessions (`mgs_administration`)
- La même clé d'application (`APP_KEY`)
- Le même cookie de session (`mgs_session`)
- Le même domaine (`.mgs.mg`)

## Rôles Disponibles

### 1. super-admin
- **Description** : Administrateur système avec accès total
- **Permissions** : Toutes (74 permissions)
- **Applications** : Toutes
- **Utilisation** : Compte système principal

### 2. admin
- **Description** : Administrateur de l'application
- **Permissions** : 22 permissions
- **Applications** : Administration (gestion utilisateurs/rôles) + lecture autres apps
- **Utilisation** : Gestion des utilisateurs et de la sécurité

### 3. direction
- **Description** : Direction générale
- **Permissions** : 68 permissions (tout sauf système)
- **Applications** : Toutes (lecture et modification)
- **Utilisation** : Supervision et décisions stratégiques

### 4. commercial
- **Description** : Service commercial
- **Permissions** : 27 permissions
- **Applications** : Gestion-Dossier (dossiers, cotations) + Commercial (devis, clients)
- **Utilisation** : Équipe commerciale terrain

### 5. facture
- **Description** : Service facturation
- **Permissions** : 7 permissions
- **Applications** : Gestion-Dossier (factures uniquement)
- **Utilisation** : Création et gestion des factures

### 6. comptable
- **Description** : Service comptabilité
- **Permissions** : 11 permissions
- **Applications** : Gestion-Dossier (règlements, validation débours)
- **Utilisation** : Gestion financière et comptable

### 7. production
- **Description** : Service production
- **Permissions** : 8 permissions
- **Applications** : Gestion-Dossier (situations, suivi production)
- **Utilisation** : Équipe de production/transit

### 8. consultation
- **Description** : Lecture seule
- **Permissions** : 9 permissions (toutes en .view)
- **Applications** : Toutes (lecture uniquement)
- **Utilisation** : Consultation, reporting, auditeurs


## Permissions par Catégorie (visibilité par site)

Chaque permission appartient à une catégorie et peut être marquée avec une visibilité par site. Cela permet de restreindre l'affichage ou l'application d'une permission à une application/portail spécifique. Exemple de valeurs de site : `administration`, `commercial`, `debours` (ou `gestion-dossier`).

### Administration (site: `administration`)
- **users** : view, create, edit, delete, active.view, status.manage
- **roles** : view, create, edit, delete
- **permissions** : view, create, delete
- **system** : backup, logs, settings

### Gestion-Dossier / Débours (site: `gestion-dossier` / `debours`)
- **dossier** : view, create, edit, delete
- **facture** : view, create, edit, delete
- **reglement** : view, create, edit, delete
- **cotation** : view, create, edit, delete
- **situation** : view, create, edit, delete, assign
- **debours** : view, create, edit, delete, assign, validate
- **production** : view

### Commercial (site: `commercial`)
- **devis** : view, create, edit, delete, validate
- **client** : view, create, edit, delete
- **commercial** : stats

## Installation et Configuration

### 1. Initialiser les rôles et permissions

```bash
# Dans l'application Administration
cd /var/www/administration
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### 2. Créer un utilisateur

```bash
# Via l'interface web
http://administration.mgs.mg/users/create

# Ou via tinker
php artisan tinker
>>> $user = User::create([...]);
>>> $user->assignRole('commercial');
```

### 3. Donner accès à une application

```php
UserApplication::create([
    'user_id' => $user->id,
    'application' => 'gestion-dossier',
    'role' => 'commercial',
    'status' => 'active',
]);
```

## Utilisation dans le Code

### Vérifier une permission

```php
// Dans un contrôleur ou middleware
if (auth()->user()->can('dossier.create')) {
    // Autoriser l'action
}

// Dans une vue Blade
@can('facture.edit')
    <button>Modifier</button>
@endcan
```

### Vérifier un rôle

```php
// Dans un contrôleur
if (auth()->user()->hasRole('admin')) {
    // Action admin
}

// Dans une vue Blade
@role('commercial')
    <div>Contenu commercial</div>
@endrole
```

### Middleware dans les routes

```php
// Route protégée par permission
Route::get('/dossiers', [DossierController::class, 'index'])
    ->middleware('permission:dossier.view');

// Route protégée par rôle
Route::get('/admin', [AdminController::class, 'index'])
    ->middleware('role:admin');
```

## Flux d'Authentification Multi-Application

1. Utilisateur accède à `gestion-dossier.mgs.mg`
2. Middleware `CheckApplicationAccess` vérifie l'authentification
3. Si non connecté → Redirection vers `administration.mgs.mg/login`
4. Utilisateur se connecte dans Administration
5. Après login → Redirection automatique vers l'URL d'origine (gestion-dossier)
6. Middleware vérifie l'accès à l'application via `UserApplication`
7. Si accès autorisé → Accès à l'application

## Maintenance

### Ajouter une nouvelle permission

```php
// Dans RolesAndPermissionsSeeder.php
$permissions = [
    // ...
    'nouvelle.permission' => 'Description de la permission',
];

// Puis réexécuter le seeder
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### Créer un nouveau rôle

```php
$role = Role::create(['name' => 'nouveau-role']);
$role->syncPermissions([
    'permission1',
    'permission2',
]);
```

## Sécurité

- ✅ Les mots de passe sont hashés avec bcrypt
- ✅ Les sessions sont partagées de manière sécurisée
- ✅ Les permissions sont vérifiées à chaque requête
- ✅ Les accès aux applications sont contrôlés
- ✅ Les utilisateurs inactifs ne peuvent pas se connecter

## Compte par Défaut

Après avoir exécuté le seeder :
- **Email** : admin@mgs.mg
- **Mot de passe** : Admin@2025
- **Rôle** : super-admin
- **Accès** : Toutes les applications

⚠️ **Important** : Changez ce mot de passe en production !

## Support

Pour toute question ou problème, consultez :
- Documentation en ligne : `http://administration.mgs.mg/roles-documentation`
- Logs : `/var/www/administration/storage/logs/`
- Base de données : `mgs_administration`
