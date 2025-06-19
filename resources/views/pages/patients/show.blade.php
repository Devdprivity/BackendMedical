@extends('layouts.app')

@section('title', 'Paciente - MediCare Pro')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Perfil del Paciente</h1>
        <p class="page-subtitle">Información detallada y historial médico</p>
    </div>
    <div style="display: flex; gap: 1rem;">
        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'doctor' || auth()->user()->role === 'receptionist')
        <a href="{{ route('appointments.create', ['patient_id' => ':patientId']) }}" class="btn btn-primary" id="scheduleAppointmentBtn">
            <i class="fas fa-calendar-plus"></i>
            Agendar Cita
        </a>
        @endif
        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'doctor')
        <a href="#" class="btn btn-secondary" id="editPatientBtn" onclick="editPatient()">
            <i class="fas fa-edit"></i>
            Editar
        </a>
        @endif
    </div>
</div>

<!-- Patient Info Card -->
<div class="row" style="margin-bottom: 2rem;">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body" style="text-align: center;">
                <div id="patientAvatar" style="width: 120px; height: 120px; border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: 700; color: white; background: var(--primary);">
                    P
                </div>
                <h3 id="patientName" style="margin-bottom: 0.5rem;">Cargando...</h3>
                <p id="patientAge" style="color: var(--gray-500); margin-bottom: 1rem;">-- años</p>
                <div style="display: grid; gap: 0.5rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-weight: 500;">Estado:</span>
                        <span id="patientStatus" class="status-badge">--</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-weight: 500;">Tipo de Sangre:</span>
                        <span id="patientBloodType" style="font-weight: 600; color: var(--accent);">--</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-weight: 500;">Teléfono:</span>
                        <span id="patientPhone">--</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-weight: 500;">Email:</span>
                        <span id="patientEmail">--</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Información Personal</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Identificación</label>
                            <p id="patientId" class="form-value">--</p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Fecha de Nacimiento</label>
                            <p id="patientBirthDate" class="form-value">--</p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Género</label>
                            <p id="patientGender" class="form-value">--</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Dirección</label>
                            <p id="patientAddress" class="form-value">--</p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Contacto de Emergencia</label>
                            <p id="patientEmergencyContact" class="form-value">--</p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Seguro Médico</label>
                            <p id="patientInsurance" class="form-value">--</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs for Medical History -->
<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs" role="tablist">
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'doctor' || auth()->user()->role === 'nurse')
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#appointments" role="tab">
                    <i class="fas fa-calendar-alt"></i>
                    Citas Médicas
                </a>
            </li>
            @endif
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'doctor')
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#medical-history" role="tab">
                    <i class="fas fa-file-medical"></i>
                    Historial Médico
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#medications" role="tab">
                    <i class="fas fa-pills"></i>
                    Medicamentos
                </a>
            </li>
            @endif
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'doctor' || auth()->user()->role === 'lab_technician')
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#exams" role="tab">
                    <i class="fas fa-flask"></i>
                    Exámenes
                </a>
            </li>
            @endif
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'doctor')
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#surgeries" role="tab">
                    <i class="fas fa-procedures"></i>
                    Cirugías
                </a>
            </li>
            @endif
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'accountant')
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#invoices" role="tab">
                    <i class="fas fa-file-invoice-dollar"></i>
                    Facturación
                </a>
            </li>
            @endif
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'doctor' || auth()->user()->role === 'nurse')
            <div class="tab-pane active" id="appointments" role="tabpanel">
                <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 1rem;">
                    <h4>Citas Médicas</h4>
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'doctor' || auth()->user()->role === 'receptionist')
                    <button class="btn btn-primary btn-sm" onclick="scheduleNewAppointment()">
                        <i class="fas fa-plus"></i>
                        Nueva Cita
                    </button>
                    @endif
                </div>
                <div id="appointmentsContainer">
                    <div style="text-align: center; padding: 2rem;">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Cargando citas...</p>
                    </div>
                </div>
            </div>
            @endif
            
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'doctor')
            <div class="tab-pane" id="medical-history" role="tabpanel">
                <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 1rem;">
                    <h4>Historial Médico</h4>
                    <button class="btn btn-primary btn-sm" onclick="addMedicalRecord()">
                        <i class="fas fa-plus"></i>
                        Agregar Registro
                    </button>
                </div>
                <div id="medicalHistoryContainer">
                    <div style="text-align: center; padding: 2rem;">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Cargando historial médico...</p>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane" id="medications" role="tabpanel">
                <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 1rem;">
                    <h4>Medicamentos Actuales</h4>
                    <button class="btn btn-primary btn-sm" onclick="prescribeMedication()">
                        <i class="fas fa-plus"></i>
                        Prescribir
                    </button>
                </div>
                <div id="medicationsContainer">
                    <div style="text-align: center; padding: 2rem;">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Cargando medicamentos...</p>
                    </div>
                </div>
            </div>
            @endif
            
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'doctor' || auth()->user()->role === 'lab_technician')
            <div class="tab-pane" id="exams" role="tabpanel">
                <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 1rem;">
                    <h4>Exámenes Médicos</h4>
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'doctor')
                    <button class="btn btn-primary btn-sm" onclick="orderExam()">
                        <i class="fas fa-plus"></i>
                        Ordenar Examen
                    </button>
                    @endif
                </div>
                <div id="examsContainer">
                    <div style="text-align: center; padding: 2rem;">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Cargando exámenes...</p>
                    </div>
                </div>
            </div>
            @endif
            
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'doctor')
            <div class="tab-pane" id="surgeries" role="tabpanel">
                <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 1rem;">
                    <h4>Cirugías</h4>
                    <button class="btn btn-primary btn-sm" onclick="scheduleSurgery()">
                        <i class="fas fa-plus"></i>
                        Programar Cirugía
                    </button>
                </div>
                <div id="surgeriesContainer">
                    <div style="text-align: center; padding: 2rem;">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Cargando cirugías...</p>
                    </div>
                </div>
            </div>
            @endif
            
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'accountant')
            <div class="tab-pane" id="invoices" role="tabpanel">
                <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 1rem;">
                    <h4>Facturación</h4>
                    <button class="btn btn-primary btn-sm" onclick="createInvoice()">
                        <i class="fas fa-plus"></i>
                        Nueva Factura
                    </button>
                </div>
                <div id="invoicesContainer">
                    <div style="text-align: center; padding: 2rem;">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Cargando facturas...</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.form-value {
    margin: 0;
    padding: 0.5rem 0;
    font-weight: 500;
    color: var(--dark);
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
}

.status-active {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.status-inactive {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.nav-tabs .nav-link {
    border: none;
    color: var(--gray-600);
    padding: 1rem 1.5rem;
    border-bottom: 2px solid transparent;
}

.nav-tabs .nav-link:hover {
    color: var(--primary);
    border-bottom-color: var(--primary);
}

.nav-tabs .nav-link.active {
    color: var(--primary);
    border-bottom-color: var(--primary);
    background: none;
}

.timeline-item {
    padding: 1rem;
    border-left: 2px solid var(--gray-200);
    margin-left: 1rem;
    position: relative;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -6px;
    top: 1rem;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--primary);
}

.timeline-date {
    font-size: 0.875rem;
    color: var(--gray-500);
    font-weight: 500;
}

.timeline-content {
    margin-top: 0.5rem;
}
</style>
@endpush

@push('scripts')
<script>
let currentPatientId = null;

document.addEventListener('DOMContentLoaded', function() {
    // Get patient ID from URL
    const pathParts = window.location.pathname.split('/');
    currentPatientId = pathParts[pathParts.length - 1];
    
    loadPatientData();
    loadPatientAppointments();
    
    // Tab switching
    document.querySelectorAll('[data-toggle="tab"]').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            switchTab(this.getAttribute('href').substring(1));
        });
    });
});

async function loadPatientData() {
    try {
        const response = await fetch(`/api/patients/${currentPatientId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const patient = await response.json();
            displayPatientData(patient);
        } else {
            showError('Error al cargar la información del paciente');
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Error al cargar la información del paciente');
    }
}

function displayPatientData(patient) {
    // Update avatar
    const initials = getPatientInitials(patient.first_name, patient.last_name);
    document.getElementById('patientAvatar').textContent = initials;
    document.getElementById('patientAvatar').style.background = getPatientColor(patient.id);
    
    // Update basic info
    document.getElementById('patientName').textContent = `${patient.first_name || ''} ${patient.last_name || ''}`.trim();
    document.getElementById('patientAge').textContent = `${calculateAge(patient.date_of_birth) || '--'} años`;
    
    const statusBadge = document.getElementById('patientStatus');
    statusBadge.textContent = patient.status === 'active' ? 'Activo' : 'Inactivo';
    statusBadge.className = `status-badge status-${patient.status || 'inactive'}`;
    
    document.getElementById('patientBloodType').textContent = patient.blood_type || '--';
    document.getElementById('patientPhone').textContent = patient.phone || '--';
    document.getElementById('patientEmail').textContent = patient.email || '--';
    
    // Update detailed info
    document.getElementById('patientId').textContent = patient.identification_number || '--';
    document.getElementById('patientBirthDate').textContent = formatDate(patient.date_of_birth) || '--';
    document.getElementById('patientGender').textContent = formatGender(patient.gender) || '--';
    document.getElementById('patientAddress').textContent = patient.address || '--';
    document.getElementById('patientEmergencyContact').textContent = patient.emergency_contact || '--';
    document.getElementById('patientInsurance').textContent = patient.insurance_info || '--';
    
    // Update button links
    updatePatientButtons(patient);
}

async function loadPatientAppointments() {
    try {
        const response = await fetch(`/api/patients/${currentPatientId}/appointments`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const appointments = await response.json();
            displayAppointments(appointments);
        } else {
            document.getElementById('appointmentsContainer').innerHTML = '<p>Error al cargar las citas</p>';
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('appointmentsContainer').innerHTML = '<p>Error al cargar las citas</p>';
    }
}

function displayAppointments(appointments) {
    const container = document.getElementById('appointmentsContainer');
    
    if (appointments.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 2rem; color: var(--gray-500);">
                <i class="fas fa-calendar-times" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                <p>No hay citas registradas</p>
            </div>
        `;
        return;
    }
    
    const appointmentsList = appointments.map(appointment => `
        <div class="timeline-item">
            <div class="timeline-date">${formatDateTime(appointment.appointment_date, appointment.appointment_time)}</div>
            <div class="timeline-content">
                <h5>${appointment.reason || 'Consulta general'}</h5>
                <p><strong>Doctor:</strong> ${appointment.doctor_name || 'No asignado'}</p>
                <p><strong>Estado:</strong> <span class="status-badge status-${appointment.status}">${formatStatus(appointment.status)}</span></p>
                ${appointment.notes ? `<p><strong>Notas:</strong> ${appointment.notes}</p>` : ''}
            </div>
        </div>
    `).join('');
    
    container.innerHTML = appointmentsList;
}

function switchTab(tabId) {
    // Remove active class from all tabs and panes
    document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
    
    // Add active class to selected tab and pane
    document.querySelector(`[href="#${tabId}"]`).classList.add('active');
    document.getElementById(tabId).classList.add('active');
    
    // Load data for the selected tab
    switch(tabId) {
        case 'medical-history':
            loadMedicalHistory();
            break;
        case 'medications':
            loadMedications();
            break;
        case 'exams':
            loadExams();
            break;
        case 'surgeries':
            loadSurgeries();
            break;
        case 'invoices':
            loadInvoices();
            break;
    }
}

// Utility functions
function getPatientInitials(firstName, lastName) {
    const first = firstName ? firstName.charAt(0).toUpperCase() : '';
    const last = lastName ? lastName.charAt(0).toUpperCase() : '';
    return first + last || 'P';
}

function getPatientColor(id) {
    const colors = ['#667eea', '#764ba2', '#00AEEF', '#FF6B6B', '#10B981', '#F59E0B'];
    return colors[id % colors.length];
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

function formatDate(dateString) {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('es-ES');
}

function formatDateTime(date, time) {
    if (!date) return '';
    const dateObj = new Date(date);
    const dateStr = dateObj.toLocaleDateString('es-ES');
    return time ? `${dateStr} ${time}` : dateStr;
}

function formatGender(gender) {
    const genders = {
        'male': 'Masculino',
        'female': 'Femenino',
        'other': 'Otro'
    };
    return genders[gender] || gender;
}

function formatStatus(status) {
    const statuses = {
        'scheduled': 'Programada',
        'completed': 'Completada',
        'cancelled': 'Cancelada',
        'no_show': 'No asistió'
    };
    return statuses[status] || status;
}

function showError(message) {
    console.error(message);
    // Implement your error display logic here
}

// Action functions
function editPatient() {
    // For now, redirect to a basic edit page or show an edit modal
    window.location.href = `/patients?edit=${currentPatientId}`;
}

function scheduleNewAppointment() {
    window.location.href = `/appointments/create?patient_id=${currentPatientId}`;
}

function addMedicalRecord() {
    // Implement medical record creation
    alert('Función de historial médico en desarrollo');
}

function prescribeMedication() {
    // Implement medication prescription
    alert('Función de prescripción en desarrollo');
}

function orderExam() {
    window.location.href = `/exams/create?patient_id=${currentPatientId}`;
}

function scheduleSurgery() {
    window.location.href = `/surgeries/create?patient_id=${currentPatientId}`;
}

function createInvoice() {
    // Implement invoice creation
    alert('Función de facturación en desarrollo');
}

// Placeholder functions for loading other tabs
function loadMedicalHistory() {
    // Implement medical history loading
}

function loadMedications() {
    // Implement medications loading
}

function loadExams() {
    // Implement exams loading
}

function loadSurgeries() {
    // Implement surgeries loading
}

function loadInvoices() {
    // Implement invoices loading
}

function updatePatientButtons(patient) {
    const scheduleBtn = document.getElementById('scheduleAppointmentBtn');
    
    if (scheduleBtn) {
        scheduleBtn.href = scheduleBtn.href.replace(':patientId', patient.id);
    }
}
</script>
@endpush 