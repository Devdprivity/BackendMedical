@extends('layouts.app')

@section('title', 'Cirugías - DrOrganiza')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Cirugías</h1>
        <p class="page-subtitle">Programación y gestión de procedimientos quirúrgicos</p>
    </div>
    <a href="{{ route('surgeries.create') }}" class="btn btn-primary">
        <i class="fas fa-procedures"></i>
        Nueva Cirugía
    </a>
</div>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-procedures"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Programadas</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="scheduledSurgeries">-</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--success); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Completadas</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="completedSurgeries">-</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--warning); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">En Proceso</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="inProgressSurgeries">-</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--accent); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Urgentes</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="urgentSurgeries">-</div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 1fr auto auto auto auto auto; gap: 1rem; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Buscar cirugías</label>
                <div style="position: relative;">
                    <input type="text" class="form-control" placeholder="Buscar por paciente, tipo o doctor..." id="searchInput">
                    <i class="fas fa-search" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: var(--gray-400);"></i>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Fecha</label>
                <input type="date" class="form-control" id="dateFilter" value="{{ date('Y-m-d') }}">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Estado</label>
                <select class="form-control" id="statusFilter">
                    <option value="">Todos los estados</option>
                    <option value="scheduled">Programada</option>
                    <option value="in_progress">En proceso</option>
                    <option value="completed">Completada</option>
                    <option value="cancelled">Cancelada</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Prioridad</label>
                <select class="form-control" id="priorityFilter">
                    <option value="">Todas las prioridades</option>
                    <option value="low">Baja</option>
                    <option value="medium">Media</option>
                    <option value="high">Alta</option>
                    <option value="urgent">Urgente</option>
                </select>
            </div>
            
            <button class="btn btn-outline" onclick="clearFilters()">
                <i class="fas fa-times"></i>
                Limpiar
            </button>
            
            <button class="btn btn-secondary" onclick="exportSurgeries()">
                <i class="fas fa-download"></i>
                Exportar
            </button>
        </div>
    </div>
</div>

<!-- Surgeries Table -->
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title">Programación Quirúrgica</h3>
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
        <div id="surgeriesTableContainer">
            <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                <p>Cargando cirugías...</p>
            </div>
        </div>
    </div>
</div>

<!-- Pagination -->
<div id="paginationContainer" style="margin-top: 2rem;"></div>
@endsection

@push('styles')
<style>
.surgeries-table {
    width: 100%;
    border-collapse: collapse;
}

.surgeries-table th,
.surgeries-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--gray-200);
    vertical-align: middle;
}

.surgeries-table th {
    font-weight: 600;
    color: var(--dark);
    background: var(--gray-100);
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.surgeries-table tr:hover {
    background: var(--gray-100);
}

.surgery-datetime {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0.5rem;
    background: var(--gray-100);
    border-radius: 8px;
    min-width: 100px;
}

.datetime-main {
    font-weight: 600;
    font-size: 0.95rem;
    color: var(--dark);
}

.datetime-sub {
    font-size: 0.75rem;
    color: var(--gray-500);
    text-transform: uppercase;
}

.patient-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.patient-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: white;
    font-size: 14px;
}

.patient-details h4 {
    margin: 0 0 0.25rem 0;
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--dark);
}

.patient-details p {
    margin: 0;
    font-size: 0.8rem;
    color: var(--gray-500);
}

.surgery-type-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: rgba(0, 174, 239, 0.1);
    color: var(--secondary);
}

.priority-low {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.priority-medium {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.priority-high {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.priority-urgent {
    background: rgba(255, 107, 107, 0.15);
    color: #DC2626;
    animation: pulse 2s infinite;
}

.status-scheduled {
    background: rgba(0, 174, 239, 0.1);
    color: var(--secondary);
}

.status-in-progress {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.status-completed {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.status-cancelled {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.doctor-team {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.doctor-main {
    font-weight: 500;
    color: var(--dark);
    font-size: 0.9rem;
}

.doctor-assistant {
    font-size: 0.8rem;
    color: var(--gray-500);
}

.duration-cell {
    text-align: center;
    font-weight: 500;
    color: var(--primary);
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

@media (max-width: 768px) {
    .surgeries-table,
    .surgeries-table tbody,
    .surgeries-table tr,
    .surgeries-table td {
        display: block;
    }
    
    .surgeries-table thead {
        display: none;
    }
    
    .surgeries-table tr {
        margin-bottom: 1rem;
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        padding: 1rem;
    }
    
    .surgeries-table td {
        border: none;
        padding: 0.5rem 0;
        display: flex;
        justify-content: space-between;
    }
    
    .surgeries-table td:before {
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
    loadSurgeries();
    loadSurgeriesStats();
    
    // Setup event listeners
    document.getElementById('searchInput').addEventListener('input', debounce(handleSearch, 300));
    document.getElementById('dateFilter').addEventListener('change', handleDateFilter);
    document.getElementById('statusFilter').addEventListener('change', handleStatusFilter);
    document.getElementById('priorityFilter').addEventListener('change', handlePriorityFilter);
    document.getElementById('perPageSelect').addEventListener('change', handlePerPageChange);
});

async function loadSurgeries(page = 1) {
    try {
        const params = new URLSearchParams({
            page: page,
            per_page: document.getElementById('perPageSelect').value,
            ...currentFilters
        });
        
        const response = await fetch(`/api/surgeries?${params}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const data = await response.json();
            // Handle Laravel pagination format
            const surgeriesData = data.data?.data || data.data || [];
            renderSurgeriesTable(surgeriesData);
            renderPagination(data.data || data);
        } else {
            throw new Error('Error al cargar cirugías');
        }
    } catch (error) {
        console.error('Error:', error);
        showErrorState();
    }
}

async function loadSurgeriesStats() {
    try {
        const response = await fetch('/api/surgeries/stats', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const stats = await response.json();
            document.getElementById('scheduledSurgeries').textContent = stats.scheduled || '0';
            document.getElementById('completedSurgeries').textContent = stats.completed || '0';
            document.getElementById('inProgressSurgeries').textContent = stats.in_progress || '0';
            document.getElementById('urgentSurgeries').textContent = stats.urgent || '0';
        } else {
            // Fallback to default values
            document.getElementById('scheduledSurgeries').textContent = '0';
            document.getElementById('completedSurgeries').textContent = '0';
            document.getElementById('inProgressSurgeries').textContent = '0';
            document.getElementById('urgentSurgeries').textContent = '0';
        }
    } catch (error) {
        console.error('Error loading stats:', error);
        // Fallback to default values
        document.getElementById('scheduledSurgeries').textContent = '0';
        document.getElementById('completedSurgeries').textContent = '0';
        document.getElementById('inProgressSurgeries').textContent = '0';
        document.getElementById('urgentSurgeries').textContent = '0';
    }
}

function renderSurgeriesTable(surgeries) {
    const container = document.getElementById('surgeriesTableContainer');
    
    if (surgeries.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
                <i class="fas fa-procedures" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                <h3 style="margin-bottom: 0.5rem;">No se encontraron cirugías</h3>
                <p>No hay cirugías programadas o que coincidan con los filtros.</p>
                <a href="{{ route('surgeries.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                    <i class="fas fa-procedures"></i>
                    Programar Primera Cirugía
                </a>
            </div>
        `;
        return;
    }
    
    const table = `
        <table class="surgeries-table">
            <thead>
                <tr>
                    <th>Fecha y Hora</th>
                    <th>Paciente</th>
                    <th>Tipo de Cirugía</th>
                    <th>Cirujano Principal</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Duración Est.</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                ${surgeries.map(surgery => `
                    <tr>
                        <td data-label="Fecha y Hora">
                            <div class="surgery-datetime">
                                <div class="datetime-main">${formatTime(surgery.date_time)}</div>
                                <div class="datetime-sub">${formatDate(surgery.date_time)}</div>
                            </div>
                        </td>
                        <td data-label="Paciente">
                            <div class="patient-info">
                                <div class="patient-avatar" style="background: ${getPatientColor(surgery.patient_id)};">
                                    ${getPatientInitials(surgery.patient?.name)}
                                </div>
                                <div class="patient-details">
                                    <h4>${surgery.patient?.name || 'Paciente no asignado'}</h4>
                                    <p>ID: ${surgery.patient?.id || 'N/A'}</p>
                                </div>
                            </div>
                        </td>
                        <td data-label="Tipo de Cirugía">
                            <span class="surgery-type-badge">${surgery.surgery_type || 'No especificado'}</span>
                        </td>
                        <td data-label="Cirujano Principal">
                            <div class="doctor-team">
                                <div class="doctor-main">${surgery.main_surgeon?.name || 'No asignado'}</div>
                                <div class="doctor-specialty">${surgery.main_surgeon?.specialty || ''}</div>
                            </div>
                        </td>
                        <td data-label="Prioridad">
                            <span class="surgery-type-badge priority-medium">
                                <i class="fas fa-clock"></i>
                                Programada
                            </span>
                        </td>
                        <td data-label="Estado">
                            <span class="surgery-type-badge status-${surgery.status || 'scheduled'}">
                                <i class="fas ${getStatusIcon(surgery.status)}"></i>
                                ${formatStatus(surgery.status || 'scheduled')}
                            </span>
                        </td>
                        <td data-label="Duración Est." class="duration-cell">
                            ${surgery.estimated_duration || 120} min
                        </td>
                        <td data-label="Acciones">
                            <div class="actions-dropdown">
                                <button class="actions-btn" onclick="showSurgeryActions(${surgery.id})">
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
        pagination += `<button class="pagination-btn" onclick="loadSurgeries(${data.current_page - 1})">
            <i class="fas fa-chevron-left"></i>
        </button>`;
    }
    
    const startPage = Math.max(1, data.current_page - 2);
    const endPage = Math.min(data.last_page, data.current_page + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        pagination += `<button class="pagination-btn ${i === data.current_page ? 'active' : ''}" 
            onclick="loadSurgeries(${i})">${i}</button>`;
    }
    
    if (data.current_page < data.last_page) {
        pagination += `<button class="pagination-btn" onclick="loadSurgeries(${data.current_page + 1})">
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
    loadSurgeries(1);
}

function handleDateFilter(event) {
    const date = event.target.value;
    if (date) {
        currentFilters.date = date;
    } else {
        delete currentFilters.date;
    }
    loadSurgeries(1);
}

function handleStatusFilter(event) {
    const status = event.target.value;
    if (status) {
        currentFilters.status = status;
    } else {
        delete currentFilters.status;
    }
    loadSurgeries(1);
}

function handlePriorityFilter(event) {
    const priority = event.target.value;
    if (priority) {
        currentFilters.priority = priority;
    } else {
        delete currentFilters.priority;
    }
    loadSurgeries(1);
}

function handlePerPageChange() {
    loadSurgeries(1);
}

function clearFilters() {
    currentFilters = {};
    document.getElementById('searchInput').value = '';
    document.getElementById('dateFilter').value = '{{ date("Y-m-d") }}';
    document.getElementById('statusFilter').value = '';
    document.getElementById('priorityFilter').value = '';
    loadSurgeries(1);
}

function exportSurgeries() {
    alert('Función de exportación en desarrollo');
}

function showSurgeryActions(surgeryId) {
    const actions = [
        `Ver detalles: /surgeries/${surgeryId}`,
        `Editar información`,
        `Cambiar estado`,
        `Asignar equipo médico`,
        `Ver historial quirúrgico`,
        `Generar reporte pre-operatorio`
    ];
    alert(actions.join('\n'));
}

function showErrorState() {
    document.getElementById('surgeriesTableContainer').innerHTML = `
        <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 1rem; color: var(--warning);"></i>
            <h3 style="margin-bottom: 0.5rem;">Error al cargar cirugías</h3>
            <p>Hubo un problema al cargar la información. Por favor, intenta de nuevo.</p>
            <button class="btn btn-primary" onclick="loadSurgeries()" style="margin-top: 1rem;">
                <i class="fas fa-redo"></i>
                Reintentar
            </button>
        </div>
    `;
}

// Utility functions
function formatTime(dateTimeString) {
    if (!dateTimeString) return 'N/A';
    const date = new Date(dateTimeString);
    return date.toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatDate(dateTimeString) {
    if (!dateTimeString) return 'N/A';
    const date = new Date(dateTimeString);
    return date.toLocaleDateString('es-ES', { 
        day: '2-digit', 
        month: 'short',
        year: '2-digit'
    });
}

function getStatusIcon(status) {
    const icons = {
        'scheduled': 'fa-calendar',
        'in_progress': 'fa-spinner',
        'completed': 'fa-check',
        'cancelled': 'fa-times',
        'postponed': 'fa-clock'
    };
    return icons[status] || 'fa-question';
}

function formatStatus(status) {
    const statuses = {
        'scheduled': 'Programada',
        'in_progress': 'En proceso',
        'completed': 'Completada',
        'cancelled': 'Cancelada',
        'postponed': 'Pospuesta'
    };
    return statuses[status] || status;
}

function getPatientColor(patientId) {
    const colors = ['#667eea', '#764ba2', '#00AEEF', '#FF6B6B', '#10B981', '#F59E0B'];
    return colors[(patientId || 0) % colors.length];
}

function getPatientInitials(name) {
    if (!name) return 'P';
    const parts = name.trim().split(' ');
    if (parts.length >= 2) {
        return parts[0].charAt(0).toUpperCase() + parts[1].charAt(0).toUpperCase();
    }
    return parts[0].charAt(0).toUpperCase();
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>
@endpush 