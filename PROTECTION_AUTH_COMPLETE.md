# 🔐 Protection par Authentification - Tous les Sites

## 📋 Vue d'ensemble

Toutes les pages des 3 applications (Administration, Commercial, Gestion Dossier) nécessitent maintenant une authentification. Les utilisateurs non connectés sont automatiquement redirigés vers la page de login centralisée.

---

## 🎯 Comportement

### Utilisateur non connecté
```
Utilisateur → /n'importe-quelle-page
    ↓
❌ Non connecté
    ↓
Redirection → /auth/login?site=XXX&callback=url-origine
    ↓
Connexion réussie
    ↓
Retour → url-origine
```

---

## 🔧 Implémentation

### 1️⃣ ADMINISTRATION

#### Middleware créé
**Fichier:** `/app/Http/Middleware/EnsureAuthenticated.php`
- Vérifie si l'utilisateur est connecté
- Redirige vers `/auth/login?site=admin` si non connecté
- Préserve l'URL d'origine pour redirection après login

#### Configuration routes
**Fichier:** `/routes/web.php`

```php
// Toutes les routes protégées sauf auth
Route::middleware(['web', EnsureAuthenticated::class])->group(function () {
    Route::get('/', ...);
    Route::get('/dashboard', ...);
    // ... toutes les autres routes
});

// Routes auth publiques
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    // ...
});
```

#### Enregistrement du middleware
**Fichier:** `/app/Http/Kernel.php`

```php
protected $routeMiddleware = [
    // ...
    'ensure.auth' => \App\Http\Middleware\EnsureAuthenticated::class,
];
```

---

### 2️⃣ COMMERCIAL

#### Routes à ajouter
**Fichier:** `/var/www/commercial/routes/web.php`

```php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirection login/register vers Administration
Route::get('/login', function() {
    $adminUrl = config('app_urls.administration', 'http://localhost/administration');
    $callback = request()->get('callback', url('/dashboard'));
    return redirect($adminUrl . '/auth/login?site=commercial&callback=' . urlencode($callback));
})->name('login');

Route::get('/register', function() {
    $adminUrl = config('app_urls.administration', 'http://localhost/administration');
    return redirect($adminUrl . '/auth/register?site=commercial');
})->name('register');

Route::get('/logout', function() {
    $adminUrl = config('app_urls.administration', 'http://localhost/administration');
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect($adminUrl . '/auth/logout');
})->name('logout');

// Callback SSO - Reçoit le token après connexion
Route::get('/auth/callback', function() {
    $token = request()->get('token');
    
    if (!$token) {
        return redirect()->route('login');
    }
    
    try {
        $response = \Illuminate\Support\Facades\Http::withToken($token)
            ->get(config('app_urls.administration') . '/api/user');
        
        if ($response->successful()) {
            $userData = $response->json();
            $user = \App\Models\User::where('email', $userData['email'])->first();
            
            if ($user) {
                Auth::login($user);
                
                // Rediriger vers l'URL callback ou dashboard
                $callback = request()->get('return_url', '/dashboard');
                return redirect($callback)->with('success', 'Connexion réussie');
            }
        }
    } catch (\Exception $e) {
        \Log::error('SSO callback failed: ' . $e->getMessage());
    }
    
    return redirect()->route('login')->with('error', 'Authentification échouée');
})->name('auth.callback');

// TOUTES les autres routes doivent être protégées
Route::middleware(['auth'])->group(function () {
    Route::get('/', function() {
        return redirect('/dashboard');
    });
    
    Route::get('/dashboard', function() {
        return view('dashboard');
    })->name('dashboard');
    
    // ... toutes vos autres routes
});
```

#### Configuration
**Fichier:** `/var/www/commercial/config/app_urls.php`

```php
<?php

return [
    'administration' => env('ADMIN_APP_URL', 'http://localhost/administration'),
    'commercial' => env('COMMERCIAL_APP_URL', 'http://localhost/commercial'),
    'gestion' => env('GESTION_DOSSIER_APP_URL', 'http://localhost/gestion-dossier'),
];
```

#### Variables d'environnement
**Fichier:** `/var/www/commercial/.env`

```env
ADMIN_APP_URL=http://localhost/administration
COMMERCIAL_APP_URL=http://localhost/commercial
GESTION_DOSSIER_APP_URL=http://localhost/gestion-dossier
```

---

### 3️⃣ GESTION DOSSIER (deboursweb)

#### Routes à ajouter
**Fichier:** `/var/www/gestion-dossier/routes/web.php`

```php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirection login/register vers Administration
Route::get('/login', function() {
    $adminUrl = config('app_urls.administration', 'http://localhost/administration');
    $callback = request()->get('callback', url('/dashboard'));
    return redirect($adminUrl . '/auth/login?site=gestion&callback=' . urlencode($callback));
})->name('login');

Route::get('/register', function() {
    $adminUrl = config('app_urls.administration', 'http://localhost/administration');
    return redirect($adminUrl . '/auth/register?site=gestion');
})->name('register');

Route::get('/logout', function() {
    $adminUrl = config('app_urls.administration', 'http://localhost/administration');
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect($adminUrl . '/auth/logout');
})->name('logout');

// Callback SSO - Reçoit le token après connexion
Route::get('/auth/callback', function() {
    $token = request()->get('token');
    
    if (!$token) {
        return redirect()->route('login');
    }
    
    try {
        $response = \Illuminate\Support\Facades\Http::withToken($token)
            ->get(config('app_urls.administration') . '/api/user');
        
        if ($response->successful()) {
            $userData = $response->json();
            $user = \App\Models\User::where('email', $userData['email'])->first();
            
            if ($user) {
                Auth::login($user);
                
                // Rediriger vers l'URL callback ou dashboard
                $callback = request()->get('return_url', '/dashboard');
                return redirect($callback)->with('success', 'Connexion réussie');
            }
        }
    } catch (\Exception $e) {
        \Log::error('SSO callback failed: ' . $e->getMessage());
    }
    
    return redirect()->route('login')->with('error', 'Authentification échouée');
})->name('auth.callback');

// TOUTES les autres routes doivent être protégées
Route::middleware(['auth'])->group(function () {
    Route::get('/', function() {
        return redirect('/dashboard');
    });
    
    Route::get('/dashboard', function() {
        return view('dashboard');
    })->name('dashboard');
    
    // ... toutes vos autres routes existantes
});
```

#### Configuration
**Fichier:** `/var/www/gestion-dossier/config/app_urls.php`

```php
<?php

return [
    'administration' => env('ADMIN_APP_URL', 'http://localhost/administration'),
    'commercial' => env('COMMERCIAL_APP_URL', 'http://localhost/commercial'),
    'gestion' => env('GESTION_DOSSIER_APP_URL', 'http://localhost/gestion-dossier'),
];
```

#### Variables d'environnement
**Fichier:** `/var/www/gestion-dossier/.env`

```env
ADMIN_APP_URL=http://localhost/administration
COMMERCIAL_APP_URL=http://localhost/commercial
GESTION_DOSSIER_APP_URL=http://localhost/gestion-dossier
```

---

## 🔄 Flux d'authentification complet

```
┌─────────────────────────────────────────────────────────────┐
│  Utilisateur accède à /commercial/tableau-de-bord           │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
                  ┌──────────────┐
                  │ Connecté ?   │
                  └──────┬───────┘
                         │
                    ┌────┴────┐
                    │         │
                 NON│         │OUI
                    │         │
                    ▼         ▼
         ┌─────────────────┐  │
         │ Redirection     │  │
         │ /auth/login     │  │
         │ ?site=commercial│  │
         │ &callback=...   │  │
         └────────┬────────┘  │
                  │           │
                  ▼           │
         ┌─────────────────┐  │
         │ Page de login   │  │
         │ Administration  │  │
         └────────┬────────┘  │
                  │           │
                  ▼           │
         ┌─────────────────┐  │
         │ User se         │  │
         │ connecte        │  │
         └────────┬────────┘  │
                  │           │
                  ▼           │
         ┌─────────────────┐  │
         │ Vérif perms     │  │
         │ commercial.*    │  │
         └────────┬────────┘  │
                  │           │
                  ▼           │
         ┌─────────────────┐  │
         │ Crée token SSO  │  │
         └────────┬────────┘  │
                  │           │
                  ▼           │
         ┌─────────────────┐  │
         │ Redirect        │  │
         │ /auth/callback  │  │
         │ ?token=xxx      │  │
         └────────┬────────┘  │
                  │           │
                  ▼           │
         ┌─────────────────┐  │
         │ Valide token    │  │
         │ Connecte user   │  │
         └────────┬────────┘  │
                  │           │
                  └───────────┴──────┐
                                     ▼
                          ┌──────────────────────┐
                          │ Accès à la page      │
                          │ /tableau-de-bord     │
                          └──────────────────────┘
```

---

## 📝 Mise à jour des middlewares

### Dans Administration

**Fichier:** `/app/Http/Kernel.php`

```php
protected $routeMiddleware = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'ensure.auth' => \App\Http\Middleware\EnsureAuthenticated::class,
    // ... autres middlewares
];

protected $middlewareGroups = [
    'web' => [
        // ... middlewares existants
    ],
];
```

### Dans Commercial et Gestion Dossier

Utiliser le middleware `auth` standard de Laravel qui redirigera automatiquement vers `route('login')` que nous avons configuré.

---

## ✅ Checklist d'implémentation

### Administration
- [x] Middleware `EnsureAuthenticated` créé
- [ ] Middleware enregistré dans `Kernel.php`
- [ ] Routes protégées avec le middleware
- [ ] Routes auth publiques (login, register)
- [x] API `/api/user` pour validation SSO

### Commercial
- [ ] Routes login/register redirigent vers Administration
- [ ] Route callback SSO créée
- [ ] Configuration `app_urls.php` créée
- [ ] Variables `.env` configurées
- [ ] Toutes les routes protégées par middleware `auth`
- [ ] Tester la redirection
- [ ] Tester le callback SSO

### Gestion Dossier
- [ ] Routes login/register redirigent vers Administration
- [ ] Route callback SSO créée
- [ ] Configuration `app_urls.php` créée
- [ ] Variables `.env` configurées
- [ ] Toutes les routes protégées par middleware `auth`
- [ ] Tester la redirection
- [ ] Tester le callback SSO

---

## 🧪 Tests

### Test 1: Accès direct à une page protégée

```bash
# Sans connexion
curl -I http://localhost/commercial/dashboard

# Devrait retourner une redirection 302 vers:
# http://localhost/administration/auth/login?site=commercial&callback=...
```

### Test 2: Connexion et redirection

1. Accéder à `http://localhost/commercial/dashboard`
2. Redirection vers login Administration
3. Se connecter
4. Devrait revenir à `/commercial/dashboard`

### Test 3: Session partagée

1. Se connecter sur Administration
2. Accéder directement à Commercial
3. Devrait être déjà connecté (si même domaine)

---

## 🔐 Sécurité

### Points importants

1. **Toutes les routes doivent être protégées** sauf:
   - `/auth/login`
   - `/auth/register`
   - `/auth/callback`
   - Routes publiques spécifiques

2. **Validation du token SSO**
   - Vérifier l'expiration
   - Valider auprès de l'API Administration
   - Créer une session locale après validation

3. **Logs de sécurité**
   - Logger toutes les tentatives d'accès non autorisées
   - Logger les validations de tokens SSO
   - Alerter sur les comportements suspects

---

## 🐛 Dépannage

### Problème: Boucle de redirection

**Cause:** Le middleware `auth` redirige vers `login` qui redirige vers lui-même

**Solution:** S'assurer que les routes auth sont exclues du middleware

```php
Route::get('/login', ...)->withoutMiddleware(['auth']);
```

### Problème: Token SSO invalide

**Cause:** Token expiré ou malformé

**Solution:**
- Vérifier la durée de vie du token dans `.env`
- Vérifier que Sanctum est bien configuré
- Consulter les logs: `tail -f storage/logs/laravel.log`

### Problème: Utilisateur non trouvé après SSO

**Cause:** L'utilisateur existe dans Administration mais pas dans l'app cible

**Solution:**
- Synchroniser les utilisateurs entre les bases
- Ou utiliser une base de données commune
- Ou créer automatiquement l'utilisateur local

---

## 📊 Résumé

### Ce qui a été fait

✅ Middleware de protection créé pour Administration  
✅ Documentation complète des redirections  
✅ Code pour Commercial et Gestion Dossier  
✅ Configuration SSO callback  
✅ Flux d'authentification documenté  

### Ce qu'il reste à faire

1. **Enregistrer le middleware** dans `Kernel.php` (Administration)
2. **Protéger toutes les routes** avec le middleware (Administration)
3. **Ajouter les routes** dans Commercial
4. **Ajouter les routes** dans Gestion Dossier
5. **Configurer les .env** dans les 3 applications
6. **Tester** le flux complet

---

## 🚀 Commandes rapides

### Administration
```bash
# Enregistrer le middleware et tester
php artisan route:clear
php artisan config:clear
php artisan route:list
```

### Commercial
```bash
cd /var/www/commercial
# Créer la config
touch config/app_urls.php
# Éditer routes/web.php
# Tester
curl -I http://localhost/commercial/dashboard
```

### Gestion Dossier
```bash
cd /var/www/gestion-dossier
# Créer la config
touch config/app_urls.php
# Éditer routes/web.php
# Tester
curl -I http://localhost/gestion-dossier/dashboard
```

---

**Documentation complète:** Voir aussi `MIGRATION_AUTH_CENTRALISEE.md`
