@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="h3"><i class="fas fa-plus-circle me-2"></i>Ajouter un Patrimoine</h2>
        </div>
    </div>

    <form action="{{ route('patrimoines.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informations Générales</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Désignation <span class="text-danger">*</span></label>
                                <input type="text" name="designation" class="form-control @error('designation') is-invalid @enderror" value="{{ old('designation') }}" required>
                                @error('designation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Catégorie <span class="text-danger">*</span></label>
                                <select name="categorie" class="form-select @error('categorie') is-invalid @enderror" required>
                                    <option value="">Sélectionner...</option>
                                    @foreach(\App\Models\Patrimoine::getCategorieLabels() as $key => $label)
                                        <option value="{{ $key }}" {{ old('categorie') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('categorie')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Marque</label>
                                <input type="text" name="marque" class="form-control @error('marque') is-invalid @enderror" value="{{ old('marque') }}">
                                @error('marque')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Modèle</label>
                                <input type="text" name="modele" class="form-control @error('modele') is-invalid @enderror" value="{{ old('modele') }}">
                                @error('modele')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Numéro de Série</label>
                                <input type="text" name="numero_serie" class="form-control @error('numero_serie') is-invalid @enderror" value="{{ old('numero_serie') }}">
                                @error('numero_serie')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informations d'Achat</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Date d'Achat <span class="text-danger">*</span></label>
                                <input type="date" name="date_achat" class="form-control @error('date_achat') is-invalid @enderror" value="{{ old('date_achat') }}" required>
                                @error('date_achat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Prix d'Achat (Ar)</label>
                                <input type="number" name="prix_achat" class="form-control @error('prix_achat') is-invalid @enderror" value="{{ old('prix_achat') }}" step="0.01">
                                @error('prix_achat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fournisseur</label>
                                <input type="text" name="fournisseur" class="form-control @error('fournisseur') is-invalid @enderror" value="{{ old('fournisseur') }}">
                                @error('fournisseur')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">N° Facture</label>
                                <input type="text" name="facture" class="form-control @error('facture') is-invalid @enderror" value="{{ old('facture') }}">
                                @error('facture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Durée Garantie (mois)</label>
                                <input type="number" name="duree_garantie_mois" class="form-control @error('duree_garantie_mois') is-invalid @enderror" value="{{ old('duree_garantie_mois') }}">
                                @error('duree_garantie_mois')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Validé par</label>
                            <select name="validateur_id" class="form-select @error('validateur_id') is-invalid @enderror">
                                <option value="">Non validé</option>
                                @foreach($validateurs as $user)
                                    <option value="{{ $user->id }}" {{ old('validateur_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('validateur_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">État et Statut</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">État <span class="text-danger">*</span></label>
                            <select name="etat" class="form-select @error('etat') is-invalid @enderror" required>
                                @foreach(\App\Models\Patrimoine::getEtatLabels() as $key => $label)
                                    <option value="{{ $key }}" {{ old('etat', 'neuf') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('etat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Statut <span class="text-danger">*</span></label>
                            <select name="statut" class="form-select @error('statut') is-invalid @enderror" required>
                                @foreach(\App\Models\Patrimoine::getStatutLabels() as $key => $label)
                                    <option value="{{ $key }}" {{ old('statut', 'disponible') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Localisation</label>
                            <input type="text" name="localisation" class="form-control @error('localisation') is-invalid @enderror" value="{{ old('localisation') }}" placeholder="Ex: Bureau 201">
                            @error('localisation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Attribution</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Utilisateur</label>
                            <select name="utilisateur_id" class="form-select @error('utilisateur_id') is-invalid @enderror">
                                <option value="">Non attribué</option>
                                @foreach($utilisateurs as $user)
                                    <option value="{{ $user->id }}" {{ old('utilisateur_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('utilisateur_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Si attribué, le statut sera automatiquement "En utilisation"</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observation</label>
                            <textarea name="observation" class="form-control @error('observation') is-invalid @enderror" rows="3">{{ old('observation') }}</textarea>
                            @error('observation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                    <a href="{{ route('patrimoines.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
