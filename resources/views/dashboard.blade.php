@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    @guest
    <!-- Message pour utilisateur non connecté -->
    <div class="alert alert-info alert-dismissible fade show">
        <i class="fas fa-info-circle"></i>
        <strong>Information :</strong> Vous n'êtes pas connecté. 
        <a href="{{ route('login') }}" class="alert-link">Cliquez ici pour vous connecter</a> 
        et accéder à toutes les fonctionnalités.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endguest

    <!-- Message de bienvenue -->
    <div class="card mb-4">
        <div class="card-body bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h2 class="text-white mb-2">
                <i class="fas fa-hand-wave"></i> Bienvenue @auth{{ Auth::user()->name }}@endauth sur l'application Administration !
            </h2>
            <p class="text-white-50 mb-0">
                <i class="fas fa-briefcase"></i> Gestion des patrimoines et demandes de fourniture
                @auth
                <br><i class="fas fa-shield-alt"></i> Système SSO - Authentification centralisée
                @endauth
            </p>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50">Patrimoines</h6>
                            <h2 class="mb-0">{{ \App\Models\Patrimoine::count() }}</h2>
                        </div>
                        <i class="fas fa-building fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top border-white">
                    <a href="{{ route('patrimoines.index') }}" class="text-white text-decoration-none small">
                        Voir tous <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50">Demandes de Fourniture</h6>
                            <h2 class="mb-0">{{ \App\Models\DemandeFourniture::count() }}</h2>
                        </div>
                        <i class="fas fa-file-alt fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top border-white">
                    <a href="{{ route('demandes-fourniture.index') }}" class="text-white text-decoration-none small">
                        Voir toutes <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Liens rapides -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-bolt"></i> Accès rapide</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <a href="{{ route('patrimoines.index') }}" class="btn btn-outline-primary w-100 py-3">
                        <i class="fas fa-building fa-2x mb-2"></i><br>
                        <strong>Patrimoines</strong>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('demandes-fourniture.index') }}" class="btn btn-outline-success w-100 py-3">
                        <i class="fas fa-file-alt fa-2x mb-2"></i><br>
                        <strong>Demandes de Fourniture</strong>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('notifications.index') }}" class="btn btn-outline-info w-100 py-3">
                        <i class="fas fa-bell fa-2x mb-2"></i><br>
                        <strong>Notifications</strong>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('organigramme.index') }}" class="btn btn-outline-dark w-100 py-3">
                        <i class="fas fa-sitemap fa-2x mb-2"></i><br>
                        <strong>Organigramme SSO</strong>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
