# Guide Rapide - Authentification et Notifications MGS

## 🚀 Accès rapide

### Connexion
- **URL**: http://administration.mgs-local.mg/login
- **Compte admin**: admin@mgs.mg / Admin@2025

### Créer un compte
- **URL**: http://administration.mgs-local.mg/register
- Remplir le formulaire avec vos informations

## ✅ Modifications effectuées

### 1. ✅ Erreurs de syntaxe corrigées

**dashboard.blade.php**
- ❌ Avant: Utilisait `</x-app-layout>` au lieu de `@endsection`
- ✅ Après: Utilise correctement `@endsection` avec `@extends('layouts.admin')`

**users/index.blade.php**
- ❌ Avant: Utilisait `</x-app-layout>` au lieu de `@endsection`
- ✅ Après: Utilise correctement `@endsection` avec `@extends('layouts.admin')`

### 2. 🔐 Nouveau système de login

**login.blade.php**
- Design moderne avec logo TLT
- Gradient violet professionnel
- Validation en temps réel
- Lien vers création de compte
- Option "Mot de passe oublié"
- Responsive mobile

**register.blade.php**
- Formulaire complet avec nom, prénom, email, poste, département
- Indicateur de force du mot de passe (faible/moyen/fort)
- Validation JavaScript en temps réel
- Design cohérent avec la page de login
- Responsive

### 3. 🔔 Système de notifications partagé

**Base de données**
- Table: `mgs_administration.notifications`
- Partagée entre toutes les applications
- Stocke: titre, message, icône, URL, timestamp

**Interface utilisateur**
- Dropdown dans la navbar avec badge de compteur
- Page complète: `/notifications`
- Actions: Marquer comme lu, Supprimer, Tout marquer comme lu
- Vérification automatique toutes les 30 secondes

**Backend**
- NotificationController avec 5 méthodes
- Routes protégées par middleware auth
- Support pour pagination
- Notifications en temps réel

## 📝 Test du système

### Tester les notifications

```bash
# Dans le terminal
cd /var/www/administration
php artisan tinker

# Dans tinker
$user = App\Models\User::first();
$user->notify(new App\Notifications\UserCreatedNotification($user));
exit
```

Puis:
1. Aller sur http://administration.mgs-local.mg/dashboard
2. Cliquer sur l'icône 🔔 dans la navbar
3. Voir la notification apparaître

### Tester le login

1. Déconnectez-vous (bouton dans le menu utilisateur)
2. Vous serez redirigé vers la nouvelle page de login
3. Testez:
   - Connexion avec admin@mgs.mg / Admin@2025
   - Bouton "Créer un compte"
   - Responsive (réduisez la fenêtre)

### Tester la création de compte

1. Aller sur http://administration.mgs-local.mg/register
2. Remplir tous les champs
3. Taper un mot de passe et observer la barre de force
4. Soumettre le formulaire

## 🔧 Intégration dans gestion-dossier

Les fichiers suivants ont été copiés:
- ✅ `NotificationController.php`
- ✅ `resources/views/notifications/`

Pour activer les notifications dans gestion-dossier, ajouter ces routes dans `routes/web.php`:

```php
use App\Http\Controllers\NotificationController;

Route::middleware('auth')->group(function () {
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/check-new', [NotificationController::class, 'checkNew'])->name('notifications.check-new');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});
```

## 📊 Exemples d'utilisation des notifications

### Notification simple

```php
use App\Models\User;
use Illuminate\Support\Facades\Notification;

// Envoyer à un utilisateur
$user = User::find(1);
$user->notify(new App\Notifications\UserCreatedNotification($user));
```

### Notification à plusieurs utilisateurs

```php
// Envoyer à tous les admins
$admins = User::role('admin')->get();
Notification::send($admins, new App\Notifications\UserCreatedNotification($newUser));
```

### Créer une nouvelle notification

```bash
php artisan make:notification DossierCreatedNotification
```

```php
public function toArray($notifiable): array
{
    return [
        'title' => 'Nouveau dossier créé',
        'message' => "Le dossier {$this->dossier->reference} a été créé.",
        'icon' => 'fa-folder-plus',
        'url' => route('dossiers.show', $this->dossier->id),
    ];
}
```

## 🎨 Personnalisation

### Changer les couleurs du login

Dans `resources/views/auth/login.blade.php`:

```css
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

Remplacez par vos couleurs:
```css
background: linear-gradient(135deg, #votre_couleur1 0%, #votre_couleur2 100%);
```

### Changer le logo

Remplacez le fichier `/public/images/logo.png` par votre logo.

## 🐛 Résolution de problèmes

### Les notifications n'apparaissent pas

```bash
# Vérifier que la table existe
php artisan tinker
DB::table('notifications')->count();
exit

# Vérifier les routes
php artisan route:list --name=notifications
```

### Erreur "Session not found"

Vérifier dans `.env`:
```env
SESSION_CONNECTION=administration
```

Et dans `config/session.php`:
```php
'connection' => env('SESSION_CONNECTION'),
```

### Le login ne fonctionne pas entre applications

Vérifier que les 3 applications ont:
1. Le même `APP_KEY`
2. Le même `SESSION_COOKIE`
3. Le même `SESSION_DOMAIN` (avec le point: `.mgs-local.mg`)

## 📚 Documentation complète

Voir: `/var/www/administration/SYSTEME_AUTHENTIFICATION_NOTIFICATIONS.md`

---

**Prochaines étapes recommandées**:

1. ✅ Tester le système de login
2. ✅ Tester les notifications
3. ✅ Créer des notifications personnalisées pour vos besoins
4. 🔄 Ajouter les routes de notifications dans gestion-dossier
5. 🔄 Intégrer le dropdown notifications dans le layout de gestion-dossier
6. 🔄 Créer des notifications pour les actions importantes (création dossier, validation, etc.)

**Testé et fonctionnel** ✅
