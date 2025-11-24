<!DOCTYPE html>
<html>
<head>
    <title>Test DemandeFourniture</title>
</head>
<body>
    <h1>Test d'affichage</h1>
    
    <h2>Données JSON:</h2>
    <pre>{{ json_encode($demandeFourniture, JSON_PRETTY_PRINT) }}</pre>
    
    <h2>Attributs:</h2>
    <pre>{{ json_encode($demandeFourniture->getAttributes(), JSON_PRETTY_PRINT) }}</pre>
    
    <h2>Champs individuels:</h2>
    <p><strong>ID:</strong> {{ $demandeFourniture->id ?? 'NULL' }}</p>
    <p><strong>Numéro:</strong> {{ $demandeFourniture->numero_demande ?? 'NULL' }}</p>
    <p><strong>Objet:</strong> {{ $demandeFourniture->objet ?? 'NULL' }}</p>
    <p><strong>Designation:</strong> {{ $demandeFourniture->designation ?? 'NULL' }}</p>
    <p><strong>Description:</strong> {{ $demandeFourniture->description ?? 'NULL' }}</p>
    <p><strong>Type:</strong> {{ $demandeFourniture->type_fourniture ?? 'NULL' }}</p>
    <p><strong>Quantité:</strong> {{ $demandeFourniture->quantite ?? 'NULL' }}</p>
    
    <h2>Isset checks:</h2>
    <p>isset ID: {{ isset($demandeFourniture->id) ? 'OUI' : 'NON' }}</p>
    <p>isset objet: {{ isset($demandeFourniture->objet) ? 'OUI' : 'NON' }}</p>
</body>
</html>
