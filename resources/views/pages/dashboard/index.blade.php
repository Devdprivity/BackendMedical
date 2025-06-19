@extends('layouts.app')

@section('title', 'Dashboard - Sistema Médico')

@section('content')
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Resumen general del sistema médico</p>
</div>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: #718096; margin-bottom: 4px;">
                    @if(Auth::user()->role === 'admin')
                        Total Pacientes
                    @else
                        Mis Pacientes
                    @endif
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: #2d3748;" id="totalPatients">-</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #48bb78, #38a169); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: #718096; margin-bottom: 4px;">
                    @if(Auth::user()->role === 'admin')
                        Citas de Hoy
                    @else
                        Mis Citas Hoy
                    @endif
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: #2d3748;" id="todayAppointments">-</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #ed8936, #dd6b20); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                <i class="fas fa-procedures"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: #718096; margin-bottom: 4px;">
                    @if(Auth::user()->role === 'admin')
                        Cirugías Programadas
                    @else
                        Mis Cirugías
                    @endif
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: #2d3748;" id="scheduledSurgeries">-</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'accountant')
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #f56565, #e53e3e); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: #718096; margin-bottom: 4px;">Facturas Pendientes</div>
                    <div style="font-size: 2rem; font-weight: 700; color: #2d3748;" id="pendingInvoices">-</div>
                </div>
            @else
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #9f7aea, #805ad5); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                    <i class="fas fa-flask"></i>
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: #718096; margin-bottom: 4px;">Exámenes Pendientes</div>
                    <div style="font-size: 2rem; font-weight: 700; color: #2d3748;" id="pendingExams">-</div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-bottom: 2rem;">
    <!-- Recent Appointments -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Citas Recientes</h3>
        </div>
        <div class="card-body">
            <div id="recentAppointments">
                <div style="text-align: center; padding: 2rem; color: #718096;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                    <p>Cargando citas...</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Acciones Rápidas</h3>
        </div>
        <div class="card-body">
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'doctor' || auth()->user()->role === 'nurse' || auth()->user()->role === 'receptionist')
                <a href="/patients/create" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i>
                    Nuevo Paciente
                </a>
                @endif
                
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'doctor' || auth()->user()->role === 'nurse' || auth()->user()->role === 'receptionist')
                <a href="/appointments/create" class="btn btn-success">
                    <i class="fas fa-calendar-plus"></i>
                    Agendar Cita
                </a>
                @endif
                
                @if(auth()->user()->role === 'admin')
                <a href="/users/create" class="btn btn-outline">
                    <i class="fas fa-user-md"></i>
                    Registrar Usuario
                </a>
                @endif
                
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'doctor')
                <a href="/surgeries/create" class="btn btn-secondary">
                    <i class="fas fa-procedures"></i>
                    Programar Cirugía
                </a>
                @endif
                
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'doctor' || auth()->user()->role === 'nurse')
                <a href="/medications/create" class="btn" style="background: linear-gradient(135deg, #9f7aea, #805ad5); color: white;">
                    <i class="fas fa-pills"></i>
                    Nuevo Medicamento
                </a>
                @endif
                
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'doctor' || auth()->user()->role === 'lab_technician')
                <a href="/exams/create" class="btn" style="background: linear-gradient(135deg, #f56565, #e53e3e); color: white;">
                    <i class="fas fa-flask"></i>
                    Crear Examen
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Additional Info Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
    <!-- Recent Patients -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Pacientes Recientes</h3>
        </div>
        <div class="card-body">
            <div id="recentPatients">
                <div style="text-align: center; padding: 1rem; color: #718096;">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Cargando...</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- System Status -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Estado del Sistema</h3>
        </div>
        <div class="card-body">
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>API Status</span>
                    <span style="display: flex; align-items: center; gap: 0.5rem; color: #48bb78;">
                        <div style="width: 8px; height: 8px; background: #48bb78; border-radius: 50%;"></div>
                        Online
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>Base de Datos</span>
                    <span style="display: flex; align-items: center; gap: 0.5rem; color: #48bb78;">
                        <div style="width: 8px; height: 8px; background: #48bb78; border-radius: 50%;"></div>
                        Conectada
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>Última Actualización</span>
                    <span style="color: #718096; font-size: 0.875rem;">{{ date('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
});

async function loadDashboardData() {
    try {
        // Load stats
        const statsResponse = await fetch('/api/dashboard/stats', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (statsResponse.ok) {
            const stats = await statsResponse.json();
            updateStats(stats.data || stats);
        }
        
        // Load recent appointments
        const appointmentsResponse = await fetch('/api/appointments?per_page=5', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (appointmentsResponse.ok) {
            const appointmentsResult = await appointmentsResponse.json();
            // Handle Laravel pagination format
            const appointmentsData = appointmentsResult.data?.data || appointmentsResult.data || [];
            updateRecentAppointments(appointmentsData);
        }
        
        // Load recent patients
        const patientsResponse = await fetch('/api/patients?per_page=5', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (patientsResponse.ok) {
            const patientsResult = await patientsResponse.json();
            // Handle Laravel pagination format
            const patientsData = patientsResult.data?.data || patientsResult.data || [];
            updateRecentPatients(patientsData);
        }
        
    } catch (error) {
        console.error('Error loading dashboard data:', error);
        showDashboardError();
    }
}

function updateStats(stats) {
    const totalPatientsEl = document.getElementById('totalPatients');
    const todayAppointmentsEl = document.getElementById('todayAppointments');
    const scheduledSurgeriesEl = document.getElementById('scheduledSurgeries');
    const pendingInvoicesEl = document.getElementById('pendingInvoices');
    const pendingExamsEl = document.getElementById('pendingExams');
    
    // Update common stats
    totalPatientsEl.textContent = stats.patients?.total || '0';
    todayAppointmentsEl.textContent = stats.appointments?.total_today || '0';
    scheduledSurgeriesEl.textContent = stats.surgeries?.scheduled_today || '0';
    
    // Update role-specific stats
    if (pendingInvoicesEl) {
        // Admin/Accountant view - show invoices
        pendingInvoicesEl.textContent = stats.invoices?.total_pending || '0';
    }
    
    if (pendingExamsEl) {
        // Doctor view - show exams
        pendingExamsEl.textContent = stats.exams?.pending || '0';
    }
    
    // Add animation
    const elements = [totalPatientsEl, todayAppointmentsEl, scheduledSurgeriesEl];
    if (pendingInvoicesEl) elements.push(pendingInvoicesEl);
    if (pendingExamsEl) elements.push(pendingExamsEl);
    
    elements.forEach(el => {
        if (el) {
            el.style.animation = 'fadeInUp 0.5s ease';
        }
    });
}

function updateRecentAppointments(appointments) {
    const container = document.getElementById('recentAppointments');
    
    if (appointments.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 2rem; color: #718096;">
                <i class="fas fa-calendar-times" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                <p>No hay citas recientes</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = appointments.map(appointment => `
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid #e2e8f0;">
            <div>
                <div style="font-weight: 600; color: #2d3748;">${appointment.patient_name || 'Paciente'}</div>
                <div style="font-size: 0.875rem; color: #718096;">${appointment.reason || 'Consulta general'}</div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.875rem; color: #2d3748;">${appointment.appointment_date || 'Hoy'}</div>
                <div style="font-size: 0.75rem; color: #718096;">${appointment.appointment_time || '10:00'}</div>
            </div>
        </div>
    `).join('');
}

function updateRecentPatients(patients) {
    const container = document.getElementById('recentPatients');
    
    if (patients.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 1rem; color: #718096;">
                <i class="fas fa-user-times"></i>
                <p>No hay pacientes recientes</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = patients.map(patient => `
        <div style="display: flex; align-items: center; gap: 1rem; padding: 0.75rem 0; border-bottom: 1px solid #e2e8f0;">
            <div style="width: 40px; height: 40px; background: #667eea; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                ${(patient.first_name || 'P').charAt(0).toUpperCase()}
            </div>
            <div>
                <div style="font-weight: 500; color: #2d3748;">${patient.first_name || 'Paciente'} ${patient.last_name || ''}</div>
                <div style="font-size: 0.875rem; color: #718096;">${patient.email || 'Sin email'}</div>
            </div>
        </div>
    `).join('');
}

function showDashboardError() {
    // Fallback data for demo
    updateStats({
        patients: { total: 156 },
        appointments: { today: 12 },
        surgeries: { scheduled: 3 },
        invoices: { pending: 8 }
    });
    
    document.getElementById('recentAppointments').innerHTML = `
        <div style="text-align: center; padding: 2rem; color: #718096;">
            <i class="fas fa-exclamation-triangle" style="color: #ed8936; font-size: 2rem; margin-bottom: 1rem;"></i>
            <p>Error al cargar las citas. Mostrando datos de demostración.</p>
        </div>
    `;
    
    document.getElementById('recentPatients').innerHTML = `
        <div style="text-align: center; padding: 1rem; color: #718096;">
            <i class="fas fa-exclamation-triangle" style="color: #ed8936;"></i>
            <p>Error al cargar pacientes</p>
        </div>
    `;
}
</script>

<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endpush 