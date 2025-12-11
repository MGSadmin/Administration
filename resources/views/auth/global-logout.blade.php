<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déconnexion en cours...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .logout-container {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            max-width: 500px;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <h1>Déconnexion en cours</h1>
        <div class="spinner"></div>
        <p id="status">Déconnexion de tous les sites...</p>
    </div>

    <script>
        // Liste des sites à déconnecter
        const sites = [
            '{{ config("app_urls.apps.commercial.url") }}/logout-from-sso?next=' + encodeURIComponent('{{ config("app_urls.apps.administration.url") }}/auth/continue-logout?step=2'),
            '{{ config("app_urls.apps.gestion-dossier.url") }}/logout-from-sso?next=' + encodeURIComponent('{{ config("app_urls.apps.administration.url") }}/auth/continue-logout?step=3')
        ];

        const step = '{{ $step ?? "1" }}';

        if (step === '1') {
            // Étape 1: Déconnecter commercial
            document.getElementById('status').textContent = 'Déconnexion du site Commercial...';
            setTimeout(() => {
                window.location.href = sites[0];
            }, 1000);
        } else if (step === '2') {
            // Étape 2: Déconnecter debours
            document.getElementById('status').textContent = 'Déconnexion du site Débours...';
            setTimeout(() => {
                window.location.href = sites[1];
            }, 1000);
        } else if (step === '3') {
            // Étape 3: Déconnexion finale d'administration
            document.getElementById('status').textContent = 'Finalisation de la déconnexion...';
            
            // Faire une requête AJAX pour déconnecter administration
            fetch('{{ route("auth.final-logout") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('status').textContent = 'Déconnexion réussie !';
                setTimeout(() => {
                    window.location.href = '{{ route("auth.login") }}';
                }, 1000);
            })
            .catch(error => {
                console.error('Erreur:', error);
                window.location.href = '{{ route("auth.login") }}';
            });
        }
    </script>
</body>
</html>
