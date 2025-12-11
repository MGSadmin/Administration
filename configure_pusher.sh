#!/bin/bash

# ========================================================================
# Guide de Configuration PUSHER pour Laravel Multi-Sites
# ========================================================================

echo "========================================="
echo "Configuration PUSHER - 2 Options"
echo "========================================="
echo ""

cat << 'EOF'

┌─────────────────────────────────────────────────────────────────┐
│                  OPTION 1 : PUSHER CLOUD (Recommandé)           │
└─────────────────────────────────────────────────────────────────┘

Avantages :
  ✅ Gratuit jusqu'à 200 000 messages/jour
  ✅ Configuration en 5 minutes
  ✅ Pas de serveur à gérer
  ✅ Monitoring inclus

ÉTAPES :

1. Créer un compte sur https://pusher.com/signup
   - Email + Mot de passe
   - Vérifier l'email

2. Créer une application "MGS Multi-Sites"
   - Cliquer sur "Create app"
   - Nom : MGS Multi-Sites
   - Cluster : Europe (eu)
   - Choisir "Vanilla JS" comme frontend
   - Choisir "Laravel" comme backend

3. Récupérer les credentials (onglet "App Keys")
   app_id       : 123456 (exemple)
   key          : a1b2c3d4e5f6g7h8i9j0
   secret       : k1l2m3n4o5p6q7r8s9t0
   cluster      : eu

4. Copier ces valeurs dans vos 3 fichiers .env


┌─────────────────────────────────────────────────────────────────┐
│          OPTION 2 : SOKETI (Auto-hébergé, 100% gratuit)         │
└─────────────────────────────────────────────────────────────────┘

Avantages :
  ✅ Totalement gratuit (pas de limite)
  ✅ Contrôle total
  ✅ Pas de dépendance externe

Inconvénients :
  ⚠️  Nécessite un serveur qui tourne en permanence
  ⚠️  Configuration plus technique

ÉTAPES :

1. Installer Soketi globalement
   npm install -g @soketi/soketi

2. Créer le fichier de configuration
   (Voir ci-dessous pour le contenu)

3. Lancer le serveur
   soketi start

4. Configurer vos .env avec des valeurs personnalisées

EOF

echo ""
echo "========================================="
echo "Quelle option choisissez-vous ?"
echo "========================================="
echo ""
read -p "Tapez 1 pour Pusher Cloud, 2 pour Soketi : " choice

if [ "$choice" = "1" ]; then
    echo ""
    echo "┌─────────────────────────────────────────┐"
    echo "│  Configuration PUSHER CLOUD             │"
    echo "└─────────────────────────────────────────┘"
    echo ""
    echo "1. Ouvrez https://pusher.com/signup dans votre navigateur"
    echo "2. Créez un compte (gratuit)"
    echo "3. Créez une application 'MGS Multi-Sites'"
    echo "4. Copiez vos credentials depuis l'onglet 'App Keys'"
    echo ""
    read -p "Appuyez sur ENTRÉE une fois vos credentials récupérés..."
    echo ""
    read -p "App ID : " APP_ID
    read -p "Key : " APP_KEY
    read -p "Secret : " APP_SECRET
    read -p "Cluster (eu par défaut) : " APP_CLUSTER
    APP_CLUSTER=${APP_CLUSTER:-eu}
    
    echo ""
    echo "Configuration de vos 3 applications..."
    
    # Configuration ADMINISTRATION
    echo "  → Administration..."
    sed -i "s|^PUSHER_APP_ID=.*|PUSHER_APP_ID=$APP_ID|" /var/www/administration/.env
    sed -i "s|^PUSHER_APP_KEY=.*|PUSHER_APP_KEY=$APP_KEY|" /var/www/administration/.env
    sed -i "s|^PUSHER_APP_SECRET=.*|PUSHER_APP_SECRET=$APP_SECRET|" /var/www/administration/.env
    sed -i "s|^PUSHER_APP_CLUSTER=.*|PUSHER_APP_CLUSTER=$APP_CLUSTER|" /var/www/administration/.env
    sed -i "s|^VITE_PUSHER_APP_KEY=.*|VITE_PUSHER_APP_KEY=\"\${PUSHER_APP_KEY}\"|" /var/www/administration/.env
    sed -i "s|^VITE_PUSHER_APP_CLUSTER=.*|VITE_PUSHER_APP_CLUSTER=\"\${PUSHER_APP_CLUSTER}\"|" /var/www/administration/.env
    
    # Configuration COMMERCIAL
    echo "  → Commercial..."
    sed -i "s|^PUSHER_APP_ID=.*|PUSHER_APP_ID=$APP_ID|" /var/www/commercial/.env
    sed -i "s|^PUSHER_APP_KEY=.*|PUSHER_APP_KEY=$APP_KEY|" /var/www/commercial/.env
    sed -i "s|^PUSHER_APP_SECRET=.*|PUSHER_APP_SECRET=$APP_SECRET|" /var/www/commercial/.env
    sed -i "s|^PUSHER_APP_CLUSTER=.*|PUSHER_APP_CLUSTER=$APP_CLUSTER|" /var/www/commercial/.env
    sed -i "s|^VITE_PUSHER_APP_KEY=.*|VITE_PUSHER_APP_KEY=\"\${PUSHER_APP_KEY}\"|" /var/www/commercial/.env
    sed -i "s|^VITE_PUSHER_APP_CLUSTER=.*|VITE_PUSHER_APP_CLUSTER=\"\${PUSHER_APP_CLUSTER}\"|" /var/www/commercial/.env
    
    # Configuration GESTION-DOSSIER
    echo "  → Gestion-Dossier..."
    sed -i "s|^PUSHER_APP_ID=.*|PUSHER_APP_ID=$APP_ID|" /var/www/gestion-dossier/.env
    sed -i "s|^PUSHER_APP_KEY=.*|PUSHER_APP_KEY=$APP_KEY|" /var/www/gestion-dossier/.env
    sed -i "s|^PUSHER_APP_SECRET=.*|PUSHER_APP_SECRET=$APP_SECRET|" /var/www/gestion-dossier/.env
    sed -i "s|^PUSHER_APP_CLUSTER=.*|PUSHER_APP_CLUSTER=$APP_CLUSTER|" /var/www/gestion-dossier/.env
    sed -i "s|^VITE_PUSHER_APP_KEY=.*|VITE_PUSHER_APP_KEY=\"\${PUSHER_APP_KEY}\"|" /var/www/gestion-dossier/.env
    sed -i "s|^VITE_PUSHER_APP_CLUSTER=.*|VITE_PUSHER_APP_CLUSTER=\"\${PUSHER_APP_CLUSTER}\"|" /var/www/gestion-dossier/.env
    
    echo ""
    echo "✅ Configuration Pusher Cloud terminée !"
    echo ""
    echo "Vérification :"
    echo "──────────────────────────────────────"
    grep "^PUSHER_" /var/www/administration/.env
    echo ""
    
elif [ "$choice" = "2" ]; then
    echo ""
    echo "┌─────────────────────────────────────────┐"
    echo "│  Configuration SOKETI (Auto-hébergé)    │"
    echo "└─────────────────────────────────────────┘"
    echo ""
    
    # Vérifier si soketi est installé
    if ! command -v soketi &> /dev/null; then
        echo "⚠️  Soketi n'est pas installé."
        read -p "Voulez-vous l'installer maintenant ? (o/n) : " install_soketi
        if [ "$install_soketi" = "o" ] || [ "$install_soketi" = "O" ]; then
            echo "Installation de Soketi..."
            npm install -g @soketi/soketi
        else
            echo "❌ Installation annulée. Installez soketi avec : npm install -g @soketi/soketi"
            exit 1
        fi
    fi
    
    # Créer la configuration Soketi
    cat > /var/www/soketi.json << 'SOKETI_EOF'
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
SOKETI_EOF
    
    echo "✅ Fichier soketi.json créé"
    echo ""
    
    # Configuration des .env
    APP_ID="mgs-app"
    APP_KEY="mgs-local-key-2025"
    APP_SECRET="mgs-local-secret-2025"
    APP_CLUSTER="mt1"
    
    echo "Configuration des 3 applications avec Soketi..."
    
    # ADMINISTRATION
    sed -i "s|^PUSHER_APP_ID=.*|PUSHER_APP_ID=$APP_ID|" /var/www/administration/.env
    sed -i "s|^PUSHER_APP_KEY=.*|PUSHER_APP_KEY=$APP_KEY|" /var/www/administration/.env
    sed -i "s|^PUSHER_APP_SECRET=.*|PUSHER_APP_SECRET=$APP_SECRET|" /var/www/administration/.env
    sed -i "s|^PUSHER_APP_CLUSTER=.*|PUSHER_APP_CLUSTER=$APP_CLUSTER|" /var/www/administration/.env
    
    # Ajouter les variables Soketi si elles n'existent pas
    if ! grep -q "^PUSHER_HOST=" /var/www/administration/.env; then
        cat >> /var/www/administration/.env << EOF

# Soketi Configuration
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
VITE_PUSHER_HOST=127.0.0.1
VITE_PUSHER_PORT=6001
VITE_PUSHER_SCHEME=http
VITE_PUSHER_APP_KEY="\${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="\${PUSHER_APP_CLUSTER}"
EOF
    fi
    
    # COMMERCIAL
    sed -i "s|^PUSHER_APP_ID=.*|PUSHER_APP_ID=$APP_ID|" /var/www/commercial/.env
    sed -i "s|^PUSHER_APP_KEY=.*|PUSHER_APP_KEY=$APP_KEY|" /var/www/commercial/.env
    sed -i "s|^PUSHER_APP_SECRET=.*|PUSHER_APP_SECRET=$APP_SECRET|" /var/www/commercial/.env
    sed -i "s|^PUSHER_APP_CLUSTER=.*|PUSHER_APP_CLUSTER=$APP_CLUSTER|" /var/www/commercial/.env
    
    if ! grep -q "^PUSHER_HOST=" /var/www/commercial/.env; then
        cat >> /var/www/commercial/.env << EOF

# Soketi Configuration
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
VITE_PUSHER_HOST=127.0.0.1
VITE_PUSHER_PORT=6001
VITE_PUSHER_SCHEME=http
VITE_PUSHER_APP_KEY="\${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="\${PUSHER_APP_CLUSTER}"
EOF
    fi
    
    # GESTION-DOSSIER
    sed -i "s|^PUSHER_APP_ID=.*|PUSHER_APP_ID=$APP_ID|" /var/www/gestion-dossier/.env
    sed -i "s|^PUSHER_APP_KEY=.*|PUSHER_APP_KEY=$APP_KEY|" /var/www/gestion-dossier/.env
    sed -i "s|^PUSHER_APP_SECRET=.*|PUSHER_APP_SECRET=$APP_SECRET|" /var/www/gestion-dossier/.env
    sed -i "s|^PUSHER_APP_CLUSTER=.*|PUSHER_APP_CLUSTER=$APP_CLUSTER|" /var/www/gestion-dossier/.env
    
    if ! grep -q "^PUSHER_HOST=" /var/www/gestion-dossier/.env; then
        cat >> /var/www/gestion-dossier/.env << EOF

# Soketi Configuration
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
VITE_PUSHER_HOST=127.0.0.1
VITE_PUSHER_PORT=6001
VITE_PUSHER_SCHEME=http
VITE_PUSHER_APP_KEY="\${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="\${PUSHER_APP_CLUSTER}"
EOF
    fi
    
    echo ""
    echo "✅ Configuration Soketi terminée !"
    echo ""
    echo "Pour démarrer Soketi :"
    echo "  cd /var/www && soketi start --config=soketi.json"
    echo ""
    echo "Soketi sera accessible sur : ws://127.0.0.1:6001"
    echo ""
    
else
    echo "❌ Choix invalide. Relancez le script."
    exit 1
fi

echo ""
echo "========================================="
echo "✅ Configuration terminée !"
echo "========================================="
echo ""
echo "Prochaines étapes :"
echo "  1. Nettoyer le cache : php artisan config:clear (sur chaque app)"
echo "  2. Compiler les assets : npm run build (sur chaque app)"
echo "  3. Démarrer les queue workers"
if [ "$choice" = "2" ]; then
    echo "  4. Démarrer Soketi : cd /var/www && soketi start --config=soketi.json"
fi
echo ""
