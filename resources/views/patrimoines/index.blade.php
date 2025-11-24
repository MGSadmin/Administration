@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="h3"><i class="fas fa-archive me-2"></i>Gestion des Patrimoines</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('patrimoines.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter un Patrimoine
            </a>
            <a href="{{ route('patrimoines.statistiques') }}" class="btn btn-info">
                <i class="fas fa-chart-bar"></i> Statistiques
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('patrimoines.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="categorie" class="form-select">
                            <option value="">Toutes catégories</option>
                            @foreach(\App\Models\Patrimoine::getCategorieLabels() as $key => $label)
                                <option value="{{ $key }}" {{ request('categorie') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="statut" class="form-select">
                            <option value="">Tous statuts</option>
                            @foreach(\App\Models\Patrimoine::getStatutLabels() as $key => $label)
                                <option value="{{ $key }}" {{ request('statut') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="etat" class="form-select">
                            <option value="">Tous états</option>
                            @foreach(\App\Models\Patrimoine::getEtatLabels() as $key => $label)
                                <option value="{{ $key }}" {{ request('etat') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="utilisateur_id" class="form-select">
                            <option value="">Tous utilisateurs</option>
                            @foreach($utilisateurs as $user)
                                <option value="{{ $user->id }}" {{ request('utilisateur_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des patrimoines -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Désignation</th>
                            <th>Catégorie</th>
                            <th>Utilisateur</th>
                            <th>Date Achat</th>
                            <th>État</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patrimoines as $patrimoine)
                            <tr>
                                <td><strong>{{ $patrimoine->code_materiel }}</strong></td>
                                <td>
                                    {{ $patrimoine->designation }}
                                    @if($patrimoine->marque)
                                        <br><small class="text-muted">{{ $patrimoine->marque }} {{ $patrimoine->modele }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ \App\Models\Patrimoine::getCategorieLabels()[$patrimoine->categorie] ?? $patrimoine->categorie }}
                                    </span>
                                </td>
                                <td>
                                    @if($patrimoine->utilisateur)
                                        <i class="fas fa-user text-primary"></i> {{ $patrimoine->utilisateur->name }}
                                        <br><small class="text-muted">Depuis {{ $patrimoine->date_attribution->format('d/m/Y') }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $patrimoine->date_achat->format('d/m/Y') }}</td>
                                <td>
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
                                    <span class="badge bg-{{ $color }}">
                                        {{ \App\Models\Patrimoine::getEtatLabels()[$patrimoine->etat] ?? $patrimoine->etat }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statutColors = [
                                            'disponible' => 'success',
                                            'en_utilisation' => 'primary',
                                            'en_maintenance' => 'warning',
                                            'reforme' => 'dark',
                                        ];
                                        $color = $statutColors[$patrimoine->statut] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">
                                        {{ \App\Models\Patrimoine::getStatutLabels()[$patrimoine->statut] ?? $patrimoine->statut }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('patrimoines.show', $patrimoine) }}" class="btn btn-sm btn-info" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('patrimoines.edit', $patrimoine) }}" class="btn btn-sm btn-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('patrimoines.destroy', $patrimoine) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce patrimoine ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>Aucun patrimoine trouvé.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $patrimoines->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
