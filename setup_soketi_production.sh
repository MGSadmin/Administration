#!/bin/bash

# ========================================================================
# Installation et Configuration SOKETI - Environnement DISTANT (Production)
# ========================================================================

echo "========================================="
echo "Installation Soketi - SERVEUR DISTANT"
echo "========================================="
echo ""

# Demander l'IP du serveur Administration
read -p "Entrez l'IP du serveur Administration (ex: 192.168.1.10 ou administration.mgs.mg): " ADMIN_SERVER
ADMIN_SERVER=${ADMIN_SERVER:-127.0.0.1}

echo ""
echo "Configuration pour :"
echo "  Serveur Administration: $ADMIN_SERVER"
echo ""

# 1. Installer Soketi sur le serveur Administration
echo "📦 Installation de Soketi sur le serveur Administration..."
echo ""
echo "⚠️  Exécutez cette commande sur le serveur Administration:"
echo "    ssh root@$ADMIN_SERVER"
echo "    npm install -g @soketi/soketi"
echo ""
read -p "Appuyez sur ENTRÉE une fois Soketi installé sur $ADMIN_SERVER..."

# 2. Créer le fichier de configuration Soketi
echo ""
echo "📝 Création du fichier soketi.json..."

cat > /tmp/soketi.json << 'EOF'
{
  "debug": false,
  "host": "0.0.0.0",
  "port": 6001,
  "appManager.array.apps": [
    {
      "id": "mgs-production",
      "key": "mgs-prod-key-secure-2025",
      "secret": "mgs-prod-secret-secure-2025-change-me",
      "maxConnections": 1000,
      "enableUserAuthentication": true,
      "enableClientMessages": true,
      "enableStatistics": true
    }
  ],
  "cors": {
    "origin": ["*"],
    "methods": ["GET", "POST"],
    "allowedHeaders": ["Origin", "Content-Type", "Accept", "Authorization"],
    "credentials": true
  }
}
EOF

echo "✅ soketi.json créé dans /tmp/"
echo ""
echo "📤 Copier le fichier sur le serveur Administration:"
echo "    scp /tmp/soketi.json root@$ADMIN_SERVER:/var/www/soketi.json"
echo ""
read -p "Appuyez sur ENTRÉE une fois le fichier copié..."

# 3. Configuration des .env
echo ""
echo "⚙️  Configuration des fichiers .env..."
echo ""

# Variables Soketi Production
APP_ID="mgs-production"
APP_KEY="mgs-prod-key-secure-2025"
APP_SECRET="mgs-prod-secret-secure-2025-change-me"
APP_CLUSTER="mt1"
APP_HOST="$ADMIN_SERVER"
APP_PORT="6001"
APP_SCHEME="http"  # Utiliser https si vous avez un certificat SSL

# Demander si SSL/TLS est activé
read -p "Utilisez-vous HTTPS/SSL ? (o/n) : " USE_SSL
if [ "$USE_SSL" = "o" ] || [ "$USE_SSL" = "O" ]; then
    APP_SCHEME="https"
    APP_PORT="443"
    echo "✅ Configuration SSL activée"
fi

echo ""
echo "⚠️  SÉCURITÉ: Changez le secret avant la production!"
read -p "Voulez-vous générer un secret aléatoire maintenant ? (o/n) : " GEN_SECRET
if [ "$GEN_SECRET" = "o" ] || [ "$GEN_SECRET" = "O" ]; then
    APP_SECRET=$(openssl rand -base64 32 | tr -d "=+/" | cut -c1-32)
    echo "✅ Nouveau secret généré: $APP_SECRET"
    # Mettre à jour le fichier soketi.json
    sed -i "s|mgs-prod-secret-secure-2025-change-me|$APP_SECRET|" /tmp/soketi.json
    echo "⚠️  N'oubliez pas de re-copier soketi.json sur le serveur!"
fi

echo ""

# Fonction pour configurer un .env distant
configure_env_remote() {
    local ENV_FILE=$1
    local APP_NAME=$2
    
    echo "  → $APP_NAME..."
    
    if [ -f "$ENV_FILE" ]; then
        # Mettre à jour ou ajouter PUSHER_*
        if grep -q "^PUSHER_APP_ID=" "$ENV_FILE"; then
            sed -i "s|^PUSHER_APP_ID=.*|PUSHER_APP_ID=$APP_ID|" "$ENV_FILE"
            sed -i "s|^PUSHER_APP_KEY=.*|PUSHER_APP_KEY=$APP_KEY|" "$ENV_FILE"
            sed -i "s|^PUSHER_APP_SECRET=.*|PUSHER_APP_SECRET=$APP_SECRET|" "$ENV_FILE"
            sed -i "s|^PUSHER_APP_CLUSTER=.*|PUSHER_APP_CLUSTER=$APP_CLUSTER|" "$ENV_FILE"
        else
            cat >> "$ENV_FILE" << ENVEOF

# Pusher/Soketi Configuration - Production
PUSHER_APP_ID=$APP_ID
PUSHER_APP_KEY=$APP_KEY
PUSHER_APP_SECRET=$APP_SECRET
PUSHER_APP_CLUSTER=$APP_CLUSTER
ENVEOF
        fi
        
        # Ajouter les variables Soketi
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
            sed -i "s|^PUSHER_HOST=.*|PUSHER_HOST=$APP_HOST|" "$ENV_FILE"
            sed -i "s|^PUSHER_PORT=.*|PUSHER_PORT=$APP_PORT|" "$ENV_FILE"
            sed -i "s|^PUSHER_SCHEME=.*|PUSHER_SCHEME=$APP_SCHEME|" "$ENV_FILE"
            sed -i "s|^VITE_PUSHER_HOST=.*|VITE_PUSHER_HOST=$APP_HOST|" "$ENV_FILE"
            sed -i "s|^VITE_PUSHER_PORT=.*|VITE_PUSHER_PORT=$APP_PORT|" "$ENV_FILE"
            sed -i "s|^VITE_PUSHER_SCHEME=.*|VITE_PUSHER_SCHEME=$APP_SCHEME|" "$ENV_FILE"
        fi
    else
        echo "    ⚠️  Fichier $ENV_FILE non trouvé"
    fi
}

# Configurer les applications (selon où elles sont)
echo "Configuration des .env..."
echo ""
echo "Si les applications sont sur le MÊME serveur que Administration:"
configure_env_remote "/var/www/administration/.env" "Administration"
configure_env_remote "/var/www/commercial/.env" "Commercial"
configure_env_remote "/var/www/gestion-dossier/.env" "Gestion-Dossier"

echo ""
echo "Si les applications sont sur des SERVEURS DIFFÉRENTS:"
echo "  Copiez cette configuration dans chaque .env:"
echo ""
cat << ENVEOF
# Pusher/Soketi Configuration - Production
PUSHER_APP_ID=$APP_ID
PUSHER_APP_KEY=$APP_KEY
PUSHER_APP_SECRET=$APP_SECRET
PUSHER_APP_CLUSTER=$APP_CLUSTER
PUSHER_HOST=$APP_HOST
PUSHER_PORT=$APP_PORT
PUSHER_SCHEME=$APP_SCHEME

VITE_PUSHER_APP_KEY="\${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="\${PUSHER_APP_CLUSTER}"
VITE_PUSHER_HOST=$APP_HOST
VITE_PUSHER_PORT=$APP_PORT
VITE_PUSHER_SCHEME=$APP_SCHEME
ENVEOF

echo ""
echo "========================================="
echo "✅ Configuration terminée !"
echo "========================================="
echo ""
echo "📋 Résumé de la configuration :"
echo "========================================="
echo ""
echo "Serveur Soketi: $ADMIN_SERVER:$APP_PORT"
echo "Schéma: $APP_SCHEME"
echo "App ID: $APP_ID"
echo "App Key: $APP_KEY"
echo "App Secret: $APP_SECRET (⚠️  GARDEZ SECRET!)"
echo ""
echo "========================================="
echo "🚀 Démarrer Soketi sur $ADMIN_SERVER :"
echo "========================================="
echo ""
echo "1. SSH vers le serveur:"
echo "   ssh root@$ADMIN_SERVER"
echo ""
echo "2. Démarrer Soketi:"
echo "   cd /var/www && soketi start --config=soketi.json"
echo ""
echo "3. Ou avec systemd (recommandé pour production):"
echo ""
cat << 'SYSTEMD'
# Créer /etc/systemd/system/soketi.service

[Unit]
Description=Soketi WebSocket Server
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www
ExecStart=/usr/bin/soketi start --config=/var/www/soketi.json
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
SYSTEMD

echo ""
echo "   sudo systemctl daemon-reload"
echo "   sudo systemctl enable soketi"
echo "   sudo systemctl start soketi"
echo "   sudo systemctl status soketi"
echo ""
echo "========================================="
echo "🔒 FIREWALL - Ouvrir le port 6001 :"
echo "========================================="
echo ""
echo "Sur le serveur $ADMIN_SERVER, exécutez:"
echo ""
echo "# UFW (Ubuntu/Debian)"
echo "sudo ufw allow 6001/tcp"
echo ""
echo "# Firewalld (CentOS/RHEL)"
echo "sudo firewall-cmd --permanent --add-port=6001/tcp"
echo "sudo firewall-cmd --reload"
echo ""
echo "========================================="
echo "🧪 Tester la connexion :"
echo "========================================="
echo ""
echo "Depuis n'importe quel serveur:"
echo "curl http://$ADMIN_SERVER:6001/health"
echo ""
echo "Résultat attendu: {\"status\":\"ok\"}"
echo ""
