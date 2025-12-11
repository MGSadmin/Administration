#!/bin/bash

#=============================================================================
# CONFIGURATION SOKETI - TOUTES LES APPLICATIONS
#=============================================================================
# Configure Soketi dans les 3 applications Laravel
# Serveur de production: mgsmg@nl1-tr102
#=============================================================================

set -e

# Couleurs
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}=========================================="
echo "  CONFIGURATION SOKETI - MGS"
echo "==========================================${NC}"
echo ""

# Chemins des applications
APPS=(
    "$HOME/administration.mgs.mg"
    "$HOME/commercial.mgs.mg"
    "$HOME/debours.mgs.mg"
)

APP_NAMES=(
    "Administration"
    "Commercial"
    "Debours"
)

# Configuration Soketi
SOKETI_APP_ID="mgs-app"
SOKETI_APP_KEY="mgs-app-key"
SOKETI_APP_SECRET="mgs-app-secret"
SOKETI_HOST="127.0.0.1"
SOKETI_PORT="6001"
SOKETI_SCHEME="http"
SOKETI_CLUSTER="mt1"

echo -e "${YELLOW}Configuration Soketi:${NC}"
echo "  App ID: $SOKETI_APP_ID"
echo "  Key: $SOKETI_APP_KEY"
echo "  Host: $SOKETI_HOST"
echo "  Port: $SOKETI_PORT"
echo ""

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
    
    # Configurer Pusher/Soketi (backend)
    update_env_var "$ENV_FILE" "PUSHER_APP_ID" "$SOKETI_APP_ID"
    update_env_var "$ENV_FILE" "PUSHER_APP_KEY" "$SOKETI_APP_KEY"
    update_env_var "$ENV_FILE" "PUSHER_APP_SECRET" "$SOKETI_APP_SECRET"
    update_env_var "$ENV_FILE" "PUSHER_HOST" "$SOKETI_HOST"
    update_env_var "$ENV_FILE" "PUSHER_PORT" "$SOKETI_PORT"
    update_env_var "$ENV_FILE" "PUSHER_SCHEME" "$SOKETI_SCHEME"
    update_env_var "$ENV_FILE" "PUSHER_APP_CLUSTER" "$SOKETI_CLUSTER"
    
    # Configurer Vite (frontend)
    update_env_var "$ENV_FILE" "VITE_PUSHER_APP_KEY" "$SOKETI_APP_KEY"
    update_env_var "$ENV_FILE" "VITE_PUSHER_HOST" "$SOKETI_HOST"
    update_env_var "$ENV_FILE" "VITE_PUSHER_PORT" "$SOKETI_PORT"
    update_env_var "$ENV_FILE" "VITE_PUSHER_SCHEME" "$SOKETI_SCHEME"
    update_env_var "$ENV_FILE" "VITE_PUSHER_APP_CLUSTER" "$SOKETI_CLUSTER"
    
    echo -e "${GREEN}✅ Configuration Soketi ajoutée${NC}"
    echo ""
done

echo -e "${GREEN}=========================================="
echo "  ✅ CONFIGURATION TERMINÉE"
echo "==========================================${NC}"
echo ""
echo -e "${YELLOW}Prochaines étapes:${NC}"
echo ""
echo "1. Nettoyer les caches Laravel:"
echo "   cd ~/administration.mgs.mg && php artisan config:clear"
echo "   cd ~/commercial.mgs.mg && php artisan config:clear"
echo "   cd ~/debours.mgs.mg && php artisan config:clear"
echo ""
echo "2. Vérifier que Soketi est démarré:"
echo "   status-soketi.sh"
echo ""
echo "3. Si Soketi n'est pas démarré:"
echo "   start-soketi.sh"
echo ""
echo "4. Recompiler les assets (si modifications frontend):"
echo "   cd ~/administration.mgs.mg && npm run build"
echo "   cd ~/commercial.mgs.mg && npm run build"
echo "   cd ~/debours.mgs.mg && npm run build"
echo ""
echo -e "${BLUE}Configuration Soketi dans .env:${NC}"
echo "  BROADCAST_CONNECTION=pusher"
echo "  PUSHER_APP_ID=$SOKETI_APP_ID"
echo "  PUSHER_APP_KEY=$SOKETI_APP_KEY"
echo "  PUSHER_HOST=$SOKETI_HOST"
echo "  PUSHER_PORT=$SOKETI_PORT"
echo ""
