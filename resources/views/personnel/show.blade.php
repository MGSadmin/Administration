@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('personnel.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
        <div>
            <a href="{{ route('personnel.change-status-form', $membre) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Changer le Statut
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Informations générales -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Profil</h5>
                </div>
                <div class="card-body text-center">
                    @if($membre->photo)
                        <img src="{{ Storage::url($membre->photo) }}" class="rounded-circle mb-3" 
                             style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 120px; height: 120px; font-size: 48px;">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif
                    <h4>{{ $membre->display_name }}</h4>
                    <p class="text-muted">{{ $membre->position->title }}</p>
                    <p class="badge bg-{{ $membre->status === 'ACTIVE' ? 'success' : ($membre->status === 'VACANT' ? 'warning' : 'danger') }}">
                        {{ $membre->status }}
                    </p>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informations</h6>
                </div>
                <div class="card-body">
                    <p><strong>Email:</strong><br>{{ $membre->email ?? '-' }}</p>
                    <p><strong>Téléphone:</strong><br>{{ $membre->phone ?? '-' }}</p>
                    <p><strong>Département:</strong><br>{{ $membre->position->department->name }}</p>
                    <p><strong>Date d'entrée:</strong><br>{{ $membre->start_date ? $membre->start_date->format('d/m/Y') : '-' }}</p>
                    @if($membre->end_date)
                    <p><strong>Date de sortie:</strong><br>{{ $membre->end_date->format('d/m/Y') }}</p>
                    @endif
                </div>
            </div>

            @if($membre->soldeConges)
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-calendar-alt"></i> Solde Congés {{ $membre->soldeConges->annee }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small>Restants</small>
                        <h3 class="text-success">{{ $membre->soldeConges->conges_annuels_restants }}</h3>
                    </div>
                    <hr>
                    <p class="mb-1"><small>Pris: {{ $membre->soldeConges->conges_annuels_pris }} / {{ $membre->soldeConges->conges_annuels_totaux }}</small></p>
                    <p class="mb-1"><small>Maladie: {{ $membre->soldeConges->conges_maladie_pris }}</small></p>
                    <p class="mb-0"><small>Permissions: {{ $membre->soldeConges->permissions_prises }}</small></p>
                </div>
            </div>
            @endif
        </div>

        <!-- Onglets -->
        <div class="col-md-8">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#conges">
                        <i class="fas fa-calendar-alt"></i> Congés
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#absences">
                        <i class="fas fa-user-clock"></i> Absences
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#documents">
                        <i class="fas fa-file-alt"></i> Documents
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#historique">
                        <i class="fas fa-history"></i> Historique
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Congés -->
                <div id="conges" class="tab-pane fade show active">
                    <div class="card">
                        <div class="card-body">
                            @if($membre->conges->count() > 0)
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Période</th>
                                            <th>Jours</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($membre->conges as $conge)
                                        <tr>
                                            <td><span class="badge bg-info">{{ $conge->type_libelle }}</span></td>
                                            <td>{{ $conge->date_debut->format('d/m/Y') }} - {{ $conge->date_fin->format('d/m/Y') }}</td>
                                            <td>{{ $conge->nb_jours }}</td>
                                            <td><span class="badge bg-{{ $conge->statut_color }}">{{ $conge->statut_libelle }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted text-center py-3">Aucun congé enregistré</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Absences -->
                <div id="absences" class="tab-pane fade">
                    <div class="card">
                        <div class="card-body">
                            @if($membre->demandesAbsence->count() > 0)
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Date</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($membre->demandesAbsence as $absence)
                                        <tr>
                                            <td><span class="badge bg-info">{{ $absence->type_libelle }}</span></td>
                                            <td>{{ $absence->date->format('d/m/Y') }}</td>
                                            <td><span class="badge bg-{{ $absence->statut_color }}">{{ $absence->statut_libelle }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted text-center py-3">Aucune absence enregistrée</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Documents -->
                <div id="documents" class="tab-pane fade">
                    <div class="card">
                        <div class="card-body">
                            @if($membre->documents->count() > 0)
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Titre</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($membre->documents as $doc)
                                        <tr>
                                            <td><span class="badge bg-secondary">{{ $doc->type_libelle }}</span></td>
                                            <td>{{ $doc->titre }}</td>
                                            <td>{{ $doc->date_emission->format('d/m/Y') }}</td>
                                            <td>
                                                <a href="{{ route('documents.show', $doc) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted text-center py-3">Aucun document</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Historique -->
                <div id="historique" class="tab-pane fade">
                    <div class="card">
                        <div class="card-body">
                            @if($membre->historiqueStatuts->count() > 0)
                                <div class="timeline">
                                    @foreach($membre->historiqueStatuts as $hist)
                                    <div class="mb-3 border-start border-3 border-primary ps-3">
                                        <small class="text-muted">{{ $hist->date_effectif->format('d/m/Y') }}</small>
                                        <h6>{{ $hist->ancien_statut }} → {{ $hist->nouveau_statut }}</h6>
                                        <p class="mb-1"><strong>Motif:</strong> {{ $hist->motif_libelle }}</p>
                                        @if($hist->commentaire)
                                        <p class="mb-1"><em>{{ $hist->commentaire }}</em></p>
                                        @endif
                                        <small>Par {{ $hist->user->name }}</small>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted text-center py-3">Aucun historique</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
