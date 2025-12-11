@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('organigramme.members.show', $member) }}" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="fas fa-arrow-left me-1"></i> Retour au profil
            </a>
            <h1 class="h3 mb-0">
                <i class="fas fa-edit me-2"></i>Modifier - {{ $member->name }}
            </h1>
            <p class="text-muted mb-0">{{ $member->position->title ?? 'Aucun poste' }} - {{ $member->position->department->name ?? '' }}</p>
        </div>
    </div>

    <!-- Carte d'informations complètes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>Informations actuelles du membre</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center mb-3">
                                @if($member->photo)
                                    <img src="{{ asset('storage/' . $member->photo) }}" class="rounded-circle mb-2" style="width: 120px; height: 120px; object-fit: cover;" alt="Photo">
                                @else
                                    <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 120px; height: 120px;">
                                        <i class="fas fa-user fa-3x text-white"></i>
                                    </div>
                                @endif
                                <div>
                                    @if($member->status === 'ACTIF')
                                        <span class="badge bg-success">{{ $member->status }}</span>
                                    @elseif($member->status === 'VACANT')
                                        <span class="badge bg-warning">{{ $member->status }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $member->status }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small fw-bold">Nom complet</label>
                                    <p class="mb-0 fs-5">{{ $member->name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small fw-bold">Email</label>
                                    <p class="mb-0">
                                        <i class="fas fa-envelope text-primary me-1"></i>
                                        {{ $member->email ?? 'Non renseigné' }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small fw-bold">Téléphone</label>
                                    <p class="mb-0">
                                        <i class="fas fa-phone text-success me-1"></i>
                                        {{ $member->phone ?? 'Non renseigné' }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small fw-bold">Utilisateur lié</label>
                                    <p class="mb-0">
                                        @if($member->user)
                                            <i class="fas fa-user-check text-success me-1"></i>
                                            {{ $member->user->name }}
                                        @else
                                            <i class="fas fa-user-times text-muted me-1"></i>
                                            <span class="text-muted">Aucun</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <label class="text-muted small fw-bold">Poste</label>
                            <p class="mb-0">
                                <i class="fas fa-briefcase text-info me-1"></i>
                                {{ $member->position->title ?? 'Non assigné' }}
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small fw-bold">Département</label>
                            <p class="mb-0">
                                <i class="fas fa-building text-warning me-1"></i>
                                {{ $member->position->department->name ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small fw-bold">Niveau hiérarchique</label>
                            <p class="mb-0">
                                <i class="fas fa-layer-group text-secondary me-1"></i>
                                Niveau {{ $member->position->level ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                    
                    @if($member->position && $member->position->parent_position_id)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="text-muted small fw-bold">Supérieur hiérarchique</label>
                            <p class="mb-0">
                                @php
                                    $superior = $member->getSuperior();
                                @endphp
                                @if($superior)
                                    <i class="fas fa-user-tie text-primary me-1"></i>
                                    {{ $superior->name }} - {{ $superior->position->title ?? '' }}
                                @else
                                    <span class="text-muted">Aucun supérieur défini</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    @endif
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold">Date d'entrée</label>
                            <p class="mb-0">
                                <i class="fas fa-calendar-plus text-success me-1"></i>
                                {{ $member->start_date ? $member->start_date->format('d/m/Y') : 'Non renseignée' }}
                            </p>
                        </div>
                        @if($member->end_date)
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold">Date de fin</label>
                            <p class="mb-0">
                                <i class="fas fa-calendar-times text-danger me-1"></i>
                                {{ $member->end_date->format('d/m/Y') }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Formulaire de modification</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('organigramme.members.update', $member) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Section Assignation utilisateur -->
                        @if($member->status === 'VACANT' || $member->status === 'ACTIF')
                        <div class="alert alert-info mb-4">
                            <h6 class="alert-heading"><i class="fas fa-user-plus me-2"></i>Assignation d'utilisateur</h6>
                            <p class="mb-2 small">
                                @if($member->status === 'VACANT')
                                    Ce poste est actuellement <strong>VACANT</strong>. Vous pouvez assigner un utilisateur à ce poste.
                                @else
                                    Ce poste est occupé par <strong>{{ $member->user ? $member->user->name : $member->name }}</strong>. Vous pouvez changer l'utilisateur assigné.
                                @endif
                            </p>
                            
                            <div class="mb-3">
                                <label for="user_id" class="form-label">
                                    <i class="fas fa-user me-1"></i>Utilisateur assigné
                                </label>
                                <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                    <option value="">-- Laisser vacant --</option>
                                    @php
                                        $availableUsers = \App\Models\User::where('is_active', true)
                                            ->whereDoesntHave('organizationMember', function($q) use ($member) {
                                                $q->where('status', 'ACTIF')
                                                  ->where('id', '!=', $member->id);
                                            })
                                            ->orderBy('name')
                                            ->get();
                                    @endphp
                                    @foreach($availableUsers as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', $member->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Les informations (nom, email, téléphone) seront automatiquement synchronisées depuis l'utilisateur sélectionné.
                                </small>
                            </div>
                        </div>
                        <hr class="my-4">
                        @endif

                        <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Informations personnelles</h6>
                        
                        @if($member->user_id)
                        <div class="alert alert-warning alert-sm mb-3">
                            <i class="fas fa-lock me-1"></i>
                            <small>Ces informations sont synchronisées avec l'utilisateur lié. Pour les modifier, changez d'abord l'utilisateur assigné ou dissociez-le.</small>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $member->name) }}" 
                                   {{ $member->user_id ? 'readonly' : 'required' }}>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($member->user_id)
                                <small class="form-text text-muted">
                                    <i class="fas fa-sync-alt me-1"></i>Synchronisé avec l'utilisateur {{ $member->user->name }}
                                </small>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $member->email) }}" 
                                   {{ $member->user_id ? 'readonly' : 'required' }}>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($member->user_id)
                                <small class="form-text text-muted">
                                    <i class="fas fa-sync-alt me-1"></i>Synchronisé avec l'utilisateur
                                </small>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Téléphone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $member->phone) }}"
                                   {{ $member->user_id ? 'readonly' : '' }}>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($member->user_id)
                                <small class="form-text text-muted">
                                    <i class="fas fa-sync-alt me-1"></i>Synchronisé avec l'utilisateur
                                </small>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="start_date" class="form-label">Date d'entrée</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" 
                                   value="{{ old('start_date', $member->start_date?->format('Y-m-d')) }}">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="linked_user_id" class="form-label">
                                <i class="fas fa-link me-1"></i>Assigner à (utilisateur)
                            </label>
                            <select name="linked_user_id" id="linked_user_id" class="form-select @error('linked_user_id') is-invalid @enderror">
                                <option value="">-- Aucun utilisateur lié --</option>
                                @php
                                    $allUsers = \App\Models\User::where('is_active', true)->orderBy('name')->get();
                                @endphp
                                @foreach($allUsers as $user)
                                    <option value="{{ $user->id }}" 
                                            {{ old('linked_user_id', $member->user_id) == $user->id ? 'selected' : '' }}
                                            @if($user->organizationMember && $user->organizationMember->id != $member->id && $user->organizationMember->status === 'ACTIF')
                                                disabled
                                            @endif>
                                        {{ $user->name }} ({{ $user->email }})
                                        @if($user->organizationMember && $user->organizationMember->id != $member->id && $user->organizationMember->status === 'ACTIF')
                                            - Déjà assigné
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('linked_user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Lie ce membre à un compte utilisateur. <strong>Les informations seront automatiquement synchronisées</strong> (nom, email, téléphone).
                            </small>
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3"><i class="fas fa-briefcase me-2"></i>Poste et position</h6>

                        <div class="mb-3">
                            <label for="position_id" class="form-label">
                                <i class="fas fa-briefcase me-1"></i>Poste <span class="text-danger">*</span>
                            </label>
                            <select name="position_id" id="position_id" class="form-select @error('position_id') is-invalid @enderror" required>
                                <option value="">-- Sélectionner un poste --</option>
                                @php
                                    $positions = \App\Models\Position::with('department')->orderBy('title')->get();
                                @endphp
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}" 
                                            {{ old('position_id', $member->position_id) == $position->id ? 'selected' : '' }}>
                                        {{ $position->title }} - {{ $position->department->name ?? 'N/A' }} (Niveau {{ $position->level }})
                                    </option>
                                @endforeach
                            </select>
                            @error('position_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Attention : Changer le poste peut affecter la hiérarchie organisationnelle.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">
                                <i class="fas fa-check-circle me-1"></i>Statut
                            </label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="ACTIF" {{ old('status', $member->status) === 'ACTIF' ? 'selected' : '' }}>ACTIF</option>
                                <option value="VACANT" {{ old('status', $member->status) === 'VACANT' ? 'selected' : '' }}>VACANT</option>
                                <option value="INTERIM" {{ old('status', $member->status) === 'INTERIM' ? 'selected' : '' }}>INTERIM</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Enregistrer les modifications
                            </button>
                            <a href="{{ route('organigramme.members.show', $member) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-sitemap me-2"></i>Position dans l'organigramme</h5>
                </div>
                <div class="card-body">
                    @if($member->position)
                        <div class="mb-3">
                            <label class="text-muted small">Poste actuel</label>
                            <p class="fw-bold mb-0">{{ $member->position->title }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Département</label>
                            <p class="mb-0">{{ $member->position->department->name ?? 'N/A' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Niveau hiérarchique</label>
                            <p class="mb-0">Niveau {{ $member->position->level }}</p>
                        </div>
                        
                        @if($member->position->parent_position_id)
                            @php
                                $parentMember = \App\Models\OrganizationMember::where('position_id', $member->position->parent_position_id)
                                    ->where('status', 'ACTIF')
                                    ->first();
                            @endphp
                            @if($parentMember)
                                <div class="mb-3">
                                    <label class="text-muted small">Supérieur hiérarchique</label>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-arrow-up text-primary me-2"></i>
                                        <div>
                                            <strong>{{ $parentMember->name }}</strong><br>
                                            <small class="text-muted">{{ $parentMember->position->title ?? '' }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                        
                        @php
                            $subordinates = \App\Models\OrganizationMember::whereHas('position', function($q) use ($member) {
                                $q->where('parent_position_id', $member->position_id);
                            })->where('status', 'ACTIF')->get();
                        @endphp
                        
                        @if($subordinates->count() > 0)
                            <div class="mb-0">
                                <label class="text-muted small">Subordonnés directs ({{ $subordinates->count() }})</label>
                                <div class="list-group list-group-flush">
                                    @foreach($subordinates as $subordinate)
                                        <div class="list-group-item px-0 py-2">
                                            <i class="fas fa-arrow-down text-success me-2"></i>
                                            <strong>{{ $subordinate->name }}</strong><br>
                                            <small class="text-muted">{{ $subordinate->position->title ?? '' }}</small>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        @if($member->position->description)
                        <hr>
                        <div class="mb-0">
                            <label class="text-muted small">Description du poste</label>
                            <p class="small mb-0">{{ $member->position->description }}</p>
                        </div>
                        @endif
                        
                        @if($member->position->responsibilities)
                        <div class="mt-2">
                            <label class="text-muted small">Responsabilités</label>
                            <p class="small mb-0">{{ $member->position->responsibilities }}</p>
                        </div>
                        @endif
                    @else
                        <p class="text-muted mb-0">Aucun poste assigné</p>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Attention</h5>
                </div>
                <div class="card-body">
                    <p class="small mb-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Les modifications seront également appliquées au compte utilisateur associé si disponible.
                    </p>
                    <p class="small mb-0">
                        <i class="fas fa-shield-alt me-1"></i>
                        Seuls les administrateurs et RH peuvent modifier ces informations.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
