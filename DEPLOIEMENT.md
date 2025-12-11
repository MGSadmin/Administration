# 🚀 Guide de Déploiement MGS - Applications

## 📦 Résumé de la configuration

Vous avez 3 applications Laravel qui partagent l'authentification :
- **administration.mgs-local.mg** → Base centrale des utilisateurs
- **debours.mgs-local.mg** → Gestion des dossiers (déjà en ligne)
- **commercial.mgs-local.mg** → Application commerciale

---

## ÉTAPE 1 : Migrer les utilisateurs vers mgs_administration

Les utilisateurs existent actuellement dans `gestion_dossiers`, ils doivent être dans `mgs_administration`.

```bash
cd /var/www/administration
chmod +x migrate_users.sh
./migrate_users.sh
```

Ceci va copier :
- ✅ Les 5+ utilisateurs existants
- ✅ Les rôles et permissions
- ✅ Toutes les associations

---

## ÉTAPE 2 : Configurer Git pour Administration

```bash
cd /var/www/administration
chmod +x setup_git_administration.sh
./setup_git_administration.sh
```

Ensuite sur GitHub/GitLab, créez un nouveau dépôt `administration` et :

```bash
cd /var/www/administration
git remote add origin https://github.com/MGSadmin/administration.git
git push -u origin main
```

---

## ÉTAPE 3 : Configurer Git pour Commercial

```bash
cd /var/www/commercial
chmod +x setup_git_commercial.sh
./setup_git_commercial.sh
```

Puis sur GitHub/GitLab, créez un nouveau dépôt `commercial` et :

```bash
cd /var/www/commercial
git remote add origin https://github.com/MGSadmin/commercial.git
git push -u origin main
```

---

## ÉTAPE 4 : Sur cPanel - Créer les bases de données

Via **cPanel → MySQL Database Wizard** :

1. Créer la base : `mgs_administration`
2. Créer la base : `commercial`
3. Base `gestion_dossiers` existe déjà
4. Créer un utilisateur : `mgs_dbuser` avec mot de passe fort
5. Donner TOUS les privilèges à cet utilisateur sur les 3 bases

---

## ÉTAPE 5 : Déployer Administration sur cPanel

### 5.1 Via Git Version Control

1. Aller dans **cPanel → Git Version Control**
2. Cliquer **Create**
3. Remplir :
   - **Clone URL** : `https://github.com/MGSadmin/administration.git`
   - **Repository Path** : `/administration`
   - **Repository Name** : `administration`
4. Cliquer **Create**

### 5.2 Configuration via Terminal SSH

Connectez-vous en SSH sur cPanel et :

```bash
cd ~/administration

# 1. Créer le fichier .env
cp .env.production.example .env

# 2. Éditer le .env (remplacez les valeurs)
nano .env
```

**Valeurs importantes dans .env** :

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://administration.mgs-local.mg

DB_HOST=localhost
DB_DATABASE=VOTRE_CPANEL_USER_mgs_administration
DB_USERNAME=VOTRE_CPANEL_USER_mgs_dbuser
DB_PASSWORD=VOTRE_MOT_DE_PASSE

SESSION_DOMAIN=.mgs-local.mg
```

```bash
# 3. Installer et configurer
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan storage:link
php artisan migrate --force

# 4. Optimiser pour production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Permissions
chmod -R 775 storage bootstrap/cache
```

### 5.3 Configurer le sous-domaine

1. **cPanel → Domains**
2. Créer/Modifier `administration.mgs-local.mg`
3. **Document Root** : `/home/VOTRE_USER/administration/public`

---

## ÉTAPE 6 : Déployer Commercial sur cPanel

Même procédure que Administration :

```bash
# Via Git Version Control
Clone URL: https://github.com/MGSadmin/commercial.git
Repository Path: /commercial

# Via SSH
cd ~/commercial
cp .env.production.example .env
nano .env  # Éditer les valeurs
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan storage:link
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
chmod -R 775 storage bootstrap/cache
```

**.env pour Commercial** (IMPORTANT) :

```env
APP_ENV=production
APP_URL=https://commercial.mgs-local.mg

# Base de données commercial
DB_DATABASE=VOTRE_CPANEL_USER_commercial
DB_USERNAME=VOTRE_CPANEL_USER_mgs_dbuser
DB_PASSWORD=VOTRE_MOT_DE_PASSE

# Base administration pour SSO
DB_ADMIN_HOST=localhost
DB_ADMIN_DATABASE=VOTRE_CPANEL_USER_mgs_administration
DB_ADMIN_USERNAME=VOTRE_CPANEL_USER_mgs_dbuser
DB_ADMIN_PASSWORD=VOTRE_MOT_DE_PASSE

SESSION_DOMAIN=.mgs-local.mg
SESSION_CONNECTION=administration
```

Document Root : `/home/VOTRE_USER/commercial/public`

---

## ÉTAPE 7 : Mettre à jour Gestion-Dossier

Le repo existe déjà, il faut juste mettre à jour :

```bash
# Pousser les dernières modifications locales
cd /var/www/gestion-dossier
git add .
git commit -m "Mise à jour avec nouvelles fonctionnalités"
git push origin main

# Sur cPanel (via Git Version Control)
# Cliquer sur "Pull or Deploy" → "Update from Remote"

# Via SSH
cd ~/gestion-dossier  # ou le nom du dossier
composer install --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Mettre à jour .env de gestion-dossier** pour ajouter :

```env
# Connexion à l'administration pour SSO
DB_ADMIN_HOST=localhost
DB_ADMIN_DATABASE=VOTRE_CPANEL_USER_mgs_administration
DB_ADMIN_USERNAME=VOTRE_CPANEL_USER_mgs_dbuser
DB_ADMIN_PASSWORD=VOTRE_MOT_DE_PASSE

SESSION_DOMAIN=.mgs-local.mg
SESSION_CONNECTION=administration
```

---

## ÉTAPE 8 : Activer SSL (HTTPS)

1. **cPanel → SSL/TLS Status**
2. Activer **AutoSSL** pour :
   - administration.mgs-local.mg
   - commercial.mgs-local.mg
   - debours.mgs-local.mg

---

## ✅ TESTS FINAUX

### Test 1 : SSO (Single Sign-On)

1. Ouvrir `https://administration.mgs-local.mg`
2. Se connecter avec un compte utilisateur
3. Ouvrir `https://debours.mgs-local.mg` → Doit être connecté automatiquement
4. Ouvrir `https://commercial.mgs-local.mg` → Doit être connecté automatiquement

### Test 2 : Permissions fichiers

```bash
# Sur chaque application
ls -la ~/administration/storage
ls -la ~/commercial/storage
ls -la ~/gestion-dossier/storage

# Les dossiers doivent être en 775
```

### Test 3 : Base de données

```bash
# Vérifier que les 3 apps voient les mêmes utilisateurs
mysql -u VOTRE_USER -p VOTRE_DB_mgs_administration -e "SELECT id, name, email FROM users;"
```

---

## 🔄 MISES À JOUR FUTURES

Quand vous modifiez le code en local :

```bash
# Sur votre machine locale
cd /var/www/administration  # ou commercial/gestion-dossier
git add .
git commit -m "Description des modifications"
git push origin main

# Sur cPanel
# Via Git Version Control → Pull or Deploy → Update from Remote
# OU via SSH :
cd ~/administration
git pull origin main
composer install --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🆘 DÉPANNAGE

### Erreur "Session not found"
- Vérifier que `SESSION_DOMAIN=.mgs-local.mg` est identique partout
- Vérifier que `SESSION_CONNECTION=administration` pointe vers mgs_administration

### Erreur "Permission denied" sur storage
```bash
chmod -R 775 storage bootstrap/cache
```

### Page blanche
```bash
# Activer temporairement le debug
nano .env
# Mettre APP_DEBUG=true
# Voir les erreurs dans storage/logs/laravel.log
```

### Les utilisateurs ne se synchronisent pas
- Vérifier que DB_ADMIN_* pointe vers la même base dans les 3 .env
- Vérifier les permissions de la base de données

---

## 📞 CHECKLIST FINALE

- [ ] Migration des utilisateurs effectuée
- [ ] Git configuré pour administration
- [ ] Git configuré pour commercial  
- [ ] Dépôts GitHub/GitLab créés
- [ ] Code poussé sur GitHub
- [ ] Bases de données créées sur cPanel
- [ ] Administration déployée
- [ ] Commercial déployé
- [ ] Gestion-dossier mis à jour
- [ ] Fichiers .env configurés (les 3 apps)
- [ ] Migrations exécutées (les 3 apps)
- [ ] SSL activé (les 3 domaines)
- [ ] SSO testé et fonctionnel
- [ ] Permissions fichiers OK

---

**Bon déploiement ! 🚀**
