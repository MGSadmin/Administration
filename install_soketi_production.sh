#!/bin/bash

#=============================================================================
# INSTALLATION SOKETI - SERVEUR PRODUCTION (Node.js v10)
#=============================================================================
# Pour: mgsmg@nl1-tr102 (hébergement mutualisé sans sudo)
# Compatible: Node.js v10.24.0
#=============================================================================

set -e

echo "=========================================="
echo "  INSTALLATION SOKETI - PRODUCTION"
echo "=========================================="
echo ""

# Couleurs
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Vérifier Node.js
echo -e "${YELLOW}Vérification de Node.js...${NC}"
NODE_VERSION=$(node -v 2>/dev/null || echo "non installé")
NPM_VERSION=$(npm -v 2>/dev/null || echo "non installé")

echo "Node.js: $NODE_VERSION"
echo "npm: $NPM_VERSION"
echo ""

if [[ "$NODE_VERSION" == "non installé" ]]; then
    echo -e "${RED}❌ Node.js n'est pas installé${NC}"
    exit 1
fi

# Dossier d'installation local (sans sudo)
INSTALL_DIR="$HOME/soketi"
BIN_DIR="$HOME/bin"

echo -e "${YELLOW}Dossier d'installation: $INSTALL_DIR${NC}"
echo ""

# Créer les dossiers
mkdir -p "$INSTALL_DIR"
mkdir -p "$BIN_DIR"

# Aller dans le dossier
cd "$INSTALL_DIR"

# Initialiser package.json si nécessaire
if [ ! -f "package.json" ]; then
    echo -e "${YELLOW}Création de package.json...${NC}"
    cat > package.json <<EOF
{
  "name": "soketi-server",
  "version": "1.0.0",
  "description": "Soketi WebSocket server for MGS applications",
  "private": true,
  "dependencies": {}
}
EOF
fi

# Installer Soketi version compatible avec Node.js v10
echo -e "${YELLOW}Installation de Soketi (version compatible Node v10)...${NC}"
echo "Cela peut prendre quelques minutes..."
echo ""

# Soketi 0.x était compatible avec Node v10
# On essaye d'installer la dernière version compatible
npm install @soketi/soketi@0.38.0 --save 2>&1 | grep -v "npm WARN"

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Soketi installé avec succès${NC}"
else
    echo -e "${RED}❌ Erreur lors de l'installation${NC}"
    echo "Tentative avec une version encore plus ancienne..."
    npm install @soketi/soketi@0.30.0 --save
fi

# Créer le fichier de configuration
echo -e "${YELLOW}Création du fichier de configuration...${NC}"
cat > config.json <<EOF
{
  "debug": false,
  "host": "0.0.0.0",
  "port": 6001,
  "appManager.array.apps": [
    {
      "id": "mgs-app",
      "key": "mgs-app-key",
      "secret": "mgs-app-secret",
      "maxConnections": 100,
      "enableClientMessages": true,
      "enabled": true,
      "maxBackendEventsPerSecond": 100,
      "maxClientEventsPerSecond": 100,
      "maxReadRequestsPerSecond": 100
    }
  ],
  "cors.credentials": true,
  "cors.origin": [
    "https://administration.mgs.mg",
    "https://commercial.mgs.mg",
    "https://debours.mgs.mg"
  ],
  "cors.methods": ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
  "cors.allowedHeaders": ["Origin", "Content-Type", "Accept", "Authorization", "X-Requested-With", "X-CSRF-Token", "X-Socket-Id"]
}
EOF

echo -e "${GREEN}✅ Configuration créée${NC}"
echo ""

# Créer le script de démarrage
echo -e "${YELLOW}Création du script de démarrage...${NC}"
cat > "$BIN_DIR/start-soketi.sh" <<'EOFSTART'
#!/bin/bash

SOKETI_DIR="$HOME/soketi"
LOG_FILE="$SOKETI_DIR/soketi.log"
PID_FILE="$SOKETI_DIR/soketi.pid"

cd "$SOKETI_DIR"

# Vérifier si Soketi est déjà en cours d'exécution
if [ -f "$PID_FILE" ]; then
    PID=$(cat "$PID_FILE")
    if ps -p $PID > /dev/null 2>&1; then
        echo "Soketi est déjà en cours d'exécution (PID: $PID)"
        exit 0
    else
        # PID file existe mais processus non actif
        rm -f "$PID_FILE"
    fi
fi

# Démarrer Soketi en arrière-plan
echo "Démarrage de Soketi..."
nohup node_modules/.bin/soketi start --config=config.json > "$LOG_FILE" 2>&1 &
echo $! > "$PID_FILE"

sleep 2

# Vérifier que le processus tourne
if ps -p $(cat "$PID_FILE") > /dev/null 2>&1; then
    echo "✅ Soketi démarré avec succès (PID: $(cat $PID_FILE))"
    echo "📝 Logs: $LOG_FILE"
else
    echo "❌ Échec du démarrage de Soketi"
    echo "Voir les logs: $LOG_FILE"
    rm -f "$PID_FILE"
    exit 1
fi
EOFSTART

chmod +x "$BIN_DIR/start-soketi.sh"

# Créer le script d'arrêt
cat > "$BIN_DIR/stop-soketi.sh" <<'EOFSTOP'
#!/bin/bash

PID_FILE="$HOME/soketi/soketi.pid"

if [ ! -f "$PID_FILE" ]; then
    echo "Soketi n'est pas en cours d'exécution"
    exit 0
fi

PID=$(cat "$PID_FILE")
if ps -p $PID > /dev/null 2>&1; then
    echo "Arrêt de Soketi (PID: $PID)..."
    kill $PID
    sleep 2
    
    # Vérifier si le processus est toujours actif
    if ps -p $PID > /dev/null 2>&1; then
        echo "Processus résistant, force kill..."
        kill -9 $PID
    fi
    
    rm -f "$PID_FILE"
    echo "✅ Soketi arrêté"
else
    echo "Processus $PID n'existe pas"
    rm -f "$PID_FILE"
fi
EOFSTOP

chmod +x "$BIN_DIR/stop-soketi.sh"

# Créer le script de statut
cat > "$BIN_DIR/status-soketi.sh" <<'EOFSTATUS'
#!/bin/bash

PID_FILE="$HOME/soketi/soketi.pid"
LOG_FILE="$HOME/soketi/soketi.log"

if [ -f "$PID_FILE" ]; then
    PID=$(cat "$PID_FILE")
    if ps -p $PID > /dev/null 2>&1; then
        echo "✅ Soketi est en cours d'exécution (PID: $PID)"
        echo ""
        echo "Dernières lignes du log:"
        tail -n 20 "$LOG_FILE"
    else
        echo "❌ Soketi n'est pas en cours d'exécution (PID file obsolète)"
        rm -f "$PID_FILE"
    fi
else
    echo "❌ Soketi n'est pas en cours d'exécution"
fi
EOFSTATUS

chmod +x "$BIN_DIR/status-soketi.sh"

# Ajouter ~/bin au PATH si nécessaire
if ! echo "$PATH" | grep -q "$HOME/bin"; then
    echo ""
    echo -e "${YELLOW}⚠️  Ajoutez ~/bin à votre PATH${NC}"
    echo "Ajoutez cette ligne à ~/.bashrc ou ~/.bash_profile:"
    echo ""
    echo -e "${GREEN}export PATH=\"\$HOME/bin:\$PATH\"${NC}"
    echo ""
    echo "Puis rechargez: source ~/.bashrc"
fi

echo ""
echo -e "${GREEN}=========================================="
echo "  ✅ INSTALLATION TERMINÉE"
echo "==========================================${NC}"
echo ""
echo "📁 Dossier: $INSTALL_DIR"
echo ""
echo "🚀 Commandes disponibles:"
echo "  start-soketi.sh   - Démarrer Soketi"
echo "  stop-soketi.sh    - Arrêter Soketi"
echo "  status-soketi.sh  - Voir le statut"
echo ""
echo "📝 Configuration Soketi:"
echo "  Host: 0.0.0.0"
echo "  Port: 6001"
echo "  App ID: mgs-app"
echo "  Key: mgs-app-key"
echo "  Secret: mgs-app-secret"
echo ""
echo "⚙️  Configuration Laravel (.env):"
echo "  BROADCAST_CONNECTION=pusher"
echo "  PUSHER_APP_ID=mgs-app"
echo "  PUSHER_APP_KEY=mgs-app-key"
echo "  PUSHER_APP_SECRET=mgs-app-secret"
echo "  PUSHER_HOST=127.0.0.1"
echo "  PUSHER_PORT=6001"
echo "  PUSHER_SCHEME=http"
echo "  PUSHER_APP_CLUSTER=mt1"
echo ""
echo "  VITE_PUSHER_APP_KEY=mgs-app-key"
echo "  VITE_PUSHER_HOST=127.0.0.1"
echo "  VITE_PUSHER_PORT=6001"
echo "  VITE_PUSHER_SCHEME=http"
echo "  VITE_PUSHER_APP_CLUSTER=mt1"
echo ""
echo "Pour démarrer maintenant:"
echo -e "${GREEN}$BIN_DIR/start-soketi.sh${NC}"
echo ""
