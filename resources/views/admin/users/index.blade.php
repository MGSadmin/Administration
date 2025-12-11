@extends('layouts.admin')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-users"></i> Gestion des Utilisateurs</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvel Utilisateur
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Rechercher</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Nom ou email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Filtrer par rôle</label>
                    <select name="role" class="form-select">
                        <option value="">Tous les rôles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Statut organigramme</label>
                    <select name="member_status" class="form-select">
                        <option value="">Tous</option>
                        <option value="with_member" {{ request('member_status') == 'with_member' ? 'selected' : '' }}>
                            Avec poste
                        </option>
                        <option value="without_member" {{ request('member_status') == 'without_member' ? 'selected' : '' }}>
                            Sans poste
                        </option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary me-2">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des utilisateurs -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Poste/Organisation</th>
                            <th>Rôles</th>
                            <th>Tokens actifs</th>
                            <th>Créé le</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <strong>{{ $user->name }}</strong>
                                    @if($user->id === auth()->id())
                                        <span class="badge bg-info">Vous</span>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->organizationMember)
                                        <div>
                                            <i class="fas fa-briefcase text-primary me-1"></i>
                                            <strong>{{ $user->organizationMember->position->title ?? 'N/A' }}</strong>
                                        </div>
                                        <small class="text-muted">
                                            {{ $user->organizationMember->position->department->name ?? '' }}
                                        </small>
                                        <div class="mt-1">
                                            <span class="badge bg-{{ $user->organizationMember->status === 'ACTIF' ? 'success' : 'warning' }}">
                                                {{ $user->organizationMember->status }}
                                            </span>
                                            <a href="{{ route('organigramme.members.show', $user->organizationMember) }}" 
                                               class="badge bg-info text-decoration-none" target="_blank">
                                                <i class="fas fa-external-link-alt"></i> Voir profil
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-user-times me-1"></i>Aucun poste assigné
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @forelse($user->roles as $role)
                                        <span class="badge bg-{{ $role->name === 'Super Admin' ? 'danger' : 'primary' }} me-1">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-muted">Aucun rôle</span>
                                    @endforelse
                                </td>
                                <td>
                                    @php
                                        $tokensCount = $user->tokens()->count();
                                    @endphp
                                    @if($tokensCount > 0)
                                        <span class="badge bg-success">{{ $tokensCount }}</span>
                                    @else
                                        <span class="badge bg-secondary">0</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.users.show', $user) }}" 
                                           class="btn btn-sm btn-info" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" 
                                           class="btn btn-sm btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($tokensCount > 0)
                                            <form action="{{ route('admin.users.revoke-tokens', $user) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Révoquer tous les tokens de {{ $user->name }} ?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-secondary" 
                                                        title="Révoquer les tokens">
                                                    <i class="fas fa-key"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Supprimer {{ $user->name }} ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <p>Aucun utilisateur trouvé</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
