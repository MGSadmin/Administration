# 📚 Index - Documentation Authentification Centralisée

## 🎯 Vue d'ensemble

Le système d'authentification centralisée MGS permet de gérer la connexion et l'inscription pour les 3 applications (Administration, Commercial, Gestion Dossier) depuis un point unique.

---

## 📖 Documents disponibles

### 1. 🚀 VISUAL_SUMMARY_AUTH.md
**Pour : Démarrage rapide**

Vue d'ensemble visuelle avec diagrammes ASCII art.
- Schémas de l'architecture
- Interface utilisateur
- Flux de connexion
- Statut d'implémentation

👉 **Commencer ici pour une vue d'ensemble rapide**

---

### 2. 📘 README_AUTH.md
**Pour : Documentation technique complète**

Documentation technique détaillée du système.
- Fonctionnalités implémentées
- Structure des fichiers
- Routes et API
- Sécurité et permissions
- Personnalisation
- Tests et maintenance

👉 **Lire pour comprendre en profondeur le système**

---

### 3. 📗 GUIDE_AUTHENTIFICATION.md
**Pour : Guide d'utilisation utilisateur/développeur**

Guide pratique d'utilisation et d'intégration.
- Comment se connecter
- Comment créer un compte
- Configuration des applications
- Workflow d'authentification
- Dépannage
- Checklist de déploiement

👉 **Utiliser pour déployer et utiliser le système**

---

### 4. 📙 MIGRATION_AUTH_CENTRALISEE.md
**Pour : Intégration avec Commercial et Gestion Dossier**

Instructions pas à pas pour migrer les autres applications.
- Code à ajouter dans Commercial
- Code à ajouter dans Gestion Dossier
- Configuration requise
- Routes de redirection
- Réception des tokens SSO

👉 **Suivre pour intégrer les autres applications**

---

## 🗂️ Structure de lecture recommandée

### Pour un administrateur système
```
1. VISUAL_SUMMARY_AUTH.md     (5 min)  - Vue d'ensemble
2. GUIDE_AUTHENTIFICATION.md  (15 min) - Utilisation
3. README_AUTH.md             (10 min) - Détails techniques
```

### Pour un développeur
```
1. VISUAL_SUMMARY_AUTH.md          (5 min)  - Vue d'ensemble
2. README_AUTH.md                  (15 min) - Architecture
3. MIGRATION_AUTH_CENTRALISEE.md   (20 min) - Intégration
4. GUIDE_AUTHENTIFICATION.md       (10 min) - Configuration
```

### Pour un utilisateur final
```
1. GUIDE_AUTHENTIFICATION.md - Section "Utilisation" uniquement
```

---

## 📂 Fichiers créés

### Vues (Frontend)
```
/resources/views/auth/
├── login.blade.php          ✅ Page de connexion
└── register.blade.php       ✅ Page d'inscription
```

### Contrôleurs (Backend)
```
/app/Http/Controllers/Auth/
└── AuthController.php       ✅ Logique d'authentification
```

### Routes
```
/routes/
├── web.php                  ✅ Routes auth ajoutées
└── api.php                  ✅ API validation token
```

### Configuration
```
/config/
└── app_urls.php            ✅ URLs des applications
```

### Documentation
```
/
├── GUIDE_AUTHENTIFICATION.md         ✅ Guide utilisateur
├── MIGRATION_AUTH_CENTRALISEE.md     ✅ Guide migration
├── README_AUTH.md                    ✅ Doc technique
├── VISUAL_SUMMARY_AUTH.md            ✅ Résumé visuel
└── INDEX_AUTH.md                     ✅ Ce fichier
```

---

## 🔗 Liens rapides

### URLs importantes
- Connexion : `/auth/login`
- Inscription : `/auth/register`
- Déconnexion : `/auth/logout`
- API validation : `/api/user`

### Commandes utiles
```bash
# Tester l'authentification
curl -X POST http://localhost/administration/auth/login \
  -d "email=test@mgs.mg" \
  -d "password=password123" \
  -d "site=admin"

# Voir les permissions d'un user
php artisan permission:show test@mgs.mg

# Créer un user de test
php artisan tinker
>>> User::factory()->create(['email' => 'test@mgs.mg']);

# Logs en temps réel
tail -f storage/logs/laravel.log
```

---

## ✅ Checklist rapide

### Administration (Central)
- [x] Page login créée
- [x] Page register créée
- [x] AuthController créé
- [x] Routes configurées
- [x] API token validation
- [x] Configuration app_urls
- [x] Documentation complète

### Commercial
- [ ] Redirection /login vers auth central
- [ ] Redirection /register vers auth central
- [ ] Route de callback SSO
- [ ] Configuration app_urls
- [ ] Variables .env

### Gestion Dossier
- [ ] Redirection /login vers auth central
- [ ] Redirection /register vers auth central
- [ ] Route de callback SSO
- [ ] Configuration app_urls
- [ ] Variables .env

---

## 🎓 FAQ

### Q: Où commence l'utilisateur ?
**R:** Sur `/auth/login` dans l'application Administration. Tous les liens de connexion des autres apps redirigent ici.

### Q: Comment ajouter une nouvelle application ?
**R:** 
1. Mettre à jour `config/app_urls.php`
2. Ajouter le badge dans `login.blade.php`
3. Ajouter la logique dans `AuthController.php`

### Q: Comment tester rapidement ?
**R:** 
```bash
# Créer un user
php artisan tinker
>>> $u = User::factory()->create(['email' => 'test@mgs.mg']);
>>> $u->assignRole('super-admin');

# Tester
http://localhost/administration/auth/login
```

### Q: Que faire en cas d'erreur "Accès refusé" ?
**R:** Vérifier que l'utilisateur a les bonnes permissions. Voir `GUIDE_AUTHENTIFICATION.md` section Dépannage.

### Q: Les tokens expirent quand ?
**R:** Après 7 jours par défaut. Configurable dans `.env` avec `SSO_TOKEN_LIFETIME`.

---

## 🆘 Support

### Problème de connexion
1. Consulter `GUIDE_AUTHENTIFICATION.md` → Section Dépannage
2. Vérifier les logs : `tail -f storage/logs/laravel.log`
3. Vérifier les permissions : `php artisan permission:show EMAIL`

### Problème d'intégration
1. Consulter `MIGRATION_AUTH_CENTRALISEE.md`
2. Vérifier la configuration des URLs dans `.env`
3. Tester l'API : `curl http://localhost/administration/api/user -H "Authorization: Bearer TOKEN"`

### Autre problème
1. Chercher dans l'index ci-dessus le document approprié
2. Consulter les logs
3. Contacter : admin@mgs.mg

---

## 📊 Résumé en 30 secondes

```
┌─────────────────────────────────────────────────────────┐
│  ✅ Ce qui fonctionne maintenant:                       │
│                                                          │
│  • Page de connexion centralisée                        │
│  • Page d'inscription centralisée                       │
│  • Sélection de l'application cible                     │
│  • Validation des permissions                           │
│  • Création de tokens SSO                               │
│  • Redirection automatique                              │
│  • Journalisation des connexions                        │
│                                                          │
│  ⏳ À faire:                                            │
│                                                          │
│  • Rediriger Commercial vers auth central               │
│  • Rediriger Gestion Dossier vers auth central          │
│  • Implémenter réception tokens SSO dans les 2 apps     │
│                                                          │
│  📖 Documents: 5 fichiers de documentation créés        │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

---

**Version** : 1.0.0  
**Date** : 8 décembre 2025  
**Statut** : ✅ Administration terminée | ⏳ Migration en attente
