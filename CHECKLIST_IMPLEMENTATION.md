# ✅ Checklist Implémentation Synchronisation Mobile

## 🎯 Phase 1: Backend Laravel (Administration)

### Installation et Configuration
- [ ] `composer require laravel/sanctum`
- [ ] `php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"`
- [ ] `php artisan migrate` (inclus Sanctum tables)
- [ ] Configurer `config/sanctum.php` (expiration, stateful domains)
- [ ] Configurer `config/cors.php` pour mobile
- [ ] Créer dossier `storage/app/public/patrimoines`
- [ ] `php artisan storage:link`

### Code Backend
- [ ] Copier `app/Http/Controllers/Api/PatrimoineController.php`
- [ ] Mettre à jour `app/Models/Patrimoine.php`
- [ ] Mettre à jour `routes/api.php`
- [ ] Copier migration `database/migrations/2025_01_26_000000_add_mobile_sync_to_patrimoines.php`
- [ ] `php artisan migrate`

### Tester API
- [ ] Test login: `POST /api/login`
- [ ] Test CRUD: `GET/POST/PUT/DELETE /api/patrimoines`
- [ ] Test images: `POST /api/patrimoines/{id}/photos`
- [ ] Vérifier headers Authorization Bearer token

### Cache et Optimisation
- [ ] `php artisan config:cache` (prod)
- [ ] `php artisan route:cache` (prod)
- [ ] Configurer CORS headers

---

## 📱 Phase 2: App Flutter (App Inventaire)

### Dépendances
- [ ] `flutter pub add provider`
- [ ] `flutter pub add sqflite`
- [ ] `flutter pub add path_provider`
- [ ] `flutter pub add http`
- [ ] `flutter pub add connectivity_plus`
- [ ] `flutter pub add flutter_secure_storage`
- [ ] `flutter pub get`

### Fichiers à copier/mettre à jour
- [ ] `lib/models/asset_models.dart` (modèles enrichis)
- [ ] `lib/models/inventory_provider.dart` (sync intégré)
- [ ] `lib/services/api_service.dart` (nouveau)
- [ ] `lib/services/sync_service.dart` (nouveau)
- [ ] `lib/services/image_service.dart` (nouveau)
- [ ] `lib/services/database_service.dart` (v3 mise à jour)
- [ ] `lib/widgets/sync_status_widget.dart` (nouveau)

### Configuration
- [ ] Vérifier URL API base dans `api_service.dart`
- [ ] Vérifier android `AndroidManifest.xml` permissions
- [ ] Vérifier iOS `Info.plist` permissions
- [ ] Mettre à jour `main.dart` avec InventoryProvider

### Vérifications Dart
- [ ] `flutter analyze` (pas d'erreurs)
- [ ] `flutter format lib/` (formatage)
- [ ] `flutter pub get` (dépendances ok)

---

## 🧪 Phase 3: Tests

### Test 1: Connexion API
```
- [ ] Login success → reçoit token
- [ ] Token stocké dans FlutterSecureStorage
- [ ] Token utilisé dans headers Authorization
- [ ] Logout efface le token
```

### Test 2: Opérations Offline
```
- [ ] Créer patrimoine sans internet
- [ ] Vérifier dans SQLite sync_operations (1 ligne, op=create)
- [ ] Vérifier dans assets (syncedToServer=0)
- [ ] Rétablir internet
- [ ] Vérifier auto-sync en 5 min
- [ ] Vérifier API logs: POST reçu
- [ ] Vérifier sync_operations table vide
- [ ] Vérifier assets.syncedToServer=1
```

### Test 3: Images Offline
```
- [ ] Prendre photo de patrimoine (offline)
- [ ] Vérifier fichier dans Documents/images/{id}/image_*.jpg
- [ ] Vérifier asset_photos.localPath rempli, remoteUrl null
- [ ] Rétablir internet → auto sync
- [ ] Vérifier upload dans storage/app/public/patrimoines/{id}/
- [ ] Vérifier asset_photos.remoteUrl rempli
```

### Test 4: Pull du serveur
```
- [ ] Créer patrimoine directement en Laravel
- [ ] Lancer app mobile
- [ ] Vérifier patrimoine apparaît localement
- [ ] Vérifier timestamps alignés
```

### Test 5: Conflit
```
- [ ] Modifier patrimoine en offline
- [ ] Entre-temps, modifier en Laravel
- [ ] Reconnecter mobile
- [ ] Vérifier: Quelle version gagne? (timestamp résout)
```

### Test 6: UI Statut
```
- [ ] SyncStatusIndicator affiche online/offline
- [ ] ManualSyncButton désactivé si offline
- [ ] SyncStatusPanel affiche opérations en attente
- [ ] Loader visible si syncing
- [ ] Message "Dernière synchro: ..." affichée
```

---

## 🚀 Phase 4: Déploiement

### Pre-prod Testing
- [ ] Tous les tests passent
- [ ] App fonctionne offline 30 min
- [ ] Images sync correctement
- [ ] Pas de fuite mémoire (DevTools)
- [ ] Pas d'erreurs logs
- [ ] Performance acceptable (<2s sync)

### Configuration Prod
- [ ] Laravel: `APP_ENV=production`, `APP_DEBUG=false`
- [ ] Laravel: `php artisan config:cache`
- [ ] Flutter: Configurer URL base API prod
- [ ] Flutter: Vérifier certificats SSL
- [ ] Laravel: Certificat SSL Let's Encrypt

### Build Release
- [ ] `flutter build apk --release` (Android)
- [ ] `flutter build ios --release` (iOS)
- [ ] Tester APK/IPA sur vrais devices
- [ ] Tester avec réseau réel (pas localhost)

### Monitoring Post-Deploy
- [ ] [ ] Vérifier logs Laravel: `/var/log/laravel.log`
- [ ] Vérifier images uploadées: `ls storage/app/public/patrimoines/`
- [ ] Vérifier tokens Sanctum pas expirés
- [ ] Vérifier DB pas fragmentée
- [ ] Configurer backups automatiques

---

## 📋 Checkliste avant Production

### Code Quality
- [ ] Pas de `print()` ou `console.log()`
- [ ] Erreurs gérées avec try/catch
- [ ] Pas de credentials en dur (env vars)
- [ ] Tests unitaires si applicable
- [ ] Code commenté si complexe

### Sécurité
- [ ] Token Sanctum expiré après 24h
- [ ] CORS limité à domaine prod
- [ ] Images validées (size, type, virus)
- [ ] Pas d'injection SQL (paramètres liés)
- [ ] Pas d'XSS (sanitize JSON)
- [ ] HTTPS forcé en prod

### Performance
- [ ] Pagination API (50 items/page)
- [ ] Images compressées avant upload
- [ ] DB indexée sur colonnes fréquentes
- [ ] Cache HTTP headers configurés
- [ ] Connexion DB pool size adapté

### Maintenance
- [ ] Documentation mise à jour
- [ ] Scripts de backup programmés
- [ ] Monitoring/alertes configurées
- [ ] Plan de rollback défini
- [ ] Support sur-appel disponible

---

## 📞 Contacts Support

| Rôle | Info |
|------|------|
| **Backend Laravel** | - Vérifier routes/api.php<br>- Logs: storage/logs/laravel.log<br>- DB admin si problème sync |
| **Frontend Flutter** | - Vérifier api_service.dart URL<br>- Logs: flutter run --verbose<br>- DevTools Network tab |
| **DevOps** | - SSL/HTTPS cert<br>- Firewall ports 80/443<br>- Storage permissions |
| **QA** | - Tester offline/online<br>- Tester images<br>- Tester conflict resolution |

---

## 🎓 Documentation de Référence

1. **INTEGRATION_GUIDE.md** (50+ pages)
   - Architecture complète
   - Tous les endpoints API
   - Diagrammes flux
   - Tests détaillés

2. **SETUP_SYNC_MOBILE.md** (30+ pages)
   - Configuration pas à pas
   - Commandes à exécuter
   - Dépannage courant
   - URLs et ports

3. **FICHIERS_MODIFIES.md** (cette page)
   - Récapitulatif fichiers
   - Schémas BD
   - Stats implémentation

---

## ⏱️ Timing Estimé

| Phase | Durée |
|-------|-------|
| Backend Setup | 30 min |
| Backend Tests | 30 min |
| Frontend Setup | 30 min |
| Frontend Tests | 1h |
| Integration Tests | 1h |
| Performance Tuning | 1h |
| **Total** | **~4h30** |

---

## 🎉 Complétion

```
Phase 1 Backend:    ▓▓▓▓▓▓▓▓▓░░ 90%
Phase 2 Frontend:   ▓▓▓▓▓▓▓▓▓░░ 90%
Phase 3 Tests:      ▓▓▓▓▓░░░░░░ 50%
Phase 4 Deployment: ▓░░░░░░░░░░ 10%
─────────────────────────────────────
Total:              ▓▓▓▓▓▓▓▓░░░ 55%

Statut: EN COURS ⚙️ → BIENTÔT PRÊT ✅
```

---

**Date**: 26 Nov 2025  
**Version**: 1.0  
**Prochaine étape**: Phase 3 Tests complets
