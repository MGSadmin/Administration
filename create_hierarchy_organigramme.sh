#!/bin/bash

echo "🏢 Création de l'organigramme hiérarchique de test..."
echo ""

cd /var/www/administration

php artisan tinker <<'EOF'
use App\Models\Department;
use App\Models\Position;
use App\Models\OrganizationMember;

// Supprimer les données existantes si besoin
// Department::truncate();
// Position::truncate();
// OrganizationMember::truncate();

// 1. Créer le département Direction Générale
$dg = Department::firstOrCreate([
    'code' => 'DG'
], [
    'name' => 'Direction Générale',
    'description' => 'Direction Générale de l\'entreprise',
    'order' => 1,
    'color' => '#FF6B35'
]);

// 2. Créer les départements principaux
$df = Department::firstOrCreate([
    'code' => 'DF'
], [
    'name' => 'Direction Financière',
    'description' => 'Direction financière et comptable',
    'order' => 2,
    'color' => '#1e3a5f'
]);

$dc = Department::firstOrCreate([
    'code' => 'DC'
], [
    'name' => 'Direction Commerciale',
    'description' => 'Direction commerciale et marketing',
    'order' => 3,
    'color' => '#2c5282'
]);

$drh = Department::firstOrCreate([
    'code' => 'DRH'
], [
    'name' => 'Direction des RH',
    'description' => 'Direction des ressources humaines',
    'order' => 4,
    'color' => '#285e61'
]);

// 3. Créer le poste PDG (niveau 1)
$pdg = Position::firstOrCreate([
    'title' => 'PDG'
], [
    'department_id' => $dg->id,
    'description' => 'Président Directeur Général',
    'level' => 1,
    'order' => 1
]);

// 4. Créer les postes de directeurs (niveau 2)
$dirFinancier = Position::firstOrCreate([
    'title' => 'Directeur Financier'
], [
    'department_id' => $df->id,
    'parent_position_id' => $pdg->id,
    'description' => 'Directeur financier',
    'level' => 2,
    'order' => 1
]);

$dirCommercial = Position::firstOrCreate([
    'title' => 'Directeur Commercial'
], [
    'department_id' => $dc->id,
    'parent_position_id' => $pdg->id,
    'description' => 'Directeur commercial',
    'level' => 2,
    'order' => 2
]);

$dirRH = Position::firstOrCreate([
    'title' => 'Directeur des RH'
], [
    'department_id' => $drh->id,
    'parent_position_id' => $pdg->id,
    'description' => 'Directeur des ressources humaines',
    'level' => 2,
    'order' => 3
]);

// 5. Services sous Direction Financière
$comptabilite = Position::firstOrCreate([
    'title' => 'Comptabilité'
], [
    'department_id' => $df->id,
    'parent_position_id' => $dirFinancier->id,
    'description' => 'Service comptabilité',
    'level' => 3,
    'order' => 1
]);

$controleGestion = Position::firstOrCreate([
    'title' => 'Contrôle de Gestion'
], [
    'department_id' => $df->id,
    'parent_position_id' => $dirFinancier->id,
    'description' => 'Contrôle de gestion',
    'level' => 3,
    'order' => 2
]);

$paie = Position::firstOrCreate([
    'title' => 'Paie'
], [
    'department_id' => $df->id,
    'parent_position_id' => $dirFinancier->id,
    'description' => 'Service paie',
    'level' => 3,
    'order' => 3
]);

// 6. Services sous Direction Commerciale
$marketing = Position::firstOrCreate([
    'title' => 'Marketing'
], [
    'department_id' => $dc->id,
    'parent_position_id' => $dirCommercial->id,
    'description' => 'Service marketing',
    'level' => 3,
    'order' => 1
]);

$produit = Position::firstOrCreate([
    'title' => 'Produit'
], [
    'department_id' => $dc->id,
    'parent_position_id' => $dirCommercial->id,
    'description' => 'Gestion produits',
    'level' => 3,
    'order' => 2
]);

$vente = Position::firstOrCreate([
    'title' => 'Vente'
], [
    'department_id' => $dc->id,
    'parent_position_id' => $dirCommercial->id,
    'description' => 'Service ventes',
    'level' => 3,
    'order' => 3
]);

$sav = Position::firstOrCreate([
    'title' => 'SAV'
], [
    'department_id' => $dc->id,
    'parent_position_id' => $dirCommercial->id,
    'description' => 'Service après-vente',
    'level' => 3,
    'order' => 4
]);

// 7. Services sous Direction RH
$gestionPersonnel = Position::firstOrCreate([
    'title' => 'Gestion du Personnel'
], [
    'department_id' => $drh->id,
    'parent_position_id' => $dirRH->id,
    'description' => 'Gestion administrative du personnel',
    'level' => 3,
    'order' => 1
]);

$recrutement = Position::firstOrCreate([
    'title' => 'Recrutement'
], [
    'department_id' => $drh->id,
    'parent_position_id' => $dirRH->id,
    'description' => 'Service recrutement',
    'level' => 3,
    'order' => 2
]);

echo "✅ Organigramme hiérarchique créé avec succès!\n";
echo "\n📊 Résumé:\n";
echo "   - Départements: " . Department::count() . "\n";
echo "   - Postes: " . Position::count() . "\n";
echo "\n🌐 Accédez à la vue hiérarchique sur: /organigramme/hierarchy\n";

exit
EOF

echo ""
echo "✅ Terminé!"
echo ""
echo "🎯 Prochaines étapes:"
echo "   1. Visitez http://votre-domaine/organigramme/hierarchy"
echo "   2. Assignez des employés aux postes depuis la gestion du personnel"
echo ""
