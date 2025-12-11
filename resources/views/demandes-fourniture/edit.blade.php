@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="h3"><i class="fas fa-edit me-2"></i>Modifier la Demande de Fourniture</h2>
        </div>
    </div>

    <form action="{{ route('demandes-fourniture.update', $demandeFourniture) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informations de la Demande</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Numéro de Demande</label>
                            <input type="text" class="form-control" value="{{ $demandeFourniture->numero_demande }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Objet <span class="text-danger">*</span></label>
                            <input type="text" name="objet" class="form-control @error('objet') is-invalid @enderror" value="{{ old('objet', $demandeFourniture->objet) }}" required>
                            @error('objet')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description Détaillée <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description', $demandeFourniture->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Type de Fourniture <span class="text-danger">*</span></label>
                                <select name="type_fourniture" class="form-select @error('type_fourniture') is-invalid @enderror" required>
                                    @foreach(\App\Models\DemandeFourniture::getTypeFournitureLabels() as $key => $label)
                                        <option value="{{ $key }}" {{ old('type_fourniture', $demandeFourniture->type_fourniture) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('type_fourniture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantité <span class="text-danger">*</span></label>
                                <input type="number" name="quantite" class="form-control @error('quantite') is-invalid @enderror" value="{{ old('quantite', $demandeFourniture->quantite) }}" min="1" required>
                                @error('quantite')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Justification</label>
                            <textarea name="justification" class="form-control @error('justification') is-invalid @enderror" rows="3">{{ old('justification', $demandeFourniture->justification) }}</textarea>
                            @error('justification')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observation</label>
                            <textarea name="observation" class="form-control @error('observation') is-invalid @enderror" rows="2">{{ old('observation', $demandeFourniture->observation) }}</textarea>
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
                                    <option value="{{ $key }}" {{ old('priorite', $demandeFourniture->priorite) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('priorite')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Budget Estimé (Ar)</label>
                            <input type="number" name="budget_estime" class="form-control @error('budget_estime') is-invalid @enderror" value="{{ old('budget_estime', $demandeFourniture->budget_estime) }}" step="0.01">
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
                                    <option value="{{ $user->id }}" {{ old('notifier_user_id', $demandeFourniture->notifier_user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('notifier_user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                @if($demandeFourniture->statut == 'rejetee')
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    Cette demande a été rejetée. En la modifiant, elle sera automatiquement remise en attente.
                </div>
                @endif

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Mettre à Jour
                    </button>
                    <a href="{{ route('demandes-fourniture.show', $demandeFourniture) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
