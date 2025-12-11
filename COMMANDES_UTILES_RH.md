# 🎯 COMMANDES UTILES - SYSTÈME RH

## 📦 Initialisation

### Exécuter les migrations
```bash
cd /var/www/administration
php artisan migrate
```

### Initialiser le système RH (rôles + soldes)
```bash
cd /var/www/administration
./init_rh_system.sh
```

### Créer le lien symbolique storage
```bash
php artisan storage:link
```

---

## 👥 Gestion des Rôles

### Voir tous les utilisateurs et leurs rôles
```bash
./assign_roles.sh
```

### Dans Laravel Tinker

```bash
php artisan tinker
```

#### Créer les rôles
```php
use Spatie\Permission\Models\Role;

Role::firstOrCreate(['name' => 'RH']);
Role::firstOrCreate(['name' => 'Ressources Humaines']);
Role::firstOrCreate(['name' => 'Direction']);
Role::firstOrCreate(['name' => 'Admin']);
```

#### Assigner un rôle RH
```php
$user = \App\Models\User::where('email', 'rh@example.com')->first();
$user->assignRole('RH');
echo "Rôle RH assigné à " . $user->name;
```

#### Assigner un rôle Direction
```php
$user = \App\Models\User::where('email', 'direction@example.com')->first();
$user->assignRole('Direction');
```

#### Assigner un rôle Admin
```php
$user = \App\Models\User::find(1);
$user->assignRole('Admin');
```

#### Voir les rôles d'un utilisateur
```php
$user = \App\Models\User::find(1);
$user->roles->pluck('name');
```

#### Retirer un rôle
```php
$user->removeRole('RH');
```

#### Vérifier si un utilisateur a un rôle
```php
$user->hasRole('RH'); // true ou false
```

---

## 📅 Gestion des Soldes de Congés

### Créer les soldes pour tous les employés actifs
```php
php artisan tinker
```

```php
use App\Models\OrganizationMember;
use App\Models\SoldeConge;

$membres = OrganizationMember::where('status', 'ACTIVE')->get();

foreach ($membres as $membre) {
    SoldeConge::firstOrCreate(
        ['organization_member_id' => $membre->id],
        [
            'conges_annuels_totaux' => 30,
            'conges_annuels_pris' => 0,
            'conges_annuels_restants' => 30,
            'conges_maladie_pris' => 0,
            'permissions_prises' => 0,
            'annee' => 2025,
            'date_derniere_mise_a_jour' => now(),
        ]
    );
}

echo "Soldes créés pour " . $membres->count() . " employés";
```

### Réinitialiser les congés pour une nouvelle année
```php
$soldes = \App\Models\SoldeConge::all();

foreach ($soldes as $solde) {
    $solde->resetForNewYear(2026);
}

echo "Soldes réinitialisés pour " . $soldes->count() . " employés";
```

### Voir le solde d'un employé
```php
$membre = \App\Models\OrganizationMember::find(1);
$solde = $membre->soldeConges;

echo "Congés restants: " . $solde->conges_annuels_restants . " jours";
```

---

## 📊 Statistiques

### Nombre de demandes de congés par statut
```php
php artisan tinker
```

```php
use App\Models\Conge;

echo "En attente: " . Conge::where('statut', 'en_attente')->count() . "\n";
echo "Approuvées: " . Conge::where('statut', 'approuve')->count() . "\n";
echo "Refusées: " . Conge::where('statut', 'refuse')->count() . "\n";
echo "Annulées: " . Conge::where('statut', 'annule')->count() . "\n";
```

### Congés par type
```php
$types = Conge::select('type', \DB::raw('count(*) as total'))
    ->groupBy('type')
    ->get();

foreach ($types as $type) {
    echo $type->type . ": " . $type->total . "\n";
}
```

### Employés licenciés cette année
```php
use App\Models\HistoriqueStatutMembre;

$licencies = HistoriqueStatutMembre::where('motif', 'licenciement')
    ->whereYear('created_at', 2025)
    ->with('organizationMember')
    ->get();

echo "Licenciés en 2025: " . $licencies->count();
```

### Postes vacants
```php
use App\Models\OrganizationMember;

$vacants = OrganizationMember::where('status', 'VACANT')->count();
echo "Postes vacants: " . $vacants;
```

---

## 📄 Gestion des Documents

### Créer un document pour un employé
```php
use App\Models\DocumentEmploye;
use App\Models\OrganizationMember;

$membre = OrganizationMember::find(1);

DocumentEmploye::create([
    'organization_member_id' => $membre->id,
    'created_by' => auth()->id() ?? 1,
    'type_document' => 'bulletin_paie',
    'titre' => 'Bulletin de paie - Novembre 2025',
    'description' => 'Bulletin de paie du mois de novembre',
    'fichier' => 'pending',
    'date_emission' => now(),
    'statut' => 'actif',
    'accessible_employe' => true,
]);

echo "Document créé pour " . $membre->display_name;
```

### Archiver les documents anciens
```php
$archived = DocumentEmploye::where('date_emission', '<', now()->subYears(5))
    ->update(['statut' => 'archive']);

echo $archived . " documents archivés";
```

### Documents en attente de génération
```php
$pending = DocumentEmploye::where('fichier', 'pending')->get();

foreach ($pending as $doc) {
    echo $doc->titre . " pour " . $doc->organizationMember->display_name . "\n";
}
```

---

## 👤 Gestion du Personnel

### Licencier un employé
```php
use App\Models\OrganizationMember;

$membre = OrganizationMember::find(1);

$membre->markAsLicencie(
    motif: 'Fin de période d\'essai',
    commentaire: 'Performance insuffisante',
    userId: auth()->id() ?? 1
);

echo "Employé licencié. Poste maintenant: " . $membre->position->status;
```

### Voir l'historique d'un employé
```php
$membre = OrganizationMember::find(1);
$historique = $membre->historiqueStatuts;

foreach ($historique as $h) {
    echo $h->date_effectif->format('d/m/Y') . ": ";
    echo $h->ancien_statut . " → " . $h->nouveau_statut;
    echo " (" . $h->motif_libelle . ")\n";
}
```

---

## 🔧 Maintenance

### Nettoyer les vieilles notifications
```bash
php artisan db:table notifications --where "created_at < now() - interval 6 month" --delete
```

### Vider le cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Optimiser l'application
```bash
php artisan optimize
```

---

## 🧪 Tests

### Créer une demande de congé de test
```php
use App\Models\Conge;
use App\Models\OrganizationMember;

$membre = OrganizationMember::where('status', 'ACTIVE')->first();

Conge::create([
    'organization_member_id' => $membre->id,
    'user_id' => $membre->user_id,
    'type' => 'conge_annuel',
    'date_debut' => now()->addDays(7),
    'date_fin' => now()->addDays(14),
    'nb_jours' => 8,
    'motif' => 'Vacances familiales',
    'statut' => 'en_attente',
]);

echo "Demande de congé créée pour " . $membre->display_name;
```

### Approuver une demande
```php
$conge = Conge::where('statut', 'en_attente')->first();

if ($conge) {
    $conge->update([
        'statut' => 'approuve',
        'validateur_id' => 1,
        'date_validation' => now(),
    ]);
    
    // Mettre à jour le solde
    $solde = $conge->organizationMember->soldeConges;
    if ($solde) {
        $solde->updateAfterCongeApproved($conge);
    }
    
    echo "Congé approuvé";
}
```

---

## 📊 Rapports

### Rapport mensuel des congés
```php
$conges = Conge::whereMonth('date_debut', now()->month)
    ->whereYear('date_debut', now()->year)
    ->with('organizationMember')
    ->get();

echo "Congés ce mois-ci: " . $conges->count() . "\n";
echo "Total jours: " . $conges->sum('nb_jours') . "\n";
```

### Employés avec le plus de congés pris
```php
$soldes = \App\Models\SoldeConge::orderBy('conges_annuels_pris', 'desc')
    ->limit(10)
    ->get();

foreach ($soldes as $solde) {
    echo $solde->organizationMember->display_name . ": ";
    echo $solde->conges_annuels_pris . " jours\n";
}
```

---

## 🚨 Dépannage

### Réinstaller les permissions
```bash
php artisan permission:cache-reset
```

### Vérifier la base de données
```bash
php artisan migrate:status
```

### Voir les erreurs récentes
```bash
tail -100 storage/logs/laravel.log
```

### Tester une route
```bash
php artisan route:list | grep conges
```

---

## 💾 Backup

### Créer un backup de la base de données
```bash
php artisan backup:run
```

### Exporter les congés en CSV
```php
use App\Models\Conge;

$conges = Conge::with('organizationMember')->get();

$csv = fopen('conges_export.csv', 'w');
fputcsv($csv, ['Employé', 'Type', 'Date Début', 'Date Fin', 'Jours', 'Statut']);

foreach ($conges as $conge) {
    fputcsv($csv, [
        $conge->organizationMember->display_name,
        $conge->type_libelle,
        $conge->date_debut->format('d/m/Y'),
        $conge->date_fin->format('d/m/Y'),
        $conge->nb_jours,
        $conge->statut_libelle,
    ]);
}

fclose($csv);
echo "Export terminé: conges_export.csv";
```

---

**Pour plus d'informations, consultez:**
- `README_SYSTEME_RH.md` - Documentation complète
- `GUIDE_SYSTEME_CONGES_RH.md` - Guide d'utilisation détaillé
