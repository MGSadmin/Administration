<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Administration') }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        /* Top navbar */
        .top-navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .top-navbar .navbar-brand img {
            height: 40px;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 56px;
            left: 0;
            bottom: 0;
            width: 250px;
            background-color: #2c3e50;
            color: #ecf0f1;
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 1020;
        }
        
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 12px 20px;
            border-radius: 0;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover {
            background-color: #34495e;
            color: #fff;
        }
        
        .sidebar .nav-link.active {
            background-color: #3498db;
            color: #fff;
        }
        
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .sidebar .sidebar-header {
            padding: 15px 20px;
            background-color: #1a252f;
            border-bottom: 1px solid #34495e;
        }
        
        /* Main content */
        .main-content {
            margin-left: 250px;
            margin-top: 56px;
            padding: 30px 20px 20px 20px;
            min-height: calc(100vh - 56px);
            transition: all 0.3s;
        }
        
        /* Mobile sidebar toggle */
        .sidebar-toggle {
            display: none;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                left: -250px;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar-toggle {
                display: inline-block;
            }
        }
        
        /* Cards */
        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #3498db;
            font-weight: 600;
        }
        
        /* Dropdown user */
        .dropdown-user {
            cursor: pointer;
        }
        
        .dropdown-user:hover {
            background-color: rgba(255,255,255,0.1);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark top-navbar">
        <div class="container-fluid">
            <button class="btn btn-link text-white sidebar-toggle me-2" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Logo MGS" class="me-2" onerror="this.style.display='none'">
                <span>Administration</span>
            </a>
            
            <div class="ms-auto d-flex align-items-center">
                <!-- Notifications -->
                <div class="dropdown me-3">
                    <button class="btn btn-link text-white position-relative" type="button" data-bs-toggle="dropdown" id="notificationDropdown">
                        <i class="fas fa-bell fa-lg"></i>
                        @php
                            $unreadCount = auth()->user()->unreadNotifications()->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="width: 380px; max-height: 500px; overflow-y: auto;">
                        <li class="dropdown-header d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-bell me-2"></i>Notifications</span>
                            @if($unreadCount > 0)
                                <a href="{{ route('notifications.mark-all-read') }}" class="btn btn-sm btn-link text-primary p-0" 
                                   onclick="event.preventDefault(); document.getElementById('mark-all-read-form').submit();">
                                    Tout marquer comme lu
                                </a>
                                <form id="mark-all-read-form" action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            @endif
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        
                        @php
                            $currentAppName = config('app.name');
                            $appLabels = [
                                'administration' => 'Administration',
                                'gestion-dossier' => 'Gestion-Dossier',
                                'commercial' => 'Commercial'
                            ];
                            $appColors = [
                                'administration' => '#667eea',
                                'gestion-dossier' => '#f59e0b',
                                'commercial' => '#10b981'
                            ];
                        @endphp
                        
                        @forelse(auth()->user()->notifications->take(10) as $notification)
                            @php
                                $notifApp = $notification->data['application'] ?? 'all';
                                $isCurrentApp = $notifApp === strtolower(str_replace(' ', '-', $currentAppName)) || $notifApp === 'all';
                                $appLabel = $appLabels[$notifApp] ?? 'Système';
                                $appColor = $appColors[$notifApp] ?? '#6b7280';
                            @endphp
                            <li style="background-color: {{ $notification->read_at ? '#f3f4f6' : '#dbeafe' }}; margin: 2px 0;">
                                <a class="dropdown-item py-3" 
                                   href="{{ $notification->data['url'] ?? '#' }}"
                                   onclick="markAsRead('{{ $notification->id }}')">
                                    <div class="d-flex flex-column">
                                        <!-- En-tête avec badge application et statut -->
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge" style="background-color: {{ $appColor }}; font-size: 0.7rem;">
                                                <i class="fas {{ $notification->data['icon'] ?? 'fa-info-circle' }} me-1"></i>
                                                {{ $appLabel }}
                                            </span>
                                            @if(!$notification->read_at)
                                                <span class="badge bg-info" style="font-size: 0.65rem;">NON LUE</span>
                                            @else
                                                <span class="badge bg-secondary" style="font-size: 0.65rem;">LUE</span>
                                            @endif
                                        </div>
                                        
                                        <!-- Message -->
                                        <div class="mb-1 {{ $notification->read_at ? 'text-muted' : 'fw-bold text-dark' }}" style="font-size: 0.9rem;">
                                            @if(!$isCurrentApp)
                                                <i class="fas fa-arrow-right me-1 text-primary"></i>
                                                <span class="text-primary">Notification de {{ $appLabel }}:</span>
                                            @endif
                                            {{ $notification->data['message'] ?? 'Nouvelle notification' }}
                                        </div>
                                        
                                        <!-- Date -->
                                        <div class="text-muted d-flex align-items-center" style="font-size: 0.75rem;">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $notification->created_at->format('d/m/Y à H:i') }}
                                            <span class="mx-1">•</span>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @if(!$loop->last)
                                <li><hr class="dropdown-divider my-0"></li>
                            @endif
                        @empty
                            <li class="dropdown-item text-center text-muted py-4">
                                <i class="fas fa-bell-slash fa-2x mb-2"></i>
                                <div>Aucune notification</div>
                            </li>
                        @endforelse
                        
                        @if(auth()->user()->notifications->count() > 10)
                            <li><hr class="dropdown-divider"></li>
                            <li class="text-center">
                                <a href="{{ route('notifications.index') }}" class="dropdown-item text-primary">
                                    Voir toutes les notifications
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
                
                <!-- Applications Menu -->
                <div class="dropdown me-3">
                    <button class="btn btn-link text-white dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-th"></i> Applications
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="http://gestion-dossier.mgs-local.mg" target="_blank">
                            <i class="fas fa-folder-open text-primary"></i> Gestion Dossiers
                        </a></li>
                        <li><a class="dropdown-item" href="http://commercial.mgs-local.mg" target="_blank">
                            <i class="fas fa-chart-line text-success"></i> Commercial
                        </a></li>
                    </ul>
                </div>
                
                <!-- User Dropdown -->
                <div class="dropdown">
                    <div class="dropdown-user d-flex align-items-center px-3 py-2 rounded" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle fa-2x text-white me-2"></i>
                        <div class="text-white">
                            <div class="fw-bold">{{ Auth::user()->name }}</div>
                            <small>{{ Auth::user()->email }}</small>
                        </div>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        
                            
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar" >
        <div class="sidebar-heade ">
            <h5 class="mb-0"><i class="fas fa-cog"></i> Administration</h5>
        </div>
        
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="fas fa-home"></i> Tableau de bord
            </a>
            
            <div class="sidebar-header">
                <h6 class="mb-0 text-muted">GESTION</h6>
            </div>
            
            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                <i class="fas fa-users"></i> Utilisateurs
            </a>
            
            <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                <i class="fas fa-user-tag"></i> Rôles
            </a>
            
            <a class="nav-link {{ request()->routeIs('roles.documentation') ? 'active' : '' }}" href="{{ route('roles.documentation') }}">
                <i class="fas fa-book"></i> Documentation Rôles
            </a>
            
            <div class="sidebar-header mt-3">
                <h6 class="mb-0 text-muted">PATRIMOINE</h6>
            </div>
            
            <a class="nav-link {{ request()->routeIs('patrimoines.*') ? 'active' : '' }}" href="{{ route('patrimoines.index') }}">
                <i class="fas fa-archive"></i> Gestion Patrimoine
            </a>
            
            <a class="nav-link {{ request()->routeIs('demandes-fourniture.*') ? 'active' : '' }}" href="{{ route('demandes-fourniture.index') }}">
                <i class="fas fa-shopping-cart"></i> Demandes Fourniture
            </a>
            
            <div class="sidebar-header mt-3">
                <h6 class="mb-0 text-muted">SYSTÈME</h6>
            </div>
            
            <a class="nav-link" href="#">
                <i class="fas fa-database"></i> Sauvegarde
            </a>
            
            <a class="nav-link" href="#">
                <i class="fas fa-history"></i> Logs d'activité
            </a>
            
            <a class="nav-link" href="#">
                <i class="fas fa-cogs"></i> Paramètres
            </a>
        </nav>
    </div>
    
    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
        
        // Auto-hide alerts
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Mark notification as read
        function markAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    // Update notification count
                    setTimeout(() => location.reload(), 100);
                }
            });
        }

        // Check for new notifications every 30 seconds
        setInterval(() => {
            fetch('/notifications/check-new', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.hasNew) {
                    // Show toast notification
                    const toastHtml = `
                        <div class="toast align-items-center text-white bg-primary border-0 position-fixed top-0 end-0 m-3" 
                             role="alert" style="z-index: 9999;">
                            <div class="d-flex">
                                <div class="toast-body">
                                    <i class="fas fa-bell me-2"></i>${data.message}
                                </div>
                                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                            </div>
                        </div>
                    `;
                    document.body.insertAdjacentHTML('beforeend', toastHtml);
                    const toast = new bootstrap.Toast(document.querySelector('.toast:last-child'));
                    toast.show();
                    
                    // Refresh notification dropdown
                    location.reload();
                }
            });
        }, 30000); // Check every 30 seconds
    </script>
    
    @stack('scripts')
</body>
</html>
