@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="h3"><i class="fas fa-plus-circle me-2"></i>Nouvelle Demande de Fourniture</h2>
        </div>
    </div>

    <form action="{{ route('demandes-fourniture.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informations de la Demande</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Objet <span class="text-danger">*</span></label>
                            <input type="text" name="objet" class="form-control @error('objet') is-invalid @enderror" value="{{ old('objet') }}" required placeholder="Ex: Achat d'ordinateur portable">
                            @error('objet')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Désignation <span class="text-danger">*</span></label>
                            <input type="text" name="designation" class="form-control @error('designation') is-invalid @enderror" value="{{ old('designation') }}" required placeholder="Ex: HP ProBook 450 G8, Intel Core i5, 8GB RAM, 256GB SSD">
                            <small class="form-text text-muted">Désignation précise du matériel/fourniture (obligatoire pour validation)</small>
                            @error('designation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description Détaillée <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" required placeholder="Décrivez précisément votre besoin...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Type de Fourniture <span class="text-danger">*</span></label>
                                <select name="type_fourniture" class="form-select @error('type_fourniture') is-invalid @enderror" required>
                                    <option value="">Sélectionner...</option>
                                    @foreach(\App\Models\DemandeFourniture::getTypeFournitureLabels() as $key => $label)
                                        <option value="{{ $key }}" {{ old('type_fourniture') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('type_fourniture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantité <span class="text-danger">*</span></label>
                                <input type="number" name="quantite" class="form-control @error('quantite') is-invalid @enderror" value="{{ old('quantite', 1) }}" min="1" required>
                                @error('quantite')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Justification</label>
                            <textarea name="justification" class="form-control @error('justification') is-invalid @enderror" rows="3" placeholder="Expliquez pourquoi cette fourniture est nécessaire...">{{ old('justification') }}</textarea>
                            @error('justification')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observation</label>
                            <textarea name="observation" class="form-control @error('observation') is-invalid @enderror" rows="2">{{ old('observation') }}</textarea>
                            @error('observation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Priorité et Budget</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Priorité <span class="text-danger">*</span></label>
                            <select name="priorite" class="form-select @error('priorite') is-invalid @enderror" required>
                                @foreach(\App\Models\DemandeFourniture::getPrioriteLabels() as $key => $label)
                                    <option value="{{ $key }}" {{ old('priorite', 'normale') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('priorite')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Budget Estimé (Ar)</label>
                            <input type="number" name="budget_estime" class="form-control @error('budget_estime') is-invalid @enderror" value="{{ old('budget_estime') }}" step="0.01" placeholder="0.00">
                            @error('budget_estime')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Notification</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Personne à notifier</label>
                            <select name="notifier_user_id" class="form-select @error('notifier_user_id') is-invalid @enderror">
                                <option value="">Aucune notification additionnelle</option>
                                @foreach($utilisateurs as $user)
                                    <option value="{{ $user->id }}" {{ old('notifier_user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('notifier_user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Cette personne sera notifiée automatiquement à chaque changement de statut de la demande.</small>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Information :</strong><br>
                    Vous serez automatiquement notifié des changements de statut de votre demande.
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane"></i> Soumettre la Demande
                    </button>
                    <a href="{{ route('demandes-fourniture.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
