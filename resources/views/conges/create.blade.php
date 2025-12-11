@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('conges.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <h1 class="h3 mb-4">
        <i class="fas fa-plus-circle text-primary"></i> Nouvelle Demande de Congé
    </h1>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Formulaire de Demande</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('conges.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Type de Congé <span class="text-danger">*</span></label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="conge_annuel">Congé annuel</option>
                                <option value="conge_maladie">Congé maladie</option>
                                <option value="conge_maternite">Congé maternité</option>
                                <option value="conge_paternite">Congé paternité</option>
                                <option value="permission">Permission</option>
                                <option value="conge_sans_solde">Congé sans solde</option>
                                <option value="autre">Autre</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date de Début <span class="text-danger">*</span></label>
                                <input type="date" name="date_debut" class="form-control @error('date_debut') is-invalid @enderror" 
                                       value="{{ old('date_debut') }}" required>
                                @error('date_debut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date de Fin <span class="text-danger">*</span></label>
                                <input type="date" name="date_fin" class="form-control @error('date_fin') is-invalid @enderror" 
                                       value="{{ old('date_fin') }}" required>
                                @error('date_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Motif <span class="text-danger">*</span></label>
                            <textarea name="motif" rows="4" class="form-control @error('motif') is-invalid @enderror" 
                                      required>{{ old('motif') }}</textarea>
                            @error('motif')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fichier Justificatif (Certificat médical, etc.)</label>
                            <input type="file" name="fichier_justificatif" 
                                   class="form-control @error('fichier_justificatif') is-invalid @enderror"
                                   accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">PDF, JPG, PNG - Max 5MB</small>
                            @error('fichier_justificatif')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Soumettre la Demande
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            @if($solde)
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Votre Solde</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Congés Restants</small>
                        <h3 class="text-success mb-0">{{ $solde->conges_annuels_restants }} jours</h3>
                    </div>
                    <hr>
                    <div class="mb-2">
                        <small class="text-muted">Congés Pris</small>
                        <p class="mb-0">{{ $solde->conges_annuels_pris }} / {{ $solde->conges_annuels_totaux }} jours</p>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted">Congés Maladie</small>
                        <p class="mb-0">{{ $solde->conges_maladie_pris }} jours</p>
                    </div>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-lightbulb"></i> Informations</h6>
                </div>
                <div class="card-body">
                    <ul class="small mb-0">
                        <li>Les demandes de congés doivent être soumises au moins 2 semaines à l'avance.</li>
                        <li>Un certificat médical est requis pour les congés maladie.</li>
                        <li>Les demandes sont validées par le RH et la Direction.</li>
                        <li>Vous serez notifié de la décision par email.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
