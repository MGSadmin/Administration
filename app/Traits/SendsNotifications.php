<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait SendsNotifications
{
    /**
     * Envoyer une notification et définir son application
     * 
     * @param object $notifiable L'utilisateur qui recevra la notification
     * @param object $notification L'instance de notification
     * @param string $application L'application ('administration', 'gestion-dossier', 'commercial', 'all')
     */
    public function sendNotification($notifiable, $notification, string $application = 'all')
    {
        // Envoyer la notification
        $notifiable->notify($notification);
        
        // Mettre à jour le champ application de la dernière notification créée
        DB::table('notifications')
            ->where('notifiable_id', $notifiable->id)
            ->where('notifiable_type', get_class($notifiable))
            ->whereNull('read_at')
            ->latest('created_at')
            ->limit(1)
            ->update(['application' => $application]);
    }

    /**
     * Envoyer une notification à plusieurs utilisateurs
     */
    public function sendNotifications($notifiables, $notification, string $application = 'all')
    {
        foreach ($notifiables as $notifiable) {
            $this->sendNotification($notifiable, $notification, $application);
        }
    }
}
