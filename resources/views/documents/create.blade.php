@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <h1 class="h3 mb-4">
        <i class="fas fa-plus-circle text-primary"></i> Ajouter un Document
    </h1>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Formulaire d'Ajout de Document</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Employé <span class="text-danger">*</span></label>
                    <select name="organization_member_id" class="form-select @error('organization_member_id') is-invalid @enderror" required>
                        <option value="">-- Sélectionner un employé --</option>
                        @foreach($membres as $m)
                        <option value="{{ $m->id }}" {{ ($member && $member->id == $m->id) ? 'selected' : '' }}>
                            {{ $m->display_name }} - {{ $m->position->title }}
                        </option>
                        @endforeach
                    </select>
                    @error('organization_member_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Type de Document <span class="text-danger">*</span></label>
                    <select name="type_document" class="form-select @error('type_document') is-invalid @enderror" required>
                        <option value="">-- Sélectionner --</option>
                        <optgroup label="Documents en cours d'emploi">
                            <option value="contrat_travail">Contrat de travail</option>
                            <option value="avenant_contrat">Avenant au contrat</option>
                            <option value="fiche_poste">Fiche de poste</option>
                            <option value="attestation_travail">Attestation de travail</option>
                            <option value="certificat_emploi">Certificat d'emploi</option>
                            <option value="bulletin_paie">Bulletin de paie</option>
                            <option value="attestation_salaire">Attestation de salaire</option>
                            <option value="releve_annuel_salaires">Relevé annuel des salaires</option>
                            <option value="etat_conges">État des congés</option>
                            <option value="etat_heures_supplementaires">État des heures supplémentaires</option>
                        </optgroup>
                        <optgroup label="Documents de fin de contrat">
                            <option value="certificat_travail_fin">Certificat de travail (fin)</option>
                            <option value="attestation_fin_contrat">Attestation de fin de contrat</option>
                            <option value="solde_tout_compte">Solde de tout compte</option>
                            <option value="releve_droits_conges">Relevé des droits de congés</option>
                            <option value="attestation_cnaps">Attestation CNAPS</option>
                            <option value="attestation_ostie">Attestation OSTIE</option>
                            <option value="lettre_licenciement">Lettre de licenciement</option>
                            <option value="lettre_recommandation">Lettre de recommandation</option>
                        </optgroup>
                        <optgroup label="Autres">
                            <option value="autre">Autre</option>
                        </optgroup>
                    </select>
                    @error('type_document')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Titre du Document <span class="text-danger">*</span></label>
                    <input type="text" name="titre" class="form-control @error('titre') is-invalid @enderror" 
                           value="{{ old('titre') }}" required>
                    @error('titre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Fichier <span class="text-danger">*</span></label>
                    <input type="file" name="fichier" class="form-control @error('fichier') is-invalid @enderror" 
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                    <small class="text-muted">PDF, DOC, DOCX, JPG, PNG - Max 10MB</small>
                    @error('fichier')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date d'Émission <span class="text-danger">*</span></label>
                        <input type="date" name="date_emission" class="form-control @error('date_emission') is-invalid @enderror" 
                               value="{{ old('date_emission', now()->format('Y-m-d')) }}" required>
                        @error('date_emission')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date de Validité</label>
                        <input type="date" name="date_validite" class="form-control @error('date_validite') is-invalid @enderror" 
                               value="{{ old('date_validite') }}">
                        @error('date_validite')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="accessible_employe" value="1" class="form-check-input" id="accessible">
                        <label class="form-check-label" for="accessible">
                            Document accessible à l'employé
                        </label>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Enregistrer le Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
