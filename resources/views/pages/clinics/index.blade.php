@extends('layouts.app')

@section('title', 'Clínicas - MediCare Pro')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Clínicas</h1>
        <p class="page-subtitle">Gestión de centros médicos y sucursales</p>
    </div>
    <a href="{{ route('clinics.create') }}" class="btn btn-primary">
        <i class="fas fa-hospital"></i>
        Nueva Clínica
    </a>
</div>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-hospital"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Total Clínicas</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="totalClinics">-</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--success); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Activas</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="activeClinics">-</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--warning); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-user-md"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Total Doctores</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="totalDoctorsInClinics">-</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--secondary); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-bed"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Total Camas</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="totalBeds">-</div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 1fr auto auto auto auto auto; gap: 1rem; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Buscar clínicas</label>
                <div style="position: relative;">
                    <input type="text" class="form-control" placeholder="Buscar por nombre, dirección o teléfono..." id="searchInput">
                    <i class="fas fa-search" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: var(--gray-400);"></i>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Tipo</label>
                <select class="form-control" id="typeFilter">
                    <option value="">Todos los tipos</option>
                    <option value="hospital">Hospital</option>
                    <option value="clinica">Clínica</option>
                    <option value="consultorio">Consultorio</option>
                    <option value="centro_especializado">Centro Especializado</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Estado</label>
                <select class="form-control" id="statusFilter">
                    <option value="">Todos los estados</option>
                    <option value="active">Activa</option>
                    <option value="inactive">Inactiva</option>
                    <option value="maintenance">En mantenimiento</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Ciudad</label>
                <select class="form-control" id="cityFilter">
                    <option value="">Todas las ciudades</option>
                    <option value="bogota">Bogotá</option>
                    <option value="medellin">Medellín</option>
                    <option value="cali">Cali</option>
                    <option value="barranquilla">Barranquilla</option>
                    <option value="cartagena">Cartagena</option>
                </select>
            </div>
            
            <button class="btn btn-outline" onclick="clearFilters()">
                <i class="fas fa-times"></i>
                Limpiar
            </button>
            
            <button class="btn btn-secondary" onclick="exportClinics()">
                <i class="fas fa-download"></i>
                Exportar
            </button>
        </div>
    </div>
</div>

<!-- Clinics Table -->
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title">Red de Centros Médicos</h3>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <span style="font-size: 0.875rem; color: var(--gray-500);">Mostrando</span>
            <select class="form-control" style="width: auto; min-width: 80px;" id="perPageSelect">
                <option value="10">10</option>
                <option value="25" selected>25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span style="font-size: 0.875rem; color: var(--gray-500);">por página</span>
        </div>
    </div>
    <div class="card-body" style="padding: 0;">
        <div id="clinicsTableContainer">
            <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                <p>Cargando clínicas...</p>
            </div>
        </div>
    </div>
</div>

<!-- Pagination -->
<div id="paginationContainer" style="margin-top: 2rem;"></div>
@endsection

@push('styles')
<style>
.clinics-table {
    width: 100%;
    border-collapse: collapse;
}

.clinics-table th,
.clinics-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--gray-200);
    vertical-align: middle;
}

.clinics-table th {
    font-weight: 600;
    color: var(--dark);
    background: var(--gray-100);
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.clinics-table tr:hover {
    background: var(--gray-100);
}

.clinic-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.clinic-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.clinic-details h4 {
    margin: 0 0 0.25rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: var(--dark);
}

.clinic-details p {
    margin: 0;
    font-size: 0.875rem;
    color: var(--gray-500);
}

.type-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.type-hospital {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.type-clinica {
    background: rgba(0, 174, 239, 0.1);
    color: var(--secondary);
}

.type-consultorio {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.type-centro-especializado {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.address-info {
    max-width: 200px;
}

.address-main {
    font-weight: 500;
    color: var(--dark);
    margin-bottom: 0.25rem;
}

.address-details {
    font-size: 0.8rem;
    color: var(--gray-500);
}

.contact-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.contact-phone {
    font-weight: 500;
    color: var(--dark);
}

.contact-email {
    font-size: 0.8rem;
    color: var(--gray-500);
}

.capacity-info {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
}

.capacity-number {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary);
}

.capacity-label {
    font-size: 0.75rem;
    color: var(--gray-500);
    text-transform: uppercase;
}

.status-active {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.status-inactive {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.status-maintenance {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.rating-display {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.rating-stars {
    color: #FCD34D;
    font-size: 0.875rem;
}

.rating-number {
    font-size: 0.875rem;
    color: var(--gray-600);
    font-weight: 500;
}

@media (max-width: 768px) {
    .clinics-table,
    .clinics-table tbody,
    .clinics-table tr,
    .clinics-table td {
        display: block;
    }
    
    .clinics-table thead {
        display: none;
    }
    
    .clinics-table tr {
        margin-bottom: 1rem;
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        padding: 1rem;
    }
    
    .clinics-table td {
        border: none;
        padding: 0.5rem 0;
        display: flex;
        justify-content: space-between;
    }
    
    .clinics-table td:before {
        content: attr(data-label);
        font-weight: 600;
        color: var(--dark);
    }
}
</style>
@endpush

@push('scripts')
<script>
let currentPage = 1;
let currentFilters = {};

document.addEventListener('DOMContentLoaded', function() {
    loadClinics();
    loadClinicsStats();
    
    // Setup event listeners
    document.getElementById('searchInput').addEventListener('input', debounce(handleSearch, 300));
    document.getElementById('typeFilter').addEventListener('change', handleTypeFilter);
    document.getElementById('statusFilter').addEventListener('change', handleStatusFilter);
    document.getElementById('cityFilter').addEventListener('change', handleCityFilter);
    document.getElementById('perPageSelect').addEventListener('change', handlePerPageChange);
});

async function loadClinics(page = 1) {
    try {
        const params = new URLSearchParams({
            page: page,
            per_page: document.getElementById('perPageSelect').value,
            ...currentFilters
        });
        
        const response = await fetch(`/api/clinics?${params}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const data = await response.json();
            renderClinicsTable(data.data || []);
            renderPagination(data);
        } else {
            throw new Error('Error al cargar clínicas');
        }
    } catch (error) {
        console.error('Error:', error);
        showErrorState();
    }
}

async function loadClinicsStats() {
    try {
        // Simular estadísticas por ahora
        document.getElementById('totalClinics').textContent = '12';
        document.getElementById('activeClinics').textContent = '10';
        document.getElementById('totalDoctorsInClinics').textContent = '48';
        document.getElementById('totalBeds').textContent = '156';
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

function renderClinicsTable(clinics) {
    const container = document.getElementById('clinicsTableContainer');
    
    if (clinics.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
                <i class="fas fa-hospital" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                <h3 style="margin-bottom: 0.5rem;">No se encontraron clínicas</h3>
                <p>No hay clínicas registradas o que coincidan con los filtros.</p>
                <a href="{{ route('clinics.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                    <i class="fas fa-hospital"></i>
                    Registrar Primera Clínica
                </a>
            </div>
        `;
        return;
    }
    
    const table = `
        <table class="clinics-table">
            <thead>
                <tr>
                    <th>Clínica</th>
                    <th>Tipo</th>
                    <th>Dirección</th>
                    <th>Contacto</th>
                    <th>Capacidad</th>
                    <th>Estado</th>
                    <th>Calificación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                ${clinics.map(clinic => `
                    <tr>
                        <td data-label="Clínica">
                            <div class="clinic-info">
                                <div class="clinic-icon" style="background: ${getClinicColor(clinic.type)};">
                                    <i class="${getClinicIcon(clinic.type)}"></i>
                                </div>
                                <div class="clinic-details">
                                    <h4>${clinic.name || 'Clínica sin nombre'}</h4>
                                    <p>ID: ${clinic.code || clinic.id}</p>
                                </div>
                            </div>
                        </td>
                        <td data-label="Tipo">
                            <span class="type-badge type-${clinic.type || 'clinica'}">
                                ${formatType(clinic.type || 'clinica')}
                            </span>
                        </td>
                        <td data-label="Dirección">
                            <div class="address-info">
                                <div class="address-main">${clinic.address || 'Dirección no especificada'}</div>
                                <div class="address-details">${clinic.city || 'Ciudad'}, ${clinic.state || 'Estado'}</div>
                            </div>
                        </td>
                        <td data-label="Contacto">
                            <div class="contact-info">
                                <div class="contact-phone">${clinic.phone || 'Sin teléfono'}</div>
                                <div class="contact-email">${clinic.email || 'Sin email'}</div>
                            </div>
                        </td>
                        <td data-label="Capacidad">
                            <div class="capacity-info">
                                <div class="capacity-number">${clinic.bed_capacity || 0}</div>
                                <div class="capacity-label">Camas</div>
                            </div>
                        </td>
                        <td data-label="Estado">
                            <span class="type-badge status-${clinic.status || 'active'}">
                                ${formatStatus(clinic.status || 'active')}
                            </span>
                        </td>
                        <td data-label="Calificación">
                            <div class="rating-display">
                                <div class="rating-stars">
                                    ${generateStars(clinic.rating || 4.2)}
                                </div>
                                <div class="rating-number">(${clinic.rating || '4.2'})</div>
                            </div>
                        </td>
                        <td data-label="Acciones">
                            <div class="actions-dropdown">
                                <button class="actions-btn" onclick="showClinicActions(${clinic.id})">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
    
    container.innerHTML = table;
}

function renderPagination(data) {
    const container = document.getElementById('paginationContainer');
    
    if (!data.last_page || data.last_page <= 1) {
        container.innerHTML = '';
        return;
    }
    
    let pagination = '<div class="pagination">';
    
    if (data.current_page > 1) {
        pagination += `<button class="pagination-btn" onclick="loadClinics(${data.current_page - 1})">
            <i class="fas fa-chevron-left"></i>
        </button>`;
    }
    
    const startPage = Math.max(1, data.current_page - 2);
    const endPage = Math.min(data.last_page, data.current_page + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        pagination += `<button class="pagination-btn ${i === data.current_page ? 'active' : ''}" 
            onclick="loadClinics(${i})">${i}</button>`;
    }
    
    if (data.current_page < data.last_page) {
        pagination += `<button class="pagination-btn" onclick="loadClinics(${data.current_page + 1})">
            <i class="fas fa-chevron-right"></i>
        </button>`;
    }
    
    pagination += '</div>';
    container.innerHTML = pagination;
}

// Event Handlers
function handleSearch(event) {
    const searchTerm = event.target.value.trim();
    if (searchTerm) {
        currentFilters.search = searchTerm;
    } else {
        delete currentFilters.search;
    }
    loadClinics(1);
}

function handleTypeFilter(event) {
    const type = event.target.value;
    if (type) {
        currentFilters.type = type;
    } else {
        delete currentFilters.type;
    }
    loadClinics(1);
}

function handleStatusFilter(event) {
    const status = event.target.value;
    if (status) {
        currentFilters.status = status;
    } else {
        delete currentFilters.status;
    }
    loadClinics(1);
}

function handleCityFilter(event) {
    const city = event.target.value;
    if (city) {
        currentFilters.city = city;
    } else {
        delete currentFilters.city;
    }
    loadClinics(1);
}

function handlePerPageChange() {
    loadClinics(1);
}

function clearFilters() {
    currentFilters = {};
    document.getElementById('searchInput').value = '';
    document.getElementById('typeFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('cityFilter').value = '';
    loadClinics(1);
}

function exportClinics() {
    alert('Función de exportación en desarrollo');
}

function showClinicActions(clinicId) {
    const actions = [
        `Ver detalles: /clinics/${clinicId}`,
        `Editar información`,
        `Ver personal médico`,
        `Gestionar horarios`,
        `Ver estadísticas`,
        `Configurar servicios`,
        `Generar reporte`
    ];
    alert(actions.join('\n'));
}

function showErrorState() {
    document.getElementById('clinicsTableContainer').innerHTML = `
        <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 1rem; color: var(--warning);"></i>
            <h3 style="margin-bottom: 0.5rem;">Error al cargar clínicas</h3>
            <p>Hubo un problema al cargar la información. Por favor, intenta de nuevo.</p>
            <button class="btn btn-primary" onclick="loadClinics()" style="margin-top: 1rem;">
                <i class="fas fa-redo"></i>
                Reintentar
            </button>
        </div>
    `;
}

// Utility functions
function getClinicColor(type) {
    const colors = {
        'hospital': '#EF4444',
        'clinica': '#00AEEF',
        'consultorio': '#10B981',
        'centro_especializado': '#F59E0B'
    };
    return colors[type] || '#667eea';
}

function getClinicIcon(type) {
    const icons = {
        'hospital': 'fas fa-hospital',
        'clinica': 'fas fa-clinic-medical',
        'consultorio': 'fas fa-user-md',
        'centro_especializado': 'fas fa-hospital-alt'
    };
    return icons[type] || 'fas fa-hospital';
}

function formatType(type) {
    const types = {
        'hospital': 'Hospital',
        'clinica': 'Clínica',
        'consultorio': 'Consultorio',
        'centro_especializado': 'Centro Especializado'
    };
    return types[type] || type;
}

function formatStatus(status) {
    const statuses = {
        'active': 'Activa',
        'inactive': 'Inactiva',
        'maintenance': 'En mantenimiento'
    };
    return statuses[status] || status;
}

function generateStars(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 !== 0;
    let stars = '';
    
    for (let i = 0; i < fullStars; i++) {
        stars += '<i class="fas fa-star"></i>';
    }
    
    if (hasHalfStar) {
        stars += '<i class="fas fa-star-half-alt"></i>';
    }
    
    const emptyStars = 5 - Math.ceil(rating);
    for (let i = 0; i < emptyStars; i++) {
        stars += '<i class="far fa-star"></i>';
    }
    
    return stars;
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        setTimeout(later, wait);
    };
}
</script>
@endpush 