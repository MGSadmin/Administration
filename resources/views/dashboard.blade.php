@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Message de bienvenue -->
    <div class="card mb-4">
        <div class="card-body bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h2 class="text-white mb-2">
                <i class="fas fa-hand-wave"></i> Bienvenue, {{ Auth::user()->name }} {{ Auth::user()->prenom }} !
            </h2>
            <p class="text-white-50 mb-0">
                <i class="fas fa-briefcase"></i> {{ Auth::user()->poste ?? 'Utilisateur' }}
                @if(Auth::user()->departement)
                    - {{ Auth::user()->departement }}
                @endif
            </p>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50">Utilisateurs</h6>
                            <h2 class="mb-0">{{ \App\Models\User::count() }}</h2>
                        </div>
                        <i class="fas fa-users fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top border-white">
                    <a href="{{ route('users.index') }}" class="text-white text-decoration-none small">
                        Voir tous <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50">Rôles</h6>
                            <h2 class="mb-0">{{ \Spatie\Permission\Models\Role::count() }}</h2>
                        </div>
                        <i class="fas fa-user-tag fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top border-white">
                    <a href="{{ route('roles.index') }}" class="text-white text-decoration-none small">
                        Gérer <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50">Permissions</h6>
                            <h2 class="mb-0">{{ \Spatie\Permission\Models\Permission::count() }}</h2>
                        </div>
                        <i class="fas fa-shield-alt fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top border-white">
                    <span class="text-white small">Total système</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50">Applications</h6>
                            <h2 class="mb-0">3</h2>
                        </div>
                        <i class="fas fa-th fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top border-white">
                    <span class="text-white small">Actives</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Applications disponibles -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-th-large"></i> Applications disponibles</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @php
                    $userApps = Auth::user()->applications->pluck('application')->toArray();
                    $hasAdminAccess = in_array('administration', $userApps);
                    $hasGestionDossierAccess = in_array('gestion-dossier', $userApps);
                    $hasCommercialAccess = in_array('commercial', $userApps);
                @endphp

                @if($hasAdminAccess || Auth::user()->hasRole('admin'))
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border-primary">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary text-white rounded p-3">
                                        <i class="fas fa-cog fa-2x"></i>
                                    </div>
                                    <h5 class="card-title mb-0 ms-3">Administration</h5>
                                </div>
                                <p class="card-text text-muted">
                                    Gestion centralisée des utilisateurs, rôles, permissions et configuration système.
                                </p>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('users.index') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-users"></i> Utilisateurs
                                    </a>
                                    <a href="{{ route('roles.index') }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-user-tag"></i> Rôles
                                    </a>
                                </div>
                            </div>
                            <div class="card-footer bg-primary text-white">
                                <small><i class="fas fa-circle text-success"></i> Application actuelle</small>
                            </div>
                        </div>
                    </div>
                @endif

                @if($hasGestionDossierAccess || Auth::user()->hasRole('admin'))
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border-warning">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-warning text-white rounded p-3">
                                        <i class="fas fa-folder-open fa-2x"></i>
                                    </div>
                                    <h5 class="card-title mb-0 ms-3">Gestion Dossiers</h5>
                                </div>
                                <p class="card-text text-muted">
                                    Gestion complète des dossiers de transit, débours, cotations et factures.
                                </p>
                                <div class="d-grid">
                                    <a href="{{ \App\Helpers\AppUrlHelper::appUrl('gestion-dossier') }}" target="_blank" class="btn btn-warning">
                                        <i class="fas fa-external-link-alt"></i> Accéder
                                    </a>
                                </div>
                            </div>
                            <div class="card-footer bg-warning text-white">
                                <small><i class="fas fa-check-circle"></i> Accès autorisé</small>
                            </div>
                        </div>
                    </div>
                @endif

                @if($hasCommercialAccess || Auth::user()->hasRole('admin'))
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border-success">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-success text-white rounded p-3">
                                        <i class="fas fa-chart-line fa-2x"></i>
                                    </div>
                                    <h5 class="card-title mb-0 ms-3">Commercial</h5>
                                </div>
                                <p class="card-text text-muted">
                                    Gestion des devis commerciaux, suivi clients et statistiques de vente.
                                </p>
                                <div class="d-grid">
                                    <a href="{{ \App\Helpers\AppUrlHelper::appUrl('commercial') }}" target="_blank" class="btn btn-success">
                                        <i class="fas fa-external-link-alt"></i> Accéder
                                    </a>
                                </div>
                            </div>
                            <div class="card-footer bg-success text-white">
                                <small><i class="fas fa-check-circle"></i> Accès autorisé</small>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Raccourcis rapides -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-bolt"></i> Raccourcis rapides</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <a href="{{ route('users.create') }}" class="btn btn-outline-primary w-100 p-3">
                        <i class="fas fa-user-plus fa-2x d-block mb-2"></i>
                        <span class="fw-bold">Nouvel utilisateur</span>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('roles.create') }}" class="btn btn-outline-success w-100 p-3">
                        <i class="fas fa-plus-circle fa-2x d-block mb-2"></i>
                        <span class="fw-bold">Nouveau rôle</span>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-warning w-100 p-3">
                        <i class="fas fa-users-cog fa-2x d-block mb-2"></i>
                        <span class="fw-bold">Tous les utilisateurs</span>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-info w-100 p-3">
                        <i class="fas fa-user-circle fa-2x d-block mb-2"></i>
                        <span class="fw-bold">Mon profil</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.opacity-50 {
    opacity: 0.5;
}
.bg-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}
</style>
@endpush
@endsection
