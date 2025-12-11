@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="card mb-4">
        <div class="card-body bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h2 class="text-white mb-2">
                <i class="fas fa-sitemap"></i> Organigramme du Système SSO
            </h2>
            <p class="text-white-50 mb-0">
                <i class="fas fa-shield-alt"></i> Architecture centralisée d'authentification MGS
            </p>
        </div>
    </div>

    <!-- Navigation par onglets -->
    <ul class="nav nav-tabs mb-4" id="organigrammeTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="architecture-tab" data-bs-toggle="tab" data-bs-target="#architecture" type="button">
                <i class="fas fa-project-diagram"></i> Architecture
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="flux-tab" data-bs-toggle="tab" data-bs-target="#flux" type="button">
                <i class="fas fa-route"></i> Flux d'authentification
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles" type="button">
                <i class="fas fa-users-cog"></i> Rôles & Permissions
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats" type="button">
                <i class="fas fa-chart-bar"></i> Statistiques
            </button>
        </li>
    </ul>

    <div class="tab-content" id="organigrammeTabsContent">
        <!-- Onglet Architecture -->
        <div class="tab-pane fade show active" id="architecture" role="tabpanel">
            <div class="row">
                <!-- Serveur Central -->
                <div class="col-md-12 mb-4">
                    <div class="card border-primary shadow-lg">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-server"></i> Serveur Central - Administration
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <div class="p-4 bg-light rounded">
                                        <i class="fas fa-shield-alt fa-4x text-primary mb-3"></i>
                                        <h6>Authentification SSO</h6>
                                        <p class="small text-muted">Laravel Sanctum</p>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="p-4 bg-light rounded">
                                        <i class="fas fa-users fa-4x text-success mb-3"></i>
                                        <h6>Gestion Utilisateurs</h6>
                                        <p class="small text-muted">{{ $stats['users'] }} utilisateurs</p>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="p-4 bg-light rounded">
                                        <i class="fas fa-key fa-4x text-warning mb-3"></i>
                                        <h6>Rôles & Permissions</h6>
                                        <p class="small text-muted">Spatie Permission</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 text-center">
                                <strong>Domaine:</strong> <span class="badge bg-primary">administration.mgs.mg</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Flèches de connexion -->
                <div class="col-md-12 text-center mb-3">
                    <i class="fas fa-arrow-down fa-3x text-muted"></i>
                    <p class="text-muted mt-2"><strong>Authentification centralisée via API</strong></p>
                </div>

                <!-- Sites Clients -->
                @foreach($stats['sites'] as $site)
                    @if($site['name'] !== 'Administration')
                    <div class="col-md-6 mb-4">
                        <div class="card border-secondary shadow">
                            <div class="card-header" style="background-color: {{ $site['color'] }}; color: white;">
                                <h5 class="mb-0">
                                    <i class="fas fa-globe"></i> {{ $site['name'] }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Rôle:</strong> <span class="badge bg-secondary">{{ $site['role'] }}</span>
                                </div>
                                <div class="mb-2">
                                    <i class="fas fa-link"></i> <strong>Domaine:</strong> {{ $site['domain'] }}
                                </div>
                                <div class="mb-2">
                                    <i class="fas fa-users"></i> <strong>Utilisateurs autorisés:</strong> {{ $site['users'] }}
                                </div>
                                <div class="mb-2">
                                    <i class="fas fa-lock"></i> <strong>Permissions:</strong> {{ $site['permissions'] }}
                                </div>
                                <div class="mt-3">
                                    <h6 class="text-muted">Middlewares:</h6>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <i class="fas fa-check text-success"></i> CentralAuth
                                        </li>
                                        <li class="list-group-item">
                                            <i class="fas fa-check text-success"></i> CheckSitePermission
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Onglet Flux d'authentification -->
        <div class="tab-pane fade" id="flux" role="tabpanel">
            <div class="row">
                <!-- Flux de connexion -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-sign-in-alt"></i> Flux de Connexion</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item mb-3">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <span class="badge bg-primary">Étape 1</span>
                                        <p class="mb-0">Utilisateur visite commercial.mgs.mg</p>
                                    </div>
                                </div>
                                <div class="timeline-item mb-3">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <span class="badge bg-primary">Étape 2</span>
                                        <p class="mb-0">Redirection vers administration.mgs.mg/sso/login</p>
                                    </div>
                                </div>
                                <div class="timeline-item mb-3">
                                    <div class="timeline-marker bg-warning"></div>
                                    <div class="timeline-content">
                                        <span class="badge bg-warning">Étape 3</span>
                                        <p class="mb-0">Saisie des identifiants utilisateur</p>
                                    </div>
                                </div>
                                <div class="timeline-item mb-3">
                                    <div class="timeline-marker bg-warning"></div>
                                    <div class="timeline-content">
                                        <span class="badge bg-warning">Étape 4</span>
                                        <p class="mb-0">Vérification des permissions pour le site</p>
                                    </div>
                                </div>
                                <div class="timeline-item mb-3">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <span class="badge bg-success">Étape 5</span>
                                        <p class="mb-0">Génération du token Sanctum</p>
                                    </div>
                                </div>
                                <div class="timeline-item mb-3">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <span class="badge bg-success">Étape 6</span>
                                        <p class="mb-0">Redirection vers commercial.mgs.mg avec token</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <span class="badge bg-success">Étape 7</span>
                                        <p class="mb-0">Stockage du token en session</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Flux de vérification -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-check-circle"></i> Flux de Vérification</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item mb-3">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <span class="badge bg-primary">Étape 1</span>
                                        <p class="mb-0">Navigation sur le site client</p>
                                    </div>
                                </div>
                                <div class="timeline-item mb-3">
                                    <div class="timeline-marker bg-warning"></div>
                                    <div class="timeline-content">
                                        <span class="badge bg-warning">Étape 2</span>
                                        <p class="mb-0">Middleware CentralAuth intercepte</p>
                                    </div>
                                </div>
                                <div class="timeline-item mb-3">
                                    <div class="timeline-marker bg-warning"></div>
                                    <div class="timeline-content">
                                        <span class="badge bg-warning">Étape 3</span>
                                        <p class="mb-0">Vérification du token auprès du serveur central</p>
                                    </div>
                                </div>
                                <div class="timeline-item mb-3">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <span class="badge bg-info">Étape 4</span>
                                        <p class="mb-0">CheckSitePermission vérifie les autorisations</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <span class="badge bg-success">Étape 5</span>
                                        <p class="mb-0">Accès autorisé ou refusé (403)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Flux de déconnexion -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="fas fa-sign-out-alt"></i> Flux de Déconnexion</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div class="text-center p-3 flex-fill">
                                    <i class="fas fa-mouse-pointer fa-2x text-danger mb-2"></i>
                                    <p class="mb-0 small">Clic déconnexion</p>
                                </div>
                                <i class="fas fa-arrow-right fa-2x text-muted"></i>
                                <div class="text-center p-3 flex-fill">
                                    <i class="fas fa-trash-alt fa-2x text-warning mb-2"></i>
                                    <p class="mb-0 small">Session locale effacée</p>
                                </div>
                                <i class="fas fa-arrow-right fa-2x text-muted"></i>
                                <div class="text-center p-3 flex-fill">
                                    <i class="fas fa-server fa-2x text-primary mb-2"></i>
                                    <p class="mb-0 small">Redirection vers serveur central</p>
                                </div>
                                <i class="fas fa-arrow-right fa-2x text-muted"></i>
                                <div class="text-center p-3 flex-fill">
                                    <i class="fas fa-shield-alt fa-2x text-danger mb-2"></i>
                                    <p class="mb-0 small">Destruction session centrale</p>
                                </div>
                                <i class="fas fa-arrow-right fa-2x text-muted"></i>
                                <div class="text-center p-3 flex-fill">
                                    <i class="fas fa-home fa-2x text-success mb-2"></i>
                                    <p class="mb-0 small">Retour au site d'origine</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet Rôles & Permissions -->
        <div class="tab-pane fade" id="roles" role="tabpanel">
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="fas fa-users-cog"></i> Rôles du Système</h5>
                        </div>
                        <div class="card-body">
                            @if(isset($stats['rolesList']) && $stats['rolesList']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><i class="fas fa-tag"></i> Rôle</th>
                                            <th><i class="fas fa-users"></i> Utilisateurs</th>
                                            <th><i class="fas fa-key"></i> Permissions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stats['rolesList'] as $role)
                                        <tr>
                                            <td><span class="badge bg-primary">{{ $role->name }}</span></td>
                                            <td>{{ $role->users_count }}</td>
                                            <td>{{ $role->permissions_count }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Aucun rôle configuré. 
                                <a href="#" class="alert-link">Créer des rôles</a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Structure des permissions par site -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="fas fa-sitemap"></i> Structure des Permissions par Site</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="text-primary"><i class="fas fa-server"></i> Administration</h6>
                                    <ul class="list-group">
                                        <li class="list-group-item"><code>admin.view_dashboard</code></li>
                                        <li class="list-group-item"><code>admin.manage_users</code></li>
                                        <li class="list-group-item"><code>admin.manage_roles</code></li>
                                        <li class="list-group-item"><code>admin.manage_permissions</code></li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-warning"><i class="fas fa-briefcase"></i> Commercial</h6>
                                    <ul class="list-group">
                                        <li class="list-group-item"><code>commercial.view_dashboard</code></li>
                                        <li class="list-group-item"><code>commercial.manage_clients</code></li>
                                        <li class="list-group-item"><code>commercial.create_invoice</code></li>
                                        <li class="list-group-item"><code>commercial.view_reports</code></li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-success"><i class="fas fa-folder-open"></i> Gestion Dossier</h6>
                                    <ul class="list-group">
                                        <li class="list-group-item"><code>debours.view_expenses</code></li>
                                        <li class="list-group-item"><code>debours.approve_expenses</code></li>
                                        <li class="list-group-item"><code>debours.create_payment</code></li>
                                        <li class="list-group-item"><code>debours.view_reports</code></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet Statistiques -->
        <div class="tab-pane fade" id="stats" role="tabpanel">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <h2>{{ $stats['users'] }}</h2>
                            <p class="mb-0">Utilisateurs</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-user-tag fa-3x mb-3"></i>
                            <h2>{{ $stats['roles'] }}</h2>
                            <p class="mb-0">Rôles</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-key fa-3x mb-3"></i>
                            <h2>{{ $stats['permissions'] }}</h2>
                            <p class="mb-0">Permissions</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-globe fa-3x mb-3"></i>
                            <h2>{{ count($stats['sites']) }}</h2>
                            <p class="mb-0">Sites</p>
                        </div>
                    </div>
                </div>

                <!-- Utilisateurs récents -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-user-clock"></i> Utilisateurs Récents</h5>
                        </div>
                        <div class="card-body">
                            @if(isset($stats['recentUsers']) && $stats['recentUsers']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Rôles</th>
                                            <th>Date de création</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stats['recentUsers'] as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @foreach($user->roles as $role)
                                                <span class="badge bg-secondary">{{ $role->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle"></i> Aucun utilisateur trouvé.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Avantages du système -->
    <div class="card mt-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-check-circle"></i> Avantages du Système SSO Centralisé</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-server fa-2x text-primary me-3"></i>
                        <div>
                            <h6>Centralisation Totale</h6>
                            <p class="text-muted small">Un seul point de gestion pour tous les utilisateurs, rôles et permissions</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-shield-alt fa-2x text-success me-3"></i>
                        <div>
                            <h6>Sécurité Renforcée</h6>
                            <p class="text-muted small">Authentification par token, vérification à chaque requête</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-expand-arrows-alt fa-2x text-info me-3"></i>
                        <div>
                            <h6>Scalabilité</h6>
                            <p class="text-muted small">Ajout facile de nouveaux sites sans modification majeure</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-user-check fa-2x text-warning me-3"></i>
                        <div>
                            <h6>Expérience Utilisateur</h6>
                            <p class="text-muted small">Single Sign-On : une seule connexion pour tous les sites</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-cogs fa-2x text-danger me-3"></i>
                        <div>
                            <h6>Gestion Granulaire</h6>
                            <p class="text-muted small">Permissions par site et par fonctionnalité</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-wrench fa-2x text-secondary me-3"></i>
                        <div>
                            <h6>Maintenance Simplifiée</h6>
                            <p class="text-muted small">Modifications centralisées, déploiement facilité</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -23px;
    top: 24px;
    width: 2px;
    height: calc(100% + 12px);
    background: #dee2e6;
}

.timeline-item:last-child::before {
    display: none;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    padding: 10px;
    background: #f8f9fa;
    border-radius: 4px;
}

.bg-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.opacity-50 {
    opacity: 0.5;
}
</style>

<script>
// Animation au chargement des cartes
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 50);
        }, index * 50);
    });
});
</script>
@endsection
