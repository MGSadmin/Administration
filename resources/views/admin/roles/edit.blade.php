@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour aux rôles
        </a>
    </div>

    <h1 class="mb-4"><i class="fas fa-edit"></i> Modifier le Rôle: {{ ucfirst($role->name) }}</h1>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name" class="form-label">Nom du Rôle <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $role->name) }}" 
                                   placeholder="Ex: gestionnaire-stock"
                                   {{ $role->name === 'super-admin' ? 'disabled' : 'required' }}>
                            <div class="form-text">
                                {{ $role->name === 'super-admin' ? 'Le nom du super-admin ne peut pas être modifié' : 'Utilisez un nom en minuscules avec tirets' }}
                            </div>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Permissions <span class="text-danger">*</span></label>
                            <div class="alert alert-info d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-info-circle"></i> Sélectionnez les permissions à attribuer à ce rôle
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleAll()">
                                    <i class="fas fa-check-double"></i> Tout sélectionner
                                </button>
                            </div>

                            @php
                                $groupedPermissions = \Spatie\Permission\Models\Permission::all()->groupBy(function($permission) {
                                    $parts = explode(' ', $permission->name);
                                    return $parts[1] ?? 'Autre';
                                });
                                $currentPermissions = old('permissions', $role->permissions->pluck('name')->toArray());
                            @endphp

                            <div class="accordion" id="permissionsAccordion">
                                @foreach($groupedPermissions as $module => $permissions)
                                    @php
                                        $moduleSelected = $permissions->whereIn('name', $currentPermissions)->count();
                                        $moduleTotal = $permissions->count();
                                    @endphp
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button {{ $moduleSelected > 0 ? '' : 'collapsed' }}" 
                                                    type="button" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#collapse{{ Str::slug($module) }}">
                                                <strong>{{ ucfirst($module) }}</strong>
                                                <span class="badge {{ $moduleSelected == $moduleTotal ? 'bg-success' : ($moduleSelected > 0 ? 'bg-warning' : 'bg-secondary') }} ms-2">
                                                    {{ $moduleSelected }}/{{ $moduleTotal }}
                                                </span>
                                            </button>
                                        </h2>
                                        <div id="collapse{{ Str::slug($module) }}" 
                                             class="accordion-collapse collapse {{ $moduleSelected > 0 ? 'show' : '' }}" 
                                             data-bs-parent="#permissionsAccordion">
                                            <div class="accordion-body">
                                                <div class="mb-2">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-secondary" 
                                                            onclick="toggleModule('{{ Str::slug($module) }}')">
                                                        <i class="fas fa-check-square"></i> Tout sélectionner dans ce module
                                                    </button>
                                                </div>
                                                <div class="row">
                                                    @foreach($permissions as $permission)
                                                        <div class="col-md-6 mb-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input module-{{ Str::slug($module) }}" 
                                                                       type="checkbox" 
                                                                       name="permissions[]" 
                                                                       value="{{ $permission->name }}" 
                                                                       id="perm_{{ $permission->id }}"
                                                                       {{ in_array($permission->name, $currentPermissions) ? 'checked' : '' }}>
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
                                <i class="fas fa-save"></i> Enregistrer les Modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-warning">
                <div class="card-header bg-warning">
                    <i class="fas fa-exclamation-triangle"></i> Attention
                </div>
                <div class="card-body">
                    <p class="small">
                        <strong>Modifier les permissions d'un rôle affectera immédiatement tous les utilisateurs ayant ce rôle.</strong>
                    </p>
                    
                    <h6 class="text-primary mt-3">Utilisateurs affectés</h6>
                    <p class="small text-muted">
                        Ce rôle est attribué à <strong>{{ $role->users()->count() }} utilisateur(s)</strong>.
                    </p>

                    @if($role->name === 'super-admin')
                        <div class="alert alert-danger mt-3">
                            <i class="fas fa-lock"></i> Le rôle super-admin ne peut pas être modifié pour des raisons de sécurité.
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-info mt-3">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-lightbulb"></i> Astuce
                </div>
                <div class="card-body small">
                    <p><strong>Double-clic</strong> sur un titre de module pour sélectionner/désélectionner toutes ses permissions.</p>
                    <p class="mb-0">Utilisez les boutons pour sélectionner rapidement les permissions par module.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleAll() {
        const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
    }

    function toggleModule(module) {
        const checkboxes = document.querySelectorAll('.module-' + module);
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
    }

    // Double-clic sur les titres
    document.querySelectorAll('.accordion-button').forEach(button => {
        button.addEventListener('dblclick', function(e) {
            e.preventDefault();
            const target = this.dataset.bsTarget;
            const module = target.replace('#collapse', '').replace('-', '');
            toggleModule(module.replace('collapse', ''));
        });
    });

</script>
@endpush