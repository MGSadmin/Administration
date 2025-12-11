@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-file-alt"></i> Détails du Document
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h4>{{ $document->titre }}</h4>
                    
                    <div class="mb-3">
                        <span class="badge bg-info fs-6">{{ $document->type_libelle }}</span>
                        @if($document->statut === 'actif')
                            <span class="badge bg-success fs-6">Actif</span>
                        @elseif($document->statut === 'archive')
                            <span class="badge bg-secondary fs-6">Archivé</span>
                        @elseif($document->statut === 'en_attente')
                            <span class="badge bg-warning fs-6">En attente</span>
                        @else
                            <span class="badge bg-danger fs-6">Périmé</span>
                        @endif
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Employé:</strong>
                            <p>{{ $document->organizationMember->display_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Poste:</strong>
                            <p>{{ $document->organizationMember->position->title }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Date d'émission:</strong>
                            <p>{{ $document->date_emission->format('d/m/Y') }}</p>
                        </div>
                        @if($document->date_validite)
                        <div class="col-md-6">
                            <strong>Date de validité:</strong>
                            <p>{{ $document->date_validite->format('d/m/Y') }}</p>
                        </div>
                        @endif
                    </div>

                    @if($document->description)
                    <div class="mb-3">
                        <strong>Description:</strong>
                        <p class="border p-3 bg-light">{{ $document->description }}</p>
                    </div>
                    @endif

                    <div class="mb-3">
                        <strong>Créé par:</strong>
                        <p>{{ $document->createdBy->name }} le {{ $document->created_at->format('d/m/Y à H:i') }}</p>
                    </div>

                    @if($document->accessible_employe)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Ce document est accessible à l'employé
                    </div>
                    @endif

                    @if($document->fichier && $document->fichier !== 'pending')
                    <div class="d-grid gap-2">
                        <a href="{{ route('documents.download', $document) }}" class="btn btn-success btn-lg">
                            <i class="fas fa-download"></i> Télécharger le Document
                        </a>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-hourglass-half"></i> Document en attente de génération
                    </div>
                    @endif
                </div>

                <div class="col-md-4">
                    @if(auth()->user()->hasRole(['RH', 'Ressources Humaines', 'Admin']))
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0">Actions RH</h6>
                        </div>
                        <div class="card-body">
                            @if($document->statut === 'actif')
                            <form action="{{ route('documents.archive', $document) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-secondary w-100"
                                        onclick="return confirm('Archiver ce document ?')">
                                    <i class="fas fa-archive"></i> Archiver
                                </button>
                            </form>
                            @endif

                            <form action="{{ route('documents.destroy', $document) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100"
                                        onclick="return confirm('Supprimer définitivement ce document ?')">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
