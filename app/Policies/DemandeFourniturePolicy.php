<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DemandeFourniture;
use Illuminate\Auth\Access\HandlesAuthorization;

class DemandeFourniturePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('Voir Demande Fourniture') || $user->can('Voir Toutes Demandes Fourniture');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DemandeFourniture $demandeFourniture): bool
    {
        // Peut voir toutes les demandes
        if ($user->can('Voir Toutes Demandes Fourniture')) {
            return true;
        }
        
        // Peut voir uniquement ses propres demandes
        if ($user->can('Voir Demande Fourniture')) {
            return $demandeFourniture->demandeur_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('Créer Demande Fourniture');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DemandeFourniture $demandeFourniture): bool
    {
        // Ne peut modifier que ses propres demandes en attente
        if ($user->can('Modifier Demande Fourniture')) {
            return $demandeFourniture->demandeur_id === $user->id 
                && $demandeFourniture->statut === 'en_attente';
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DemandeFourniture $demandeFourniture): bool
    {
        // Ne peut supprimer que ses propres demandes en attente
        if ($user->can('Supprimer Demande Fourniture')) {
            return $demandeFourniture->demandeur_id === $user->id 
                && $demandeFourniture->statut === 'en_attente';
        }
        
        return false;
    }

    /**
     * Determine whether the user can validate the request.
     */
    public function validate(User $user): bool
    {
        return $user->can('Valider Demande Fourniture');
    }

    /**
     * Determine whether the user can reject the request.
     */
    public function reject(User $user): bool
    {
        return $user->can('Rejeter Demande Fourniture');
    }

    /**
     * Determine whether the user can order supplies.
     */
    public function order(User $user): bool
    {
        return $user->can('Commander Fourniture');
    }

    /**
     * Determine whether the user can mark as received.
     */
    public function markReceived(User $user): bool
    {
        return $user->can('Marquer Fourniture Reçue');
    }

    /**
     * Determine whether the user can deliver.
     */
    public function deliver(User $user): bool
    {
        return $user->can('Livrer Fourniture');
    }

    /**
     * Determine whether the user can view statistics.
     */
    public function viewStatistics(User $user): bool
    {
        return $user->can('Voir Statistiques Fourniture');
    }
}
