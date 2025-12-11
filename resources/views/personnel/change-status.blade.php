@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('personnel.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <h1 class="h3 mb-4">
        <i class="fas fa-user-edit text-warning"></i> Changement de Statut
    </h1>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Modifier le Statut de l'Employé</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Attention :</strong> Le changement de statut est une action importante qui sera enregistrée dans l'historique.
                    </div>

                    <form action="{{ route('personnel.change-status', $membre) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Statut Actuel</label>
                            <input type="text" class="form-control" value="{{ $membre->status }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nouveau Statut <span class="text-danger">*</span></label>
                            <select name="nouveau_statut" class="form-select @error('nouveau_statut') is-invalid @enderror" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="ACTIVE">ACTIF</option>
                                <option value="VACANT">VACANT</option>
                                <option value="INTERIM">INTÉRIM</option>
                                <option value="LICENCIE">LICENCIÉ</option>
                                <option value="DEMISSION">DÉMISSION</option>
                                <option value="RETRAITE">RETRAITE</option>
                            </select>
                            @error('nouveau_statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Motif <span class="text-danger">*</span></label>
                            <select name="motif" class="form-select @error('motif') is-invalid @enderror" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="embauche">Embauche</option>
                                <option value="promotion">Promotion</option>
                                <option value="mutation">Mutation</option>
                                <option value="demission">Démission</option>
                                <option value="licenciement">Licenciement</option>
                                <option value="retraite">Retraite</option>
                                <option value="deces">Décès</option>
                                <option value="fin_contrat">Fin de contrat</option>
                                <option value="autre">Autre</option>
                            </select>
                            @error('motif')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date Effective <span class="text-danger">*</span></label>
                            <input type="date" name="date_effectif" 
                                   class="form-control @error('date_effectif') is-invalid @enderror" 
                                   value="{{ old('date_effectif', now()->format('Y-m-d')) }}" required>
                            @error('date_effectif')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Commentaire</label>
                            <textarea name="commentaire" rows="4" 
                                      class="form-control @error('commentaire') is-invalid @enderror">{{ old('commentaire') }}</textarea>
                            @error('commentaire')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note :</strong> Si vous sélectionnez "LICENCIÉ", le poste deviendra automatiquement VACANT 
                            et les documents de fin de contrat seront créés.
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fas fa-save"></i> Enregistrer le Changement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-user"></i> Informations Employé</h6>
                </div>
                <div class="card-body">
                    <p><strong>Nom:</strong> {{ $membre->display_name }}</p>
                    <p><strong>Poste:</strong> {{ $membre->position->title }}</p>
                    <p><strong>Département:</strong> {{ $membre->position->department->name }}</p>
                    <p><strong>Email:</strong> {{ $membre->email }}</p>
                    <p><strong>Date d'entrée:</strong> {{ $membre->start_date ? $membre->start_date->format('d/m/Y') : '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
