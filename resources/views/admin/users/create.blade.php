@extends('layouts.admin')

@section('title', 'Créer un Utilisateur')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1><i class="fas fa-user-plus"></i> Créer un Utilisateur</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Utilisateurs</a></li>
                <li class="breadcrumb-item active">Créer</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
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
                                   value="{{ old('email') }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required>
                            <small class="text-muted">Minimum 8 caractères</small>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required>
                        </div>

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
                                                   {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
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
                                <i class="fas fa-save"></i> Créer l'utilisateur
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informations</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Rôles disponibles:</strong></p>
                    <ul class="list-unstyled mb-3">
                        @foreach($roles as $role)
                            <li>
                                <span class="badge bg-{{ $role->name === 'Super Admin' ? 'danger' : 'primary' }}">
                                    {{ $role->name }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                    
                    <hr>
                    
                    <p class="mb-0 text-muted">
                        <i class="fas fa-lightbulb"></i> 
                        Un email sera envoyé à l'utilisateur avec ses identifiants de connexion.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
