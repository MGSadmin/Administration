@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('conges.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-file-alt"></i> Détails de la Demande de Congé
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h4>{{ $conge->type_libelle }}</h4>
                    
                    <div class="mb-3">
                        <strong>Statut:</strong>
                        <span class="badge bg-{{ $conge->statut_color }} fs-6">
                            {{ $conge->statut_libelle }}
                        </span>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Employé:</strong>
                            <p>{{ $conge->organizationMember->display_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Poste:</strong>
                            <p>{{ $conge->organizationMember->position->title }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Département:</strong>
                            <p>{{ $conge->organizationMember->position->department->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Date de demande:</strong>
                            <p>{{ $conge->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Date de début:</strong>
                            <p class="text-primary">{{ $conge->date_debut->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Date de fin:</strong>
                            <p class="text-primary">{{ $conge->date_fin->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Nombre de jours:</strong>
                            <p class="text-primary"><strong>{{ $conge->nb_jours }}</strong> jours</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Motif:</strong>
                        <p class="border p-3 bg-light">{{ $conge->motif }}</p>
                    </div>

                    @if($conge->fichier_justificatif)
                    <div class="mb-3">
                        <strong>Fichier justificatif:</strong><br>
                        <a href="{{ Storage::url($conge->fichier_justificatif) }}" 
                           target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-download"></i> Télécharger le justificatif
                        </a>
                    </div>
                    @endif

                    @if($conge->validateur)
                    <hr>
                    <div class="mb-3">
                        <strong>Validé par:</strong>
                        <p>{{ $conge->validateur->name }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>Date de validation:</strong>
                        <p>{{ $conge->date_validation ? $conge->date_validation->format('d/m/Y à H:i') : '-' }}</p>
                    </div>
                    @endif

                    @if($conge->commentaire_rh)
                    <div class="mb-3">
                        <strong>Commentaire RH:</strong>
                        <p class="border p-3 bg-light text-danger">{{ $conge->commentaire_rh }}</p>
                    </div>
                    @endif
                </div>

                <div class="col-md-4">
                    @can('approve', $conge)
                    @if($conge->statut === 'en_attente')
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">Actions RH/Direction</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('conges.approve', $conge) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-success w-100"
                                        onclick="return confirm('Approuver cette demande ?')">
                                    <i class="fas fa-check"></i> Approuver
                                </button>
                            </form>

                            <button type="button" class="btn btn-danger w-100" 
                                    data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="fas fa-times"></i> Refuser
                            </button>
                        </div>
                    </div>
                    @endif
                    @endcan

                    @if($conge->statut === 'en_attente' && $conge->user_id === auth()->id())
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0">Mes Actions</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('conges.destroy', $conge) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-warning w-100"
                                        onclick="return confirm('Annuler cette demande ?')">
                                    <i class="fas fa-ban"></i> Annuler la demande
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Refus -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('conges.reject', $conge) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Refuser la Demande</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Motif du refus <span class="text-danger">*</span></label>
                        <textarea name="commentaire_rh" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Refuser
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
