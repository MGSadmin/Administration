# CONFIGURATION PUSHER - LOCAL ET PRODUCTION

**Date:** 11 décembre 2025  
**Service:** Pusher Channels (Sandbox plan gratuit)

---

## 🌐 ÉTAPE 1 : CRÉER VOTRE COMPTE PUSHER

### 1. Aller sur Pusher.com

🔗 https://pusher.com/signup

### 2. Choisir le bon produit

⚠️ **IMPORTANT:** Choisir **"Channels"** (PAS Beams)

- ✅ **Channels** = WebSocket temps réel (c'est ce qu'on veut)
- ❌ **Beams** = Push notifications mobiles (pas pour nous)

### 3. Créer une nouvelle app

Dans le Dashboard Pusher:

1. Cliquez sur **"Create app"** ou **"Get started with Channels"**
2. **Name:** `MGS Production`
3. **Cluster:** `Europe (eu)` ⚠️ IMPORTANT - Choisir EU pour la performance
4. **Frontend tech:** Laravel
5. **Backend tech:** Laravel
6. Cliquez sur **"Create app"**

### 4. Récupérer vos credentials

Dans l'onglet **"App Keys"** de votre application, notez ces informations:

```
app_id = _____________ (exemple: 1234567)
key = _________________ (exemple: abc123def456ghi789)
secret = ______________ (exemple: xyz789abc012def345)
cluster = eu
```

**⚠️ GARDEZ CES INFORMATIONS SECRÈTES !**

---

## 💻 ÉTAPE 2 : CONFIGURATION LOCALE (votre machine)

### Configuration Administration

```bash
cd /var/www/administration
nano .env
```

**Ajouter/Modifier ces lignes:**

```bash
# Broadcasting
BROADCAST_CONNECTION=pusher

# Pusher Configuration
PUSHER_APP_ID=<votre app_id>
PUSHER_APP_KEY=<votre key>
PUSHER_APP_SECRET=<votre secret>
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=eu

# Vite (Frontend)
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST=
VITE_PUSHER_PORT=443
VITE_PUSHER_SCHEME=https
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

**Nettoyer le cache:**

```bash
php artisan config:clear
php artisan config:cache
```

---

### Configuration Commercial

```bash
cd /var/www/commercial
nano .env
```

**Ajouter/Modifier ces lignes:**

```bash
# Broadcasting
BROADCAST_CONNECTION=pusher

# Pusher Configuration
PUSHER_APP_ID=<votre app_id>
PUSHER_APP_KEY=<votre key>
PUSHER_APP_SECRET=<votre secret>
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=eu

# Vite (Frontend)
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST=
VITE_PUSHER_PORT=443
VITE_PUSHER_SCHEME=https
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

**Nettoyer le cache:**

```bash
php artisan config:clear
php artisan config:cache
```

---

### Configuration Gestion-Dossier

```bash
cd /var/www/gestion-dossier
nano .env
```

**Ajouter/Modifier ces lignes:**

```bash
# Broadcasting
BROADCAST_CONNECTION=pusher

# Pusher Configuration
PUSHER_APP_ID=<votre app_id>
PUSHER_APP_KEY=<votre key>
PUSHER_APP_SECRET=<votre secret>
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=eu

# Vite (Frontend)
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST=
VITE_PUSHER_PORT=443
VITE_PUSHER_SCHEME=https
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

**Nettoyer le cache:**

```bash
php artisan config:clear
php artisan config:cache
```

---

## ✅ ÉTAPE 3 : TESTER EN LOCAL

### Test 1: Vérifier la configuration

```bash
cd /var/www/administration
php artisan tinker
```

Dans Tinker:

```php
config('broadcasting.connections.pusher');
// Devrait afficher vos credentials Pusher
```

### Test 2: Envoyer une notification test

```bash
php artisan tinker
```

```php
$user = App\Models\User::first();

if ($user) {
    $notification = new App\Notifications\GenericNotification(
        'Test WebSocket',
        'Si vous voyez ceci, Pusher fonctionne !',
        'success'
    );
    
    $user->notify($notification);
    echo "Notification envoyée !\n";
} else {
    echo "Aucun utilisateur trouvé. Créez-en un d'abord.\n";
}
```

### Test 3: Ouvrir le Dashboard Pusher

Allez sur https://dashboard.pusher.com → Votre app → **Debug Console**

Vous devriez voir les événements en temps réel quand vous envoyez une notification.

---

## 🌍 ÉTAPE 4 : DÉPLOIEMENT EN PRODUCTION

### Transférer le script

```bash
# Depuis votre machine locale
scp /var/www/administration/deploy_pusher_production.sh mgsmg@nl1-tr102:~/
```

### Se connecter au serveur

```bash
ssh mgsmg@nl1-tr102
```

### Exécuter le script

```bash
chmod +x ~/deploy_pusher_production.sh
~/deploy_pusher_production.sh
```

**Le script va demander:**

```
Pusher App ID: <entrer votre app_id>
Pusher Key: <entrer votre key>
Pusher Secret: <entrer votre secret>
Pusher Cluster: eu
```

### Nettoyer les caches sur production

```bash
cd ~/administration.mgs.mg
php artisan config:clear
php artisan config:cache

cd ~/commercial.mgs.mg
php artisan config:clear
php artisan config:cache

cd ~/debours.mgs.mg
php artisan config:clear
php artisan config:cache
```

### Compiler les assets (si modifiés)

```bash
cd ~/administration.mgs.mg
npm run build

cd ~/commercial.mgs.mg
npm run build

cd ~/debours.mgs.mg
npm run build
```

---

## 🧪 VÉRIFICATIONS

### ✅ Vérifier dans Pusher Dashboard

Allez sur https://dashboard.pusher.com → **Debug Console**

Vous devriez voir:
- Les connexions WebSocket
- Les événements broadcast
- Le nombre de connexions actives

### ✅ Vérifier les logs Laravel

```bash
# En local
tail -f /var/www/administration/storage/logs/laravel.log

# En production
tail -f ~/administration.mgs.mg/storage/logs/laravel.log
```

### ✅ Tester une notification en production

```bash
ssh mgsmg@nl1-tr102
cd ~/administration.mgs.mg
php artisan tinker
```

```php
$user = App\Models\User::first();
$user->notify(new App\Notifications\GenericNotification(
    'Test Production',
    'WebSocket fonctionne en production !',
    'success'
));
```

Allez dans le Dashboard Pusher → Debug Console, vous devriez voir l'événement.

---

## 📊 LIMITES DU PLAN SANDBOX (GRATUIT)

✅ **Inclus:**
- 200 000 messages par jour
- 100 connexions simultanées max
- Support communautaire
- Debug console

⚠️ **Si vous dépassez:**
- Passer au plan **Channels - Startup** ($49/mois)
- Ou optimiser l'utilisation (moins de broadcasts)

---

## 🔧 RÉSOLUTION DE PROBLÈMES

### Problème: "Connection refused"

**Cause:** Mauvais cluster ou credentials invalides

**Solution:**
1. Vérifier le cluster dans .env (doit être `eu`)
2. Vérifier que PUSHER_HOST est vide (pas 127.0.0.1)
3. Vérifier les credentials dans Pusher Dashboard

---

### Problème: "Failed to load resource: net::ERR_BLOCKED_BY_CLIENT"

**Cause:** Bloqueur de pub ou pare-feu

**Solution:**
- Désactiver les bloqueurs de pub
- Vérifier le pare-feu serveur (autoriser port 443 sortant)

---

### Problème: Events non reçus côté frontend

**Cause:** VITE_PUSHER_* non configurés

**Solution:**
```bash
# Vérifier que ces variables existent
grep VITE_PUSHER .env

# Si manquantes, les ajouter et recompiler
npm run build
```

---

## 📝 CREDENTIALS À SAUVEGARDER

```
═══════════════════════════════════════
PUSHER CHANNELS - MGS PRODUCTION
═══════════════════════════════════════

App ID: _________________________
Key: ____________________________
Secret: _________________________
Cluster: eu

Dashboard: https://dashboard.pusher.com
App URL: https://dashboard.pusher.com/apps/<app_id>

═══════════════════════════════════════
```

**⚠️ Ne jamais commiter ces informations dans Git !**

---

## ✅ CHECKLIST COMPLÈTE

### Local
- [ ] Compte Pusher créé
- [ ] App "MGS Production" créée (Cluster: eu)
- [ ] Credentials notés
- [ ] .env mis à jour (Administration)
- [ ] .env mis à jour (Commercial)
- [ ] .env mis à jour (Gestion-Dossier)
- [ ] `php artisan config:cache` sur les 3 apps
- [ ] Test notification envoyée
- [ ] Debug Console Pusher vérifié

### Production
- [ ] Script `deploy_pusher_production.sh` transféré
- [ ] Script exécuté avec credentials
- [ ] Caches Laravel nettoyés
- [ ] Assets compilés (si nécessaire)
- [ ] Test notification production
- [ ] Debug Console Pusher vérifié (production)

---

**🎉 Une fois tout validé, votre système de notifications temps réel est opérationnel !**
