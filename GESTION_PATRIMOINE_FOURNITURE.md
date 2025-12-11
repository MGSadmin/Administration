# Gestion des Patrimoines et Demandes de Fourniture

## Vue d'ensemble

Le système de gestion administrative inclut maintenant deux modules complets :

### 1. Gestion des Patrimoines

Module complet pour gérer tous les biens matériels de la société.

#### Fonctionnalités
- **Catalogage complet** : Enregistrement détaillé de chaque bien (matériel informatique, mobilier, véhicules, équipements)
- **Traçabilité** :
  - Qui utilise le matériel
  - Date d'achat
  - Validateur de l'achat
  - Historique d'attribution
- **Gestion de l'état** : Suivi de l'état physique (neuf, bon, moyen, mauvais, en réparation, hors service)
- **Gestion du statut** : Disponible, en utilisation, en maintenance, réformé
- **Garanties** : Suivi automatique des garanties avec calcul de la date d'expiration
- **Code automatique** : Génération automatique du code matériel selon la catégorie

#### Informations enregistrées
- Code matériel (auto-généré)
- Désignation, description
- Catégorie (informatique, mobilier, véhicule, équipement bureau, autres)
- Marque, modèle, numéro de série
- Prix et date d'achat
- Fournisseur et facture
- Validateur de l'achat et date de validation
- Utilisateur actuel et date d'attribution
- État et statut
- Localisation
- Garantie (durée et date de fin)
- Observations

#### Routes principales
- `/patrimoines` - Liste des patrimoines
- `/patrimoines/create` - Créer un patrimoine
- `/patrimoines/{id}` - Voir un patrimoine
- `/patrimoines/{id}/edit` - Modifier un patrimoine
- `/patrimoines-statistiques` - Statistiques

### 2. Demandes de Fourniture

Système complet de gestion des demandes d'achat avec workflow de validation et notifications automatiques.

#### Workflow
1. **Création** : Un utilisateur crée une demande
2. **Validation** : Un administrateur valide ou rejette
3. **Commande** : Si validée, achat auprès du fournisseur
4. **Réception** : Marquage de la réception
5. **Livraison** : Livraison au demandeur

#### Notifications automatiques
- À chaque changement de statut, notifications envoyées automatiquement :
  - Au demandeur
  - À la personne désignée dans la demande (si spécifiée)
  - Au validateur (selon le cas)
- Notifications par email et dans l'application

#### Informations enregistrées
- Numéro de demande (auto-généré)
- Demandeur
- Objet et description détaillée
- Type de fourniture
- Quantité
- Priorité (faible, normale, urgente)
- Justification
- Budget estimé
- Statut du workflow
- Validateur et date de validation
- Motif de rejet (si applicable)
- Informations d'achat (fournisseur, montant, bon de commande, facture)
- Dates (commande, réception, livraison)
- Personne à notifier automatiquement

#### Routes principales
- `/demandes-fourniture` - Liste des demandes
- `/demandes-fourniture/create` - Créer une demande
- `/demandes-fourniture/{id}` - Voir une demande
- `/demandes-fourniture/{id}/edit` - Modifier une demande
- `/demandes-fourniture/{id}/valider` - Valider
- `/demandes-fourniture/{id}/rejeter` - Rejeter
- `/demandes-fourniture/{id}/commander` - Commander
- `/demandes-fourniture/{id}/marquer-recue` - Marquer comme reçue
- `/demandes-fourniture/{id}/livrer` - Livrer
- `/demandes-fourniture-statistiques` - Statistiques

## Accès aux modules

Les deux modules sont accessibles depuis le menu latéral de l'application d'administration :

**Section PATRIMOINE**
- Gestion Patrimoine
- Demandes Fourniture

## Permissions

Les utilisateurs avec le rôle "admin" ont accès à toutes les fonctionnalités.
Les autres utilisateurs peuvent :
- Voir leurs propres demandes de fourniture
- Créer de nouvelles demandes
- Modifier leurs demandes en attente

## Corrections apportées

### Page Profile
- **Problème** : La page `/profile` utilisait un layout inexistant (`<x-app-layout>`)
- **Solution** : Conversion pour utiliser le layout `admin.blade.php` avec le menu et la navigation
- Maintenant le menu s'affiche correctement sur la page de profil

## Base de données

### Tables créées
- `patrimoines` : Stockage des biens matériels
- `demandes_fourniture` : Stockage des demandes avec workflow

### Migrations
Les migrations ont été exécutées avec succès :
- `2025_11_20_000001_create_patrimoines_table.php`
- `2025_11_20_000002_create_demandes_fourniture_table.php`

## Prochaines étapes recommandées

1. **Permissions avancées** : Créer des permissions spécifiques pour :
   - `gerer_patrimoine`
   - `valider_patrimoine`
   - `valider_demande_fourniture`
   - `commander_fourniture`

2. **Export** : Ajouter la possibilité d'exporter les listes en Excel

3. **Photos** : Permettre l'ajout de photos pour les patrimoines

4. **Historique** : Ajouter un historique complet des changements

5. **Dashboard** : Intégrer des widgets sur le dashboard avec statistiques clés

6. **Rapports** : Générer des rapports PDF pour inventaires et demandes

## Support

Pour toute question ou problème, contactez l'équipe de développement.
