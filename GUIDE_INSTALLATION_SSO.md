# Guide d'Installation et Configuration SSO

## 📋 Vue d'ensemble

Ce document explique comment installer et configurer le système d'authentification centralisée SSO pour les 3 sites MGS.

## 🚀 Étape 1 : Exécuter les migrations

```bash
cd /var/www/administration
php artisan migrate
```

Cette commande va créer la table `sites` pour gérer les différents domaines.

## 🌱 Étape 2 : Exécuter les seeders

```bash
# Créer les sites
php artisan db:seed --class=SitesSeeder

# Créer les rôles et permissions
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### Données créées :

**Sites :**
- Administration (admin) - administration.mgs.mg
- Commercial (commercial) - commercial.mgs.mg  
- Gestion Dossier (debours) - debours.mgs.mg

**Rôles :**
- Super Admin (accès total)
- Administrateur (administration uniquement)
- Manager Commercial (commercial complet)
- Commercial (commercial limité)
- Gestionnaire Débours (débours complet)
- Assistant Débours (débours limité)
- Comptable (multi-sites en lecture)

**Utilisateur par défaut :**
- Email: `admin@mgs.mg`
- Mot de passe: `password`
- Rôle: Super Admin

## 🔐 Étape 3 : Configuration de l'environnement

Ajoutez dans votre fichier `.env` :

```env
# Domaines autorisés pour Sanctum
SANCTUM_STATEFUL_DOMAINS=administration.mgs.mg,commercial.mgs.mg,debours.mgs.mg

# URL de l'application
APP_URL=https://administration.mgs.mg
```

## 📁 Étape 4 : Structure créée

### Contrôleurs

**SSO :**
- `SSOController` - Gestion de l'authentification centralisée
- `Api/AuthController` - API pour vérification des tokens

**Administration :**
- `Admin/UserController` - CRUD utilisateurs
- `Admin/RoleController` - CRUD rôles
- `OrganigrammeController` - Visualisation de l'architecture

### Modèles

- `Site` - Gestion des sites/domaines
- `User` - Utilisateurs (avec Spatie Permission)

### Vues

- `sso/login.blade.php` - Page de connexion SSO
- `sso/error.blade.php` - Page d'erreur SSO
- `organigramme.blade.php` - Vue d'architecture du système

### Routes

**Web (routes/web.php) :**
```php
// SSO
/sso/login
/sso/authenticate
/sso/logout

// Administration
/admin/users
/admin/roles
/organigramme
```

**API (routes/api.php) :**
```php
// Authentification
POST /api/auth/verify-token
GET  /api/auth/user-permissions/{siteCode}
POST /api/auth/check-permission
POST /api/auth/check-role
GET  /api/auth/me
GET  /api/auth/accessible-sites
POST /api/auth/refresh-token
GET  /api/auth/tokens
```

## 🌐 Étape 5 : Flux d'authentification SSO

### Connexion depuis un site client

1. L'utilisateur visite `commercial.mgs.mg`
2. Il est redirigé vers `administration.mgs.mg/sso/login?callback=https://commercial.mgs.mg/auth/callback&site=commercial`
3. Il saisit ses identifiants
4. Le serveur vérifie les permissions pour le site commercial
5. Un token Sanctum est généré
6. L'utilisateur est redirigé vers `commercial.mgs.mg/auth/callback?token=xxx`
7. Le site commercial stocke le token en session

### Vérification des permissions

Le site client peut vérifier les permissions via l'API :

```bash
# Vérifier le token
curl -X POST https://administration.mgs.mg/api/auth/verify-token \
  -H "Authorization: Bearer {token}"

# Récupérer les permissions pour un site
curl -X GET https://administration.mgs.mg/api/auth/user-permissions/commercial \
  -H "Authorization: Bearer {token}"

# Vérifier une permission spécifique
curl -X POST https://administration.mgs.mg/api/auth/check-permission \
  -H "Authorization: Bearer {token}" \
  -d "permission=commercial.create_devis"
```

## 📊 Étape 6 : Gestion des permissions

### Convention de nommage

Les permissions suivent le format : `{site}.{action}`

**Exemples :**
- `admin.manage_users`
- `commercial.create_devis`
- `debours.approve_expenses`

### Permissions créées par défaut

**Administration (14 permissions) :**
- `admin.view_dashboard`
- `admin.manage_users`
- `admin.create_user`
- `admin.edit_user`
- `admin.delete_user`
- `admin.manage_roles`
- `admin.create_role`
- `admin.edit_role`
- `admin.delete_role`
- `admin.manage_permissions`
- `admin.manage_sites`
- `admin.view_logs`
- `admin.manage_patrimoines`
- `admin.manage_demandes`

**Commercial (18 permissions) :**
- `commercial.view_dashboard`
- `commercial.manage_clients`
- `commercial.create_client`
- `commercial.edit_client`
- `commercial.delete_client`
- `commercial.view_clients`
- `commercial.manage_devis`
- `commercial.create_devis`
- `commercial.edit_devis`
- `commercial.delete_devis`
- `commercial.view_devis`
- `commercial.manage_opportunities`
- `commercial.create_opportunity`
- `commercial.edit_opportunity`
- `commercial.delete_opportunity`
- `commercial.view_opportunities`
- `commercial.view_reports`
- `commercial.export_data`

**Débours (15 permissions) :**
- `debours.view_dashboard`
- `debours.view_expenses`
- `debours.create_expense`
- `debours.edit_expense`
- `debours.delete_expense`
- `debours.approve_expenses`
- `debours.reject_expenses`
- `debours.create_payment`
- `debours.view_payments`
- `debours.manage_dossiers`
- `debours.create_dossier`
- `debours.edit_dossier`
- `debours.delete_dossier`
- `debours.view_reports`
- `debours.export_data`

## 🔒 Étape 7 : Sécurité

### CORS Configuration

Le fichier `config/cors.php` autorise les requêtes depuis :
- `administration.mgs.mg`
- `commercial.mgs.mg`
- `debours.mgs.mg`

### Sanctum Configuration

Le fichier `config/sanctum.php` définit les domaines de confiance pour les cookies d'authentification.

### Tokens

- Les tokens expirent après 7 jours par défaut
- Chaque token peut être révoqué individuellement
- Les utilisateurs peuvent voir tous leurs tokens actifs

## 🎨 Étape 8 : Interface d'administration

### Accès à l'organigramme

URL : `https://administration.mgs.mg/organigramme`

Visualisez :
- Architecture du système
- Flux d'authentification
- Rôles et permissions
- Statistiques

### Gestion des utilisateurs

URL : `https://administration.mgs.mg/admin/users`

Fonctionnalités :
- Créer un utilisateur
- Modifier un utilisateur
- Attribuer des rôles
- Révoquer les tokens
- Supprimer un utilisateur

### Gestion des rôles

URL : `https://administration.mgs.mg/admin/roles`

Fonctionnalités :
- Créer un rôle
- Modifier un rôle
- Attribuer des permissions
- Voir les utilisateurs du rôle
- Supprimer un rôle

## 🧪 Étape 9 : Tests

### Tester la connexion SSO

1. Visitez : `https://administration.mgs.mg/sso/login?callback=https://google.com&site=commercial`
2. Connectez-vous avec `admin@mgs.mg` / `password`
3. Vous devriez être redirigé vers Google avec un token dans l'URL

### Tester l'API

```bash
# Se connecter et récupérer un token
TOKEN="votre_token_ici"

# Vérifier le token
curl -X POST https://administration.mgs.mg/api/auth/verify-token \
  -H "Authorization: Bearer $TOKEN"

# Récupérer les infos utilisateur
curl -X GET https://administration.mgs.mg/api/auth/me \
  -H "Authorization: Bearer $TOKEN"
```

## 📝 Étape 10 : Prochaines étapes

Pour les sites clients (commercial.mgs.mg et debours.mgs.mg), vous devrez créer :

1. **Middleware d'authentification centralisée** - `CentralAuth.php`
2. **Middleware de vérification des permissions** - `CheckSitePermission.php`
3. **Routes d'authentification** - Login, callback, logout
4. **Configuration** - `.env` avec l'URL du serveur central

Exemple de configuration client dans un prochain document.

## 🎯 Résumé

✅ Sanctum installé et configuré  
✅ Spatie Permission installé  
✅ Table sites créée  
✅ API d'authentification complète  
✅ Interface SSO créée  
✅ Gestion des utilisateurs  
✅ Gestion des rôles  
✅ Seeders de données  
✅ CORS configuré  
✅ Organigramme visuel  

Le système d'authentification centralisée est maintenant opérationnel ! 🚀
