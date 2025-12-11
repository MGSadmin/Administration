#!/bin/bash

# ========================================================================
# Mise à jour Node.js et Installation Soketi
# ========================================================================

echo "========================================="
echo "Diagnostic de l'environnement"
echo "========================================="
echo ""

NODE_VERSION=$(node -v 2>/dev/null || echo "non installé")
echo "Node.js actuel : $NODE_VERSION"
echo "Node.js requis : ≥ v16.0.0"
echo ""

# Vérifier la version de Node.js
if [[ "$NODE_VERSION" =~ ^v([0-9]+) ]]; then
    VERSION_NUM=${BASH_REMATCH[1]}
    if [ "$VERSION_NUM" -lt 16 ]; then
        echo "⚠️  Node.js $NODE_VERSION est trop ancien pour Soketi"
        echo ""
        echo "========================================="
        echo "OPTION 1 : Mettre à jour Node.js (RECOMMANDÉ)"
        echo "========================================="
        echo ""
        read -p "Voulez-vous mettre à jour Node.js vers v20 LTS ? (o/n) : " UPDATE_NODE
        
        if [ "$UPDATE_NODE" = "o" ] || [ "$UPDATE_NODE" = "O" ]; then
            echo ""
            echo "📦 Installation de Node.js v20 LTS via NodeSource..."
            
            # Installer curl si nécessaire
            if ! command -v curl &> /dev/null; then
                echo "Installation de curl..."
                sudo apt-get update
                sudo apt-get install -y curl
            fi
            
            # Ajouter le dépôt NodeSource pour Node.js 20.x
            echo "Ajout du dépôt NodeSource..."
            curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
            
            # Installer Node.js
            echo "Installation de Node.js 20.x..."
            sudo apt-get install -y nodejs
            
            echo ""
            echo "✅ Node.js mis à jour !"
            NODE_VERSION=$(node -v)
            echo "Nouvelle version : $NODE_VERSION"
            echo ""
        else
            echo ""
            echo "========================================="
            echo "OPTION 2 : Utiliser Pusher Cloud"
            echo "========================================="
            echo ""
            echo "Si vous ne pouvez pas mettre à jour Node.js,"
            echo "utilisez Pusher Cloud (gratuit jusqu'à 200k messages/jour)"
            echo ""
            echo "Exécutez : /var/www/administration/configure_pusher.sh"
            echo "Et choisissez l'option 1 (Pusher Cloud)"
            echo ""
            exit 1
        fi
    else
        echo "✅ Node.js $NODE_VERSION est compatible"
        echo ""
    fi
else
    echo "❌ Node.js n'est pas installé"
    echo ""
    read -p "Voulez-vous installer Node.js v20 LTS ? (o/n) : " INSTALL_NODE
    
    if [ "$INSTALL_NODE" = "o" ] || [ "$INSTALL_NODE" = "O" ]; then
        echo "📦 Installation de Node.js v20 LTS..."
        
        sudo apt-get update
        sudo apt-get install -y curl
        curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
        sudo apt-get install -y nodejs
        
        echo "✅ Node.js installé !"
        node -v
        echo ""
    else
        exit 1
    fi
fi

# Installer Soketi
echo "========================================="
echo "Installation de Soketi"
echo "========================================="
echo ""

echo "📦 Installation globale de @soketi/soketi..."
sudo npm install -g @soketi/soketi

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Soketi installé avec succès !"
    echo ""
    soketi --version
    echo ""
    
    # Créer le fichier de configuration
    echo "📝 Création du fichier de configuration..."
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
    
    echo "✅ Configuration créée : /var/www/soketi.json"
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
    
    read -p "Voulez-vous configurer les .env maintenant ? (o/n) : " CONFIG_ENV
    if [ "$CONFIG_ENV" = "o" ] || [ "$CONFIG_ENV" = "O" ]; then
        /var/www/administration/setup_soketi_local.sh
    fi
    
else
    echo ""
    echo "❌ Erreur lors de l'installation de Soketi"
    echo ""
    echo "Alternative : Utilisez Pusher Cloud"
    echo "Exécutez : /var/www/administration/configure_pusher.sh"
    echo ""
    exit 1
fi
