# Guide de l'Organigramme TLT

## Vue d'ensemble

Système interactif de gestion de l'organigramme de la société TLT avec fonctionnalités complètes CRUD (Créer, Lire, Modifier, Supprimer).

## Accès

- **URL**: `http://votre-domaine/organigramme/company`
- **Ancien organigramme SSO**: `http://votre-domaine/organigramme`

## Fonctionnalités

### 1. Mode Consultation (par défaut)
- Visualisation de l'organigramme complet
- Navigation interactive
- Zoom et pan
- Recherche par nom ou poste
- Filtrage par département ou statut
- Export PDF

### 2. Mode Édition
Cliquer sur le bouton "Mode Édition" pour activer :
- Drag & drop pour réorganiser la hiérarchie
- Boutons d'édition et suppression sur chaque nœud
- Ajout de nouveaux éléments

### 3. Gestion des Départements
**Créer un département** :
- Cliquer sur "Ajouter Département"
- Remplir : Nom, Code, Description, Couleur
- Choisir un département parent (optionnel)

**Modifier/Supprimer** :
- En mode édition, utiliser les boutons sur les nœuds

### 4. Gestion des Postes
**Créer un poste** :
- Cliquer sur "Ajouter Poste"
- Remplir : Titre, Département, Poste parent (hiérarchie)
- Ajouter description et responsabilités

**Responsabilités** : Chaque ligne = une responsabilité

### 5. Gestion des Membres
**Affecter un membre à un poste** :
- Cliquer sur "Ajouter Membre"
- Sélectionner le poste
- Remplir : Nom, Statut, Email, Téléphone
- Dates de début/fin (optionnel)

**Statuts disponibles** :
- `ACTIVE` : Employé actif (vert)
- `VACANT` : Poste vacant (rouge)
- `INTERIM` : Employé en intérim (jaune)

### 6. Recherche et Filtres
- **Recherche** : Tapez un nom ou titre de poste
- **Filtrer par département** : DG, Commercial, Opérations, DAF
- **Filtrer par statut** : Actif, Vacant, Intérim

### 7. Export PDF
Cliquer sur "Exporter PDF" pour générer un document imprimable de l'organigramme.

## Structure Actuelle (TLT)

### Direction Générale
- Mme Prisca (DG)

### Direction Commerciale
- Steffy (Directeur Commercial)
  - Aina (Sales 1)
  - VACANT (Sales 2)
  - VACANT (Sales 3)

### Direction des Opérations
- VACANT (Directeur des Opérations)
  - Justin (Service Logistique)
  - Claude (Coordination)
  - Tina, Lala, Sandra (Documentation)
  - José/Mparany (Douane)

### Direction Administrative et Financière
- Haja (DAF)
  - Iarinah (Comptabilité)
    - Zara (O2C)
    - Nary (P2P)
  - Zinà/Mparany (Finance)
  - RH & Affaires Généraux
    - Andry (Informatique)
    - Zara (Gestion Personnel)

## API Endpoints

### Récupérer les données
```
GET /organigramme/data
```

### Départements
```
POST   /organigramme/departments
PUT    /organigramme/departments/{id}
DELETE /organigramme/departments/{id}
```

### Postes
```
POST   /organigramme/positions
PUT    /organigramme/positions/{id}
DELETE /organigramme/positions/{id}
```

### Membres
```
POST   /organigramme/members
PUT    /organigramme/members/{id}
DELETE /organigramme/members/{id}
```

### Mise à jour hiérarchie
```
POST /organigramme/hierarchy
{
  "position_id": 1,
  "parent_position_id": 2
}
```

## Base de données

### Tables créées
- `departments` : Départements et sous-départements
- `positions` : Postes dans l'organisation
- `organization_members` : Employés affectés aux postes

### Relations
- Un département peut avoir plusieurs sous-départements
- Un poste appartient à un département
- Un poste peut avoir plusieurs sous-postes (hiérarchie)
- Un membre est affecté à un poste

## Personnalisation

### Changer les couleurs des départements
Éditer un département et modifier le champ "Couleur" (format hex: #RRGGBB)

### Réorganiser la hiérarchie
En mode édition, drag & drop les nœuds ou modifier le "poste parent"

### Ajouter des informations
Chaque poste peut avoir :
- Description
- Liste de responsabilités
- Multiples membres (historique)

## Développement futur

Fonctionnalités possibles à ajouter :
- Upload de photos des employés
- Organigramme par projet
- Export en différents formats (PNG, SVG, Excel)
- Historique des changements
- Notifications de changements
- Intégration avec le système RH
- Vue par compétences
- Génération automatique de fiches de poste

## Support

Pour toute question ou problème, contacter l'équipe IT.
