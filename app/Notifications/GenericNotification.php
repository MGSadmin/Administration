<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GenericNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $notificationData;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $notificationData)
    {
        $this->notificationData = $notificationData;
    }

    /**
     * Get the notification's delivery channels.
     * IMPORTANT : 'mail' est TOUJOURS inclus pour garantir l'envoi d'emails
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->notificationData['titre'] ?? 'Notification')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line($this->notificationData['message']);

        if (isset($this->notificationData['url'])) {
            $mail->action('Voir les détails', $this->notificationData['url']);
        }

        return $mail->line('Merci d\'utiliser notre application!');
    }

    /**
     * Get the array representation of the notification (Database).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'titre' => $this->notificationData['titre'] ?? 'Notification',
            'message' => $this->notificationData['message'],
            'type' => $this->notificationData['type'] ?? 'info',
            'url' => $this->notificationData['url'] ?? null,
            'data' => $this->notificationData['data'] ?? [],
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'titre' => $this->notificationData['titre'] ?? 'Notification',
            'message' => $this->notificationData['message'],
            'type' => $this->notificationData['type'] ?? 'info',
            'url' => $this->notificationData['url'] ?? null,
        ]);
    }
}
