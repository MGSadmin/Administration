<?php

if (!function_exists('can_view_organigramme')) {
    function can_view_organigramme(): bool
    {
        return auth()->check() && auth()->user()->can('Voir Organigramme');
    }
}

if (!function_exists('can_edit_organigramme')) {
    function can_edit_organigramme(): bool
    {
        return auth()->check() && (
            auth()->user()->can('Modifier Département') ||
            auth()->user()->can('Modifier Position') ||
            auth()->user()->can('Modifier Membre')
        );
    }
}

if (!function_exists('can_delete_organigramme')) {
    function can_delete_organigramme(): bool
    {
        return auth()->check() && (
            auth()->user()->can('Supprimer Département') ||
            auth()->user()->can('Supprimer Position') ||
            auth()->user()->can('Supprimer Membre')
        );
    }
}

if (!function_exists('can_view_patrimoine')) {
    function can_view_patrimoine(): bool
    {
        return auth()->check() && (
            auth()->user()->can('Voir Patrimoine') ||
            auth()->user()->can('Voir Tous Patrimoines')
        );
    }
}

if (!function_exists('can_create_patrimoine')) {
    function can_create_patrimoine(): bool
    {
        return auth()->check() && auth()->user()->can('Créer Patrimoine');
    }
}

if (!function_exists('can_edit_patrimoine')) {
    function can_edit_patrimoine(): bool
    {
        return auth()->check() && auth()->user()->can('Modifier Patrimoine');
    }
}

if (!function_exists('can_delete_patrimoine')) {
    function can_delete_patrimoine(): bool
    {
        return auth()->check() && auth()->user()->can('Supprimer Patrimoine');
    }
}

if (!function_exists('can_view_fourniture')) {
    function can_view_fourniture(): bool
    {
        return auth()->check() && (
            auth()->user()->can('Voir Demande Fourniture') ||
            auth()->user()->can('Voir Toutes Demandes Fourniture')
        );
    }
}

if (!function_exists('can_create_fourniture')) {
    function can_create_fourniture(): bool
    {
        return auth()->check() && auth()->user()->can('Créer Demande Fourniture');
    }
}

if (!function_exists('can_edit_fourniture')) {
    function can_edit_fourniture(): bool
    {
        return auth()->check() && auth()->user()->can('Modifier Demande Fourniture');
    }
}

if (!function_exists('can_delete_fourniture')) {
    function can_delete_fourniture(): bool
    {
        return auth()->check() && auth()->user()->can('Supprimer Demande Fourniture');
    }
}

if (!function_exists('can_validate_fourniture')) {
    function can_validate_fourniture(): bool
    {
        return auth()->check() && auth()->user()->can('Valider Demande Fourniture');
    }
}

if (!function_exists('can_view_conge')) {
    function can_view_conge(): bool
    {
        return auth()->check() && (
            auth()->user()->can('Voir Congé') ||
            auth()->user()->can('Voir Tous Congés')
        );
    }
}

if (!function_exists('can_create_conge')) {
    function can_create_conge(): bool
    {
        return auth()->check() && auth()->user()->can('Créer Congé');
    }
}

if (!function_exists('can_edit_conge')) {
    function can_edit_conge(): bool
    {
        return auth()->check() && auth()->user()->can('Modifier Congé');
    }
}

if (!function_exists('can_delete_conge')) {
    function can_delete_conge(): bool
    {
        return auth()->check() && auth()->user()->can('Supprimer Congé');
    }
}

if (!function_exists('can_approve_conge')) {
    function can_approve_conge(): bool
    {
        return auth()->check() && auth()->user()->can('Approuver Congé');
    }
}

if (!function_exists('can_view_personnel')) {
    function can_view_personnel(): bool
    {
        return auth()->check() && auth()->user()->can('Voir Personnel');
    }
}

if (!function_exists('can_manage_personnel')) {
    function can_manage_personnel(): bool
    {
        return auth()->check() && (
            auth()->user()->can('Modifier Statut Personnel') ||
            auth()->user()->can('Gérer Documents Personnel')
        );
    }
}

if (!function_exists('can_view_users')) {
    function can_view_users(): bool
    {
        return auth()->check() && auth()->user()->can('Voir Utilisateurs');
    }
}

if (!function_exists('can_create_user')) {
    function can_create_user(): bool
    {
        return auth()->check() && auth()->user()->can('Créer Utilisateur');
    }
}

if (!function_exists('can_edit_user')) {
    function can_edit_user(): bool
    {
        return auth()->check() && auth()->user()->can('Modifier Utilisateur');
    }
}

if (!function_exists('can_delete_user')) {
    function can_delete_user(): bool
    {
        return auth()->check() && auth()->user()->can('Supprimer Utilisateur');
    }
}

if (!function_exists('can_view_roles')) {
    function can_view_roles(): bool
    {
        return auth()->check() && auth()->user()->can('Voir Rôles');
    }
}

if (!function_exists('can_create_role')) {
    function can_create_role(): bool
    {
        return auth()->check() && auth()->user()->can('Créer Rôle');
    }
}

if (!function_exists('can_edit_role')) {
    function can_edit_role(): bool
    {
        return auth()->check() && auth()->user()->can('Modifier Rôle');
    }
}

if (!function_exists('can_delete_role')) {
    function can_delete_role(): bool
    {
        return auth()->check() && auth()->user()->can('Supprimer Rôle');
    }
}
