<?php

namespace App\Policies;

use App\Models\User;
use App\Models\OrganigrammeMembers;
use Illuminate\Auth\Access\HandlesAuthorization;

class MemberPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('Voir Personnel') || $user->can('Voir Organigramme');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrganigrammeMembers $member): bool
    {
        return $user->can('Voir Personnel') || $user->can('Voir Détails Personnel');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('Créer Membre');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OrganigrammeMembers $member): bool
    {
        return $user->can('Modifier Membre');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OrganigrammeMembers $member): bool
    {
        return $user->can('Supprimer Membre');
    }

    /**
     * Determine whether the user can assign members.
     */
    public function assign(User $user): bool
    {
        return $user->can('Assigner Membre');
    }

    /**
     * Determine whether the user can change member status.
     */
    public function changeStatus(User $user): bool
    {
        return $user->can('Modifier Statut Personnel');
    }

    /**
     * Determine whether the user can view history.
     */
    public function viewHistory(User $user): bool
    {
        return $user->can('Voir Historique Personnel');
    }

    /**
     * Determine whether the user can manage documents.
     */
    public function manageDocuments(User $user): bool
    {
        return $user->can('Gérer Documents Personnel');
    }
}
