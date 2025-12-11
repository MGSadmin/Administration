@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-shield-alt"></i> Gestion des Rôles</h1>
        
        @can('Créer Rôle')
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Créer un Rôle
            </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 20%;">Rôle</th>
                            <th style="width: 10%;" class="text-center">Utilisateurs</th>
                            <th style="width: 10%;" class="text-center">Permissions</th>
                            <th style="width: 45%;">Principales Permissions</th>
                            <th style="width: 15%;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td>
                                    <strong class="text-primary">{{ ucfirst($role->name) }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $role->users_count ?? 0 }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success">{{ $role->permissions_count ?? 0 }}</span>
                                </td>
                                <td>
                                    @php
                                        $permissions = $role->permissions->take(5);
                                        $remaining = $role->permissions->count() - 5;
                                    @endphp
                                    
                                    @foreach($permissions as $permission)
                                        <span class="badge bg-secondary me-1 mb-1">{{ $permission->name }}</span>
                                    @endforeach
                                    
                                    @if($remaining > 0)
                                        <span class="badge bg-dark">+{{ $remaining }} autres</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.roles.show', $role) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @can('Modifier Rôle')
                                            <a href="{{ route('admin.roles.edit', $role) }}" 
                                               class="btn btn-sm btn-warning" 
                                               title="Modifier Rôle">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan

                                        @can('Supprimer Rôle')
                                            @if($role->name !== 'super-admin' && $role->users_count == 0)
                                                <form action="{{ route('admin.roles.destroy', $role) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce rôle ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="Supprimer Rôle">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle"></i> Aucun rôle trouvé
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($roles->hasPages())
                <div class="mt-3">
                    {{ $roles->links() }}
                </div>
            @endif
        </div>
    </div>

    <div class="card mt-4 border-info">
        <div class="card-header bg-info text-white">
            <i class="fas fa-info-circle"></i> Informations sur les Rôles
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <h6 class="text-primary">Super Admin</h6>
                    <p class="small text-muted">Accès complet à toutes les fonctionnalités du système.</p>
                </div>
                <div class="col-md-4">
                    <h6 class="text-primary">Administrateur</h6>
                    <p class="small text-muted">Gestion complète de tous les modules sauf suppression critique.</p>
                </div>
                <div class="col-md-4">
                    <h6 class="text-primary">RH</h6>
                    <p class="small text-muted">Gestion des ressources humaines et du personnel.</p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4">
                    <h6 class="text-primary">Direction</h6>
                    <p class="small text-muted">Validation et supervision des opérations.</p>
                </div>
                <div class="col-md-4">
                    <h6 class="text-primary">Chef de Département</h6>
                    <p class="small text-muted">Gestion de son équipe et de son département.</p>
                </div>
                <div class="col-md-4">
                    <h6 class="text-primary">Employé</h6>
                    <p class="small text-muted">Accès de base pour les opérations quotidiennes.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
