#!/bin/bash

echo "════════════════════════════════════════════════════════════"
echo "  Installation du Système de Permissions Moderne - MGS"
echo "════════════════════════════════════════════════════════════"
echo ""

# Vérifier qu'on est dans le bon répertoire
if [ ! -f "artisan" ]; then
    echo "❌ Erreur: Ce script doit être exécuté depuis le répertoire racine de Laravel"
    exit 1
fi

echo "📦 Étape 1/5: Mise à jour de l'autoload..."
composer dump-autoload
if [ $? -ne 0 ]; then
    echo "❌ Erreur lors de la mise à jour de l'autoload"
    exit 1
fi
echo "✅ Autoload mis à jour"
echo ""

echo "🗑️  Étape 2/5: Nettoyage du cache..."
php artisan optimize:clear
if [ $? -ne 0 ]; then
    echo "❌ Erreur lors du nettoyage du cache"
    exit 1
fi
echo "✅ Cache nettoyé"
echo ""

echo "🔄 Étape 3/5: Exécution du seeder des permissions..."
php artisan db:seed --class=ModernRolesPermissionsSeeder
if [ $? -ne 0 ]; then
    echo "❌ Erreur lors de l'exécution du seeder"
    echo "⚠️  Vérifiez que la base de données est accessible"
    exit 1
fi
echo "✅ Permissions et rôles créés"
echo ""

echo "🔐 Étape 4/5: Nettoyage du cache des permissions..."
php artisan permission:cache-reset
if [ $? -ne 0 ]; then
    echo "⚠️  Avertissement: Impossible de nettoyer le cache des permissions"
    echo "   Cela peut être normal si Spatie Permission n'a pas cette commande"
fi
echo "✅ Cache des permissions nettoyé"
echo ""

echo "🧪 Étape 5/5: Vérification de l'installation..."
php artisan tinker --execute="
    use Spatie\Permission\Models\Role;
    use Spatie\Permission\Models\Permission;
    echo 'Rôles créés: ' . Role::count() . PHP_EOL;
    echo 'Permissions créées: ' . Permission::count() . PHP_EOL;
    echo PHP_EOL;
    echo 'Liste des rôles:' . PHP_EOL;
    foreach(Role::all() as \$role) {
        echo '  - ' . \$role->name . ' (' . \$role->permissions->count() . ' permissions)' . PHP_EOL;
    }
"
echo ""

echo "════════════════════════════════════════════════════════════"
echo "✅ Installation terminée avec succès!"
echo "════════════════════════════════════════════════════════════"
echo ""
echo "📝 Informations importantes:"
echo "   • Utilisateur Super Admin: admin@mgs.mg"
echo "   • Mot de passe: Admin@2025"
echo ""
echo "📚 Documentation:"
echo "   • Guide complet: GUIDE_PERMISSIONS.md"
echo "   • Exemples d'implémentation: EXEMPLES_IMPLEMENTATION_PERMISSIONS.md"
echo "   • Exemples de vues: resources/views/examples/permissions-examples.blade.php"
echo ""
echo "🎯 Prochaines étapes:"
echo "   1. Connectez-vous avec le compte Super Admin"
echo "   2. Créez des utilisateurs de test"
echo "   3. Assignez-leur des rôles"
echo "   4. Testez les permissions dans l'interface"
echo ""
echo "💡 Commandes utiles:"
echo "   • Lister les permissions: php artisan permission:show"
echo "   • Créer un rôle: php artisan tinker"
echo "   • Voir les utilisateurs: php artisan tinker"
echo ""
echo "════════════════════════════════════════════════════════════"
