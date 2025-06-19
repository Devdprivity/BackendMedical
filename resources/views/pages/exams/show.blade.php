@extends('layouts.app')

@section('title', 'Detalles del Examen - MediCare Pro')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Detalles del Examen</h1>
        <p class="page-subtitle">Información completa del examen médico</p>
    </div>
    <div style="display: flex; gap: 1rem;">
        <a href="/exams" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i>
            Volver a Exámenes
        </a>
        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'doctor' || auth()->user()->role === 'lab_technician')
        <button class="btn btn-primary" id="updateResultsBtn" onclick="updateResults()">
            <i class="fas fa-edit"></i>
            Actualizar Resultados
        </button>
        @endif
    </div>
</div>

<!-- Exam Info Card -->
<div class="row" style="margin-bottom: 2rem;">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body" style="text-align: center;">
                <div id="examIcon" style="width: 120px; height: 120px; border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: white; background: var(--accent);">
                    <i class="fas fa-flask"></i>
                </div>
                <h3 id="examName" style="margin-bottom: 0.5rem;">Cargando...</h3>
                <p id="examType" style="color: var(--gray-500); margin-bottom: 1rem;">--</p>
                <div style="display: grid; gap: 0.5rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-weight: 500;">Estado:</span>
                        <span id="examStatus" class="status-badge">--</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-weight: 500;">Prioridad:</span>
                        <span id="examPriority" class="priority-badge">--</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-weight: 500;">Fecha Solicitado:</span>
                        <span id="examRequestDate">--</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-weight: 500;">Fecha Programado:</span>
                        <span id="examScheduledDate">--</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Información del Examen</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Paciente</label>
                            <p id="patientName" class="form-value">--</p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Doctor Solicitante</label>
                            <p id="doctorName" class="form-value">--</p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Técnico Asignado</label>
                            <p id="technicianName" class="form-value">--</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Ubicación</label>
                            <p id="examLocation" class="form-value">--</p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Costo</label>
                            <p id="examCost" class="form-value">--</p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Duración Estimada</label>
                            <p id="examDuration" class="form-value">--</p>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Indicaciones Especiales</label>
                    <p id="examInstructions" class="form-value">--</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Results and Details -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Resultados del Examen</h3>
            </div>
            <div class="card-body">
                <div id="examResults">
                    <div style="text-align: center; padding: 2rem; color: var(--gray-500);">
                        <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                        <p>Cargando resultados...</p>
                    </div>
                </div>
                
                <!-- Results will be displayed here -->
                <div id="resultsContent" style="display: none;">
                    <div class="form-group">
                        <label class="form-label">Valores Principales</label>
                        <div id="mainValues" class="results-grid">
                            <!-- Main values will be populated here -->
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Observaciones</label>
                        <p id="examObservations" class="form-value">--</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Conclusiones</label>
                        <p id="examConclusions" class="form-value">--</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Recomendaciones</label>
                        <p id="examRecommendations" class="form-value">--</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Archivos Adjuntos</h3>
            </div>
            <div class="card-body">
                <div id="examAttachments">
                    <div style="text-align: center; padding: 1rem; color: var(--gray-500);">
                        <i class="fas fa-file-medical"></i>
                        <p>No hay archivos adjuntos</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card" style="margin-top: 1rem;">
            <div class="card-header">
                <h3 class="card-title">Historial de Cambios</h3>
            </div>
            <div class="card-body">
                <div id="examHistory">
                    <div class="timeline-item">
                        <div class="timeline-date" id="createdDate">--</div>
                        <div class="timeline-content">
                            <h5>Examen Solicitado</h5>
                            <p>El examen fue solicitado por el médico</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Results Modal -->
<div id="updateResultsModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Actualizar Resultados del Examen</h3>
            <button class="modal-close" onclick="closeUpdateModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="updateResultsForm">
                <div class="form-group">
                    <label for="status" class="form-label">Estado del Examen</label>
                    <select id="status" name="status" class="form-control">
                        <option value="pending">Pendiente</option>
                        <option value="in_progress">En Progreso</option>
                        <option value="completed">Completado</option>
                        <option value="cancelled">Cancelado</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="observations" class="form-label">Observaciones</label>
                    <textarea id="observations" name="observations" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="conclusions" class="form-label">Conclusiones</label>
                    <textarea id="conclusions" name="conclusions" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="recommendations" class="form-label">Recomendaciones</label>
                    <textarea id="recommendations" name="recommendations" class="form-control" rows="2"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeUpdateModal()">Cancelar</button>
            <button class="btn btn-primary" onclick="saveResults()">Guardar Cambios</button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.form-value {
    color: var(--gray-700);
    font-weight: 500;
    margin: 0;
    min-height: 1.5rem;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-pending { background: rgba(245, 158, 11, 0.1); color: #D97706; }
.status-in_progress { background: rgba(59, 130, 246, 0.1); color: #2563EB; }
.status-completed { background: rgba(16, 185, 129, 0.1); color: #059669; }
.status-cancelled { background: rgba(239, 68, 68, 0.1); color: #DC2626; }

.priority-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.priority-low { background: rgba(16, 185, 129, 0.1); color: #059669; }
.priority-normal { background: rgba(59, 130, 246, 0.1); color: #2563EB; }
.priority-high { background: rgba(245, 158, 11, 0.1); color: #D97706; }
.priority-urgent { background: rgba(239, 68, 68, 0.1); color: #DC2626; }

.results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 0.5rem;
}

.result-item {
    background: var(--gray-50);
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid var(--primary);
}

.result-label {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin-bottom: 0.25rem;
}

.result-value {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--gray-900);
}

.result-unit {
    font-size: 0.875rem;
    color: var(--gray-500);
    margin-left: 0.25rem;
}

.timeline-item {
    display: flex;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid var(--gray-200);
}

.timeline-date {
    flex-shrink: 0;
    width: 120px;
    font-size: 0.875rem;
    color: var(--gray-500);
}

.timeline-content h5 {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
    color: var(--gray-900);
}

.timeline-content p {
    margin: 0;
    font-size: 0.875rem;
    color: var(--gray-600);
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    border-radius: 8px;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
}

.modal-header h3 {
    margin: 0;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--gray-500);
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    padding: 1.5rem;
    border-top: 1px solid var(--gray-200);
}
</style>
@endpush

@push('scripts')
<script>
let currentExamId = {{ $id }};

document.addEventListener('DOMContentLoaded', function() {
    loadExamDetails();
});

async function loadExamDetails() {
    try {
        const response = await fetch(`/api/exams/${currentExamId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const exam = await response.json();
            displayExamDetails(exam);
        } else {
            showError('Error al cargar los detalles del examen');
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Error de conexión al cargar el examen');
    }
}

function displayExamDetails(exam) {
    // Update basic info
    document.getElementById('examName').textContent = exam.name || 'Examen sin nombre';
    document.getElementById('examType').textContent = exam.type || 'Tipo no especificado';
    document.getElementById('examStatus').textContent = formatStatus(exam.status);
    document.getElementById('examStatus').className = `status-badge status-${exam.status}`;
    document.getElementById('examPriority').textContent = formatPriority(exam.priority);
    document.getElementById('examPriority').className = `priority-badge priority-${exam.priority}`;
    document.getElementById('examRequestDate').textContent = formatDate(exam.requested_date);
    document.getElementById('examScheduledDate').textContent = formatDate(exam.scheduled_date);
    
    // Update detailed info
    document.getElementById('patientName').textContent = exam.patient_name || '--';
    document.getElementById('doctorName').textContent = exam.doctor_name || '--';
    document.getElementById('technicianName').textContent = exam.technician_name || 'No asignado';
    document.getElementById('examLocation').textContent = exam.location || '--';
    document.getElementById('examCost').textContent = exam.cost ? `$${exam.cost}` : '--';
    document.getElementById('examDuration').textContent = exam.estimated_duration || '--';
    document.getElementById('examInstructions').textContent = exam.instructions || 'Sin instrucciones especiales';
    
    // Update results
    displayResults(exam);
    
    // Update history
    document.getElementById('createdDate').textContent = formatDate(exam.created_at);
}

function displayResults(exam) {
    const resultsContainer = document.getElementById('examResults');
    const resultsContent = document.getElementById('resultsContent');
    
    if (exam.status === 'completed' && exam.results) {
        resultsContainer.style.display = 'none';
        resultsContent.style.display = 'block';
        
        // Display main values if available
        if (exam.results.values) {
            const mainValues = document.getElementById('mainValues');
            mainValues.innerHTML = exam.results.values.map(value => `
                <div class="result-item">
                    <div class="result-label">${value.name}</div>
                    <div class="result-value">
                        ${value.value}
                        <span class="result-unit">${value.unit || ''}</span>
                    </div>
                </div>
            `).join('');
        }
        
        document.getElementById('examObservations').textContent = exam.observations || 'Sin observaciones';
        document.getElementById('examConclusions').textContent = exam.conclusions || 'Sin conclusiones';
        document.getElementById('examRecommendations').textContent = exam.recommendations || 'Sin recomendaciones';
    } else {
        resultsContainer.innerHTML = `
            <div style="text-align: center; padding: 2rem; color: var(--gray-500);">
                <i class="fas fa-hourglass-half" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                <p>Resultados pendientes</p>
                <small>Los resultados aparecerán aquí una vez completado el examen</small>
            </div>
        `;
    }
}

function updateResults() {
    document.getElementById('updateResultsModal').style.display = 'flex';
}

function closeUpdateModal() {
    document.getElementById('updateResultsModal').style.display = 'none';
}

async function saveResults() {
    const formData = new FormData(document.getElementById('updateResultsForm'));
    const data = Object.fromEntries(formData.entries());
    
    try {
        const response = await fetch(`/api/exams/${currentExamId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify(data)
        });
        
        if (response.ok) {
            closeUpdateModal();
            loadExamDetails(); // Reload to show updated data
            showSuccess('Resultados actualizados exitosamente');
        } else {
            showError('Error al actualizar los resultados');
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Error de conexión al actualizar');
    }
}

// Utility functions
function formatDate(dateString) {
    if (!dateString) return '--';
    return new Date(dateString).toLocaleDateString('es-ES');
}

function formatStatus(status) {
    const statuses = {
        'pending': 'Pendiente',
        'in_progress': 'En Progreso',
        'completed': 'Completado',
        'cancelled': 'Cancelado'
    };
    return statuses[status] || status;
}

function formatPriority(priority) {
    const priorities = {
        'low': 'Baja',
        'normal': 'Normal',
        'high': 'Alta',
        'urgent': 'Urgente'
    };
    return priorities[priority] || priority;
}

function showError(message) {
    alert(message); // Replace with your preferred error display method
}

function showSuccess(message) {
    alert(message); // Replace with your preferred success display method
}
</script>
@endpush 