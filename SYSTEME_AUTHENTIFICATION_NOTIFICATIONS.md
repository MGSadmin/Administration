# Système d'Authentification et de Notifications MGS

## Vue d'ensemble

Le système MGS utilise une architecture centralisée pour l'authentification et les notifications, permettant à toutes les applications (Administration, Gestion-Dossier, Commercial) de partager les mêmes utilisateurs et notifications.

## 🔐 Système d'Authentification

### Architecture

- **Base de données centrale**: `mgs_administration`
- **Table utilisateurs**: `users` (stocke tous les utilisateurs de toutes les applications)
- **Session partagée**: Utilise le driver `database` avec connexion `administration`

### Configuration requise

Toutes les applications doivent avoir la même configuration dans `.env`:

```env
# Clé d'encryption (IDENTIQUE pour toutes les apps)
APP_KEY=base64:4h1KyrT9SPyovQVBOv4k+Ko1Qg/e++0K38850z2kDU4=

# Configuration session
SESSION_DRIVER=database
SESSION_CONNECTION=administration
SESSION_COOKIE=mgs_session
SESSION_DOMAIN=.mgs.mg
SESSION_LIFETIME=120

# Base de données administration (pour les sessions)
DB_CONNECTION_ADMINISTRATION=mysql
DB_HOST_ADMINISTRATION=127.0.0.1
DB_PORT_ADMINISTRATION=3306
DB_DATABASE_ADMINISTRATION=mgs_administration
DB_USERNAME_ADMINISTRATION=root
DB_PASSWORD_ADMINISTRATION=
```

### Pages d'authentification

#### Page de connexion

- **Fichier**: `/resources/views/auth/login.blade.php`
- **Design**: Moderne avec logo TLT, gradient violet
- **Fonctionnalités**:
  - Connexion avec email et mot de passe
  - Option "Se souvenir de moi"
  - Lien "Mot de passe oublié"
  - Lien vers création de compte
  - Validation des erreurs en temps réel
  - Design responsive

#### Page de création de compte

- **Fichier**: `/resources/views/auth/register.blade.php`
- **Champs**:
  - Nom et Prénom
  - Email
  - Poste (optionnel)
  - Département (optionnel)
  - Mot de passe avec indicateur de force
  - Confirmation du mot de passe
- **Fonctionnalités**:
  - Indicateur visuel de force du mot de passe
  - Validation en temps réel
  - Design cohérent avec la page de login

### Flux d'authentification multi-applications

1. **Utilisateur non connecté accède à gestion-dossier**:
   - Le middleware `CheckApplicationAccess` détecte l'absence de session
   - Sauvegarde l'URL demandée dans `session()->put('url.intended')`
   - Redirige vers `http://administration.mgs.mg/login`

2. **Connexion sur administration**:
   - L'utilisateur se connecte
   - `AuthenticatedSessionController` récupère `url.intended`
   - Si l'URL contient `gestion-dossier` ou `commercial`, redirige vers cette URL
   - Sinon, redirige vers le dashboard de l'administration

3. **Session partagée**:
   - La session est stockée dans `mgs_administration.sessions`
   - Cookie: `mgs_session` valide sur `.mgs.mg`
   - Toutes les sous-domaines peuvent lire cette session

## 🔔 Système de Notifications

### Architecture

- **Base de données**: `mgs_administration.notifications`
- **Package Laravel**: Notifications natives de Laravel
- **Stockage**: Base de données uniquement (pas d'emails pour l'instant)

### Table notifications

```sql
CREATE TABLE notifications (
    id CHAR(36) PRIMARY KEY,
    type VARCHAR(255),
    notifiable_type VARCHAR(255),
    notifiable_id BIGINT UNSIGNED,
    data TEXT,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX(notifiable_type, notifiable_id)
);
```

### Utilisation dans le code

#### Créer une notification

```php
use App\Notifications\UserCreatedNotification;

// Envoyer une notification à un utilisateur
$user->notify(new UserCreatedNotification($newUser));

// Envoyer à tous les admins
$admins = User::role('admin')->get();
Notification::send($admins, new UserCreatedNotification($newUser));
```

#### Créer une nouvelle classe de notification

```bash
php artisan make:notification NomDeVotreNotification
```

```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class NomDeVotreNotification extends Notification
{
    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Titre de la notification',
            'message' => 'Message détaillé',
            'icon' => 'fa-info-circle', // Icône Font Awesome
            'url' => route('some.route'), // URL optionnelle
            // Autres données personnalisées
        ];
    }
}
```

### Interface utilisateur

#### Dropdown notifications (dans la navbar)

- **Badge**: Affiche le nombre de notifications non lues
- **Liste**: 10 dernières notifications
- **Actions**:
  - Cliquer sur une notification → la marque comme lue
  - "Tout marquer comme lu" → marque toutes les notifications
  - "Voir toutes les notifications" → redirige vers `/notifications`

#### Page complète des notifications

- **URL**: `/notifications`
- **Fonctionnalités**:
  - Liste paginée de toutes les notifications
  - Filtrage lu/non lu (visuel)
  - Actions: Marquer comme lu, Supprimer
  - Bouton "Tout marquer comme lu"

### Vérification automatique

Le système vérifie automatiquement les nouvelles notifications toutes les 30 secondes:

```javascript
// Dans layouts/admin.blade.php
setInterval(() => {
    fetch('/notifications/check-new')
        .then(response => response.json())
        .then(data => {
            if (data.hasNew) {
                // Affiche un toast Bootstrap
                // Recharge la liste des notifications
            }
        });
}, 30000);
```

### Routes

```php
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');
    
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.mark-as-read');
    
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.mark-all-read');
    
    Route::get('/notifications/check-new', [NotificationController::class, 'checkNew'])
        ->name('notifications.check-new');
    
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])
        ->name('notifications.destroy');
});
```

## 📱 Intégration dans une nouvelle application

### Étape 1: Configuration `.env`

Copier la configuration depuis `/var/www/administration/.env`:

```env
APP_KEY=base64:4h1KyrT9SPyovQVBOv4k+Ko1Qg/e++0K38850z2kDU4=
SESSION_DRIVER=database
SESSION_CONNECTION=administration
SESSION_COOKIE=mgs_session
SESSION_DOMAIN=.mgs.mg
```

### Étape 2: Fichiers à copier

```bash
# Contrôleur notifications
cp /var/www/administration/app/Http/Controllers/NotificationController.php \
   /chemin/vers/nouvelle-app/app/Http/Controllers/

# Vues notifications
cp -r /var/www/administration/resources/views/notifications \
      /chemin/vers/nouvelle-app/resources/views/

# Pages d'authentification
cp /var/www/administration/resources/views/auth/login.blade.php \
   /chemin/vers/nouvelle-app/resources/views/auth/

cp /var/www/administration/resources/views/auth/register.blade.php \
   /chemin/vers/nouvelle-app/resources/views/auth/
```

### Étape 3: Layout avec notifications

Ajouter le dropdown notifications dans votre layout (navbar):

```html
<!-- Notifications -->
<div class="dropdown me-3">
    <button class="btn btn-link text-white position-relative" type="button" data-bs-toggle="dropdown">
        <i class="fas fa-bell fa-lg"></i>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ auth()->user()->unreadNotifications->count() }}
            </span>
        @endif
    </button>
    <!-- Voir /var/www/administration/resources/views/layouts/admin.blade.php pour le code complet -->
</div>
```

### Étape 4: Routes

Ajouter dans `routes/web.php`:

```php
use App\Http\Controllers\NotificationController;

Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/check-new', [NotificationController::class, 'checkNew'])->name('notifications.check-new');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});
```

## 🎨 Personnalisation

### Couleurs et style

Les pages de login utilisent un gradient violet par défaut:

```css
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

Pour changer les couleurs, modifiez le style dans:
- `/resources/views/auth/login.blade.php`
- `/resources/views/auth/register.blade.php`

### Logo

Les pages utilisent `/public/images/logo.png`. Remplacez ce fichier dans chaque application ou utilisez un CDN centralisé.

## 🔧 Maintenance

### Nettoyer les anciennes notifications

```php
// Supprimer les notifications lues de plus de 30 jours
use Illuminate\Support\Facades\DB;

DB::table('notifications')
    ->whereNotNull('read_at')
    ->where('read_at', '<', now()->subDays(30))
    ->delete();
```

Créer une commande artisan pour l'automatiser:

```bash
php artisan make:command CleanOldNotifications
```

### Vérifier les sessions actives

```sql
SELECT * FROM mgs_administration.sessions 
WHERE last_activity > UNIX_TIMESTAMP(NOW() - INTERVAL 2 HOUR);
```

## 📊 Statistiques

### Compter les notifications

```php
// Total notifications
$total = auth()->user()->notifications()->count();

// Non lues
$unread = auth()->user()->unreadNotifications()->count();

// Par type
$byType = auth()->user()->notifications()
    ->select('type', DB::raw('count(*) as total'))
    ->groupBy('type')
    ->get();
```

## 🛡️ Sécurité

### Bonnes pratiques

1. **APP_KEY**: Ne JAMAIS modifier l'APP_KEY après la mise en production
2. **SESSION_DOMAIN**: Utiliser un point devant le domaine (`.mgs.mg`)
3. **HTTPS**: En production, forcer HTTPS pour toutes les applications
4. **CSRF**: Toujours inclure `@csrf` dans les formulaires
5. **Sanitization**: Les notifications sont automatiquement échappées par Blade

### Validation des données

```php
// Dans une notification
public function toArray($notifiable): array
{
    return [
        'title' => strip_tags($this->title), // Enlever les balises HTML
        'message' => e($this->message), // Échapper les caractères spéciaux
        'url' => filter_var($this->url, FILTER_VALIDATE_URL) ? $this->url : '#',
    ];
}
```

## 📞 Support

Pour toute question sur le système d'authentification ou de notifications:

1. Consulter ce document
2. Vérifier la configuration `.env`
3. Examiner les logs Laravel: `storage/logs/laravel.log`
4. Tester la connexion à la base de données `mgs_administration`

---

**Dernière mise à jour**: 20 novembre 2025
**Version**: 1.0.0
**Auteur**: MGS Development Team
