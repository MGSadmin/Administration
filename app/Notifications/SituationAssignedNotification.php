<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SituationAssignedNotification extends Notification
{
    use Queueable;

    protected $situation;
    protected $assignedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct($situation, $assignedBy)
    {
        $this->situation = $situation;
        $this->assignedBy = $assignedBy;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail']; // Database + Email
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
                    ->subject('Nouvelle situation attribuée - ' . $this->situation->reference)
                    ->greeting('Bonjour ' . $notifiable->name . ',')
                    ->line($this->assignedBy->name . ' vous a attribué une nouvelle situation.')
                    ->line('**Référence:** ' . $this->situation->reference)
                    ->action('Voir la situation', url('#'))
                    ->line('Merci d\'utiliser notre application MGS!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Nouvelle situation attribuée',
            'message' => "{$this->assignedBy->name} vous a attribué une nouvelle situation : {$this->situation->reference}",
            'icon' => 'fa-tasks',
            'url' => '#', // Route à définir dans gestion-dossier
            'situation_id' => $this->situation->id,
            'assigned_by' => $this->assignedBy->name,
            'application' => 'gestion-dossier', // Visible dans Gestion-Dossier ET Administration
        ];
    }
}
