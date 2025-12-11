# Guide d'Authentification Centralisée MGS

## 📋 Vue d'ensemble

Le système MGS dispose désormais d'une **authentification centralisée** qui permet aux utilisateurs de se connecter une seule fois et d'accéder aux trois applications :
- 🔐 **Administration** - Gestion du personnel, congés, organigramme
- 📊 **Commercial** - CRM, devis, opportunités
- 📁 **Gestion Dossier** - Gestion des dossiers clients

## 🎯 Fonctionnalités principales

### ✨ Pages centralisées
- **Page de connexion unique** : `/auth/login`
- **Page d'inscription unique** : `/auth/register`
- **Déconnexion centralisée** : `/auth/logout`

### 🔄 Sélection de l'application
Les utilisateurs peuvent choisir l'application à laquelle ils souhaitent accéder :
- Directement sur la page de connexion (badges cliquables)
- Via l'URL : `/auth/login?site=admin|commercial|gestion`

## 🚀 Utilisation

### Pour se connecter

1. **Accéder à la page de connexion**
   ```
   http://votre-domaine/auth/login
   ```

2. **Sélectionner l'application**
   - Cliquer sur le badge de l'application souhaitée :
     - 🔐 Administration
     - 📊 Commercial
     - 📁 Gestion Dossier

3. **Saisir les identifiants**
   - Email
   - Mot de passe
   - Cocher "Se souvenir de moi" (optionnel)

4. **Se connecter**
   - Le système vérifie les permissions
   - Redirige vers l'application appropriée
   - Crée un token SSO si nécessaire

### Pour créer un compte

1. **Accéder à la page d'inscription**
   ```
   http://votre-domaine/auth/register
   ```

2. **Remplir le formulaire**
   - Nom et prénom
   - Adresse email (unique)
   - Mot de passe (min. 8 caractères)
   - Téléphone (optionnel)
   - Poste (optionnel)

3. **Sélectionner l'application cible**
   - Le système assignera automatiquement un rôle par défaut

4. **Accepter les conditions**
   - Cocher la case des conditions d'utilisation

5. **Créer le compte**
   - Connexion automatique après création
   - Redirection vers l'application choisie

## 🔐 Sécurité et permissions

### Contrôle d'accès

Le système vérifie que l'utilisateur a les permissions nécessaires pour accéder à l'application demandée.

**Rôles par défaut :**
- `admin-viewer` - Pour Administration
- `commercial-user` - Pour Commercial
- `gestion-user` - Pour Gestion Dossier

**Super Admin :**
- A accès à toutes les applications
- Peut gérer les utilisateurs et leurs permissions

### Gestion des sessions

- Session unique partagée entre les applications
- Token SSO avec expiration (7 jours par défaut)
- Déconnexion globale sur toutes les applications

## 🔧 Configuration

### Fichiers importants

1. **Routes** : `/routes/web.php`
   ```php
   Route::prefix('auth')->name('auth.')->group(function () {
       Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
       Route::post('/login', [AuthController::class, 'login']);
       Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
       Route::post('/register', [AuthController::class, 'register']);
       Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
   });
   ```

2. **Contrôleur** : `/app/Http/Controllers/Auth/AuthController.php`
   - Gère l'authentification
   - Vérifie les permissions
   - Redirige vers les applications

3. **Configuration** : `/config/app_urls.php`
   ```php
   'sites' => [
       'admin' => [...],
       'commercial' => [...],
       'gestion' => [...],
   ]
   ```

4. **Vues** :
   - `/resources/views/auth/login.blade.php`
   - `/resources/views/auth/register.blade.php`

### Variables d'environnement

Dans votre fichier `.env`, configurez les URLs des applications :

```env
# URLs des applications
ADMIN_APP_URL=http://localhost/administration
COMMERCIAL_APP_URL=http://localhost/commercial
GESTION_DOSSIER_APP_URL=http://localhost/gestion-dossier

# Configuration SSO
SSO_ENABLED=true
SSO_TOKEN_LIFETIME=7
SSO_AUTO_REDIRECT=true

# Domaine de session (si applications sur sous-domaines)
SESSION_DOMAIN=.mgs.local
```

## 🌐 Intégration avec les autres applications

### Commercial

Dans l'application Commercial, rediriger vers l'authentification centralisée :

```php
// routes/web.php
Route::get('/login', function() {
    $adminUrl = config('app_urls.apps.administration.login');
    return redirect($adminUrl . '?site=commercial');
})->name('login');
```

### Gestion Dossier

Dans l'application Gestion Dossier, même principe :

```php
// routes/web.php
Route::get('/login', function() {
    $adminUrl = config('app_urls.apps.administration.login');
    return redirect($adminUrl . '?site=gestion');
})->name('login');
```

### Réception du token SSO

Dans les applications Commercial et Gestion Dossier, créer un middleware pour accepter le token :

```php
// Middleware SSOAuth
public function handle($request, Closure $next)
{
    if ($token = $request->get('token')) {
        // Valider le token auprès de l'application Administration
        // Connecter l'utilisateur
        // Rediriger vers le dashboard
    }
    
    if (!Auth::check()) {
        return redirect(config('app_urls.apps.administration.login') . '?site=commercial');
    }
    
    return $next($request);
}
```

## 📊 Workflow d'authentification

```
┌─────────────────┐
│  Utilisateur    │
└────────┬────────┘
         │
         ▼
┌─────────────────────────────┐
│  /auth/login?site=commercial│
└────────┬────────────────────┘
         │
         ▼
┌─────────────────────────────┐
│  Saisie identifiants        │
│  Email + Password           │
└────────┬────────────────────┘
         │
         ▼
┌─────────────────────────────┐
│  Vérification permissions   │
│  User → commercial.*        │
└────────┬────────────────────┘
         │
    ┌────┴────┐
    │         │
    ▼         ▼
  ✅ OK     ❌ NOK
    │         │
    │         └──────────────────┐
    │                            │
    ▼                            ▼
┌─────────────────┐      ┌──────────────┐
│ Création token  │      │ Erreur accès │
│ SSO             │      │ refusé       │
└────────┬────────┘      └──────────────┘
         │
         ▼
┌─────────────────────────────┐
│ Redirection Commercial      │
│ avec token dans l'URL       │
└────────┬────────────────────┘
         │
         ▼
┌─────────────────────────────┐
│ Commercial valide token     │
│ et connecte l'utilisateur   │
└─────────────────────────────┘
```

## 🎨 Personnalisation

### Modifier les couleurs et styles

Les fichiers de vue (`login.blade.php` et `register.blade.php`) contiennent des CSS inline que vous pouvez personnaliser :

```css
:root {
    --primary-color: #667eea;    /* Couleur principale */
    --secondary-color: #764ba2;   /* Couleur secondaire */
    --success-color: #10b981;     /* Succès */
    --danger-color: #ef4444;      /* Erreur */
}
```

### Ajouter une nouvelle application

1. **Mettre à jour `config/app_urls.php`** :
   ```php
   'sites' => [
       // ...existing sites
       'nouvelle-app' => [
           'name' => 'Nouvelle Application',
           'code' => 'nouvelle-app',
           'url' => env('NOUVELLE_APP_URL', 'http://localhost/nouvelle-app'),
           'icon' => 'fas fa-star',
           'color' => '#3b82f6',
           'description' => 'Description de la nouvelle app',
       ],
   ]
   ```

2. **Mettre à jour les vues** :
   Ajouter le badge dans `login.blade.php` et `register.blade.php`

3. **Mettre à jour le contrôleur** :
   Ajouter la logique de redirection dans `AuthController.php`

## 🐛 Dépannage

### Problème : Erreur "Vous n'avez pas accès à ce site"

**Solution :** Vérifier que l'utilisateur a bien les permissions pour le site demandé.
```bash
php artisan permission:show USER_EMAIL
```

### Problème : Redirection en boucle

**Solution :** Vérifier la configuration des URLs dans `.env` et `config/app_urls.php`

### Problème : Token SSO invalide

**Solution :** Vérifier que Sanctum est bien configuré et que les tokens n'ont pas expiré.

## 📝 Logs et monitoring

Toutes les connexions sont journalisées avec :
- Utilisateur
- Application cible
- Adresse IP
- User Agent
- Timestamp

Accessible via le package `spatie/laravel-activitylog` :
```php
Activity::causedBy($user)->where('description', 'Connexion réussie')->get();
```

## 🔄 Migration depuis l'ancien système

1. **Mettre à jour les liens de connexion**
   - Remplacer `/login` par `/auth/login`
   - Remplacer `/register` par `/auth/register`

2. **Mettre à jour les layouts**
   - Utiliser `route('auth.logout')` au lieu de `route('logout')`

3. **Tester les permissions**
   - Vérifier que tous les utilisateurs ont les bonnes permissions

## ✅ Checklist de déploiement

- [ ] Configurer les URLs dans `.env`
- [ ] Vérifier les permissions des utilisateurs
- [ ] Tester la connexion sur chaque application
- [ ] Tester la création de compte
- [ ] Vérifier les tokens SSO
- [ ] Tester la déconnexion
- [ ] Vérifier les logs
- [ ] Former les utilisateurs

## 📞 Support

Pour toute question ou problème :
- Documentation complète : `/GUIDE_AUTHENTIFICATION.md`
- Logs : `storage/logs/laravel.log`
- Contact : admin@mgs.mg
