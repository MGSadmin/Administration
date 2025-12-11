# Système de Gestion des Postes Vacants - Organigramme

## 🎯 Objectif

Gérer l'attribution des postes de l'organigramme aux utilisateurs avec suivi complet des changements (démission, licenciement, retraite, réaffectation).

## ⚡ Installation rapide

```bash
cd /var/www/administration
./setup_member_management.sh
```

## 📋 Fonctionnalités

✅ **Affectation d'utilisateurs aux postes**
- Assigner un utilisateur à un poste vacant
- Vérifier qu'un utilisateur n'a qu'un poste actif
- Historiser toutes les affectations

✅ **Gestion des départs**
- Démission (avec commentaire optionnel)
- Licenciement (avec motif obligatoire)
- Retraite (avec date effective)
- Le poste devient automatiquement VACANT

✅ **Réaffectation**
- Demande de mutation vers un autre poste
- Workflow d'approbation/rejet
- Historisation du changement

✅ **Postes vacants**
- Vue dédiée aux postes non pourvus
- Liste des utilisateurs disponibles
- Affectation rapide

✅ **Historique complet**
- Traçabilité de tous les changements
- Qui a fait quoi, quand et pourquoi
- Consultation par membre ou globale

## 🌐 Accès

| URL | Description |
|-----|-------------|
| `/organigramme/members` | Liste des membres |
| `/organigramme/members-vacant` | Postes vacants |
| `/organigramme/members-history` | Historique complet |
| `/organigramme/members/{id}` | Détail d'un membre |

## 📁 Fichiers créés/modifiés

### Migrations
- `database/migrations/2024_12_09_000001_create_historique_statut_membres_table.php`

### Modèles
- `app/Models/OrganizationMember.php` (amélioré)
- `app/Models/HistoriqueStatutMembre.php` (existe déjà)
- `app/Models/PositionAssignment.php` (nouveau)
- `app/Models/ReaffectationRequest.php` (nouveau)

### Contrôleur
- `app/Http/Controllers/MemberStatusController.php`

### Vues
- `resources/views/organigramme/members/index.blade.php`
- `resources/views/organigramme/members/show.blade.php`
- `resources/views/organigramme/members/vacant.blade.php`
- `resources/views/organigramme/members/history.blade.php`
- `resources/views/organigramme/members/modals/demission.blade.php`
- `resources/views/organigramme/members/modals/licenciement.blade.php`
- `resources/views/organigramme/members/modals/retraite.blade.php`
- `resources/views/organigramme/members/modals/reaffectation.blade.php`

### Routes
- `routes/web.php` (section organigramme.members)

### Documentation
- `GUIDE_GESTION_MEMBRES_ORGANIGRAMME.md` (guide complet)
- `setup_member_management.sh` (script d'installation)

## 💡 Exemples d'utilisation

### Affecter un utilisateur

```php
$member = OrganizationMember::where('position_id', $positionId)->first();
$user = User::find($userId);
$member->assignUser($user, "Nouvelle embauche", auth()->id());
```

### Marquer une démission

```php
$member->markAsDemission("Démission pour raisons personnelles", auth()->id());
```

### Marquer un licenciement

```php
$member->markAsLicencie("Faute grave", auth()->id());
```

### Obtenir les postes vacants

```php
$vacants = OrganizationMember::getVacantPositions();
```

## 🔒 Permissions

- `voir_membres_organigramme`
- `modifier_membres_organigramme`
- `affecter_membres_organigramme`
- `licencier_membres_organigramme`
- `voir_historique_membres`

Attribuées automatiquement aux rôles **administrateur** et **rh**.

## 📊 Statuts disponibles

- `ACTIVE` - Poste occupé et actif
- `VACANT` - Poste vacant
- `INTERIM` - Poste en intérim
- `DEMISSION` - Démission
- `LICENCIE` - Licencié
- `RETRAITE` - Retraité

## 📚 Documentation complète

Voir `GUIDE_GESTION_MEMBRES_ORGANIGRAMME.md` pour la documentation détaillée.

## ✅ Prêt à l'emploi !

Le système est maintenant opérationnel. Vous pouvez :

1. Consulter l'organigramme interactif
2. Voir les postes vacants
3. Affecter des utilisateurs
4. Gérer les départs
5. Suivre l'historique complet

---

**Auteur**: Système Administration MGS  
**Date**: Décembre 2024  
**Version**: 1.0
