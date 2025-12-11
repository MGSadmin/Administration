# Guide de Gestion des Postes et Membres de l'Organigramme

## 📋 Vue d'ensemble

Ce système permet de gérer l'affectation des utilisateurs aux différents postes de l'organigramme et de suivre l'évolution de leur statut (actif, démission, licenciement, retraite, etc.).

## 🎯 Fonctionnalités principales

### 1. Affectation d'utilisateurs aux postes

- Attribution d'un utilisateur à un poste vacant
- Historisation de l'affectation
- Vérification qu'un utilisateur n'a qu'un seul poste actif

### 2. Gestion des départs

#### Démission
- Marquer un membre comme démissionnaire
- Le poste devient automatiquement vacant
- Traçabilité dans l'historique

#### Licenciement
- Enregistrement du motif obligatoire
- Libération immédiate du poste
- Historisation complète

#### Retraite
- Départ en retraite avec date effective
- Poste marqué comme vacant
- Traçabilité de l'événement

### 3. Réaffectation

- Demande de mutation vers un autre poste
- Workflow d'approbation
- Historisation du changement de poste

### 4. Suivi des postes vacants

- Vue dédiée aux postes vacants
- Liste des utilisateurs disponibles
- Affectation rapide depuis cette interface

### 5. Historique complet

- Traçabilité de tous les changements de statut
- Qui a fait quoi et quand
- Motifs et commentaires

## 🚀 Installation

### Méthode rapide

```bash
cd /var/www/administration
./setup_member_management.sh
```

### Méthode manuelle

```bash
# 1. Exécuter les migrations
php artisan migrate --path=database/migrations/2024_12_09_000001_create_historique_statut_membres_table.php

# 2. Nettoyer le cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## 📊 Structure de la base de données

### Table `organization_members`

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | Identifiant unique |
| position_id | bigint | Lien vers le poste |
| user_id | bigint | Lien vers l'utilisateur |
| name | string | Nom (si pas de user_id) |
| status | string | Statut: ACTIVE, VACANT, INTERIM, DEMISSION, LICENCIE, RETRAITE |
| email | string | Email |
| phone | string | Téléphone |
| photo | string | Photo |
| start_date | date | Date de début |
| end_date | date | Date de fin |

### Table `historique_statut_membres`

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | Identifiant unique |
| organization_member_id | bigint | Membre concerné |
| ancien_statut | string | Ancien statut |
| nouveau_statut | string | Nouveau statut |
| motif | string | Motif du changement |
| commentaire | text | Commentaire détaillé |
| user_id | bigint | Utilisateur ayant effectué le changement |
| date_effectif | date | Date effective du changement |

### Table `position_assignments`

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | Identifiant unique |
| position_id | bigint | Poste concerné |
| user_id | bigint | Utilisateur affecté |
| status | string | ACTIVE, VACANT, PENDING |
| date_debut | date | Date de début d'affectation |
| date_fin | date | Date de fin d'affectation |
| notes | text | Notes |
| assigned_by | bigint | Affecté par |

### Table `reaffectation_requests`

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | Identifiant unique |
| organization_member_id | bigint | Membre à réaffecter |
| current_position_id | bigint | Poste actuel |
| new_position_id | bigint | Nouveau poste souhaité |
| requested_by | bigint | Demandé par |
| status | string | PENDING, APPROVED, REJECTED |
| motif | text | Motif de la demande |
| date_souhaite | date | Date souhaitée |
| approved_by | bigint | Approuvé par |
| approved_at | timestamp | Date d'approbation |
| commentaire_approbation | text | Commentaire d'approbation |

## 🔗 Routes disponibles

### Interface utilisateur

| Route | Description |
|-------|-------------|
| `/organigramme/members` | Liste des membres |
| `/organigramme/members/{id}` | Détail d'un membre |
| `/organigramme/members-vacant` | Postes vacants |
| `/organigramme/members-history` | Historique des changements |

### Actions

| Route | Méthode | Description |
|-------|---------|-------------|
| `/organigramme/positions/{id}/assign` | GET | Formulaire d'affectation |
| `/organigramme/positions/{id}/assign` | POST | Affecter un utilisateur |
| `/organigramme/members/{id}/demission` | POST | Démission |
| `/organigramme/members/{id}/licenciement` | POST | Licenciement |
| `/organigramme/members/{id}/retraite` | POST | Retraite |
| `/organigramme/members/{id}/reaffectation` | POST | Demande de réaffectation |
| `/organigramme/reaffectation/{id}/approve` | POST | Approuver une réaffectation |
| `/organigramme/reaffectation/{id}/reject` | POST | Rejeter une réaffectation |

## 💻 Utilisation dans le code

### Affecter un utilisateur à un poste

```php
use App\Models\OrganizationMember;
use App\Models\Position;
use App\Models\User;

$position = Position::find(1);
$user = User::find(5);

// Créer un nouveau membre
$member = OrganizationMember::create([
    'position_id' => $position->id,
    'user_id' => $user->id,
    'name' => $user->name,
    'email' => $user->email,
    'status' => OrganizationMember::STATUS_ACTIVE,
    'start_date' => now(),
]);

// Ou affecter un utilisateur à un poste vacant
$member->assignUser($user, "Nouvel employé", auth()->id());
```

### Gérer une démission

```php
$member = OrganizationMember::find(1);
$member->markAsDemission("Démission pour raisons personnelles", auth()->id());
// Le poste devient automatiquement VACANT
```

### Gérer un licenciement

```php
$member = OrganizationMember::find(1);
$member->markAsLicencie("Licenciement pour faute grave", auth()->id());
```

### Gérer un départ en retraite

```php
$member = OrganizationMember::find(1);
$member->markAsRetraite("Départ en retraite à 65 ans", auth()->id());
```

### Réaffecter à un nouveau poste

```php
$member = OrganizationMember::find(1);
$newPosition = Position::find(5);

$member->reaffectToPosition(
    $newPosition,
    HistoriqueStatutMembre::MOTIF_MUTATION,
    "Mutation vers le département commercial",
    auth()->id()
);
```

### Obtenir tous les postes vacants

```php
$vacantPositions = OrganizationMember::getVacantPositions();

foreach ($vacantPositions as $vacant) {
    echo $vacant->position->title . " - " . $vacant->position->department->name;
}
```

### Vérifier si un utilisateur a déjà un poste

```php
$user = User::find(1);
$activeAssignment = OrganizationMember::getActiveAssignmentForUser($user);

if ($activeAssignment) {
    echo "Utilisateur déjà affecté au poste: " . $activeAssignment->position->title;
}
```

## 🎨 Statuts disponibles

| Statut | Description |
|--------|-------------|
| `ACTIVE` | Poste occupé et actif |
| `VACANT` | Poste vacant |
| `INTERIM` | Poste occupé en intérim |
| `DEMISSION` | Démission |
| `LICENCIE` | Licencié |
| `RETRAITE` | Retraite |

## 📝 Motifs de changement

| Motif | Description |
|-------|-------------|
| `EMBAUCHE` | Nouvelle embauche |
| `DEMISSION` | Démission |
| `LICENCIEMENT` | Licenciement |
| `RETRAITE` | Départ en retraite |
| `MUTATION` | Mutation interne |
| `PROMOTION` | Promotion |
| `REAFFECTATION` | Réaffectation |
| `RETOUR_CONGE` | Retour de congé |
| `INTERIM` | Intérim |

## 🔒 Permissions

Les permissions suivantes sont créées et attribuées aux rôles administrateur et RH:

- `voir_membres_organigramme` - Voir les membres
- `modifier_membres_organigramme` - Modifier les membres
- `affecter_membres_organigramme` - Affecter des utilisateurs
- `licencier_membres_organigramme` - Licencier des membres
- `voir_historique_membres` - Voir l'historique

### Vérifier les permissions

```php
if (auth()->user()->can('affecter_membres_organigramme')) {
    // Autoriser l'affectation
}
```

## 📈 Exemples d'utilisation

### Scénario 1: Nouvelle embauche

```php
// 1. Créer l'utilisateur
$user = User::create([
    'name' => 'Jean Dupont',
    'email' => 'jean.dupont@mgs.mg',
    'password' => Hash::make('password'),
    'poste' => 'Commercial Senior',
]);

// 2. L'affecter à un poste
$position = Position::where('title', 'Commercial Senior')->first();
$member = OrganizationMember::create([
    'position_id' => $position->id,
    'user_id' => $user->id,
    'name' => $user->name,
    'email' => $user->email,
    'status' => OrganizationMember::STATUS_ACTIVE,
    'start_date' => now(),
]);
```

### Scénario 2: Démission avec réaffectation

```php
// 1. Marquer la démission
$member = OrganizationMember::find(1);
$member->markAsDemission("Démission pour nouvelle opportunité", auth()->id());

// 2. Affecter un nouveau membre au poste devenu vacant
$newUser = User::find(10);
$vacantMember = OrganizationMember::where('position_id', $member->position_id)
    ->where('status', OrganizationMember::STATUS_VACANT)
    ->first();

if ($vacantMember) {
    $vacantMember->assignUser($newUser, "Remplacement suite à démission", auth()->id());
}
```

### Scénario 3: Promotion interne

```php
// 1. Créer une demande de réaffectation
$member = OrganizationMember::find(1);
$newPosition = Position::where('title', 'Directeur Commercial')->first();

$request = ReaffectationRequest::create([
    'organization_member_id' => $member->id,
    'current_position_id' => $member->position_id,
    'new_position_id' => $newPosition->id,
    'requested_by' => auth()->id(),
    'motif' => 'Promotion suite à excellent travail',
    'status' => ReaffectationRequest::STATUS_PENDING,
]);

// 2. Approuver la demande
$request->approve(auth()->user(), "Promotion approuvée par la direction");
```

## 🐛 Dépannage

### Les routes ne sont pas trouvées

```bash
php artisan route:clear
php artisan route:cache
```

### Les vues ne s'affichent pas

```bash
php artisan view:clear
php artisan cache:clear
```

### Erreur de permission

Vérifier que l'utilisateur a bien les permissions nécessaires:

```php
$user = auth()->user();
dd($user->getAllPermissions());
```

## 📚 Ressources

- **Modèles**: `app/Models/OrganizationMember.php`, `app/Models/HistoriqueStatutMembre.php`
- **Contrôleur**: `app/Http/Controllers/MemberStatusController.php`
- **Vues**: `resources/views/organigramme/members/`
- **Routes**: `routes/web.php` (section organigramme)
- **Migrations**: `database/migrations/2024_12_09_000001_create_historique_statut_membres_table.php`

## ✅ Checklist de mise en production

- [ ] Exécuter les migrations
- [ ] Créer les permissions
- [ ] Assigner les permissions aux rôles appropriés
- [ ] Tester l'affectation d'un utilisateur
- [ ] Tester une démission
- [ ] Tester un licenciement
- [ ] Tester une réaffectation
- [ ] Vérifier l'historique
- [ ] Vérifier les postes vacants

## 🎉 Conclusion

Le système de gestion des postes et membres permet une gestion complète et traçable de l'organigramme avec:

- ✅ Affectation d'utilisateurs aux postes
- ✅ Gestion des départs (démission, licenciement, retraite)
- ✅ Réaffectations internes
- ✅ Suivi des postes vacants
- ✅ Historique complet et traçable
- ✅ Interface intuitive
- ✅ Permissions granulaires

Le système est maintenant prêt à l'emploi!
