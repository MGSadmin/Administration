#!/bin/bash

# Script d'installation du système de gestion des postes vacants
# Usage: ./setup_member_management.sh

echo "🚀 Installation du système de gestion des postes vacants"
echo "=========================================================="
echo ""

# Couleurs
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Vérifier qu'on est dans le bon répertoire
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Erreur: Ce script doit être exécuté depuis le répertoire /var/www/administration${NC}"
    exit 1
fi

echo -e "${YELLOW}📋 Étape 1: Exécution des migrations${NC}"
php artisan migrate --path=database/migrations/2024_12_09_000001_create_historique_statut_membres_table.php
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Migrations exécutées avec succès${NC}"
else
    echo -e "${RED}❌ Erreur lors de l'exécution des migrations${NC}"
    exit 1
fi

echo ""
echo -e "${YELLOW}📋 Étape 2: Création des permissions${NC}"
php artisan tinker --execute="
use Spatie\Permission\Models\Permission;

\$permissions = [
    'voir_membres_organigramme',
    'modifier_membres_organigramme',
    'affecter_membres_organigramme',
    'licencier_membres_organigramme',
    'voir_historique_membres',
];

foreach (\$permissions as \$perm) {
    Permission::firstOrCreate(['name' => \$perm, 'guard_name' => 'web']);
    echo \"✓ Permission créée: \$perm\n\";
}

// Assigner les permissions au rôle administrateur et RH
\$adminRole = Spatie\Permission\Models\Role::where('name', 'administrateur')->first();
if (\$adminRole) {
    \$adminRole->givePermissionTo(\$permissions);
    echo \"✓ Permissions assignées au rôle administrateur\n\";
}

\$rhRole = Spatie\Permission\Models\Role::where('name', 'rh')->first();
if (\$rhRole) {
    \$rhRole->givePermissionTo(\$permissions);
    echo \"✓ Permissions assignées au rôle RH\n\";
}
"

echo ""
echo -e "${YELLOW}📋 Étape 3: Vérification de la structure${NC}"
php artisan route:list | grep "organigramme.members"
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Routes créées avec succès${NC}"
else
    echo -e "${RED}⚠️  Attention: Les routes n'ont pas été trouvées${NC}"
fi

echo ""
echo -e "${YELLOW}📋 Étape 4: Nettoyage du cache${NC}"
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo -e "${GREEN}✅ Cache nettoyé${NC}"

echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}✅ Installation terminée avec succès!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "${YELLOW}📚 Fonctionnalités disponibles:${NC}"
echo "  • Affectation d'utilisateurs aux postes"
echo "  • Gestion des démissions"
echo "  • Gestion des licenciements"
echo "  • Gestion des départs en retraite"
echo "  • Réaffectation de postes"
echo "  • Suivi des postes vacants"
echo "  • Historique complet des changements"
echo ""
echo -e "${YELLOW}🌐 Accès:${NC}"
echo "  • Liste des membres: /organigramme/members"
echo "  • Postes vacants: /organigramme/members-vacant"
echo "  • Historique: /organigramme/members-history"
echo ""
echo -e "${YELLOW}👤 Permissions créées:${NC}"
echo "  • voir_membres_organigramme"
echo "  • modifier_membres_organigramme"
echo "  • affecter_membres_organigramme"
echo "  • licencier_membres_organigramme"
echo "  • voir_historique_membres"
echo ""
echo -e "${GREEN}🎉 Le système est prêt à être utilisé!${NC}"
