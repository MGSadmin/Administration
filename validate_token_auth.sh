#!/bin/bash

# Token Auth Migration Validation Script
# Purpose: Verify that token-based auth is properly deployed across all apps
# Usage: bash validate_token_auth.sh [app_name | all]

set -e

PROJECTS=("administration" "commercial" "gestion-dossier")
COLORS="\033[1;33m"  # Yellow for headers
GREEN="\033[0;32m"   # Green for success
RED="\033[0;31m"     # Red for errors
NC="\033[0m"         # No color

echo -e "${COLORS}=== Token Auth Migration Validator ===${NC}"
echo ""

# Function to validate file exists
validate_file() {
    local app=$1
    local file=$2
    if [ -f "/var/www/$app/$file" ]; then
        echo -e "${GREEN}✓${NC} $app: $file exists"
        return 0
    else
        echo -e "${RED}✗${NC} $app: $file MISSING"
        return 1
    fi
}

# Function to check PHP syntax
check_syntax() {
    local app=$1
    local file=$2
    if php -l "/var/www/$app/$file" > /dev/null 2>&1; then
        echo -e "${GREEN}✓${NC} $app: $file syntax OK"
        return 0
    else
        echo -e "${RED}✗${NC} $app: $file has syntax errors"
        php -l "/var/www/$app/$file"
        return 1
    fi
}

# Function to validate no direct DB connections
validate_no_db_connection() {
    local app=$1
    if grep -q "protected \\\$connection = 'administration'" "/var/www/$app/app/Models/User.php"; then
        echo -e "${RED}✗${NC} $app: Still has protected \$connection in User model!"
        return 1
    else
        echo -e "${GREEN}✓${NC} $app: User model does NOT have admin DB connection"
        return 0
    fi
}

# Function to validate routes exist
validate_routes() {
    local app=$1
    if grep -q "Route::get('\/login'" "/var/www/$app/routes/web.php"; then
        echo -e "${GREEN}✓${NC} $app: Login routes registered"
        return 0
    else
        echo -e "${RED}✗${NC} $app: Login routes missing"
        return 1
    fi
}

# Check Administration App (Central Hub)
echo -e "${COLORS}[Administration - Central Hub]${NC}"
validate_file "administration" "routes/api.php"
validate_file "administration" "app/Models/User.php"
check_syntax "administration" "routes/api.php"
# Check for new endpoints
if grep -q "'/me'" "/var/www/administration/routes/api.php"; then
    echo -e "${GREEN}✓${NC} administration: /api/me endpoint added"
else
    echo -e "${RED}✗${NC} administration: /api/me endpoint missing"
fi

echo ""

# Check Commercial App
echo -e "${COLORS}[Commercial - Client App]${NC}"
validate_file "commercial" "app/Services/AdminAuthService.php"
validate_file "commercial" "app/Http/Controllers/Auth/LoginController.php"
validate_file "commercial" "app/Http/Middleware/SsoAuthentication.php"
validate_file "commercial" "routes/web.php"
validate_file "commercial" "app/Models/User.php"

check_syntax "commercial" "app/Services/AdminAuthService.php"
check_syntax "commercial" "app/Http/Controllers/Auth/LoginController.php"
check_syntax "commercial" "app/Http/Middleware/SsoAuthentication.php"

validate_no_db_connection "commercial"
validate_routes "commercial"

echo ""

# Check Gestion-Dossier App
echo -e "${COLORS}[Gestion-Dossier - Client App]${NC}"
validate_file "gestion-dossier" "app/Services/AdminAuthService.php"
validate_file "gestion-dossier" "app/Http/Controllers/Auth/LoginController.php"
validate_file "gestion-dossier" "app/Http/Middleware/SsoAuthentication.php"
validate_file "gestion-dossier" "routes/web.php"
validate_file "gestion-dossier" "app/Models/User.php"

check_syntax "gestion-dossier" "app/Services/AdminAuthService.php"
check_syntax "gestion-dossier" "app/Http/Controllers/Auth/LoginController.php"
check_syntax "gestion-dossier" "app/Http/Middleware/SsoAuthentication.php"

validate_no_db_connection "gestion-dossier"
validate_routes "gestion-dossier"

echo ""
echo -e "${COLORS}=== Validation Complete ===${NC}"
echo ""
echo "Next steps:"
echo "1. Clear app caches: php artisan cache:clear && php artisan config:cache"
echo "2. Run curl tests on administration /api/login and /api/me"
echo "3. Test login flow on commercial: /login"
echo "4. Test login flow on gestion-dossier: /login"
echo "5. Monitor logs: tail -f storage/logs/laravel.log"
echo ""
