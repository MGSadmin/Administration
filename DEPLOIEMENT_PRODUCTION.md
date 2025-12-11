# GUIDE DE DÉPLOIEMENT - SERVEUR PRODUCTION (nl1-tr102)

**Date:** 11 décembre 2025  
**Serveur:** mgsmg@nl1-tr102  
**Type:** Hébergement mutualisé (sans sudo)

---

## 📋 PRÉREQUIS

✅ Compte Pusher Cloud (https://pusher.com/signup)  
✅ Accès SSH au serveur  
✅ Credentials OAuth2 depuis local  

---

## 🚀 DÉPLOIEMENT COMPLET

### ÉTAPE 1 : Créer un compte Pusher Cloud

1. Aller sur https://pusher.com/signup
2. Créer une application "MGS Production"
3. **Cluster:** Europe (eu)
4. **Noter les credentials:**
   ```
   App ID: ____________
   Key: _______________
   Secret: ____________
   Cluster: eu
   ```

---

### ÉTAPE 2 : Se connecter au serveur

```bash
ssh mgsmg@nl1-tr102
```

---

### ÉTAPE 3 : Télécharger le script de configuration

```bash
# Depuis votre machine locale
scp /var/www/administration/deploy_pusher_production.sh mgsmg@nl1-tr102:~/
```

**OU** créer le fichier manuellement sur le serveur :

```bash
# Sur le serveur
nano ~/deploy_pusher_production.sh
# Coller le contenu du script
chmod +x ~/deploy_pusher_production.sh
```

---

### ÉTAPE 4 : Configurer Pusher

```bash
cd ~
./deploy_pusher_production.sh
```

Entrer vos credentials Pusher quand demandé.

---

### ÉTAPE 5 : Configurer OAuth2 sur Administration

```bash
cd ~/administration.mgs.mg

# Exécuter les migrations
php artisan migrate --force

# Installer Passport
php artisan passport:install --force

# Créer le client OAuth2 pour Commercial
php artisan passport:client --password --name="Commercial Client"
```

**⚠️ IMPORTANT:** Noter le **Client ID** et **Client Secret** affichés

```bash
# Créer le client OAuth2 pour Debours
php artisan passport:client --password --name="Debours Client"
```

**⚠️ IMPORTANT:** Noter le **Client ID** et **Client Secret** affichés

---

### ÉTAPE 6 : Configurer Commercial

```bash
cd ~/commercial.mgs.mg

# Éditer le .env
nano .env
```

**Ajouter ces lignes:**

```bash
# OAuth2 Configuration
OAUTH_CLIENT_ID=<Client ID de Commercial depuis l'étape 5>
OAUTH_CLIENT_SECRET=<Client Secret de Commercial depuis l'étape 5>
OAUTH_REDIRECT_URI=https://commercial.mgs.mg/auth/callback
OAUTH_SERVER_URL=https://administration.mgs.mg

# Broadcasting
BROADCAST_CONNECTION=pusher

# Queue
QUEUE_CONNECTION=database
```

Sauvegarder: `Ctrl+O`, `Enter`, `Ctrl+X`

---

### ÉTAPE 7 : Configurer Debours (Gestion-Dossier)

```bash
cd ~/debours.mgs.mg

# Éditer le .env
nano .env
```

**Ajouter ces lignes:**

```bash
# OAuth2 Configuration
OAUTH_CLIENT_ID=<Client ID de Debours depuis l'étape 5>
OAUTH_CLIENT_SECRET=<Client Secret de Debours depuis l'étape 5>
OAUTH_REDIRECT_URI=https://debours.mgs.mg/auth/callback
OAUTH_SERVER_URL=https://administration.mgs.mg

# Broadcasting
BROADCAST_CONNECTION=pusher

# Queue
QUEUE_CONNECTION=database
```

Sauvegarder: `Ctrl+O`, `Enter`, `Ctrl+X`

---

### ÉTAPE 8 : Optimiser Laravel

```bash
# Administration
cd ~/administration.mgs.mg
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Commercial
cd ~/commercial.mgs.mg
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Debours
cd ~/debours.mgs.mg
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### ÉTAPE 9 : Compiler les assets (si nécessaire)

```bash
# Administration
cd ~/administration.mgs.mg
npm run build

# Commercial
cd ~/commercial.mgs.mg
npm run build

# Debours
cd ~/debours.mgs.mg
npm run build
```

**Note:** Si `npm run build` échoue avec Node v10, ce n'est pas bloquant pour le moment. Les assets actuels fonctionneront.

---

### ÉTAPE 10 : Démarrer les queue workers

```bash
# Administration
cd ~/administration.mgs.mg
nohup php artisan queue:work database --queue=notifications,emails,default --sleep=3 --tries=3 > storage/logs/queue-worker.log 2>&1 &

# Commercial
cd ~/commercial.mgs.mg
nohup php artisan queue:work database --queue=notifications,emails,default --sleep=3 --tries=3 > storage/logs/queue-worker.log 2>&1 &

# Debours
cd ~/debours.mgs.mg
nohup php artisan queue:work database --queue=notifications,emails,default --sleep=3 --tries=3 > storage/logs/queue-worker.log 2>&1 &
```

**Vérifier que les workers tournent:**

```bash
ps aux | grep "queue:work"
```

Vous devriez voir 3 processus.

---

### ÉTAPE 11 : Permissions

```bash
cd ~
chmod -R 755 administration.mgs.mg commercial.mgs.mg debours.mgs.mg
chmod -R 775 administration.mgs.mg/storage
chmod -R 775 commercial.mgs.mg/storage
chmod -R 775 debours.mgs.mg/storage
chmod -R 775 administration.mgs.mg/bootstrap/cache
chmod -R 775 commercial.mgs.mg/bootstrap/cache
chmod -R 775 debours.mgs.mg/bootstrap/cache
```

---

## ✅ VÉRIFICATIONS

### 1. Vérifier OAuth2

```bash
curl https://administration.mgs.mg/oauth/clients
```

Devrait retourner une liste (peut-être vide).

### 2. Vérifier Pusher dans les logs

```bash
tail -f ~/administration.mgs.mg/storage/logs/laravel.log
```

### 3. Tester l'authentification

Accéder à: `https://commercial.mgs.mg/login/oauth`

Vous devriez être redirigé vers Administration pour vous connecter.

---

## 🔧 MAINTENANCE

### Redémarrer les queue workers

```bash
# Arrêter tous les workers
pkill -f "queue:work"

# Redémarrer
cd ~/administration.mgs.mg
nohup php artisan queue:work database --sleep=3 --tries=3 > storage/logs/queue-worker.log 2>&1 &

cd ~/commercial.mgs.mg
nohup php artisan queue:work database --sleep=3 --tries=3 > storage/logs/queue-worker.log 2>&1 &

cd ~/debours.mgs.mg
nohup php artisan queue:work database --sleep=3 --tries=3 > storage/logs/queue-worker.log 2>&1 &
```

### Voir les logs

```bash
# Logs Laravel
tail -f ~/administration.mgs.mg/storage/logs/laravel.log

# Logs queue worker
tail -f ~/administration.mgs.mg/storage/logs/queue-worker.log
```

### Nettoyer les caches

```bash
cd ~/administration.mgs.mg
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 📝 RÉCAPITULATIF DES CREDENTIALS

### Pusher Cloud
```
App ID: ____________
Key: _______________
Secret: ____________
Cluster: eu
```

### OAuth2 - Commercial Client
```
Client ID: ____________
Client Secret: ____________
```

### OAuth2 - Debours Client
```
Client ID: ____________
Client Secret: ____________
```

**⚠️ Conservez ces informations en lieu sûr !**

---

## ❗ IMPORTANT - Hébergement Mutualisé

Sur un serveur mutualisé sans sudo :

✅ **Utilisez Pusher Cloud** (pas Soketi)  
✅ **Queue:** `database` (pas Redis)  
✅ **Cache:** `file` (pas Redis)  
✅ **Sessions:** `database` ou `file`  

**Configuration .env recommandée:**

```bash
QUEUE_CONNECTION=database
CACHE_STORE=file
SESSION_DRIVER=database
BROADCAST_CONNECTION=pusher
```

---

## 🆘 SUPPORT

**Problème:** Queue workers s'arrêtent  
**Solution:** Ajouter dans crontab (si disponible)

```bash
crontab -e
```

Ajouter:

```cron
*/5 * * * * cd ~/administration.mgs.mg && php artisan queue:restart > /dev/null 2>&1
```

---

**Problème:** Erreurs de permissions  
**Solution:** 

```bash
find ~/administration.mgs.mg/storage -type d -exec chmod 775 {} \;
find ~/administration.mgs.mg/storage -type f -exec chmod 664 {} \;
```

---

## ✅ CHECKLIST DE DÉPLOIEMENT

- [ ] Compte Pusher créé
- [ ] Script `deploy_pusher_production.sh` exécuté
- [ ] Credentials Pusher configurés dans les 3 `.env`
- [ ] `php artisan passport:install` exécuté sur Administration
- [ ] Clients OAuth2 créés (Commercial + Debours)
- [ ] Credentials OAuth2 ajoutés dans `.env` de Commercial et Debours
- [ ] `php artisan config:cache` exécuté sur les 3 apps
- [ ] Queue workers démarrés (3 processus)
- [ ] Permissions configurées (755/775)
- [ ] Test OAuth: https://commercial.mgs.mg/login/oauth
- [ ] Logs vérifiés (aucune erreur)

---

**Déploiement terminé ! 🎉**
