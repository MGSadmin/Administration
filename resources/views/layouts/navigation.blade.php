@if(Auth::check())
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    {{-- Menu Organigramme --}}
                    @can('Voir Organigramme')
                    <x-nav-link :href="route('organigramme.index')" :active="request()->routeIs('organigramme.*')">
                        {{ __('Organigramme') }}
                    </x-nav-link>
                    @endcan

                    {{-- Menu Patrimoine (Admin, RH, Direction, Chef Dept) --}}
                    @if(Auth::user() && Auth::user()->can('Voir Patrimoine'))
                    <x-nav-link :href="route('patrimoines.index')" :active="request()->routeIs('patrimoines.*')">
                        {{ __('Patrimoine') }}
                    </x-nav-link>
                    @endif

                    {{-- Menu Demandes de Fourniture --}}
                    @if(Auth::user() && Auth::user()->can('Voir Demande Fourniture'))
                    <x-nav-link :href="route('demandes-fourniture.index')" :active="request()->routeIs('demandes-fourniture.*')">
                        {{ __('Fournitures') }}
                    </x-nav-link>
                    @endif

                    {{-- Menu Congés/Absences --}}
                    @if(Auth::user() && Auth::user()->can('Voir Congé'))
                    <x-nav-link :href="route('conges.index')" :active="request()->routeIs('conges.*', 'absences.*')">
                        {{ __('Congés') }}
                    </x-nav-link>
                    @endif

                    {{-- Menu Personnel (Admin, RH, Direction, Chef Dept) --}}
                    @if(Auth::user() && Auth::user()->can('Voir Personnel'))
                    <x-nav-link :href="route('personnel.index')" :active="request()->routeIs('personnel.*')">
                        {{ __('Personnel') }}
                    </x-nav-link>
                    @endif

                    {{-- Menu Utilisateurs (Admin uniquement) --}}
                    @if(Auth::user() && Auth::user()->can('Voir Utilisateurs'))
                    <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                        {{ __('Utilisateurs') }}
                    </x-nav-link>
                    @endif
                    
                    {{-- Menu Rôles (Admin uniquement) --}}
                    @if(Auth::user() && Auth::user()->can('Voir Rôles'))
                    <x-nav-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')">
                        {{ __('Rôles') }}
                    </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('auth.logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('auth.logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Déconnexion') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @can('Voir Organigramme')
            <x-responsive-nav-link :href="route('organigramme.index')" :active="request()->routeIs('organigramme.*')">
                {{ __('Organigramme') }}
            </x-responsive-nav-link>
            @endcan

            @if(Auth::user() && Auth::user()->can('Voir Patrimoine'))
            <x-responsive-nav-link :href="route('patrimoines.index')" :active="request()->routeIs('patrimoines.*')">
                {{ __('Patrimoine') }}
            </x-responsive-nav-link>
            @endif

            @if(Auth::user() && Auth::user()->can('Voir Demande Fourniture'))
            <x-responsive-nav-link :href="route('demandes-fourniture.index')" :active="request()->routeIs('demandes-fourniture.*')">
                {{ __('Fournitures') }}
            </x-responsive-nav-link>
            @endif

            @if(Auth::user() && Auth::user()->can('Voir Congé'))
            <x-responsive-nav-link :href="route('conges.index')" :active="request()->routeIs('conges.*', 'absences.*')">
                {{ __('Congés') }}
            </x-responsive-nav-link>
            @endif

            @if(Auth::user() && Auth::user()->can('Voir Personnel'))
            <x-responsive-nav-link :href="route('personnel.index')" :active="request()->routeIs('personnel.*')">
                {{ __('Personnel') }}
            </x-responsive-nav-link>
            @endif

            @if(Auth::user() && Auth::user()->can('Voir Utilisateurs'))
            <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                {{ __('Utilisateurs') }}
            </x-responsive-nav-link>
            @endif
            
            @if(Auth::user() && Auth::user()->can('Voir Rôles'))
            <x-responsive-nav-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')">
                {{ __('Rôles') }}
            </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Authentication -->
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('auth.logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Déconnexion') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
@else
<!-- Non authentifié - Message de connexion requise -->
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="text-center px-4">
        <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Accès restreint</h2>
        <p class="text-gray-600 mb-6">Vous devez être connecté pour accéder à cette application.</p>
        <a href="{{ route('auth.login') }}" 
           class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-sign-in-alt mr-2"></i> Se connecter
        </a>
    </div>
</div>
@endif

