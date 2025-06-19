@extends('layouts.app')

@section('title', 'Doctores - MediCare Pro')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Doctores</h1>
        <p class="page-subtitle">Gestión del personal médico especializado</p>
    </div>
    <a href="{{ route('doctors.create') }}" class="btn btn-primary">
        <i class="fas fa-user-md"></i>
        Nuevo Doctor
    </a>
</div>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-user-md"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Total Doctores</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="totalDoctors">-</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--success); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-stethoscope"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Disponibles</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="availableDoctors">-</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--warning); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Con Citas Hoy</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="doctorsWithAppointments">-</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--secondary); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-star"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Especialidades</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="totalSpecialties">-</div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 1fr auto auto auto auto; gap: 1rem; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Buscar doctores</label>
                <div style="position: relative;">
                    <input type="text" class="form-control" placeholder="Buscar por nombre, especialidad o teléfono..." id="searchInput">
                    <i class="fas fa-search" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: var(--gray-400);"></i>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Especialidad</label>
                <select class="form-control" id="specialtyFilter">
                    <option value="">Todas las especialidades</option>
                    <option value="cardiologia">Cardiología</option>
                    <option value="neurologia">Neurología</option>
                    <option value="pediatria">Pediatría</option>
                    <option value="ginecologia">Ginecología</option>
                    <option value="traumatologia">Traumatología</option>
                    <option value="medicina_general">Medicina General</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Estado</label>
                <select class="form-control" id="statusFilter">
                    <option value="">Todos los estados</option>
                    <option value="available">Disponible</option>
                    <option value="busy">Ocupado</option>
                    <option value="on_leave">De vacaciones</option>
                </select>
            </div>
            
            <button class="btn btn-outline" onclick="clearFilters()">
                <i class="fas fa-times"></i>
                Limpiar
            </button>
            
            <button class="btn btn-secondary" onclick="exportDoctors()">
                <i class="fas fa-download"></i>
                Exportar
            </button>
        </div>
    </div>
</div>

<!-- Doctors Table -->
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title">Personal Médico</h3>
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
        <div id="doctorsTableContainer">
            <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                <p>Cargando doctores...</p>
            </div>
        </div>
    </div>
</div>

<!-- Pagination -->
<div id="paginationContainer" style="margin-top: 2rem;"></div>
@endsection

@push('styles')
<style>
.doctors-table {
    width: 100%;
    border-collapse: collapse;
}

.doctors-table th,
.doctors-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--gray-200);
    vertical-align: middle;
}

.doctors-table th {
    font-weight: 600;
    color: var(--dark);
    background: var(--gray-100);
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.doctors-table tr:hover {
    background: var(--gray-100);
}

.doctor-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: white;
    font-size: 18px;
    margin-right: 1rem;
}

.doctor-info {
    display: flex;
    align-items: center;
}

.doctor-details h4 {
    margin: 0 0 0.25rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: var(--dark);
}

.doctor-details p {
    margin: 0;
    font-size: 0.875rem;
    color: var(--gray-500);
}

.specialty-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: rgba(0, 174, 239, 0.1);
    color: var(--secondary);
}

.status-available {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.status-busy {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.status-on-leave {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.rating-stars {
    color: #FCD34D;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .doctors-table,
    .doctors-table tbody,
    .doctors-table tr,
    .doctors-table td {
        display: block;
    }
    
    .doctors-table thead {
        display: none;
    }
    
    .doctors-table tr {
        margin-bottom: 1rem;
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        padding: 1rem;
    }
    
    .doctors-table td {
        border: none;
        padding: 0.5rem 0;
        display: flex;
        justify-content: space-between;
    }
    
    .doctors-table td:before {
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
    loadDoctors();
    loadDoctorsStats();
    
    // Setup event listeners
    document.getElementById('searchInput').addEventListener('input', debounce(handleSearch, 300));
    document.getElementById('specialtyFilter').addEventListener('change', handleSpecialtyFilter);
    document.getElementById('statusFilter').addEventListener('change', handleStatusFilter);
    document.getElementById('perPageSelect').addEventListener('change', handlePerPageChange);
});

async function loadDoctors(page = 1) {
    try {
        const params = new URLSearchParams({
            page: page,
            per_page: document.getElementById('perPageSelect').value,
            ...currentFilters
        });
        
        const response = await fetch(`/api/doctors?${params}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const data = await response.json();
            // Handle Laravel pagination format
            const doctorsData = data.data?.data || data.data || [];
            renderDoctorsTable(doctorsData);
            renderPagination(data.data || data);
        } else {
            throw new Error('Error al cargar doctores');
        }
    } catch (error) {
        console.error('Error:', error);
        showErrorState();
    }
}

async function loadDoctorsStats() {
    try {
        // Simular estadísticas por ahora
        document.getElementById('totalDoctors').textContent = '24';
        document.getElementById('availableDoctors').textContent = '18';
        document.getElementById('doctorsWithAppointments').textContent = '16';
        document.getElementById('totalSpecialties').textContent = '8';
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

function renderDoctorsTable(doctors) {
    const container = document.getElementById('doctorsTableContainer');
    
    if (doctors.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
                <i class="fas fa-user-md" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                <h3 style="margin-bottom: 0.5rem;">No se encontraron doctores</h3>
                <p>No hay doctores registrados o que coincidan con los filtros.</p>
                <a href="{{ route('doctors.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                    <i class="fas fa-user-plus"></i>
                    Registrar Primer Doctor
                </a>
            </div>
        `;
        return;
    }
    
    const table = `
        <table class="doctors-table">
            <thead>
                <tr>
                    <th>Doctor</th>
                    <th>Especialidad</th>
                    <th>Contacto</th>
                    <th>Experiencia</th>
                    <th>Calificación</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                ${doctors.map(doctor => `
                    <tr>
                        <td data-label="Doctor">
                            <div class="doctor-info">
                                <div class="doctor-avatar" style="background: ${getDoctorColor(doctor.id)};">
                                    ${getDoctorInitials(doctor.first_name, doctor.last_name)}
                                </div>
                                <div class="doctor-details">
                                    <h4>Dr. ${doctor.first_name || 'N/A'} ${doctor.last_name || ''}</h4>
                                    <p>Licencia: ${doctor.license_number || 'N/A'}</p>
                                </div>
                            </div>
                        </td>
                        <td data-label="Especialidad">
                            <span class="specialty-badge">${formatSpecialty(doctor.specialty) || 'No especificada'}</span>
                        </td>
                        <td data-label="Contacto">
                            <div>
                                <div style="font-weight: 500;">${doctor.email || 'Sin email'}</div>
                                <div style="font-size: 0.875rem; color: var(--gray-500);">${doctor.phone || 'Sin teléfono'}</div>
                            </div>
                        </td>
                        <td data-label="Experiencia">
                            <span style="font-weight: 500;">${doctor.years_of_experience || 0} años</span>
                        </td>
                        <td data-label="Calificación">
                            <div class="rating-stars">
                                ${generateStars(doctor.rating || 4.5)}
                                <span style="color: var(--gray-500); margin-left: 0.5rem;">(${doctor.rating || '4.5'})</span>
                            </div>
                        </td>
                        <td data-label="Estado">
                            <span class="specialty-badge status-${doctor.status || 'available'}">
                                ${formatStatus(doctor.status || 'available')}
                            </span>
                        </td>
                        <td data-label="Acciones">
                            <div class="actions-dropdown">
                                <button class="actions-btn" onclick="showDoctorActions(${doctor.id})">
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
        pagination += `<button class="pagination-btn" onclick="loadDoctors(${data.current_page - 1})">
            <i class="fas fa-chevron-left"></i>
        </button>`;
    }
    
    const startPage = Math.max(1, data.current_page - 2);
    const endPage = Math.min(data.last_page, data.current_page + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        pagination += `<button class="pagination-btn ${i === data.current_page ? 'active' : ''}" 
            onclick="loadDoctors(${i})">${i}</button>`;
    }
    
    if (data.current_page < data.last_page) {
        pagination += `<button class="pagination-btn" onclick="loadDoctors(${data.current_page + 1})">
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
    loadDoctors(1);
}

function handleSpecialtyFilter(event) {
    const specialty = event.target.value;
    if (specialty) {
        currentFilters.specialty = specialty;
    } else {
        delete currentFilters.specialty;
    }
    loadDoctors(1);
}

function handleStatusFilter(event) {
    const status = event.target.value;
    if (status) {
        currentFilters.status = status;
    } else {
        delete currentFilters.status;
    }
    loadDoctors(1);
}

function handlePerPageChange() {
    loadDoctors(1);
}

function clearFilters() {
    currentFilters = {};
    document.getElementById('searchInput').value = '';
    document.getElementById('specialtyFilter').value = '';
    document.getElementById('statusFilter').value = '';
    loadDoctors(1);
}

function exportDoctors() {
    alert('Función de exportación en desarrollo');
}

function showDoctorActions(doctorId) {
    const actions = [
        `Ver perfil: /doctors/${doctorId}`,
        `Editar información`,
        `Ver agenda`,
        `Asignar especialidad`,
        `Generar reporte`
    ];
    alert(actions.join('\n'));
}

function showErrorState() {
    document.getElementById('doctorsTableContainer').innerHTML = `
        <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 1rem; color: var(--warning);"></i>
            <h3 style="margin-bottom: 0.5rem;">Error al cargar doctores</h3>
            <p>Hubo un problema al cargar la información. Por favor, intenta de nuevo.</p>
            <button class="btn btn-primary" onclick="loadDoctors()" style="margin-top: 1rem;">
                <i class="fas fa-redo"></i>
                Reintentar
            </button>
        </div>
    `;
}

// Utility functions
function getDoctorColor(id) {
    const colors = ['#667eea', '#764ba2', '#00AEEF', '#FF6B6B', '#10B981', '#F59E0B'];
    return colors[id % colors.length];
}

function getDoctorInitials(firstName, lastName) {
    const first = firstName ? firstName.charAt(0).toUpperCase() : '';
    const last = lastName ? lastName.charAt(0).toUpperCase() : '';
    return first + last || 'D';
}

function formatSpecialty(specialty) {
    const specialties = {
        'cardiologia': 'Cardiología',
        'neurologia': 'Neurología',
        'pediatria': 'Pediatría',
        'ginecologia': 'Ginecología',
        'traumatologia': 'Traumatología',
        'medicina_general': 'Medicina General'
    };
    return specialties[specialty] || specialty;
}

function formatStatus(status) {
    const statuses = {
        'available': 'Disponible',
        'busy': 'Ocupado',
        'on_leave': 'De vacaciones'
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
        timeout = setTimeout(later, wait);
    };
}
</script>
@endpush 