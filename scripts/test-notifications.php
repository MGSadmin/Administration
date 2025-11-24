#!/usr/bin/env php
<?php

/**
 * Script de test des notifications
 * Usage: php scripts/test-notifications.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\DemandeFourniture;

echo "\n";
echo "========================================\n";
echo "   TEST DES NOTIFICATIONS\n";
echo "========================================\n\n";

// 1. Vérifier qu'il y a des utilisateurs
$users = User::all();
if ($users->count() < 2) {
    echo "❌ Erreur: Il faut au moins 2 utilisateurs dans la base de données\n";
    exit(1);
}

echo "✅ " . $users->count() . " utilisateurs trouvés\n";

// 2. Prendre deux utilisateurs
$demandeur = $users->first();
$notifie = $users->skip(1)->first();

echo "👤 Demandeur: {$demandeur->name} (ID: {$demandeur->id})\n";
echo "🔔 Personne notifiée: {$notifie->name} (ID: {$notifie->id})\n\n";

// 3. Créer une demande de test
echo "📝 Création d'une demande de test...\n";

try {
    $demande = DemandeFourniture::create([
        'numero_demande' => 'TEST-' . now()->format('Ymd-His'),
        'objet' => 'Test de notification - ' . now()->format('d/m/Y H:i:s'),
        'type_fourniture' => 'materiel_informatique',
        'description' => 'Ceci est un test automatique pour vérifier que les notifications fonctionnent correctement.',
        'quantite' => 1,
        'priorite' => 'normale',
        'demandeur_id' => $demandeur->id,
        'notifier_user_id' => $notifie->id,
        'statut' => 'en_attente',
        'budget_estime' => 50000,
    ]);

    echo "✅ Demande créée: {$demande->numero_demande}\n\n";

    // 4. Envoyer la notification
    echo "📧 Envoi de la notification...\n";
    $demande->envoyerNotification('creee');
    echo "✅ Notification envoyée\n\n";

    // 5. Vérifier les notifications en base de données
    echo "🔍 Vérification dans la base de données...\n";
    
    $notificationsDemandeur = $demandeur->notifications()
        ->where('data->demande_id', $demande->id)
        ->count();
    
    $notificationsNotifie = $notifie->notifications()
        ->where('data->demande_id', $demande->id)
        ->count();

    echo "📬 Notifications pour {$demandeur->name}: {$notificationsDemandeur}\n";
    echo "📬 Notifications pour {$notifie->name}: {$notificationsNotifie}\n\n";

    if ($notificationsDemandeur > 0 && $notificationsNotifie > 0) {
        echo "✅ ✅ ✅ SUCCÈS! Les notifications fonctionnent correctement!\n\n";
        
        // Afficher les détails de la notification
        $notification = $notifie->notifications()
            ->where('data->demande_id', $demande->id)
            ->first();
        
        if ($notification) {
            echo "📋 Détails de la notification:\n";
            echo "   Type: " . $notification->data['type'] . "\n";
            echo "   Événement: " . $notification->data['evenement'] . "\n";
            echo "   Message: " . $notification->data['message'] . "\n";
            echo "   Icône: " . $notification->data['icon'] . "\n";
            echo "   URL: " . $notification->data['url'] . "\n\n";
        }
    } else {
        echo "❌ ÉCHEC: Les notifications n'ont pas été créées\n";
        echo "   Vérifiez la configuration de la queue (QUEUE_CONNECTION dans .env)\n\n";
    }

    // 6. Nettoyer
    echo "🧹 Nettoyage...\n";
    $demande->delete();
    $demandeur->notifications()->where('data->demande_id', $demande->id)->delete();
    $notifie->notifications()->where('data->demande_id', $demande->id)->delete();
    echo "✅ Nettoyage terminé\n\n";

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "   Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    exit(1);
}

echo "========================================\n";
echo "   TEST TERMINÉ AVEC SUCCÈS\n";
echo "========================================\n\n";
