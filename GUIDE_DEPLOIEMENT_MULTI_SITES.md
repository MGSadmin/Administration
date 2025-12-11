================================================================================
GUIDE DE DÉPLOIEMENT - ARCHITECTURE MULTI-SITES LARAVEL
================================================================================
Date: 10 décembre 2025
Version: 1.0

================================================================================
PARTIE 1 : INSTALLATION DES DÉPENDANCES
================================================================================

ÉTAPE 1.1 : SITE ADMINISTRATION (Serveur OAuth2)
-------------------------------------------------

cd /var/www/administration

# Installation des dépendances Composer
composer require laravel/passport
composer require predis/predis
composer require pusher/pusher-php-server

# Installation des dépendances NPM
npm install --save laravel-echo pusher-js


ÉTAPE 1.2 : SITE COMMERCIAL (Client OAuth2)
--------------------------------------------

cd /var/www/commercial

# Installation des dépendances Composer
composer require laravel/socialite
composer require predis/predis
composer require pusher/pusher-php-server
composer require guzzlehttp/guzzle

# Installation des dépendances NPM
npm install --save laravel-echo pusher-js


ÉTAPE 1.3 : SITE GESTION-DOSSIER (Client OAuth2)
-------------------------------------------------

cd /var/www/gestion-dossier

# Installation des dépendances Composer
composer require laravel/socialite
composer require predis/predis
composer require pusher/pusher-php-server
composer require guzzlehttp/guzzle

# Installation des dépendances NPM
npm install --save laravel-echo pusher-js


================================================================================
PARTIE 2 : CONFIGURATION BASE DE DONNÉES ET MIGRATIONS
================================================================================

ÉTAPE 2.1 : ADMINISTRATION - Installer Passport
------------------------------------------------

cd /var/www/administration

# Exécuter les migrations (crée les tables OAuth2)
php artisan migrate

# Installer Passport (génère les clés de chiffrement)
php artisan passport:install --force

# IMPORTANT : Noter les informations affichées
# Vous obtiendrez :
# - Password grant client ID
# - Password grant client secret
# Ces informations seront utilisées par les sites clients

# Créer un client OAuth2 pour le site Commercial
php artisan passport:client --password --name="Commercial Client"
# Noter le Client ID et Client Secret générés

# Créer un client OAuth2 pour le site Gestion-Dossier
php artisan passport:client --password --name="Gestion Dossier Client"
# Noter le Client ID et Client Secret générés


ÉTAPE 2.2 : COMMERCIAL et GESTION-DOSSIER - Migrations
-------------------------------------------------------

cd /var/www/commercial
php artisan migrate

cd /var/www/gestion-dossier
php artisan migrate

# Créer les migrations pour les champs OAuth sur la table users
php artisan make:migration add_oauth_fields_to_users_table


MIGRATION À CRÉER (commercial et gestion-dossier) :
----------------------------------------------------

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('oauth_token')->nullable();
            $table->text('oauth_refresh_token')->nullable();
            $table->timestamp('oauth_token_expires_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['oauth_token', 'oauth_refresh_token', 'oauth_token_expires_at']);
        });
    }
};

# Exécuter la migration
php artisan migrate


================================================================================
PARTIE 3 : CONFIGURATION DES VARIABLES D'ENVIRONNEMENT
================================================================================

ÉTAPE 3.1 : ADMINISTRATION (.env)
---------------------------------

# Base de données (ajuster selon votre environnement)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mgs_administration
DB_USERNAME=root
DB_PASSWORD=

# Queue et Cache
BROADCAST_CONNECTION=pusher
QUEUE_CONNECTION=redis
CACHE_STORE=redis

# Redis
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Pusher (ou utiliser Soketi pour auto-hébergé)
PUSHER_APP_ID=123456
PUSHER_APP_KEY=your_pusher_key
PUSHER_APP_SECRET=your_pusher_secret
PUSHER_APP_CLUSTER=eu

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@administration.mgs.mg
MAIL_FROM_NAME="${APP_NAME}"


ÉTAPE 3.2 : COMMERCIAL (.env)
------------------------------

# OAuth2 Client Configuration
OAUTH_CLIENT_ID=<Client ID du site Commercial depuis Passport>
OAUTH_CLIENT_SECRET=<Client Secret du site Commercial depuis Passport>
OAUTH_REDIRECT_URI=http://commercial.mgs-local.mg/auth/callback
OAUTH_SERVER_URL=http://administration.mgs-local.mg

# Queue et Cache
BROADCAST_CONNECTION=pusher
QUEUE_CONNECTION=redis
CACHE_STORE=redis

# Redis (MÊME configuration que Administration)
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Pusher (MÊME configuration que Administration)
PUSHER_APP_ID=123456
PUSHER_APP_KEY=your_pusher_key
PUSHER_APP_SECRET=your_pusher_secret
PUSHER_APP_CLUSTER=eu

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@commercial.mgs.mg
MAIL_FROM_NAME="${APP_NAME}"


ÉTAPE 3.3 : GESTION-DOSSIER (.env)
-----------------------------------

# OAuth2 Client Configuration
OAUTH_CLIENT_ID=<Client ID du site Gestion-Dossier depuis Passport>
OAUTH_CLIENT_SECRET=<Client Secret du site Gestion-Dossier depuis Passport>
OAUTH_REDIRECT_URI=http://gestion-dossier.mgs-local.mg/auth/callback
OAUTH_SERVER_URL=http://administration.mgs-local.mg

# Queue et Cache
BROADCAST_CONNECTION=pusher
QUEUE_CONNECTION=redis
CACHE_STORE=redis

# Redis (MÊME configuration que Administration)
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Pusher (MÊME configuration que Administration)
PUSHER_APP_ID=123456
PUSHER_APP_KEY=your_pusher_key
PUSHER_APP_SECRET=your_pusher_secret
PUSHER_APP_CLUSTER=eu

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@gestion-dossier.mgs.mg
MAIL_FROM_NAME="${APP_NAME}"


================================================================================
PARTIE 4 : CONFIGURATION PUSHER / BROADCASTING
================================================================================

OPTION 1 : UTILISER PUSHER (Recommandé pour démarrer)
------------------------------------------------------

1. Créer un compte sur https://pusher.com
2. Créer une nouvelle application "MGS Multi-Sites"
3. Choisir le cluster "Europe (eu)"
4. Récupérer les credentials :
   - App ID
   - App Key
   - App Secret
   - Cluster

5. Mettre à jour les .env des 3 applications avec ces credentials


OPTION 2 : UTILISER SOKETI (Auto-hébergé, gratuit)
---------------------------------------------------

1. Installation de Soketi :

npm install -g @soketi/soketi

2. Configuration (créer soketi.json) :

{
  "debug": true,
  "host": "0.0.0.0",
  "port": 6001,
  "appManager.array.apps": [
    {
      "id": "app-id",
      "key": "app-key",
      "secret": "app-secret",
      "maxConnections": 100,
      "enableUserAuthentication": true
    }
  ]
}

3. Lancer Soketi :

soketi start --config=soketi.json

4. Configuration .env (sur les 3 sites) :

PUSHER_APP_ID=app-id
PUSHER_APP_KEY=app-key
PUSHER_APP_SECRET=app-secret
PUSHER_APP_CLUSTER=mt1
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http

VITE_PUSHER_HOST=127.0.0.1
VITE_PUSHER_PORT=6001
VITE_PUSHER_SCHEME=http


================================================================================
PARTIE 5 : CONFIGURATION REDIS
================================================================================

ÉTAPE 5.1 : Installation Redis (si pas déjà installé)
------------------------------------------------------

# Ubuntu/Debian
sudo apt update
sudo apt install redis-server

# macOS
brew install redis

# Démarrer Redis
sudo systemctl start redis-server

# Vérifier que Redis fonctionne
redis-cli ping
# Doit retourner : PONG


ÉTAPE 5.2 : Configuration Redis dans Laravel
---------------------------------------------

config/database.php est déjà configuré par défaut.
Assurez-vous que les .env contiennent :

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

Tester la connexion :

php artisan tinker
>>> Illuminate\Support\Facades\Redis::connection()->ping();
# Doit retourner : true


================================================================================
PARTIE 6 : CONFIGURATION DES ROUTES OAUTH
================================================================================

ÉTAPE 6.1 : COMMERCIAL - Ajouter les routes OAuth
--------------------------------------------------

Fichier : /var/www/commercial/routes/web.php

use App\Http\Controllers\Auth\OAuthController;

// Routes OAuth2
Route::get('/login/oauth', [OAuthController::class, 'redirectToProvider'])->name('oauth.redirect');
Route::get('/auth/callback', [OAuthController::class, 'handleProviderCallback'])->name('oauth.callback');
Route::post('/logout', [OAuthController::class, 'logout'])->name('logout');


ÉTAPE 6.2 : GESTION-DOSSIER - Ajouter les routes OAuth
--------------------------------------------------------

Fichier : /var/www/gestion-dossier/routes/web.php

use App\Http\Controllers\Auth\OAuthController;

// Routes OAuth2
Route::get('/login/oauth', [OAuthController::class, 'redirectToProvider'])->name('oauth.redirect');
Route::get('/auth/callback', [OAuthController::class, 'handleProviderCallback'])->name('oauth.callback');
Route::post('/logout', [OAuthController::class, 'logout'])->name('logout');


ÉTAPE 6.3 : ADMINISTRATION - Activer les routes Passport
---------------------------------------------------------

Les routes Passport sont automatiquement enregistrées :
- /oauth/authorize
- /oauth/token
- /oauth/tokens
- etc.

Tester : http://administration.mgs-local.mg/oauth/clients


================================================================================
PARTIE 7 : CONFIGURATION DU FRONTEND (Laravel Echo)
================================================================================

ÉTAPE 7.1 : Créer resources/js/bootstrap.js
--------------------------------------------

Sur CHACUNE des 3 applications, créer/modifier :
/var/www/[app]/resources/js/bootstrap.js

import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    wsHost: import.meta.env.VITE_PUSHER_HOST ?? `api-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
    wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});


ÉTAPE 7.2 : Créer resources/js/notifications.js
------------------------------------------------

Sur CHACUNE des 3 applications :

// Écouter les notifications pour l'utilisateur connecté
const userId = document.querySelector('meta[name="user-id"]')?.content;

if (userId && window.Echo) {
    window.Echo.private(`App.Models.User.${userId}`)
        .notification((notification) => {
            console.log('Notification reçue:', notification);
            
            // Afficher un toast
            showNotification(notification);
            
            // Mettre à jour le badge de compteur
            updateNotificationBadge();
        });
}

function showNotification(notification) {
    // Utiliser votre système de notification préféré
    // Exemple avec une alerte simple :
    if (notification.titre && notification.message) {
        alert(`${notification.titre}\n${notification.message}`);
    }
    
    // OU utiliser Toastr, SweetAlert, etc.
}

function updateNotificationBadge() {
    // Récupérer le nouveau compteur
    fetch('/api/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('#notification-count');
            if (badge) {
                badge.textContent = data.count;
                badge.style.display = data.count > 0 ? 'inline' : 'none';
            }
        });
}


ÉTAPE 7.3 : Importer dans app.js
---------------------------------

/var/www/[app]/resources/js/app.js

import './bootstrap';
import './notifications';

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();


ÉTAPE 7.4 : Compiler les assets
--------------------------------

cd /var/www/administration
npm run build

cd /var/www/commercial
npm run build

cd /var/www/gestion-dossier
npm run build


================================================================================
PARTIE 8 : DÉMARRAGE DES SERVICES
================================================================================

ÉTAPE 8.1 : Démarrer les Queue Workers
---------------------------------------

Sur CHACUNE des 3 applications, ouvrir un terminal et exécuter :

# Administration
cd /var/www/administration
php artisan queue:work redis --queue=notifications,emails,default --sleep=3 --tries=3

# Commercial
cd /var/www/commercial
php artisan queue:work redis --queue=notifications,emails,default --sleep=3 --tries=3

# Gestion-Dossier
cd /var/www/gestion-dossier
php artisan queue:work redis --queue=notifications,emails,default --sleep=3 --tries=3


ÉTAPE 8.2 : Configuration avec Supervisor (Production)
-------------------------------------------------------

Créer /etc/supervisor/conf.d/laravel-worker-administration.conf :

[program:laravel-worker-administration]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/administration/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/administration/storage/logs/worker.log
stopwaitsecs=3600

Répéter pour commercial et gestion-dossier.

Commandes Supervisor :
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker-administration:*


================================================================================
PARTIE 9 : TESTS DE VALIDATION
================================================================================

ÉTAPE 9.1 : Tester l'authentification OAuth2
---------------------------------------------

1. Accéder à : http://commercial.mgs-local.mg/login/oauth

2. Vous devez être redirigé vers :
   http://administration.mgs-local.mg/oauth/authorize

3. Se connecter avec un compte existant

4. Autoriser l'accès

5. Vous devez être redirigé vers :
   http://commercial.mgs-local.mg/auth/callback?code=...

6. Connexion automatique et redirection vers /dashboard


ÉTAPE 9.2 : Tester les notifications
-------------------------------------

Dans tinker (Administration) :

php artisan tinker

>>> $user = \App\Models\User::first();
>>> $user->notify(new \App\Notifications\GenericNotification([
...     'titre' => 'Test de notification',
...     'message' => 'Ceci est un test',
...     'type' => 'test',
...     'url' => 'http://administration.mgs-local.mg/dashboard'
... ]));

Vérifications :
✓ Email reçu
✓ Notification dans la BDD (table notifications)
✓ Notification en temps réel (si Echo configuré)


ÉTAPE 9.3 : Tester la notification inter-applications
------------------------------------------------------

Dans le site Commercial, envoyer une notification à l'Administration :

use Illuminate\Support\Facades\Http;

$user = auth()->user();

Http::withToken($user->oauth_token)
    ->post(env('OAUTH_SERVER_URL').'/api/notifications/send', [
        'user_id' => 1, // ID d'un utilisateur
        'type' => 'test_commercial',
        'titre' => 'Notification depuis Commercial',
        'message' => 'Test de notification inter-applications',
        'url' => 'http://commercial.mgs-local.mg/dashboard'
    ]);


================================================================================
PARTIE 10 : CHECKLIST DE DÉPLOIEMENT
================================================================================

□ Phase 1 : Préparation
  □ Redis installé et fonctionnel
  □ Composer et NPM à jour sur les 3 sites
  □ Compte Pusher créé (ou Soketi installé)
  □ Configuration SMTP pour les emails

□ Phase 2 : Administration
  □ composer install
  □ npm install
  □ php artisan migrate
  □ php artisan passport:install
  □ Créer les clients OAuth2
  □ Configurer .env
  □ npm run build
  □ Tester l'accès

□ Phase 3 : Commercial
  □ composer install
  □ npm install
  □ Créer migration OAuth fields
  □ php artisan migrate
  □ Configurer .env avec les credentials OAuth
  □ Ajouter routes OAuth
  □ npm run build
  □ Tester le login OAuth

□ Phase 4 : Gestion-Dossier
  □ composer install
  □ npm install
  □ Créer migration OAuth fields
  □ php artisan migrate
  □ Configurer .env avec les credentials OAuth
  □ Ajouter routes OAuth
  □ npm run build
  □ Tester le login OAuth

□ Phase 5 : Notifications
  □ Queue workers démarrés sur les 3 sites
  □ Pusher/Soketi opérationnel
  □ Tester notification simple
  □ Tester notification inter-applications
  □ Tester email
  □ Tester broadcast temps réel

□ Phase 6 : Production
  □ Configurer Supervisor pour les queues
  □ Configurer les cron jobs si nécessaire
  □ Monitoring (logs, Horizon, Telescope)
  □ Backup de la BDD
  □ Documentation utilisateur


================================================================================
PARTIE 11 : COMMANDES UTILES
================================================================================

# Vérifier les routes OAuth2
php artisan route:list --path=oauth

# Vérifier les clients Passport
php artisan passport:client --list

# Révoquer tous les tokens d'un utilisateur
php artisan tinker
>>> \Laravel\Passport\Token::where('user_id', 1)->delete();

# Vider le cache Redis
php artisan cache:clear
redis-cli FLUSHALL

# Voir les jobs en queue
php artisan queue:monitor

# Voir les notifications d'un utilisateur
php artisan tinker
>>> \App\Models\User::find(1)->notifications;

# Tester la connexion Pusher
php artisan tinker
>>> broadcast(new \Illuminate\Notifications\Events\BroadcastNotificationCreated(
...     \App\Models\User::first(),
...     new \App\Notifications\GenericNotification(['titre' => 'Test'])
... ));


================================================================================
PARTIE 12 : DÉPANNAGE (TROUBLESHOOTING)
================================================================================

PROBLÈME : Erreur "Invalid state parameter"
SOLUTION : Vérifier que SESSION_DRIVER=database et que les sessions sont persistantes

PROBLÈME : "Failed to obtain access token"
SOLUTION : Vérifier OAUTH_CLIENT_ID et OAUTH_CLIENT_SECRET dans .env

PROBLÈME : Notifications non reçues en temps réel
SOLUTION : 
  - Vérifier que le queue worker tourne
  - Vérifier les credentials Pusher
  - Vérifier que Echo est correctement configuré dans bootstrap.js
  - Ouvrir la console du navigateur pour voir les erreurs WebSocket

PROBLÈME : Emails non envoyés
SOLUTION :
  - Vérifier MAIL_* dans .env
  - Vérifier que le queue worker tourne
  - Vérifier les logs : storage/logs/laravel.log

PROBLÈME : Erreur 401 sur les appels API
SOLUTION :
  - Le token OAuth a peut-être expiré
  - Utiliser la route /oauth/refresh-token

PROBLÈME : Redis connection refused
SOLUTION :
  - Démarrer Redis : sudo systemctl start redis-server
  - Vérifier REDIS_HOST et REDIS_PORT dans .env


================================================================================
FIN DU GUIDE DE DÉPLOIEMENT
================================================================================

Pour toute question ou problème, consulter :
- Laravel Passport : https://laravel.com/docs/passport
- Laravel Broadcasting : https://laravel.com/docs/broadcasting
- Laravel Notifications : https://laravel.com/docs/notifications
- Pusher : https://pusher.com/docs
- Soketi : https://docs.soketi.app
