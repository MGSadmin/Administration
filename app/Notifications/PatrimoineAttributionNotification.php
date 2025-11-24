<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Patrimoine;

class PatrimoineAttributionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $patrimoine;
    protected $action; // 'attribue' ou 'libere'

    /**
     * Create a new notification instance.
     */
    public function __construct(Patrimoine $patrimoine, $action = 'attribue')
    {
        $this->patrimoine = $patrimoine;
        $this->action = $action;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->getSubject())
            ->greeting('Bonjour ' . $notifiable->name . ',');

        if ($this->action === 'attribue') {
            $message->line('Un patrimoine vous a été attribué.')
                ->line('**Code:** ' . $this->patrimoine->code_materiel)
                ->line('**Désignation:** ' . $this->patrimoine->designation);
            
            if ($this->patrimoine->marque) {
                $message->line('**Marque/Modèle:** ' . $this->patrimoine->marque . ' ' . $this->patrimoine->modele);
            }
            
            if ($this->patrimoine->localisation) {
                $message->line('**Localisation:** ' . $this->patrimoine->localisation);
            }
            
            $message->line('Vous êtes maintenant responsable de ce matériel.')
                ->line('Merci de le maintenir en bon état.');
        } else {
            $message->line('Le patrimoine suivant vous a été retiré.')
                ->line('**Code:** ' . $this->patrimoine->code_materiel)
                ->line('**Désignation:** ' . $this->patrimoine->designation)
                ->line('Le matériel est maintenant disponible.');
        }

        $message->action('Voir le patrimoine', url('/patrimoines/' . $this->patrimoine->id))
            ->line('Merci d\'utiliser notre application!');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'application' => 'administration',
            'type' => 'patrimoine_attribution',
            'action' => $this->action,
            'patrimoine_id' => $this->patrimoine->id,
            'code_materiel' => $this->patrimoine->code_materiel,
            'designation' => $this->patrimoine->designation,
            'message' => $this->getMessage(),
            'url' => url('/patrimoines/' . $this->patrimoine->id),
            'icon' => $this->getIcon(),
        ];
    }

    /**
     * Get the notification subject
     */
    private function getSubject()
    {
        if ($this->action === 'attribue') {
            return 'Patrimoine attribué - ' . $this->patrimoine->code_materiel;
        }
        return 'Patrimoine libéré - ' . $this->patrimoine->code_materiel;
    }

    /**
     * Get the notification message
     */
    private function getMessage()
    {
        if ($this->action === 'attribue') {
            return "Le patrimoine {$this->patrimoine->code_materiel} ({$this->patrimoine->designation}) vous a été attribué";
        }
        return "Le patrimoine {$this->patrimoine->code_materiel} ({$this->patrimoine->designation}) vous a été retiré";
    }

    /**
     * Get the notification icon
     */
    private function getIcon()
    {
        return $this->action === 'attribue' ? 'fa-user-plus' : 'fa-user-minus';
    }
}
