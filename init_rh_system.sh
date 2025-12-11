#!/bin/bash

echo "🚀 Initialisation du système RH..."
echo ""

cd /var/www/administration

echo "📝 Création des rôles RH et Direction..."
php artisan tinker <<EOF
use Spatie\Permission\Models\Role;

// Créer les rôles
\$rh = Role::firstOrCreate(['name' => 'RH']);
\$ressourcesHumaines = Role::firstOrCreate(['name' => 'Ressources Humaines']);
\$direction = Role::firstOrCreate(['name' => 'Direction']);
\$admin = Role::firstOrCreate(['name' => 'Admin']);

echo "✅ Rôles créés: RH, Ressources Humaines, Direction, Admin\n";

// Assigner le rôle Admin au premier utilisateur
\$admin_user = \App\Models\User::first();
if (\$admin_user) {
    \$admin_user->assignRole('Admin');
    echo "✅ Rôle Admin assigné à: " . \$admin_user->name . "\n";
}

exit
EOF

echo ""
echo "📊 Création des soldes de congés pour les employés actifs..."
php artisan tinker <<EOF
use App\Models\OrganizationMember;
use App\Models\SoldeConge;

\$membres = OrganizationMember::where('status', 'ACTIVE')->get();
\$count = 0;

foreach (\$membres as \$membre) {
    \$solde = SoldeConge::firstOrCreate(
        ['organization_member_id' => \$membre->id],
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
    \$count++;
}

echo "✅ Soldes de congés créés pour \$count employés\n";

exit
EOF

echo ""
echo "✅ Initialisation terminée !"
echo ""
echo "📋 Prochaines étapes:"
echo "   1. Assignez les rôles RH/Direction aux utilisateurs appropriés"
echo "   2. Testez les fonctionnalités en vous connectant"
echo "   3. Créez vos premières demandes de congés"
echo ""
echo "🎯 Commandes utiles:"
echo "   - Assigner un rôle RH: php artisan tinker puis \$user->assignRole('RH')"
echo "   - Voir les rôles: php artisan tinker puis Role::all()"
echo ""
