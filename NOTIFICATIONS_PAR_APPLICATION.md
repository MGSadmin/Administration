# Système de Notifications Contextuelles par Application

## 📋 Vue d'ensemble

Les notifications sont maintenant **filtrées par application** pour éviter que les notifications de Gestion-Dossier n'apparaissent dans Commercial, etc.

## 🎯 Règles de visibilité

### Champ `application` dans la table notifications

Valeurs possibles:
- `'administration'` → Visible uniquement dans Administration
- `'gestion-dossier'` → Visible dans Gestion-Dossier ET Administration
- `'commercial'` → Visible dans Commercial ET Administration
- `'all'` → Visible partout (notifications système)

### Matrice de visibilité

| Notification créée dans | application = | Visible dans Admin | Visible dans Gestion-Dossier | Visible dans Commercial |
|------------------------|---------------|-------------------|----------------------------|----------------------|
| Administration | `administration` | ✅ | ❌ | ❌ |
| Gestion-Dossier | `gestion-dossier` | ✅ | ✅ | ❌ |
| Commercial | `commercial` | ✅ | ❌ | ✅ |
| N'importe où | `all` | ✅ | ✅ | ✅ |

**Règle générale**: Administration voit TOUTES les notifications de toutes les applications (car c'est l'application de supervision).

## 💻 Utilisation dans le code

### Méthode 1: Définir `application` dans la notification

```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class DossierCreatedNotification extends Notification
{
    public function toArray($notifiable): array
    {
        return [
            'title' => 'Nouveau dossier créé',
            'message' => "Le dossier {$this->dossier->reference} a été créé.",
            'icon' => 'fa-folder-plus',
            'url' => route('dossiers.show', $this->dossier->id),
            'application' => 'gestion-dossier', // ← Définir ici
        ];
    }
}
```

### Méthode 2: Utiliser le trait SendsNotifications

```php
use App\Traits\SendsNotifications;

class DossierController extends Controller
{
    use SendsNotifications;
    
    public function store(Request $request)
    {
        $dossier = Dossier::create($request->validated());
        
        // Notifier les superviseurs
        $superviseurs = User::role('direction')->get();
        
        $this->sendNotifications(
            $superviseurs,
            new DossierCreatedNotification($dossier),
            'gestion-dossier' // ← Spécifier l'application
        );
        
        return redirect()->route('dossiers.index');
    }
}
```

### Méthode 3: Mise à jour manuelle après envoi

```php
use Illuminate\Support\Facades\DB;

$user->notify(new SituationAssignedNotification($situation, auth()->user()));

// Mettre à jour l'application de la dernière notification
DB::table('notifications')
    ->where('notifiable_id', $user->id)
    ->whereNull('read_at')
    ->latest('created_at')
    ->limit(1)
    ->update(['application' => 'gestion-dossier']);
```

## 📝 Exemples concrets

### Exemple 1: Notification de situation attribuée (Gestion-Dossier)

**Scénario**: Un chef attribue une situation à un collaborateur

```php
// Dans SituationController@assign
public function assign(Request $request, $id)
{
    $situation = Situation::findOrFail($id);
    $collaborateur = User::findOrFail($request->collaborateur_id);
    
    $situation->update(['user_id' => $collaborateur->id]);
    
    // Envoyer la notification
    $collaborateur->notify(
        new SituationAssignedNotification($situation, auth()->user())
    );
    
    // La notification aura application='gestion-dossier'
    // Elle sera visible:
    // ✅ Dans Gestion-Dossier (pour le collaborateur)
    // ✅ Dans Administration (pour les admins)
    // ❌ PAS dans Commercial
    
    return redirect()->back()->with('success', 'Situation attribuée');
}
```

### Exemple 2: Notification de devis validé (Commercial)

```php
namespace App\Notifications;

class DevisApprovedNotification extends Notification
{
    public function toArray($notifiable): array
    {
        return [
            'title' => 'Devis approuvé',
            'message' => "Le devis {$this->devis->reference} a été approuvé !",
            'icon' => 'fa-check-circle',
            'url' => route('devis.show', $this->devis->id),
            'application' => 'commercial', // Visible dans Commercial + Administration
        ];
    }
}
```

### Exemple 3: Notification système (toutes les applications)

```php
namespace App\Notifications;

class SystemMaintenanceNotification extends Notification
{
    public function toArray($notifiable): array
    {
        return [
            'title' => 'Maintenance planifiée',
            'message' => 'Le système sera en maintenance demain de 2h à 4h du matin.',
            'icon' => 'fa-tools',
            'url' => '#',
            'application' => 'all', // Visible partout
        ];
    }
}
```

## 🔧 Configuration par application

### Administration

Dans `/var/www/administration/config/app.php`:
```php
'name' => 'Administration',
```

### Gestion-Dossier

Dans `/var/www/gestion-dossier/config/app.php`:
```php
'name' => 'Gestion-Dossier',
```

### Commercial

Dans `/var/www/commercial/config/app.php`:
```php
'name' => 'Commercial',
```

Le système détecte automatiquement l'application via:
```php
$currentApp = config('app.name') === 'Administration' ? 'administration' : 
              (config('app.name') === 'Gestion-Dossier' ? 'gestion-dossier' : 'commercial');
```

## 📊 Base de données

### Structure de la table notifications

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
    application VARCHAR(50) DEFAULT 'all', -- ← Nouveau champ
    KEY idx_notifiable (notifiable_type, notifiable_id),
    KEY idx_application (application) -- ← Nouvel index
);
```

### Migration

```bash
php artisan migrate
# Applique: 2025_11_20_063151_add_application_to_notifications_table.php
```

## 🎨 Interface utilisateur

### Dropdown (navbar)

Le compteur et la liste sont filtrés automatiquement:

```php
@php
    $currentApp = config('app.name') === 'Administration' ? 'administration' : 'gestion-dossier';
    $unreadCount = auth()->user()->unreadNotifications()
        ->where(function($query) use ($currentApp) {
            $query->where('application', $currentApp)
                  ->orWhere('application', 'all');
        })->count();
@endphp
```

### Page complète (/notifications)

Le contrôleur filtre automatiquement:

```php
public function index()
{
    $currentApp = config('app.name') === 'Administration' ? 'administration' : 'gestion-dossier';
    
    $notifications = Auth::user()->notifications()
        ->where(function($query) use ($currentApp) {
            $query->where('application', $currentApp)
                  ->orWhere('application', 'all');
        })
        ->paginate(20);
    
    return view('notifications.index', compact('notifications'));
}
```

## ✅ Réponse à votre question

> Dans situation, quand on attribue à quelqu'un, est-ce qu'on voit cette notification partout?

**NON**, avec ce système:

1. **Notification créée dans Gestion-Dossier** (attribution de situation)
   - `application = 'gestion-dossier'`
   - Visible dans: ✅ Gestion-Dossier, ✅ Administration
   - PAS visible dans: ❌ Commercial

2. **Notification créée dans Commercial** (nouveau devis)
   - `application = 'commercial'`
   - Visible dans: ✅ Commercial, ✅ Administration
   - PAS visible dans: ❌ Gestion-Dossier

3. **Notification système** (maintenance, mise à jour)
   - `application = 'all'`
   - Visible dans: ✅ Toutes les applications

**Pourquoi Administration voit tout?**
- C'est l'application de supervision
- Les admins doivent pouvoir voir l'activité de toutes les applications
- Ils peuvent filtrer par application si nécessaire (future amélioration)

## 🚀 Prochaines étapes

1. ✅ Migration exécutée
2. ✅ Layout modifié avec filtrage
3. ✅ NotificationController modifié
4. ✅ Notification exemple créée
5. 🔄 À faire: Copier les modifications dans gestion-dossier
6. 🔄 À faire: Créer les notifications pour attribution de situation
7. 🔄 À faire: Tester le système complet

---

**Date**: 20 novembre 2025  
**Version**: 2.0.0
