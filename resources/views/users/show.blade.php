@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-user"></i> Détails de l'utilisateur</h1>
        <div>
            <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informations personnelles -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-id-card"></i> Informations personnelles</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Nom</label>
                            <p class="fw-bold">{{ $user->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Prénom</label>
                            <p class="fw-bold">{{ $user->prenom ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Email</label>
                            <p class="fw-bold"><i class="fas fa-envelope me-2"></i>{{ $user->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Matricule</label>
                            <p class="fw-bold">{{ $user->matricule ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Poste</label>
                            <p class="fw-bold"><i class="fas fa-briefcase me-2"></i>{{ $user->poste ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Département</label>
                            <p class="fw-bold"><i class="fas fa-building me-2"></i>{{ $user->departement ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Date de création</label>
                            <p class="fw-bold"><i class="fas fa-calendar me-2"></i>{{ $user->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Dernière modification</label>
                            <p class="fw-bold"><i class="fas fa-clock me-2"></i>{{ $user->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label class="text-muted small">Statut</label>
                            <p>
                                @if($user->active ?? true)
                                    <span class="badge bg-success"><i class="fas fa-check-circle"></i> Actif</span>
                                @else
                                    <span class="badge bg-danger"><i class="fas fa-times-circle"></i> Inactif</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rôles et permissions -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-user-tag"></i> Rôles et Permissions</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Rôles attribués</label>
                        <div>
                            @forelse($user->roles as $role)
                                <span class="badge bg-primary me-2 mb-2">
                                    <i class="fas fa-shield-alt"></i> {{ ucfirst($role->name) }}
                                </span>
                            @empty
                                <p class="text-muted">Aucun rôle attribué</p>
                            @endforelse
                        </div>
                    </div>

                    @if($user->roles->isNotEmpty())
                        <div>
                            <label class="text-muted small">Permissions héritées</label>
                            <div class="row">
                                @php
                                    $allPermissions = $user->getAllPermissions()->groupBy(function($permission) {
                                        return explode('.', $permission->name)[0];
                                    });
                                @endphp
                                @foreach($allPermissions as $category => $permissions)
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-primary">{{ ucfirst($category) }}</h6>
                                        <ul class="list-unstyled">
                                            @foreach($permissions as $permission)
                                                <li><i class="fas fa-check text-success me-2"></i>{{ $permission->name }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Applications accessibles -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-th"></i> Applications</h5>
                </div>
                <div class="card-body">
                    @if($user->applications && $user->applications->isNotEmpty())
                        @foreach($user->applications as $app)
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span class="badge bg-secondary">{{ ucfirst($app->application) }}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">Aucune application attribuée</p>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-cog"></i> Actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning w-100 mb-2">
                        <i class="fas fa-edit"></i> Modifier l'utilisateur
                    </a>
                    <button type="button" class="btn btn-info w-100 mb-2" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                        <i class="fas fa-key"></i> Réinitialiser mot de passe
                    </button>
                    <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal réinitialisation mot de passe -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-key"></i> Réinitialiser le mot de passe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Réinitialiser</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
