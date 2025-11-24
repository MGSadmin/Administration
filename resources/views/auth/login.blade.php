<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - MGS Administration</title>
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
        }
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            margin: 20px;
        }
        .login-left {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 60px 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .login-left img {
            max-width: 200px;
            margin-bottom: 30px;
            filter: brightness(0) invert(1);
        }
        .login-left h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        .login-left p {
            opacity: 0.9;
            font-size: 16px;
        }
        .login-right {
            padding: 60px 50px;
        }
        .login-right h3 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .login-right p {
            color: #666;
            margin-bottom: 30px;
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
        .btn-login {
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
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
        }
        .divider::before {
            content: "";
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #e1e8ed;
        }
        .divider span {
            background: white;
            padding: 0 15px;
            position: relative;
            color: #999;
            font-size: 14px;
        }
        .alert {
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .login-left {
                display: none;
            }
            .login-right {
                padding: 40px 30px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="row g-0">
            <!-- Left Side -->
            <div class="col-md-5">
                <div class="login-left">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo MGS">
                    <h2>Bienvenue sur MGS</h2>
                    <p>Système de Gestion Multi-Applications</p>
                    <div class="mt-4">
                        <i class="fas fa-shield-alt fa-3x mb-3" style="opacity: 0.7;"></i>
                        <p class="small">Plateforme sécurisée pour la gestion de vos applications d'entreprise</p>
                    </div>
                </div>
            </div>
            
            <!-- Right Side -->
            <div class="col-md-7">
                <div class="login-right">
                    <h3>Se connecter</h3>
                    <p>Accédez à votre espace de travail</p>

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Erreur:</strong> {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse email</label>
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autofocus 
                                       autocomplete="username"
                                       placeholder="exemple@mgs.mg">
                            </div>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password"
                                       placeholder="••••••••">
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                            <label class="form-check-label" for="remember_me">
                                Se souvenir de moi
                            </label>
                        </div>

                        <button type="submit" class="btn btn-login">
                            <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                        </button>

                        @if (Route::has('password.request'))
                            <div class="text-center mt-3">
                                <a href="{{ route('password.request') }}" class="text-decoration-none" style="color: #667eea;">
                                    <i class="fas fa-question-circle me-1"></i>Mot de passe oublié ?
                                </a>
                            </div>
                        @endif

                        @if (Route::has('register'))
                            <div class="divider">
                                <span>ou</span>
                            </div>

                            <div class="text-center">
                                <p class="mb-2">Vous n'avez pas encore de compte ?</p>
                                <a href="{{ route('register') }}" class="btn btn-outline-secondary" style="border-radius: 10px; border-width: 2px;">
                                    <i class="fas fa-user-plus me-2"></i>Créer un compte
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
