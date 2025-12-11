# Instructions de Migration - Authentification Centralisée

## 🎯 Objectif

Rediriger toutes les routes de connexion et d'inscription des applications **Commercial** et **Gestion Dossier** vers le système d'authentification centralisé dans **Administration**.

---

## 📱 Application COMMERCIAL

### Fichier : `/var/www/commercial/routes/web.php`

Ajouter ces routes au début du fichier :

```php
use Illuminate\Support\Facades\Route;

// Redirection vers l'authentification centralisée
Route::get('/login', function() {
    $adminUrl = config('app_urls.apps.administration.login', 'http://localhost/administration/auth/login');
    return redirect($adminUrl . '?site=commercial&callback=' . urlencode(url('/dashboard')));
})->name('login');

Route::get('/register', function() {
    $adminUrl = str_replace('/login', '/register', config('app_urls.apps.administration.login', 'http://localhost/administration/auth/register'));
    return redirect($adminUrl . '?site=commercial');
})->name('register');

Route::get('/logout', function() {
    $adminUrl = config('app_urls.apps.administration.logout', 'http://localhost/administration/auth/logout');
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect($adminUrl);
})->name('logout');

// Route pour recevoir le token SSO
Route::get('/sso/callback', function() {
    $token = request()->get('token');
    
    if (!$token) {
        return redirect()->route('login');
    }
    
    try {
        // Valider le token auprès de l'API Administration
        $response = Http::withToken($token)
            ->get(config('app_urls.administration') . '/api/user');
        
        if ($response->successful()) {
            $userData = $response->json();
            $user = \App\Models\User::where('email', $userData['email'])->first();
            
            if ($user) {
                Auth::login($user);
                return redirect('/dashboard')->with('success', 'Connexion réussie');
            }
        }
    } catch (\Exception $e) {
        \Log::error('SSO Token validation failed: ' . $e->getMessage());
    }
    
    return redirect()->route('login')->with('error', 'Authentification échouée');
})->name('sso.callback');
```

### Fichier : `/var/www/commercial/config/app_urls.php`

Créer ou mettre à jour le fichier :

```php
<?php

return [
    'administration' => env('ADMIN_APP_URL', 'http://localhost/administration'),
    'commercial' => env('COMMERCIAL_APP_URL', 'http://localhost/commercial'),
    'gestion' => env('GESTION_DOSSIER_APP_URL', 'http://localhost/gestion-dossier'),

    'apps' => [
        'administration' => [
            'url' => env('ADMIN_APP_URL', 'http://localhost/administration'),
            'login' => env('ADMIN_APP_URL', 'http://localhost/administration') . '/auth/login',
            'register' => env('ADMIN_APP_URL', 'http://localhost/administration') . '/auth/register',
            'logout' => env('ADMIN_APP_URL', 'http://localhost/administration') . '/auth/logout',
            'api' => env('ADMIN_APP_URL', 'http://localhost/administration') . '/api',
        ],
    ],
];
```

### Fichier : `/var/www/commercial/.env`

Ajouter :

```env
ADMIN_APP_URL=http://localhost/administration
COMMERCIAL_APP_URL=http://localhost/commercial
GESTION_DOSSIER_APP_URL=http://localhost/gestion-dossier
```

---

## 📁 Application GESTION DOSSIER

### Fichier : `/var/www/gestion-dossier/routes/web.php`

Ajouter ces routes au début du fichier :

```php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

// Redirection vers l'authentification centralisée
Route::get('/login', function() {
    $adminUrl = config('app_urls.apps.administration.login', 'http://localhost/administration/auth/login');
    return redirect($adminUrl . '?site=gestion&callback=' . urlencode(url('/dashboard')));
})->name('login');

Route::get('/register', function() {
    $adminUrl = str_replace('/login', '/register', config('app_urls.apps.administration.login', 'http://localhost/administration/auth/register'));
    return redirect($adminUrl . '?site=gestion');
})->name('register');

Route::get('/logout', function() {
    $adminUrl = config('app_urls.apps.administration.logout', 'http://localhost/administration/auth/logout');
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect($adminUrl);
})->name('logout');

// Route pour recevoir le token SSO
Route::get('/sso/callback', function() {
    $token = request()->get('token');
    
    if (!$token) {
        return redirect()->route('login');
    }
    
    try {
        // Valider le token auprès de l'API Administration
        $response = Http::withToken($token)
            ->get(config('app_urls.administration') . '/api/user');
        
        if ($response->successful()) {
            $userData = $response->json();
            $user = \App\Models\User::where('email', $userData['email'])->first();
            
            if ($user) {
                Auth::login($user);
                return redirect('/dashboard')->with('success', 'Connexion réussie');
            }
        }
    } catch (\Exception $e) {
        \Log::error('SSO Token validation failed: ' . $e->getMessage());
    }
    
    return redirect()->route('login')->with('error', 'Authentification échouée');
})->name('sso.callback');
```

### Fichier : `/var/www/gestion-dossier/config/app_urls.php`

Créer ou mettre à jour le fichier :

```php
<?php

return [
    'administration' => env('ADMIN_APP_URL', 'http://localhost/administration'),
    'commercial' => env('COMMERCIAL_APP_URL', 'http://localhost/commercial'),
    'gestion' => env('GESTION_DOSSIER_APP_URL', 'http://localhost/gestion-dossier'),

    'apps' => [
        'administration' => [
            'url' => env('ADMIN_APP_URL', 'http://localhost/administration'),
            'login' => env('ADMIN_APP_URL', 'http://localhost/administration') . '/auth/login',
            'register' => env('ADMIN_APP_URL', 'http://localhost/administration') . '/auth/register',
            'logout' => env('ADMIN_APP_URL', 'http://localhost/administration') . '/auth/logout',
            'api' => env('ADMIN_APP_URL', 'http://localhost/administration') . '/api',
        ],
    ],
];
```

### Fichier : `/var/www/gestion-dossier/.env`

Ajouter :

```env
ADMIN_APP_URL=http://localhost/administration
COMMERCIAL_APP_URL=http://localhost/commercial
GESTION_DOSSIER_APP_URL=http://localhost/gestion-dossier
```

---

## 🔐 API d'authentification (Administration)

### Fichier : `/var/www/administration/routes/api.php`

Ajouter cette route pour permettre la validation des tokens SSO :

```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->get('/verify-token', function (Request $request) {
    return response()->json([
        'valid' => true,
        'user' => $request->user(),
    ]);
});
```

---

## 🔄 Mise à jour des layouts

### Dans Commercial et Gestion Dossier

Mettre à jour les liens de déconnexion dans les layouts :

**Avant :**
```blade
<a href="{{ route('logout') }}">Déconnexion</a>
```

**Après :**
```blade
<a href="{{ config('app_urls.apps.administration.logout') }}">Déconnexion</a>
```

Ou si vous gardez la route locale :
```blade
<a href="{{ route('logout') }}">Déconnexion</a>
```

---

## ✅ Checklist de migration

### Pour COMMERCIAL

- [ ] Mettre à jour `/routes/web.php`
- [ ] Créer/mettre à jour `/config/app_urls.php`
- [ ] Ajouter les variables dans `.env`
- [ ] Mettre à jour les layouts
- [ ] Tester la connexion
- [ ] Tester la déconnexion
- [ ] Tester la création de compte

### Pour GESTION DOSSIER

- [ ] Mettre à jour `/routes/web.php`
- [ ] Créer/mettre à jour `/config/app_urls.php`
- [ ] Ajouter les variables dans `.env`
- [ ] Mettre à jour les layouts
- [ ] Tester la connexion
- [ ] Tester la déconnexion
- [ ] Tester la création de compte

### Pour ADMINISTRATION

- [ ] ✅ Pages auth/login et auth/register créées
- [ ] ✅ AuthController créé
- [ ] ✅ Routes configurées
- [ ] ✅ Configuration app_urls mise à jour
- [ ] Ajouter la route API pour validation des tokens
- [ ] Tester avec les autres applications

---

## 🧪 Tests

### Test de connexion

1. Accéder à `http://localhost/commercial`
2. Cliquer sur "Connexion"
3. Devrait rediriger vers `http://localhost/administration/auth/login?site=commercial`
4. Se connecter
5. Devrait rediriger vers `http://localhost/commercial/dashboard` avec le token

### Test de création de compte

1. Accéder à `http://localhost/gestion-dossier`
2. Cliquer sur "Créer un compte"
3. Devrait rediriger vers `http://localhost/administration/auth/register?site=gestion`
4. Remplir le formulaire
5. Devrait créer le compte et rediriger vers `http://localhost/gestion-dossier/dashboard`

---

## 🐛 Dépannage

### Problème : Boucle de redirection

**Cause :** Les URLs ne sont pas correctement configurées

**Solution :** Vérifier les variables d'environnement dans `.env`

### Problème : Token invalide

**Cause :** L'API de validation n'est pas accessible ou le token a expiré

**Solution :** 
- Vérifier que la route `/api/user` existe dans Administration
- Vérifier que Sanctum est bien configuré
- Vérifier les logs : `tail -f storage/logs/laravel.log`

### Problème : Utilisateur non trouvé après SSO

**Cause :** L'utilisateur existe dans Administration mais pas dans l'application cible

**Solution :** Synchroniser les utilisateurs entre les bases de données ou utiliser une base commune

---

## 📞 Support

En cas de problème, consulter :
- `GUIDE_AUTHENTIFICATION.md` pour la documentation complète
- Logs Laravel : `storage/logs/laravel.log`
- Contact : admin@mgs.mg
