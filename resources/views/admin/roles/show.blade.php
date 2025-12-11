@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour aux rôles
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-shield-alt"></i> {{ ucfirst($role->name) }}</h4>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="text-primary"><i class="fas fa-key"></i> Permissions Attribuées</h5>
                        
                        @php
                            $groupedPermissions = $role->permissions->groupBy(function($permission) {
                                $parts = explode(' ', $permission->name);
                                return $parts[1] ?? 'Autre';
                            });
                        @endphp

                        @if($groupedPermissions->count() > 0)
                            @foreach($groupedPermissions as $module => $permissions)
                                <div class="mb-3">
                                    <h6 class="text-secondary">
                                        <i class="fas fa-folder"></i> {{ ucfirst($module) }}
                                        <span class="badge bg-primary">{{ $permissions->count() }}</span>
                                    </h6>
                                    <div class="ms-3">
                                        @foreach($permissions as $permission)
                                            <span class="badge bg-success me-1 mb-1">
                                                <i class="fas fa-check"></i> {{ $permission->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">
                                <i class="fas fa-info-circle"></i> Aucune permission assignée à ce rôle
                            </p>
                        @endif
                    </div>

                    <div class="mt-4">
                        <h5 class="text-primary"><i class="fas fa-users"></i> Utilisateurs avec ce Rôle</h5>
                        
                        @php
                            $users = $role->users()->with('roles')->get();
                        @endphp

                        @if($users->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Autres Rôles</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                            <tr>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    @foreach($user->roles->where('id', '!=', $role->id) as $otherRole)
                                                        <span class="badge bg-secondary">{{ $otherRole->name }}</span>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">
                                <i class="fas fa-info-circle"></i> Aucun utilisateur n'a ce rôle actuellement
                            </p>
                        @endif
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <div>
                            @can('Modifier Rôle')
                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                            @endcan
                        </div>
                        <div>
                            @can('Supprimer Rôle')
                                @if($role->name !== 'super-admin' && $users->count() == 0)
                                    <form action="{{ route('admin.roles.destroy', $role) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce rôle ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </button>
                                    </form>
                                @endif
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-info-circle"></i> Informations
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-6">Nom du rôle:</dt>
                        <dd class="col-sm-6">{{ $role->name }}</dd>

                        <dt class="col-sm-6">Permissions:</dt>
                        <dd class="col-sm-6"><span class="badge bg-success">{{ $role->permissions->count() }}</span></dd>

                        <dt class="col-sm-6">Utilisateurs:</dt>
                        <dd class="col-sm-6"><span class="badge bg-info">{{ $users->count() }}</span></dd>

                        <dt class="col-sm-6">Créé le:</dt>
                        <dd class="col-sm-6">{{ $role->created_at->format('d/m/Y H:i') }}</dd>

                        @if($role->updated_at != $role->created_at)
                            <dt class="col-sm-6">Modifié le:</dt>
                            <dd class="col-sm-6">{{ $role->updated_at->format('d/m/Y H:i') }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
