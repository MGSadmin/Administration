@extends('layouts.admin')

@section('title', 'Modifier un Utilisateur')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1><i class="fas fa-user-edit"></i> Modifier l'Utilisateur</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Utilisateurs</a></li>
                <li class="breadcrumb-item active">Modifier {{ $user->name }}</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <h5 class="mb-3">Changer le mot de passe (optionnel)</h5>

                        <div class="mb-3">
                            <label for="password" class="form-label">Nouveau mot de passe</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password">
                            <small class="text-muted">Laisser vide pour conserver le mot de passe actuel</small>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmer le nouveau mot de passe</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation">
                        </div>

                        <hr>

                        <div class="mb-4">
                            <label class="form-label">Rôles</label>
                            <div class="card">
                                <div class="card-body">
                                    @foreach($roles as $role)
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="roles[]" 
                                                   value="{{ $role->name }}" 
                                                   id="role_{{ $role->id }}"
                                                   {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="role_{{ $role->id }}">
                                                <strong>{{ $role->name }}</strong>
                                                @if($role->permissions_count ?? 0)
                                                    <small class="text-muted">({{ $role->permissions_count }} permissions)</small>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                    @error('roles')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informations</h5>
                </div>
                <div class="card-body">
                    <p><strong>Créé le:</strong><br>{{ $user->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Modifié le:</strong><br>{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Tokens actifs:</strong><br>
                        <span class="badge bg-{{ $user->tokens()->count() > 0 ? 'success' : 'secondary' }}">
                            {{ $user->tokens()->count() }}
                        </span>
                    </p>
                </div>
            </div>

            @if($user->tokens()->count() > 0)
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-key"></i> Tokens</h5>
                    </div>
                    <div class="card-body">
                        <p>Cet utilisateur a {{ $user->tokens()->count() }} token(s) actif(s).</p>
                        <form action="{{ route('admin.users.revoke-tokens', $user) }}" 
                              method="POST"
                              onsubmit="return confirm('Révoquer tous les tokens de {{ $user->name }} ?')">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm w-100">
                                <i class="fas fa-ban"></i> Révoquer tous les tokens
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
