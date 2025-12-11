#!/bin/bash

# ========================================================================
# Installation et Configuration SOKETI - Environnement LOCAL
# ========================================================================

echo "========================================="
echo "Installation Soketi - LOCAL"
echo "========================================="
echo ""

# 1. Installer Soketi globalement
echo "📦 Installation de Soketi..."
npm install -g @soketi/soketi

# 2. Créer le fichier de configuration
echo "📝 Création du fichier soketi.json..."
cat > /var/www/soketi.json << 'EOF'
{
  "debug": true,
  "host": "0.0.0.0",
  "port": 6001,
  "appManager.array.apps": [
    {
      "id": "mgs-app",
      "key": "mgs-local-key-2025",
      "secret": "mgs-local-secret-2025",
      "maxConnections": 100,
      "enableUserAuthentication": true,
      "enableClientMessages": true,
      "enableStatistics": true
    }
  ]
}
EOF

echo "✅ soketi.json créé dans /var/www/"
echo ""

# 3. Configuration des .env pour les 3 applications
echo "⚙️  Configuration des fichiers .env..."
echo ""

# Variables Soketi
APP_ID="mgs-app"
APP_KEY="mgs-local-key-2025"
APP_SECRET="mgs-local-secret-2025"
APP_CLUSTER="mt1"
APP_HOST="127.0.0.1"
APP_PORT="6001"
APP_SCHEME="http"

# Fonction pour configurer un .env
configure_env() {
    local ENV_FILE=$1
    local APP_NAME=$2
    
    echo "  → $APP_NAME..."
    
    # Mettre à jour ou ajouter PUSHER_*
    if grep -q "^PUSHER_APP_ID=" "$ENV_FILE"; then
        sed -i "s|^PUSHER_APP_ID=.*|PUSHER_APP_ID=$APP_ID|" "$ENV_FILE"
        sed -i "s|^PUSHER_APP_KEY=.*|PUSHER_APP_KEY=$APP_KEY|" "$ENV_FILE"
        sed -i "s|^PUSHER_APP_SECRET=.*|PUSHER_APP_SECRET=$APP_SECRET|" "$ENV_FILE"
        sed -i "s|^PUSHER_APP_CLUSTER=.*|PUSHER_APP_CLUSTER=$APP_CLUSTER|" "$ENV_FILE"
    else
        cat >> "$ENV_FILE" << ENVEOF

# Pusher/Soketi Configuration
PUSHER_APP_ID=$APP_ID
PUSHER_APP_KEY=$APP_KEY
PUSHER_APP_SECRET=$APP_SECRET
PUSHER_APP_CLUSTER=$APP_CLUSTER
ENVEOF
    fi
    
    # Ajouter les variables Soketi si elles n'existent pas
    if ! grep -q "^PUSHER_HOST=" "$ENV_FILE"; then
        cat >> "$ENV_FILE" << ENVEOF
PUSHER_HOST=$APP_HOST
PUSHER_PORT=$APP_PORT
PUSHER_SCHEME=$APP_SCHEME

# Vite variables for frontend
VITE_PUSHER_APP_KEY="\${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="\${PUSHER_APP_CLUSTER}"
VITE_PUSHER_HOST=$APP_HOST
VITE_PUSHER_PORT=$APP_PORT
VITE_PUSHER_SCHEME=$APP_SCHEME
ENVEOF
    else
        # Mettre à jour les valeurs existantes
        sed -i "s|^PUSHER_HOST=.*|PUSHER_HOST=$APP_HOST|" "$ENV_FILE"
        sed -i "s|^PUSHER_PORT=.*|PUSHER_PORT=$APP_PORT|" "$ENV_FILE"
        sed -i "s|^PUSHER_SCHEME=.*|PUSHER_SCHEME=$APP_SCHEME|" "$ENV_FILE"
        sed -i "s|^VITE_PUSHER_HOST=.*|VITE_PUSHER_HOST=$APP_HOST|" "$ENV_FILE"
        sed -i "s|^VITE_PUSHER_PORT=.*|VITE_PUSHER_PORT=$APP_PORT|" "$ENV_FILE"
        sed -i "s|^VITE_PUSHER_SCHEME=.*|VITE_PUSHER_SCHEME=$APP_SCHEME|" "$ENV_FILE"
    fi
}

# Configurer les 3 applications
configure_env "/var/www/administration/.env" "Administration"
configure_env "/var/www/commercial/.env" "Commercial"
configure_env "/var/www/gestion-dossier/.env" "Gestion-Dossier"

echo ""
echo "✅ Configuration terminée !"
echo ""
echo "========================================="
echo "📋 Configuration appliquée :"
echo "========================================="
echo ""
echo "Soketi Server:"
echo "  Host: 0.0.0.0 (écoute sur toutes les interfaces)"
echo "  Port: 6001"
echo "  Config: /var/www/soketi.json"
echo ""
echo "Applications (3x):"
echo "  PUSHER_HOST: 127.0.0.1"
echo "  PUSHER_PORT: 6001"
echo "  PUSHER_SCHEME: http"
echo ""
echo "========================================="
echo "🚀 Pour démarrer Soketi :"
echo "========================================="
echo ""
echo "cd /var/www && soketi start --config=soketi.json"
echo ""
echo "Ou en arrière-plan :"
echo "cd /var/www && nohup soketi start --config=soketi.json > soketi.log 2>&1 &"
echo ""
echo "========================================="
echo "🧪 Tester la connexion :"
echo "========================================="
echo ""
echo "curl http://127.0.0.1:6001/health"
echo ""
