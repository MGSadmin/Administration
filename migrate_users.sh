#!/bin/bash

# Script de migration des utilisateurs de gestion_dossiers vers mgs_administration
# Usage: ./migrate_users.sh

echo "🔄 Migration des utilisateurs vers mgs_administration"
echo "====================================================="

DB_USER="andry"
DB_PASS="AndryIT@123"

echo ""
echo "📊 État actuel des bases de données :"
echo "--------------------------------------"

echo "Utilisateurs dans gestion_dossiers:"
mysql -u "$DB_USER" -p"$DB_PASS" gestion_dossiers -e "SELECT COUNT(*) as total FROM users;" 2>/dev/null

echo ""
echo "Utilisateurs dans mgs_administration:"
mysql -u "$DB_USER" -p"$DB_PASS" mgs_administration -e "SELECT COUNT(*) as total FROM users;" 2>/dev/null

echo ""
read -p "❓ Voulez-vous migrer les utilisateurs ? (o/n) " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Oo]$ ]]; then
    echo ""
    echo "🚀 Exécution de la migration..."
    
    mysql -u "$DB_USER" -p"$DB_PASS" < /var/www/administration/migrate_users.sql
    
    if [ $? -eq 0 ]; then
        echo ""
        echo "✅ Migration réussie !"
        echo ""
        echo "📊 Résumé après migration :"
        echo "----------------------------"
        mysql -u "$DB_USER" -p"$DB_PASS" mgs_administration -e "
            SELECT 'Utilisateurs' as Type, COUNT(*) as Total FROM users
            UNION ALL
            SELECT 'Rôles' as Type, COUNT(*) as Total FROM roles
            UNION ALL
            SELECT 'Permissions' as Type, COUNT(*) as Total FROM permissions
            UNION ALL
            SELECT 'User-Role' as Type, COUNT(*) as Total FROM model_has_roles
            UNION ALL
            SELECT 'Role-Permission' as Type, COUNT(*) as Total FROM role_has_permissions;
        " 2>/dev/null
    else
        echo ""
        echo "❌ Erreur lors de la migration !"
        exit 1
    fi
else
    echo ""
    echo "⏭️  Migration annulée."
    exit 0
fi

echo ""
echo "✅ Terminé !"
