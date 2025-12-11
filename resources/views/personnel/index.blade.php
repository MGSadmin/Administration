@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-users text-primary"></i> Gestion du Personnel
        </h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h6 class="text-white-50 mb-1">Actifs</h6>
                    <h2 class="mb-0">{{ $stats['actifs'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h6 class="text-white-50 mb-1">Postes Vacants</h6>
                    <h2 class="mb-0">{{ $stats['vacants'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h6 class="text-white-50 mb-1">Licenciés</h6>
                    <h2 class="mb-0">{{ $stats['licencies'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h6 class="text-white-50 mb-1">Total</h6>
                    <h2 class="mb-0">{{ $stats['total'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste du personnel -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> Liste du Personnel</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Poste</th>
                            <th>Département</th>
                            <th>Statut</th>
                            <th>Date d'entrée</th>
                            <th>Congés restants</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($membres as $membre)
                        <tr>
                            <td>
                                <strong>{{ $membre->display_name }}</strong><br>
                                <small class="text-muted">{{ $membre->email }}</small>
                            </td>
                            <td>{{ $membre->position->title }}</td>
                            <td>{{ $membre->position->department->name }}</td>
                            <td>
                                @if($membre->status === 'ACTIVE')
                                    <span class="badge bg-success">Actif</span>
                                @elseif($membre->status === 'VACANT')
                                    <span class="badge bg-warning">Vacant</span>
                                @elseif($membre->status === 'LICENCIE')
                                    <span class="badge bg-danger">Licencié</span>
                                @else
                                    <span class="badge bg-secondary">{{ $membre->status }}</span>
                                @endif
                            </td>
                            <td>{{ $membre->start_date ? $membre->start_date->format('d/m/Y') : '-' }}</td>
                            <td>
                                @if($membre->soldeConges)
                                    <strong>{{ $membre->soldeConges->conges_annuels_restants }}</strong> jours
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('personnel.show', $membre) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('personnel.change-status-form', $membre) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $membres->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
