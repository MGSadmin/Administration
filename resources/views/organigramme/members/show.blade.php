@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('organigramme.members.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                    </a>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-user me-2"></i>{{ $member->name }}
                    </h1>
                </div>
                <div>
                    <a href="{{ route('organigramme.members.edit', $member) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Modifier
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Informations personnelles</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Nom complet</label>
                        <p class="mb-0 fw-bold">{{ $member->name }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Email</label>
                        <p class="mb-0">{{ $member->email }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Téléphone</label>
                        <p class="mb-0">{{ $member->phone ?? 'Non renseigné' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Date d'entrée</label>
                        <p class="mb-0">{{ $member->start_date ? $member->start_date->format('d/m/Y') : 'Non renseignée' }}</p>
                    </div>
                    <div class="mb-0">
                        <label class="text-muted small">Statut</label>
                        <p class="mb-0">
                            @if($member->status === 'ACTIF')
                                <span class="badge bg-success">{{ $member->status }}</span>
                            @elseif($member->status === 'VACANT')
                                <span class="badge bg-warning">{{ $member->status }}</span>
                            @else
                                <span class="badge bg-secondary">{{ $member->status }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>Poste actuel</h5>
                </div>
                <div class="card-body">
                    @if($member->position)
                        <div class="mb-3">
                            <label class="text-muted small">Intitulé du poste</label>
                            <p class="mb-0 fw-bold">{{ $member->position->title }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Département</label>
                            <p class="mb-0">{{ $member->position->department->name ?? 'Non assigné' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Niveau hiérarchique</label>
                            <p class="mb-0">Niveau {{ $member->position->level }}</p>
                        </div>
                        @if($member->position->description)
                        <div class="mb-0">
                            <label class="text-muted small">Description</label>
                            <p class="mb-0 small">{{ $member->position->description }}</p>
                        </div>
                        @endif
                    @else
                        <p class="text-muted mb-0">Aucun poste assigné</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Congés et absences</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Congés en cours</label>
                        <p class="mb-0 fw-bold">{{ $member->conges->where('statut', 'approuve')->count() }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Demandes d'absence en attente</label>
                        <p class="mb-0 fw-bold">{{ $member->demandesAbsence->where('statut', 'en_attente')->count() }}</p>
                    </div>
                    <div class="mb-0">
                        <a href="{{ route('conges.index', ['member_id' => $member->id]) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye me-1"></i> Voir détails
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Onglets -->
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-tabs" id="memberTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button">
                        <i class="fas fa-history me-1"></i> Historique des statuts
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="conges-tab" data-bs-toggle="tab" data-bs-target="#conges" type="button">
                        <i class="fas fa-umbrella-beach me-1"></i> Congés
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="absences-tab" data-bs-toggle="tab" data-bs-target="#absences" type="button">
                        <i class="fas fa-clock me-1"></i> Absences
                    </button>
                </li>
            </ul>

            <div class="tab-content border border-top-0 rounded-bottom p-3" id="memberTabsContent">
                <!-- Historique des statuts -->
                <div class="tab-pane fade show active" id="history" role="tabpanel">
                    @if($member->historiqueStatuts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Ancien statut</th>
                                        <th>Nouveau statut</th>
                                        <th>Motif</th>
                                        <th>Modifié par</th>
                                        <th>Commentaire</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($member->historiqueStatuts as $historique)
                                    <tr>
                                        <td>{{ $historique->date_effectif->format('d/m/Y') }}</td>
                                        <td><span class="badge bg-secondary">{{ $historique->ancien_statut }}</span></td>
                                        <td><span class="badge bg-success">{{ $historique->nouveau_statut }}</span></td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $historique->motif)) }}</td>
                                        <td>{{ $historique->user->name ?? 'N/A' }}</td>
                                        <td>{{ $historique->commentaire ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">Aucun historique disponible</p>
                    @endif
                </div>

                <!-- Congés -->
                <div class="tab-pane fade" id="conges" role="tabpanel">
                    @if($member->conges->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Date début</th>
                                        <th>Date fin</th>
                                        <th>Nb jours</th>
                                        <th>Motif</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($member->conges as $conge)
                                    <tr>
                                        <td>{{ ucfirst(str_replace('_', ' ', $conge->type)) }}</td>
                                        <td>{{ $conge->date_debut->format('d/m/Y') }}</td>
                                        <td>{{ $conge->date_fin->format('d/m/Y') }}</td>
                                        <td>{{ $conge->nb_jours }}</td>
                                        <td>{{ Str::limit($conge->motif, 50) }}</td>
                                        <td>
                                            @if($conge->statut === 'approuve')
                                                <span class="badge bg-success">Approuvé</span>
                                            @elseif($conge->statut === 'refuse')
                                                <span class="badge bg-danger">Refusé</span>
                                            @elseif($conge->statut === 'en_attente')
                                                <span class="badge bg-warning">En attente</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $conge->statut }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('conges.show', $conge) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">Aucun congé enregistré</p>
                    @endif
                </div>

                <!-- Absences -->
                <div class="tab-pane fade" id="absences" role="tabpanel">
                    @if($member->demandesAbsence->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Horaire</th>
                                        <th>Motif</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($member->demandesAbsence as $absence)
                                    <tr>
                                        <td>{{ ucfirst(str_replace('_', ' ', $absence->type)) }}</td>
                                        <td>{{ $absence->date->format('d/m/Y') }}</td>
                                        <td>
                                            @if($absence->heure_debut && $absence->heure_fin)
                                                {{ substr($absence->heure_debut, 0, 5) }} - {{ substr($absence->heure_fin, 0, 5) }}
                                            @else
                                                Toute la journée
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($absence->motif, 50) }}</td>
                                        <td>
                                            @if($absence->statut === 'approuve')
                                                <span class="badge bg-success">Approuvé</span>
                                            @elseif($absence->statut === 'refuse')
                                                <span class="badge bg-danger">Refusé</span>
                                            @else
                                                <span class="badge bg-warning">En attente</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('absences.show', $absence) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">Aucune absence enregistrée</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Actions rapides</h5>
                </div>
                <div class="card-body">
                    <div class="btn-group" role="group">
                        @if($member->status === 'ACTIF')
                            <form action="{{ route('organigramme.members.demission', $member) }}" method="POST" class="d-inline" 
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir marquer ce membre comme démissionnaire ?')">
                                @csrf
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-sign-out-alt me-1"></i> Démission
                                </button>
                            </form>
                            
                            <form action="{{ route('organigramme.members.licenciement', $member) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir licencier ce membre ?')">
                                @csrf
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-user-times me-1"></i> Licenciement
                                </button>
                            </form>
                            
                            <form action="{{ route('organigramme.members.retraite', $member) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir marquer ce membre comme retraité ?')">
                                @csrf
                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-user-clock me-1"></i> Retraite
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
