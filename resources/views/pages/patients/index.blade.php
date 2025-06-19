@extends('layouts.app')

@section('title', 'Pacientes - MediCare Pro')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Pacientes</h1>
        <p class="page-subtitle">Gestión integral de pacientes registrados</p>
    </div>
    <a href="{{ route('patients.create') }}" class="btn btn-primary">
        <i class="fas fa-user-plus"></i>
        Nuevo Paciente
    </a>
</div>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Total Pacientes</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="totalPatients">-</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--success); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-user-check"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Activos</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="activePatients">-</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--warning); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Con Citas Hoy</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="patientsWithAppointments">-</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--accent); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Con Alergias</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="patientsWithAllergies">-</div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 1fr auto auto auto; gap: 1rem; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Buscar pacientes</label>
                <div style="position: relative;">
                    <input type="text" class="form-control" placeholder="Buscar por nombre, email o teléfono..." id="searchInput">
                    <i class="fas fa-search" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: var(--gray-400);"></i>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Filtrar por</label>
                <select class="form-control" id="filterSelect">
                    <option value="">Todos los pacientes</option>
                    <option value="active">Pacientes activos</option>
                    <option value="with_allergies">Con alergias</option>
                    <option value="with_appointments">Con citas programadas</option>
                    <option value="recent">Registrados recientemente</option>
                </select>
            </div>
            
            <button class="btn btn-outline" onclick="clearFilters()">
                <i class="fas fa-times"></i>
                Limpiar
            </button>
            
            <button class="btn btn-secondary" onclick="exportPatients()">
                <i class="fas fa-download"></i>
                Exportar
            </button>
        </div>
    </div>
</div>

<!-- Patients Table -->
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title">Lista de Pacientes</h3>
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
        <div id="patientsTableContainer">
            <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                <p>Cargando pacientes...</p>
            </div>
        </div>
    </div>
</div>

<!-- Pagination -->
<div id="paginationContainer" style="margin-top: 2rem;"></div>
@endsection

@push('styles')
<style>
.patients-table {
    width: 100%;
    border-collapse: collapse;
}

.patients-table th,
.patients-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--gray-200);
    vertical-align: middle;
}

.patients-table th {
    font-weight: 600;
    color: var(--dark);
    background: var(--gray-100);
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.patients-table tr:hover {
    background: var(--gray-100);
}

.patient-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: white;
    font-size: 16px;
    margin-right: 1rem;
}

.patient-info {
    display: flex;
    align-items: center;
}

.patient-details h4 {
    margin: 0 0 0.25rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: var(--dark);
}

.patient-details p {
    margin: 0;
    font-size: 0.875rem;
    color: var(--gray-500);
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-active {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.status-inactive {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.actions-dropdown {
    position: relative;
}

.actions-btn {
    background: none;
    border: none;
    padding: 0.5rem;
    border-radius: 6px;
    cursor: pointer;
    color: var(--gray-500);
    transition: var(--transition);
}

.actions-btn:hover {
    background: var(--gray-100);
    color: var(--dark);
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
}

.pagination-btn {
    padding: 0.5rem 1rem;
    border: 1px solid var(--gray-300);
    background: white;
    color: var(--gray-600);
    border-radius: 6px;
    cursor: pointer;
    transition: var(--transition);
    text-decoration: none;
}

.pagination-btn:hover {
    background: var(--gray-100);
    border-color: var(--gray-400);
}

.pagination-btn.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

.pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .patients-table,
    .patients-table tbody,
    .patients-table tr,
    .patients-table td {
        display: block;
    }
    
    .patients-table thead {
        display: none;
    }
    
    .patients-table tr {
        margin-bottom: 1rem;
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        padding: 1rem;
    }
    
    .patients-table td {
        border: none;
        padding: 0.5rem 0;
        display: flex;
        justify-content: space-between;
    }
    
    .patients-table td:before {
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
    loadPatients();
    loadPatientsStats();
    
    // Setup event listeners
    document.getElementById('searchInput').addEventListener('input', debounce(handleSearch, 300));
    document.getElementById('filterSelect').addEventListener('change', handleFilter);
    document.getElementById('perPageSelect').addEventListener('change', handlePerPageChange);
});

async function loadPatients(page = 1) {
    try {
        const params = new URLSearchParams({
            page: page,
            per_page: document.getElementById('perPageSelect').value,
            ...currentFilters
        });
        
        const response = await fetch(`/api/patients?${params}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const data = await response.json();
            // Handle Laravel pagination format
            const patientsData = data.data?.data || data.data || [];
            renderPatientsTable(patientsData);
            renderPagination(data.data || data);
        } else {
            throw new Error('Error al cargar pacientes');
        }
    } catch (error) {
        console.error('Error:', error);
        showErrorState();
    }
}

async function loadPatientsStats() {
    try {
        const response = await fetch('/api/patients/stats', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const stats = await response.json();
            document.getElementById('totalPatients').textContent = stats.total || '0';
            document.getElementById('activePatients').textContent = stats.active || '0';
            document.getElementById('patientsWithAppointments').textContent = stats.with_appointments_today || '0';
            document.getElementById('patientsWithAllergies').textContent = stats.with_allergies || '0';
        } else {
            // Fallback to default values
            document.getElementById('totalPatients').textContent = '0';
            document.getElementById('activePatients').textContent = '0';
            document.getElementById('patientsWithAppointments').textContent = '0';
            document.getElementById('patientsWithAllergies').textContent = '0';
        }
    } catch (error) {
        console.error('Error loading stats:', error);
        // Fallback to default values
        document.getElementById('totalPatients').textContent = '0';
        document.getElementById('activePatients').textContent = '0';
        document.getElementById('patientsWithAppointments').textContent = '0';
        document.getElementById('patientsWithAllergies').textContent = '0';
    }
}

function renderPatientsTable(patients) {
    const container = document.getElementById('patientsTableContainer');
    
    if (patients.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
                <i class="fas fa-user-times" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                <h3 style="margin-bottom: 0.5rem;">No se encontraron pacientes</h3>
                <p>No hay pacientes registrados o que coincidan con los filtros.</p>
                <a href="{{ route('patients.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                    <i class="fas fa-user-plus"></i>
                    Registrar Primer Paciente
                </a>
            </div>
        `;
        return;
    }
    
    const table = `
        <table class="patients-table">
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Contacto</th>
                    <th>Edad</th>
                    <th>Tipo de Sangre</th>
                    <th>Estado</th>
                    <th>Última Cita</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                ${patients.map(patient => `
                    <tr>
                        <td data-label="Paciente">
                            <div class="patient-info">
                                <div class="patient-avatar" style="background: ${getPatientColor(patient.id)};">
                                    ${getPatientInitials(patient.first_name, patient.last_name)}
                                </div>
                                <div class="patient-details">
                                    <h4>${patient.first_name || 'N/A'} ${patient.last_name || ''}</h4>
                                    <p>ID: ${patient.identification_number || patient.id}</p>
                                </div>
                            </div>
                        </td>
                        <td data-label="Contacto">
                            <div>
                                <div style="font-weight: 500;">${patient.email || 'Sin email'}</div>
                                <div style="font-size: 0.875rem; color: var(--gray-500);">${patient.phone || 'Sin teléfono'}</div>
                            </div>
                        </td>
                        <td data-label="Edad">
                            <span style="font-weight: 500;">${calculateAge(patient.date_of_birth) || 'N/A'} años</span>
                        </td>
                        <td data-label="Tipo de Sangre">
                            <span style="font-weight: 500; color: var(--accent);">${patient.blood_type || 'N/A'}</span>
                        </td>
                        <td data-label="Estado">
                            <span class="status-badge ${patient.status === 'active' ? 'status-active' : 'status-inactive'}">
                                ${patient.status === 'active' ? 'Activo' : 'Inactivo'}
                            </span>
                        </td>
                        <td data-label="Última Cita">
                            <span style="font-size: 0.875rem; color: var(--gray-500);">
                                ${patient.last_appointment || 'Nunca'}
                            </span>
                        </td>
                        <td data-label="Acciones">
                            <div class="actions-dropdown">
                                <button class="actions-btn" onclick="showPatientActions(${patient.id})">
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
    
    // Previous button
    if (data.current_page > 1) {
        pagination += `<button class="pagination-btn" onclick="loadPatients(${data.current_page - 1})">
            <i class="fas fa-chevron-left"></i>
        </button>`;
    }
    
    // Page numbers
    const startPage = Math.max(1, data.current_page - 2);
    const endPage = Math.min(data.last_page, data.current_page + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        pagination += `<button class="pagination-btn ${i === data.current_page ? 'active' : ''}" 
            onclick="loadPatients(${i})">${i}</button>`;
    }
    
    // Next button
    if (data.current_page < data.last_page) {
        pagination += `<button class="pagination-btn" onclick="loadPatients(${data.current_page + 1})">
            <i class="fas fa-chevron-right"></i>
        </button>`;
    }
    
    pagination += '</div>';
    container.innerHTML = pagination;
}

function handleSearch(event) {
    const searchTerm = event.target.value.trim();
    if (searchTerm) {
        currentFilters.search = searchTerm;
    } else {
        delete currentFilters.search;
    }
    loadPatients(1);
}

function handleFilter(event) {
    const filterValue = event.target.value;
    if (filterValue) {
        currentFilters.filter = filterValue;
    } else {
        delete currentFilters.filter;
    }
    loadPatients(1);
}

function handlePerPageChange() {
    loadPatients(1);
}

function clearFilters() {
    currentFilters = {};
    document.getElementById('searchInput').value = '';
    document.getElementById('filterSelect').value = '';
    loadPatients(1);
}

function exportPatients() {
    // Implementar exportación
    alert('Función de exportación en desarrollo');
}

function showPatientActions(patientId) {
    // Implementar menú de acciones
    const actions = [
        `Ver perfil: /patients/${patientId}`,
        `Editar información`,
        `Programar cita`,
        `Ver historial médico`,
        `Generar reporte`
    ];
    alert(actions.join('\n'));
}

function showErrorState() {
    document.getElementById('patientsTableContainer').innerHTML = `
        <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 1rem; color: var(--warning);"></i>
            <h3 style="margin-bottom: 0.5rem;">Error al cargar pacientes</h3>
            <p>Hubo un problema al cargar la información. Por favor, intenta de nuevo.</p>
            <button class="btn btn-primary" onclick="loadPatients()" style="margin-top: 1rem;">
                <i class="fas fa-redo"></i>
                Reintentar
            </button>
        </div>
    `;
}

// Utility functions
function getPatientColor(id) {
    const colors = ['#667eea', '#764ba2', '#00AEEF', '#FF6B6B', '#10B981', '#F59E0B'];
    return colors[id % colors.length];
}

function getPatientInitials(firstName, lastName) {
    const first = firstName ? firstName.charAt(0).toUpperCase() : '';
    const last = lastName ? lastName.charAt(0).toUpperCase() : '';
    return first + last || 'P';
}

function calculateAge(birthDate) {
    if (!birthDate) return null;
    const today = new Date();
    const birth = new Date(birthDate);
    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        age--;
    }
    
    return age;
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