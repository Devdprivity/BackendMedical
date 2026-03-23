@extends('layouts.app')

@section('title', 'Nueva Cirugía - DrOrganiza')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Nueva Cirugía</h1>
        <p class="page-subtitle">Programar un nuevo procedimiento quirúrgico</p>
    </div>
</div>

<form id="surgeryForm" class="card">
    <div class="card-header">
        <h3 class="card-title">Información de la Cirugía</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Patient Selection -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="patient_id" class="form-label required">Paciente</label>
                    <select id="patient_id" name="patient_id" class="form-control" required>
                        <option value="">Seleccionar paciente...</option>
                    </select>
                    <small class="form-text">Paciente que será operado</small>
                </div>
            </div>

            <!-- Main Surgeon -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="main_surgeon_id" class="form-label required">Cirujano Principal</label>
                    @if(auth()->user()->role === 'admin')
                    <select id="main_surgeon_id" name="main_surgeon_id" class="form-control" required>
                        <option value="">Seleccionar cirujano...</option>
                    </select>
                    @elseif(auth()->user()->role === 'doctor')
                    <select id="main_surgeon_id" name="main_surgeon_id" class="form-control" required>
                        <option value="{{ auth()->user()->id }}" selected>{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</option>
                    </select>
                    @endif
                    <small class="form-text">Cirujano que realizará el procedimiento</small>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Surgery Type -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="surgery_type" class="form-label required">Tipo de Cirugía</label>
                    <select id="surgery_type" name="surgery_type" class="form-control" required>
                        <option value="">Seleccionar tipo...</option>
                        <option value="Apendicectomía">Apendicectomía</option>
                        <option value="Colecistectomía">Colecistectomía</option>
                        <option value="Hernioplastia">Hernioplastia</option>
                        <option value="Cesárea">Cesárea</option>
                        <option value="Artroscopia">Artroscopia</option>
                        <option value="Cataratas">Cirugía de cataratas</option>
                        <option value="Cardiovascular">Cirugía cardiovascular</option>
                        <option value="Neurológica">Cirugía neurológica</option>
                        <option value="Ortopédica">Cirugía ortopédica</option>
                        <option value="Plástica">Cirugía plástica</option>
                        <option value="Otra">Otra</option>
                    </select>
                    <small class="form-text">Tipo de procedimiento quirúrgico</small>
                </div>
            </div>

            <!-- Specialty -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="specialty" class="form-label">Especialidad</label>
                    <select id="specialty" name="specialty" class="form-control">
                        <option value="">Seleccionar especialidad...</option>
                        <option value="Cirugía General">Cirugía General</option>
                        <option value="Ginecología">Ginecología</option>
                        <option value="Traumatología">Traumatología</option>
                        <option value="Oftalmología">Oftalmología</option>
                        <option value="Cardiocirugía">Cardiocirugía</option>
                        <option value="Neurocirugía">Neurocirugía</option>
                        <option value="Cirugía Plástica">Cirugía Plástica</option>
                        <option value="Urología">Urología</option>
                        <option value="Otorrinolaringología">Otorrinolaringología</option>
                    </select>
                    <small class="form-text">Especialidad médica del procedimiento</small>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Date and Time -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="scheduled_date" class="form-label required">Fecha</label>
                    <input type="date" id="scheduled_date" name="scheduled_date" class="form-control" required min="{{ date('Y-m-d') }}">
                    <small class="form-text">Fecha programada para la cirugía</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="scheduled_time" class="form-label required">Hora</label>
                    <input type="time" id="scheduled_time" name="scheduled_time" class="form-control" required>
                    <small class="form-text">Hora programada para la cirugía</small>
                </div>
            </div>

            <!-- Duration -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="estimated_duration" class="form-label">Duración Estimada (minutos)</label>
                    <select id="estimated_duration" name="estimated_duration" class="form-control">
                        <option value="60">1 hora</option>
                        <option value="90">1.5 horas</option>
                        <option value="120" selected>2 horas</option>
                        <option value="180">3 horas</option>
                        <option value="240">4 horas</option>
                        <option value="300">5 horas</option>
                        <option value="360">6 horas</option>
                    </select>
                    <small class="form-text">Duración estimada del procedimiento</small>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Operating Room -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="operating_room" class="form-label">Quirófano</label>
                    <select id="operating_room" name="operating_room" class="form-control">
                        <option value="">Asignar automáticamente</option>
                        <option value="Quirófano 1">Quirófano 1</option>
                        <option value="Quirófano 2">Quirófano 2</option>
                        <option value="Quirófano 3">Quirófano 3</option>
                        <option value="Quirófano 4">Quirófano 4</option>
                        <option value="Quirófano de Emergencia">Quirófano de Emergencia</option>
                    </select>
                    <small class="form-text">Quirófano donde se realizará la cirugía</small>
                </div>
            </div>

            <!-- Priority -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="priority" class="form-label">Prioridad</label>
                    <select id="priority" name="priority" class="form-control">
                        <option value="routine" selected>Rutina</option>
                        <option value="urgent">Urgente</option>
                        <option value="emergency">Emergencia</option>
                    </select>
                    <small class="form-text">Nivel de prioridad del procedimiento</small>
                </div>
            </div>

            <!-- Anesthesia Type -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="anesthesia_type" class="form-label">Tipo de Anestesia</label>
                    <select id="anesthesia_type" name="anesthesia_type" class="form-control">
                        <option value="">Seleccionar tipo...</option>
                        <option value="General">General</option>
                        <option value="Regional">Regional</option>
                        <option value="Local">Local</option>
                        <option value="Sedación">Sedación</option>
                        <option value="Epidural">Epidural</option>
                        <option value="Raquídea">Raquídea</option>
                    </select>
                    <small class="form-text">Tipo de anestesia a utilizar</small>
                </div>
            </div>
        </div>

        <!-- Assistant Surgeons -->
        <div class="form-group">
            <label for="assistant_surgeons" class="form-label">Cirujanos Asistentes</label>
            <select id="assistant_surgeons" name="assistant_surgeons[]" class="form-control" multiple>
                <!-- Will be populated via JavaScript -->
            </select>
            <small class="form-text">Cirujanos que asistirán en el procedimiento (opcional)</small>
        </div>

        <!-- Pre-operative Instructions -->
        <div class="form-group">
            <label for="pre_operative_instructions" class="form-label">Instrucciones Preoperatorias</label>
            <textarea id="pre_operative_instructions" name="pre_operative_instructions" class="form-control" rows="3" placeholder="Instrucciones para el paciente antes de la cirugía..."></textarea>
            <small class="form-text">Instrucciones específicas para el paciente</small>
        </div>

        <!-- Surgery Description -->
        <div class="form-group">
            <label for="description" class="form-label">Descripción del Procedimiento</label>
            <textarea id="description" name="description" class="form-control" rows="4" placeholder="Descripción detallada del procedimiento quirúrgico..."></textarea>
            <small class="form-text">Descripción detallada del procedimiento a realizar</small>
        </div>

        <!-- Medical Equipment -->
        <div class="form-group">
            <label for="required_equipment" class="form-label">Equipamiento Requerido</label>
            <textarea id="required_equipment" name="required_equipment" class="form-control" rows="2" placeholder="Lista de equipamiento médico necesario..."></textarea>
            <small class="form-text">Equipamiento médico especial requerido</small>
        </div>

        <!-- Special Considerations -->
        <div class="form-group">
            <label for="special_considerations" class="form-label">Consideraciones Especiales</label>
            <textarea id="special_considerations" name="special_considerations" class="form-control" rows="3" placeholder="Alergias, condiciones médicas, riesgos especiales..."></textarea>
            <small class="form-text">Alergias, condiciones médicas o riesgos especiales del paciente</small>
        </div>

        <!-- Room Availability Check -->
        <div id="availabilityCheck" class="alert alert-info" style="display: none;">
            <i class="fas fa-info-circle"></i>
            <span id="availabilityMessage">Verificando disponibilidad del quirófano...</span>
        </div>

        <!-- Conflict Warning -->
        <div id="conflictWarning" class="alert alert-warning" style="display: none;">
            <i class="fas fa-exclamation-triangle"></i>
            <span id="conflictMessage">Se detectaron conflictos de horario</span>
        </div>
    </div>

    <div class="card-footer" style="display: flex; justify-content: space-between; align-items: center;">
        <a href="{{ route('surgeries.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i>
            Cancelar
        </a>
        <div style="display: flex; gap: 1rem;">
            <button type="button" id="checkAvailabilityBtn" class="btn btn-secondary">
                <i class="fas fa-search"></i>
                Verificar Disponibilidad
            </button>
            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fas fa-procedures"></i>
                Programar Cirugía
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

.form-control[multiple] {
    min-height: 100px;
}

.priority-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
}

.priority-routine {
    background: rgba(59, 130, 246, 0.1);
    color: #1D4ED8;
}

.priority-urgent {
    background: rgba(245, 158, 11, 0.1);
    color: #D97706;
}

.priority-emergency {
    background: rgba(239, 68, 68, 0.1);
    color: #DC2626;
}
</style>
@endpush

@push('scripts')
<script>
let selectedPatientId = null;

document.addEventListener('DOMContentLoaded', function() {
    // Get patient ID from URL if provided
    const urlParams = new URLSearchParams(window.location.search);
    const patientId = urlParams.get('patient_id');
    
    if (patientId) {
        selectedPatientId = patientId;
    }
    
    loadPatients();
    loadSurgeons();
    
    // Event listeners
    document.getElementById('patient_id').addEventListener('change', handlePatientChange);
    document.getElementById('scheduled_date').addEventListener('change', handleDateTimeChange);
    document.getElementById('scheduled_time').addEventListener('change', handleDateTimeChange);
    document.getElementById('operating_room').addEventListener('change', handleDateTimeChange);
    document.getElementById('checkAvailabilityBtn').addEventListener('click', checkAvailability);
    document.getElementById('surgeryForm').addEventListener('submit', handleSubmit);
    
    // Auto-update specialty based on surgery type
    document.getElementById('surgery_type').addEventListener('change', updateSpecialtyBasedOnType);
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
                option.textContent = `${patient.first_name} ${patient.last_name} - ${patient.identification_number}`;
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

async function loadSurgeons() {
    try {
        @if(auth()->user()->role === 'doctor')
        // For doctors, load other doctors for assistant surgeons
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
            const assistantSelect = document.getElementById('assistant_surgeons');
            
            assistantSelect.innerHTML = '';
            doctors.forEach(doctor => {
                if (doctor.id !== {{ auth()->user()->id }}) { // Exclude current user
                    const option = document.createElement('option');
                    option.value = doctor.id;
                    option.textContent = `Dr. ${doctor.first_name} ${doctor.last_name}`;
                    assistantSelect.appendChild(option);
                }
            });
        }
        @else
        // For admins, load all doctors for both main and assistant
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
            
            // Populate main surgeon dropdown
            const mainSelect = document.getElementById('main_surgeon_id');
            mainSelect.innerHTML = '<option value="">Seleccionar cirujano...</option>';
            doctors.forEach(doctor => {
                const option = document.createElement('option');
                option.value = doctor.id;
                option.textContent = `Dr. ${doctor.first_name} ${doctor.last_name}`;
                mainSelect.appendChild(option);
            });
            
            // Populate assistant surgeons dropdown
            const assistantSelect = document.getElementById('assistant_surgeons');
            assistantSelect.innerHTML = '';
            doctors.forEach(doctor => {
                const option = document.createElement('option');
                option.value = doctor.id;
                option.textContent = `Dr. ${doctor.first_name} ${doctor.last_name}`;
                assistantSelect.appendChild(option);
            });
        }
        @endif
    } catch (error) {
        console.error('Error loading surgeons:', error);
    }
}

function updateSpecialtyBasedOnType() {
    const surgeryType = document.getElementById('surgery_type').value;
    const specialtySelect = document.getElementById('specialty');
    
    const typeToSpecialty = {
        'Apendicectomía': 'Cirugía General',
        'Colecistectomía': 'Cirugía General',
        'Hernioplastia': 'Cirugía General',
        'Cesárea': 'Ginecología',
        'Artroscopia': 'Traumatología',
        'Cataratas': 'Oftalmología',
        'Cardiovascular': 'Cardiocirugía',
        'Neurológica': 'Neurocirugía',
        'Ortopédica': 'Traumatología',
        'Plástica': 'Cirugía Plástica'
    };
    
    if (typeToSpecialty[surgeryType]) {
        specialtySelect.value = typeToSpecialty[surgeryType];
    }
}

function handlePatientChange(e) {
    selectedPatientId = e.target.value;
    resetAvailability();
}

function handleDateTimeChange() {
    resetAvailability();
}

async function checkAvailability() {
    const date = document.getElementById('scheduled_date').value;
    const time = document.getElementById('scheduled_time').value;
    const room = document.getElementById('operating_room').value;
    const surgeonId = document.getElementById('main_surgeon_id').value;
    
    if (!date || !time || !surgeonId) {
        showAlert('warning', 'Por favor complete la fecha, hora y cirujano principal');
        return;
    }
    
    showAlert('info', 'Verificando disponibilidad...');
    
    try {
        const response = await fetch('/api/surgeries/check-availability', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                scheduled_date: date,
                scheduled_time: time,
                operating_room: room,
                main_surgeon_id: surgeonId,
                estimated_duration: document.getElementById('estimated_duration').value
            })
        });
        
        const result = await response.json();
        
        if (response.ok) {
            if (result.available) {
                showAlert('success', 'Horario y quirófano disponibles. Puede proceder a programar la cirugía.');
                document.getElementById('submitBtn').disabled = false;
            } else {
                showAlert('warning', result.message || 'El horario o quirófano no está disponible');
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
    
    // Handle multiple select for assistant surgeons
    const assistantSurgeons = Array.from(document.getElementById('assistant_surgeons').selectedOptions)
        .map(option => option.value);
    data.assistant_surgeons = assistantSurgeons;
    
    try {
        showAlert('info', 'Programando cirugía...');
        
        const response = await fetch('/api/surgeries', {
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
            showAlert('success', 'Cirugía programada exitosamente. Redirigiendo...');
            setTimeout(() => {
                window.location.href = '/surgeries';
            }, 2000);
        } else {
            showAlert('warning', result.message || 'Error al programar la cirugía');
            
            // Show validation errors if any
            if (result.errors) {
                Object.keys(result.errors).forEach(field => {
                    const input = document.getElementById(field);
                    if (input) {
                        input.classList.add('is-invalid');
                    }
                });
            }
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('warning', 'Error al programar la cirugía');
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
    document.getElementById('submitBtn').disabled = false; // Allow submission without availability check
    document.querySelectorAll('.alert').forEach(alert => {
        alert.style.display = 'none';
    });
}
</script>
@endpush 