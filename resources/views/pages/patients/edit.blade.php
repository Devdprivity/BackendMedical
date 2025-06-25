@extends('layouts.app')

@section('title', 'Editar Paciente - MediCare Pro')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Editar Paciente</h1>
        <p class="page-subtitle">Actualizar información del paciente</p>
    </div>
</div>

<form id="patientForm" class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Paciente</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Personal Information -->
            <div class="col-md-12">
                <div class="form-group">
                    <label for="name" class="form-label required">Nombre Completo</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                    <small class="form-text">Nombre completo del paciente</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="dni" class="form-label required">Número de Identificación</label>
                    <input type="text" id="dni" name="dni" class="form-control" required>
                    <small class="form-text">Cédula, pasaporte o documento de identidad</small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="birth_date" class="form-label required">Fecha de Nacimiento</label>
                    <input type="date" id="birth_date" name="birth_date" class="form-control" required max="{{ date('Y-m-d') }}">
                    <small class="form-text">Fecha de nacimiento del paciente</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="gender" class="form-label required">Género</label>
                    <select id="gender" name="gender" class="form-control" required>
                        <option value="">Seleccionar género...</option>
                        <option value="male">Masculino</option>
                        <option value="female">Femenino</option>
                        <option value="other">Otro</option>
                    </select>
                    <small class="form-text">Género del paciente</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="blood_type" class="form-label">Tipo de Sangre</label>
                    <select id="blood_type" name="blood_type" class="form-control">
                        <option value="">Seleccionar tipo...</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                    <small class="form-text">Tipo de sangre del paciente</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="status" class="form-label">Estado</label>
                    <select id="status" name="status" class="form-control">
                        <option value="active" selected>Activo</option>
                        <option value="inactive">Inactivo</option>
                    </select>
                    <small class="form-text">Estado del paciente en el sistema</small>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <h4 style="margin-top: 2rem; margin-bottom: 1rem; color: var(--primary);">Información de Contacto</h4>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="phone" class="form-label">Teléfono</label>
                    <input type="tel" id="phone" name="phone" class="form-control">
                    <small class="form-text">Número de teléfono principal</small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control">
                    <small class="form-text">Correo electrónico del paciente</small>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="address" class="form-label">Dirección</label>
            <textarea id="address" name="address" class="form-control" rows="2" placeholder="Dirección completa del paciente..."></textarea>
            <small class="form-text">Dirección completa de residencia</small>
        </div>

        <div class="form-group">
            <label for="emergency_contact" class="form-label">Contacto de Emergencia</label>
            <input type="text" id="emergency_contact" name="emergency_contact" class="form-control" placeholder="Nombre y teléfono del contacto de emergencia">
            <small class="form-text">Persona a contactar en caso de emergencia</small>
        </div>

        <!-- Medical Information -->
        <h4 style="margin-top: 2rem; margin-bottom: 1rem; color: var(--primary);">Información Médica</h4>
        
        <div class="form-group">
            <label for="insurance_info" class="form-label">Información del Seguro</label>
            <input type="text" id="insurance_info" name="insurance_info" class="form-control" placeholder="Compañía de seguros y número de póliza">
            <small class="form-text">Información del seguro médico</small>
        </div>

        <div class="form-group">
            <label for="allergies" class="form-label">Alergias</label>
            <textarea id="allergies" name="allergies" class="form-control" rows="2" placeholder="Alergias conocidas del paciente..."></textarea>
            <small class="form-text">Alergias conocidas (medicamentos, alimentos, etc.)</small>
        </div>

        <div class="form-group">
            <label for="medical_history" class="form-label">Historial Médico</label>
            <textarea id="medical_history" name="medical_history" class="form-control" rows="3" placeholder="Historial médico relevante..."></textarea>
            <small class="form-text">Enfermedades previas, cirugías, tratamientos importantes</small>
        </div>

        <div class="form-group">
            <label for="current_medications" class="form-label">Medicamentos Actuales</label>
            <textarea id="current_medications" name="current_medications" class="form-control" rows="2" placeholder="Medicamentos que toma actualmente..."></textarea>
            <small class="form-text">Medicamentos que el paciente toma regularmente</small>
        </div>

        <!-- Success/Error Messages -->
        <div id="successMessage" class="alert alert-success" style="display: none;">
            <i class="fas fa-check-circle"></i>
            <span>Paciente creado exitosamente</span>
        </div>

        <div id="errorMessage" class="alert alert-danger" style="display: none;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Error al crear el paciente</span>
        </div>
    </div>

    <div class="card-footer" style="display: flex; justify-content: space-between; align-items: center;">
        <a href="/patients" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i>
            Cancelar
        </a>
        <button type="submit" class="btn btn-primary" id="submitBtn">
            <i class="fas fa-save"></i>
            Actualizar Paciente
        </button>
    </div>
</form>
@endsection

@push('styles')
<style>
.required::after {
    content: ' *';
    color: var(--danger);
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.alert-success {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.alert-danger {
    background: rgba(239, 68, 68, 0.1);
    color: #DC2626;
    border: 1px solid rgba(239, 68, 68, 0.2);
}

.form-control.is-invalid {
    border-color: var(--danger);
}

.invalid-feedback {
    color: var(--danger);
    font-size: 0.875rem;
    margin-top: 0.25rem;
}
</style>
@endpush

@push('scripts')
<script>
let patientId = {{ $id }};

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('patientForm').addEventListener('submit', handleSubmit);
    loadPatientData();
    
    // Auto-calculate age when date of birth changes
    document.getElementById('birth_date').addEventListener('change', function() {
        const birthDate = new Date(this.value);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        
        if (age >= 0 && age <= 150) {
            console.log(`Edad calculada: ${age} años`);
        }
    });
});

async function loadPatientData() {
    try {
        showLoading();
        
        const response = await fetch(`/api/patients/${patientId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const result = await response.json();
            const patient = result.data;
            
            // Fill form with patient data
            document.getElementById('name').value = patient.name || '';
            document.getElementById('dni').value = patient.dni || '';
            document.getElementById('birth_date').value = patient.birth_date || '';
            document.getElementById('gender').value = patient.gender || '';
            document.getElementById('blood_type').value = patient.blood_type || '';
            document.getElementById('status').value = patient.status || 'active';
            document.getElementById('phone').value = patient.phone || '';
            document.getElementById('email').value = patient.email || '';
            document.getElementById('address').value = patient.address || '';
            
            hideLoading();
        } else {
            hideLoading();
            showError('Error al cargar los datos del paciente');
        }
    } catch (error) {
        hideLoading();
        console.error('Error:', error);
        showError('Error de conexión al cargar los datos');
    }
}

async function handleSubmit(e) {
    e.preventDefault();
    
    // Clear previous errors
    clearErrors();
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    
    // Validate required fields
    if (!validateForm(data)) {
        return;
    }
    
    try {
        showLoading();
        
        const response = await fetch(`/api/patients/${patientId}`, {
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
        
        const result = await response.json();
        
        if (response.ok) {
            showSuccess('Paciente actualizado exitosamente. Redirigiendo...');
            setTimeout(() => {
                window.location.href = '/patients';
            }, 2000);
        } else {
            hideLoading();
            
            if (result.errors) {
                showValidationErrors(result.errors);
            } else {
                showError(result.message || 'Error al crear el paciente');
            }
        }
    } catch (error) {
        hideLoading();
        console.error('Error:', error);
        showError('Error de conexión. Por favor, inténtelo de nuevo.');
    }
}

function validateForm(data) {
    let isValid = true;
    
    // Required fields validation
    const requiredFields = ['name', 'dni', 'birth_date', 'gender'];
    
    requiredFields.forEach(field => {
        if (!data[field] || data[field].trim() === '') {
            showFieldError(field, 'Este campo es requerido');
            isValid = false;
        }
    });
    
    // Email validation
    if (data.email && !isValidEmail(data.email)) {
        showFieldError('email', 'Por favor ingrese un email válido');
        isValid = false;
    }
    
    // Date validation
    if (data.birth_date) {
        const birthDate = new Date(data.birth_date);
        const today = new Date();
        
        if (birthDate > today) {
            showFieldError('birth_date', 'La fecha de nacimiento no puede ser futura');
            isValid = false;
        }
        
        const age = today.getFullYear() - birthDate.getFullYear();
        if (age > 150) {
            showFieldError('birth_date', 'Por favor verifique la fecha de nacimiento');
            isValid = false;
        }
    }
    
    return isValid;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showFieldError(fieldName, message) {
    const field = document.getElementById(fieldName);
    if (field) {
        field.classList.add('is-invalid');
        
        // Remove existing error message
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }
        
        // Add new error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }
}

function showValidationErrors(errors) {
    Object.keys(errors).forEach(field => {
        const messages = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
        showFieldError(field, messages[0]);
    });
}

function clearErrors() {
    // Remove invalid classes
    document.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
    });
    
    // Remove error messages
    document.querySelectorAll('.invalid-feedback').forEach(el => {
        el.remove();
    });
    
    // Hide alert messages
    document.getElementById('successMessage').style.display = 'none';
    document.getElementById('errorMessage').style.display = 'none';
}

function showSuccess(message) {
    const successEl = document.getElementById('successMessage');
    successEl.querySelector('span').textContent = message;
    successEl.style.display = 'flex';
    
    // Scroll to top to show message
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function showError(message) {
    const errorEl = document.getElementById('errorMessage');
    errorEl.querySelector('span').textContent = message;
    errorEl.style.display = 'flex';
    
    // Scroll to top to show message
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function showLoading() {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
}

function hideLoading() {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="fas fa-save"></i> Actualizar Paciente';
}
</script>
@endpush 