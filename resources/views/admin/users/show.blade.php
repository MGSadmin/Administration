@extends('layouts.admin')

@section('title', 'Détails de l\'Utilisateur')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1><i class="fas fa-user"></i> {{ $user->name }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Utilisateurs</a></li>
                <li class="breadcrumb-item active">{{ $user->name }}</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Informations de base</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Nom:</strong><br>
                            {{ $user->name }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Email:</strong><br>
                            {{ $user->email }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Créé le:</strong><br>
                            {{ $user->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Modifié le:</strong><br>
                            {{ $user->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Rôles et Permissions</h5>
                </div>
                <div class="card-body">
                    <h6>Rôles:</h6>
                    <div class="mb-3">
                        @forelse($user->roles as $role)
                            <span class="badge bg-{{ $role->name === 'Super Admin' ? 'danger' : 'primary' }} me-1">
                                {{ $role->name }}
                            </span>
                        @empty
                            <p class="text-muted">Aucun rôle attribué</p>
                        @endforelse
                    </div>

                    @if($user->getAllPermissions()->count() > 0)
                        <h6>Permissions:</h6>
                        <div class="row">
                            @foreach($user->getAllPermissions()->groupBy(fn($p) => explode('.', $p->name)[0]) as $prefix => $permissions)
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-uppercase text-muted">{{ $prefix }}</h6>
                                    <ul class="list-unstyled">
                                        @foreach($permissions as $permission)
                                            <li><i class="fas fa-check text-success"></i> {{ $permission->name }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            @if($user->tokens()->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Tokens d'authentification</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Créé le</th>
                                        <th>Dernière utilisation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->tokens as $token)
                                        <tr>
                                            <td>{{ $token->name }}</td>
                                            <td>{{ $token->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if($token->last_used_at)
                                                    {{ $token->last_used_at->format('d/m/Y H:i') }}
                                                @else
                                                    <span class="text-muted">Jamais</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    
                    @if($user->tokens()->count() > 0)
                        <form action="{{ route('admin.users.revoke-tokens', $user) }}" 
                              method="POST"
                              onsubmit="return confirm('Révoquer tous les tokens ?')">
                            @csrf
                            <button type="submit" class="btn btn-secondary w-100">
                                <i class="fas fa-key"></i> Révoquer les tokens
                            </button>
                        </form>
                    @endif
                    
                    @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user) }}" 
                              method="POST"
                              onsubmit="return confirm('Supprimer cet utilisateur ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Statistiques</h5>
                </div>
                <div class="card-body">
                    <p><strong>Tokens actifs:</strong><br>
                        <span class="badge bg-{{ $user->tokens()->count() > 0 ? 'success' : 'secondary' }}">
                            {{ $user->tokens()->count() }}
                        </span>
                    </p>
                    <p><strong>Rôles:</strong><br>
                        <span class="badge bg-primary">{{ $user->roles()->count() }}</span>
                    </p>
                    <p><strong>Permissions:</strong><br>
                        <span class="badge bg-info">{{ $user->getAllPermissions()->count() }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
