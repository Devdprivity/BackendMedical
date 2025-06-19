@extends('layouts.app')

@section('title', 'Nuevo Usuario - MediCare Pro')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Nuevo Usuario</h1>
        <p class="page-subtitle">Registrar un nuevo usuario en el sistema</p>
    </div>
</div>

<form id="userForm" class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Usuario</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Personal Information -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="first_name" class="form-label required">Nombres</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" required>
                    <small class="form-text">Nombres del usuario</small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="last_name" class="form-label required">Apellidos</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" required>
                    <small class="form-text">Apellidos del usuario</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="email" class="form-label required">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                    <small class="form-text">Correo electrónico (será usado para iniciar sesión)</small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="phone" class="form-label">Teléfono</label>
                    <input type="tel" id="phone" name="phone" class="form-control">
                    <small class="form-text">Número de teléfono del usuario</small>
                </div>
            </div>
        </div>

        <!-- Role and Access -->
        <h4 style="margin-top: 2rem; margin-bottom: 1rem; color: var(--primary);">Rol y Acceso</h4>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="role" class="form-label required">Rol del Usuario</label>
                    <select id="role" name="role" class="form-control" required>
                        <option value="">Seleccionar rol...</option>
                        <option value="doctor">Doctor</option>
                        <option value="nurse">Enfermero/a</option>
                        <option value="receptionist">Recepcionista</option>
                        <option value="lab_technician">Técnico de Laboratorio</option>
                        <option value="accountant">Contador</option>
                        <option value="admin">Administrador</option>
                    </select>
                    <small class="form-text">Rol que determinará los permisos del usuario</small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="status" class="form-label">Estado</label>
                    <select id="status" name="status" class="form-control">
                        <option value="active" selected>Activo</option>
                        <option value="inactive">Inactivo</option>
                    </select>
                    <small class="form-text">Estado del usuario en el sistema</small>
                </div>
            </div>
        </div>

        <!-- Password -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="password" class="form-label required">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" required minlength="8">
                    <small class="form-text">Mínimo 8 caracteres</small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="password_confirmation" class="form-label required">Confirmar Contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required minlength="8">
                    <small class="form-text">Debe coincidir con la contraseña</small>
                </div>
            </div>
        </div>

        <!-- Professional Information (for medical staff) -->
        <div id="professionalInfo" style="display: none;">
            <h4 style="margin-top: 2rem; margin-bottom: 1rem; color: var(--primary);">Información Profesional</h4>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="license_number" class="form-label">Número de Licencia</label>
                        <input type="text" id="license_number" name="license_number" class="form-control">
                        <small class="form-text">Número de licencia profesional</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="specialty" class="form-label">Especialidad</label>
                        <select id="specialty" name="specialty" class="form-control">
                            <option value="">Seleccionar especialidad...</option>
                            <option value="medicina_general">Medicina General</option>
                            <option value="cardiologia">Cardiología</option>
                            <option value="neurologia">Neurología</option>
                            <option value="ginecologia">Ginecología</option>
                            <option value="pediatria">Pediatría</option>
                            <option value="traumatologia">Traumatología</option>
                            <option value="oftalmologia">Oftalmología</option>
                            <option value="dermatologia">Dermatología</option>
                            <option value="psiquiatria">Psiquiatría</option>
                            <option value="cirugia_general">Cirugía General</option>
                            <option value="anestesiologia">Anestesiología</option>
                            <option value="radiologia">Radiología</option>
                        </select>
                        <small class="form-text">Especialidad médica (solo para doctores)</small>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="bio" class="form-label">Biografía Profesional</label>
                <textarea id="bio" name="bio" class="form-control" rows="3" placeholder="Experiencia, educación, certificaciones..."></textarea>
                <small class="form-text">Información profesional del usuario</small>
            </div>
        </div>

        <!-- Booking Configuration (for doctors) -->
        <div id="bookingInfo" style="display: none;">
            <h4 style="margin-top: 2rem; margin-bottom: 1rem; color: var(--primary);">Configuración de Reservas Públicas</h4>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" id="booking_enabled" name="booking_enabled" class="form-check-input" value="1">
                            <label for="booking_enabled" class="form-check-label">
                                <strong>Habilitar reservas públicas</strong>
                            </label>
                        </div>
                        <small class="form-text">Permite que pacientes reserven citas online a través de un link público</small>
                    </div>
                </div>
            </div>

            <div id="bookingSettings" style="display: none;">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="booking_slug" class="form-label">URL de Reservas</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ url('/booking') }}/</span>
                                <input type="text" id="booking_slug" name="booking_slug" class="form-control" placeholder="mi-clinica">
                            </div>
                            <small class="form-text">URL única para que pacientes accedan a reservar citas (solo letras, números y guiones)</small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="consultation_fee" class="form-label">Costo de Consulta</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" id="consultation_fee" name="consultation_fee" class="form-control" step="0.01" min="0">
                            </div>
                            <small class="form-text">Costo por consulta (opcional)</small>
                        </div>
                    </div>
                </div>

                <div class="booking-preview" id="bookingPreview" style="display: none;">
                    <div class="alert" style="background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); color: #4338CA;">
                        <i class="fas fa-link"></i>
                        <div>
                            <strong>Tu link de reservas será:</strong><br>
                            <span id="previewUrl" style="font-weight: 600;"></span>
                            <br><small>Los pacientes podrán usar este link para reservar citas contigo</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule (for medical staff) -->
        <div id="scheduleInfo" style="display: none;">
            <h4 style="margin-top: 2rem; margin-bottom: 1rem; color: var(--primary);">Horario de Trabajo</h4>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="schedule_start" class="form-label">Hora de Inicio</label>
                        <input type="time" id="schedule_start" name="schedule_start" class="form-control" value="08:00">
                        <small class="form-text">Hora de inicio del turno</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="schedule_end" class="form-label">Hora de Fin</label>
                        <input type="time" id="schedule_end" name="schedule_end" class="form-control" value="17:00">
                        <small class="form-text">Hora de fin del turno</small>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Días de Trabajo</label>
                <div class="form-check-group">
                    <div class="form-check">
                        <input type="checkbox" id="monday" name="work_days[]" value="monday" class="form-check-input" checked>
                        <label for="monday" class="form-check-label">Lunes</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="tuesday" name="work_days[]" value="tuesday" class="form-check-input" checked>
                        <label for="tuesday" class="form-check-label">Martes</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="wednesday" name="work_days[]" value="wednesday" class="form-check-input" checked>
                        <label for="wednesday" class="form-check-label">Miércoles</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="thursday" name="work_days[]" value="thursday" class="form-check-input" checked>
                        <label for="thursday" class="form-check-label">Jueves</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="friday" name="work_days[]" value="friday" class="form-check-input" checked>
                        <label for="friday" class="form-check-label">Viernes</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="saturday" name="work_days[]" value="saturday" class="form-check-input">
                        <label for="saturday" class="form-check-label">Sábado</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="sunday" name="work_days[]" value="sunday" class="form-check-input">
                        <label for="sunday" class="form-check-label">Domingo</label>
                    </div>
                </div>
                <small class="form-text">Días de la semana en que trabajará el usuario</small>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <div id="successMessage" class="alert alert-success" style="display: none;">
            <i class="fas fa-check-circle"></i>
            <span>Usuario creado exitosamente</span>
        </div>

        <div id="errorMessage" class="alert alert-danger" style="display: none;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Error al crear el usuario</span>
        </div>
    </div>

    <div class="card-footer" style="display: flex; justify-content: space-between; align-items: center;">
        <a href="/users" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i>
            Cancelar
        </a>
        <button type="submit" class="btn btn-primary" id="submitBtn">
            <i class="fas fa-user-plus"></i>
            Crear Usuario
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

.form-check-group {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.form-check {
    display: flex;
    align-items: center;
    gap: 0.5rem;
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

.role-description {
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid rgba(59, 130, 246, 0.2);
    border-radius: 6px;
    padding: 0.75rem;
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: #1D4ED8;
}

.input-group {
    display: flex;
    align-items: stretch;
}

.input-group-text {
    background: var(--gray-100);
    border: 2px solid var(--gray-200);
    border-right: none;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    color: var(--gray-600);
    border-radius: 8px 0 0 8px;
    display: flex;
    align-items: center;
}

.input-group .form-control {
    border-left: none;
    border-radius: 0 8px 8px 0;
}

.booking-preview {
    margin-top: 1rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('userForm').addEventListener('submit', handleSubmit);
    document.getElementById('role').addEventListener('change', handleRoleChange);
    document.getElementById('password_confirmation').addEventListener('input', validatePasswordMatch);
    document.getElementById('booking_enabled').addEventListener('change', handleBookingToggle);
    document.getElementById('booking_slug').addEventListener('input', handleSlugChange);
    document.getElementById('first_name').addEventListener('input', generateSlugFromName);
    document.getElementById('last_name').addEventListener('input', generateSlugFromName);
});

function handleRoleChange() {
    const role = document.getElementById('role').value;
    const professionalInfo = document.getElementById('professionalInfo');
    const bookingInfo = document.getElementById('bookingInfo');
    const scheduleInfo = document.getElementById('scheduleInfo');
    
    // Show professional info for medical staff
    if (['doctor', 'nurse', 'lab_technician'].includes(role)) {
        professionalInfo.style.display = 'block';
        scheduleInfo.style.display = 'block';
    } else {
        professionalInfo.style.display = 'none';
        scheduleInfo.style.display = 'none';
    }
    
    // Show booking info only for doctors
    if (role === 'doctor') {
        bookingInfo.style.display = 'block';
    } else {
        bookingInfo.style.display = 'none';
    }
    
    // Show role description
    showRoleDescription(role);
}

function handleBookingToggle() {
    const enabled = document.getElementById('booking_enabled').checked;
    const settings = document.getElementById('bookingSettings');
    
    if (enabled) {
        settings.style.display = 'block';
        generateSlugFromName(); // Auto-generate slug when enabled
    } else {
        settings.style.display = 'none';
        document.getElementById('bookingPreview').style.display = 'none';
    }
}

function generateSlugFromName() {
    const firstName = document.getElementById('first_name').value;
    const lastName = document.getElementById('last_name').value;
    const bookingEnabled = document.getElementById('booking_enabled').checked;
    
    if (bookingEnabled && firstName && lastName) {
        const slug = generateSlug(firstName + ' ' + lastName);
        document.getElementById('booking_slug').value = slug;
        updateSlugPreview(slug);
    }
}

function handleSlugChange() {
    const slug = document.getElementById('booking_slug').value;
    const cleanSlug = generateSlug(slug);
    
    if (slug !== cleanSlug) {
        document.getElementById('booking_slug').value = cleanSlug;
    }
    
    updateSlugPreview(cleanSlug);
}

function generateSlug(text) {
    return text
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '') // Remove accents
        .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
        .replace(/\s+/g, '-') // Replace spaces with hyphens
        .replace(/-+/g, '-') // Replace multiple hyphens with single
        .replace(/^-|-$/g, ''); // Remove leading/trailing hyphens
}

function updateSlugPreview(slug) {
    const preview = document.getElementById('bookingPreview');
    const previewUrl = document.getElementById('previewUrl');
    
    if (slug) {
        const baseUrl = '{{ url("/booking") }}';
        previewUrl.textContent = baseUrl + '/' + slug;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}

function showRoleDescription(role) {
    const descriptions = {
        'doctor': 'Acceso completo a pacientes, citas, cirugías, medicamentos y exámenes. Puede crear prescripciones y realizar diagnósticos.',
        'nurse': 'Acceso a pacientes con citas próximas, medicamentos y asistencia en procedimientos.',
        'receptionist': 'Gestión de citas, registro de pacientes y atención al cliente.',
        'lab_technician': 'Gestión de exámenes médicos, resultados de laboratorio y reportes.',
        'accountant': 'Gestión de facturación, pagos y reportes financieros.',
        'admin': 'Acceso completo al sistema, gestión de usuarios y configuración.'
    };
    
    // Remove existing description
    const existingDesc = document.querySelector('.role-description');
    if (existingDesc) {
        existingDesc.remove();
    }
    
    if (descriptions[role]) {
        const desc = document.createElement('div');
        desc.className = 'role-description';
        desc.textContent = descriptions[role];
        document.getElementById('role').parentNode.appendChild(desc);
    }
}

function validatePasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmation = document.getElementById('password_confirmation').value;
    const confirmField = document.getElementById('password_confirmation');
    
    if (confirmation && password !== confirmation) {
        showFieldError('password_confirmation', 'Las contraseñas no coinciden');
    } else {
        clearFieldError('password_confirmation');
    }
}

async function handleSubmit(e) {
    e.preventDefault();
    
    // Clear previous errors
    clearErrors();
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    
    // Handle work days array
    const workDays = Array.from(document.querySelectorAll('input[name="work_days[]"]:checked'))
        .map(checkbox => checkbox.value);
    data.work_days = workDays;
    
    // Handle booking enabled checkbox
    data.booking_enabled = document.getElementById('booking_enabled').checked ? 1 : 0;
    
    // Validate form
    if (!validateForm(data)) {
        return;
    }
    
    try {
        showLoading();
        
        const response = await fetch('/api/users', {
            method: 'POST',
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
            showSuccess('Usuario creado exitosamente. Redirigiendo...');
            setTimeout(() => {
                window.location.href = '/users';
            }, 2000);
        } else {
            hideLoading();
            
            if (result.errors) {
                showValidationErrors(result.errors);
            } else {
                showError(result.message || 'Error al crear el usuario');
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
    const requiredFields = ['first_name', 'last_name', 'email', 'role', 'password', 'password_confirmation'];
    
    requiredFields.forEach(field => {
        if (!data[field] || data[field].trim() === '') {
            showFieldError(field, 'Este campo es requerido');
            isValid = false;
        }
    });
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (data.email && !emailRegex.test(data.email)) {
        showFieldError('email', 'El formato del email no es válido');
        isValid = false;
    }
    
    // Password validation
    if (data.password !== data.password_confirmation) {
        showFieldError('password_confirmation', 'Las contraseñas no coinciden');
        isValid = false;
    }
    
    // Booking slug validation for doctors with booking enabled
    if (data.role === 'doctor' && data.booking_enabled && data.booking_slug) {
        const slugRegex = /^[a-z0-9-]+$/;
        if (!slugRegex.test(data.booking_slug)) {
            showFieldError('booking_slug', 'La URL solo puede contener letras minúsculas, números y guiones');
            isValid = false;
        }
    }
    
    return isValid;
}

function showFieldError(fieldName, message) {
    const field = document.getElementById(fieldName);
    if (field) {
        field.classList.add('is-invalid');
        
        // Remove existing error message
        clearFieldError(fieldName);
        
        // Add new error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }
}

function clearFieldError(fieldName) {
    const field = document.getElementById(fieldName);
    if (field) {
        field.classList.remove('is-invalid');
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }
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
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando...';
}

function hideLoading() {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Crear Usuario';
}
</script>
@endpush 