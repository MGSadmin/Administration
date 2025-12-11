# 🔐 SYSTÈME D'AUTHENTIFICATION ET NOTIFICATIONS UNIFIÉES

## ✅ CORRECTIONS IMPLÉMENTÉES

Ce document décrit les corrections apportées pour résoudre les problèmes d'authentification et de notifications entre les 3 sites (Administration, Commercial, Gestion-Dossier).

---

## 📋 PROBLÈMES RÉSOLUS

### ✅ 1. Session partagée entre les sites
**Problème** : Chaque site avait son propre cookie de session, donc un utilisateur connecté sur `administration` n'était pas reconnu sur `commercial` ou `gestion-dossier`.

**Solution implémentée** :
- **Cookie unique** : `mgs_session` pour les 3 sites
- **Domaine partagé** : `.mgs.mg` (accessible depuis tous les sous-domaines)
- **Connexion DB partagée** : Tous les sites utilisent la connexion `administration` pour stocker les sessions

**Fichiers modifiés** :
- `/var/www/administration/config/session.php`
- `/var/www/commercial/config/session.php`
- `/var/www/gestion-dossier/config/session.php`

```php
// Configuration commune dans les 3 sites
'cookie' => env('SESSION_COOKIE', 'mgs_session'),
'domain' => env('SESSION_DOMAIN', '.mgs.mg'),
'connection' => env('SESSION_CONNECTION', 'administration'),
```

---

### ✅ 2. Notifications centralisées

**Problème** : Chaque site avait sa propre table `notifications`, donc une notification créée dans `debours` était invisible dans `commercial` et `administration`.

**Solution implémentée** :
- **API centralisée** dans `administration` pour gérer toutes les notifications
- **Service client** dans `commercial` et `gestion-dossier` pour accéder à l'API

**Nouveaux fichiers créés** :

#### API Centralisée (Administration)
- `/var/www/administration/app/Http/Controllers/Api/NotificationApiController.php`
- Routes dans `/var/www/administration/routes/api.php` :
  ```
  GET    /api/notifications              - Récupérer les notifications
  GET    /api/notifications/unread-count - Compter les non lues
  POST   /api/notifications              - Créer une notification
  PATCH  /api/notifications/{id}/mark-as-read - Marquer comme lue
  POST   /api/notifications/mark-all-as-read  - Tout marquer comme lu
  DELETE /api/notifications/{id}         - Supprimer une notification
  ```

#### Services clients
- `/var/www/commercial/app/Services/CentralNotificationService.php`
- `/var/www/gestion-dossier/app/Services/CentralNotificationService.php`

**Utilisation** :
```php
use App\Services\CentralNotificationService;

$service = new CentralNotificationService();

// Récupérer les notifications d'un utilisateur
$result = $service->getUserNotifications($userId, $unreadOnly = false, $limit = 50);

// Compter les non lues
$count = $service->getUnreadCount($userId);

// Créer une notification
$service->createNotification($userId, 'App\\Notifications\\NewDossierNotification', [
    'message' => 'Nouveau dossier créé',
    'dossier_id' => 123
]);

// Marquer comme lue
$service->markAsRead($notificationId);

// Tout marquer comme lu
$service->markAllAsRead($userId);
```

---

### ✅ 3. Déconnexion globale

**Problème** : Déconnexion sur un site ne déconnectait pas des autres sites.

**Solution implémentée** :
- Déconnexion centralisée sur `administration`
- Les sites `commercial` et `gestion-dossier` redirigent vers `administration/auth/logout`
- La session partagée est invalidée en un seul endroit

**Fichiers modifiés** :
- `/var/www/administration/app/Http/Controllers/Auth/AuthController.php`
- `/var/www/commercial/app/Http/Controllers/AuthController.php`
- `/var/www/gestion-dossier/app/Http/Controllers/AuthController.php`

**Comportement** :
1. Utilisateur clique "Déconnexion" sur n'importe quel site
2. Redirection vers `administration/auth/logout`
3. Révocation de tous les tokens Sanctum
4. Invalidation de la session (partagée par les 3 sites)
5. Redirection vers la page de login

---

## 🚀 FONCTIONNEMENT ACTUEL

### Scénario 1 : Connexion
1. Utilisateur va sur `commercial.mgs.mg`
2. Redirection vers `administration.mgs.mg/auth/login?site=commercial`
3. Connexion réussie → Cookie `mgs_session` créé avec domaine `.mgs.mg`
4. Redirection vers `commercial.mgs.mg/dashboard`
5. ✅ **Si l'utilisateur va maintenant sur `administration.mgs.mg`, il est automatiquement connecté** (même cookie)

### Scénario 2 : Notifications
1. Dans `gestion-dossier`, création d'une notification pour l'utilisateur #5
   ```php
   $service->createNotification(5, 'NewDossierNotification', ['message' => 'Dossier X créé']);
   ```
2. ✅ **Cette notification est immédiatement visible dans `administration`, `commercial` ET `gestion-dossier`**
3. Si l'utilisateur marque la notification comme lue dans `commercial`, elle apparaît lue partout

### Scénario 3 : Déconnexion
1. Utilisateur clique "Déconnexion" dans `commercial.mgs.mg`
2. Redirection vers `administration.mgs.mg/auth/logout`
3. Session invalidée (cookie `mgs_session` supprimé)
4. ✅ **L'utilisateur est déconnecté de TOUS les sites simultanément**

---

## ⚙️ CONFIGURATION REQUISE

### Variables d'environnement

#### Administration (.env)
```env
SESSION_DRIVER=database
SESSION_COOKIE=mgs_session
SESSION_DOMAIN=.mgs.mg
SESSION_CONNECTION=mysql
```

#### Commercial (.env)
```env
SESSION_DRIVER=database
SESSION_COOKIE=mgs_session
SESSION_DOMAIN=.mgs.mg
SESSION_CONNECTION=administration

CENTRAL_AUTH_URL=https://administration.mgs.mg
```

#### Gestion-Dossier (.env)
```env
SESSION_DRIVER=database
SESSION_COOKIE=mgs_session
SESSION_DOMAIN=.mgs.mg
SESSION_CONNECTION=administration

CENTRAL_AUTH_URL=http://administration.mgs-local.mg
```

---

## 🔧 MIGRATION NÉCESSAIRE

### 1. Vider les anciennes sessions
```bash
# Sur chaque site
php artisan session:clear
```

### 2. S'assurer que la table sessions existe dans la base administration
```bash
cd /var/www/administration
php artisan session:table
php artisan migrate
```

### 3. Tester les cookies
1. Se connecter sur `administration.mgs.mg`
2. Vérifier dans DevTools → Application → Cookies
   - Nom : `mgs_session`
   - Domaine : `.mgs.mg`
   - Path : `/`
3. Accéder à `commercial.mgs.mg` → Doit être automatiquement connecté

---

## 📊 VÉRIFICATION

### Test 1 : Session partagée
```bash
# 1. Se connecter sur administration
# 2. Ouvrir commercial dans un nouvel onglet
# 3. Vérifier qu'on est connecté sans redemander login
```

### Test 2 : Notifications partagées
```bash
# Dans gestion-dossier
curl -X POST http://administration.mgs.mg/api/notifications \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 1,
    "type": "TestNotification",
    "data": {"message": "Test notification"}
  }'

# Vérifier que la notification apparaît dans les 3 sites
```

### Test 3 : Déconnexion globale
```bash
# 1. Se connecter sur commercial
# 2. Ouvrir administration dans un autre onglet (doit être connecté)
# 3. Se déconnecter de commercial
# 4. Rafraîchir administration → Doit demander login
```

---

## 🎯 AVANTAGES

1. **Expérience utilisateur fluide** : Une seule connexion pour tous les sites
2. **Notifications unifiées** : Toutes les notifications au même endroit
3. **Sécurité renforcée** : Déconnexion globale instantanée
4. **Maintenance simplifiée** : Une seule source de vérité pour les sessions et notifications
5. **Performance** : Pas de vérifications multiples, session unique

---

## 📝 NOTES IMPORTANTES

1. Le domaine `.mgs.mg` (avec le point) est crucial pour partager le cookie entre sous-domaines
2. Tous les sites doivent pointer vers la même base de données `administration` pour les sessions
3. L'API de notifications nécessite un token Sanctum valide
4. En local, utiliser `.mgs-local.mg` comme domaine de session

---

**Date de mise en œuvre** : 10 décembre 2025
**Statut** : ✅ Implémenté et testé
