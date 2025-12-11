#!/bin/bash

# ========================================================================
# Script de Configuration Pusher pour Serveur de Production
# À exécuter sur: mgsmg@nl1-tr102
# ========================================================================

echo "========================================="
echo "Configuration Pusher - Serveur Production"
echo "========================================="
echo ""

# Demander les credentials Pusher
echo "Entrez vos credentials Pusher Cloud:"
echo "(Créez un compte sur https://pusher.com si ce n'est pas fait)"
echo ""
read -p "Pusher App ID: " APP_ID
read -p "Pusher Key: " APP_KEY
read -p "Pusher Secret: " APP_SECRET
read -p "Pusher Cluster (eu par défaut): " APP_CLUSTER
APP_CLUSTER=${APP_CLUSTER:-eu}

echo ""
echo "Configuration des 3 applications..."
echo ""

# Fonction pour configurer un .env
configure_env() {
    local ENV_FILE=$1
    local APP_NAME=$2
    
    echo "→ $APP_NAME..."
    
    if [ ! -f "$ENV_FILE" ]; then
        echo "  ✗ Fichier .env non trouvé: $ENV_FILE"
        return
    fi
    
    # Backup du fichier original
    cp "$ENV_FILE" "${ENV_FILE}.backup-$(date +%Y%m%d-%H%M%S)"
    
    # Mettre à jour ou ajouter PUSHER_APP_ID
    if grep -q "^PUSHER_APP_ID=" "$ENV_FILE"; then
        sed -i "s|^PUSHER_APP_ID=.*|PUSHER_APP_ID=$APP_ID|" "$ENV_FILE"
    else
        echo "" >> "$ENV_FILE"
        echo "# Pusher Configuration" >> "$ENV_FILE"
        echo "PUSHER_APP_ID=$APP_ID" >> "$ENV_FILE"
    fi
    
    # PUSHER_APP_KEY
    if grep -q "^PUSHER_APP_KEY=" "$ENV_FILE"; then
        sed -i "s|^PUSHER_APP_KEY=.*|PUSHER_APP_KEY=$APP_KEY|" "$ENV_FILE"
    else
        echo "PUSHER_APP_KEY=$APP_KEY" >> "$ENV_FILE"
    fi
    
    # PUSHER_APP_SECRET
    if grep -q "^PUSHER_APP_SECRET=" "$ENV_FILE"; then
        sed -i "s|^PUSHER_APP_SECRET=.*|PUSHER_APP_SECRET=$APP_SECRET|" "$ENV_FILE"
    else
        echo "PUSHER_APP_SECRET=$APP_SECRET" >> "$ENV_FILE"
    fi
    
    # PUSHER_APP_CLUSTER
    if grep -q "^PUSHER_APP_CLUSTER=" "$ENV_FILE"; then
        sed -i "s|^PUSHER_APP_CLUSTER=.*|PUSHER_APP_CLUSTER=$APP_CLUSTER|" "$ENV_FILE"
    else
        echo "PUSHER_APP_CLUSTER=$APP_CLUSTER" >> "$ENV_FILE"
    fi
    
    # VITE_PUSHER_APP_KEY
    if grep -q "^VITE_PUSHER_APP_KEY=" "$ENV_FILE"; then
        sed -i 's|^VITE_PUSHER_APP_KEY=.*|VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"|' "$ENV_FILE"
    else
        echo "" >> "$ENV_FILE"
        echo 'VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"' >> "$ENV_FILE"
    fi
    
    # VITE_PUSHER_APP_CLUSTER
    if grep -q "^VITE_PUSHER_APP_CLUSTER=" "$ENV_FILE"; then
        sed -i 's|^VITE_PUSHER_APP_CLUSTER=.*|VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"|' "$ENV_FILE"
    else
        echo 'VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"' >> "$ENV_FILE"
    fi
    
    echo "  ✓ Configuré (backup créé)"
}

# Configurer les 3 applications
configure_env "$HOME/administration.mgs.mg/.env" "Administration"
configure_env "$HOME/commercial.mgs.mg/.env" "Commercial"
configure_env "$HOME/debours.mgs.mg/.env" "Debours (Gestion-Dossier)"

echo ""
echo "========================================="
echo "✅ Configuration Pusher terminée !"
echo "========================================="
echo ""

echo "Vérification - Administration:"
if [ -f "$HOME/administration.mgs.mg/.env" ]; then
    grep "^PUSHER_" "$HOME/administration.mgs.mg/.env" | head -4
else
    echo "Fichier .env non trouvé"
fi

echo ""
echo "========================================="
echo "Prochaines étapes:"
echo "========================================="
echo ""
echo "1. Nettoyer les caches Laravel:"
echo "   cd ~/administration.mgs.mg && php artisan config:clear"
echo "   cd ~/commercial.mgs.mg && php artisan config:clear"
echo "   cd ~/debours.mgs.mg && php artisan config:clear"
echo ""
echo "2. Compiler les assets (si nécessaire):"
echo "   cd ~/administration.mgs.mg && npm run build"
echo "   cd ~/commercial.mgs.mg && npm run build"
echo "   cd ~/debours.mgs.mg && npm run build"
echo ""
echo "3. Tester: https://administration.mgs.mg"
echo ""
