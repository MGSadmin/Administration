<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - MGS Administration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
        }
        
        .login-container {
            width: 100%;
            max-width: 1100px;
        }
        
        .login-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            animation: slideIn 0.6s ease-out;
            display: flex;
            flex-direction: row;
            min-height: 600px;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 50px 40px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
            flex: 0 0 45%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        
        .logo-container {
            width: 140px;
            height: 70px;
            background: white;
            border-radius: 15px;
            margin: 0 auto 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
        }
        
        .logo-container img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }
        
        .login-header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: -0.5px;
            position: relative;
            z-index: 1;
        }
        
        .login-header p {
            margin: 12px 0 0 0;
            opacity: 0.95;
            font-size: 15px;
            font-weight: 400;
            position: relative;
            z-index: 1;
        }
        
        .site-selector {
            margin-top: 25px;
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }
        
        .site-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        
        .site-badge:hover {
            background: rgba(255, 255, 255, 0.35);
            transform: translateY(-2px);
        }
        
        .site-badge.active {
            background: white;
            color: var(--primary-color);
            border-color: white;
        }
        
        .login-body {
            padding: 45px 40px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .form-group {
            margin-bottom: 28px;
        }
        
        .form-label {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        
        .form-label i {
            color: var(--primary-color);
        }
        
        .input-group {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
            font-size: 18px;
            z-index: 10;
        }
        
        .form-control {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 14px 16px 14px 48px;
            font-size: 15px;
            transition: all 0.3s;
            width: 100%;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            outline: none;
        }
        
        .form-control.is-invalid {
            border-color: var(--danger-color);
        }
        
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9ca3af;
            z-index: 10;
            padding: 8px;
            transition: color 0.2s;
        }
        
        .password-toggle:hover {
            color: var(--primary-color);
        }
        
        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            padding: 14px 18px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }
        
        .alert-info {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 30px 0;
            color: #9ca3af;
            font-size: 13px;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .divider span {
            padding: 0 15px;
        }
        
        .register-link {
            text-align: center;
            padding: 20px 0 0 0;
        }
        
        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .register-link a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
        
        .login-footer {
            text-align: center;
            padding: 25px;
            background: #f9fafb;
            color: #6b7280;
            font-size: 13px;
            border-top: 1px solid #e5e7eb;
        }
        
        .login-footer i {
            color: var(--success-color);
            margin-right: 5px;
        }
        
        .text-danger {
            color: var(--danger-color);
            font-size: 12px;
            margin-top: 6px;
            display: block;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 25px;
        }
        
        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--primary-color);
        }
        
        .remember-me label {
            margin: 0;
            cursor: pointer;
            font-size: 14px;
            color: #4b5563;
        }
        
        @media (max-width: 992px) {
            .login-card {
                flex-direction: column;
                min-height: auto;
            }
            
            .login-header {
                flex: none;
                padding: 40px 30px;
            }
            
            .login-body {
                padding: 35px 30px;
            }
        }
        
        @media (max-width: 576px) {
            .logo-container {
                width: 110px;
                height: 55px;
                padding: 8px;
            }
            
            .login-header h1 {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-container">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo MGS">
                </div>
                <h1>Connexion</h1>
                <p>Plateforme de gestion TLT</p>
                
               
            </div>
            
            <div class="login-body">
                @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                </div>
                @endif
                
                @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
                @endif
                
                @if(session('info'))
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <span>{{ session('info') }}</span>
                </div>
                @endif
                
                <form method="POST" action="{{ route('auth.login') }}" id="loginForm">
                    @csrf
                    @if(request('redirect'))
                    <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                    @endif
                    <input type="hidden" name="site" id="siteInput" value="{{ request('site', 'admin') }}">
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-envelope"></i>
                            Adresse email
                        </label>
                        <div class="input-group">
                            <i class="input-icon fas fa-user"></i>
                            <input 
                                type="email" 
                                name="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                placeholder="votre.email@mgs.mg"
                                value="{{ old('email') }}"
                                required 
                                autofocus
                            >
                        </div>
                        @error('email')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock"></i>
                            Mot de passe
                        </label>
                        <div class="input-group">
                            <i class="input-icon fas fa-key"></i>
                            <input 
                                type="password" 
                                name="password" 
                                id="password"
                                class="form-control @error('password') is-invalid @enderror" 
                                placeholder="••••••••••"
                                required
                            >
                            <span class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                        @error('password')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div class="remember-me">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember">Se souvenir de moi</label>
                    </div>
                    
                    <button type="submit" class="btn-login" id="loginBtn">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Se connecter</span>
                    </button>
                </form>
                
                <div class="divider">
                    <span>ou</span>
                </div>
                
                <div class="register-link">
                    <span>Vous n'avez pas de compte ? </span>
                    <a href="{{ route('auth.register', ['site' => request('site', 'admin')]) }}">
                        Créer un compte
                    </a>
                </div>
           
            
                <div class="login-footer">
                    <div>
                        <i class="fas fa-shield-alt"></i>
                        Connexion sécurisée SSL
                    </div>
                    <div style="margin-top: 8px;">
                        © {{ date('Y') }} MGS - Tous droits réservés
                    </div>
                </div>
             </div>
        </div>
    </div>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        function selectSite(site) {
            document.getElementById('siteInput').value = site;
            
            // Update active badge
            document.querySelectorAll('.site-badge').forEach(badge => {
                badge.classList.remove('active');
            });
            event.currentTarget.classList.add('active');
            
            // Update URL without reload
            const url = new URL(window.location);
            url.searchParams.set('site', site);
            window.history.pushState({}, '', url);
        }
        
        // Form submission animation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Connexion en cours...</span>';
        });
        
        // Auto-focus email field
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.querySelector('input[name="email"]');
            if (emailInput && !emailInput.value) {
                emailInput.focus();
            }
            
            // Auto-sélectionner le site selon l'URL de redirection
            const redirectUrl = '{{ request("redirect", "") }}';
            if (redirectUrl) {
                let selectedSite = 'admin';
                if (redirectUrl.includes('commercial')) {
                    selectedSite = 'commercial';
                } else if (redirectUrl.includes('debours') || redirectUrl.includes('gestion')) {
                    selectedSite = 'gestion';
                }
                
                // Mettre à jour le site sélectionné
                document.getElementById('siteInput').value = selectedSite;
                document.querySelectorAll('.site-badge').forEach(badge => {
                    badge.classList.remove('active');
                });
                const siteBadge = document.querySelector(`.site-badge[onclick*="${selectedSite}"]`);
                if (siteBadge) {
                    siteBadge.classList.add('active');
                }
            }
        });
    </script>
</body>
</html>
