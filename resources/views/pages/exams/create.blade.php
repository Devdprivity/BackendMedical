@extends('layouts.app')

@section('title', 'Nuevo Examen - MediCare Pro')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-flask"></i>
            Nuevo Examen Médico
        </h1>
        <p class="page-subtitle">Programar un nuevo examen de laboratorio o estudio médico</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('exams.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i>
            Volver a Exámenes
        </a>
    </div>
</div>

<form id="examForm" class="needs-validation" novalidate>
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <!-- Main Form -->
        <div>
            <!-- Patient Information -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3 class="card-title">Información del Paciente</h3>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label class="form-label">Paciente *</label>
                            <select class="form-control" name="patient_id" required>
                                <option value="">Seleccionar paciente</option>
                                <option value="1">Juan Pérez - 12345678</option>
                                <option value="2">María López - 87654321</option>
                                <option value="3">Carlos García - 11223344</option>
                                <option value="4">Ana Martínez - 55667788</option>
                                <option value="5">Pedro Rodríguez - 99887766</option>
                            </select>
                            <div class="invalid-feedback">
                                Por favor selecciona un paciente.
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Doctor Solicitante *</label>
                            <select class="form-control" name="doctor_id" required>
                                <option value="">Seleccionar doctor</option>
                                <option value="1">Dr. Ana García - Cardiología</option>
                                <option value="2">Dr. Carlos Rodríguez - Neurología</option>
                                <option value="3">Dr. Laura Martínez - Pediatría</option>
                                <option value="4">Dr. Miguel Torres - Medicina Interna</option>
                                <option value="5">Dr. Sofia Herrera - Ginecología</option>
                            </select>
                            <div class="invalid-feedback">
                                Por favor selecciona un doctor.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exam Details -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3 class="card-title">Detalles del Examen</h3>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div class="form-group">
                            <label class="form-label">Tipo de Examen *</label>
                            <select class="form-control" name="type" required onchange="updateExamOptions()">
                                <option value="">Seleccionar tipo</option>
                                <option value="laboratorio">Laboratorio</option>
                                <option value="imagenologia">Imagenología</option>
                                <option value="cardiologia">Cardiología</option>
                                <option value="neurologia">Neurología</option>
                                <option value="endoscopia">Endoscopia</option>
                            </select>
                            <div class="invalid-feedback">
                                Por favor selecciona un tipo de examen.
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Examen Específico *</label>
                            <select class="form-control" name="exam_name" required id="examNameSelect">
                                <option value="">Primero selecciona un tipo</option>
                            </select>
                            <div class="invalid-feedback">
                                Por favor selecciona un examen específico.
                            </div>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div class="form-group">
                            <label class="form-label">Fecha Programada *</label>
                            <input type="date" class="form-control" name="scheduled_date" required min="{{ date('Y-m-d') }}">
                            <div class="invalid-feedback">
                                Por favor selecciona una fecha.
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Hora Programada *</label>
                            <input type="time" class="form-control" name="scheduled_time" required>
                            <div class="invalid-feedback">
                                Por favor selecciona una hora.
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Duración Estimada</label>
                            <select class="form-control" name="estimated_duration">
                                <option value="15">15 minutos</option>
                                <option value="30" selected>30 minutos</option>
                                <option value="45">45 minutos</option>
                                <option value="60">1 hora</option>
                                <option value="90">1.5 horas</option>
                                <option value="120">2 horas</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label class="form-label">Prioridad *</label>
                            <select class="form-control" name="priority" required>
                                <option value="routine" selected>Rutina</option>
                                <option value="urgent">Urgente</option>
                                <option value="emergency">Emergencia</option>
                            </select>
                            <div class="invalid-feedback">
                                Por favor selecciona una prioridad.
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Ubicación</label>
                            <select class="form-control" name="location">
                                <option value="">Seleccionar ubicación</option>
                                <option value="lab1">Laboratorio 1</option>
                                <option value="lab2">Laboratorio 2</option>
                                <option value="imaging">Sala de Imagenología</option>
                                <option value="cardio">Sala de Cardiología</option>
                                <option value="neuro">Sala de Neurología</option>
                                <option value="endoscopy">Sala de Endoscopia</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Instrucciones Especiales</label>
                        <textarea class="form-control" name="special_instructions" rows="3" placeholder="Instrucciones especiales para el paciente o el personal médico..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Preparation Instructions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Preparación del Paciente</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Instrucciones de Preparación</label>
                        <textarea class="form-control" name="preparation_instructions" rows="4" placeholder="Ej: Ayuno de 12 horas, suspender medicamentos, etc."></textarea>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="checkbox" id="requires_fasting" name="requires_fasting" value="1">
                                <label for="requires_fasting" class="form-label" style="margin: 0;">Requiere ayuno</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="checkbox" id="requires_contrast" name="requires_contrast" value="1">
                                <label for="requires_contrast" class="form-label" style="margin: 0;">Requiere contraste</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Quick Actions -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3 class="card-title">Acciones</h3>
                </div>
                <div class="card-body">
                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">
                        <i class="fas fa-save"></i>
                        Programar Examen
                    </button>
                    
                    <button type="button" class="btn btn-outline" onclick="saveDraft()" style="width: 100%; margin-bottom: 1rem;">
                        <i class="fas fa-file-alt"></i>
                        Guardar Borrador
                    </button>
                    
                    <a href="{{ route('exams.index') }}" class="btn btn-secondary" style="width: 100%;">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </a>
                </div>
            </div>

            <!-- Exam Guidelines -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3 class="card-title">Guías Rápidas</h3>
                </div>
                <div class="card-body">
                    <div style="font-size: 0.875rem; color: var(--gray-600); line-height: 1.6;">
                        <div style="margin-bottom: 1rem;">
                            <strong style="color: var(--dark);">Laboratorio:</strong><br>
                            • Ayuno 8-12 horas<br>
                            • Hidratación normal<br>
                            • Medicamentos según indicación
                        </div>
                        
                        <div style="margin-bottom: 1rem;">
                            <strong style="color: var(--dark);">Imagenología:</strong><br>
                            • Retirar objetos metálicos<br>
                            • Ropa cómoda<br>
                            • Informar embarazo
                        </div>
                        
                        <div>
                            <strong style="color: var(--dark);">Endoscopia:</strong><br>
                            • Ayuno 12 horas<br>
                            • Acompañante requerido<br>
                            • Suspender anticoagulantes
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Exams -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Exámenes Recientes</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: var(--gray-50); border-radius: 8px;">
                            <div style="width: 32px; height: 32px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.875rem;">
                                <i class="fas fa-vial"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 500; font-size: 0.875rem;">Hemograma Completo</div>
                                <div style="font-size: 0.75rem; color: var(--gray-600);">Juan Pérez - Hoy</div>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: var(--gray-50); border-radius: 8px;">
                            <div style="width: 32px; height: 32px; background: var(--secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.875rem;">
                                <i class="fas fa-x-ray"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 500; font-size: 0.875rem;">Radiografía Tórax</div>
                                <div style="font-size: 0.75rem; color: var(--gray-600);">María López - Ayer</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
const examOptions = {
    laboratorio: [
        'Hemograma Completo',
        'Química Sanguínea',
        'Perfil Lipídico',
        'Examen de Orina',
        'Glicemia',
        'Hemoglobina Glicosilada',
        'TSH',
        'Perfil Hepático',
        'Perfil Renal',
        'Marcadores Tumorales'
    ],
    imagenologia: [
        'Radiografía de Tórax',
        'Radiografía de Abdomen',
        'Ecografía Abdominal',
        'Ecografía Pélvica',
        'Tomografía Computarizada',
        'Resonancia Magnética',
        'Mamografía',
        'Densitometría Ósea'
    ],
    cardiologia: [
        'Electrocardiograma',
        'Ecocardiograma',
        'Prueba de Esfuerzo',
        'Holter 24 horas',
        'MAPA',
        'Cateterismo Cardíaco'
    ],
    neurologia: [
        'Electroencefalograma',
        'Resonancia Magnética Cerebral',
        'Tomografía Cerebral',
        'Doppler Carotídeo',
        'Electromiografía'
    ],
    endoscopia: [
        'Endoscopia Digestiva Alta',
        'Colonoscopia',
        'Rectosigmoidoscopia',
        'Broncoscopia',
        'Cistoscopia'
    ]
};

function updateExamOptions() {
    const typeSelect = document.querySelector('select[name="type"]');
    const examSelect = document.getElementById('examNameSelect');
    const selectedType = typeSelect.value;
    
    examSelect.innerHTML = '<option value="">Seleccionar examen</option>';
    
    if (selectedType && examOptions[selectedType]) {
        examOptions[selectedType].forEach(exam => {
            const option = document.createElement('option');
            option.value = exam.toLowerCase().replace(/\s+/g, '_');
            option.textContent = exam;
            examSelect.appendChild(option);
        });
    }
}

document.getElementById('examForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!this.checkValidity()) {
        e.stopPropagation();
        this.classList.add('was-validated');
        return;
    }
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('/api/exams', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        if (response.ok) {
            const result = await response.json();
            showAlert('success', 'Examen programado exitosamente');
            setTimeout(() => {
                window.location.href = '/exams';
            }, 2000);
        } else {
            const error = await response.json();
            showAlert('error', error.message || 'Error al programar el examen');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('error', 'Error al programar el examen');
    }
});

function saveDraft() {
    const formData = new FormData(document.getElementById('examForm'));
    const data = Object.fromEntries(formData);
    
    localStorage.setItem('examDraft', JSON.stringify(data));
    showAlert('success', 'Borrador guardado exitosamente');
}

function loadDraft() {
    const draft = localStorage.getItem('examDraft');
    if (draft) {
        const data = JSON.parse(draft);
        const form = document.getElementById('examForm');
        
        Object.keys(data).forEach(key => {
            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                if (input.type === 'checkbox') {
                    input.checked = data[key] === '1';
                } else {
                    input.value = data[key];
                }
            }
        });
        
        // Update exam options if type is selected
        if (data.type) {
            updateExamOptions();
            setTimeout(() => {
                const examSelect = document.getElementById('examNameSelect');
                if (data.exam_name) {
                    examSelect.value = data.exam_name;
                }
            }, 100);
        }
    }
}

function showAlert(type, message) {
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        box-shadow: var(--shadow-lg);
        animation: slideInRight 0.3s ease;
        max-width: 400px;
    `;
    
    if (type === 'success') {
        alert.style.background = 'var(--success)';
        alert.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
    } else {
        alert.style.background = 'var(--danger)';
        alert.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
    }
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        alert.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => alert.remove(), 300);
    }, 5000);
}

// Load draft on page load
document.addEventListener('DOMContentLoaded', function() {
    loadDraft();
});
</script>

<style>
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.form-control:invalid {
    border-color: var(--danger);
}

.form-control:valid {
    border-color: var(--success);
}

.invalid-feedback {
    display: none;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: var(--danger);
}

.was-validated .form-control:invalid ~ .invalid-feedback {
    display: block;
}

input[type="checkbox"] {
    width: 1.25rem;
    height: 1.25rem;
    accent-color: var(--primary);
}
</style>
@endpush 