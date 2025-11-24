<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nouvel Utilisateur') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <!-- Informations personnelles -->
                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nom *</label>
                                <input type="text" name="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-gray-300">
                                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Prénom</label>
                                <input type="text" name="prenom" value="{{ old('prenom') }}" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Matricule</label>
                                <input type="text" name="matricule" value="{{ old('matricule') }}" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email *</label>
                                <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 block w-full rounded-md border-gray-300">
                                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Mot de passe *</label>
                                <input type="password" name="password" required class="mt-1 block w-full rounded-md border-gray-300">
                                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Confirmer le mot de passe *</label>
                                <input type="password" name="password_confirmation" required class="mt-1 block w-full rounded-md border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Téléphone</label>
                                <input type="text" name="telephone" value="{{ old('telephone') }}" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Poste</label>
                                <input type="text" name="poste" value="{{ old('poste') }}" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Département</label>
                                <input type="text" name="departement" value="{{ old('departement') }}" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date d'embauche</label>
                                <input type="date" name="date_embauche" value="{{ old('date_embauche') }}" class="mt-1 block w-full rounded-md border-gray-300">
                            </div>
                        </div>

                        <!-- Rôles -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rôles</label>
                            <div class="grid grid-cols-3 gap-4">
                                @foreach($roles as $role)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="rounded border-gray-300">
                                        <span class="ml-2 text-sm text-gray-700">{{ ucfirst($role->name) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Applications -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Accès aux Applications</label>
                            @foreach($applications as $index => $app)
                                <div class="flex items-center gap-4 mb-3 p-3 bg-gray-50 rounded">
                                    <input type="checkbox" name="applications[{{$index}}][name]" value="{{$app}}" class="rounded border-gray-300">
                                    <span class="flex-1 text-sm font-medium">{{ ucfirst($app) }}</span>
                                    <select name="applications[{{$index}}][status]" class="rounded-md border-gray-300 text-sm">
                                        <option value="active">Actif</option>
                                        <option value="inactive">Inactif</option>
                                        <option value="blocked">Bloqué</option>
                                    </select>
                                </div>
                            @endforeach
                        </div>

                        <!-- Statut -->
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300">
                                <span class="ml-2 text-sm text-gray-700">Utilisateur actif</span>
                            </label>
                        </div>

                        <!-- Boutons -->
                        <div class="flex justify-end gap-4">
                            <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Annuler
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Créer l'utilisateur
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
