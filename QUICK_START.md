# 🎯 Quick Start - Authentification Centralisée

## ⚡ Démarrage en 5 minutes

### 1️⃣ Tester l'installation

```bash
cd /var/www/administration
./test_auth.sh
```

### 2️⃣ Créer un utilisateur de test

```bash
php artisan tinker
```

```php
// Dans tinker:
$user = User::create([
    'name' => 'Test User',
    'email' => 'test@mgs.mg',
    'password' => bcrypt('password123'),
    'is_active' => true
]);

$user->assignRole('super-admin');
exit
```

### 3️⃣ Tester la connexion

Ouvrir dans le navigateur :
```
http://localhost/administration/auth/login
```

**Identifiants :**
- Email: `test@mgs.mg`
- Password: `password123`

---

## 🔧 Configuration rapide

### Variables d'environnement (.env)

```env
# URLs des applications
ADMIN_APP_URL=http://localhost/administration
COMMERCIAL_APP_URL=http://localhost/commercial
GESTION_DOSSIER_APP_URL=http://localhost/gestion-dossier

# SSO
SSO_ENABLED=true
SSO_TOKEN_LIFETIME=7
```

---

## 📍 URLs importantes

| Page | URL | Description |
|------|-----|-------------|
| Connexion Admin | `/auth/login?site=admin` | Se connecter à Administration |
| Connexion Commercial | `/auth/login?site=commercial` | Se connecter à Commercial |
| Connexion Gestion | `/auth/login?site=gestion` | Se connecter à Gestion Dossier |
| Inscription | `/auth/register` | Créer un compte |
| Déconnexion | `/auth/logout` | Se déconnecter |

---

## 🎨 Fichiers créés

### Frontend
- ✅ `/resources/views/auth/login.blade.php` - Page de connexion
- ✅ `/resources/views/auth/register.blade.php` - Page d'inscription

### Backend
- ✅ `/app/Http/Controllers/Auth/AuthController.php` - Logique d'auth
- ✅ `/routes/web.php` - Routes ajoutées
- ✅ `/routes/api.php` - API validation token
- ✅ `/config/app_urls.php` - Configuration URLs

### Documentation
- ✅ `INDEX_AUTH.md` - Index des docs
- ✅ `VISUAL_SUMMARY_AUTH.md` - Résumé visuel
- ✅ `README_AUTH.md` - Doc technique
- ✅ `GUIDE_AUTHENTIFICATION.md` - Guide d'utilisation
- ✅ `MIGRATION_AUTH_CENTRALISEE.md` - Guide migration
- ✅ `QUICK_START.md` - Ce fichier

### Scripts
- ✅ `test_auth.sh` - Script de test

---

## 🔄 Workflow simplifié

```
Utilisateur → /auth/login → Choisit app → Email/Pass 
    → AuthController vérifie → Crée token (si besoin)
    → Redirige vers app choisie
```

---

## 🧪 Tests rapides

### Test 1: Login Administration
```bash
curl -X POST http://localhost/administration/auth/login \
  -d "email=test@mgs.mg" \
  -d "password=password123" \
  -d "site=admin" \
  -c cookies.txt
```

### Test 2: Vérifier les routes
```bash
cd /var/www/administration
php artisan route:list --name=auth
```

### Test 3: Vérifier l'API
```bash
# Récupérer un token d'abord via login
curl http://localhost/administration/api/user \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## 🚨 Dépannage rapide

### Problème: Erreur 404 sur /auth/login

**Solution:**
```bash
php artisan route:clear
php artisan cache:clear
php artisan config:clear
```

### Problème: "Vous n'avez pas accès"

**Solution:** Vérifier les permissions
```bash
php artisan permission:show test@mgs.mg
```

### Problème: Page blanche

**Solution:** Vérifier les logs
```bash
tail -f storage/logs/laravel.log
```

---

## 📚 Documentation complète

Pour plus de détails, consulter:

1. **INDEX_AUTH.md** - Point d'entrée de la documentation
2. **VISUAL_SUMMARY_AUTH.md** - Vue d'ensemble avec diagrammes
3. **README_AUTH.md** - Documentation technique complète
4. **GUIDE_AUTHENTIFICATION.md** - Guide d'utilisation
5. **MIGRATION_AUTH_CENTRALISEE.md** - Migrer Commercial & Gestion

---

## ✅ Checklist de vérification

- [ ] Test du script: `./test_auth.sh` passe tous les tests
- [ ] Utilisateur créé et peut se connecter
- [ ] Page login accessible et fonctionne
- [ ] Page register accessible et fonctionne
- [ ] Redirection fonctionne correctement
- [ ] Logs créés dans `storage/logs/laravel.log`

---

## 🎓 Commandes utiles

```bash
# Voir toutes les routes auth
php artisan route:list --name=auth

# Créer un utilisateur
php artisan tinker
>>> User::factory()->create(['email' => 'user@mgs.mg']);

# Assigner un rôle
>>> User::where('email', 'user@mgs.mg')->first()->assignRole('admin-viewer');

# Voir les permissions d'un user
php artisan permission:show user@mgs.mg

# Effacer les caches
php artisan optimize:clear

# Voir les logs en temps réel
tail -f storage/logs/laravel.log

# Tester le script
./test_auth.sh
```

---

## 🌟 Prochaines étapes

### Pour Commercial
1. Ouvrir `/var/www/commercial/routes/web.php`
2. Ajouter redirection vers auth centralisée
3. Voir `MIGRATION_AUTH_CENTRALISEE.md`

### Pour Gestion Dossier
1. Ouvrir `/var/www/gestion-dossier/routes/web.php`
2. Ajouter redirection vers auth centralisée
3. Voir `MIGRATION_AUTH_CENTRALISEE.md`

---

## 📞 Aide

- **Documentation**: Lire `INDEX_AUTH.md`
- **Problèmes**: Consulter `GUIDE_AUTHENTIFICATION.md` section Dépannage
- **Logs**: `tail -f storage/logs/laravel.log`
- **Tests**: `./test_auth.sh`

---

**✨ Vous êtes prêt à utiliser l'authentification centralisée !**

Pour une vue d'ensemble complète, consulter `INDEX_AUTH.md`
