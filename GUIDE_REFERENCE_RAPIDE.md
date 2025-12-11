================================================================================
GUIDE RÉFÉRENCE RAPIDE - ARCHITECTURE MULTI-SITES
================================================================================
Date: 11 décembre 2025

================================================================================
COMMANDES D'INSTALLATION
================================================================================

# ADMINISTRATION (Serveur OAuth2)
cd /var/www/administration
composer install
npm install
php artisan migrate
php artisan passport:install
php artisan passport:client --password --name="Commercial Client"
php artisan passport:client --password --name="Gestion Dossier Client"
npm run build

# COMMERCIAL (Client OAuth2)
cd /var/www/commercial
composer install
npm install
php artisan migrate
npm run build

# GESTION-DOSSIER (Client OAuth2)
cd /var/www/gestion-dossier
composer install
npm install
php artisan migrate
npm run build


================================================================================
VARIABLES .ENV ESSENTIELLES
================================================================================

ADMINISTRATION (.env)
---------------------
BROADCAST_CONNECTION=pusher
QUEUE_CONNECTION=redis
CACHE_STORE=redis

PUSHER_APP_ID=123456
PUSHER_APP_KEY=your_key
PUSHER_APP_SECRET=your_secret
PUSHER_APP_CLUSTER=eu

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password


COMMERCIAL (.env)
-----------------
OAUTH_CLIENT_ID=<from passport:client>
OAUTH_CLIENT_SECRET=<from passport:client>
OAUTH_REDIRECT_URI=http://commercial.mgs-local.mg/auth/callback
OAUTH_SERVER_URL=http://administration.mgs-local.mg

BROADCAST_CONNECTION=pusher
QUEUE_CONNECTION=redis
PUSHER_APP_ID=123456
PUSHER_APP_KEY=your_key
PUSHER_APP_SECRET=your_secret
PUSHER_APP_CLUSTER=eu


GESTION-DOSSIER (.env)
----------------------
OAUTH_CLIENT_ID=<from passport:client>
OAUTH_CLIENT_SECRET=<from passport:client>
OAUTH_REDIRECT_URI=http://gestion-dossier.mgs-local.mg/auth/callback
OAUTH_SERVER_URL=http://administration.mgs-local.mg

BROADCAST_CONNECTION=pusher
QUEUE_CONNECTION=redis
PUSHER_APP_ID=123456
PUSHER_APP_KEY=your_key
PUSHER_APP_SECRET=your_secret
PUSHER_APP_CLUSTER=eu


================================================================================
DÉMARRAGE DES SERVICES
================================================================================

# Terminal 1 : Redis
redis-server

# Terminal 2 : Queue Worker Administration
cd /var/www/administration
php artisan queue:work redis --queue=notifications,emails,default

# Terminal 3 : Queue Worker Commercial
cd /var/www/commercial
php artisan queue:work redis --queue=notifications,emails,default

# Terminal 4 : Queue Worker Gestion-Dossier
cd /var/www/gestion-dossier
php artisan queue:work redis --queue=notifications,emails,default

# Terminal 5, 6, 7 : Serveurs de développement
php artisan serve --host=administration.mgs-local.mg
php artisan serve --host=commercial.mgs-local.mg --port=8001
php artisan serve --host=gestion-dossier.mgs-local.mg --port=8002


================================================================================
TESTS RAPIDES
================================================================================

# 1. Tester OAuth2 
Naviguer vers: http://commercial.mgs-local.mg/login/oauth

# 2. Tester notification simple
php artisan tinker
>>> $user = \App\Models\User::first();
>>> $user->notify(new \App\Notifications\GenericNotification([
...     'titre' => 'Test',
...     'message' => 'Message de test',
...     'type' => 'info'
... ]));

# 3. Tester notification inter-applications
// Dans le site Commercial (via tinker ou contrôleur)
use Illuminate\Support\Facades\Http;

$user = auth()->user();
Http::withToken($user->oauth_token)
    ->post('http://administration.mgs-local.mg/api/notifications/send', [
        'user_id' => 1,
        'type' => 'test',
        'titre' => 'Depuis Commercial',
        'message' => 'Notification inter-apps',
    ]);


================================================================================
UTILISATION DES NOTIFICATIONS
================================================================================

# Envoyer une notification à un utilisateur
$user->notify(new \App\Notifications\CongeApprouve($conge));

# Envoyer à plusieurs utilisateurs
$managers = User::role('manager')->get();
Notification::send($managers, new \App\Notifications\NouvelleDemandeConge($conge, $demandeur));

# Broadcast événement global
broadcast(new \App\Events\AnnoncePubliee($annonce));

# Notification inter-applications (depuis Commercial/Gestion-dossier vers Admin)
Http::withToken(auth()->user()->oauth_token)
    ->post(env('OAUTH_SERVER_URL').'/api/notifications/send', [
        'user_id' => $userId,
        'type' => 'nouvelle_vente',
        'titre' => 'Nouvelle vente',
        'message' => 'Vente créée par '.auth()->user()->name,
        'url' => 'http://commercial.mgs-local.mg/ventes/'.$vente->id
    ]);


================================================================================
ROUTES OAUTH2
================================================================================

# ADMINISTRATION (Serveur)
/oauth/authorize          - Autorisation OAuth2
/oauth/token              - Obtenir/rafraîchir un token
/api/user                 - Informations utilisateur authentifié
/api/auth/me              - User avec rôles et permissions
/api/notifications/send   - Envoyer une notification

# COMMERCIAL & GESTION-DOSSIER (Clients)
/login/oauth              - Rediriger vers serveur OAuth
/auth/callback            - Callback OAuth2
/logout                   - Déconnexion


================================================================================
FRONTEND - ÉCOUTER LES NOTIFICATIONS
================================================================================

Dans votre layout Blade, ajouter:

<meta name="user-id" content="{{ auth()->id() }}">

<div class="notification-bell">
    <i class="fa fa-bell"></i>
    <span id="notification-count" style="display: none;">0</span>
</div>

<div class="notification-dropdown">
    <div class="notification-dropdown-list">
        <!-- Notifications ici -->
    </div>
</div>

Les notifications arrivent automatiquement via le fichier notifications.js


================================================================================
COMMANDES UTILES
================================================================================

# Voir les routes OAuth
php artisan route:list --path=oauth

# Lister les clients Passport
php artisan passport:client --list

# Vider le cache Redis
php artisan cache:clear

# Voir les jobs en queue
php artisan queue:monitor

# Vérifier Redis
redis-cli ping

# Voir les notifications d'un user
php artisan tinker
>>> User::find(1)->notifications

# Marquer toutes les notifications comme lues
>>> User::find(1)->unreadNotifications->markAsRead();

# Supprimer les tokens expirés
php artisan passport:purge


================================================================================
DÉPANNAGE RAPIDE
================================================================================

PROBLÈME: "Invalid state parameter" lors OAuth
SOLUTION: Vérifier SESSION_DRIVER=database dans .env

PROBLÈME: "Failed to obtain access token"
SOLUTION: Vérifier OAUTH_CLIENT_ID et OAUTH_CLIENT_SECRET

PROBLÈME: Notifications non reçues en temps réel
SOLUTION: 
  1. Vérifier queue worker actif
  2. Vérifier PUSHER_* credentials
  3. Console navigateur pour erreurs WebSocket

PROBLÈME: Emails non envoyés
SOLUTION:
  1. Vérifier MAIL_* dans .env
  2. Vérifier queue worker actif
  3. Voir logs: storage/logs/laravel.log

PROBLÈME: Erreur 401 sur API
SOLUTION: Token expiré, utiliser refresh token

PROBLÈME: Redis connection refused
SOLUTION: sudo systemctl start redis-server


================================================================================
SÉCURITÉ
================================================================================

✓ HTTPS obligatoire en production
✓ Ne jamais committer .env
✓ Changer PUSHER_APP_SECRET en production
✓ Utiliser APP_KEY unique par application
✓ Configurer CORS correctement
✓ Rate limiting sur les APIs
✓ Valider toutes les entrées


================================================================================
STRUCTURE DES FICHIERS CRÉÉS
================================================================================

ADMINISTRATION/
├── app/
│   ├── Http/Controllers/Api/
│   │   └── NotificationApiController.php
│   ├── Notifications/
│   │   ├── GenericNotification.php
│   │   ├── CongeApprouve.php
│   │   └── NouvelleDemandeConge.php
│   └── Providers/
│       └── AuthServiceProvider.php
├── config/
│   ├── auth.php (modifié)
│   └── broadcasting.php
├── resources/
│   ├── js/
│   │   ├── bootstrap.js (modifié)
│   │   ├── app.js (modifié)
│   │   └── notifications.js
│   └── css/
│       └── notifications.css
├── routes/
│   └── api.php (modifié)
└── composer.json (modifié)

COMMERCIAL/
├── app/
│   └── Http/Controllers/Auth/
│       └── OAuthController.php
├── config/
│   └── broadcasting.php
├── database/migrations/
│   └── 2025_12_10_000001_add_oauth_fields_to_users_table.php
├── resources/
│   └── js/
│       ├── bootstrap.js (modifié)
│       ├── app.js (modifié)
│       └── notifications.js
└── composer.json (modifié)

GESTION-DOSSIER/
├── app/
│   └── Http/Controllers/Auth/
│       └── OAuthController.php
├── config/
│   └── broadcasting.php
├── database/migrations/
│   └── 2025_12_10_000001_add_oauth_fields_to_users_table.php
├── resources/
│   └── js/
│       ├── bootstrap.js (modifié)
│       ├── app.js (modifié)
│       └── notifications.js
└── composer.json (modifié)


================================================================================
PROCHAINES ÉTAPES
================================================================================

1. Exécuter composer install sur les 3 applications
2. Exécuter npm install sur les 3 applications
3. Configurer les .env avec les bonnes valeurs
4. Exécuter les migrations
5. Installer Passport et créer les clients OAuth
6. Compiler les assets (npm run build)
7. Démarrer Redis et les queue workers
8. Tester l'authentification OAuth
9. Tester les notifications
10. Déployer en production avec Supervisor

================================================================================
FIN DU GUIDE
================================================================================
