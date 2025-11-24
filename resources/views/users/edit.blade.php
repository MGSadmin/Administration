@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-user-edit"></i> Modifier l'utilisateur</h1>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> <strong>Erreurs:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="row">
            <div class="col-md-8">
                <!-- Informations personnelles -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-id-card"></i> Informations personnelles</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="prenom" class="form-label">Prénom</label>
                                <input type="text" class="form-control @error('prenom') is-invalid @enderror" 
                                       id="prenom" name="prenom" value="{{ old('prenom', $user->prenom) }}">
                                @error('prenom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="matricule" class="form-label">Matricule</label>
                                <input type="text" class="form-control @error('matricule') is-invalid @enderror" 
                                       id="matricule" name="matricule" value="{{ old('matricule', $user->matricule) }}">
                                @error('matricule')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="poste" class="form-label">Poste</label>
                                <input type="text" class="form-control @error('poste') is-invalid @enderror" 
                                       id="poste" name="poste" value="{{ old('poste', $user->poste) }}">
                                @error('poste')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="departement" class="form-label">Département</label>
                                <input type="text" class="form-control @error('departement') is-invalid @enderror" 
                                       id="departement" name="departement" value="{{ old('departement', $user->departement) }}">
                                @error('departement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Compte actif
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mot de passe (optionnel) -->
                <div class="card mb-4">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="fas fa-key"></i> Modifier le mot de passe (optionnel)</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Laissez vide si vous ne souhaitez pas changer le mot de passe</p>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Nouveau mot de passe</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Rôles -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-user-tag"></i> Rôles</h5>
                    </div>
                    <div class="card-body">
                        @foreach($roles as $role)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="role_{{ $role->id }}" 
                                       name="roles[]" value="{{ $role->name }}"
                                       {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                                <label class="form-check-label" for="role_{{ $role->id }}">
                                    <span class="badge bg-primary">{{ ucfirst($role->name) }}</span>
                                    <br>
                                    <small class="text-muted">{{ $role->permissions->count() }} permissions</small>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Applications -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-th"></i> Applications autorisées</h5>
                    </div>
                    <div class="card-body">
                        @foreach($applications as $app)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="app_{{ $app }}" 
                                       name="applications[]" value="{{ $app }}"
                                       {{ $user->applications->contains('application', $app) ? 'checked' : '' }}>
                                <label class="form-check-label" for="app_{{ $app }}">
                                    {{ ucfirst($app) }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save"></i> Enregistrer les modifications
                        </button>
                        <a href="{{ route('users.show', $user) }}" class="btn btn-secondary w-100 mb-2">
                            <i class="fas fa-eye"></i> Voir le profil
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
