# Guide de Configuration des Notifications

## Problème Résolu

Les notifications dans l'application Administration ne fonctionnaient pas pour deux raisons:

1. **Permissions de fichiers** - Les logs Laravel n'étaient pas accessibles en écriture
2. **Queue Worker** - Les notifications sont en queue (`ShouldQueue`) mais le worker ne tournait pas

---

## Solution Appliquée

### 1. Correction des Permissions

```bash
sudo chown -R www-data:www-data /var/www/administration/storage /var/www/administration/bootstrap/cache
sudo chmod -R 775 /var/www/administration/storage /var/www/administration/bootstrap/cache
```

### 2. Ajout de la Notification lors de la Création

Modifié `DemandeFournitureController::store()` pour envoyer une notification lors de la création:

```php
$demande = DemandeFourniture::create($validated);

// Envoyer notification à la personne désignée
$demande->envoyerNotification('creee');
```

### 3. Configuration de la Queue

**Pour DÉVELOPPEMENT (Immédiat, sans queue):**
```env
QUEUE_CONNECTION=sync
```

**Pour PRODUCTION (Avec queue, plus performant):**
```env
QUEUE_CONNECTION=database
```

---

## Types de Notifications Disponibles

### Demandes de Fourniture

| Événement | Quand | Qui est notifié |
|-----------|-------|-----------------|
| `creee` | Création de la demande | Demandeur + Personne désignée (notifier_user_id) |
| `validee` | Validation de la demande | Demandeur + Personne désignée + Validateur |
| `rejetee` | Rejet de la demande | Demandeur + Personne désignée + Validateur |
| `commandee` | Commande passée | Demandeur + Personne désignée |
| `recue` | Fourniture réceptionnée | Demandeur + Personne désignée |
| `livree` | Fourniture livrée | Demandeur + Personne désignée |

### Patrimoine

| Événement | Quand | Qui est notifié |
|-----------|-------|-----------------|
| `attribue` | Attribution du matériel | Nouvel utilisateur + Ancien utilisateur (si existant) |
| `libere` | Libération du matériel | Utilisateur précédent |

---

## Configuration de la Queue en Production

### Option 1: Supervisor (Recommandé)

1. **Installer Supervisor:**
```bash
sudo apt-get install supervisor
```

2. **Créer le fichier de configuration:**
```bash
sudo nano /etc/supervisor/conf.d/administration-worker.conf
```

3. **Contenu du fichier:**
```ini
[program:administration-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/administration/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/administration/storage/logs/worker.log
stopwaitsecs=3600
```

4. **Activer et démarrer:**
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start administration-worker:*
```

5. **Vérifier le statut:**
```bash
sudo supervisorctl status administration-worker:*
```

### Option 2: Systemd Service

1. **Créer le service:**
```bash
sudo nano /etc/systemd/system/administration-queue.service
```

2. **Contenu:**
```ini
[Unit]
Description=Administration Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/administration
ExecStart=/usr/bin/php /var/www/administration/artisan queue:work database --sleep=3 --tries=3
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```

3. **Activer et démarrer:**
```bash
sudo systemctl enable administration-queue
sudo systemctl start administration-queue
sudo systemctl status administration-queue
```

### Option 3: Cron (Solution simple)

1. **Éditer crontab:**
```bash
crontab -e
```

2. **Ajouter cette ligne:**
```cron
* * * * * cd /var/www/administration && php artisan queue:work --stop-when-empty
```

Cette méthode lance le worker chaque minute et s'arrête quand la queue est vide.

---

## Commandes Utiles

### Gestion de la Queue

```bash
# Voir les jobs en attente
php artisan queue:monitor

# Traiter un seul job (pour test)
php artisan queue:work --once

# Démarrer le worker manuellement
php artisan queue:work

# Démarrer avec options
php artisan queue:work --tries=3 --timeout=60

# Arrêter gracieusement les workers
php artisan queue:restart

# Vider la queue
php artisan queue:flush

# Voir les jobs échoués
php artisan queue:failed

# Rejouer un job échoué
php artisan queue:retry <job-id>

# Rejouer tous les jobs échoués
php artisan queue:retry all

# Supprimer tous les jobs échoués
php artisan queue:flush
```

### Vérifier les Notifications

```bash
# Voir les notifications dans la base de données
php artisan tinker

>>> \App\Models\User::find(1)->notifications;
>>> \App\Models\User::find(1)->unreadNotifications;
```

---

## Test des Notifications

### 1. Créer une Demande de Fourniture

1. Connectez-vous à l'application
2. Allez dans "Demandes de Fourniture" → "Nouvelle Demande"
3. Remplissez le formulaire
4. **Important**: Sélectionnez une personne dans "Personne à notifier"
5. Créez la demande

### 2. Vérifier la Notification

#### Dans la Base de Données:
```sql
SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5;
```

#### Dans l'Application:
- Cliquez sur l'icône de notification (cloche) en haut à droite
- Les notifications non lues apparaissent en gras

#### Dans les Logs (si MAIL_MAILER=log):
```bash
tail -f /var/www/administration/storage/logs/laravel.log
```

### 3. Test Complet du Workflow

```bash
# 1. Créer une demande (utilisateur normal)
# 2. Valider la demande (direction/admin)
php artisan queue:work --once  # Si queue=database

# 3. Commander (admin/rh)
php artisan queue:work --once

# 4. Réceptionner (admin/rh)
php artisan queue:work --once

# 5. Livrer (admin/rh)
php artisan queue:work --once
```

À chaque étape, vérifiez que les notifications sont bien reçues.

---

## Configuration Email (Production)

Pour envoyer de vrais emails au lieu de les logger:

### 1. Modifier `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@gmail.com
MAIL_PASSWORD=votre-mot-de-passe-app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@votre-domaine.com
MAIL_FROM_NAME="MGS Administration"
```

### 2. Pour Gmail - Créer un Mot de Passe d'Application:

1. Allez sur https://myaccount.google.com/security
2. Activez la validation en 2 étapes
3. Créez un mot de passe d'application
4. Utilisez ce mot de passe dans `MAIL_PASSWORD`

### 3. Tester l'envoi d'email:

```bash
php artisan tinker

>>> Mail::raw('Test email', function($msg) { 
    $msg->to('destinataire@example.com')->subject('Test'); 
});
```

---

## Dépannage

### Les notifications ne s'affichent pas

1. **Vérifier la queue:**
```bash
php artisan queue:work --once
```

2. **Vérifier les permissions:**
```bash
ls -la storage/logs/
```

3. **Vérifier la base de données:**
```sql
SELECT COUNT(*) FROM notifications;
```

### Les emails ne partent pas

1. **Vérifier la configuration:**
```bash
php artisan config:cache
php artisan config:clear
```

2. **Tester avec log:**
```env
MAIL_MAILER=log
```

3. **Voir les logs:**
```bash
tail -f storage/logs/laravel.log | grep -i mail
```

### Le queue worker se bloque

1. **Arrêter tous les workers:**
```bash
php artisan queue:restart
```

2. **Vider les jobs en erreur:**
```bash
php artisan queue:flush
```

3. **Redémarrer proprement:**
```bash
php artisan queue:work --tries=3
```

---

## Optimisations

### 1. Limiter le Nombre de Notifications

Dans `DemandeFourniture::envoyerNotification()`:

```php
// Ne pas notifier si déjà notifié récemment
if ($this->notification_envoyee && $this->date_notification > now()->subMinutes(5)) {
    return;
}
```

### 2. Grouper les Notifications

```php
// Au lieu de notifier immédiatement, grouper par batch
Notification::send($users, new DemandeFournitureNotification($this, $evenement));
```

### 3. Notifications Asynchrones Sélectives

Certaines notifications peuvent être synchrones (importantes), d'autres asynchrones:

```php
// Notification importante = sync
class UrgentNotification extends Notification 
{
    // Pas de ShouldQueue
}

// Notification normale = async
class NormalNotification extends Notification implements ShouldQueue
{
    use Queueable;
}
```

---

## Récapitulatif de la Configuration Actuelle

**État actuel:**
- ✅ Permissions corrigées
- ✅ Notification de création ajoutée
- ✅ Queue configurée en `sync` (développement)
- ✅ Emails en mode `log`

**Pour passer en production:**
1. Changer `QUEUE_CONNECTION=database` dans `.env`
2. Configurer Supervisor pour le queue worker
3. Configurer un vrai serveur SMTP
4. Tester avec de vrais utilisateurs

---

*Dernière mise à jour: 20/11/2025*
