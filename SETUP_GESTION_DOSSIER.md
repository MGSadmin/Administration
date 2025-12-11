# 🚀 MISE EN PLACE RAPIDE - Protection Auth pour Gestion Dossier

## 📋 Étapes à suivre

### 1️⃣ Créer la configuration des URLs

**Créer le fichier:** `/var/www/gestion-dossier/config/app_urls.php`

```php
<?php

return [
    'administration' => env('ADMIN_APP_URL', 'http://localhost/administration'),
    'commercial' => env('COMMERCIAL_APP_URL', 'http://localhost/commercial'),
    'gestion' => env('GESTION_DOSSIER_APP_URL', 'http://localhost/gestion-dossier'),
];
```

### 2️⃣ Mettre à jour .env

**Ajouter dans:** `/var/www/gestion-dossier/.env`

```env
ADMIN_APP_URL=http://localhost/administration
COMMERCIAL_APP_URL=http://localhost/commercial
GESTION_DOSSIER_APP_URL=http://localhost/gestion-dossier
```

### 3️⃣ Modifier routes/web.php

**Ajouter AU DÉBUT du fichier:** `/var/www/gestion-dossier/routes/web.php`

```php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

// ============================================
// Routes publiques - Redirection vers auth centralisée
// ============================================
Route::get('/login', function() {
    $adminUrl = config('app_urls.administration', 'http://localhost/administration');
    $callback = request()->get('callback', url('/dashboard'));
    return redirect($adminUrl . '/auth/login?site=gestion&callback=' . urlencode($callback));
})->name('login');

Route::get('/register', function() {
    $adminUrl = config('app_urls.administration', 'http://localhost/administration');
    return redirect($adminUrl . '/auth/register?site=gestion');
})->name('register');

Route::post('/logout', function() {
    $adminUrl = config('app_urls.administration', 'http://localhost/administration');
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect($adminUrl . '/auth/logout');
})->name('logout');

Route::get('/logout', function() {
    return redirect()->route('logout');
});

// Callback SSO - Reçoit le token après connexion
Route::get('/auth/callback', function() {
    $token = request()->get('token');
    
    if (!$token) {
        return redirect()->route('login')->with('error', 'Token manquant');
    }
    
    try {
        // Valider le token auprès de l'API Administration
        $adminUrl = config('app_urls.administration');
        $response = Http::withToken($token)->get($adminUrl . '/api/user');
        
        if ($response->successful()) {
            $userData = $response->json();
            
            // Trouver ou créer l'utilisateur local
            $user = \App\Models\User::where('email', $userData['email'])->first();
            
            if (!$user) {
                // Créer l'utilisateur s'il n'existe pas localement
                $user = \App\Models\User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => \Hash::make(\Str::random(32)), // Mot de passe aléatoire
                ]);
            }
            
            // Connecter l'utilisateur
            Auth::login($user);
            
            // Rediriger vers l'URL callback ou dashboard
            $callback = request()->get('return_url', '/dashboard');
            return redirect($callback)->with('success', 'Connexion réussie !');
        }
        
        \Log::error('SSO validation failed', ['status' => $response->status()]);
        
    } catch (\Exception $e) {
        \Log::error('SSO callback error: ' . $e->getMessage());
    }
    
    return redirect()->route('login')->with('error', 'Authentification échouée. Veuillez réessayer.');
})->name('auth.callback');

// ============================================
// Toutes les autres routes PROTÉGÉES
// ============================================
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/', function() {
        return redirect('/dashboard');
    });
    
    Route::get('/dashboard', function() {
        return view('dashboard');
    })->name('dashboard');
    
    // *** IMPORTANT: Déplacer TOUTES vos routes existantes ici ***
    // Exemple:
    // Route::resource('dossiers', DossierController::class);
    // Route::resource('clients', ClientController::class);
    // etc.
    
});
```

### 4️⃣ Déplacer toutes les routes existantes

**Toutes** vos routes existantes doivent être à l'intérieur du groupe `Route::middleware(['auth'])->group(...)`.

**Exemple de migration:**

**AVANT:**
```php
Route::get('/dashboard', [DashboardController::class, 'index']);
Route::resource('dossiers', DossierController::class);
Route::get('/clients', [ClientController::class, 'index']);
```

**APRÈS:**
```php
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::resource('dossiers', DossierController::class);
    Route::get('/clients', [ClientController::class, 'index']);
});
```

### 5️⃣ Créer le middleware Authenticate (si pas déjà existant)

**Créer:** `/var/www/gestion-dossier/app/Http/Middleware/Authenticate.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Non authentifié.'], 401);
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
```

### 6️⃣ Mettre à jour les liens de déconnexion

Dans vos layouts/templates, remplacer:

**AVANT:**
```blade
<a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
    Déconnexion
</a>
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>
```

**APRÈS:**
```blade
<form action="{{ route('logout') }}" method="POST" style="display:inline;">
    @csrf
    <button type="submit" class="btn btn-link">Déconnexion</button>
</form>
```

Ou simplement:
```blade
<a href="{{ route('logout') }}">Déconnexion</a>
```

### 7️⃣ Tester

```bash
# Effacer les caches
cd /var/www/gestion-dossier
php artisan route:clear
php artisan config:clear
php artisan cache:clear

# Voir les routes
php artisan route:list

# Tester l'accès
curl -I http://localhost/gestion-dossier/dashboard
# Devrait retourner 302 (redirection) vers login
```

### 8️⃣ Test manuel

1. Ouvrir le navigateur: `http://localhost/gestion-dossier`
2. Devrait rediriger vers `http://localhost/administration/auth/login?site=gestion`
3. Se connecter avec vos identifiants
4. Devrait revenir sur `http://localhost/gestion-dossier/dashboard`

---

## ✅ Checklist

- [ ] Fichier `config/app_urls.php` créé
- [ ] Variables dans `.env` ajoutées
- [ ] Routes login/register/logout ajoutées
- [ ] Route callback SSO ajoutée
- [ ] Toutes les routes existantes déplacées dans `middleware(['auth'])`
- [ ] Middleware `Authenticate.php` créé (si nécessaire)
- [ ] Liens de déconnexion mis à jour dans les vues
- [ ] Caches effacés
- [ ] Tests effectués

---

## 🐛 Dépannage

### Erreur: "Route [login] not defined"
**Solution:** Vérifier que la route `login` est bien définie dans `routes/web.php`

### Erreur: Boucle de redirection
**Solution:** S'assurer que les routes login/register/callback ne sont PAS dans le groupe `middleware(['auth'])`

### Erreur: Token invalide
**Solution:** Vérifier que l'API `/api/user` fonctionne dans Administration:
```bash
curl http://localhost/administration/api/user -H "Authorization: Bearer TOKEN"
```

### Utilisateur non créé automatiquement
**Solution:** Vérifier que la table `users` a les bonnes colonnes et que `User::create()` fonctionne

---

## 📞 Besoin d'aide?

Consulter la documentation complète: `PROTECTION_AUTH_COMPLETE.md`
