@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-user-clock text-primary"></i> Gestion des Absences
        </h1>
        @if(!$stats)
            <a href="{{ route('absences.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle Demande
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($stats)
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h6 class="text-white-50 mb-1">En Attente</h6>
                        <h2 class="mb-0">{{ $stats['en_attente'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h6 class="text-white-50 mb-1">Approuvées</h6>
                        <h2 class="mb-0">{{ $stats['approuvees'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h6 class="text-white-50 mb-1">Refusées</h6>
                        <h2 class="mb-0">{{ $stats['refusees'] }}</h2>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list"></i> 
                @if($stats) Toutes les Demandes @else Mes Demandes @endif
            </h5>
        </div>
        <div class="card-body">
            @if($demandes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                @if($stats)
                                <th>Employé</th>
                                <th>Poste</th>
                                @endif
                                <th>Type</th>
                                <th>Date</th>
                                <th>Période</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($demandes as $demande)
                            <tr>
                                @if($stats)
                                <td>{{ $demande->organizationMember->display_name }}</td>
                                <td>{{ $demande->organizationMember->position->title }}</td>
                                @endif
                                <td>
                                    <span class="badge bg-info">{{ $demande->type_libelle }}</span>
                                </td>
                                <td>{{ $demande->date->format('d/m/Y') }}</td>
                                <td>
                                    @if($demande->heure_debut && $demande->heure_fin)
                                        {{ $demande->heure_debut }} - {{ $demande->heure_fin }}
                                    @else
                                        Journée complète
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $demande->statut_color }}">
                                        {{ $demande->statut_libelle }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('absences.show', $demande) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $demandes->links() }}
                </div>
            @else
                <p class="text-muted text-center py-4">
                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                    Aucune demande d'absence pour le moment.
                </p>
            @endif
        </div>
    </div>
</div>
@endsection
