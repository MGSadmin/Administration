@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="h3"><i class="fas fa-archive me-2"></i>Détails du Patrimoine</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('patrimoines.edit', $patrimoine) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="{{ route('patrimoines.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations Générales</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Code Matériel :</strong><br>
                            <span class="h5 text-primary">{{ $patrimoine->code_materiel }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Catégorie :</strong><br>
                            <span class="badge bg-secondary fs-6">{{ \App\Models\Patrimoine::getCategorieLabels()[$patrimoine->categorie] ?? $patrimoine->categorie }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Désignation :</strong><br>
                        {{ $patrimoine->designation }}
                    </div>

                    @if($patrimoine->description)
                    <div class="mb-3">
                        <strong>Description :</strong><br>
                        {{ $patrimoine->description }}
                    </div>
                    @endif

                    <div class="row mb-3">
                        @if($patrimoine->marque)
                        <div class="col-md-4">
                            <strong>Marque :</strong><br>
                            {{ $patrimoine->marque }}
                        </div>
                        @endif
                        @if($patrimoine->modele)
                        <div class="col-md-4">
                            <strong>Modèle :</strong><br>
                            {{ $patrimoine->modele }}
                        </div>
                        @endif
                        @if($patrimoine->numero_serie)
                        <div class="col-md-4">
                            <strong>N° Série :</strong><br>
                            {{ $patrimoine->numero_serie }}
                        </div>
                        @endif
                    </div>

                    @if($patrimoine->localisation)
                    <div class="mb-3">
                        <strong>Localisation :</strong><br>
                        <i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $patrimoine->localisation }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>Informations d'Achat</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Date d'Achat :</strong><br>
                            <i class="fas fa-calendar me-1"></i>{{ $patrimoine->date_achat->format('d/m/Y') }}
                        </div>
                        @if($patrimoine->prix_achat)
                        <div class="col-md-4">
                            <strong>Prix d'Achat :</strong><br>
                            <span class="text-success fw-bold">{{ number_format($patrimoine->prix_achat, 2) }} Ar</span>
                        </div>
                        @endif
                        @if($patrimoine->age_en_annees !== null)
                        <div class="col-md-4">
                            <strong>Âge :</strong><br>
                            {{ $patrimoine->age_en_annees }} {{ $patrimoine->age_en_annees > 1 ? 'ans' : 'an' }}
                        </div>
                        @endif
                    </div>

                    @if($patrimoine->fournisseur || $patrimoine->facture)
                    <div class="row mb-3">
                        @if($patrimoine->fournisseur)
                        <div class="col-md-6">
                            <strong>Fournisseur :</strong><br>
                            {{ $patrimoine->fournisseur }}
                        </div>
                        @endif
                        @if($patrimoine->facture)
                        <div class="col-md-6">
                            <strong>N° Facture :</strong><br>
                            {{ $patrimoine->facture }}
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($patrimoine->validateur)
                    <div class="mb-3">
                        <strong>Validé par :</strong><br>
                        <i class="fas fa-user-check text-success me-1"></i>{{ $patrimoine->validateur->name }}
                        @if($patrimoine->date_validation)
                            <span class="text-muted">le {{ $patrimoine->date_validation->format('d/m/Y') }}</span>
                        @endif
                    </div>
                    @endif

                    @if($patrimoine->duree_garantie_mois)
                    <div class="mb-3">
                        <strong>Garantie :</strong><br>
                        {{ $patrimoine->duree_garantie_mois }} mois
                        @if($patrimoine->date_fin_garantie)
                            - Jusqu'au {{ $patrimoine->date_fin_garantie->format('d/m/Y') }}
                            @if($patrimoine->est_sous_garantie)
                                <span class="badge bg-success ms-2">Sous garantie</span>
                            @else
                                <span class="badge bg-secondary ms-2">Garantie expirée</span>
                            @endif
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            @if($patrimoine->observation)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-comment me-2"></i>Observations</h5>
                </div>
                <div class="card-body">
                    {{ $patrimoine->observation }}
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-flag me-2"></i>État et Statut</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>État :</strong><br>
                        @php
                            $etatColors = [
                                'neuf' => 'success',
                                'bon' => 'info',
                                'moyen' => 'warning',
                                'mauvais' => 'danger',
                                'en_reparation' => 'warning',
                                'hors_service' => 'dark',
                            ];
                            $color = $etatColors[$patrimoine->etat] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }} fs-6">{{ \App\Models\Patrimoine::getEtatLabels()[$patrimoine->etat] ?? $patrimoine->etat }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Statut :</strong><br>
                        @php
                            $statutColors = [
                                'disponible' => 'success',
                                'en_utilisation' => 'primary',
                                'en_maintenance' => 'warning',
                                'reforme' => 'dark',
                            ];
                            $color = $statutColors[$patrimoine->statut] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }} fs-6">{{ \App\Models\Patrimoine::getStatutLabels()[$patrimoine->statut] ?? $patrimoine->statut }}</span>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Attribution</h5>
                </div>
                <div class="card-body">
                    @if($patrimoine->utilisateur)
                        <div class="mb-3">
                            <strong>Utilisateur :</strong><br>
                            <i class="fas fa-user-circle text-primary me-1"></i>{{ $patrimoine->utilisateur->name }}
                        </div>
                        <div class="mb-3">
                            <strong>Depuis le :</strong><br>
                            {{ $patrimoine->date_attribution->format('d/m/Y') }}
                        </div>
                        <form action="{{ route('patrimoines.liberer', $patrimoine) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment libérer ce matériel ?')">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-unlock"></i> Libérer le Matériel
                            </button>
                        </form>
                    @else
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>Matériel non attribué
                        </div>
                        <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#attribuerModal">
                            <i class="fas fa-user-plus"></i> Attribuer
                        </button>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <small class="text-muted">
                        <strong>Créé le :</strong> {{ $patrimoine->created_at->format('d/m/Y à H:i') }}<br>
                        <strong>Modifié le :</strong> {{ $patrimoine->updated_at->format('d/m/Y à H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Attribution -->
<div class="modal fade" id="attribuerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('patrimoines.attribuer', $patrimoine) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Attribuer le Matériel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Sélectionner un utilisateur <span class="text-danger">*</span></label>
                        <select name="utilisateur_id" class="form-select" required>
                            <option value="">Choisir...</option>
                            @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Attribuer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
