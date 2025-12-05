#!/bin/bash

# Script de configuration Git pour Administration
# Usage: ./setup_git_administration.sh

echo "🚀 Configuration Git pour Administration MGS"
echo "=============================================="

cd /var/www/administration || exit 1

# 1. Initialiser le dépôt Git
echo "📦 Initialisation du dépôt Git..."
git init

# 2. Configuration Git
echo "⚙️  Configuration Git..."
git config user.name "MGS Admin"
git config user.email "admin@mgs-local.mg"

# 3. Créer la branche main
echo "🌿 Création de la branche main..."
git checkout -b main

# 4. Ajouter les fichiers
echo "📝 Ajout des fichiers..."
git add .

# 5. Premier commit
echo "💾 Premier commit..."
git commit -m "Initial commit - Application Administration MGS

- Gestion centralisée des utilisateurs
- Authentification SSO
- Gestion des rôles et permissions
- Interface d'administration"

echo ""
echo "✅ Configuration Git terminée !"
echo ""
echo "📌 Prochaines étapes :"
echo "1. Créer un dépôt sur GitHub/GitLab/Bitbucket"
echo "2. Ajouter le remote : git remote add origin <URL_REPO>"
echo "3. Pousser le code : git push -u origin main"
echo ""
echo "Exemple de commandes :"
echo "  git remote add origin https://github.com/MGSadmin/administration.git"
echo "  git push -u origin main"
