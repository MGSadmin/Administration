@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4"><i class="fas fa-book"></i> Documentation des Rôles et Permissions</h1>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h3>{{ \Spatie\Permission\Models\Role::count() }}</h3>
                    <p class="mb-0">Rôles disponibles</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h3>{{ \Spatie\Permission\Models\Permission::count() }}</h3>
                    <p class="mb-0">Permissions totales</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h3>3</h3>
                    <p class="mb-0">Applications</p>
                </div>
            </div>
        </div>
    </div>

    @php
        $roles = \Spatie\Permission\Models\Role::with('permissions')->get();
        $permissions = \Spatie\Permission\Models\Permission::all()->groupBy(function($p) {
            return explode('.', $p->name)[0];
        });
    @endphp

    <!-- Description des rôles -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-users-cog"></i> Description des Rôles</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Rôle</th>
                            <th>Description</th>
                            <th>Permissions</th>
                            <th>Applications</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="badge bg-danger">super-admin</span></td>
                            <td>Administrateur système - Accès total à toutes les fonctionnalités</td>
                            <td><span class="badge bg-info">{{ $roles->where('name', 'super-admin')->first()?->permissions->count() ?? 0 }}</span></td>
                            <td>Administration, Commercial, Gestion-Dossier</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-warning">admin</span></td>
                            <td>Administrateur - Gestion des utilisateurs et rôles + lecture des applications</td>
                            <td><span class="badge bg-info">{{ $roles->where('name', 'admin')->first()?->permissions->count() ?? 0 }}</span></td>
                            <td>Administration</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-purple">direction</span></td>
                            <td>Direction - Accès complet sauf gestion système</td>
                            <td><span class="badge bg-info">{{ $roles->where('name', 'direction')->first()?->permissions->count() ?? 0 }}</span></td>
                            <td>Toutes (lecture/modification)</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-success">commercial</span></td>
                            <td>Commercial - Gestion dossiers, cotations, devis et clients</td>
                            <td><span class="badge bg-info">{{ $roles->where('name', 'commercial')->first()?->permissions->count() ?? 0 }}</span></td>
                            <td>Commercial, Gestion-Dossier</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-primary">facture</span></td>
                            <td>Facturier - Gestion des factures uniquement</td>
                            <td><span class="badge bg-info">{{ $roles->where('name', 'facture')->first()?->permissions->count() ?? 0 }}</span></td>
                            <td>Gestion-Dossier (factures)</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-info">comptable</span></td>
                            <td>Comptable - Gestion financière, règlements et validation débours</td>
                            <td><span class="badge bg-info">{{ $roles->where('name', 'comptable')->first()?->permissions->count() ?? 0 }}</span></td>
                            <td>Gestion-Dossier (finance)</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-warning">production</span></td>
                            <td>Production - Suivi de production et gestion situations</td>
                            <td><span class="badge bg-info">{{ $roles->where('name', 'production')->first()?->permissions->count() ?? 0 }}</span></td>
                            <td>Gestion-Dossier (production)</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-secondary">consultation</span></td>
                            <td>Consultation - Lecture seule sur toutes les applications</td>
                            <td><span class="badge bg-info">{{ $roles->where('name', 'consultation')->first()?->permissions->count() ?? 0 }}</span></td>
                            <td>Toutes (lecture seule)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Détail des permissions par catégorie -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-shield-alt"></i> Permissions par Catégorie</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($permissions as $category => $perms)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 text-primary">
                                    <i class="fas fa-tag"></i> {{ ucfirst($category) }}
                                    <span class="badge bg-secondary float-end">{{ $perms->count() }}</span>
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($perms as $perm)
                                        <span class="badge bg-light text-dark border">{{ $perm->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Matrice Rôles/Permissions -->
    <div class="card mt-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-table"></i> Matrice des Permissions par Rôle</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Cette matrice montre quels rôles ont accès à quelles permissions
            </div>
            
            @foreach($permissions as $category => $perms)
                <h6 class="mt-4 text-primary"><i class="fas fa-folder"></i> {{ ucfirst($category) }}</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 250px;">Permission</th>
                                @foreach($roles as $role)
                                    <th class="text-center" style="width: 100px;">{{ $role->name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($perms as $perm)
                                <tr>
                                    <td><small>{{ $perm->name }}</small></td>
                                    @foreach($roles as $role)
                                        <td class="text-center">
                                            @if($role->permissions->contains('id', $perm->id))
                                                <i class="fas fa-check text-success"></i>
                                            @else
                                                <i class="fas fa-times text-danger opacity-25"></i>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
.bg-purple {
    background-color: #6f42c1;
    color: white;
}
.opacity-25 {
    opacity: 0.25;
}
</style>
@endsection
