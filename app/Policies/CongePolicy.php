<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Conge;
use Illuminate\Auth\Access\HandlesAuthorization;

class CongePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('Voir Congé') || $user->can('Voir Tous Congés');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Conge $conge): bool
    {
        // Peut voir tous les congés
        if ($user->can('Voir Tous Congés')) {
            return true;
        }
        
        // Peut voir uniquement ses propres congés
        if ($user->can('Voir Congé')) {
            return $conge->user_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('Créer Congé');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Conge $conge): bool
    {
        // Ne peut modifier que ses propres congés en attente
        if ($user->can('Modifier Congé')) {
            return $conge->user_id === $user->id 
                && $conge->statut === 'en_attente';
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Conge $conge): bool
    {
        // Ne peut supprimer que ses propres congés en attente
        if ($user->can('Supprimer Congé')) {
            return $conge->user_id === $user->id 
                && $conge->statut === 'en_attente';
        }
        
        return false;
    }

    /**
     * Determine whether the user can approve leave requests.
     */
    public function approve(User $user): bool
    {
        return $user->can('Approuver Congé');
    }

    /**
     * Determine whether the user can reject leave requests.
     */
    public function reject(User $user): bool
    {
        return $user->can('Rejeter Congé');
    }
}
