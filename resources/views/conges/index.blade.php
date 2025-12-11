@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-calendar-alt text-primary"></i> Gestion des Congés
        </h1>
        @if(!$stats)
            <a href="{{ route('conges.create') }}" class="btn btn-primary">
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($stats)
        <!-- Statistiques pour RH/Direction -->
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
                        <h6 class="text-white-50 mb-1">Approuvés</h6>
                        <h2 class="mb-0">{{ $stats['approuves'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h6 class="text-white-50 mb-1">Refusés</h6>
                        <h2 class="mb-0">{{ $stats['refuses'] }}</h2>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Solde de congés pour l'employé -->
        @if($solde)
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Mon Solde de Congés ({{ $solde->annee }})</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6 class="text-muted">Total Annuel</h6>
                            <h3 class="text-primary">{{ $solde->conges_annuels_totaux }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6 class="text-muted">Pris</h6>
                            <h3 class="text-danger">{{ $solde->conges_annuels_pris }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6 class="text-muted">Restants</h6>
                            <h3 class="text-success">{{ $solde->conges_annuels_restants }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6 class="text-muted">Maladie</h6>
                            <h3 class="text-warning">{{ $solde->conges_maladie_pris }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endif

    <!-- Liste des congés -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list"></i> 
                @if($stats) Toutes les Demandes @else Mes Demandes @endif
            </h5>
        </div>
        <div class="card-body">
            @if($conges->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                @if($stats)
                                <th>Employé</th>
                                <th>Poste</th>
                                @endif
                                <th>Type</th>
                                <th>Période</th>
                                <th>Jours</th>
                                <th>Statut</th>
                                <th>Date demande</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($conges as $conge)
                            <tr>
                                @if($stats)
                                <td>{{ $conge->organizationMember->display_name }}</td>
                                <td>{{ $conge->organizationMember->position->title }}</td>
                                @endif
                                <td>
                                    <span class="badge bg-info">{{ $conge->type_libelle }}</span>
                                </td>
                                <td>
                                    {{ $conge->date_debut->format('d/m/Y') }} - 
                                    {{ $conge->date_fin->format('d/m/Y') }}
                                </td>
                                <td>{{ $conge->nb_jours }}</td>
                                <td>
                                    <span class="badge bg-{{ $conge->statut_color }}">
                                        {{ $conge->statut_libelle }}
                                    </span>
                                </td>
                                <td>{{ $conge->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('conges.show', $conge) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($conge->statut === 'en_attente' && !$stats)
                                    <form action="{{ route('conges.destroy', $conge) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Annuler cette demande ?')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $conges->links() }}
                </div>
            @else
                <p class="text-muted text-center py-4">
                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                    Aucune demande de congé pour le moment.
                </p>
            @endif
        </div>
    </div>
</div>
@endsection
