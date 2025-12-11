#!/bin/bash

# ========================================================================
# Script de configuration OAuth2 pour Commercial et Gestion-Dossier
# ========================================================================

echo "========================================="
echo "Configuration OAuth2 Multi-Sites"
echo "========================================="
echo ""

# Credentials OAuth2
COMMERCIAL_CLIENT_ID="019b0bad-1854-7299-bc98-53167cbf6749"
COMMERCIAL_CLIENT_SECRET="sKWkeJTHaimY0PPjRxMPyfevNaWFtF3TQdC3AA7U"

GESTION_CLIENT_ID="019b0bad-3bc1-71a9-8cde-5f3eec91dc08"
GESTION_CLIENT_SECRET="gHCN06y45p1lfwJ77wXcl44bovbmfYzzy0M5BPQP"

# ========================================
# COMMERCIAL - Configuration OAuth2
# ========================================
echo "Configuration COMMERCIAL..."

if [ ! -f "/var/www/commercial/.env" ]; then
    echo "❌ Fichier .env introuvable pour Commercial"
    exit 1
fi

# Vérifier si les variables existent déjà
if grep -q "^OAUTH_CLIENT_ID=" /var/www/commercial/.env; then
    echo "⚠️  Variables OAuth déjà présentes dans .env de Commercial"
    echo "   Mise à jour des valeurs..."
    sed -i "s|^OAUTH_CLIENT_ID=.*|OAUTH_CLIENT_ID=$COMMERCIAL_CLIENT_ID|" /var/www/commercial/.env
    sed -i "s|^OAUTH_CLIENT_SECRET=.*|OAUTH_CLIENT_SECRET=$COMMERCIAL_CLIENT_SECRET|" /var/www/commercial/.env
    sed -i "s|^OAUTH_REDIRECT_URI=.*|OAUTH_REDIRECT_URI=http://commercial.mgs-local.mg/auth/callback|" /var/www/commercial/.env
    sed -i "s|^OAUTH_SERVER_URL=.*|OAUTH_SERVER_URL=http://administration.mgs-local.mg|" /var/www/commercial/.env
else
    echo "   Ajout des variables OAuth..."
    cat >> /var/www/commercial/.env << EOF

# OAuth2 Client Configuration
OAUTH_CLIENT_ID=$COMMERCIAL_CLIENT_ID
OAUTH_CLIENT_SECRET=$COMMERCIAL_CLIENT_SECRET
OAUTH_REDIRECT_URI=http://commercial.mgs-local.mg/auth/callback
OAUTH_SERVER_URL=http://administration.mgs-local.mg
EOF
fi

echo "✅ Commercial configuré"
echo ""

# ========================================
# GESTION-DOSSIER - Configuration OAuth2
# ========================================
echo "Configuration GESTION-DOSSIER..."

if [ ! -f "/var/www/gestion-dossier/.env" ]; then
    echo "❌ Fichier .env introuvable pour Gestion-Dossier"
    exit 1
fi

# Vérifier si les variables existent déjà
if grep -q "^OAUTH_CLIENT_ID=" /var/www/gestion-dossier/.env; then
    echo "⚠️  Variables OAuth déjà présentes dans .env de Gestion-Dossier"
    echo "   Mise à jour des valeurs..."
    sed -i "s|^OAUTH_CLIENT_ID=.*|OAUTH_CLIENT_ID=$GESTION_CLIENT_ID|" /var/www/gestion-dossier/.env
    sed -i "s|^OAUTH_CLIENT_SECRET=.*|OAUTH_CLIENT_SECRET=$GESTION_CLIENT_SECRET|" /var/www/gestion-dossier/.env
    sed -i "s|^OAUTH_REDIRECT_URI=.*|OAUTH_REDIRECT_URI=http://gestion-dossier.mgs-local.mg/auth/callback|" /var/www/gestion-dossier/.env
    sed -i "s|^OAUTH_SERVER_URL=.*|OAUTH_SERVER_URL=http://administration.mgs-local.mg|" /var/www/gestion-dossier/.env
else
    echo "   Ajout des variables OAuth..."
    cat >> /var/www/gestion-dossier/.env << EOF

# OAuth2 Client Configuration
OAUTH_CLIENT_ID=$GESTION_CLIENT_ID
OAUTH_CLIENT_SECRET=$GESTION_CLIENT_SECRET
OAUTH_REDIRECT_URI=http://gestion-dossier.mgs-local.mg/auth/callback
OAUTH_SERVER_URL=http://administration.mgs-local.mg
EOF
fi

echo "✅ Gestion-Dossier configuré"
echo ""

# ========================================
# Vérification finale
# ========================================
echo "========================================="
echo "Vérification de la configuration..."
echo "========================================="
echo ""

echo "COMMERCIAL OAuth Configuration:"
grep "^OAUTH_" /var/www/commercial/.env
echo ""

echo "GESTION-DOSSIER OAuth Configuration:"
grep "^OAUTH_" /var/www/gestion-dossier/.env
echo ""

echo "========================================="
echo "✅ Configuration OAuth2 terminée !"
echo "========================================="
echo ""
echo "Prochaines étapes :"
echo "  1. Compiler les assets : cd /var/www/commercial && npm run build"
echo "  2. Compiler les assets : cd /var/www/gestion-dossier && npm run build"
echo "  3. Tester : http://commercial.mgs-local.mg/login/oauth"
echo ""
