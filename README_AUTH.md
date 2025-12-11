# 🔐 Système d'Authentification Centralisée MGS

## 📋 Résumé

Le projet **Administration** gère maintenant l'authentification pour les 3 applications du système MGS :
- **Administration** - RH, congés, organigramme
- **Commercial** - CRM, devis, opportunités  
- **Gestion Dossier** - Gestion des dossiers clients

## ✅ Fonctionnalités implémentées

### 🎨 Pages créées

1. **Page de connexion** : `/resources/views/auth/login.blade.php`
   - Design moderne avec sélection d'application
   - Support multi-applications (3 badges cliquables)
   - Validation côté client et serveur
   - Option "Se souvenir de moi"
   - Affichage des erreurs et succès

2. **Page d'inscription** : `/resources/views/auth/register.blade.php`
   - Formulaire complet (nom, prénom, email, etc.)
   - Indicateur de force du mot de passe
   - Validation en temps réel
   - Sélection de l'application cible
   - Acceptation des conditions d'utilisation

### 🎯 Contrôleur d'authentification

**Fichier** : `/app/Http/Controllers/Auth/AuthController.php`

**Méthodes principales** :
- `showLoginForm()` - Affiche la page de connexion
- `showRegisterForm()` - Affiche la page d'inscription
- `login()` - Traite la connexion et redirige
- `register()` - Crée un compte utilisateur
- `logout()` - Déconnexion centralisée
- `userHasAccessToSite()` - Vérifie les permissions
- `redirectToSite()` - Redirige vers l'application appropriée

### 🛣️ Routes configurées

**Fichier** : `/routes/web.php`

```php
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AuthController::class, 'logout']);
});
```

**Fichier** : `/routes/api.php`

```php
// Validation des tokens SSO
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user());
});
```

### ⚙️ Configuration

**Fichier** : `/config/app_urls.php`

- URLs des 3 applications
- Configuration SSO
- Métadonnées des sites (nom, icône, couleur, etc.)

## 🚀 Utilisation

### Connexion

1. Accéder à : `http://localhost/administration/auth/login`
2. Sélectionner l'application (Administration, Commercial ou Gestion Dossier)
3. Saisir email et mot de passe
4. Cliquer sur "Se connecter"
5. ➡️ Redirection automatique vers l'application choisie

### Inscription

1. Accéder à : `http://localhost/administration/auth/register`
2. Sélectionner l'application cible
3. Remplir le formulaire
4. Accepter les conditions
5. Cliquer sur "Créer mon compte"
6. ➡️ Connexion automatique + redirection

### Déconnexion

- URL : `http://localhost/administration/auth/logout`
- Déconnecte de toutes les applications
- Redirige vers la page de connexion

## 🔗 URLs importantes

| Page | URL |
|------|-----|
| Connexion | `/auth/login` |
| Connexion Admin | `/auth/login?site=admin` |
| Connexion Commercial | `/auth/login?site=commercial` |
| Connexion Gestion | `/auth/login?site=gestion` |
| Inscription | `/auth/register` |
| Déconnexion | `/auth/logout` |
| API utilisateur | `/api/user` (avec token) |

## 📂 Structure des fichiers

```
administration/
├── app/
│   └── Http/
│       └── Controllers/
│           └── Auth/
│               └── AuthController.php          ✅ Créé
├── resources/
│   └── views/
│       └── auth/
│           ├── login.blade.php                 ✅ Créé
│           └── register.blade.php              ✅ Créé
├── routes/
│   ├── web.php                                 ✅ Mis à jour
│   └── api.php                                 ✅ Mis à jour
├── config/
│   └── app_urls.php                            ✅ Mis à jour
├── GUIDE_AUTHENTIFICATION.md                   ✅ Créé
├── MIGRATION_AUTH_CENTRALISEE.md               ✅ Créé
└── README_AUTH.md                              ✅ Ce fichier
```

## 🔄 Flux d'authentification

```
┌──────────────────────────────────────────────────────────────┐
│                    Utilisateur                                │
└───────────────────────┬──────────────────────────────────────┘
                        │
                        ▼
            ┌─────────────────────────┐
            │  /auth/login?site=XXX   │
            └───────────┬─────────────┘
                        │
                        ▼
            ┌─────────────────────────┐
            │  AuthController::login   │
            │  - Valide credentials    │
            │  - Vérifie permissions   │
            └───────────┬─────────────┘
                        │
                   ┌────┴────┐
                   │         │
              ✅ Succès   ❌ Erreur
                   │         │
                   │         └──────────────┐
                   ▼                        ▼
        ┌──────────────────────┐   ┌──────────────┐
        │ Création token SSO    │   │ Message      │
        │ (si autre app)        │   │ erreur       │
        └──────────┬────────────┘   └──────────────┘
                   │
                   ▼
        ┌──────────────────────┐
        │ Redirection vers      │
        │ l'application         │
        │ avec token            │
        └──────────┬────────────┘
                   │
                   ▼
        ┌──────────────────────┐
        │ App valide le token   │
        │ Connecte l'utilisateur│
        └───────────────────────┘
```

## 📖 Documentation complète

- **Guide utilisateur** : `GUIDE_AUTHENTIFICATION.md`
- **Guide migration** : `MIGRATION_AUTH_CENTRALISEE.md`

## 🔐 Sécurité

### Permissions

Le système vérifie automatiquement que l'utilisateur a les permissions pour accéder à l'application demandée :
- Permissions `admin.*` → Administration
- Permissions `commercial.*` → Commercial  
- Permissions `gestion.*` → Gestion Dossier
- Rôle `super-admin` → Accès à tout

### Tokens SSO

- Générés via Laravel Sanctum
- Durée de vie : 7 jours (configurable)
- Un token par application
- Révocables à tout moment

### Logs

Toutes les actions sont journalisées :
- Connexion réussie
- Connexion échouée
- Création de compte
- Déconnexion

## 🎨 Personnalisation

### Couleurs

Les couleurs peuvent être modifiées dans les vues :

```css
:root {
    --primary-color: #667eea;    /* Violet */
    --secondary-color: #764ba2;   /* Violet foncé */
    --success-color: #10b981;     /* Vert */
    --danger-color: #ef4444;      /* Rouge */
}
```

### Logos

Pour changer les icônes des applications, modifier dans `config/app_urls.php` :

```php
'sites' => [
    'admin' => [
        'icon' => 'fas fa-users-cog',  // FontAwesome icon
        // ...
    ],
]
```

## 🧪 Tests

### Test manuel

1. **Connexion Administration**
   ```
   URL: /auth/login?site=admin
   Email: admin@mgs.mg
   Password: votre_mot_de_passe
   ✅ Devrait rediriger vers /dashboard
   ```

2. **Connexion Commercial**
   ```
   URL: /auth/login?site=commercial
   Email: commercial@mgs.mg
   Password: votre_mot_de_passe
   ✅ Devrait rediriger vers Commercial avec token
   ```

3. **Inscription**
   ```
   URL: /auth/register?site=gestion
   ✅ Créer un compte et être connecté automatiquement
   ```

### Commandes utiles

```bash
# Voir les permissions d'un utilisateur
php artisan permission:show email@example.com

# Créer un utilisateur de test
php artisan tinker
>>> $user = User::factory()->create(['email' => 'test@mgs.mg']);
>>> $user->assignRole('admin-viewer');

# Voir les tokens actifs
php artisan sanctum:prune-expired

# Logs en temps réel
tail -f storage/logs/laravel.log
```

## 🔧 Maintenance

### Nettoyer les tokens expirés

```bash
php artisan sanctum:prune-expired
```

### Révoquer tous les tokens d'un utilisateur

```php
$user = User::find($id);
$user->tokens()->delete();
```

### Voir les sessions actives

```bash
php artisan session:table
php artisan migrate
```

## 📞 Support

- **Documentation** : Voir `GUIDE_AUTHENTIFICATION.md`
- **Migration** : Voir `MIGRATION_AUTH_CENTRALISEE.md`
- **Logs** : `storage/logs/laravel.log`
- **Contact** : admin@mgs.mg

## ✨ Améliorations futures

- [ ] Authentification à deux facteurs (2FA)
- [ ] Connexion via Google/Microsoft
- [ ] Historique des connexions
- [ ] Gestion des sessions actives
- [ ] Notifications de connexion suspecte
- [ ] Politique de mot de passe personnalisable
- [ ] Réinitialisation de mot de passe
- [ ] Vérification d'email

---

**Version** : 1.0.0  
**Date** : 8 décembre 2025  
**Auteur** : MGS Development Team
