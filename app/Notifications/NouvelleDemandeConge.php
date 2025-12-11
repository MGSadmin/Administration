<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NouvelleDemandeConge extends Notification implements ShouldQueue
{
    use Queueable;

    public $conge;
    public $demandeur;

    /**
     * Create a new notification instance.
     */
    public function __construct($conge, $demandeur)
    {
        $this->conge = $conge;
        $this->demandeur = $demandeur;
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
            ->subject('Nouvelle demande de congé à valider')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line("{$this->demandeur->name} a soumis une nouvelle demande de congé.")
            ->line("Période : du {$this->conge->date_debut} au {$this->conge->date_fin}")
            ->line("Durée : {$this->conge->nombre_jours} jour(s)")
            ->line("Type : {$this->conge->type}")
            ->line("Motif : {$this->conge->motif}")
            ->action('Valider/Rejeter', route('conges.show', $this->conge->id))
            ->line('Merci de traiter cette demande rapidement.')
            ->salutation('Cordialement, L\'équipe RH');
    }

    /**
     * Get the array representation of the notification (Database).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'titre' => 'Nouvelle demande de congé',
            'message' => "{$this->demandeur->name} a soumis une demande de congé du {$this->conge->date_debut} au {$this->conge->date_fin}",
            'type' => 'demande_conge',
            'conge_id' => $this->conge->id,
            'demandeur_id' => $this->demandeur->id,
            'demandeur_name' => $this->demandeur->name,
            'url' => route('conges.show', $this->conge->id),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'titre' => 'Nouvelle demande de congé',
            'message' => "{$this->demandeur->name} a soumis une demande de congé",
            'url' => route('conges.show', $this->conge->id),
            'type' => 'info',
        ]);
    }
}
