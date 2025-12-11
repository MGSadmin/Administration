# 🚀 Plan d'Amélioration: Rendez l'Architecture Production-Ready

**Priorité:** Haute  
**Effort:** 2-3 heures  
**Impact:** Critique pour la sécurité en production

---

## 1️⃣ URGENCE: Token Expiration

### Problème
```
'expiration' => null,  // ❌ Les tokens ne s'expirent jamais!
```

### Solution

**Étape 1:** Ajouter en `.env`
```env
SANCTUM_EXPIRATION=1440  # 24 heures
```

**Étape 2:** Mettre à jour `config/sanctum.php`
```php
'expiration' => env('SANCTUM_EXPIRATION', 1440),
```

**Étape 3:** Tester
```bash
cd /var/www/administration

# Vérifier la config
php artisan tinker
>>> config('sanctum.expiration')
# Devrait retourner: 1440
```

---

## 2️⃣ URGENT: Rate Limiting sur /api/login

### Problème
```
❌ Aucune protection contre les attaques par force brute
```

### Solution

**Fichier:** `/var/www/administration/routes/api.php`

Remplacer:
```php
Route::post('/login', function (Request $request) {
    // ...
});
```

Par:
```php
Route::post('/login', function (Request $request) {
    $data = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Rate limiting: max 5 tentatives par minute par IP
    $throttleKey = 'login:' . $request->ip();
    if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
        throw ValidationException::withMessages([
            'email' => 'Trop de tentatives. Réessayez dans ' . RateLimiter::availableIn($throttleKey) . ' secondes.',
        ]);
    }

    $user = User::where('email', $data['email'])->first();

    if (! $user || ! Hash::check($data['password'], $user->password)) {
        RateLimiter::hit($throttleKey);
        Log::warning('Failed login attempt', [
            'email' => $data['email'],
            'ip' => $request->ip(),
            'timestamp' => now(),
        ]);
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    RateLimiter::clear($throttleKey);
    
    $token = $user->createToken('api-token')->plainTextToken;

    Log::info('Successful API login', [
        'user_id' => $user->id,
        'email' => $user->email,
        'ip' => $request->ip(),
    ]);

    return response()->json([
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'prenom' => $user->prenom,
            'email' => $user->email,
            'roles' => $user->getRoleNames()->toArray(),
            'permissions' => $user->getPermissionNames()->toArray(),
        ],
    ]);
})->middleware('throttle:5,1');
```

---

## 3️⃣ IMPORTANT: CORS Configuration

### Fichier: `/var/www/administration/config/cors.php`

Vérifier/Ajouter:
```php
<?php

return [
    'paths' => ['api/*'],
    
    'allowed_methods' => ['*'],
    
    'allowed_origins' => [
        'http://localhost:3000',
        'http://localhost:8001',
        'http://localhost:8002',
        'http://commercial.mgs-local.mg',
        'http://debours.mgs-local.mg',
        'http://debours.mgs-local.mg',
        'https://commercial.mgs.mg',      // Production
        'https://debours.mgs.mg',  // Production
        'https://debours.mgs.mg',          // Production
    ],

    'allowed_origins_patterns' => [
        '#.*\.mgs\.mg$#',  // Tout sous-domaine mgs.mg
    ],

    'allowed_headers' => ['*'],
    
    'exposed_headers' => [],
    
    'max_age' => 0,
    
    'supports_credentials' => true,
];
```

**Appliquer le middleware:**
```bash
cd /var/www/administration
php artisan config:publish cors
```

---

## 4️⃣ IMPORTANT: Logging des Authentifications

### Fichier: `/var/www/administration/app/Http/Controllers/Api/AuthController.php`

Créer le contrôleur:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController
{
    /**
     * Login endpoint
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Rate limiting
        $throttleKey = 'api_login:' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            Log::warning('Rate limit exceeded for API login', [
                'ip' => $request->ip(),
                'email' => $data['email'],
            ]);
            return response()->json([
                'message' => 'Trop de tentatives. Réessayez plus tard.'
            ], 429);
        }

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            RateLimiter::hit($throttleKey);
            Log::warning('Failed API login attempt', [
                'email' => $data['email'],
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);
            return response()->json(['message' => 'Credentials invalides'], 401);
        }

        // Vérifier que l'utilisateur est actif
        if (!$user->is_active) {
            Log::warning('Login attempt by inactive user', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            return response()->json(['message' => 'Utilisateur inactif'], 401);
        }

        RateLimiter::clear($throttleKey);
        
        $token = $user->createToken('api-token')->plainTextToken;
        
        // Mettre à jour last_login_at
        $user->update(['last_login_at' => now()]);

        Log::info('Successful API login', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
            'timestamp' => now(),
        ]);

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'roles' => $user->getRoleNames()->toArray(),
                'permissions' => $user->getPermissionNames()->toArray(),
            ],
        ]);
    }

    /**
     * Get current authenticated user
     */
    public function me(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'roles' => $user->getRoleNames()->toArray(),
                'permissions' => $user->getPermissionNames()->toArray(),
            ],
        ]);
    }

    /**
     * Logout (revoke token)
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        
        Log::info('API logout', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
        
        $request->user()->currentAccessToken()->delete();
        
        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        
        Log::info('Token refresh', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
        
        // Revoque l'ancien token
        $request->user()->currentAccessToken()->delete();
        
        // Crée un nouveau token
        $newToken = $user->createToken('api-token')->plainTextToken;
        
        return response()->json([
            'token' => $newToken,
            'message' => 'Token refreshed',
        ]);
    }
}
```

---

## 5️⃣ MOYEN TERME: Client-Side Token Refresh

### Fichier: `/var/www/commercial/app/Services/AdminAuthService.php`

Ajouter:
```php
public function refresh(?string $token): array
{
    if (empty($token)) {
        return ['ok' => false, 'data' => null];
    }
    try {
        $resp = Http::withToken($token)->post($this->adminBaseUrl . '/api/refresh');
        if ($resp->successful()) {
            return ['ok' => true, 'data' => $resp->json()];
        }
        return ['ok' => false, 'data' => $resp->json()];
    } catch (\Exception $e) {
        Log::error('AdminAuthService refresh error: ' . $e->getMessage());
        return ['ok' => false, 'data' => null];
    }
}
```

### Middleware: `/var/www/commercial/app/Http/Middleware/RefreshAdminToken.php`

Créer:
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AdminAuthService;
use Symfony\Component\HttpFoundation\Response;

class RefreshAdminToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->session()->get('admin_token');
        $lastRefresh = $request->session()->get('last_token_refresh', 0);
        
        // Rafraîchir le token toutes les 12 heures
        if ($token && (time() - $lastRefresh) > (12 * 3600)) {
            $service = app(AdminAuthService::class);
            $result = $service->refresh($token);
            
            if ($result['ok']) {
                $request->session()->put('admin_token', $result['data']['token']);
                $request->session()->put('last_token_refresh', time());
            }
        }
        
        return $next($request);
    }
}
```

---

## 6️⃣ MIGRATION DE ROUTES

### Ancien style (inline) → Nouveau style (contrôleur)

**Ancien (`routes/api.php`):**
```php
Route::post('/login', function (Request $request) {
    // ...
});
```

**Nouveau:**
```php
<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
```

---

## ✅ CHECKLIST D'IMPLÉMENTATION

### Niveau 1 (Critique - 2-3h)
- [ ] Token expiration (SANCTUM_EXPIRATION)
- [ ] Rate limiting sur /api/login
- [ ] Logging des authentifications
- [ ] Vérifier que is_active est respecté

### Niveau 2 (Important - 4-6h)
- [ ] CORS configuration
- [ ] Créer AuthController
- [ ] Migrer routes vers contrôleur
- [ ] Tests end-to-end

### Niveau 3 (Nice-to-have - 2-3h)
- [ ] Refresh token flow
- [ ] Middleware pour auto-refresh
- [ ] 2FA (optionnel)
- [ ] OAuth2 (si nécessaire)

---

## 🧪 TESTS

### Test Token Expiration
```bash
cd /var/www/administration

# Créer un token
php artisan tinker
>>> $user = User::first()
>>> $token = $user->createToken('test')->plainTextToken

# Attendre que le token expire (config: 1440 minutes = 24h)
# Ou modifier SANCTUM_EXPIRATION=1 pour tester rapidement

# Appeler /api/me avec le token expiré
>>> Http::withToken($token)->get('http://localhost:8000/api/me')->json()
# Devrait retourner 401 Unauthorized
```

### Test Rate Limiting
```bash
# Faire 6 requêtes de login rapidement
for i in {1..6}; do
  curl -X POST http://localhost:8000/api/login \
    -H "Content-Type: application/json" \
    -d '{"email":"test@test.com","password":"wrong"}'
done

# La 6ème devrait retourner 429 Too Many Requests
```

### Test CORS
```bash
# Depuis le navigateur de commercial
fetch('http://localhost:8000/api/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Origin': 'http://localhost:8001'
  },
  body: JSON.stringify({
    email: 'test@test.com',
    password: 'password'
  })
})
.then(r => r.json())
.then(data => console.log(data))
```

---

## 📋 Priorisation

| Tâche | Impact | Effort | Priorité |
|-------|--------|--------|----------|
| Token Expiration | 🔴 Critique | 30min | 🟥 P1 |
| Rate Limiting | 🔴 Critique | 1h | 🟥 P1 |
| Logging | 🟠 Important | 1h | 🟧 P2 |
| CORS | 🟠 Important | 30min | 🟧 P2 |
| AuthController | 🟡 Nice | 2h | 🟨 P3 |
| Refresh Token | 🟡 Nice | 1.5h | 🟨 P3 |

---

**Status:** Ready to implement  
**Effort Total:** ~6 heures pour tout  
**Impact:** Production-ready
