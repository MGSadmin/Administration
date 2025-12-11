@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour aux rôles
        </a>
    </div>

    <h1 class="mb-4"><i class="fas fa-plus-circle"></i> Créer un Nouveau Rôle</h1>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.roles.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="form-label">Nom du Rôle <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="Ex: gestionnaire-stock"
                                   required>
                            <div class="form-text">Utilisez un nom en minuscules avec tirets (ex: chef-projet)</div>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Permissions <span class="text-danger">*</span></label>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Sélectionnez les permissions à attribuer à ce rôle
                            </div>

                            @php
                                $groupedPermissions = \Spatie\Permission\Models\Permission::all()->groupBy(function($permission) {
                                    // Grouper par le premier mot
                                    $parts = explode(' ', $permission->name);
                                    return $parts[1] ?? 'Autre'; // Prendre le 2ème mot (module)
                                });
                            @endphp

                            <div class="accordion" id="permissionsAccordion">
                                @foreach($groupedPermissions as $module => $permissions)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#collapse{{ Str::slug($module) }}">
                                                <strong>{{ ucfirst($module) }}</strong>
                                                <span class="badge bg-primary ms-2">{{ $permissions->count() }}</span>
                                            </button>
                                        </h2>
                                        <div id="collapse{{ Str::slug($module) }}" 
                                             class="accordion-collapse collapse" 
                                             data-bs-parent="#permissionsAccordion">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    @foreach($permissions as $permission)
                                                        <div class="col-md-6 mb-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" 
                                                                       type="checkbox" 
                                                                       name="permissions[]" 
                                                                       value="{{ $permission->name }}" 
                                                                       id="perm_{{ $permission->id }}"
                                                                       {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                                    {{ $permission->name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @error('permissions')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Créer le Rôle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-lightbulb"></i> Conseils
                </div>
                <div class="card-body">
                    <h6 class="text-primary">Nommage des rôles</h6>
                    <ul class="small">
                        <li>Utilisez des minuscules</li>
                        <li>Séparez les mots par des tirets</li>
                        <li>Soyez descriptif</li>
                    </ul>

                    <h6 class="text-primary mt-3">Permissions</h6>
                    <ul class="small">
                        <li>Accordez uniquement les permissions nécessaires</li>
                        <li>Regroupées par module</li>
                        <li>Noms en français pour clarté</li>
                    </ul>

                    <h6 class="text-primary mt-3">Exemples</h6>
                    <ul class="small text-muted">
                        <li>gestionnaire-stock</li>
                        <li>chef-projet</li>
                        <li>comptable</li>
                        <li>auditeur</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Sélectionner/Désélectionner tous dans un module
    document.querySelectorAll('.accordion-button').forEach(button => {
        button.addEventListener('dblclick', function(e) {
            e.preventDefault();
            const target = this.dataset.bsTarget;
            const checkboxes = document.querySelectorAll(target + ' input[type="checkbox"]');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
        });
    });
    });
</script>
@endpush