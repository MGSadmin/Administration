<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CongeApprouve extends Notification implements ShouldQueue
{
    use Queueable;

    public $conge;

    /**
     * Create a new notification instance.
     */
    public function __construct($conge)
    {
        $this->conge = $conge;
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
        return (new MailMessage)
            ->subject('Congé approuvé')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line("Votre demande de congé du {$this->conge->date_debut} a été approuvée par votre responsable.")
            ->line("Durée : {$this->conge->nombre_jours} jour(s)")
            ->line("Type : {$this->conge->type}")
            ->action('Voir les détails', route('conges.show', $this->conge->id))
            ->line('Merci d\'utiliser notre application!')
            ->salutation('Cordialement, L\'équipe RH');
    }

    /**
     * Get the array representation of the notification (Database).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'titre' => 'Congé approuvé',
            'message' => "Votre demande de congé du {$this->conge->date_debut} a été approuvée",
            'type' => 'conge_approuve',
            'conge_id' => $this->conge->id,
            'url' => route('conges.show', $this->conge->id),
            'nombre_jours' => $this->conge->nombre_jours,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'titre' => 'Congé approuvé',
            'message' => "Votre demande de congé du {$this->conge->date_debut} a été approuvée",
            'url' => route('conges.show', $this->conge->id),
            'type' => 'success',
        ]);
    }
}
