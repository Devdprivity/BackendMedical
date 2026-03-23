@extends('layouts.app')

@section('title', 'Exámenes Médicos - DrOrganiza')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Exámenes Médicos</h1>
        <p class="page-subtitle">Gestión de exámenes de laboratorio y estudios médicos</p>
    </div>
    <a href="{{ route('exams.create') }}" class="btn btn-primary">
        <i class="fas fa-flask"></i>
        Nuevo Examen
    </a>
</div>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                <i class="fas fa-flask"></i>
            </div>
            <div>
                <div style="font-size: 1.75rem; font-weight: 700; color: var(--dark);" id="totalExams">-</div>
                <div style="font-size: 0.875rem; color: var(--gray-600);">Total Exámenes</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--warning), #F59E0B); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div>
                <div style="font-size: 1.75rem; font-weight: 700; color: var(--dark);" id="pendingExams">-</div>
                <div style="font-size: 0.875rem; color: var(--gray-600);">Pendientes</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--success), #10B981); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <div style="font-size: 1.75rem; font-weight: 700; color: var(--dark);" id="completedExams">-</div>
                <div style="font-size: 0.875rem; color: var(--gray-600);">Completados</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--danger), #DC2626); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <div style="font-size: 1.75rem; font-weight: 700; color: var(--dark);" id="urgentExams">-</div>
                <div style="font-size: 0.875rem; color: var(--gray-600);">Urgentes</div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
            <div>
                <label class="form-label">Buscar</label>
                <input type="text" class="form-control" placeholder="Buscar por paciente, tipo de examen..." id="searchInput">
            </div>
            <div>
                <label class="form-label">Tipo de Examen</label>
                <select class="form-control" id="typeFilter">
                    <option value="">Todos los tipos</option>
                    <option value="laboratorio">Laboratorio</option>
                    <option value="imagenologia">Imagenología</option>
                    <option value="cardiologia">Cardiología</option>
                    <option value="neurologia">Neurología</option>
                    <option value="endoscopia">Endoscopia</option>
                </select>
            </div>
            <div>
                <label class="form-label">Estado</label>
                <select class="form-control" id="statusFilter">
                    <option value="">Todos los estados</option>
                    <option value="scheduled">Programado</option>
                    <option value="in_progress">En Proceso</option>
                    <option value="completed">Completado</option>
                    <option value="cancelled">Cancelado</option>
                </select>
            </div>
            <div>
                <label class="form-label">Prioridad</label>
                <select class="form-control" id="priorityFilter">
                    <option value="">Todas las prioridades</option>
                    <option value="routine">Rutina</option>
                    <option value="urgent">Urgente</option>
                    <option value="emergency">Emergencia</option>
                </select>
            </div>
            <div>
                <button class="btn btn-outline" onclick="clearFilters()" style="width: 100%;">
                    <i class="fas fa-times"></i>
                    Limpiar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Exams Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lista de Exámenes</h3>
    </div>
    <div class="card-body">
        <div style="overflow-x: auto;">
            <table class="table" id="examsTable">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Tipo de Examen</th>
                        <th>Fecha Programada</th>
                        <th>Doctor Solicitante</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Resultados</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="examsTableBody">
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2rem; color: var(--gray-500);">
                            <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                            <div>Cargando exámenes...</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="pagination" id="pagination" style="display: none;">
            <button class="pagination-btn" id="prevBtn" onclick="changePage(-1)">
                <i class="fas fa-chevron-left"></i>
                Anterior
            </button>
            <span id="pageInfo">Página 1 de 1</span>
            <button class="pagination-btn" id="nextBtn" onclick="changePage(1)">
                Siguiente
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPage = 1;
let totalPages = 1;
let examsData = [];

document.addEventListener('DOMContentLoaded', function() {
    loadExams();
    loadExamsStats();
    
    // Add event listeners for filters
    document.getElementById('searchInput').addEventListener('input', debounce(filterExams, 300));
    document.getElementById('typeFilter').addEventListener('change', filterExams);
    document.getElementById('statusFilter').addEventListener('change', filterExams);
    document.getElementById('priorityFilter').addEventListener('change', filterExams);
});

async function loadExams() {
    try {
        const response = await fetch('/api/exams', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const result = await response.json();
            // Handle Laravel pagination format
            examsData = result.data?.data || result.data || [];
            renderExamsTable(examsData);
            updatePagination(result.data || result);
        } else {
            showError('Error al cargar los exámenes');
        }
    } catch (error) {
        console.error('Error loading exams:', error);
        showError('Error al cargar los exámenes');
    }
}

async function loadExamsStats() {
    try {
        const response = await fetch('/api/exams/stats', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const stats = await response.json();
            document.getElementById('totalExams').textContent = stats.total || '0';
            document.getElementById('pendingExams').textContent = stats.pending || '0';
            document.getElementById('completedExams').textContent = stats.completed || '0';
            document.getElementById('urgentExams').textContent = stats.urgent || '0';
        } else {
            // Fallback to default values
            document.getElementById('totalExams').textContent = '0';
            document.getElementById('pendingExams').textContent = '0';
            document.getElementById('completedExams').textContent = '0';
            document.getElementById('urgentExams').textContent = '0';
        }
    } catch (error) {
        console.error('Error loading stats:', error);
        // Fallback to default values
        document.getElementById('totalExams').textContent = '0';
        document.getElementById('pendingExams').textContent = '0';
        document.getElementById('completedExams').textContent = '0';
        document.getElementById('urgentExams').textContent = '0';
    }
}

function renderExamsTable(exams) {
    const tbody = document.getElementById('examsTableBody');
    
    if (exams.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" style="text-align: center; padding: 2rem; color: var(--gray-500);">
                    <i class="fas fa-flask" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                    <div>No se encontraron exámenes</div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = exams.map(exam => `
        <tr>
            <td>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 32px; height: 32px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem;">
                        ${(exam.patient?.name || 'P').charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <div style="font-weight: 600; color: var(--dark);">${exam.patient?.name || 'N/A'}</div>
                        <div style="font-size: 0.875rem; color: var(--gray-600);">ID: ${exam.patient?.id || 'N/A'}</div>
                    </div>
                </div>
            </td>
            <td>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas ${getExamTypeIcon(exam.laboratory_area)}" style="color: var(--primary);"></i>
                    <div>
                        <div style="font-weight: 500;">${exam.exam_type || 'N/A'}</div>
                        <div style="font-size: 0.875rem; color: var(--gray-600);">${exam.laboratory_area || ''}</div>
                    </div>
                </div>
            </td>
            <td>
                <div style="font-weight: 500; color: var(--dark);">${formatDate(exam.scheduled_date) || 'N/A'}</div>
                <div style="font-size: 0.875rem; color: var(--gray-600);">${formatTime(exam.scheduled_date) || ''}</div>
            </td>
            <td>
                <div style="font-weight: 500; color: var(--dark);">${exam.requesting_doctor?.name || 'N/A'}</div>
                <div style="font-size: 0.875rem; color: var(--gray-600);">${exam.requesting_doctor?.specialty || ''}</div>
            </td>
            <td>
                <span class="badge badge-info">
                    <i class="fas fa-clock"></i>
                    Rutina
                </span>
            </td>
            <td>
                <span class="badge badge-${getStatusColor(exam.status)}">
                    <i class="fas ${getStatusIcon(exam.status)}"></i>
                    ${getStatusLabel(exam.status)}
                </span>
            </td>
            <td>
                ${exam.result ? 
                    `<a href="#" onclick="viewResults(${exam.id})" class="btn btn-outline" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">
                        <i class="fas fa-file-medical"></i>
                        Ver Resultados
                    </a>` : 
                    `<span style="color: var(--gray-500); font-size: 0.875rem;">
                        <i class="fas fa-hourglass-half"></i>
                        Pendiente
                    </span>`
                }
            </td>
            <td>
                <div class="actions-dropdown">
                    <button class="actions-btn" onclick="toggleActions(${exam.id})">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="actions-menu" id="actions-${exam.id}">
                        <a href="#" onclick="viewExam(${exam.id})">
                            <i class="fas fa-eye"></i>
                            Ver Detalles
                        </a>
                        <a href="#" onclick="editExam(${exam.id})">
                            <i class="fas fa-edit"></i>
                            Editar
                        </a>
                        ${exam.status === 'scheduled' ? 
                            `<a href="#" onclick="updateStatus(${exam.id}, 'in_progress')">
                                <i class="fas fa-play"></i>
                                Iniciar Examen
                            </a>` : ''
                        }
                        ${exam.status === 'in_progress' ? 
                            `<a href="#" onclick="addResults(${exam.id})">
                                <i class="fas fa-file-medical"></i>
                                Agregar Resultados
                            </a>` : ''
                        }
                        <a href="#" onclick="deleteExam(${exam.id})" style="color: var(--danger);">
                            <i class="fas fa-trash"></i>
                            Eliminar
                        </a>
                    </div>
                </div>
            </td>
        </tr>
    `).join('');
}

function getExamTypeIcon(type) {
    const icons = {
        'laboratorio': 'fa-vial',
        'imagenologia': 'fa-x-ray',
        'cardiologia': 'fa-heartbeat',
        'neurologia': 'fa-brain',
        'endoscopia': 'fa-search'
    };
    return icons[type] || 'fa-flask';
}

function getExamTypeLabel(type) {
    const labels = {
        'laboratorio': 'Laboratorio',
        'imagenologia': 'Imagenología',
        'cardiologia': 'Cardiología',
        'neurologia': 'Neurología',
        'endoscopia': 'Endoscopia'
    };
    return labels[type] || 'Examen Médico';
}

function getPriorityColor(priority) {
    const colors = {
        'routine': 'info',
        'urgent': 'warning',
        'emergency': 'danger'
    };
    return colors[priority] || 'secondary';
}

function getPriorityIcon(priority) {
    const icons = {
        'routine': 'fa-clock',
        'urgent': 'fa-exclamation',
        'emergency': 'fa-bolt'
    };
    return icons[priority] || 'fa-clock';
}

function getPriorityLabel(priority) {
    const labels = {
        'routine': 'Rutina',
        'urgent': 'Urgente',
        'emergency': 'Emergencia'
    };
    return labels[priority] || 'Rutina';
}

function getStatusColor(status) {
    const colors = {
        'scheduled': 'info',
        'in_progress': 'warning',
        'completed': 'success',
        'cancelled': 'danger'
    };
    return colors[status] || 'secondary';
}

function getStatusIcon(status) {
    const icons = {
        'scheduled': 'fa-calendar',
        'in_progress': 'fa-spinner',
        'completed': 'fa-check',
        'cancelled': 'fa-times'
    };
    return icons[status] || 'fa-question';
}

function getStatusLabel(status) {
    const labels = {
        'scheduled': 'Programado',
        'in_progress': 'En Proceso',
        'completed': 'Completado',
        'cancelled': 'Cancelado'
    };
    return labels[status] || 'Desconocido';
}

function filterExams() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const type = document.getElementById('typeFilter').value;
    const status = document.getElementById('statusFilter').value;
    const priority = document.getElementById('priorityFilter').value;
    
    const filtered = examsData.filter(exam => {
        const matchesSearch = !search || 
            (exam.patient?.name && exam.patient?.name.toLowerCase().includes(search)) ||
            (exam.exam_type && exam.exam_type.toLowerCase().includes(search)) ||
            (exam.requesting_doctor?.name && exam.requesting_doctor?.name.toLowerCase().includes(search));
        
        const matchesType = !type || exam.laboratory_area === type;
        const matchesStatus = !status || exam.status === status;
        const matchesPriority = !priority || exam.priority === priority;
        
        return matchesSearch && matchesType && matchesStatus && matchesPriority;
    });
    
    renderExamsTable(filtered);
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('typeFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('priorityFilter').value = '';
    renderExamsTable(examsData);
}

function toggleActions(examId) {
    const menu = document.getElementById(`actions-${examId}`);
    const allMenus = document.querySelectorAll('.actions-menu');
    
    allMenus.forEach(m => {
        if (m !== menu) m.classList.remove('show');
    });
    
    menu.classList.toggle('show');
}

function viewExam(id) {
    window.location.href = `/exams/${id}`;
}

function editExam(id) {
    window.location.href = `/exams/${id}/edit`;
}

function viewResults(id) {
    window.location.href = `/exams/${id}/results`;
}

function addResults(id) {
    window.location.href = `/exams/${id}/results/create`;
}

async function updateStatus(id, newStatus) {
    try {
        const response = await fetch(`/api/exams/${id}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        });
        
        if (response.ok) {
            loadExams();
            showSuccess('Estado actualizado exitosamente');
        } else {
            showError('Error al actualizar el estado');
        }
    } catch (error) {
        console.error('Error updating status:', error);
        showError('Error al actualizar el estado');
    }
}

async function deleteExam(id) {
    if (confirm('¿Estás seguro de que quieres eliminar este examen?')) {
        try {
            const response = await fetch(`/api/exams/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                loadExams();
                showSuccess('Examen eliminado exitosamente');
            } else {
                showError('Error al eliminar el examen');
            }
        } catch (error) {
            console.error('Error deleting exam:', error);
            showError('Error al eliminar el examen');
        }
    }
}

function updatePagination(result) {
    const pagination = document.getElementById('pagination');
    const pageInfo = document.getElementById('pageInfo');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    if (result.last_page && result.last_page > 1) {
        pagination.style.display = 'flex';
        pageInfo.textContent = `Página ${result.current_page} de ${result.last_page}`;
        prevBtn.disabled = result.current_page <= 1;
        nextBtn.disabled = result.current_page >= result.last_page;
        
        currentPage = result.current_page;
        totalPages = result.last_page;
    } else {
        pagination.style.display = 'none';
    }
}

function changePage(direction) {
    const newPage = currentPage + direction;
    if (newPage >= 1 && newPage <= totalPages) {
        currentPage = newPage;
        loadExams();
    }
}

function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

function formatTime(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit'
    });
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

function showSuccess(message) {
    // Implementation for success message
    console.log('Success:', message);
}

function showError(message) {
    // Implementation for error message
    console.error('Error:', message);
}

// Close action menus when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.actions-dropdown')) {
        document.querySelectorAll('.actions-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});
</script>

<style>
.badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-info {
    background: rgba(59, 130, 246, 0.1);
    color: #1D4ED8;
}

.badge-warning {
    background: rgba(245, 158, 11, 0.1);
    color: #D97706;
}

.badge-success {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
}

.badge-danger {
    background: rgba(239, 68, 68, 0.1);
    color: #DC2626;
}

.badge-secondary {
    background: rgba(107, 114, 128, 0.1);
    color: #6B7280;
}

.actions-dropdown {
    position: relative;
}

.actions-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border-radius: 8px;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--gray-200);
    min-width: 160px;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: var(--transition);
}

.actions-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.actions-menu a {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    color: var(--gray-700);
    text-decoration: none;
    transition: var(--transition);
    font-size: 0.875rem;
}

.actions-menu a:hover {
    background: var(--gray-100);
    color: var(--primary);
}

.actions-menu a:first-child {
    border-radius: 8px 8px 0 0;
}

.actions-menu a:last-child {
    border-radius: 0 0 8px 8px;
}
</style>
@endpush 