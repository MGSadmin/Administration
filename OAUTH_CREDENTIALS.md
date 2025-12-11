# CREDENTIALS OAuth2 - ARCHITECTURE MULTI-SITES

**Date de génération:** 11 décembre 2025

---

## 🔐 Clients OAuth2 Password Grant

### Client Commercial
```
Client ID:     019b0bad-1854-7299-bc98-53167cbf6749
Client Secret: sKWkeJTHaimY0PPjRxMPyfevNaWFtF3TQdC3AA7U
```

### Client Gestion-Dossier
```
Client ID:     019b0bad-3bc1-71a9-8cde-5f3eec91dc08
Client Secret: gHCN06y45p1lfwJ77wXcl44bovbmfYzzy0M5BPQP
```

---

## ⚙️ Configuration .env à appliquer

### COMMERCIAL (.env)
```bash
# OAuth2 Client Configuration
OAUTH_CLIENT_ID=019b0bad-1854-7299-bc98-53167cbf6749
OAUTH_CLIENT_SECRET=sKWkeJTHaimY0PPjRxMPyfevNaWFtF3TQdC3AA7U
OAUTH_REDIRECT_URI=http://commercial.mgs-local.mg/auth/callback
OAUTH_SERVER_URL=http://administration.mgs-local.mg
```

### GESTION-DOSSIER (.env)
```bash
# OAuth2 Client Configuration
OAUTH_CLIENT_ID=019b0bad-3bc1-71a9-8cde-5f3eec91dc08
OAUTH_CLIENT_SECRET=gHCN06y45p1lfwJ77wXcl44bovbmfYzzy0M5BPQP
OAUTH_REDIRECT_URI=http://gestion-dossier.mgs-local.mg/auth/callback
OAUTH_SERVER_URL=http://administration.mgs-local.mg
```

---

## 📋 Status des Migrations

### ✅ Administration
- 20 migrations réussies
- Tables OAuth2 créées par Passport
- Table `users` OK avec champs OAuth
- Index `pos_assign_search_idx` corrigé

### ✅ Commercial  
- 45 migrations réussies
- Table `users` créée avec champs OAuth
- Migrations CRM complètes
- Migration `sessions` en double supprimée

### ✅ Gestion-Dossier
- 46 migrations réussies
- Table `users` créée avec champs OAuth
- Migrations système debours complètes
- Migration `sessions` en double supprimée

---

## 🔄 Prochaines étapes

### 1. Mise à jour des fichiers .env
```bash
# Commercial
nano /var/www/commercial/.env
# Ajouter les 4 lignes OAUTH_* ci-dessus

# Gestion-Dossier
nano /var/www/gestion-dossier/.env
# Ajouter les 4 lignes OAUTH_* ci-dessus
```

### 2. Ajouter les routes OAuth dans web.php

**Commercial:** `/var/www/commercial/routes/web.php`
```php
use App\Http\Controllers\Auth\OAuthController;

// Routes OAuth2
Route::get('/login/oauth', [OAuthController::class, 'redirectToProvider'])->name('oauth.redirect');
Route::get('/auth/callback', [OAuthController::class, 'handleProviderCallback'])->name('oauth.callback');
Route::post('/logout', [OAuthController::class, 'logout'])->name('logout');
```

**Gestion-Dossier:** `/var/www/gestion-dossier/routes/web.php`
```php
use App\Http\Controllers\Auth\OAuthController;

// Routes OAuth2
Route::get('/login/oauth', [OAuthController::class, 'redirectToProvider'])->name('oauth.redirect');
Route::get('/auth/callback', [OAuthController::class, 'handleProviderCallback'])->name('oauth.callback');
Route::post('/logout', [OAuthController::class, 'logout'])->name('logout');
```

### 3. Compiler les assets frontend
```bash
cd /var/www/administration && npm run build
cd /var/www/commercial && npm run build
cd /var/www/gestion-dossier && npm run build
```

### 4. Tester le flux OAuth2
```
1. Accéder à: http://commercial.mgs-local.mg/login/oauth
2. Redirection vers: http://administration.mgs-local.mg/oauth/authorize
3. Connexion avec un compte existant
4. Autorisation de l'accès
5. Redirection vers: http://commercial.mgs-local.mg/auth/callback
6. Connexion automatique
```

---

## 🚨 IMPORTANT - Conservation des Credentials

**⚠️ Ces credentials ne seront PLUS affichés après cette génération.**

**Sauvegardez ce fichier dans un endroit sécurisé:**
- Ne PAS le commiter dans Git (déjà dans .gitignore)
- Le stocker dans un gestionnaire de mots de passe (1Password, LastPass, etc.)
- Faire une copie de backup chiffrée

**En cas de perte:**
```bash
cd /var/www/administration
php artisan passport:client --password --name="Nom du Client"
# Génère de nouveaux credentials
```

---

## 📊 Architecture Rappel

```
┌─────────────────────────────────────────────────┐
│          ADMINISTRATION (OAuth2 Server)         │
│  - Laravel Passport                             │
│  - Base de données: mgs_administration          │
│  - Hub central de notifications                 │
│  - API: /api/user, /api/notifications/send      │
└────────────┬─────────────────┬──────────────────┘
             │                 │
             ▼                 ▼
   ┌─────────────────┐  ┌─────────────────┐
   │   COMMERCIAL    │  │ GESTION-DOSSIER │
   │ (OAuth Client)  │  │  (OAuth Client) │
   │ - Socialite     │  │  - Socialite    │
   │ - DB: commercial│  │  - DB: gestion_ │
   │                 │  │       dossiers  │
   └─────────────────┘  └─────────────────┘
```

---

**Généré automatiquement le 11 décembre 2025**
