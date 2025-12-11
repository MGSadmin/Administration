{{-- Exemple de vue INDEX avec permissions --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion des Patrimoines</h1>
        
        {{-- Bouton Créer uniquement si permission --}}
        @can('Créer Patrimoine')
            <a href="{{ route('patrimoines.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Créer Patrimoine
            </a>
        @endcan
    </div>

    {{-- Filtres et recherche --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('patrimoines.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Rechercher..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tableau des patrimoines --}}
    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Désignation</th>
                        <th>Catégorie</th>
                        <th>Statut</th>
                        <th>Utilisateur</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patrimoines as $patrimoine)
                        <tr>
                            <td>{{ $patrimoine->code_materiel }}</td>
                            <td>{{ $patrimoine->designation }}</td>
                            <td>{{ ucfirst($patrimoine->categorie) }}</td>
                            <td>
                                <span class="badge bg-{{ $patrimoine->statut === 'disponible' ? 'success' : 'warning' }}">
                                    {{ ucfirst($patrimoine->statut) }}
                                </span>
                            </td>
                            <td>{{ $patrimoine->utilisateur?->name ?? 'Non attribué' }}</td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    {{-- Voir - Toujours visible pour ceux qui ont accès à la liste --}}
                                    <a href="{{ route('patrimoines.show', $patrimoine) }}" 
                                       class="btn btn-sm btn-info" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- Modifier - Uniquement si permission --}}
                                    @can('Modifier Patrimoine')
                                        <a href="{{ route('patrimoines.edit', $patrimoine) }}" 
                                           class="btn btn-sm btn-warning" title="Modifier Patrimoine">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan

                                    {{-- Attribuer - Uniquement si permission et patrimoine disponible --}}
                                    @can('Attribuer Patrimoine')
                                        @if($patrimoine->statut === 'disponible')
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#attribuerModal{{ $patrimoine->id }}"
                                                    title="Attribuer Patrimoine">
                                                <i class="fas fa-user-plus"></i>
                                            </button>
                                        @endif
                                    @endcan

                                    {{-- Libérer - Uniquement si permission et patrimoine attribué --}}
                                    @can('Libérer Patrimoine')
                                        @if($patrimoine->statut === 'attribue')
                                            <form action="{{ route('patrimoines.liberer', $patrimoine) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Confirmer la libération de ce patrimoine ?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-secondary" 
                                                        title="Libérer Patrimoine">
                                                    <i class="fas fa-user-minus"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endcan

                                    {{-- Supprimer - Uniquement si permission --}}
                                    @can('Supprimer Patrimoine')
                                        <form action="{{ route('patrimoines.destroy', $patrimoine) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Confirmer la suppression ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    title="Supprimer Patrimoine">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Aucun patrimoine trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $patrimoines->links() }}
        </div>
    </div>
</div>

{{-- Modals d'attribution - Uniquement si permission --}}
@can('Attribuer Patrimoine')
    @foreach($patrimoines as $patrimoine)
        @if($patrimoine->statut === 'disponible')
            <div class="modal fade" id="attribuerModal{{ $patrimoine->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('patrimoines.attribuer', $patrimoine) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Attribuer Patrimoine</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Utilisateur</label>
                                    <select name="utilisateur_id" class="form-select" required>
                                        <option value="">Sélectionner...</option>
                                        @foreach($utilisateurs as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Date d'attribution</label>
                                    <input type="date" name="date_attribution" class="form-control" 
                                           value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary">Attribuer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endcan
@endsection

{{-- ============================================================ --}}
{{-- Exemple FOURNITURES avec permissions --}}
{{-- ============================================================ --}}

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Demandes de Fourniture</h1>
        
        @can('Créer Demande Fourniture')
            <a href="{{ route('demandes-fourniture.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Créer Demande Fourniture
            </a>
        @endcan
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Article</th>
                        <th>Quantité</th>
                        <th>Demandeur</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($demandes as $demande)
                        <tr>
                            <td>{{ $demande->article }}</td>
                            <td>{{ $demande->quantite }}</td>
                            <td>{{ $demande->demandeur->name }}</td>
                            <td>
                                <span class="badge bg-{{ $demande->statut === 'validee' ? 'success' : ($demande->statut === 'rejetee' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($demande->statut) }}
                                </span>
                            </td>
                            <td>{{ $demande->created_at->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('demandes-fourniture.show', $demande) }}" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- Modifier - Uniquement ses propres demandes en attente --}}
                                    @can('update', $demande)
                                        <a href="{{ route('demandes-fourniture.edit', $demande) }}" 
                                           class="btn btn-sm btn-warning" title="Modifier Demande Fourniture">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan

                                    {{-- Valider - Uniquement si permission --}}
                                    @if($demande->statut === 'en_attente')
                                        @can('Valider Demande Fourniture')
                                            <form action="{{ route('demandes-fourniture.valider', $demande) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" 
                                                        title="Valider Demande Fourniture">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endcan

                                        @can('Rejeter Demande Fourniture')
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#rejetModal{{ $demande->id }}"
                                                    title="Rejeter Demande Fourniture">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endcan
                                    @endif

                                    {{-- Supprimer - Uniquement ses propres demandes en attente --}}
                                    @can('delete', $demande)
                                        <form action="{{ route('demandes-fourniture.destroy', $demande) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Confirmer la suppression ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    title="Supprimer Demande Fourniture">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

{{-- ============================================================ --}}
{{-- Exemple CONGÉS avec permissions --}}
{{-- ============================================================ --}}

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion des Congés</h1>
        
        @can('Créer Congé')
            <a href="{{ route('conges.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Créer Congé
            </a>
        @endcan
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Employé</th>
                        <th>Type</th>
                        <th>Période</th>
                        <th>Jours</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($conges as $conge)
                        <tr>
                            <td>{{ $conge->user->name }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $conge->type)) }}</td>
                            <td>{{ $conge->date_debut->format('d/m/Y') }} - {{ $conge->date_fin->format('d/m/Y') }}</td>
                            <td>{{ $conge->nb_jours }}</td>
                            <td>
                                <span class="badge bg-{{ $conge->statut === 'approuve' ? 'success' : ($conge->statut === 'rejete' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($conge->statut) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('conges.show', $conge) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- Approuver - Uniquement si permission et en attente --}}
                                    @if($conge->statut === 'en_attente')
                                        @can('Approuver Congé')
                                            <form action="{{ route('conges.approve', $conge) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" 
                                                        title="Approuver Congé">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endcan

                                        @can('Rejeter Congé')
                                            <form action="{{ route('conges.reject', $conge) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        title="Rejeter Congé">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    @endif

                                    {{-- Supprimer - Uniquement ses propres congés en attente --}}
                                    @can('delete', $conge)
                                        <form action="{{ route('conges.destroy', $conge) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Supprimer ce congé ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    title="Supprimer Congé">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
