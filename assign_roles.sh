#!/bin/bash

echo "👥 Script d'assignation des rôles RH"
echo "===================================="
echo ""

cd /var/www/administration

echo "📋 Liste des utilisateurs actuels:"
php artisan tinker <<EOF
\$users = \App\Models\User::all();
foreach (\$users as \$user) {
    echo \$user->id . " - " . \$user->name . " (" . \$user->email . ")\n";
    if (\$user->roles->count() > 0) {
        echo "   Rôles: " . \$user->roles->pluck('name')->join(', ') . "\n";
    }
}
exit
EOF

echo ""
echo "📝 Pour assigner un rôle à un utilisateur:"
echo ""
echo "1. Lancez php artisan tinker"
echo "2. Trouvez l'utilisateur:"
echo "   \$user = \App\Models\User::where('email', 'email@example.com')->first();"
echo ""
echo "3. Assignez le rôle:"
echo "   \$user->assignRole('RH');              // Pour le RH"
echo "   \$user->assignRole('Direction');       // Pour la Direction"
echo "   \$user->assignRole('Admin');           // Pour un Admin"
echo ""
echo "4. Vérifiez:"
echo "   \$user->roles;"
echo ""
echo "📌 Exemple complet dans tinker:"
echo "   \$user = \App\Models\User::find(2);"
echo "   \$user->assignRole('RH');"
echo "   echo 'Rôle RH assigné à ' . \$user->name;"
echo ""
