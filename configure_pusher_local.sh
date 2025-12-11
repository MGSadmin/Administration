#!/bin/bash

#=============================================================================
# CONFIGURATION PUSHER - ENVIRONNEMENT LOCAL
#=============================================================================
# Configure Pusher Channels dans les 3 applications (local)
#=============================================================================

set -e

# Couleurs
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}=========================================="
echo "  CONFIGURATION PUSHER - LOCAL"
echo "==========================================${NC}"
echo ""

# Demander les credentials Pusher
echo -e "${YELLOW}Entrez vos credentials Pusher Channels:${NC}"
echo "Trouvez-les sur: https://dashboard.pusher.com → App Keys"
echo ""
read -p "Pusher App ID: " PUSHER_APP_ID
read -p "Pusher Key: " PUSHER_KEY
read -p "Pusher Secret: " PUSHER_SECRET
read -p "Pusher Cluster (eu par défaut): " PUSHER_CLUSTER
PUSHER_CLUSTER=${PUSHER_CLUSTER:-eu}

echo ""
echo -e "${GREEN}Configuration:${NC}"
echo "  App ID: $PUSHER_APP_ID"
echo "  Key: $PUSHER_KEY"
echo "  Cluster: $PUSHER_CLUSTER"
echo ""

# Chemins des applications locales
APPS=(
    "/var/www/administration"
    "/var/www/commercial"
    "/var/www/gestion-dossier"
)

APP_NAMES=(
    "Administration"
    "Commercial"
    "Gestion-Dossier"
)

# Fonction pour mettre à jour ou ajouter une variable dans .env
update_env_var() {
    local env_file=$1
    local key=$2
    local value=$3
    
    if grep -q "^${key}=" "$env_file" 2>/dev/null; then
        # La variable existe, on la remplace
        sed -i "s|^${key}=.*|${key}=${value}|" "$env_file"
    else
        # La variable n'existe pas, on l'ajoute
        echo "${key}=${value}" >> "$env_file"
    fi
}

# Configurer chaque application
for i in "${!APPS[@]}"; do
    APP_PATH="${APPS[$i]}"
    APP_NAME="${APP_NAMES[$i]}"
    ENV_FILE="${APP_PATH}/.env"
    
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${YELLOW}Configuration de ${APP_NAME}...${NC}"
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    
    # Vérifier que l'application existe
    if [ ! -d "$APP_PATH" ]; then
        echo -e "${RED}❌ Dossier non trouvé: $APP_PATH${NC}"
        echo "   Ignoré."
        echo ""
        continue
    fi
    
    # Vérifier que le fichier .env existe
    if [ ! -f "$ENV_FILE" ]; then
        echo -e "${RED}❌ Fichier .env non trouvé${NC}"
        echo "   Ignoré."
        echo ""
        continue
    fi
    
    # Sauvegarder le .env
    BACKUP_FILE="${ENV_FILE}.backup-$(date +%Y%m%d-%H%M%S)"
    cp "$ENV_FILE" "$BACKUP_FILE"
    echo -e "${GREEN}✅ Backup créé: $(basename $BACKUP_FILE)${NC}"
    
    # Configurer Broadcasting
    update_env_var "$ENV_FILE" "BROADCAST_CONNECTION" "pusher"
    
    # Configurer Pusher (backend)
    update_env_var "$ENV_FILE" "PUSHER_APP_ID" "$PUSHER_APP_ID"
    update_env_var "$ENV_FILE" "PUSHER_APP_KEY" "$PUSHER_KEY"
    update_env_var "$ENV_FILE" "PUSHER_APP_SECRET" "$PUSHER_SECRET"
    update_env_var "$ENV_FILE" "PUSHER_HOST" ""
    update_env_var "$ENV_FILE" "PUSHER_PORT" "443"
    update_env_var "$ENV_FILE" "PUSHER_SCHEME" "https"
    update_env_var "$ENV_FILE" "PUSHER_APP_CLUSTER" "$PUSHER_CLUSTER"
    
    # Configurer Vite (frontend)
    update_env_var "$ENV_FILE" "VITE_PUSHER_APP_KEY" "\"\${PUSHER_APP_KEY}\""
    update_env_var "$ENV_FILE" "VITE_PUSHER_HOST" ""
    update_env_var "$ENV_FILE" "VITE_PUSHER_PORT" "443"
    update_env_var "$ENV_FILE" "VITE_PUSHER_SCHEME" "https"
    update_env_var "$ENV_FILE" "VITE_PUSHER_APP_CLUSTER" "\"\${PUSHER_APP_CLUSTER}\""
    
    echo -e "${GREEN}✅ Configuration Pusher ajoutée${NC}"
    
    # Nettoyer le cache Laravel
    echo -e "${YELLOW}Nettoyage du cache...${NC}"
    cd "$APP_PATH"
    php artisan config:clear > /dev/null 2>&1
    php artisan config:cache > /dev/null 2>&1
    echo -e "${GREEN}✅ Cache nettoyé${NC}"
    echo ""
done

echo -e "${GREEN}=========================================="
echo "  ✅ CONFIGURATION TERMINÉE"
echo "==========================================${NC}"
echo ""
echo -e "${YELLOW}Prochaines étapes:${NC}"
echo ""
echo "1. Vérifier la configuration:"
echo "   cd /var/www/administration"
echo "   php artisan tinker"
echo "   >>> config('broadcasting.connections.pusher')"
echo ""
echo "2. Tester une notification:"
echo "   php artisan tinker"
echo '   >>> $user = App\Models\User::first();'
echo '   >>> $user->notify(new App\Notifications\GenericNotification("Test", "Pusher works!", "success"));'
echo ""
echo "3. Ouvrir Pusher Debug Console:"
echo "   https://dashboard.pusher.com → Debug Console"
echo ""
echo -e "${BLUE}Configuration dans .env:${NC}"
echo "  BROADCAST_CONNECTION=pusher"
echo "  PUSHER_APP_ID=$PUSHER_APP_ID"
echo "  PUSHER_APP_KEY=$PUSHER_KEY"
echo "  PUSHER_APP_CLUSTER=$PUSHER_CLUSTER"
echo ""
