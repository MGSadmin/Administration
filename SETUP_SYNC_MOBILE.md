# 📱 Setup Synchronisation Mobile ↔ Web

## 🎯 Résumé

Ce guide explique comment configurer et utiliser la synchronisation bidirectionnelle entre l'application Flutter mobile (`app_inventaire`) et le système web Laravel (`administration`).

## ⚙️ Configuration Laravel

### 1. Installer les dépendances
```bash
cd /var/www/administration
composer require laravel/sanctum
```

### 2. Publier la configuration Sanctum
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 3. Migrer la base de données
```bash
php artisan migrate

# Migration custom pour sync mobile
php artisan make:migration add_mobile_sync_to_patrimoines
# Puis copier le contenu de database/migrations/2025_01_26_000000_add_mobile_sync_to_patrimoines.php
php artisan migrate
```

### 4. Configurer le middleware dans `app/Http/Middleware/Authenticate.php`
```php
// Ajouter sanctum aux guards
protected $guards = [
    'web' => 'session',
    'sanctum' => 'sanctum',
];
```

### 5. Vérifier la configuration `config/sanctum.php`
```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost,127.0.0.1')),
'expiration' => 60 * 24, // 24 heures pour les tokens API
```

### 6. Copier le contrôleur API
```bash
# Créer le répertoire s'il n'existe pas
mkdir -p app/Http/Controllers/Api

# Copier App\Http\Controllers\Api\PatrimoineController.php
# du fichier app/Http/Controllers/Api/PatrimoineController.php
```

### 7. Configurer les routes (`routes/api.php`)
Le fichier `routes/api.php` contient déjà tous les endpoints requis.

### 8. Configurer CORS (si serveur distant)
```php
# config/cors.php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_methods' => ['*'],
'allowed_origins' => ['*'],
'allowed_origins_patterns' => [],
'allowed_headers' => ['*'],
'exposed_headers' => [],
'max_age' => 0,
'supports_credentials' => true,
```

### 9. Créer le dossier de stockage pour les images
```bash
mkdir -p storage/app/public/patrimoines
php artisan storage:link
```

## 📱 Configuration Flutter

### 1. Mettre à jour `pubspec.yaml`
```yaml
dependencies:
  flutter:
    sdk: flutter
  provider: ^6.0.0
  sqflite: ^2.2.0
  path_provider: ^2.0.0
  http: ^0.13.0
  connectivity_plus: ^3.0.0
  flutter_secure_storage: ^5.0.0
  image_picker: ^0.8.0  # Pour prendre des photos
```

```bash
flutter pub get
```

### 2. Copier les fichiers Dart
Copier les fichiers de `/home/tlt/Documents/Inventory/app_inventaire/lib/`:
- `models/asset_models.dart` (modèles mis à jour)
- `models/inventory_provider.dart` (provider mis à jour)
- `services/api_service.dart` (nouveau)
- `services/sync_service.dart` (nouveau)
- `services/image_service.dart` (nouveau)
- `services/database_service.dart` (mis à jour v3)
- `widgets/sync_status_widget.dart` (nouveau)

### 3. Configuration de l'URL API
Modifier `lib/services/api_service.dart`:
```dart
static const String baseUrl = 'http://administration.mgs-local.mg/api';
// En prod: 'https://administration.example.com/api'
```

### 4. Android - Permissions (`android/app/src/main/AndroidManifest.xml`)
```xml
<uses-permission android:name="android.permission.INTERNET" />
<uses-permission android:name="android.permission.ACCESS_NETWORK_STATE" />
<uses-permission android:name="android.permission.READ_EXTERNAL_STORAGE" />
<uses-permission android:name="android.permission.WRITE_EXTERNAL_STORAGE" />
<uses-permission android:name="android.permission.CAMERA" />
```

### 5. iOS - Permissions (`ios/Runner/Info.plist`)
```xml
<key>NSCameraUsageDescription</key>
<string>Nous avons besoin de l'accès à la caméra pour photographier les articles</string>
<key>NSPhotoLibraryUsageDescription</key>
<string>Nous avons besoin de l'accès à la galerie pour importer des photos</string>
<key>NSLocalNetworkUsageDescription</key>
<string>Nous avons besoin de scanner le réseau local</string>
```

### 6. Initialiser la synchronisation dans `main.dart`
```dart
void main() {
  runApp(
    ChangeNotifierProvider(
      create: (_) => InventoryProvider(), // Initialise tout auto
      child: const MyApp(),
    ),
  );
}
```

## 🔄 Flux d'utilisation

### Ajouter un patrimoine (offline)
```dart
// L'app ajoute localement ET en queue de sync
await provider.addPatrimoine(
  designation: 'Laptop',
  categorie: Categorie.informatique,
  localisation: 'Bureau 1',
);
// Si offline → enregistré dans sync_operations
// Si online → sync automatiquement
```

### Ajouter une image
```dart
final imageService = ImageService();
final imageBytes = await imagePicker.pickImage();

// Sauvegarder localement
String localPath = await imageService.saveLocalImage(
  patrimoineId,
  imageBytes,
);
// Sera uploadée auto lors du sync
```

### Synchronisation manuelle
```dart
// Afficher le widget de statut
SyncStatusIndicator()

// Ou bouton de sync manuel
ManualSyncButton()

// Ou forcer la sync
await provider.synchronizeData();
```

## 🧪 Tests

### Test 1: Login et création offline
```dart
// Test: App mobile
1. Lancer l'app
2. Désactiver Wifi
3. Créer un patrimoine
4. Vérifier dans DB: sync_operations table a 1 ligne
5. Réactiver Wifi
6. Vérifier sync automatique
```

### Test 2: Récupération données serveur
```bash
# Test: Côté serveur Laravel
php artisan tinker
>>> App\Models\Patrimoine::factory()->create(['designation' => 'Test']);
>>> exit

# App mobile
1. Lancer l'app
2. Vérifier que le patrimoine créé en serveur apparaît
```

### Test 3: Conflit synchronisation
```dart
// Test: Modification concurrent
// Mobil
1. Créer patrimoine A (offline)
2. Modifier patrimoine A (offline)
3. Envoyer les 2 opérations lors sync

// Web
// Entre-temps, quelqu'un a modifié le patrimoine A
1. Patrimoine A modifié par quelqu'un d'autre
2. Lors sync mobile → gestion conflit par timestamp
```

### Test 4: Upload images
```dart
// Test: Photos
1. Ajouter patrimoine
2. Prendre 3 photos
3. Désactiver Wifi
4. Vérifier images dans Documents/images/patrimoineId/
5. Réactiver Wifi
6. Vérifier upload auto dans storage/app/public/patrimoines/
```

## 📊 Monitoring

### Vérifier la synchronisation (Laravel)
```bash
# SQL pour vérifier quoi a été synchro
select id, code_materiel, last_synced_at, sync_source 
from patrimoines 
order by last_synced_at desc 
limit 10;

# Images uploadées
ls -la storage/app/public/patrimoines/
```

### DevTools Flutter
```
1. DevTools → Network tab
2. Voir requêtes HTTP vers /api/patrimoines
3. Voir les uploads multipart images
```

### Logs
```bash
# Laravel
tail -f storage/logs/laravel.log

# Flutter (Android Studio)
flutter logs
```

## ⚠️ Dépannage courants

### "Token expiré"
```dart
// Cause: Token Sanctum expiré (default 60min)
// Solution:
// 1. Relancer login automatique
// 2. Augmenter expiration dans config/sanctum.php
// 3. Implémenter refresh token
```

### "Images ne s'uploadent pas"
```bash
# Cause 1: Dossier storage/app/public/patrimoines n'existe pas
mkdir -p storage/app/public/patrimoines
chmod 755 storage/app/public/patrimoines

# Cause 2: symlink public/storage manquant
php artisan storage:link
```

### "Sync en boucle"
```dart
// Cause: retry_count augmente infini
// Solution: Vérifier retryCount < 5 dans sync_service.dart
// Ou DELETE des sync_operations stuck
```

### "Connexion refusée"
```bash
# Vérifier base URL dans api_service.dart
# Vérifier CORS config
# Vérifier firewall/réseau

# Test connexion:
curl -X GET http://administration.mgs-local.mg/api/patrimoines \
  -H "Authorization: Bearer {token}"
```

## 🚀 Déploiement production

### 1. Laravel
```bash
# Générer APP_KEY s'il n'existe pas
php artisan key:generate

# Optimisation
php artisan config:cache
php artisan route:cache
php artisan view:cache

# HTTPS obligatoire
APP_DEBUG=false
APP_ENV=production
```

### 2. Flutter
```bash
# Build APK/IPA
flutter build apk --release
flutter build ios --release

# Configurer URL base production
const String baseUrl = 'https://administration.example.com/api';
```

### 3. Certificats SSL
```bash
# Laravel doit être en HTTPS pour Sanctum en prod
# Obtenir certificat (Let's Encrypt)
sudo certbot certonly --standalone -d administration.example.com
```

## 📞 Support

Pour les problèmes:
1. Vérifier les logs: `tail -f storage/logs/laravel.log`
2. Vérifier la connectivité: `ping administration.mgs-local.mg`
3. Vérifier le token: `flutter run --verbose`
4. Tester API directement: `curl ...`

---

**Dernière mise à jour**: 26 Nov 2025  
**Statut**: Production-ready ✅
