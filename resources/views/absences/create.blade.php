@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('absences.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <h1 class="h3 mb-4">
        <i class="fas fa-plus-circle text-primary"></i> Nouvelle Demande d'Absence
    </h1>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Formulaire de Demande</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('absences.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Type d'Absence <span class="text-danger">*</span></label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="absence_justifiee">Absence justifiée</option>
                                <option value="absence_non_justifiee">Absence non justifiée</option>
                                <option value="retard">Retard</option>
                                <option value="sortie_anticipee">Sortie anticipée</option>
                                <option value="teletravail">Télétravail</option>
                                <option value="mission_externe">Mission externe</option>
                                <option value="formation">Formation</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" 
                                   value="{{ old('date') }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Heure de Début</label>
                                <input type="time" name="heure_debut" class="form-control @error('heure_debut') is-invalid @enderror" 
                                       value="{{ old('heure_debut') }}">
                                @error('heure_debut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Heure de Fin</label>
                                <input type="time" name="heure_fin" class="form-control @error('heure_fin') is-invalid @enderror" 
                                       value="{{ old('heure_fin') }}">
                                @error('heure_fin')
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
                            <label class="form-label">Fichier Justificatif</label>
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
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informations</h6>
                </div>
                <div class="card-body">
                    <ul class="small mb-0">
                        <li>Les absences justifiées nécessitent un document.</li>
                        <li>Le télétravail doit être approuvé à l'avance.</li>
                        <li>Les retards répétés peuvent faire l'objet de sanctions.</li>
                        <li>Informez votre responsable dès que possible.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
