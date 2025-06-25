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

.actions-menu {
    position: fixed;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15), 0 4px 6px rgba(0, 0, 0, 0.05);
    z-index: 9999;
    min-width: 200px;
    overflow: hidden;
    display: none;
    backdrop-filter: blur(10px);
    animation: fadeInScale 0.15s ease-out;
}

@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.action-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    color: var(--dark);
    text-decoration: none;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    font-size: 0.875rem;
    cursor: pointer;
    transition: var(--transition);
}

.action-item:hover {
    background: var(--gray-100);
    color: var(--primary);
}

.action-item.danger {
    color: var(--danger);
}

.action-item.danger:hover {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.action-item i {
    width: 16px;
    text-align: center;
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
                                    ${getPatientInitials(patient.name)}
                                </div>
                                <div class="patient-details">
                                    <h4>${patient.name || 'N/A'}</h4>
                                    <p>DNI: ${patient.dni || patient.id}</p>
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
                            <span style="font-weight: 500;">${calculateAge(patient.birth_date) || 'N/A'}</span>
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
                                <button class="actions-btn" onclick="toggleActionsMenu(${patient.id}, this)">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="actions-menu" id="actionsMenu${patient.id}" style="display: none;">
                                    <a href="/patients/${patient.id}" class="action-item">
                                        <i class="fas fa-eye"></i>
                                        Ver Detalles
                                    </a>
                                    <button class="action-item" onclick="editPatient(${patient.id})">
                                        <i class="fas fa-edit"></i>
                                        Editar
                                    </button>
                                    <button class="action-item" onclick="scheduleAppointment(${patient.id})">
                                        <i class="fas fa-calendar-plus"></i>
                                        Programar Cita
                                    </button>
                                    <button class="action-item" onclick="viewMedicalHistory(${patient.id})">
                                        <i class="fas fa-file-medical"></i>
                                        Historial Médico
                                    </button>
                                    <button class="action-item danger" onclick="deletePatient(${patient.id}, '${patient.name}')">
                                        <i class="fas fa-trash"></i>
                                        Eliminar
                                    </button>
                                </div>
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

// Toggle actions menu
function toggleActionsMenu(patientId, buttonElement) {
    const menu = document.getElementById(`actionsMenu${patientId}`);
    const isVisible = menu.style.display === 'block';
    
    // Hide all other menus
    document.querySelectorAll('.actions-menu').forEach(m => m.style.display = 'none');
    
    if (!isVisible) {
        // Calculate position relative to the button
        const rect = buttonElement.getBoundingClientRect();
        const menuWidth = 200; // min-width from CSS
        
        // Position menu to the left of the button if there's not enough space on the right
        let left = rect.right + 5;
        if (left + menuWidth > window.innerWidth) {
            left = rect.left - menuWidth - 5;
        }
        
        // Position menu below the button, but adjust if it goes off screen
        let top = rect.bottom + 5;
        if (top + 300 > window.innerHeight) { // Estimate menu height
            top = rect.top - 250; // Position above
        }
        
        menu.style.left = left + 'px';
        menu.style.top = top + 'px';
        menu.style.display = 'block';
    }
}

// Hide menus when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.actions-dropdown')) {
        document.querySelectorAll('.actions-menu').forEach(menu => {
            menu.style.display = 'none';
        });
    }
});

// Hide menus when scrolling
document.addEventListener('scroll', function() {
    document.querySelectorAll('.actions-menu').forEach(menu => {
        menu.style.display = 'none';
    });
});

// Hide menus when window is resized
window.addEventListener('resize', function() {
    document.querySelectorAll('.actions-menu').forEach(menu => {
        menu.style.display = 'none';
    });
});

// CRUD Functions
function editPatient(patientId) {
    window.location.href = `/patients/${patientId}/edit`;
}

function scheduleAppointment(patientId) {
    window.location.href = `/appointments/create?patient_id=${patientId}`;
}

function viewMedicalHistory(patientId) {
    window.location.href = `/patients/${patientId}/medical-history`;
}

async function deletePatient(patientId, patientName) {
    const confirmed = confirm(`¿Estás seguro de que deseas eliminar al paciente "${patientName}"?\n\nEsta acción no se puede deshacer.`);
    
    if (!confirmed) return;
    
    try {
        const response = await fetch(`/api/patients/${patientId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            alert('Paciente eliminado exitosamente');
            loadPatients(currentPage); // Reload current page
            loadPatientsStats(); // Update stats
        } else {
            const errorData = await response.json();
            alert(errorData.message || 'Error al eliminar el paciente');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error de conexión al eliminar el paciente');
    }
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

function getPatientInitials(fullName) {
    if (!fullName) return 'P';
    
    const names = fullName.trim().split(' ');
    if (names.length === 1) {
        return names[0].charAt(0).toUpperCase();
    }
    
    const first = names[0].charAt(0).toUpperCase();
    const last = names[names.length - 1].charAt(0).toUpperCase();
    return first + last;
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