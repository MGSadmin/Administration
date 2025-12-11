<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - MGS Administration</title>
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
        
        .register-container {
            width: 100%;
            max-width: 1200px;
        }
        
        .register-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            animation: slideIn 0.6s ease-out;
            display: flex;
            flex-direction: row;
            min-height: 700px;
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
        
        .register-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 45px 40px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
            flex: 0 0 42%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .register-header::before {
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
            width: 130px;
            height: 65px;
            background: white;
            border-radius: 15px;
            margin: 0 auto 20px;
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
        
        .register-header h1 {
            margin: 0;
            font-size: 30px;
            font-weight: 700;
            letter-spacing: -0.5px;
            position: relative;
            z-index: 1;
        }
        
        .register-header p {
            margin: 10px 0 0 0;
            opacity: 0.95;
            font-size: 14px;
            font-weight: 400;
            position: relative;
            z-index: 1;
        }
        
        .site-selector {
            margin-top: 20px;
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }
        
        .site-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
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
        
        .register-body {
            padding: 45px 40px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow-y: auto;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-label {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
        }
        
        .form-label i {
            color: var(--primary-color);
        }
        
        .form-label .required {
            color: var(--danger-color);
            margin-left: 2px;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
            font-size: 16px;
            z-index: 10;
        }
        
        .form-control {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px 14px 12px 44px;
            font-size: 14px;
            transition: all 0.3s;
            width: 100%;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }
        
        .form-control.is-invalid {
            border-color: var(--danger-color);
        }
        
        .form-select {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px 14px 12px 44px;
            font-size: 14px;
            transition: all 0.3s;
            width: 100%;
            cursor: pointer;
        }
        
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }
        
        .password-toggle {
            position: absolute;
            right: 14px;
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
        
        .password-strength {
            margin-top: 8px;
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
            display: none;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s;
            border-radius: 2px;
        }
        
        .password-strength.show {
            display: block;
        }
        
        .password-strength.weak .password-strength-bar {
            width: 33%;
            background: var(--danger-color);
        }
        
        .password-strength.medium .password-strength-bar {
            width: 66%;
            background: var(--warning-color);
        }
        
        .password-strength.strong .password-strength-bar {
            width: 100%;
            background: var(--success-color);
        }
        
        .password-hint {
            font-size: 11px;
            color: #6b7280;
            margin-top: 6px;
        }
        
        .btn-register {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            margin-top: 10px;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
        }
        
        .btn-register:active {
            transform: translateY(0);
        }
        
        .btn-register:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
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
            margin: 25px 0;
            color: #9ca3af;
            font-size: 12px;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .divider span {
            padding: 0 12px;
        }
        
        .login-link {
            text-align: center;
            padding: 15px 0 0 0;
        }
        
        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
            font-size: 14px;
        }
        
        .login-link a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
        
        .register-footer {
            text-align: center;
            padding: 20px;
            background: #f9fafb;
            color: #6b7280;
            font-size: 12px;
            border-top: 1px solid #e5e7eb;
        }
        
        .register-footer i {
            color: var(--success-color);
            margin-right: 4px;
        }
        
        .text-danger {
            color: var(--danger-color);
            font-size: 11px;
            margin-top: 5px;
            display: block;
        }
        
        .terms-checkbox {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin: 20px 0;
        }
        
        .terms-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--primary-color);
            margin-top: 2px;
            flex-shrink: 0;
        }
        
        .terms-checkbox label {
            margin: 0;
            cursor: pointer;
            font-size: 13px;
            color: #4b5563;
            line-height: 1.5;
        }
        
        .terms-checkbox a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .terms-checkbox a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 992px) {
            .register-card {
                flex-direction: column;
                min-height: auto;
            }
            
            .register-header {
                flex: none;
                padding: 40px 30px;
            }
            
            .register-body {
                padding: 35px 30px;
            }
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
            
            .register-header {
                padding: 35px 30px;
            }
            
            .register-body {
                padding: 30px 25px;
            }
            
            .logo-container {
                width: 75px;
                height: 75px;
                font-size: 34px;
            }
            
            .register-header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <div class="logo-container">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo MGS">
                </div>
                <h1>Créer un compte</h1>
                <p>Rejoignez la plateforme de TLT</p>
                
                
            </div>
            
            <div class="register-body">
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
                
                <form method="POST" action="{{ route('auth.register') }}" id="registerForm">
                    @csrf
                    <input type="hidden" name="site" id="siteInput" value="{{ request('site', 'admin') }}">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-user"></i>
                                Nom <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                <i class="input-icon fas fa-user"></i>
                                <input 
                                    type="text" 
                                    name="last_name" 
                                    class="form-control @error('last_name') is-invalid @enderror" 
                                    placeholder="Votre nom"
                                    value="{{ old('last_name') }}"
                                    required
                                >
                            </div>
                            @error('last_name')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-user"></i>
                                Prénom <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                <i class="input-icon fas fa-user"></i>
                                <input 
                                    type="text" 
                                    name="first_name" 
                                    class="form-control @error('first_name') is-invalid @enderror" 
                                    placeholder="Votre prénom"
                                    value="{{ old('first_name') }}"
                                    required
                                >
                            </div>
                            @error('first_name')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-envelope"></i>
                            Adresse email <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <i class="input-icon fas fa-at"></i>
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
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-lock"></i>
                                Mot de passe <span class="required">*</span>
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
                                    onkeyup="checkPasswordStrength()"
                                >
                                <span class="password-toggle" onclick="togglePassword('password', 'toggleIcon1')">
                                    <i class="fas fa-eye" id="toggleIcon1"></i>
                                </span>
                            </div>
                            <div class="password-strength" id="passwordStrength">
                                <div class="password-strength-bar"></div>
                            </div>
                            <small class="password-hint">Min. 8 caractères, une majuscule, un chiffre</small>
                            @error('password')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-lock"></i>
                                Confirmer le mot de passe <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                <i class="input-icon fas fa-key"></i>
                                <input 
                                    type="password" 
                                    name="password_confirmation" 
                                    id="password_confirmation"
                                    class="form-control" 
                                    placeholder="••••••••••"
                                    required
                                >
                                <span class="password-toggle" onclick="togglePassword('password_confirmation', 'toggleIcon2')">
                                    <i class="fas fa-eye" id="toggleIcon2"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-phone"></i>
                                Téléphone
                            </label>
                            <div class="input-group">
                                <i class="input-icon fas fa-mobile-alt"></i>
                                <input 
                                    type="tel" 
                                    name="phone" 
                                    class="form-control @error('phone') is-invalid @enderror" 
                                    placeholder="+261 XX XX XXX XX"
                                    value="{{ old('phone') }}"
                                >
                            </div>
                            @error('phone')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-briefcase"></i>
                                Poste
                            </label>
                            <div class="input-group">
                                <i class="input-icon fas fa-id-badge"></i>
                                <input 
                                    type="text" 
                                    name="position" 
                                    class="form-control @error('position') is-invalid @enderror" 
                                    placeholder="Ex: Développeur"
                                    value="{{ old('position') }}"
                                >
                            </div>
                            @error('position')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- <div class="terms-checkbox">
                        <input type="checkbox" name="terms" id="terms" required>
                        <label for="terms">
                            J'accepte les <a href="#" target="_blank">conditions d'utilisation</a> 
                            et la <a href="#" target="_blank">politique de confidentialité</a>
                        </label>
                    </div> -->
                    
                    <button type="submit" class="btn-register" id="registerBtn">
                        <i class="fas fa-user-plus"></i>
                        <span>Créer mon compte</span>
                    </button>
                </form>
                
                <div class="divider">
                    <span>ou</span>
                </div>
                
                <div class="login-link">
                    <span>Vous avez déjà un compte ? </span>
                    <a href="{{ route('auth.login', ['site' => request('site', 'admin')]) }}">
                        Se connecter
                    </a>
                </div>
            
            
                <div class="register-footer">
                    <div>
                        <i class="fas fa-shield-alt"></i>
                        Données protégées et sécurisées
                    </div>
                    <div style="margin-top: 6px;">
                        © {{ date('Y') }} MGS - Tous droits réservés
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function togglePassword(fieldId, iconId) {
            const passwordInput = document.getElementById(fieldId);
            const toggleIcon = document.getElementById(iconId);
            
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
        
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthBar = document.getElementById('passwordStrength');
            
            if (password.length === 0) {
                strengthBar.classList.remove('show', 'weak', 'medium', 'strong');
                return;
            }
            
            strengthBar.classList.add('show');
            strengthBar.classList.remove('weak', 'medium', 'strong');
            
            let strength = 0;
            
            // Length check
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            
            // Complexity checks
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            if (strength <= 2) {
                strengthBar.classList.add('weak');
            } else if (strength <= 4) {
                strengthBar.classList.add('medium');
            } else {
                strengthBar.classList.add('strong');
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
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('registerBtn');
            const terms = document.getElementById('terms');
            
            if (!terms.checked) {
                e.preventDefault();
                alert('Veuillez accepter les conditions d\'utilisation');
                return;
            }
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Création en cours...</span>';
        });
    </script>
</body>
</html>
