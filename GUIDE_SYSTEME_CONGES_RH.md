# Système de Gestion des Congés et du Personnel

## 📋 Vue d'ensemble

Ce système complet de gestion RH intègre :
- **Gestion des congés** : demandes, validation, suivi des soldes
- **Gestion des absences** : retards, télétravail, missions, etc.
- **Gestion des documents** : contrats, bulletins de paie, attestations, etc.
- **Gestion du personnel** : changement de statut, licenciement, historique

## 🎯 Fonctionnalités Principales

### Pour les Employés
- ✅ Demander des congés (annuels, maladie, maternité, etc.)
- ✅ Consulter son solde de congés
- ✅ Déclarer des absences
- ✅ Accéder à ses documents RH
- ✅ Demander des documents administratifs

### Pour le RH
- ✅ Voir toutes les demandes de congés/absences
- ✅ Approuver ou refuser les demandes
- ✅ Gérer les documents des employés
- ✅ Modifier le statut des employés (licenciement, démission, etc.)
- ✅ Consulter l'historique des changements
- ✅ Générer automatiquement les documents de fin de contrat

### Pour la Direction
- ✅ Vue globale sur les congés et absences
- ✅ Valider les demandes importantes
- ✅ Accès aux documents du personnel

## 🗂️ Structure des Tables

### `conges`
- Stockage des demandes de congés
- Types : congé annuel, maladie, maternité, paternité, sans solde, permission
- Statuts : en attente, approuvé, refusé, annulé
- Lié à `organization_members`

### `demandes_absence`
- Déclarations d'absence (justifiée, non justifiée, retard, sortie anticipée)
- Types spéciaux : télétravail, mission externe, formation
- Validation par RH/Direction

### `documents_employe`
- **Documents en cours d'emploi** :
  - Contrat de travail, avenants
  - Bulletins de paie
  - Attestations (travail, salaire, emploi)
  - États (congés, heures supplémentaires)
  - Documents disciplinaires
  
- **Documents de fin de contrat** :
  - Certificat de travail
  - Solde de tout compte
  - Attestations CNAPS, OSTIE (Madagascar)
  - Lettre de licenciement
  - Certificat de non-dettes
  - Attestation de remise du matériel

### `solde_conges`
- Suivi du solde de congés par employé
- Congés totaux, pris, restants
- Réinitialisation annuelle automatique

### `historique_statuts_membres`
- Traçabilité de tous les changements de statut
- Motifs : embauche, promotion, mutation, licenciement, démission, retraite
- Audit complet avec date et auteur

## 🔐 Permissions et Accès

### Employé Standard
- Accès : Ses propres congés, absences et documents accessibles
- Actions : Créer des demandes, annuler en attente

### RH (Ressources Humaines)
- Accès : Toutes les données du personnel
- Actions : Validation, création de documents, changement de statut

### Direction
- Accès : Vue globale sur congés et absences
- Actions : Validation des demandes importantes

## 🚀 Installation et Migration

### 1. Exécuter les migrations
```bash
cd /var/www/administration
php artisan migrate
```

### 2. Créer les rôles nécessaires
```bash
php artisan tinker
```
```php
use Spatie\Permission\Models\Role;

// Créer les rôles si inexistants
Role::firstOrCreate(['name' => 'RH']);
Role::firstOrCreate(['name' => 'Ressources Humaines']);
Role::firstOrCreate(['name' => 'Direction']);
Role::firstOrCreate(['name' => 'Admin']);
```

### 3. Assigner les rôles aux utilisateurs
```php
$user = \App\Models\User::where('email', 'rh@example.com')->first();
$user->assignRole('RH');
```

## 📍 Routes Disponibles

### Congés
- `GET /conges` - Liste des congés
- `GET /conges/create` - Formulaire de demande
- `POST /conges` - Créer une demande
- `GET /conges/{id}` - Détails d'une demande
- `POST /conges/{id}/approve` - Approuver (RH/Direction)
- `POST /conges/{id}/reject` - Refuser (RH/Direction)
- `DELETE /conges/{id}` - Annuler (Employé)

### Absences
- `GET /absences` - Liste des absences
- `GET /absences/create` - Formulaire de déclaration
- `POST /absences` - Créer une déclaration
- `GET /absences/{id}` - Détails
- `POST /absences/{id}/approve` - Approuver
- `POST /absences/{id}/reject` - Refuser

### Documents
- `GET /documents` - Liste des documents
- `GET /documents/create` - Ajouter un document (RH)
- `POST /documents` - Créer un document (RH)
- `GET /documents/{id}` - Voir le document
- `GET /documents/{id}/download` - Télécharger
- `POST /documents/request` - Demander un document (Employé)

### Personnel (RH uniquement)
- `GET /personnel` - Liste du personnel
- `GET /personnel/{id}` - Profil complet
- `GET /personnel/{id}/change-status` - Changer le statut
- `POST /personnel/{id}/change-status` - Effectuer le changement
- `GET /personnel/{id}/historique` - Historique des changements

## 🔄 Logique de Licenciement

Lorsqu'un employé est marqué comme **LICENCIE** :

1. ✅ Le statut de l'employé passe à `LICENCIE`
2. ✅ La date de fin (`end_date`) est enregistrée
3. ✅ Le poste devient **VACANT** automatiquement
4. ✅ L'historique est enregistré avec le motif
5. ✅ Les documents obligatoires sont créés :
   - Certificat de travail
   - Attestation de fin de contrat
   - Solde de tout compte
   - Relevé des droits de congés
   - Attestations CNAPS/OSTIE

## 📊 Intégration à l'Organigramme

Toutes les fonctionnalités sont **liées à l'organigramme** via `organization_members` :
- Les demandes sont liées au poste de l'employé
- Les documents sont associés au membre
- Le changement de statut met à jour l'organigramme
- Les postes vacants sont visibles dans l'organigramme

## 🎨 Navigation

Un nouveau menu **RH** a été ajouté dans la barre de navigation avec :
- Congés
- Absences
- Documents
- Gestion Personnel (pour RH uniquement)

## 📝 Types de Documents Disponibles

### En cours d'emploi
- Contrat de travail et avenants
- Fiche de poste
- Bulletins de paie
- Attestations (travail, salaire, emploi)
- États des congés et heures sup
- Documents disciplinaires

### Fin de contrat
- Certificat de travail
- Solde de tout compte
- Attestations officielles (CNAPS, OSTIE)
- Lettre de licenciement/recommandation

### Autres
- Justificatifs de remboursement
- Attestations de versement
- Certificats divers

## 🔧 Maintenance

### Réinitialisation annuelle des congés
```php
// À exécuter en début d'année
$soldes = \App\Models\SoldeConge::all();
foreach ($soldes as $solde) {
    $solde->resetForNewYear(2026);
}
```

### Archiver les anciens documents
```php
// Archiver les documents de plus de 5 ans
\App\Models\DocumentEmploye::where('date_emission', '<', now()->subYears(5))
    ->update(['statut' => 'archive']);
```

## ✅ Checklist de Déploiement

- [ ] Exécuter les migrations
- [ ] Créer les rôles RH et Direction
- [ ] Assigner les rôles aux utilisateurs appropriés
- [ ] Configurer le stockage pour les fichiers (storage/app/public)
- [ ] Créer le lien symbolique : `php artisan storage:link`
- [ ] Tester les permissions d'accès
- [ ] Vérifier l'upload de fichiers

## 🆘 Support

En cas de problème :
1. Vérifier les logs : `storage/logs/laravel.log`
2. Vérifier les permissions des dossiers storage et bootstrap/cache
3. S'assurer que les rôles sont bien assignés
4. Vérifier que le lien symbolique storage existe

---

**Développé pour l'application Administration - Système RH Complet**
