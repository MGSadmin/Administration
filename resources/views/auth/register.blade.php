<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte - MGS Administration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px 0;
        }
        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 950px;
            width: 100%;
            margin: 20px;
        }
        .register-left {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 60px 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .register-left img {
            max-width: 180px;
            margin-bottom: 25px;
            filter: brightness(0) invert(1);
        }
        .register-left h2 {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        .register-left p {
            opacity: 0.9;
            font-size: 15px;
        }
        .register-left .features {
            margin-top: 30px;
            text-align: left;
        }
        .register-left .features .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .register-left .features .feature-item i {
            font-size: 20px;
            margin-right: 12px;
            opacity: 0.8;
        }
        .register-right {
            padding: 50px 45px;
        }
        .register-right h3 {
            color: #333;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .register-right p {
            color: #666;
            margin-bottom: 25px;
        }
        .form-control {
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 15px;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .input-icon {
            position: relative;
        }
        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        .input-icon input {
            padding-left: 45px;
        }
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: transform 0.2s;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .alert {
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 20px;
        }
        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 5px;
            background: #e1e8ed;
            transition: all 0.3s;
        }
        .password-strength.weak { background: #dc3545; width: 33%; }
        .password-strength.medium { background: #ffc107; width: 66%; }
        .password-strength.strong { background: #28a745; width: 100%; }
        @media (max-width: 768px) {
            .register-left {
                display: none;
            }
            .register-right {
                padding: 35px 25px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="row g-0">
            <!-- Left Side -->
            <div class="col-md-5">
                <div class="register-left">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo MGS">
                    <h2>Rejoignez MGS</h2>
                    <p>Créez votre compte et accédez à toutes nos applications</p>
                    
                    <div class="features">
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Accès à toutes les applications</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-shield-alt"></i>
                            <span>Sécurité renforcée</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-users"></i>
                            <span>Gestion collaborative</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-chart-line"></i>
                            <span>Suivi en temps réel</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Side -->
            <div class="col-md-7">
                <div class="register-right">
                    <h3>Créer un compte</h3>
                    <p>Remplissez le formulaire ci-dessous pour commencer</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Erreur:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" id="registerForm">
                        @csrf

                        <div class="row">
                            <!-- Nom -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <i class="fas fa-user"></i>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           required 
                                           autofocus
                                           placeholder="Votre nom">
                                </div>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Prénom -->
                            <div class="col-md-6 mb-3">
                                <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <i class="fas fa-user"></i>
                                    <input type="text" 
                                           class="form-control @error('prenom') is-invalid @enderror" 
                                           id="prenom" 
                                           name="prenom" 
                                           value="{{ old('prenom') }}" 
                                           required
                                           placeholder="Votre prénom">
                                </div>
                                @error('prenom')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse email <span class="text-danger">*</span></label>
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autocomplete="username"
                                       placeholder="exemple@mgs-local.mg">
                            </div>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Poste -->
                            <div class="col-md-6 mb-3">
                                <label for="poste" class="form-label">Poste</label>
                                <div class="input-icon">
                                    <i class="fas fa-briefcase"></i>
                                    <input type="text" 
                                           class="form-control" 
                                           id="poste" 
                                           name="poste" 
                                           value="{{ old('poste') }}"
                                           placeholder="Votre fonction">
                                </div>
                            </div>

                            <!-- Département -->
                            <div class="col-md-6 mb-3">
                                <label for="departement" class="form-label">Département</label>
                                <div class="input-icon">
                                    <i class="fas fa-building"></i>
                                    <input type="text" 
                                           class="form-control" 
                                           id="departement" 
                                           name="departement" 
                                           value="{{ old('departement') }}"
                                           placeholder="Votre département">
                                </div>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required 
                                       autocomplete="new-password"
                                       placeholder="Minimum 8 caractères">
                            </div>
                            <div class="password-strength" id="passwordStrength"></div>
                            <small class="text-muted">Utilisez au moins 8 caractères avec majuscules, minuscules et chiffres</small>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required 
                                       autocomplete="new-password"
                                       placeholder="Confirmez votre mot de passe">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-register">
                            <i class="fas fa-user-plus me-2"></i>Créer mon compte
                        </button>

                        <div class="text-center mt-3">
                            <p class="mb-0">Vous avez déjà un compte ? 
                                <a href="{{ route('login') }}" class="text-decoration-none fw-bold" style="color: #667eea;">
                                    Se connecter
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function(e) {
            const password = e.target.value;
            const strengthBar = document.getElementById('passwordStrength');
            
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            strengthBar.className = 'password-strength';
            if (strength <= 1) strengthBar.classList.add('weak');
            else if (strength <= 2) strengthBar.classList.add('medium');
            else strengthBar.classList.add('strong');
        });
    </script>
</body>
</html>
