# Documentation du Système de Contrôle d'Accès Basé sur les Rôles (RBAC)

## Vue d'ensemble

Le système RBAC de l'application Administration utilise **Spatie Laravel-Permission** pour gérer 6 rôles organisationnels avec 65 permissions spécifiques réparties sur 3 applications (Administration, Gestion-Dossier, Commercial).

---

## Structure des Rôles

### 1. **Administrateur**
**Accès:** Complet à toutes les fonctionnalités  
**Permissions:** Toutes les 65 permissions

**Responsabilités:**
- Gestion complète des utilisateurs et rôles
- Administration du patrimoine et des demandes de fourniture
- Accès total aux applications Commercial et Gestion-Dossier
- Supervision générale du système

---

### 2. **Direction**
**Accès:** Validation des fournitures + Lecture seule (sauf situations)  
**Permissions:** 18 permissions

**Fonctionnalités autorisées:**

**Administration:**
- ✅ `voir_utilisateurs` - Consultation de la liste des utilisateurs
- ✅ `voir_roles` - Consultation des rôles
- ✅ `voir_patrimoine` - Consultation du patrimoine
- ✅ `voir_statistiques_patrimoine` - Tableaux de bord patrimoine

**Demandes de Fourniture:**
- ✅ `voir_toutes_demandes_fourniture` - Voir toutes les demandes (pas seulement les siennes)
- ✅ `valider_demande_fourniture` - Approuver les demandes
- ✅ `rejeter_demande_fourniture` - Rejeter les demandes

**Gestion-Dossier:**
- ✅ `voir_dossiers` - Consultation des dossiers
- ✅ `modifier_dossiers` - Modification pour les situations uniquement
- ✅ `voir_cotations`, `voir_reglements_debours`, `voir_reglements_clients`, `voir_factures` - Lecture des documents

**Commercial:**
- ✅ `voir_prospects`, `voir_clients`, `voir_devis`, `voir_contrats` - Lecture seule

---

### 3. **Employé Commercial**
**Accès:** Application Commercial complète + Cotations Gestion-Dossier  
**Permissions:** 22 permissions

**Fonctionnalités autorisées:**

**Commercial (Accès complet):**
- ✅ CRUD complet sur: `prospects`, `clients`, `devis`, `contrats`
- 16 permissions: voir/créer/modifier/supprimer pour chaque module

**Gestion-Dossier:**
- ✅ `voir_dossiers` - Consultation
- ✅ CRUD complet sur `cotations` - 4 permissions

**Demandes de Fourniture:**
- ✅ `voir_mes_demandes_fourniture` - Voir uniquement ses demandes
- ✅ `creer_demande_fourniture`, `modifier_demande_fourniture` - Gérer ses demandes

---

### 4. **RH (Ressources Humaines)**
**Accès:** Gestion utilisateurs + Patrimoine + Inventaire complets + Lecture autres modules  
**Permissions:** 29 permissions

**Fonctionnalités autorisées:**

**Gestion Utilisateurs (Complet):**
- ✅ CRUD complet: `voir_utilisateurs`, `creer_utilisateurs`, `modifier_utilisateurs`, `supprimer_utilisateurs`
- ✅ `voir_roles` - Consultation des rôles

**Patrimoine & Inventaire (Complet):**
- ✅ CRUD complet sur patrimoine: 7 permissions incluant `attribuer_patrimoine`
- ✅ CRUD complet sur inventaire: 4 permissions
- ✅ `voir_statistiques_patrimoine`

**Demandes de Fourniture:**
- ✅ `voir_toutes_demandes_fourniture` - Voir toutes les demandes
- ✅ `creer_demande_fourniture`, `modifier_demande_fourniture` - Gérer les demandes

**Commercial (Lecture seule):**
- ✅ `voir_prospects`, `voir_clients`, `voir_devis`, `voir_contrats`

**Gestion-Dossier (Lecture seule):**
- ✅ `voir_dossiers`, `voir_cotations`, `voir_reglements_debours`, `voir_reglements_clients`, `voir_factures`

---

### 5. **Sales**
**Accès:** Création et modification de dossiers  
**Permissions:** 8 permissions

**Fonctionnalités autorisées:**

**Gestion-Dossier:**
- ✅ `voir_dossiers`, `creer_dossiers`, `modifier_dossiers` - Gestion des dossiers
- ✅ `voir_cotations` - Consultation

**Commercial (Lecture limitée):**
- ✅ `voir_prospects`, `voir_clients` - Consultation pour référence

**Demandes de Fourniture:**
- ✅ `voir_mes_demandes_fourniture` - Voir uniquement ses demandes
- ✅ `creer_demande_fourniture`, `modifier_demande_fourniture` - Gérer ses demandes

---

### 6. **Comptable**
**Accès:** Règlements et factures complets  
**Permissions:** 17 permissions

**Fonctionnalités autorisées:**

**Règlements Débours (Complet):**
- ✅ CRUD complet: 5 permissions incluant `valider_reglements_debours`

**Règlements Clients (Complet):**
- ✅ CRUD complet: 4 permissions

**Factures (Complet):**
- ✅ CRUD complet: 4 permissions

**Gestion-Dossier (Lecture):**
- ✅ `voir_dossiers`, `voir_cotations` - Consultation

**Demandes de Fourniture:**
- ✅ `voir_mes_demandes_fourniture` - Voir uniquement ses demandes
- ✅ `creer_demande_fourniture`, `modifier_demande_fourniture` - Gérer ses demandes

---

## Règles Spéciales

### Demandes de Fourniture - Visibilité

**Qui voit TOUTES les demandes:**
- ✅ Administrateur (permission: `voir_toutes_demandes_fourniture`)
- ✅ Direction (permission: `voir_toutes_demandes_fourniture`)
- ✅ RH (permission: `voir_toutes_demandes_fourniture`)

**Qui voit UNIQUEMENT SES demandes:**
- ❌ Employé Commercial (permission: `voir_mes_demandes_fourniture`)
- ❌ Sales (permission: `voir_mes_demandes_fourniture`)
- ❌ Comptable (permission: `voir_mes_demandes_fourniture`)

### Workflow Demandes de Fourniture

| Étape | Statut | Qui peut faire l'action | Permission requise |
|-------|--------|------------------------|-------------------|
| 1. Créer | - | Tous les utilisateurs | `creer_demande_fourniture` |
| 2. Valider | en_attente | Direction, RH, Admin | `valider_demande_fourniture` |
| 3. Rejeter | en_attente | Direction, RH, Admin | `rejeter_demande_fourniture` |
| 4. Commander | validee | RH, Admin | `commander_fourniture` |
| 5. Réceptionner | commandee | RH, Admin | Rôle: `rh`, `admin`, `administrateur` |
| 6. Livrer | recue | RH, Admin | `livrer_fourniture` |

---

## Utilisation dans le Code

### Dans les Controllers

#### Vérification de permission spécifique:
```php
if (!Auth::user()->can('valider_demande_fourniture')) {
    abort(403, 'Non autorisé');
}
```

#### Vérification de rôle multiple:
```php
if (!Auth::user()->hasAnyRole(['administrateur', 'admin', 'direction', 'rh'])) {
    abort(403, 'Non autorisé');
}
```

#### Filtrage par permission dans le controller:
```php
public function index()
{
    if (Auth::user()->can('voir_toutes_demandes_fourniture')) {
        $demandes = DemandeFourniture::with(['demandeur', 'validateur'])->get();
    } else {
        $demandes = DemandeFourniture::where('demandeur_id', Auth::id())->get();
    }
}
```

### Dans les Views Blade

#### Affichage conditionnel par permission:
```blade
@can('creer_patrimoine')
    <a href="{{ route('patrimoines.create') }}" class="btn btn-primary">
        Ajouter un Patrimoine
    </a>
@endcan
```

#### Affichage conditionnel par rôle:
```blade
@if(Auth::user()->hasAnyRole(['administrateur', 'admin', 'rh']))
    <button>Action réservée RH/Admin</button>
@endif
```

#### Boutons d'action workflow:
```blade
@if($demande->statut == 'en_attente' && Auth::user()->can('valider_demande_fourniture'))
    <button data-bs-toggle="modal" data-bs-target="#validerModal">Valider</button>
@endif
```

---

## Liste Complète des 65 Permissions

### Administration (21 permissions)

**Utilisateurs (4):**
- `voir_utilisateurs`
- `creer_utilisateurs`
- `modifier_utilisateurs`
- `supprimer_utilisateurs`

**Rôles (4):**
- `voir_roles`
- `creer_roles`
- `modifier_roles`
- `supprimer_roles`

**Patrimoine (7):**
- `voir_patrimoine`
- `creer_patrimoine`
- `modifier_patrimoine`
- `supprimer_patrimoine`
- `valider_patrimoine`
- `attribuer_patrimoine`
- `voir_statistiques_patrimoine`

**Demandes de Fourniture (9):**
- `voir_toutes_demandes_fourniture`
- `voir_mes_demandes_fourniture`
- `creer_demande_fourniture`
- `modifier_demande_fourniture`
- `supprimer_demande_fourniture`
- `valider_demande_fourniture`
- `rejeter_demande_fourniture`
- `commander_fourniture`
- `livrer_fourniture`

**Inventaire (4):**
- `voir_inventaire`
- `creer_inventaire`
- `modifier_inventaire`
- `supprimer_inventaire`

### Gestion-Dossier (28 permissions)

**Dossiers (4):**
- `voir_dossiers`
- `creer_dossiers`
- `modifier_dossiers`
- `supprimer_dossiers`

**Cotations (4):**
- `voir_cotations`
- `creer_cotations`
- `modifier_cotations`
- `supprimer_cotations`

**Règlements Débours (5):**
- `voir_reglements_debours`
- `creer_reglements_debours`
- `modifier_reglements_debours`
- `supprimer_reglements_debours`
- `valider_reglements_debours`

**Règlements Clients (4):**
- `voir_reglements_clients`
- `creer_reglements_clients`
- `modifier_reglements_clients`
- `supprimer_reglements_clients`

**Factures (4):**
- `voir_factures`
- `creer_factures`
- `modifier_factures`
- `supprimer_factures`

### Commercial (16 permissions)

**Prospects (4):**
- `voir_prospects`
- `creer_prospects`
- `modifier_prospects`
- `supprimer_prospects`

**Clients (4):**
- `voir_clients`
- `creer_clients`
- `modifier_clients`
- `supprimer_clients`

**Devis (4):**
- `voir_devis`
- `creer_devis`
- `modifier_devis`
- `supprimer_devis`

**Contrats (4):**
- `voir_contrats`
- `creer_contrats`
- `modifier_contrats`
- `supprimer_contrats`

---

## Installation et Configuration

### 1. Exécuter le Seeder

```bash
php artisan db:seed --class=RolesPermissionsSeeder
```

**Résultat attendu:**
```
✅ 65 permissions créées
✅ 6 rôles créés (Administrateur, Direction, Employé Commercial, RH, Sales, Comptable)
Total in DB: 140 permissions, 15 roles
```

### 2. Assigner un Rôle à un Utilisateur

```php
// Dans un seeder ou controller
$user = User::find(1);
$user->assignRole('administrateur');

// Ou plusieurs rôles
$user->assignRole(['direction', 'rh']);
```

### 3. Vérifier les Permissions d'un Utilisateur

```bash
php artisan tinker

>>> $user = User::find(1);
>>> $user->roles->pluck('name');
=> ["administrateur"]

>>> $user->getAllPermissions()->pluck('name');
=> [tous les 65 permissions]

>>> $user->can('valider_demande_fourniture');
=> true
```

---

## Middleware de Protection des Routes

### Dans `routes/web.php`:

```php
// Protéger par permission
Route::middleware(['permission:voir_patrimoine'])->group(function () {
    Route::get('/patrimoines', [PatrimoineController::class, 'index']);
});

// Protéger par rôle
Route::middleware(['role:administrateur|direction'])->group(function () {
    Route::get('/statistiques', [StatistiqueController::class, 'index']);
});
```

---

## Maintenance

### Ajouter une Nouvelle Permission

1. Modifier `RolesPermissionsSeeder.php`:
```php
$nouvellesPermissions = [
    'voir_nouveau_module',
    'creer_nouveau_module',
];

foreach ($nouvellesPermissions as $permission) {
    Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
}
```

2. Assigner aux rôles appropriés:
```php
$adminRole->givePermissionTo('voir_nouveau_module', 'creer_nouveau_module');
$rhRole->givePermissionTo('voir_nouveau_module');
```

3. Réexécuter le seeder:
```bash
php artisan db:seed --class=RolesPermissionsSeeder
```

### Réinitialiser Complètement le Système RBAC

```bash
# Vider les tables de permissions/rôles
php artisan db:seed --class=RolesPermissionsSeeder

# Ou réinitialiser complètement
php artisan migrate:fresh --seed
```

---

## Tests Recommandés

### 1. Test de Visibilité des Demandes

- Créer 3 utilisateurs: Admin, Direction, Employé Commercial
- Créer 5 demandes avec différents demandeurs
- Vérifier:
  - Admin/Direction voient les 5 demandes
  - Employé Commercial voit uniquement ses demandes

### 2. Test du Workflow de Validation

- Créer une demande avec Employé Commercial
- Se connecter en tant que Direction
- Valider la demande
- Se connecter en tant que RH
- Commander la demande
- Vérifier que Employé Commercial ne peut pas commander

### 3. Test des Permissions de Modification

- Créer un patrimoine avec Admin
- Se connecter en tant que RH
- Vérifier que RH peut modifier
- Se connecter en tant que Direction
- Vérifier que Direction ne peut que consulter

---

## Résolution des Problèmes Courants

### "Permission does not exist"

**Cause:** La permission n'a pas été créée dans la base de données

**Solution:**
```bash
php artisan cache:clear
php artisan db:seed --class=RolesPermissionsSeeder
```

### "User does not have the right roles"

**Cause:** L'utilisateur n'a pas le rôle requis

**Solution:**
```php
$user = User::find($userId);
$user->assignRole('nom_du_role');
```

### Les permissions ne se mettent pas à jour

**Cause:** Cache des permissions

**Solution:**
```bash
php artisan cache:forget spatie.permission.cache
php artisan optimize:clear
```

---

## Sécurité et Bonnes Pratiques

1. **Ne jamais donner de permissions directes aux utilisateurs** - Toujours passer par les rôles
2. **Utiliser `can()` au lieu de `hasPermissionTo()`** - Plus performant avec cache
3. **Préfixer les permissions par module** - Ex: `patrimoine.voir`, `demande.valider`
4. **Documenter chaque permission** - Maintenir cette doc à jour
5. **Tester chaque rôle** - Vérifier régulièrement que les permissions sont correctes
6. **Principe du moindre privilège** - Donner le minimum de permissions nécessaires

---

## Contacts et Support

**Fichier Seeder:** `/var/www/administration/database/seeders/RolesPermissionsSeeder.php`  
**Documentation Spatie:** https://spatie.be/docs/laravel-permission/v6/introduction  
**Version:** Laravel 12.39.0, Spatie Permission v6

---

*Dernière mise à jour: {{ now()->format('d/m/Y H:i') }}*
