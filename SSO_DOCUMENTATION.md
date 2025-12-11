# Système SSO - Authentification Centralisée MGS

## ✅ Installation Terminée

Le système d'authentification centralisé SSO (Single Sign-On) a été installé avec succès !

### 🎯 Ce qui a été créé

#### 1. **Modèles**
- ✅ `app/Models/User.php` - Modèle utilisateur avec support Sanctum et Spatie Permission
- ✅ `app/Models/Site.php` - Modèle pour gérer les 3 sites

#### 2. **Contrôleurs**
- ✅ `app/Http/Controllers/SSOController.php` - Gestion login/logout centralisé
- ✅ `app/Http/Controllers/Api/AuthController.php` - API d'authentification
- ✅ `app/Http/Controllers/Admin/UserController.php` - CRUD utilisateurs
- ✅ `app/Http/Controllers/Admin/RoleController.php` - CRUD rôles
- ✅ `app/Http/Controllers/OrganigrammeController.php` - Visualisation architecture

#### 3. **Vues**
- ✅ `resources/views/sso/login.blade.php` - Page de connexion SSO élégante
- ✅ `resources/views/sso/error.blade.php` - Page d'erreur SSO
- ✅ `resources/views/organigramme.blade.php` - Organigramme interactif du système

#### 4. **Base de données**
- ✅ Table `users` - Utilisateurs
- ✅ Table `sites` - 3 sites (Administration, Commercial, Gestion Dossier)
- ✅ Tables Spatie Permission (roles, permissions, model_has_roles, etc.)
- ✅ Table `personal_access_tokens` (Laravel Sanctum)

#### 5. **Seeders**
- ✅ `SitesSeeder` - 3 sites préconfigurés
- ✅ `RolesAndPermissionsSeeder` - 7 rôles et 65+ permissions

### 📊 Données Initiales

#### Sites
1. **Administration** (admin) - Serveur Central SSO
2. **Commercial** (commercial) - Client SSO
3. **Gestion Dossier** (debours) - Client SSO

#### Rôles Créés
1. **Super Admin** - Accès total tous sites
2. **Administrateur** - Administration uniquement
3. **Manager Commercial** - Accès complet commercial
4. **Commercial** - Accès limité commercial
5. **Gestionnaire Débours** - Accès complet gestion dossier
6. **Assistant Débours** - Accès limité gestion dossier
7. **Comptable** - Accès multi-sites (rapports)

#### Compte Super Admin
- 📧 Email: `admin@mgs.mg`
- 🔑 Mot de passe: `password`

### 🔐 Routes SSO

```
GET  /sso/login                 - Page de connexion SSO
POST /sso/authenticate          - Authentification
GET  /sso/logout                - Déconnexion
POST /sso/revoke-token          - Révoquer un token
```

### 🌐 Routes API Authentification

```
POST /api/auth/verify-token              - Vérifier validité token
GET  /api/auth/user-permissions/{site}   - Permissions par site
POST /api/auth/check-permission          - Vérifier une permission
POST /api/auth/check-role                - Vérifier un rôle
GET  /api/auth/me                        - Infos utilisateur
GET  /api/auth/accessible-sites          - Sites accessibles
POST /api/auth/refresh-token             - Rafraîchir token
GET  /api/auth/tokens                    - Lister tokens actifs
```

### 👥 Routes Administration

```
GET    /admin/users              - Liste utilisateurs
GET    /admin/users/create       - Créer utilisateur
POST   /admin/users              - Enregistrer utilisateur
GET    /admin/users/{user}       - Voir utilisateur
GET    /admin/users/{user}/edit  - Éditer utilisateur
PUT    /admin/users/{user}       - Mettre à jour
DELETE /admin/users/{user}       - Supprimer
POST   /admin/users/{user}/revoke-tokens - Révoquer tokens

GET    /admin/roles              - Liste rôles
GET    /admin/roles/create       - Créer rôle
POST   /admin/roles              - Enregistrer rôle
GET    /admin/roles/{role}       - Voir rôle
GET    /admin/roles/{role}/edit  - Éditer rôle
PUT    /admin/roles/{role}       - Mettre à jour
DELETE /admin/roles/{role}       - Supprimer
```

### 📈 Route Organigramme

```
GET /organigramme                - Vue architecture SSO
GET /organigramme/roles-data     - API données rôles
GET /organigramme/flow-data      - API flux authentification
```

### 🔧 Configuration

#### CORS (`config/cors.php`)
✅ Configuré pour autoriser:
- administration.mgs.mg
- commercial.mgs.mg
- debours.mgs.mg

#### Sanctum (`config/sanctum.php`)
✅ Domaines stateful configurés
✅ Expiration tokens: 7 jours

### 📝 Permissions par Site

#### Administration (admin.*)
- view_dashboard, manage_users, create_user, edit_user, delete_user
- manage_roles, create_role, edit_role, delete_role
- manage_permissions, manage_sites, view_logs
- manage_patrimoines, manage_demandes

#### Commercial (commercial.*)
- view_dashboard, manage_clients, create_client, edit_client, delete_client, view_clients
- manage_devis, create_devis, edit_devis, delete_devis, view_devis
- manage_opportunities, create_opportunity, edit_opportunity, delete_opportunity, view_opportunities
- view_reports, export_data

#### Gestion Dossier (debours.*)
- view_dashboard, view_expenses, create_expense, edit_expense, delete_expense
- approve_expenses, reject_expenses
- create_payment, view_payments
- manage_dossiers, create_dossier, edit_dossier, delete_dossier, view_dossiers
- view_reports, export_data

### 🚀 Utilisation

#### 1. Accéder à l'organigramme
```
http://administration.mgs-local.mg/organigramme
```

#### 2. Se connecter
```
http://administration.mgs-local.mg/sso/login?site=admin&callback=http://administration.mgs-local.mg/
```

#### 3. Gérer les utilisateurs
```
http://administration.mgs-local.mg/admin/users
```

#### 4. Gérer les rôles
```
http://administration.mgs-local.mg/admin/roles
```

### 🔄 Flux SSO (Sites Clients)

#### Pour Commercial et Gestion Dossier

1. **Redirection vers SSO**
```php
$callbackUrl = urlencode(url('/auth/callback'));
$siteName = 'commercial'; // ou 'debours'
return redirect("https://administration.mgs.mg/sso/login?callback={$callbackUrl}&site={$siteName}");
```

2. **Callback avec token**
```php
Route::get('/auth/callback', function(Request $request) {
    $token = $request->get('token');
    session(['auth_token' => $token]);
    return redirect('/dashboard');
});
```

3. **Vérifier token (Middleware)**
```php
$response = Http::withToken($token)
    ->post('https://administration.mgs.mg/api/auth/verify-token');
```

4. **Vérifier permission**
```php
$response = Http::withToken($token)
    ->get('https://administration.mgs.mg/api/auth/user-permissions/commercial');
```

### 📚 Prochaines Étapes

1. **Créer les vues d'administration**
   - admin/users/index.blade.php
   - admin/users/create.blade.php
   - admin/users/edit.blade.php
   - admin/roles/index.blade.php
   - admin/roles/create.blade.php
   - admin/roles/edit.blade.php

2. **Implémenter le SSO sur sites clients**
   - Créer middleware CentralAuth sur Commercial
   - Créer middleware CentralAuth sur Gestion Dossier
   - Configurer routes auth

3. **Tester le flux complet**
   - Connexion depuis Commercial
   - Vérification permissions
   - Déconnexion centralisée

### ⚠️ Important

- Changez le mot de passe du Super Admin en production
- Configurez les URLs en HTTPS pour la production
- Activez le cache des permissions: `php artisan permission:cache-reset`
- Logs d'activité avec spatie/laravel-activitylog (à installer)

### 🎨 Interface Organigramme

L'organigramme accessible via `/organigramme` affiche:
- Architecture du système SSO
- Flux d'authentification détaillé
- Statistiques en temps réel
- Liste des rôles et permissions
- Avantages du système

---

**Système SSO MGS - Version 1.0**
*Déploiement: 8 décembre 2025*
