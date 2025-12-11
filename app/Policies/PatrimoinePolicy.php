<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Patrimoine;
use Illuminate\Auth\Access\HandlesAuthorization;

class PatrimoinePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('Voir Patrimoine') || $user->can('Voir Tous Patrimoines');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Patrimoine $patrimoine): bool
    {
        // Peut voir tous les patrimoines
        if ($user->can('Voir Tous Patrimoines')) {
            return true;
        }
        
        // Peut voir uniquement ses propres patrimoines
        if ($user->can('Voir Patrimoine')) {
            return $patrimoine->utilisateur_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('Créer Patrimoine');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Patrimoine $patrimoine): bool
    {
        return $user->can('Modifier Patrimoine');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Patrimoine $patrimoine): bool
    {
        return $user->can('Supprimer Patrimoine');
    }

    /**
     * Determine whether the user can assign the patrimoine.
     */
    public function assign(User $user): bool
    {
        return $user->can('Attribuer Patrimoine');
    }

    /**
     * Determine whether the user can liberate the patrimoine.
     */
    public function liberate(User $user): bool
    {
        return $user->can('Libérer Patrimoine');
    }

    /**
     * Determine whether the user can view statistics.
     */
    public function viewStatistics(User $user): bool
    {
        return $user->can('Voir Statistiques Patrimoine');
    }
}
