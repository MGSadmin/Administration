# GUIDE DE DÉPLOIEMENT - SERVEUR PRODUCTION (nl1-tr102)

**Date:** 11 décembre 2025  
**Serveur:** mgsmg@nl1-tr102  
**Type:** Hébergement mutualisé (sans sudo)  
**WebSocket:** Soketi (auto-hébergé)

---

## 📋 PRÉREQUIS

✅ Accès SSH au serveur  
✅ Node.js installé (v10+ disponible)  
✅ Credentials OAuth2 depuis local  

---

## 🚀 DÉPLOIEMENT COMPLET

### ÉTAPE 1 : Se connecter au serveur

```bash
ssh mgsmg@nl1-tr102
```

---

### ÉTAPE 2 : Télécharger les scripts d'installation

```bash
# Depuis votre machine locale
scp /var/www/administration/install_soketi_production.sh mgsmg@nl1-tr102:~/
scp /var/www/administration/configure_soketi.sh mgsmg@nl1-tr102:~/
```

**OU** créer les fichiers manuellement sur le serveur.

---

### ÉTAPE 3 : Installer Soketi

```bash
cd ~
chmod +x install_soketi_production.sh
./install_soketi_production.sh
```

**Important:** Le script va installer Soketi v0.38.0 (compatible Node.js v10) dans `~/soketi/`

Après l'installation, ajouter `~/bin` au PATH:

```bash
echo 'export PATH="$HOME/bin:$PATH"' >> ~/.bashrc
source ~/.bashrc
```

---

### ÉTAPE 4 : Démarrer Soketi

```bash
start-soketi.sh
```

Vérifier que Soketi tourne:

```bash
status-soketi.sh
```

Vous devriez voir le serveur WebSocket sur le port 6001.

---

### ÉTAPE 5 : Configurer Soketi dans toutes les applications

```bash
cd ~
chmod +x configure_soketi.sh
./configure_soketi.sh
```

Le script va configurer automatiquement les 3 applications avec:
- **App ID:** mgs-app
- **Key:** mgs-app-key
- **Secret:** mgs-app-secret
- **Host:** 127.0.0.1
- **Port:** 6001

---

### ÉTAPE 6 : Configurer OAuth2 sur Administration

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

### ÉTAPE 7 : Configurer Commercial

```bash
cd ~/commercial.mgs.mg

# Éditer le .env
nano .env
```

**Ajouter ces lignes:**

```bash
# OAuth2 Configuration
OAUTH_CLIENT_ID=<Client ID de Commercial depuis l'étape 6>
OAUTH_CLIENT_SECRET=<Client Secret de Commercial depuis l'étape 6>
OAUTH_REDIRECT_URI=https://commercial.mgs.mg/auth/callback
OAUTH_SERVER_URL=https://administration.mgs.mg

# Queue
QUEUE_CONNECTION=database
```

**Note:** La configuration Soketi a déjà été ajoutée par le script à l'étape 5.

Sauvegarder: `Ctrl+O`, `Enter`, `Ctrl+X`

---

### ÉTAPE 8 : Configurer Debours (Gestion-Dossier)

```bash
cd ~/debours.mgs.mg

# Éditer le .env
nano .env
```

**Ajouter ces lignes:**

```bash
# OAuth2 Configuration
OAUTH_CLIENT_ID=<Client ID de Debours depuis l'étape 6>
OAUTH_CLIENT_SECRET=<Client Secret de Debours depuis l'étape 6>
OAUTH_REDIRECT_URI=https://debours.mgs.mg/auth/callback
OAUTH_SERVER_URL=https://administration.mgs.mg

# Queue
QUEUE_CONNECTION=database
```

**Note:** La configuration Soketi a déjà été ajoutée par le script à l'étape 5.

Sauvegarder: `Ctrl+O`, `Enter`, `Ctrl+X`

---

### ÉTAPE 9 : Optimiser Laravel

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

### ÉTAPE 10 : Compiler les assets (si nécessaire)

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

### ÉTAPE 10 : Compiler les assets (si nécessaire)

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

### ÉTAPE 11 : Démarrer les queue workers

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

### ÉTAPE 12 : Permissions

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

### 1. Vérifier Soketi

```bash
status-soketi.sh
```

Devrait afficher "Soketi est en cours d'exécution".

Tester la connexion WebSocket:

```bash
curl http://127.0.0.1:6001
```

### 2. Vérifier OAuth2

```bash
curl https://administration.mgs.mg/oauth/clients
```

Devrait retourner une liste (peut-être vide).

### 3. Vérifier les logs

### 3. Vérifier les logs

```bash
tail -f ~/administration.mgs.mg/storage/logs/laravel.log
```

### 4. Tester l'authentification

Accéder à: `https://commercial.mgs.mg/login/oauth`

Vous devriez être redirigé vers Administration pour vous connecter.

---

## 🔧 MAINTENANCE

### Redémarrer Soketi

```bash
# Arrêter Soketi
stop-soketi.sh

# Démarrer Soketi
start-soketi.sh

# Vérifier le statut
status-soketi.sh
```

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

# Logs Soketi
tail -f ~/soketi/soketi.log

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

### Soketi (Auto-hébergé)
```
App ID: mgs-app
Key: mgs-app-key
Secret: mgs-app-secret
Host: 127.0.0.1
Port: 6001
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

✅ **WebSocket:** Soketi auto-hébergé (port 6001)  
✅ **Queue:** `database` (pas Redis)  
✅ **Cache:** `file` (pas Redis)  
✅ **Sessions:** `database` ou `file`  

**Configuration .env recommandée:**

```bash
QUEUE_CONNECTION=database
CACHE_STORE=file
SESSION_DRIVER=database
BROADCAST_CONNECTION=pusher

# Soketi Configuration
PUSHER_APP_ID=mgs-app
PUSHER_APP_KEY=mgs-app-key
PUSHER_APP_SECRET=mgs-app-secret
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
PUSHER_APP_CLUSTER=mt1
```

---

## 🆘 SUPPORT

**Problème:** Soketi ne démarre pas  
**Solution:** Vérifier les logs et la compatibilité Node.js

```bash
cat ~/soketi/soketi.log
node -v  # Devrait être v10+
```

Si erreur de version, Soketi 0.38.0 est compatible avec Node.js v10.

---

**Problème:** Queue workers s'arrêtent  
**Solution:** Ajouter dans crontab (si disponible)

```bash
crontab -e
```

Ajouter:

```cron
*/5 * * * * cd ~/administration.mgs.mg && php artisan queue:restart > /dev/null 2>&1
*/10 * * * * ~/bin/start-soketi.sh > /dev/null 2>&1
```

---

**Problème:** Erreurs de permissions  
**Solution:** 

```bash
find ~/administration.mgs.mg/storage -type d -exec chmod 775 {} \;
find ~/administration.mgs.mg/storage -type f -exec chmod 664 {} \;
```

---

**Problème:** WebSocket ne se connecte pas  
**Solution:** Vérifier que Soketi est accessible

```bash
status-soketi.sh
netstat -tuln | grep 6001
```

---

## ✅ CHECKLIST DE DÉPLOIEMENT

- [ ] Soketi installé (`install_soketi_production.sh`)
- [ ] Soketi démarré (`start-soketi.sh`)
- [ ] Script `configure_soketi.sh` exécuté
- [ ] Configuration Soketi dans les 3 `.env`
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
