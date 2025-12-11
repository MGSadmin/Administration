@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-file-alt text-primary"></i> Mes Documents
        </h1>
        <div>
            @if(auth()->user()->hasRole(['RH', 'Ressources Humaines', 'Admin']))
                <a href="{{ route('documents.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Ajouter un Document
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-folder-open"></i> Documents Disponibles</h5>
        </div>
        <div class="card-body">
            @if($documents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                @if(auth()->user()->hasRole(['RH', 'Ressources Humaines', 'Direction', 'Admin']))
                                <th>Employé</th>
                                @endif
                                <th>Type</th>
                                <th>Titre</th>
                                <th>Date d'émission</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $document)
                            <tr>
                                @if(auth()->user()->hasRole(['RH', 'Ressources Humaines', 'Direction', 'Admin']))
                                <td>{{ $document->organizationMember->display_name }}</td>
                                @endif
                                <td><span class="badge bg-info">{{ $document->type_libelle }}</span></td>
                                <td>{{ $document->titre }}</td>
                                <td>{{ $document->date_emission->format('d/m/Y') }}</td>
                                <td>
                                    @if($document->statut === 'actif')
                                        <span class="badge bg-success">Actif</span>
                                    @elseif($document->statut === 'archive')
                                        <span class="badge bg-secondary">Archivé</span>
                                    @elseif($document->statut === 'en_attente')
                                        <span class="badge bg-warning">En attente</span>
                                    @else
                                        <span class="badge bg-danger">Périmé</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('documents.show', $document) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($document->fichier !== 'pending')
                                    <a href="{{ route('documents.download', $document) }}" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $documents->links() }}
                </div>
            @else
                <p class="text-muted text-center py-4">
                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                    Aucun document disponible.
                </p>
            @endif
        </div>
    </div>

    @if(!auth()->user()->hasRole(['RH', 'Ressources Humaines', 'Admin']))
    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-file-medical"></i> Demander un Document</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('documents.request') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Type de Document <span class="text-danger">*</span></label>
                        <select name="type_document" class="form-select" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="attestation_travail">Attestation de travail</option>
                            <option value="certificat_emploi">Certificat d'emploi</option>
                            <option value="bulletin_paie">Bulletin de paie</option>
                            <option value="attestation_salaire">Attestation de salaire</option>
                            <option value="etat_conges">État des congés</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Précisions</label>
                        <input type="text" name="description" class="form-control" 
                               placeholder="Ex: Bulletin du mois de novembre">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Envoyer la Demande
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
