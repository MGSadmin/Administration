@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-user-tag"></i> Détails du rôle : {{ $role->name }}</h1>
        <div>
            <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informations</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">Nom :</th>
                            <td>{{ $role->name }}</td>
                        </tr>
                        <tr>
                            <th>Créé le :</th>
                            <td>{{ $role->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Modifié le :</th>
                            <td>{{ $role->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Utilisateurs :</th>
                            <td>
                                <span class="badge bg-info">{{ $role->users()->count() }} utilisateur(s)</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Permissions ({{ $role->permissions->count() }})</h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @if($role->permissions->count() > 0)
                        @php
                            $grouped = $role->permissions->groupBy(function($permission) {
                                return explode('.', $permission->name)[0];
                            });
                        @endphp
                        
                        @foreach($grouped as $category => $perms)
                            <div class="mb-3">
                                <h6 class="text-primary">{{ ucfirst($category) }}</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($perms as $permission)
                                        <span class="badge bg-success">{{ $permission->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">Aucune permission assignée.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($role->users()->count() === 0)
        <div class="mt-4">
            <form action="{{ route('roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce rôle ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Supprimer ce rôle
                </button>
            </form>
        </div>
    @endif
</div>
@endsection
