<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\DemandeFourniture;

class DemandeFournitureNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $demande;
    protected $evenement;

    /**
     * Create a new notification instance.
     */
    public function __construct(DemandeFourniture $demande, $evenement)
    {
        $this->demande = $demande;
        $this->evenement = $evenement;
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

        switch ($this->evenement) {
            case 'creee':
                $message->line('Une nouvelle demande de fourniture a été créée.')
                    ->line('**Numéro:** ' . $this->demande->numero_demande)
                    ->line('**Objet:** ' . $this->demande->objet);
                
                if ($this->demande->demandeur) {
                    $message->line('**Demandeur:** ' . $this->demande->demandeur->name);
                }
                break;

            case 'validee':
                $message->line('Votre demande de fourniture a été validée.')
                    ->line('**Numéro:** ' . $this->demande->numero_demande);
                
                if ($this->demande->validateur) {
                    $message->line('**Validée par:** ' . $this->demande->validateur->name);
                }
                
                if ($this->demande->commentaire_validateur) {
                    $message->line('**Commentaire:** ' . $this->demande->commentaire_validateur);
                }
                break;

            case 'rejetee':
                $message->line('Votre demande de fourniture a été rejetée.')
                    ->line('**Numéro:** ' . $this->demande->numero_demande);
                
                if ($this->demande->validateur) {
                    $message->line('**Rejetée par:** ' . $this->demande->validateur->name);
                }
                
                if ($this->demande->motif_rejet) {
                    $message->line('**Motif:** ' . $this->demande->motif_rejet);
                }
                break;

            case 'commandee':
                $message->line('La fourniture a été commandée.')
                    ->line('**Numéro:** ' . $this->demande->numero_demande)
                    ->line('**Fournisseur:** ' . $this->demande->fournisseur)
                    ->line('**Montant:** ' . number_format($this->demande->montant_reel, 2) . ' Ar');
                break;

            case 'recue':
                $message->line('La fourniture a été réceptionnée.')
                    ->line('**Numéro:** ' . $this->demande->numero_demande)
                    ->line('**Date de réception:** ' . $this->demande->date_reception->format('d/m/Y'));
                break;

            case 'livree':
                $message->line('La fourniture vous a été livrée.')
                    ->line('**Numéro:** ' . $this->demande->numero_demande)
                    ->line('**Date de livraison:** ' . $this->demande->date_livraison->format('d/m/Y'));
                break;
        }

        $message->action('Voir la demande', url('/demandes-fourniture/' . $this->demande->id))
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
            'type' => 'demande_fourniture',
            'evenement' => $this->evenement,
            'demande_id' => $this->demande->id,
            'numero_demande' => $this->demande->numero_demande,
            'objet' => $this->demande->objet,
            'message' => $this->getMessage(),
            'url' => url('/demandes-fourniture/' . $this->demande->id),
            'icon' => $this->getIcon(),
        ];
    }

    /**
     * Get the notification subject
     */
    private function getSubject()
    {
        $subjects = [
            'creee' => 'Nouvelle demande de fourniture créée',
            'validee' => 'Demande de fourniture validée',
            'rejetee' => 'Demande de fourniture rejetée',
            'commandee' => 'Fourniture commandée',
            'recue' => 'Fourniture réceptionnée',
            'livree' => 'Fourniture livrée',
        ];

        return $subjects[$this->evenement] ?? 'Notification demande de fourniture';
    }

    /**
     * Get the notification message
     */
    private function getMessage()
    {
        $demandeurName = $this->demande->demandeur ? $this->demande->demandeur->name : 'Utilisateur inconnu';
        $validateurName = $this->demande->validateur ? $this->demande->validateur->name : 'Administrateur';
        
        $messages = [
            'creee' => "Nouvelle demande de fourniture #{$this->demande->numero_demande} créée par {$demandeurName}",
            'validee' => "Demande #{$this->demande->numero_demande} validée par {$validateurName}",
            'rejetee' => "Demande #{$this->demande->numero_demande} rejetée par {$validateurName}",
            'commandee' => "Demande #{$this->demande->numero_demande} commandée chez {$this->demande->fournisseur}",
            'recue' => "Demande #{$this->demande->numero_demande} réceptionnée",
            'livree' => "Demande #{$this->demande->numero_demande} livrée",
        ];

        return $messages[$this->evenement] ?? "Notification pour la demande #{$this->demande->numero_demande}";
    }

    /**
     * Get the notification icon
     */
    private function getIcon()
    {
        $icons = [
            'creee' => 'fa-file-alt',
            'validee' => 'fa-check-circle',
            'rejetee' => 'fa-times-circle',
            'commandee' => 'fa-shopping-cart',
            'recue' => 'fa-box',
            'livree' => 'fa-truck',
        ];

        return $icons[$this->evenement] ?? 'fa-bell';
    }
}
