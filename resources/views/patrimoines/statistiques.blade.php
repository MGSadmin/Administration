@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="h3"><i class="fas fa-chart-bar me-2"></i>Statistiques - Patrimoines</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('patrimoines.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <!-- Statistiques générales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Total</h6>
                            <h2 class="mb-0">{{ $stats['total'] }}</h2>
                        </div>
                        <i class="fas fa-archive fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Disponibles</h6>
                            <h2 class="mb-0">{{ $stats['disponibles'] }}</h2>
                        </div>
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">En Utilisation</h6>
                            <h2 class="mb-0">{{ $stats['en_utilisation'] }}</h2>
                        </div>
                        <i class="fas fa-user fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Sous Garantie</h6>
                            <h2 class="mb-0">{{ $stats['sous_garantie'] }}</h2>
                        </div>
                        <i class="fas fa-shield-alt fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase">En Maintenance</h6>
                    <h2 class="text-warning">{{ $stats['en_maintenance'] }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase">Réformés</h6>
                    <h2 class="text-dark">{{ $stats['reformes'] }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Valeur Totale</h6>
                            <h2 class="mb-0">{{ number_format($stats['valeur_totale'], 2) }} Ar</h2>
                        </div>
                        <i class="fas fa-money-bill-wave fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Répartition par catégorie -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-pie-chart me-2"></i>Répartition par Catégorie</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Catégorie</th>
                                    <th class="text-end">Nombre</th>
                                    <th class="text-end">Pourcentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $labels = \App\Models\Patrimoine::getCategorieLabels();
                                @endphp
                                @foreach($stats['par_categorie'] as $categorie => $count)
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">{{ $labels[$categorie] ?? $categorie }}</span>
                                        </td>
                                        <td class="text-end"><strong>{{ $count }}</strong></td>
                                        <td class="text-end">
                                            @php
                                                $percentage = $stats['total'] > 0 ? ($count / $stats['total']) * 100 : 0;
                                            @endphp
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-primary" role="progressbar" 
                                                     style="width: {{ $percentage }}%">
                                                    {{ number_format($percentage, 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @if($stats['par_categorie']->isEmpty())
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Aucune donnée</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Résumé par Statut</h5>
                </div>
                <div class="card-body">
                    @php
                        $statutData = [
                            ['label' => 'Disponible', 'count' => $stats['disponibles'], 'color' => 'success'],
                            ['label' => 'En Utilisation', 'count' => $stats['en_utilisation'], 'color' => 'primary'],
                            ['label' => 'En Maintenance', 'count' => $stats['en_maintenance'], 'color' => 'warning'],
                            ['label' => 'Réformé', 'count' => $stats['reformes'], 'color' => 'dark'],
                        ];
                    @endphp
                    
                    @foreach($statutData as $data)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ $data['label'] }}</span>
                                <strong>{{ $data['count'] }}</strong>
                            </div>
                            @php
                                $percentage = $stats['total'] > 0 ? ($data['count'] / $stats['total']) * 100 : 0;
                            @endphp
                            <div class="progress">
                                <div class="progress-bar bg-{{ $data['color'] }}" 
                                     role="progressbar" 
                                     style="width: {{ $percentage }}%"
                                     aria-valuenow="{{ $data['count'] }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="{{ $stats['total'] }}">
                                    {{ number_format($percentage, 1) }}%
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
