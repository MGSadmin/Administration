@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="h3"><i class="fas fa-shopping-cart me-2"></i>Demandes de Fourniture</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('demandes-fourniture.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle Demande
            </a>
            <a href="{{ route('demandes-fourniture.statistiques') }}" class="btn btn-info">
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('demandes-fourniture.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="statut" class="form-select">
                            <option value="">Tous statuts</option>
                            @foreach(\App\Models\DemandeFourniture::getStatutLabels() as $key => $label)
                                <option value="{{ $key }}" {{ request('statut') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="priorite" class="form-select">
                            <option value="">Toutes priorités</option>
                            @foreach(\App\Models\DemandeFourniture::getPrioriteLabels() as $key => $label)
                                <option value="{{ $key }}" {{ request('priorite') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="type_fourniture" class="form-select">
                            <option value="">Tous types</option>
                            @foreach(\App\Models\DemandeFourniture::getTypeFournitureLabels() as $key => $label)
                                <option value="{{ $key }}" {{ request('type_fourniture') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if(Auth::user()->can('voir_toutes_demandes_fourniture'))
                    <div class="col-md-2">
                        <select name="demandeur_id" class="form-select">
                            <option value="">Tous demandeurs</option>
                            @foreach($utilisateurs as $user)
                                <option value="{{ $user->id }}" {{ request('demandeur_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des demandes -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Numéro</th>
                            <th>Objet</th>
                            <th>Demandeur</th>
                            <th>Type</th>
                            <th>Priorité</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($demandes as $demande)
                            <tr>
                                <td><strong>{{ $demande->numero_demande }}</strong></td>
                                <td>
                                    {{ Str::limit($demande->objet, 40) }}
                                    @if($demande->quantite > 1)
                                        <br><small class="text-muted">Qté: {{ $demande->quantite }}</small>
                                    @endif
                                </td>
                                <td>
                                    <i class="fas fa-user"></i> {{ $demande->demandeur->name }}
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ \App\Models\DemandeFourniture::getTypeFournitureLabels()[$demande->type_fourniture] ?? $demande->type_fourniture }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $prioriteColors = [
                                            'faible' => 'secondary',
                                            'normale' => 'primary',
                                            'urgente' => 'danger',
                                        ];
                                        $color = $prioriteColors[$demande->priorite] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">
                                        {{ \App\Models\DemandeFourniture::getPrioriteLabels()[$demande->priorite] ?? $demande->priorite }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $colors = \App\Models\DemandeFourniture::getStatutColors();
                                        $color = $colors[$demande->statut] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">
                                        {{ \App\Models\DemandeFourniture::getStatutLabels()[$demande->statut] ?? $demande->statut }}
                                    </span>
                                </td>
                                <td>{{ $demande->created_at ? $demande->created_at->format('d/m/Y') : '-' }}</td>
                                <td>
                                    <a href="{{ route('demandes-fourniture.show', $demande) }}" class="btn btn-sm btn-info" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(in_array($demande->statut, ['en_attente', 'rejetee']) && ($demande->demandeur_id == Auth::id() || Auth::user()->hasAnyRole(['administrateur', 'admin', 'direction', 'rh'])))
                                        <a href="{{ route('demandes-fourniture.edit', $demande) }}" class="btn btn-sm btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    @if($demande->statut == 'en_attente' && ($demande->demandeur_id == Auth::id() || Auth::user()->hasAnyRole(['administrateur', 'admin', 'direction', 'rh'])))
                                        <form action="{{ route('demandes-fourniture.destroy', $demande) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette demande ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>Aucune demande trouvée.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $demandes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
