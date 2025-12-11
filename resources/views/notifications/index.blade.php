@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-bell"></i> Mes Notifications</h1>
        @if($notifications->where('read_at', null)->count() > 0)
            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check-double"></i> Tout marquer comme lu
                </button>
            </form>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $currentAppName = config('app.name');
        $appLabels = [
            'administration' => 'Administration',
            'gestion-dossier' => 'Gestion-Dossier',
            'commercial' => 'Commercial',
            'all' => 'Système'
        ];
        $appColors = [
            'administration' => '#667eea',
            'gestion-dossier' => '#f59e0b',
            'commercial' => '#10b981',
            'all' => '#6b7280'
        ];
        $appIcons = [
            'administration' => 'fa-user-shield',
            'gestion-dossier' => 'fa-folder-open',
            'commercial' => 'fa-chart-line',
            'all' => 'fa-globe'
        ];
    @endphp

    <div class="card">
        <div class="card-body p-0">
            @forelse($notifications as $notification)
                @php
                    $notifApp = $notification->data['application'] ?? 'all';
                    $isCurrentApp = $notifApp === strtolower(str_replace(' ', '-', $currentAppName)) || $notifApp === 'all';
                    $appLabel = $appLabels[$notifApp] ?? 'Système';
                    $appColor = $appColors[$notifApp] ?? '#6b7280';
                    $appIcon = $appIcons[$notifApp] ?? 'fa-info-circle';
                @endphp
                
                <div class="notification-item border-bottom p-4" 
                     style="background-color: {{ $notification->read_at ? '#f9fafb' : '#dbeafe' }}; transition: all 0.3s;">
                    <div class="d-flex align-items-start">
                        <!-- Icône de l'application -->
                        <div class="me-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 50px; height: 50px; background-color: {{ $appColor }}20; border: 2px solid {{ $appColor }};">
                                <i class="fas {{ $notification->data['icon'] ?? $appIcon }} fa-lg" style="color: {{ $appColor }};"></i>
                            </div>
                        </div>
                        
                        <div class="flex-grow-1">
                            <!-- En-tête -->
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="badge me-2" style="background-color: {{ $appColor }};">
                                        <i class="fas {{ $appIcon }} me-1"></i>{{ $appLabel }}
                                    </span>
                                    @if(!$notification->read_at)
                                        <span class="badge bg-info">
                                            <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>NON LUE
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-check me-1"></i>LUE
                                        </span>
                                    @endif
                                </div>
                                <div class="d-flex gap-2">
                                    @if(!$notification->read_at)
                                        <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary" title="Marquer comme lue">
                                                <i class="fas fa-check"></i> Marquer comme lue
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Supprimer cette notification ?')" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Titre -->
                            <h6 class="mb-2 {{ $notification->read_at ? 'text-muted' : 'fw-bold text-dark' }}">
                                @if(!$isCurrentApp)
                                    <i class="fas fa-arrow-right me-1" style="color: {{ $appColor }};"></i>
                                    <span style="color: {{ $appColor }};">Notification de {{ $appLabel }}:</span>
                                @endif
                                {{ $notification->data['title'] ?? 'Notification' }}
                            </h6>
                            
                            <!-- Message -->
                            <p class="mb-2 {{ $notification->read_at ? 'text-muted' : 'text-dark' }}">
                                {{ $notification->data['message'] ?? 'Nouvelle notification' }}
                            </p>
                            
                            <!-- Date et actions -->
                            <div class="d-flex align-items-center text-muted" style="font-size: 0.85rem;">
                                <i class="fas fa-calendar me-1"></i>
                                {{ $notification->created_at->format('d/m/Y à H:i') }}
                                <span class="mx-2">•</span>
                                <i class="fas fa-clock me-1"></i>
                                {{ $notification->created_at->diffForHumans() }}
                            </div>
                            
                            @if(isset($notification->data['url']) && $notification->data['url'] !== '#')
                                <div class="mt-2">
                                    <a href="{{ $notification->data['url'] }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-external-link-alt me-1"></i>Voir les détails
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune notification</h5>
                    <p class="text-muted">Vous n'avez pas encore de notifications</p>
                </div>
            @endforelse

            @if($notifications->hasPages())
                <div class="p-4">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .notification-item:hover {
        background-color: #e0f2fe !important;
        cursor: pointer;
    }
    .notification-item:last-child {
        border-bottom: none !important;
    }
</style>
@endpush
@endsection
