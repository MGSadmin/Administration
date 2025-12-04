# Workflow Demande de Fourniture → Patrimoine

## Résumé des Changements

### 1. Tables Créées

**Administration (mgs_administration):**
- `fournisseurs` - Liste des fournisseurs avec autocomplete
- Colonnes ajoutées à `demandes_fourniture`:
  - `designation` (required) - Désignation précise du matériel
  - `fournisseur_id` - Référence au fournisseur
  - `patrimoine_id` - ID du patrimoine créé
  - `patrimoine_cree` - Boolean pour tracer la création

**Gestion-Dossier (gestion_dossiers):**
- `patrimoines` - Table des patrimoines avec statuts

### 2. Workflow Automatisé

```
1. Création Demande Fourniture (Administration)
   ↓
   - Statut: en_attente
   - Champ "designation" OBLIGATOIRE
   - Notification au demandeur + personne désignée

2. Validation par Direction/RH/Admin
   ↓
   - Statut: validee
   - Création AUTOMATIQUE d'un patrimoine dans gestion-dossier:
     * designation = designation de la demande
     * type_fourniture = type de la demande
     * etat = "neuf" (par défaut)
     * statut = "en_attente_achat"
     * validateur_id + validateur_nom
     * numero_demande (référence)
   - Notification au COMPTABLE pour achat
   - Notification au demandeur

3. Achat par Comptable (dans gestion-dossier)
   ↓
   - Remplir les informations d'achat:
     * fournisseur_id + fournisseur_nom
     * prix_achat
     * date_achat
     * bon_commande
     * facture
   - Statut patrimoine: en_attente_achat → en_utilisation
   - Calcul automatique de la garantie

4. Utilisation
   ↓
   - Le patrimoine est maintenant actif
   - Peut être attribué à un utilisateur
   - Traçabilité complète
```

### 3. Statuts du Patrimoine

- **en_attente_achat** - Créé après validation de la demande, attend l'achat
- **en_utilisation** - Acheté et en service
- **disponible** - Disponible pour attribution
- **en_maintenance** - En réparation
- **hors_service** - Hors d'usage

### 4. États du Patrimoine

- **neuf** - Toujours "neuf" lors de la création depuis une demande validée
- **bon** - Bon état général
- **moyen** - État moyen
- **mauvais** - Mauvais état
- **hors_service** - Inutilisable

### 5. Champs Obligatoires

**Création Demande:**
- Objet ✅
- **Designation** ✅ (NOUVEAU - requis pour validation)
- Description ✅
- Type fourniture ✅
- Quantité ✅
- Priorité ✅

**Validation:**
- Aucun champ supplémentaire
- Création automatique du patrimoine

**Achat (après validation):**
- Les informations d'achat NE SONT PAS remplies automatiquement
- Le comptable doit les saisir manuellement dans gestion-dossier

### 6. Notifications

| Événement | Qui est notifié |
|-----------|----------------|
| Demande créée | Demandeur + Personne désignée |
| Demande validée | Demandeur + Personne désignée + **COMPTABLE** |
| Demande rejetée | Demandeur + Personne désignée |
| Achat effectué | À implémenter dans gestion-dossier |

### 7. Autocomplete Fournisseurs

5 fournisseurs créés par défaut:
1. Comp@gnie Informatique (local) - Matériel informatique
2. Bureau Plus (local) - Fournitures de bureau
3. TechnoMad (local) - Électronique, IT
4. MobilPro Madagascar (local) - Mobilier
5. Dell Technologies (international) - Ordinateurs, serveurs

### 8. Améliorations Appliquées

✅ APP_URL corrigé: `http://administration.mgs.mg`
✅ Notifications pointent vers le bon domaine
✅ Refresh du statut avant suppression
✅ Table fournisseurs avec autocomplete
✅ Champ designation obligatoire
✅ Création automatique patrimoine après validation
✅ Notification comptable pour achat
✅ État "neuf" par défaut
✅ Statut "en_attente_achat" initial

### 9. Prochaines Étapes

**Dans gestion-dossier:**
1. Créer un controller PatrimoineController
2. Créer des vues pour:
   - Liste des patrimoines en attente d'achat
   - Formulaire de saisie des informations d'achat
   - Liste des patrimoines en utilisation
3. Ajouter autocomplete pour les fournisseurs
4. Implémenter les notifications d'achat

**Dans administration:**
1. Ajouter autocomplete pour les fournisseurs dans le formulaire demande
2. Créer un CRUD pour gérer les fournisseurs
3. Afficher le lien vers le patrimoine créé dans la vue demande

### 10. Base de Données

**Connexions:**
- Administration: `mgs_administration`
- Gestion-Dossier: `gestion_dossiers`

**Communication inter-bases:**
- Les ID sont stockés sans clé étrangère (cross-database)
- Les noms sont dupliqués pour éviter les jointures complexes
- La méthode `creerPatrimoineGestionDossier()` gère la connexion temporaire

### 11. Test du Workflow

```bash
# 1. Créer une demande de fourniture
# - Aller sur http://administration.mgs.mg/demandes-fourniture/create
# - Remplir TOUS les champs incluant "designation"
# - Soumettre

# 2. Valider la demande (en tant que Direction/Admin)
# - Aller sur la demande
# - Cliquer sur "Valider"
# - Vérifier que:
#   * Un patrimoine est créé dans gestion_dossiers.patrimoines
#   * Le comptable reçoit une notification
#   * Le statut du patrimoine est "en_attente_achat"
#   * L'état du patrimoine est "neuf"

# 3. Vérifier le patrimoine créé
mysql -u andry -p'AndryIT@123' gestion_dossiers -e "
  SELECT code_materiel, designation, etat, statut, numero_demande 
  FROM patrimoines 
  ORDER BY created_at DESC 
  LIMIT 5;
"
```

---

*Date: 20/11/2025*
*Système: Laravel 12.39.0 - Multi-database*
