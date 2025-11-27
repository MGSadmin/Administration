#!/bin/bash

# Script d'export des utilisateurs depuis le serveur de production
# À exécuter sur le serveur distant (debours.mgs.mg)

echo "🔍 Export des utilisateurs depuis le serveur de production"
echo "=========================================================="

# Configuration - MODIFIER SELON VOTRE SERVEUR
DB_USER="cpanel_user_dbuser"
DB_NAME="cpanel_user_gestion_dossiers"
OUTPUT_FILE="export_users_$(date +%Y%m%d_%H%M%S).sql"

echo ""
read -sp "Mot de passe MySQL : " DB_PASS
echo ""

# Vérifier la connexion
echo ""
echo "📊 Vérification des données à exporter..."
mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "
SELECT 'Utilisateurs:' as Type, COUNT(*) as Total FROM users
UNION ALL
SELECT 'Rôles:' as Type, COUNT(*) as Total FROM roles
UNION ALL
SELECT 'Permissions:' as Type, COUNT(*) as Total FROM permissions
UNION ALL
SELECT 'User-Role:' as Type, COUNT(*) as Total FROM model_has_roles
UNION ALL
SELECT 'Role-Permission:' as Type, COUNT(*) as Total FROM role_has_permissions;
" 2>/dev/null

if [ $? -ne 0 ]; then
    echo "❌ Erreur de connexion à la base de données"
    exit 1
fi

echo ""
read -p "Continuer l'export ? (o/n) " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Oo]$ ]]; then
    echo "Export annulé."
    exit 0
fi

# Export des tables
echo ""
echo "📦 Export en cours..."

mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" \
    users \
    roles \
    permissions \
    model_has_roles \
    role_has_permissions \
    --no-create-info \
    --skip-add-drop-table \
    --complete-insert \
    > "$OUTPUT_FILE" 2>/dev/null

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Export réussi !"
    echo ""
    echo "📄 Fichier créé : $OUTPUT_FILE"
    echo "📊 Taille : $(du -h "$OUTPUT_FILE" | cut -f1)"
    echo ""
    echo "📥 Pour télécharger ce fichier :"
    echo "   scp $(whoami)@$(hostname):~/$(basename "$OUTPUT_FILE") ~/Downloads/"
    echo ""
    echo "📤 Pour importer en local :"
    echo "   mysql -u andry -p'AndryIT@123' mgs_administration < ~/Downloads/$OUTPUT_FILE"
else
    echo ""
    echo "❌ Erreur lors de l'export"
    exit 1
fi
