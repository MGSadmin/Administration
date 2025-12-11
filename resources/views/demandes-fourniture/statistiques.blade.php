@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="h3"><i class="fas fa-chart-bar me-2"></i>Statistiques - Demandes de Fourniture</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('demandes-fourniture.index') }}" class="btn btn-secondary">
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
                            <h6 class="text-uppercase mb-1">Total Demandes</h6>
                            <h2 class="mb-0">{{ $stats['total'] }}</h2>
                        </div>
                        <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">En Attente</h6>
                            <h2 class="mb-0">{{ $stats['en_attente'] }}</h2>
                        </div>
                        <i class="fas fa-clock fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Validées</h6>
                            <h2 class="mb-0">{{ $stats['validees'] }}</h2>
                        </div>
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Urgentes</h6>
                            <h2 class="mb-0">{{ $stats['urgentes'] }}</h2>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase">Commandées</h6>
                    <h2 class="text-info">{{ $stats['commandees'] }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase">Livrées</h6>
                    <h2 class="text-success">{{ $stats['livrees'] }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase">Rejetées</h6>
                    <h2 class="text-danger">{{ $stats['rejetees'] }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white text-center">
                    <h6 class="text-uppercase mb-1">Taux de Validation</h6>
                    @php
                        $total = $stats['total'];
                        $tauxValidation = $total > 0 ? ($stats['validees'] / $total) * 100 : 0;
                    @endphp
                    <h2 class="mb-0">{{ number_format($tauxValidation, 1) }}%</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Budget -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Budget</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center mb-3">
                                <small class="text-muted">Budget Estimé Total</small>
                                <h4 class="text-primary">{{ number_format($stats['budget_estime_total'], 2) }} Ar</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center mb-3">
                                <small class="text-muted">Dépenses Réelles</small>
                                <h4 class="text-success">{{ number_format($stats['budget_total'], 2) }} Ar</h4>
                            </div>
                        </div>
                    </div>
                    @php
                        $ecart = $stats['budget_estime_total'] - $stats['budget_total'];
                        $ecartPourcent = $stats['budget_estime_total'] > 0 ? ($ecart / $stats['budget_estime_total']) * 100 : 0;
                    @endphp
                    <div class="text-center">
                        <small class="text-muted">Écart</small>
                        <h5 class="{{ $ecart >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $ecart >= 0 ? '+' : '' }}{{ number_format($ecart, 2) }} Ar
                            ({{ number_format($ecartPourcent, 1) }}%)
                        </h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-pie-chart me-2"></i>Répartition par Type</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th class="text-end">Nombre</th>
                                    <th class="text-end">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $labels = \App\Models\DemandeFourniture::getTypeFournitureLabels();
                                @endphp
                                @foreach($stats['par_type'] as $type => $count)
                                    <tr>
                                        <td><small>{{ $labels[$type] ?? $type }}</small></td>
                                        <td class="text-end"><strong>{{ $count }}</strong></td>
                                        <td class="text-end">
                                            @php
                                                $percentage = $stats['total'] > 0 ? ($count / $stats['total']) * 100 : 0;
                                            @endphp
                                            <small>{{ number_format($percentage, 1) }}%</small>
                                        </td>
                                    </tr>
                                @endforeach
                                @if($stats['par_type']->isEmpty())
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
    </div>

    <!-- Workflow -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>État du Workflow</h5>
                </div>
                <div class="card-body">
                    @php
                        $workflowData = [
                            ['label' => 'En Attente', 'count' => $stats['en_attente'], 'color' => 'warning'],
                            ['label' => 'Validées', 'count' => $stats['validees'], 'color' => 'success'],
                            ['label' => 'Rejetées', 'count' => $stats['rejetees'], 'color' => 'danger'],
                            ['label' => 'Commandées', 'count' => $stats['commandees'], 'color' => 'info'],
                            ['label' => 'Livrées', 'count' => $stats['livrees'], 'color' => 'primary'],
                        ];
                    @endphp
                    
                    <div class="row">
                        @foreach($workflowData as $data)
                            <div class="col-md mb-3">
                                <div class="text-center">
                                    <div class="mb-2">
                                        <span class="badge bg-{{ $data['color'] }} fs-6">{{ $data['label'] }}</span>
                                    </div>
                                    <h3 class="text-{{ $data['color'] }}">{{ $data['count'] }}</h3>
                                    @php
                                        $percentage = $stats['total'] > 0 ? ($data['count'] / $stats['total']) * 100 : 0;
                                    @endphp
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar bg-{{ $data['color'] }}" 
                                             role="progressbar" 
                                             style="width: {{ $percentage }}%">
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ number_format($percentage, 1) }}%</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
