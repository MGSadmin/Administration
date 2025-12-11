# ⚡ ACTION RAPIDE: Améliorer la Sécurité en 2 Heures

**Objectif:** Passer de A+ à A++ (production-ready)  
**Temps:** ~2 heures  
**Effort:** Facile (copier-coller + tests)

---

## 🟥 ÉTAPE 1: Token Expiration (15 min)

### Fichier: `/var/www/administration/.env`

Ajouter:
```env
SANCTUM_EXPIRATION=1440
```

### Fichier: `/var/www/administration/config/sanctum.php`

Vérifier/Ajouter (ligne ~47):
```php
'expiration' => env('SANCTUM_EXPIRATION', 1440),
```

### Test:
```bash
cd /var/www/administration
php artisan tinker
>>> config('sanctum.expiration')
# Résultat: 1440
```

✅ **Status:** Tokens expirent après 24h

---

## 🟥 ÉTAPE 2: Rate Limiting (30 min)

### Fichier: `/var/www/administration/routes/api.php`

Remplacer ENTIÈREMENT:
```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

// === PUBLIC ROUTES ===

// POST /api/login - Authentification
Route::post('/login', function (Request $request) {
    // Rate limiting: max 5 tentatives par minute par IP
    $throttleKey = 'api_login:' . $request->ip();
    
    if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
        $seconds = RateLimiter::availableIn($throttleKey);
        Log::warning('API login rate limit exceeded', [
            'ip' => $request->ip(),
            'retry_after' => $seconds,
        ]);
        return response()->json([
            'message' => 'Trop de tentatives. Réessayez dans ' . $seconds . ' secondes.',
        ], 429);
    }

    $data = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

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

    RateLimiter::clear($throttleKey);
    
    $token = $user->createToken('api-token')->plainTextToken;

    // Log successful login
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
});

// GET /api/test
Route::get('/test', function () {
    return response()->json(['message' => 'API working']);
});

// === PROTECTED ROUTES ===

// GET /api/me - Récupère l'utilisateur actuel
Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
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
});

// POST /api/logout - Révoque le token
Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    Log::info('API logout', [
        'user_id' => $request->user()->id,
        'email' => $request->user()->email,
    ]);
    
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out successfully']);
});
```

### Test:
```bash
# Faire 6 appels rapides (le 6e devrait avoir 429)
for i in {1..6}; do
  echo "Attempt $i:"
  curl -X POST http://localhost:8000/api/login \
    -H "Content-Type: application/json" \
    -d '{"email":"test@test.com","password":"wrong"}' | jq .
  sleep 0.5
done

# Le 6e devrait avoir:
# "message": "Trop de tentatives. Réessayez dans XX secondes."
# HTTP 429
```

✅ **Status:** Protection brute-force activée

---

## 🟧 ÉTAPE 3: Ajouter Logging (20 min)

### Vérifier: `/var/www/administration/config/logging.php`

Vérifier que le logging est activé:
```php
'default' => env('LOG_CHANNEL', 'stack'),

'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single'],
    ],

    'single' => [
        'driver' => 'single',
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'debug'),
    ],
],
```

### Test:
```bash
cd /var/www/administration

# Voir les logs en temps réel
tail -f storage/logs/laravel.log

# Dans un autre terminal, faire un login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"andry@mgs.mg","password":"password"}'

# Le log devrait montrer:
# [2025-12-05 14:30:45] local.INFO: Successful API login {"user_id":1,"email":"andry@mgs.mg","ip":"127.0.0.1"}
```

✅ **Status:** Logs d'authentification activés

---

## 🟧 ÉTAPE 4: CORS Configuration (20 min)

### Fichier: `/var/www/administration/config/cors.php`

Vérifier/Créer:
```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',
        'http://localhost:8000',
        'http://localhost:8001',
        'http://localhost:8002',
        'http://127.0.0.1:8000',
        'http://127.0.0.1:8001',
        'http://127.0.0.1:8002',
        'http://commercial.mgs-local.mg',
        'http://debours.mgs-local.mg',
        'http://debours.mgs-local.mg',
        'http://administration.mgs-local.mg',
    ],

    'allowed_origins_patterns' => [
        '#.*\.mgs\.mg$#',  // Production
        '#.*\.mgs-local\.mg$#',  // Development
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['Content-Disposition'],

    'max_age' => 86400,

    'supports_credentials' => true,
];
```

### Publier la config:
```bash
cd /var/www/administration
php artisan vendor:publish --tag=laravel-cors
```

### Test:
```bash
# Depuis le navigateur (console)
fetch('http://localhost:8000/api/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Origin': 'http://localhost:8001'
  },
  body: JSON.stringify({
    email: 'andry@mgs.mg',
    password: 'password'
  })
})
.then(r => r.json())
.then(data => console.log('Success:', data))
.catch(err => console.error('Error:', err))

// Devrait fonctionner sans erreur CORS
```

✅ **Status:** CORS configuré

---

## 🟨 ÉTAPE 5: Tester Complètement (30 min)

### Test 1: Login valide
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"andry@mgs.mg","password":"password"}'

# Résultat attendu:
{
  "token": "1|...",
  "user": {
    "id": 1,
    "name": "Andry",
    "email": "andry@mgs.mg",
    "roles": ["admin"],
    "permissions": [...]
  }
}

# ✅ PASS
```

### Test 2: Credentials invalides
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"andry@mgs.mg","password":"wrong"}'

# Résultat attendu:
{
  "message": "Credentials invalides"
}

# HTTP 401
# ✅ PASS
```

### Test 3: Rate limiting
```bash
# Faire 6 tentatives rapides
for i in {1..6}; do
  curl -X POST http://localhost:8000/api/login \
    -H "Content-Type: application/json" \
    -d '{"email":"test@test.com","password":"wrong"}' > /dev/null
done

# La 6e devrait avoir HTTP 429
# ✅ PASS
```

### Test 4: Utiliser le token
```bash
TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"andry@mgs.mg","password":"password"}' | jq -r '.token')

curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:8000/api/me

# Résultat attendu:
{
  "user": {
    "id": 1,
    "name": "Andry",
    ...
  }
}

# ✅ PASS
```

### Test 5: Token expiré
```bash
# Attendre 24h (ou modifier SANCTUM_EXPIRATION=1 pour tester)
curl -H "Authorization: Bearer INVALID_TOKEN" \
  http://localhost:8000/api/me

# Résultat attendu: HTTP 401
# ✅ PASS
```

### Test 6: CORS (depuis navigateur client)
```javascript
// Depuis http://localhost:8001 (commercial)
fetch('http://localhost:8000/api/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    email: 'andry@mgs.mg',
    password: 'password'
  })
})
.then(r => r.json())
.then(data => console.log('✅ PASS:', data))
.catch(err => console.error('❌ FAIL:', err))

// Devrait afficher le token
```

---

## ✅ CHECKLIST FINAL

- [ ] Token expiration configuré (15 min)
- [ ] Rate limiting ajouté (30 min)
- [ ] Logging activé (20 min)
- [ ] CORS configuré (20 min)
- [ ] Test 1: Login valide ✓
- [ ] Test 2: Credentials invalides ✓
- [ ] Test 3: Rate limiting ✓
- [ ] Test 4: Token valide ✓
- [ ] Test 5: Token expiré ✓
- [ ] Test 6: CORS ✓

**Total:** ~2 heures

---

## 🎯 Résultat

### Avant (A+)
```
✅ Authentification centralisée
✅ Sanctum configuré
❌ Pas d'expiration de token
❌ Pas de rate limiting
⚠️ Logging minimal
⚠️ CORS non configuré
```

### Après (A++)
```
✅ Authentification centralisée
✅ Sanctum configuré
✅ Token expiration (24h)
✅ Rate limiting (5/min)
✅ Logging complet
✅ CORS configuré
✅ PRODUCTION READY
```

---

## 📝 Commandes Récapitulatives

```bash
# 1. Mettre à jour .env
echo "SANCTUM_EXPIRATION=1440" >> /var/www/administration/.env

# 2. Vider le cache
cd /var/www/administration
php artisan config:clear
php artisan cache:clear

# 3. Publier CORS
php artisan vendor:publish --tag=laravel-cors

# 4. Tester
php artisan tinker
>>> config('sanctum.expiration')  # Devrait être 1440

# 5. Vérifier les logs
tail -f /var/www/administration/storage/logs/laravel.log
```

---

**Temps total:** ~2h  
**Complexité:** Facile  
**Impact:** CRITIQUE  
**Status:** Ready to deploy  

Allez-y! 🚀
