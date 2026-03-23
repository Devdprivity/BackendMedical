@extends('layouts.app')

@section('title', 'Nueva Cita - DrOrganiza')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Nueva Cita Médica</h1>
        <p class="page-subtitle">Programar una nueva cita para el paciente</p>
    </div>
</div>

<form id="appointmentForm" class="card">
    <div class="card-header">
        <h3 class="card-title">Información de la Cita</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Patient Selection -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="patient_id" class="form-label required">Paciente</label>
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'receptionist')
                    <select id="patient_id" name="patient_id" class="form-control" required>
                        <option value="">Seleccionar paciente...</option>
                    </select>
                    @else
                    <select id="patient_id" name="patient_id" class="form-control" required>
                        <option value="">Cargando pacientes...</option>
                    </select>
                    @endif
                    <small class="form-text">Seleccione el paciente para la cita</small>
                </div>
            </div>

            <!-- Doctor Selection -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="doctor_id" class="form-label required">Doctor</label>
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'receptionist')
                    <select id="doctor_id" name="doctor_id" class="form-control" required>
                        <option value="">Seleccionar doctor...</option>
                    </select>
                    @elseif(auth()->user()->role === 'doctor')
                    <select id="doctor_id" name="doctor_id" class="form-control" required>
                        <option value="{{ auth()->user()->id }}" selected>Dr. {{ auth()->user()->name }}</option>
                    </select>
                    @else
                    <select id="doctor_id" name="doctor_id" class="form-control" required>
                        <option value="">Cargando doctores...</option>
                    </select>
                    @endif
                    <small class="form-text">Doctor que atenderá la cita</small>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Date -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="appointment_date" class="form-label required">Fecha</label>
                    <input type="date" id="appointment_date" name="appointment_date" class="form-control" required min="{{ date('Y-m-d') }}">
                    <small class="form-text">Fecha de la cita</small>
                </div>
            </div>

            <!-- Time -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="appointment_time" class="form-label required">Hora</label>
                    <select id="appointment_time" name="appointment_time" class="form-control" required>
                        <option value="">Seleccionar hora...</option>
                    </select>
                    <small class="form-text">Hora disponible para la cita</small>
                </div>
            </div>

            <!-- Duration -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="duration" class="form-label">Duración (minutos)</label>
                    <select id="duration" name="duration" class="form-control">
                        <option value="30" selected>30 minutos</option>
                        <option value="45">45 minutos</option>
                        <option value="60">1 hora</option>
                        <option value="90">1.5 horas</option>
                        <option value="120">2 horas</option>
                    </select>
                    <small class="form-text">Duración estimada de la cita</small>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Reason -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="reason" class="form-label required">Motivo de la consulta</label>
                    <select id="reason" name="reason" class="form-control" required>
                        <option value="">Seleccionar motivo...</option>
                        <option value="Consulta general">Consulta general</option>
                        <option value="Control rutinario">Control rutinario</option>
                        <option value="Seguimiento">Seguimiento</option>
                        <option value="Emergencia">Emergencia</option>
                        <option value="Especialidad">Consulta de especialidad</option>
                        <option value="Examen físico">Examen físico</option>
                        <option value="Vacunación">Vacunación</option>
                        <option value="Otro">Otro</option>
                    </select>
                    <small class="form-text">Motivo principal de la cita</small>
                </div>
            </div>

            <!-- Priority -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="priority" class="form-label">Prioridad</label>
                    <select id="priority" name="priority" class="form-control">
                        <option value="normal" selected>Normal</option>
                        <option value="urgent">Urgente</option>
                        <option value="emergency">Emergencia</option>
                    </select>
                    <small class="form-text">Nivel de prioridad de la cita</small>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="form-group">
            <label for="notes" class="form-label">Notas adicionales</label>
            <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="Información adicional sobre la cita..."></textarea>
            <small class="form-text">Información adicional o instrucciones especiales</small>
        </div>

        <!-- Patient Contact Preferences (only for receptionists and admins) -->
        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'receptionist')
        <div class="form-group">
            <label class="form-label">Recordatorios</label>
            <div class="form-check-group">
                <div class="form-check">
                    <input type="checkbox" id="send_sms" name="send_sms" class="form-check-input" checked>
                    <label for="send_sms" class="form-check-label">Enviar recordatorio por SMS</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" id="send_email" name="send_email" class="form-check-input" checked>
                    <label for="send_email" class="form-check-label">Enviar recordatorio por email</label>
                </div>
            </div>
        </div>
        @endif

        <!-- Availability Check -->
        <div id="availabilityCheck" class="alert alert-info" style="display: none;">
            <i class="fas fa-info-circle"></i>
            <span id="availabilityMessage">Verificando disponibilidad...</span>
        </div>

        <!-- Conflict Warning -->
        <div id="conflictWarning" class="alert alert-warning" style="display: none;">
            <i class="fas fa-exclamation-triangle"></i>
            <span id="conflictMessage">Se detectaron conflictos de horario</span>
        </div>
    </div>

    <div class="card-footer" style="display: flex; justify-content: space-between; align-items: center;">
        <a href="{{ route('appointments.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i>
            Cancelar
        </a>
        <div style="display: flex; gap: 1rem;">
            <button type="button" id="checkAvailabilityBtn" class="btn btn-secondary">
                <i class="fas fa-search"></i>
                Verificar Disponibilidad
            </button>
            <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                <i class="fas fa-calendar-plus"></i>
                Crear Cita
            </button>
        </div>
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
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.form-check {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.time-slot {
    padding: 0.5rem 1rem;
    border: 1px solid var(--gray-300);
    border-radius: 6px;
    cursor: pointer;
    transition: var(--transition);
    text-align: center;
    background: white;
}

.time-slot:hover {
    border-color: var(--primary);
    background: rgba(0, 83, 155, 0.05);
}

.time-slot.selected {
    border-color: var(--primary);
    background: var(--primary);
    color: white;
}

.time-slot.unavailable {
    background: var(--gray-100);
    color: var(--gray-400);
    cursor: not-allowed;
}

.availability-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    gap: 0.5rem;
    margin-top: 1rem;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.alert-info {
    background: rgba(59, 130, 246, 0.1);
    color: #1D4ED8;
    border: 1px solid rgba(59, 130, 246, 0.2);
}

.alert-warning {
    background: rgba(245, 158, 11, 0.1);
    color: #D97706;
    border: 1px solid rgba(245, 158, 11, 0.2);
}

.alert-success {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.2);
}
</style>
@endpush

@push('scripts')
<script>
let selectedPatientId = null;
let selectedDoctorId = null;
let availableSlots = [];

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initializing appointment creation form...');
    
    document.getElementById('appointmentForm').addEventListener('submit', handleSubmit);
    document.getElementById('checkAvailabilityBtn').addEventListener('click', checkAvailability);
    
    // Add event listeners for dynamic time slot loading
    document.getElementById('doctor_id').addEventListener('change', function() {
        loadAvailableTimeSlots();
        resetAvailability();
    });
    
    document.getElementById('appointment_date').addEventListener('change', function() {
        loadAvailableTimeSlots();
        resetAvailability();
    });
    
    // Add listener for appointment time selection
    document.getElementById('appointment_time').addEventListener('change', resetAvailability);
    
    // Load initial data with debug
    console.log('🔄 Loading initial data...');
    
    // Run debug functions first
    debugUserInfo().then(() => {
        debugPatientLoad().then(() => {
            loadPatients();
        });
        
        debugDoctorLoad().then(() => {
            loadDoctors();
        });
    });
});

async function loadPatients() {
    try {
        const response = await fetch('/api/patients?per_page=1000', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const data = await response.json();
            const patients = data.data?.data || data.data || [];
            const select = document.getElementById('patient_id');
            
            select.innerHTML = '<option value="">Seleccionar paciente...</option>';
            patients.forEach(patient => {
                const option = document.createElement('option');
                option.value = patient.id;
                option.textContent = `${patient.name} - ${patient.identification_number || patient.dni || 'Sin ID'}`;
                if (selectedPatientId && patient.id == selectedPatientId) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading patients:', error);
    }
}

async function loadDoctors() {
    try {
        // For doctors, they can only create appointments for themselves
        @if(auth()->user()->role === 'doctor')
        return; // Doctor dropdown is already set
        @endif
        
        const response = await fetch('/api/users?role=doctor&per_page=1000', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const data = await response.json();
            const doctors = data.data?.data || data.data || [];
            const select = document.getElementById('doctor_id');
            
            select.innerHTML = '<option value="">Seleccionar doctor...</option>';
            doctors.forEach(doctor => {
                const option = document.createElement('option');
                option.value = doctor.id;
                option.textContent = `Dr. ${doctor.name}`;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading doctors:', error);
    }
}

async function loadAvailableTimeSlots() {
    const doctorId = document.getElementById('doctor_id').value;
    const date = document.getElementById('appointment_date').value;
    const timeSelect = document.getElementById('appointment_time');
    
    if (!doctorId || !date) {
        timeSelect.innerHTML = '<option value="">Seleccionar hora...</option>';
        return;
    }
    
    timeSelect.innerHTML = '<option value="">Cargando horarios...</option>';
    
    try {
        const response = await fetch(`/api/appointments/available-slots?doctor_id=${doctorId}&date=${date}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            if (result.data.length === 0) {
                timeSelect.innerHTML = '<option value="">No hay horarios disponibles</option>';
            } else {
                const options = result.data.map(slot => 
                    `<option value="${slot.time}">${slot.display_time}</option>`
                ).join('');
                timeSelect.innerHTML = '<option value="">Seleccionar hora...</option>' + options;
            }
        } else {
            timeSelect.innerHTML = '<option value="">Error al cargar horarios</option>';
        }
    } catch (error) {
        console.error('Error:', error);
        timeSelect.innerHTML = '<option value="">Error al cargar horarios</option>';
    }
}

function handlePatientChange(e) {
    selectedPatientId = e.target.value;
    resetAvailability();
}

function handleDoctorChange(e) {
    selectedDoctorId = e.target.value;
    resetAvailability();
}

function handleDateChange(e) {
    resetAvailability();
    if (e.target.value && selectedDoctorId) {
        loadTimeSlots();
    }
}

async function loadTimeSlots() {
    const date = document.getElementById('appointment_date').value;
    const doctorId = selectedDoctorId;
    
    if (!date || !doctorId) return;
    
    try {
        const response = await fetch(`/api/appointments/available-slots?doctor_id=${doctorId}&date=${date}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const slots = await response.json();
            populateTimeSlots(slots);
        }
    } catch (error) {
        console.error('Error loading time slots:', error);
    }
}

function populateTimeSlots(slots) {
    const select = document.getElementById('appointment_time');
    select.innerHTML = '<option value="">Seleccionar hora...</option>';
    
    slots.forEach(slot => {
        const option = document.createElement('option');
        option.value = slot.time;
        option.textContent = slot.time;
        option.disabled = !slot.available;
        select.appendChild(option);
    });
}

async function checkAvailability() {
    const patientId = document.getElementById('patient_id').value;
    const doctorId = document.getElementById('doctor_id').value;
    const date = document.getElementById('appointment_date').value;
    const time = document.getElementById('appointment_time').value;
    
    if (!patientId || !doctorId || !date || !time) {
        showAlert('warning', 'Por favor complete todos los campos requeridos');
        return;
    }
    
    showAlert('info', 'Verificando disponibilidad...');
    
    try {
        const response = await fetch('/api/appointments/check-availability', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                doctor_id: doctorId,
                date: date,
                time: time,
                duration: parseInt(document.getElementById('duration').value) || 30
            })
        });
        
        const result = await response.json();
        
        if (response.ok) {
            if (result.available) {
                showAlert('success', 'Horario disponible. Puede proceder a crear la cita.');
                document.getElementById('submitBtn').disabled = false;
            } else {
                let message = result.message || 'El horario no está disponible';
                if (result.conflict) {
                    message += ` (Conflicto con cita de ${result.conflict.patient_name} a las ${result.conflict.time})`;
                }
                showAlert('warning', message);
                document.getElementById('submitBtn').disabled = true;
            }
        } else {
            showAlert('warning', result.message || 'Error al verificar disponibilidad');
            document.getElementById('submitBtn').disabled = true;
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('warning', 'Error al verificar disponibilidad');
        document.getElementById('submitBtn').disabled = true;
    }
}

async function handleSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    
    // Convert separate date and time to new format
    const appointmentDate = data.appointment_date;
    const appointmentTime = data.appointment_time;
    
    // Remove old date_time field if it exists
    delete data.date_time;
    
    // Ensure we have the new format
    data.appointment_date = appointmentDate;
    data.appointment_time = appointmentTime;
    
    // Add checkboxes that might not be in FormData
    data.send_sms = document.getElementById('send_sms')?.checked || false;
    data.send_email = document.getElementById('send_email')?.checked || false;
    
    try {
        showAlert('info', 'Creando cita...');
        
        const response = await fetch('/api/appointments', {
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
            showAlert('success', 'Cita creada exitosamente. Redirigiendo...');
            setTimeout(() => {
                window.location.href = '/appointments';
            }, 2000);
        } else {
            showAlert('warning', result.message || 'Error al crear la cita');
            
            // Show validation errors if any
            if (result.errors) {
                Object.keys(result.errors).forEach(field => {
                    const input = document.getElementById(field);
                    if (input) {
                        input.classList.add('is-invalid');
                        // You can add error message display here
                    }
                });
            }
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('warning', 'Error al crear la cita');
    }
}

function showAlert(type, message) {
    // Hide all alerts first
    document.querySelectorAll('.alert').forEach(alert => {
        alert.style.display = 'none';
    });
    
    let alertElement;
    switch(type) {
        case 'info':
            alertElement = document.getElementById('availabilityCheck');
            document.getElementById('availabilityMessage').textContent = message;
            break;
        case 'warning':
            alertElement = document.getElementById('conflictWarning');
            document.getElementById('conflictMessage').textContent = message;
            break;
        case 'success':
            // Create success alert if it doesn't exist
            alertElement = document.querySelector('.alert-success');
            if (!alertElement) {
                alertElement = document.createElement('div');
                alertElement.className = 'alert alert-success';
                alertElement.innerHTML = `<i class="fas fa-check-circle"></i><span>${message}</span>`;
                document.getElementById('conflictWarning').parentNode.insertBefore(alertElement, document.getElementById('conflictWarning').nextSibling);
            } else {
                alertElement.querySelector('span').textContent = message;
            }
            break;
    }
    
    if (alertElement) {
        alertElement.style.display = 'flex';
    }
}

function resetAvailability() {
    document.getElementById('submitBtn').disabled = true;
    document.querySelectorAll('.alert').forEach(alert => {
        alert.style.display = 'none';
    });
}

// Add debug functions
async function debugUserInfo() {
    try {
        const response = await fetch('/debug/auth-status', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const data = await response.json();
            console.log('🔍 Debug - User Info:', data);
            return data;
        }
    } catch (error) {
        console.error('❌ Debug - Error getting user info:', error);
    }
}

async function debugPatientLoad() {
    console.log('🔍 Debug - Starting patient load...');
    
    const userInfo = await debugUserInfo();
    
    try {
        const response = await fetch('/api/patients?per_page=1000', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        console.log('🔍 Debug - Patient API Response Status:', response.status);
        
        if (response.ok) {
            const data = await response.json();
            console.log('🔍 Debug - Patient API Response:', data);
            console.log('🔍 Debug - Patients found:', data.data?.data?.length || data.data?.length || 0);
        } else {
            const errorData = await response.text();
            console.log('❌ Debug - Patient API Error:', errorData);
        }
    } catch (error) {
        console.error('❌ Debug - Patient load error:', error);
    }
}

async function debugDoctorLoad() {
    console.log('🔍 Debug - Starting doctor load...');
    
    try {
        const response = await fetch('/api/users?role=doctor&per_page=1000', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        console.log('🔍 Debug - Doctor API Response Status:', response.status);
        
        if (response.ok) {
            const data = await response.json();
            console.log('🔍 Debug - Doctor API Response:', data);
            console.log('🔍 Debug - Doctors found:', data.data?.data?.length || data.data?.length || 0);
        } else {
            const errorData = await response.text();
            console.log('❌ Debug - Doctor API Error:', errorData);
        }
    } catch (error) {
        console.error('❌ Debug - Doctor load error:', error);
    }
}
</script>
@endpush 