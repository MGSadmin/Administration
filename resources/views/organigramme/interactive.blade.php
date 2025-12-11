@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <i class="fas fa-sitemap me-2"></i>Organigramme de l'entreprise
                </h1>
                <div class="btn-group">
                    <a href="{{ route('organigramme.members.index') }}" class="btn btn-primary">
                        <i class="fas fa-users me-2"></i>Gestion des Membres
                    </a>
                    <a href="{{ route('organigramme.members.vacant') }}" class="btn btn-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>Postes Vacants
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Postes</h6>
                            <h3 class="mb-0" id="totalPositions">-</h3>
                        </div>
                        <i class="fas fa-briefcase fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Membres Actifs</h6>
                            <h3 class="mb-0" id="activeMembers">-</h3>
                        </div>
                        <i class="fas fa-user-check fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Postes Vacants</h6>
                            <h3 class="mb-0" id="vacantPositions">-</h3>
                        </div>
                        <i class="fas fa-user-times fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Départements</h6>
                            <h3 class="mb-0" id="totalDepartments">-</h3>
                        </div>
                        <i class="fas fa-building fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Organigramme -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Structure Hiérarchique</h5>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-primary" id="zoomIn">
                    <i class="fas fa-search-plus"></i>
                </button>
                <button class="btn btn-outline-primary" id="zoomOut">
                    <i class="fas fa-search-minus"></i>
                </button>
                <button class="btn btn-outline-primary" id="resetZoom">
                    <i class="fas fa-redo"></i> Réinitialiser
                </button>
            </div>
        </div>
        <div class="card-body p-0" style="min-height: 600px; overflow: auto; background: #f8f9fa;">
            <div id="organigramme-container" style="width: 100%; height: 100%;"></div>
        </div>
    </div>

    <!-- Légende -->
    <div class="card mt-4">
        <div class="card-header">
            <h6 class="mb-0">Légende</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="badge bg-success me-2" style="width: 20px; height: 20px;"></div>
                        <span>Poste occupé (ACTIVE)</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="badge bg-warning me-2" style="width: 20px; height: 20px;"></div>
                        <span>Poste vacant (VACANT)</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="badge bg-info me-2" style="width: 20px; height: 20px;"></div>
                        <span>Poste en intérim (INTERIM)</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="badge bg-danger me-2" style="width: 20px; height: 20px;"></div>
                        <span>En congé</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Détails Membre -->
<div class="modal fade" id="memberModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="memberModalTitle">Détails</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="memberModalBody">
                <!-- Contenu chargé dynamiquement -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <a href="#" id="memberDetailsLink" class="btn btn-primary">Voir la fiche complète</a>
            </div>
        </div>
    </div>
</div>

<style>
.organigramme-box {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 15px;
    min-width: 200px;
    max-width: 250px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.organigramme-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.organigramme-box.status-ACTIVE {
    border-color: #10b981;
    background: linear-gradient(to bottom, #ffffff 0%, #f0fdf4 100%);
}

.organigramme-box.status-VACANT {
    border-color: #f59e0b;
    background: linear-gradient(to bottom, #ffffff 0%, #fffbeb 100%);
}

.organigramme-box.status-INTERIM {
    border-color: #3b82f6;
    background: linear-gradient(to bottom, #ffffff 0%, #eff6ff 100%);
}

.organigramme-box.on-leave {
    border-color: #ef4444;
    background: linear-gradient(to bottom, #ffffff 0%, #fef2f2 100%);
}

.organigramme-level-1 {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #667eea;
}

.organigramme-level-1 .position-title,
.organigramme-level-1 .member-name,
.organigramme-level-1 .department-name {
    color: white !important;
}

.position-title {
    font-weight: 600;
    font-size: 14px;
    color: #1f2937;
    margin-bottom: 8px;
}

.member-name {
    font-size: 13px;
    color: #4b5563;
    margin-bottom: 4px;
}

.department-name {
    font-size: 11px;
    color: #6b7280;
    display: inline-block;
    padding: 2px 8px;
    border-radius: 4px;
    margin-top: 5px;
}

.status-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 3px;
    font-weight: 600;
}

.level-indicator {
    position: absolute;
    top: -8px;
    left: 10px;
    background: white;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 600;
    border: 1px solid #e5e7eb;
}

#organigramme-container {
    padding: 40px;
    overflow: auto;
}

.organigramme-tree {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 40px;
}

.organigramme-children {
    display: flex;
    flex-direction: row;
    justify-content: center;
    gap: 30px;
    margin-top: 40px;
    flex-wrap: wrap;
}

.organigramme-node {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}

.connection-line {
    position: absolute;
    background: #d1d5db;
}

.vertical-line {
    width: 2px;
    height: 40px;
    top: -40px;
    left: 50%;
    transform: translateX(-50%);
}

.horizontal-line {
    height: 2px;
}
</style>

<script>
let organigrammeData = [];
let currentZoom = 1;

document.addEventListener('DOMContentLoaded', function() {
    loadOrganigrammeData();
    
    // Zoom controls
    document.getElementById('zoomIn')?.addEventListener('click', () => zoom(0.1));
    document.getElementById('zoomOut')?.addEventListener('click', () => zoom(-0.1));
    document.getElementById('resetZoom')?.addEventListener('click', () => resetZoom());
});

function zoom(delta) {
    currentZoom += delta;
    currentZoom = Math.max(0.5, Math.min(2, currentZoom));
    document.getElementById('organigramme-container').style.transform = `scale(${currentZoom})`;
}

function resetZoom() {
    currentZoom = 1;
    document.getElementById('organigramme-container').style.transform = 'scale(1)';
}

async function loadOrganigrammeData() {
    try {
        const response = await fetch('{{ route("organigramme.data") }}');
        const data = await response.json();
        organigrammeData = data;
        
        updateStatistics(data);
        renderOrganigramme(data);
    } catch (error) {
        console.error('Erreur chargement organigramme:', error);
        document.getElementById('organigramme-container').innerHTML = 
            '<div class="alert alert-danger m-4">Erreur de chargement des données</div>';
    }
}

function updateStatistics(data) {
    const departments = new Set(data.map(d => d.department)).size;
    const active = data.filter(d => d.status === 'ACTIVE').length;
    const vacant = data.filter(d => d.status === 'VACANT' || d.name === 'VACANT').length;
    
    document.getElementById('totalPositions').textContent = data.length;
    document.getElementById('activeMembers').textContent = active;
    document.getElementById('vacantPositions').textContent = vacant;
    document.getElementById('totalDepartments').textContent = departments;
}

function renderOrganigramme(data) {
    const container = document.getElementById('organigramme-container');
    
    // Construire l'arbre hiérarchique
    const tree = buildTree(data);
    
    // Générer le HTML
    const html = generateTreeHTML(tree);
    container.innerHTML = html;
    
    // Ajouter les event listeners
    attachEventListeners();
}

function buildTree(data) {
    // Trouver la racine (niveau 1, pas de parent)
    const root = data.find(d => d.level === 1 && !d.parentId);
    
    if (!root) {
        return { children: data.filter(d => !d.parentId).map(node => ({
            ...node,
            children: getChildren(node.id, data)
        }))};
    }
    
    return {
        ...root,
        children: getChildren(root.id, data)
    };
}

function getChildren(parentId, data) {
    const children = data.filter(d => d.parentId === parentId);
    return children.map(child => ({
        ...child,
        children: getChildren(child.id, data)
    }));
}

function generateTreeHTML(node, isRoot = true) {
    if (!node) return '';
    
    if (node.children && !node.id) {
        // Cas spécial: plusieurs racines
        return `
            <div class="organigramme-tree">
                <div class="organigramme-children">
                    ${node.children.map(child => generateTreeHTML(child, true)).join('')}
                </div>
            </div>
        `;
    }
    
    const statusClass = node.status ? `status-${node.status}` : '';
    const levelClass = node.level === 1 ? 'organigramme-level-1' : '';
    const onLeaveClass = node.onLeave ? 'on-leave' : '';
    
    const statusBadge = getStatusBadge(node);
    const departmentBadge = getDepartmentBadge(node);
    
    let html = `
        <div class="organigramme-node">
            ${!isRoot ? '<div class="connection-line vertical-line"></div>' : ''}
            <div class="organigramme-box ${statusClass} ${levelClass} ${onLeaveClass}" 
                 data-id="${node.id}"
                 data-name="${node.name || 'VACANT'}"
                 data-title="${node.title || ''}"
                 onclick="showMemberDetails(${node.id})">
                <div class="level-indicator">N${node.level || 1}</div>
                ${statusBadge}
                <div class="position-title">${node.title || 'Poste non défini'}</div>
                <div class="member-name">
                    ${node.name === 'VACANT' ? 
                        '<span class="text-warning"><i class="fas fa-user-slash me-1"></i>VACANT</span>' : 
                        `<i class="fas fa-user me-1"></i>${node.name || 'Non assigné'}`
                    }
                </div>
                ${departmentBadge}
                ${node.email ? `<div class="mt-2"><small><i class="fas fa-envelope me-1"></i>${node.email}</small></div>` : ''}
                ${node.onLeave ? '<div class="mt-1"><span class="badge bg-danger">En congé</span></div>' : ''}
            </div>
            
            ${node.children && node.children.length > 0 ? `
                <div class="organigramme-children">
                    ${node.children.map(child => generateTreeHTML(child, false)).join('')}
                </div>
            ` : ''}
        </div>
    `;
    
    return html;
}

function getStatusBadge(node) {
    const statusColors = {
        'ACTIVE': 'success',
        'VACANT': 'warning',
        'INTERIM': 'info',
        'DEMISSION': 'secondary',
        'LICENCIE': 'danger',
        'RETRAITE': 'dark'
    };
    
    const color = statusColors[node.status] || 'secondary';
    return `<span class="status-badge badge bg-${color}">${node.status || 'N/A'}</span>`;
}

function getDepartmentBadge(node) {
    if (!node.department) return '';
    
    const bgColor = node.departmentColor || '#3b82f6';
    return `<span class="department-name" style="background-color: ${bgColor}20; color: ${bgColor}; border: 1px solid ${bgColor}40;">
        ${node.department}
    </span>`;
}

function attachEventListeners() {
    // Les événements sont déjà attachés via onclick dans le HTML
}

function showMemberDetails(id) {
    const member = organigrammeData.find(m => m.id === id);
    if (!member) return;
    
    const modal = new bootstrap.Modal(document.getElementById('memberModal'));
    document.getElementById('memberModalTitle').textContent = member.title || 'Détails';
    
    const statusColors = {
        'ACTIVE': 'success',
        'VACANT': 'warning',
        'INTERIM': 'info',
        'DEMISSION': 'secondary',
        'LICENCIE': 'danger',
        'RETRAITE': 'dark'
    };
    
    const statusColor = statusColors[member.status] || 'secondary';
    
    let html = `
        <div class="row">
            <div class="col-md-12">
                <h5>${member.title}</h5>
                <p class="text-muted mb-3">${member.department}</p>
                
                <div class="mb-3">
                    <strong>Nom:</strong> 
                    ${member.name === 'VACANT' ? 
                        '<span class="badge bg-warning">VACANT</span>' : 
                        member.name
                    }
                </div>
                
                <div class="mb-3">
                    <strong>Statut:</strong> 
                    <span class="badge bg-${statusColor}">${member.status || 'N/A'}</span>
                </div>
                
                ${member.email ? `<div class="mb-3"><strong>Email:</strong> ${member.email}</div>` : ''}
                ${member.phone ? `<div class="mb-3"><strong>Téléphone:</strong> ${member.phone}</div>` : ''}
                ${member.description ? `<div class="mb-3"><strong>Description:</strong><br>${member.description}</div>` : ''}
                ${member.responsibilities ? `<div class="mb-3"><strong>Responsabilités:</strong><br>${member.responsibilities}</div>` : ''}
                ${member.onLeave ? '<div class="alert alert-danger mt-3"><i class="fas fa-exclamation-triangle me-2"></i>Cette personne est actuellement en congé</div>' : ''}
            </div>
        </div>
    `;
    
    document.getElementById('memberModalBody').innerHTML = html;
    
    // Mettre à jour le lien vers la fiche complète
    if (member.status !== 'VACANT' && member.name !== 'VACANT') {
        const link = document.getElementById('memberDetailsLink');
        // Trouver l'organization_member_id depuis les données
        link.href = `/organigramme/members/${id}`;
        link.style.display = 'inline-block';
    } else {
        document.getElementById('memberDetailsLink').style.display = 'none';
    }
    
    modal.show();
}
</script>
@endsection