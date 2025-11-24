<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class UserCreatedNotification extends Notification
{
    use Queueable;

    protected $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
                    ->subject('Nouvel utilisateur créé dans MGS')
                    ->greeting('Bonjour ' . $notifiable->name . ',')
                    ->line("Un nouvel utilisateur a été créé dans le système MGS.")
                    ->line('**Nom:** ' . $this->user->name . ' ' . $this->user->prenom)
                    ->line('**Email:** ' . $this->user->email)
                    ->action('Voir l\'utilisateur', route('users.show', $this->user->id))
                    ->line('Cette notification a été envoyée depuis l\'application Administration.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Nouvel utilisateur créé',
            'message' => "L'utilisateur {$this->user->name} {$this->user->prenom} a été créé avec succès.",
            'icon' => 'fa-user-plus',
            'url' => route('users.show', $this->user->id),
            'user_id' => $this->user->id,
            'user_name' => $this->user->name . ' ' . $this->user->prenom,
            'application' => 'administration', // Visible uniquement dans Administration
        ];
    }
}