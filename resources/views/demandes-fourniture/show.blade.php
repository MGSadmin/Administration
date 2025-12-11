@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="h3"><i class="fas fa-shopping-cart me-2"></i>Détails de la Demande</h2>
        </div>
        <div class="col-md-6 text-end">
            @if(in_array($demandeFourniture->statut, ['en_attente', 'rejetee']) && ($demandeFourniture->demandeur_id == Auth::id() || Auth::user()->hasRole('admin')))
                <a href="{{ route('demandes-fourniture.edit', $demandeFourniture) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Modifier
                </a>
            @endif
            <a href="{{ route('demandes-fourniture.index') }}" class="btn btn-secondary">
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations de la Demande</h5>
                        <span class="badge bg-light text-dark">{{ $demandeFourniture->numero_demande }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Objet :</strong><br>
                        <span class="h5">{{ $demandeFourniture->objet }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Description :</strong><br>
                        {{ $demandeFourniture->description }}
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Type de Fourniture :</strong><br>
                            <span class="badge bg-info">{{ \App\Models\DemandeFourniture::getTypeFournitureLabels()[$demandeFourniture->type_fourniture] ?? $demandeFourniture->type_fourniture }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Quantité :</strong><br>
                            <span class="badge bg-secondary">{{ $demandeFourniture->quantite }}</span>
                        </div>
                    </div>

                    @if($demandeFourniture->justification)
                    <div class="mb-3">
                        <strong>Justification :</strong><br>
                        {{ $demandeFourniture->justification }}
                    </div>
                    @endif

                    @if($demandeFourniture->observation)
                    <div class="mb-3">
                        <strong>Observation :</strong><br>
                        {{ $demandeFourniture->observation }}
                    </div>
                    @endif
                </div>
            </div>

            @if($demandeFourniture->validateur || $demandeFourniture->statut == 'rejetee')
            <div class="card mb-4">
                <div class="card-header {{ $demandeFourniture->statut == 'rejetee' ? 'bg-danger' : 'bg-success' }} text-white">
                    <h5 class="mb-0">
                        <i class="fas {{ $demandeFourniture->statut == 'rejetee' ? 'fa-times-circle' : 'fa-check-circle' }} me-2"></i>
                        {{ $demandeFourniture->statut == 'rejetee' ? 'Rejet' : 'Validation' }}
                    </h5>
                </div>
                <div class="card-body">
                    @if($demandeFourniture->validateur)
                    <div class="mb-2">
                        <strong>{{ $demandeFourniture->statut == 'rejetee' ? 'Rejeté' : 'Validé' }} par :</strong>
                        <i class="fas fa-user ms-2"></i>{{ $demandeFourniture->validateur->name }}
                    </div>
                    @endif
                    @if($demandeFourniture->date_validation)
                    <div class="mb-2">
                        <strong>Date :</strong> {{ $demandeFourniture->date_validation->format('d/m/Y') }}
                    </div>
                    @endif
                    @if($demandeFourniture->commentaire_validateur)
                    <div class="mb-2">
                        <strong>Commentaire :</strong><br>
                        {{ $demandeFourniture->commentaire_validateur }}
                    </div>
                    @endif
                    @if($demandeFourniture->motif_rejet)
                    <div class="alert alert-danger mb-0">
                        <strong>Motif du rejet :</strong><br>
                        {{ $demandeFourniture->motif_rejet }}
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if(in_array($demandeFourniture->statut, ['commandee', 'recue', 'livree']))
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>Informations d'Achat</h5>
                </div>
                <div class="card-body">
                    @if($demandeFourniture->fournisseur)
                    <div class="mb-2">
                        <strong>Fournisseur :</strong> {{ $demandeFourniture->fournisseur }}
                    </div>
                    @endif
                    @if($demandeFourniture->montant_reel)
                    <div class="mb-2">
                        <strong>Montant :</strong> <span class="text-success fw-bold">{{ number_format($demandeFourniture->montant_reel, 2) }} Ar</span>
                    </div>
                    @endif
                    @if($demandeFourniture->bon_commande)
                    <div class="mb-2">
                        <strong>Bon de Commande :</strong> {{ $demandeFourniture->bon_commande }}
                    </div>
                    @endif
                    @if($demandeFourniture->date_commande)
                    <div class="mb-2">
                        <strong>Date Commande :</strong> {{ $demandeFourniture->date_commande->format('d/m/Y') }}
                    </div>
                    @endif
                    @if($demandeFourniture->facture)
                    <div class="mb-2">
                        <strong>Facture :</strong> {{ $demandeFourniture->facture }}
                    </div>
                    @endif
                    @if($demandeFourniture->date_reception)
                    <div class="mb-2">
                        <strong>Date Réception :</strong> {{ $demandeFourniture->date_reception->format('d/m/Y') }}
                    </div>
                    @endif
                    @if($demandeFourniture->date_livraison)
                    <div class="mb-2">
                        <strong>Date Livraison :</strong> {{ $demandeFourniture->date_livraison->format('d/m/Y') }}
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-flag me-2"></i>Statut et Priorité</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Statut :</strong><br>
                        @php
                            $colors = \App\Models\DemandeFourniture::getStatutColors();
                            $color = $colors[$demandeFourniture->statut] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }} fs-6">
                            {{ \App\Models\DemandeFourniture::getStatutLabels()[$demandeFourniture->statut] ?? $demandeFourniture->statut }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <strong>Priorité :</strong><br>
                        @php
                            $prioriteColors = [
                                'faible' => 'secondary',
                                'normale' => 'primary',
                                'urgente' => 'danger',
                            ];
                            $color = $prioriteColors[$demandeFourniture->priorite] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }} fs-6">
                            {{ \App\Models\DemandeFourniture::getPrioriteLabels()[$demandeFourniture->priorite] ?? $demandeFourniture->priorite }}
                        </span>
                    </div>

                    @if($demandeFourniture->budget_estime)
                    <div class="mb-3">
                        <strong>Budget Estimé :</strong><br>
                        {{ number_format($demandeFourniture->budget_estime, 2) }} Ar
                    </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Intervenants</h5>
                </div>
                <div class="card-body">
                    @if($demandeFourniture->demandeur)
                    <div class="mb-3">
                        <strong>Demandeur :</strong><br>
                        <i class="fas fa-user-circle text-primary"></i> {{ $demandeFourniture->demandeur->name }}
                    </div>
                    @endif

                    @if($demandeFourniture->userANotifier)
                    <div class="mb-3">
                        <strong>Personne notifiée :</strong><br>
                        <i class="fas fa-bell text-info"></i> {{ $demandeFourniture->userANotifier->name }}
                    </div>
                    @endif

                    @if($demandeFourniture->acheteur)
                    <div class="mb-3">
                        <strong>Acheteur :</strong><br>
                        <i class="fas fa-user-tie text-success"></i> {{ $demandeFourniture->acheteur->name }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions selon le statut -->
            @if(Auth::user()->can('valider_demande_fourniture') || Auth::user()->can('commander_fourniture') || Auth::user()->can('livrer_fourniture'))
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Actions</h5>
                </div>
                <div class="card-body">
                    @if($demandeFourniture->statut == 'en_attente' && Auth::user()->can('valider_demande_fourniture'))
                        <button type="button" class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#validerModal">
                            <i class="fas fa-check"></i> Valider
                        </button>
                        <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejeterModal">
                            <i class="fas fa-times"></i> Rejeter
                        </button>
                    @elseif($demandeFourniture->statut == 'validee' && Auth::user()->can('commander_fourniture'))
                        <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#commanderModal">
                            <i class="fas fa-shopping-cart"></i> Commander
                        </button>
                    @elseif($demandeFourniture->statut == 'commandee' && Auth::user()->hasAnyRole(['administrateur', 'admin', 'rh']))
                        <button type="button" class="btn btn-info w-100" data-bs-toggle="modal" data-bs-target="#recueModal">
                            <i class="fas fa-box"></i> Marquer comme Reçue
                        </button>
                    @elseif($demandeFourniture->statut == 'recue' && Auth::user()->can('livrer_fourniture'))
                        <form action="{{ route('demandes-fourniture.livrer', $demandeFourniture) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-truck"></i> Livrer
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <small class="text-muted">
                        @if($demandeFourniture->created_at)
                            <strong>Créée le :</strong> {{ $demandeFourniture->created_at->format('d/m/Y à H:i') }}<br>
                        @endif
                        @if($demandeFourniture->updated_at)
                            <strong>Modifiée le :</strong> {{ $demandeFourniture->updated_at->format('d/m/Y à H:i') }}
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@if(isset($demandeFourniture) && $demandeFourniture->id && (Auth::user()->can('valider_demande_fourniture') || Auth::user()->can('commander_fourniture')))
<!-- Modal Valider -->
@if(Auth::user()->can('valider_demande_fourniture'))
<div class="modal fade" id="validerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('demandes-fourniture.valider', $demandeFourniture->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Valider la Demande</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Commentaire</label>
                        <textarea name="commentaire" class="form-control" rows="3" placeholder="Commentaire optionnel..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Valider</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modal Rejeter -->
@if(Auth::user()->can('rejeter_demande_fourniture'))
<div class="modal fade" id="rejeterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('demandes-fourniture.rejeter', $demandeFourniture->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i>Rejeter la Demande</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Motif du Rejet <span class="text-danger">*</span></label>
                        <textarea name="motif_rejet" class="form-control" rows="3" required placeholder="Expliquez pourquoi cette demande est rejetée..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Rejeter</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modal Commander -->
@if(Auth::user()->can('commander_fourniture'))
<div class="modal fade" id="commanderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('demandes-fourniture.commander', $demandeFourniture->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-shopping-cart me-2"></i>Commander</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Fournisseur <span class="text-danger">*</span></label>
                        <input type="text" name="fournisseur" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Montant Réel (Ar) <span class="text-danger">*</span></label>
                        <input type="number" name="montant_reel" class="form-control" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">N° Bon de Commande</label>
                        <input type="text" name="bon_commande" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Commander</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modal Reçue -->
@if(Auth::user()->hasAnyRole(['administrateur', 'admin', 'rh']))
<div class="modal fade" id="recueModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('demandes-fourniture.marquer-recue', $demandeFourniture->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-box me-2"></i>Marquer comme Reçue</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">N° Facture</label>
                        <input type="text" name="facture" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-info">Confirmer Réception</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endif
@endsection
