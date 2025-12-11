#!/bin/bash

# Script d'installation et configuration du système SSO
# Administration MGS

echo "=================================================="
echo "  Installation du Système SSO - Administration  "
echo "=================================================="
echo ""

# Couleurs pour l'affichage
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Fonction pour afficher les messages
print_success() {
    echo -e "${GREEN}✓${NC} $1"
}

print_info() {
    echo -e "${BLUE}ℹ${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

# Vérifier si nous sommes dans le bon répertoire
if [ ! -f "artisan" ]; then
    print_error "Ce script doit être exécuté depuis le répertoire /var/www/administration"
    exit 1
fi

print_info "Démarrage de l'installation..."
echo ""

# Étape 1: Vérifier les dépendances
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Étape 1: Vérification des dépendances"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

if composer show laravel/sanctum > /dev/null 2>&1; then
    print_success "Laravel Sanctum est installé"
else
    print_error "Laravel Sanctum n'est pas installé"
    exit 1
fi

if composer show spatie/laravel-permission > /dev/null 2>&1; then
    print_success "Spatie Permission est installé"
else
    print_error "Spatie Permission n'est pas installé"
    exit 1
fi

echo ""

# Étape 2: Exécuter les migrations
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Étape 2: Exécution des migrations"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

print_info "Exécution de php artisan migrate..."
if php artisan migrate --force; then
    print_success "Migrations exécutées avec succès"
else
    print_error "Erreur lors de l'exécution des migrations"
    exit 1
fi

echo ""

# Étape 3: Exécuter les seeders
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Étape 3: Initialisation des données"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

print_info "Création des sites..."
if php artisan db:seed --class=SitesSeeder --force; then
    print_success "Sites créés avec succès"
else
    print_warning "Les sites existent peut-être déjà"
fi

print_info "Création des rôles et permissions..."
if php artisan db:seed --class=RolesAndPermissionsSeeder --force; then
    print_success "Rôles et permissions créés avec succès"
else
    print_warning "Les rôles et permissions existent peut-être déjà"
fi

echo ""

# Étape 4: Vérifier la configuration
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Étape 4: Vérification de la configuration"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

if [ -f "config/cors.php" ]; then
    print_success "Configuration CORS présente"
else
    print_warning "Configuration CORS manquante"
fi

if [ -f "config/sanctum.php" ]; then
    print_success "Configuration Sanctum présente"
else
    print_error "Configuration Sanctum manquante"
    exit 1
fi

echo ""

# Étape 5: Optimiser l'application
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Étape 5: Optimisation de l'application"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

print_info "Nettoyage du cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

print_info "Optimisation..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

print_success "Application optimisée"

echo ""

# Étape 6: Afficher les informations de connexion
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Installation terminée avec succès ! 🎉"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo -e "${GREEN}Utilisateur Super Admin créé :${NC}"
echo "  Email      : admin@mgs.mg"
echo "  Mot de passe : password"
echo ""
echo -e "${BLUE}URLs importantes :${NC}"
echo "  Dashboard     : /dashboard"
echo "  Organigramme  : /organigramme"
echo "  Login SSO     : /sso/login"
echo "  Admin Users   : /admin/users"
echo "  Admin Roles   : /admin/roles"
echo ""
echo -e "${BLUE}API Endpoints :${NC}"
echo "  Vérifier token         : POST /api/auth/verify-token"
echo "  Permissions utilisateur : GET /api/auth/user-permissions/{site}"
echo "  Vérifier permission    : POST /api/auth/check-permission"
echo "  Infos utilisateur      : GET /api/auth/me"
echo ""
echo -e "${YELLOW}⚠ N'oubliez pas de :${NC}"
echo "  1. Modifier le mot de passe du Super Admin"
echo "  2. Configurer SANCTUM_STATEFUL_DOMAINS dans .env"
echo "  3. Vérifier les domaines dans config/cors.php"
echo "  4. Configurer les sites clients"
echo ""
echo "Pour plus d'informations, consultez : GUIDE_INSTALLATION_SSO.md"
echo ""

# Afficher les statistiques
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Statistiques"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Compter les rôles
ROLES_COUNT=$(php artisan tinker --execute="echo \Spatie\Permission\Models\Role::count();" 2>/dev/null | tail -1)
print_info "Rôles créés : ${ROLES_COUNT}"

# Compter les permissions
PERMISSIONS_COUNT=$(php artisan tinker --execute="echo \Spatie\Permission\Models\Permission::count();" 2>/dev/null | tail -1)
print_info "Permissions créées : ${PERMISSIONS_COUNT}"

# Compter les sites
SITES_COUNT=$(php artisan tinker --execute="echo \App\Models\Site::count();" 2>/dev/null | tail -1)
print_info "Sites configurés : ${SITES_COUNT}"

echo ""
print_success "Installation SSO terminée !"
