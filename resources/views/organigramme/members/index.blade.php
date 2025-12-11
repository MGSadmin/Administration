@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <i class="fas fa-users me-2"></i>Gestion des Membres
                </h1>
                <div>
                    <a href="{{ route('organigramme.interactive') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-sitemap me-1"></i> Retour à l'organigramme
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('organigramme.members.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="ACTIF" {{ request('status') === 'ACTIF' ? 'selected' : '' }}>Actif</option>
                            <option value="VACANT" {{ request('status') === 'VACANT' ? 'selected' : '' }}>Vacant</option>
                            <option value="DEMISSIONNAIRE" {{ request('status') === 'DEMISSIONNAIRE' ? 'selected' : '' }}>Démissionnaire</option>
                            <option value="LICENCIE" {{ request('status') === 'LICENCIE' ? 'selected' : '' }}>Licencié</option>
                            <option value="RETRAITE" {{ request('status') === 'RETRAITE' ? 'selected' : '' }}>Retraité</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Département</label>
                        <select name="department" class="form-select">
                            <option value="">Tous les départements</option>
                            <!-- Ajoutez les départements dynamiquement -->
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i> Filtrer
                            </button>
                            <a href="{{ route('organigramme.members.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-1"></i> Réinitialiser
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des membres -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Liste des membres ({{ $members->total() }})</h5>
        </div>
        <div class="card-body">
            @if($members->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Poste</th>
                                <th>Département</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Statut</th>
                                <th>Date d'entrée</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($members as $member)
                            <tr>
                                <td>
                                    <strong>{{ $member->name }}</strong>
                                </td>
                                <td>{{ $member->position->title ?? 'N/A' }}</td>
                                <td>{{ $member->position->department->name ?? 'N/A' }}</td>
                                <td>{{ $member->email }}</td>
                                <td>{{ $member->phone ?? '-' }}</td>
                                <td>
                                    @if($member->status === 'ACTIF')
                                        <span class="badge bg-success">{{ $member->status }}</span>
                                    @elseif($member->status === 'VACANT')
                                        <span class="badge bg-warning">{{ $member->status }}</span>
                                    @elseif($member->status === 'DEMISSIONNAIRE')
                                        <span class="badge bg-info">{{ $member->status }}</span>
                                    @elseif($member->status === 'LICENCIE')
                                        <span class="badge bg-danger">{{ $member->status }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $member->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $member->start_date ? $member->start_date->format('d/m/Y') : '-' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('organigramme.members.show', $member) }}" class="btn btn-outline-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('organigramme.members.edit', $member) }}" class="btn btn-outline-secondary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $members->links() }}
                </div>
            @else
                <p class="text-muted text-center mb-0">Aucun membre trouvé</p>
            @endif
        </div>
    </div>

    @if(isset($vacantPositions) && $vacantPositions->count() > 0)
    <!-- Postes vacants -->
    <div class="card mt-4">
        <div class="card-header bg-warning">
            <h5 class="mb-0">Postes vacants ({{ $vacantPositions->count() }})</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Poste</th>
                            <th>Département</th>
                            <th>Niveau</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vacantPositions as $vacant)
                        <tr>
                            <td>{{ $vacant->position->title ?? 'N/A' }}</td>
                            <td>{{ $vacant->position->department->name ?? 'N/A' }}</td>
                            <td>Niveau {{ $vacant->position->level ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('organigramme.positions.assign-form', $vacant->position) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-user-plus me-1"></i> Assigner
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
