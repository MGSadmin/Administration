#!/bin/bash

# Script de test de l'authentification centralisée MGS
# Usage: ./test_auth.sh

echo "============================================"
echo "🧪 Test d'Authentification Centralisée MGS"
echo "============================================"
echo ""

# Couleurs
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
ADMIN_URL="http://localhost/administration"
BASE_DIR="/var/www/administration"

echo "📍 Répertoire: $BASE_DIR"
echo "🌐 URL: $ADMIN_URL"
echo ""

# Test 1: Vérifier que les fichiers existent
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Test 1: Vérification des fichiers"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

FILES=(
    "resources/views/auth/login.blade.php"
    "resources/views/auth/register.blade.php"
    "app/Http/Controllers/Auth/AuthController.php"
    "routes/web.php"
    "routes/api.php"
    "config/app_urls.php"
)

for file in "${FILES[@]}"; do
    if [ -f "$BASE_DIR/$file" ]; then
        echo -e "${GREEN}✅${NC} $file"
    else
        echo -e "${RED}❌${NC} $file - MANQUANT"
    fi
done
echo ""

# Test 2: Vérifier que les routes sont définies
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Test 2: Vérification des routes"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

cd $BASE_DIR

ROUTES=(
    "auth.login"
    "auth.register"
    "auth.logout"
)

for route in "${ROUTES[@]}"; do
    if php artisan route:list --name="$route" 2>/dev/null | grep -q "$route"; then
        echo -e "${GREEN}✅${NC} Route: $route"
    else
        echo -e "${RED}❌${NC} Route: $route - NON TROUVÉE"
    fi
done
echo ""

# Test 3: Vérifier la configuration
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Test 3: Vérification de la configuration"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Vérifier que le contrôleur existe et est valide
if php -l app/Http/Controllers/Auth/AuthController.php > /dev/null 2>&1; then
    echo -e "${GREEN}✅${NC} AuthController.php - Syntaxe valide"
else
    echo -e "${RED}❌${NC} AuthController.php - Erreur de syntaxe"
fi

# Vérifier les vues
if php artisan view:clear > /dev/null 2>&1; then
    echo -e "${GREEN}✅${NC} Cache des vues effacé"
else
    echo -e "${YELLOW}⚠️${NC}  Impossible d'effacer le cache des vues"
fi
echo ""

# Test 4: Vérifier la documentation
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Test 4: Vérification de la documentation"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

DOCS=(
    "GUIDE_AUTHENTIFICATION.md"
    "MIGRATION_AUTH_CENTRALISEE.md"
    "README_AUTH.md"
    "VISUAL_SUMMARY_AUTH.md"
    "INDEX_AUTH.md"
)

for doc in "${DOCS[@]}"; do
    if [ -f "$BASE_DIR/$doc" ]; then
        lines=$(wc -l < "$BASE_DIR/$doc")
        echo -e "${GREEN}✅${NC} $doc ($lines lignes)"
    else
        echo -e "${RED}❌${NC} $doc - MANQUANT"
    fi
done
echo ""

# Test 5: Test HTTP (optionnel)
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Test 5: Test HTTP des pages (optionnel)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

if command -v curl &> /dev/null; then
    # Test page login
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$ADMIN_URL/auth/login")
    if [ "$HTTP_CODE" = "200" ]; then
        echo -e "${GREEN}✅${NC} /auth/login - HTTP $HTTP_CODE"
    else
        echo -e "${YELLOW}⚠️${NC}  /auth/login - HTTP $HTTP_CODE"
    fi

    # Test page register
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$ADMIN_URL/auth/register")
    if [ "$HTTP_CODE" = "200" ]; then
        echo -e "${GREEN}✅${NC} /auth/register - HTTP $HTTP_CODE"
    else
        echo -e "${YELLOW}⚠️${NC}  /auth/register - HTTP $HTTP_CODE"
    fi
else
    echo -e "${YELLOW}⚠️${NC}  curl non installé - Tests HTTP ignorés"
fi
echo ""

# Test 6: Vérifier les permissions de fichiers
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Test 6: Permissions des fichiers"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

if [ -w "$BASE_DIR/storage/logs" ]; then
    echo -e "${GREEN}✅${NC} storage/logs - Écriture possible"
else
    echo -e "${RED}❌${NC} storage/logs - Permissions insuffisantes"
fi

if [ -w "$BASE_DIR/bootstrap/cache" ]; then
    echo -e "${GREEN}✅${NC} bootstrap/cache - Écriture possible"
else
    echo -e "${RED}❌${NC} bootstrap/cache - Permissions insuffisantes"
fi
echo ""

# Résumé
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📊 Résumé"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "✨ Fonctionnalités implémentées:"
echo "   • Pages de login et register"
echo "   • Contrôleur d'authentification"
echo "   • Routes configurées"
echo "   • API de validation"
echo "   • Documentation complète"
echo ""
echo "📖 Prochaines étapes:"
echo "   1. Tester manuellement: $ADMIN_URL/auth/login"
echo "   2. Créer un utilisateur de test"
echo "   3. Migrer Commercial et Gestion Dossier"
echo ""
echo "📚 Documentation disponible:"
echo "   • INDEX_AUTH.md - Point d'entrée"
echo "   • VISUAL_SUMMARY_AUTH.md - Vue d'ensemble"
echo "   • README_AUTH.md - Documentation technique"
echo "   • GUIDE_AUTHENTIFICATION.md - Guide d'utilisation"
echo "   • MIGRATION_AUTH_CENTRALISEE.md - Migration"
echo ""
echo "============================================"
echo "✅ Tests terminés!"
echo "============================================"
