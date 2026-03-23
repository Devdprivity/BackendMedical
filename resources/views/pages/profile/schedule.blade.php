@extends('layouts.app')

@section('title', 'Configurar Horarios - DrOrganiza')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-calendar-alt"></i>
            Configurar Horarios de Disponibilidad
        </h1>
        <p class="page-subtitle">Define tus horarios de trabajo para las reservas públicas</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('profile.show') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i>
            Volver al Perfil
        </a>
    </div>
</div>

@if(!$user->specialty || !$user->consultation_fee)
<div class="alert alert-warning" style="margin-bottom: 2rem;">
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Información Incompleta</strong>
    </div>
    <p style="margin: 0;">
        Antes de configurar tus horarios, debes completar tu 
        <a href="{{ route('profile.edit') }}" style="color: var(--warning-dark); text-decoration: underline;">especialidad y precio de consulta</a>.
    </p>
</div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-clock"></i>
            Horarios de Trabajo
        </h3>
        <p style="color: var(--gray-600); margin: 0; font-size: 0.875rem;">
            Configura los días y horarios en los que estás disponible para consultas
        </p>
    </div>
    <div class="card-body">
        <form id="scheduleForm">
            <!-- Work Days -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-calendar-week"></i>
                    Días de Trabajo *
                </label>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 0.5rem; margin-top: 0.5rem;">
                    @php
                        $days = [
                            'monday' => 'Lunes',
                            'tuesday' => 'Martes', 
                            'wednesday' => 'Miércoles',
                            'thursday' => 'Jueves',
                            'friday' => 'Viernes',
                            'saturday' => 'Sábado',
                            'sunday' => 'Domingo'
                        ];
                        $userWorkDays = json_decode($user->work_days ?? '[]', true);
                    @endphp
                    
                    @foreach($days as $day => $label)
                    <label class="day-checkbox">
                        <input type="checkbox" name="work_days[]" value="{{ $day }}" 
                               {{ in_array($day, $userWorkDays) ? 'checked' : '' }}>
                        <span class="checkmark">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
                <small style="color: var(--gray-600); font-size: 0.875rem;">
                    Selecciona los días en los que estarás disponible para consultas
                </small>
            </div>

            <!-- Schedule Times -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-sun"></i>
                        Hora de Inicio *
                    </label>
                    <input type="time" class="form-control" name="schedule_start" 
                           value="{{ $user->schedule_start }}" required>
                    <small style="color: var(--gray-600); font-size: 0.875rem;">
                        Hora en que inicias tu jornada laboral
                    </small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-moon"></i>
                        Hora de Fin *
                    </label>
                    <input type="time" class="form-control" name="schedule_end" 
                           value="{{ $user->schedule_end }}" required>
                    <small style="color: var(--gray-600); font-size: 0.875rem;">
                        Hora en que terminas tu jornada laboral
                    </small>
                </div>
            </div>

            <!-- Break Times -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-coffee"></i>
                        Inicio de Descanso
                    </label>
                    <input type="time" class="form-control" name="break_start" 
                           value="{{ $user->break_start }}">
                    <small style="color: var(--gray-600); font-size: 0.875rem;">
                        Opcional: Hora de inicio del descanso/almuerzo
                    </small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-coffee"></i>
                        Fin de Descanso
                    </label>
                    <input type="time" class="form-control" name="break_end" 
                           value="{{ $user->break_end }}">
                    <small style="color: var(--gray-600); font-size: 0.875rem;">
                        Opcional: Hora de fin del descanso/almuerzo
                    </small>
                </div>
            </div>

            <!-- Consultation Duration -->
            <div class="form-group" style="margin-top: 1.5rem;">
                <label class="form-label">
                    <i class="fas fa-hourglass-half"></i>
                    Duración de Consulta *
                </label>
                <select class="form-control" name="consultation_duration" required>
                    <option value="15" {{ ($user->consultation_duration ?? 30) == 15 ? 'selected' : '' }}>15 minutos</option>
                    <option value="30" {{ ($user->consultation_duration ?? 30) == 30 ? 'selected' : '' }}>30 minutos</option>
                    <option value="45" {{ ($user->consultation_duration ?? 30) == 45 ? 'selected' : '' }}>45 minutos</option>
                    <option value="60" {{ ($user->consultation_duration ?? 30) == 60 ? 'selected' : '' }}>60 minutos</option>
                </select>
                <small style="color: var(--gray-600); font-size: 0.875rem;">
                    Tiempo promedio que dedicas a cada consulta
                </small>
            </div>

            <!-- Preview Section -->
            <div style="border-top: 1px solid var(--gray-200); margin-top: 2rem; padding-top: 2rem;">
                <h4 style="margin-bottom: 1rem; color: var(--primary);">
                    <i class="fas fa-eye"></i>
                    Vista Previa de Disponibilidad
                </h4>
                <div id="schedulePreview" style="background: var(--gray-50); padding: 1.5rem; border-radius: 8px;">
                    <p style="color: var(--gray-500); text-align: center;">
                        Configura tus horarios para ver la vista previa
                    </p>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="fas fa-save"></i>
                    Guardar Horarios
                </button>
                
                @if($user->specialty && $user->consultation_fee && $user->schedule_start && $user->schedule_end)
                <button type="button" class="btn btn-success" onclick="enableBookingAfterSchedule()" style="flex: 1;">
                    <i class="fas fa-rocket"></i>
                    Activar Reservas Públicas
                </button>
                @endif
            </div>
        </form>
    </div>
</div>

<style>
.day-checkbox {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    border: 2px solid var(--gray-200);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.day-checkbox:hover {
    border-color: var(--primary);
    background: rgba(102, 126, 234, 0.05);
}

.day-checkbox input[type="checkbox"] {
    display: none;
}

.day-checkbox input[type="checkbox"]:checked + .checkmark {
    color: var(--primary);
    font-weight: 600;
}

.day-checkbox input[type="checkbox"]:checked {
    & + .checkmark {
        color: var(--primary);
        font-weight: 600;
    }
    
    & ~ {
        border-color: var(--primary);
        background: rgba(102, 126, 234, 0.1);
    }
}

.day-checkbox:has(input:checked) {
    border-color: var(--primary);
    background: rgba(102, 126, 234, 0.1);
}

.checkmark {
    font-size: 0.875rem;
    color: var(--gray-700);
    transition: all 0.3s ease;
}

.alert {
    padding: 1rem 1.5rem;
    border-radius: 8px;
    border: 1px solid;
}

.alert-warning {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning-dark, #92400e);
    border-color: rgba(245, 158, 11, 0.2);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('scheduleForm');
    const inputs = form.querySelectorAll('input, select');
    
    // Update preview when any input changes
    inputs.forEach(input => {
        input.addEventListener('change', updatePreview);
    });
    
    // Initial preview update
    updatePreview();
    
    form.addEventListener('submit', handleSubmit);
});

function updatePreview() {
    const workDays = Array.from(document.querySelectorAll('input[name="work_days[]"]:checked'))
        .map(cb => cb.value);
    const scheduleStart = document.querySelector('input[name="schedule_start"]').value;
    const scheduleEnd = document.querySelector('input[name="schedule_end"]').value;
    const breakStart = document.querySelector('input[name="break_start"]').value;
    const breakEnd = document.querySelector('input[name="break_end"]').value;
    const duration = document.querySelector('select[name="consultation_duration"]').value;
    
    const preview = document.getElementById('schedulePreview');
    
    if (workDays.length === 0 || !scheduleStart || !scheduleEnd) {
        preview.innerHTML = '<p style="color: var(--gray-500); text-align: center;">Configura tus horarios para ver la vista previa</p>';
        return;
    }
    
    const dayNames = {
        'monday': 'Lunes',
        'tuesday': 'Martes',
        'wednesday': 'Miércoles', 
        'thursday': 'Jueves',
        'friday': 'Viernes',
        'saturday': 'Sábado',
        'sunday': 'Domingo'
    };
    
    const workDaysText = workDays.map(day => dayNames[day]).join(', ');
    const breakText = breakStart && breakEnd ? 
        `<br><strong>Descanso:</strong> ${breakStart} - ${breakEnd}` : '';
    
    preview.innerHTML = `
        <div style="display: grid; gap: 0.75rem;">
            <div><strong>Días de trabajo:</strong> ${workDaysText}</div>
            <div><strong>Horario:</strong> ${scheduleStart} - ${scheduleEnd} ${breakText}</div>
            <div><strong>Duración por consulta:</strong> ${duration} minutos</div>
        </div>
    `;
}

async function handleSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    // Convert work_days array to proper format
    const workDays = formData.getAll('work_days[]');
    data.work_days = workDays;
    
    try {
        const response = await fetch('{{ route("profile.update-schedule") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('success', result.message);
            // Reload page to update the "Activar Reservas" button
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showAlert('error', result.message || 'Error al guardar los horarios');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('error', 'Error al guardar los horarios');
    }
}

async function enableBookingAfterSchedule() {
    try {
        const response = await fetch('/api/users/{{ $user->id }}/enable-booking', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('success', '¡Reservas públicas activadas exitosamente!');
            setTimeout(() => {
                window.location.href = '{{ route("profile.show") }}';
            }, 1500);
        } else {
            showAlert('error', result.message || 'Error al activar las reservas');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('error', 'Error al activar las reservas');
    }
}

function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert-notification');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create new alert
    const alert = document.createElement('div');
    alert.className = `alert-notification alert-${type}`;
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
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        alert.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => alert.remove(), 300);
    }, 5000);
}
</script>
@endsection 