@extends('layouts.onboarding')

@section('title', 'Paso 2: Configura tu Horario - MediCare Pro')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Paso 2: Configura tu Horario de Atención</h1>
        <p class="page-subtitle">Define cuándo estarás disponible para consultas</p>
    </div>
</div>

<!-- Progress Indicator -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <div style="display: flex; align-items: center; justify-content: center; gap: 1rem;">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--success); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">✓</div>
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">2</div>
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--gray-300); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">3</div>
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--gray-300); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">4</div>
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--gray-300); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">5</div>
        </div>
    </div>
</div>

<!-- Form Card -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Horario de Atención</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('onboarding.schedule.update') }}">
            @csrf
            
            <!-- Basic Schedule -->
            <h4 style="margin-bottom: 1.5rem; color: var(--primary); display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-clock"></i>
                Horario General
            </h4>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                <div class="form-group">
                    <label for="schedule_start" class="form-label required">Hora de Inicio</label>
                    <input type="time" class="form-control @error('schedule_start') is-invalid @enderror" 
                           id="schedule_start" name="schedule_start" 
                           value="{{ old('schedule_start', $user->schedule_start ?? '09:00') }}" required>
                    @error('schedule_start')
                        <small class="form-text" style="color: var(--danger);">{{ $message }}</small>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="schedule_end" class="form-label required">Hora de Fin</label>
                    <input type="time" class="form-control @error('schedule_end') is-invalid @enderror" 
                           id="schedule_end" name="schedule_end" 
                           value="{{ old('schedule_end', $user->schedule_end ?? '17:00') }}" required>
                    @error('schedule_end')
                        <small class="form-text" style="color: var(--danger);">{{ $message }}</small>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="consultation_duration" class="form-label required">Duración por Consulta (minutos)</label>
                    <select class="form-control @error('consultation_duration') is-invalid @enderror" 
                            id="consultation_duration" name="consultation_duration" required>
                        <option value="15" {{ old('consultation_duration', $user->consultation_duration) == '15' ? 'selected' : '' }}>15 minutos</option>
                        <option value="30" {{ old('consultation_duration', $user->consultation_duration) == '30' ? 'selected' : '' }}>30 minutos</option>
                        <option value="45" {{ old('consultation_duration', $user->consultation_duration) == '45' ? 'selected' : '' }}>45 minutos</option>
                        <option value="60" {{ old('consultation_duration', $user->consultation_duration) == '60' ? 'selected' : '' }}>60 minutos</option>
                    </select>
                    @error('consultation_duration')
                        <small class="form-text" style="color: var(--danger);">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            
            <!-- Work Days -->
            <h4 style="margin-bottom: 1.5rem; color: var(--primary); display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-calendar-week"></i>
                Días de Trabajo
            </h4>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                @php
                    $days = ['monday' => 'Lunes', 'tuesday' => 'Martes', 'wednesday' => 'Miércoles', 'thursday' => 'Jueves', 'friday' => 'Viernes', 'saturday' => 'Sábado', 'sunday' => 'Domingo'];
                    $userWorkDays = old('work_days', $user->work_days ? json_decode($user->work_days, true) : ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
                @endphp
                
                @foreach($days as $dayKey => $dayName)
                    <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; border: 1px solid var(--gray-200); border-radius: 8px;">
                        <input type="checkbox" id="work_day_{{ $dayKey }}" name="work_days[]" value="{{ $dayKey }}" 
                               {{ in_array($dayKey, $userWorkDays) ? 'checked' : '' }}
                               style="margin: 0;">
                        <label for="work_day_{{ $dayKey }}" style="margin: 0; cursor: pointer;">{{ $dayName }}</label>
                    </div>
                @endforeach
            </div>
            
            <!-- Break Time -->
            <h4 style="margin-bottom: 1.5rem; color: var(--primary); display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-coffee"></i>
                Horario de Descanso (Opcional)
            </h4>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                <div class="form-group">
                    <label for="break_start" class="form-label">Inicio del Descanso</label>
                    <input type="time" class="form-control @error('break_start') is-invalid @enderror" 
                           id="break_start" name="break_start" 
                           value="{{ old('break_start', $user->break_start ?? '13:00') }}">
                    @error('break_start')
                        <small class="form-text" style="color: var(--danger);">{{ $message }}</small>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="break_end" class="form-label">Fin del Descanso</label>
                    <input type="time" class="form-control @error('break_end') is-invalid @enderror" 
                           id="break_end" name="break_end" 
                           value="{{ old('break_end', $user->break_end ?? '14:00') }}">
                    @error('break_end')
                        <small class="form-text" style="color: var(--danger);">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            
            <!-- Schedule Preview -->
            <div style="background: var(--gray-100); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                <h5 style="color: var(--dark); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-eye"></i>
                    Vista Previa del Horario
                </h5>
                <div id="schedule-preview" style="color: var(--gray-600);">
                    <p>Selecciona tus horarios para ver la vista previa</p>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1.5rem; border-top: 1px solid var(--gray-200);">
                <a href="{{ route('onboarding.profile') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i>
                    Volver
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-arrow-right"></i>
                    Continuar
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.form-label.required::after {
    content: '*';
    color: var(--danger);
    margin-left: 0.2rem;
}

.form-control.is-invalid {
    border-color: var(--danger);
}

.form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 2px rgba(0, 83, 155, 0.1);
}

input[type="checkbox"] {
    accent-color: var(--primary);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update schedule preview when inputs change
    function updatePreview() {
        const start = document.getElementById('schedule_start').value;
        const end = document.getElementById('schedule_end').value;
        const duration = document.getElementById('consultation_duration').value;
        const breakStart = document.getElementById('break_start').value;
        const breakEnd = document.getElementById('break_end').value;
        
        const checkedDays = Array.from(document.querySelectorAll('input[name="work_days[]"]:checked'))
            .map(cb => cb.nextElementSibling.textContent);
        
        const preview = document.getElementById('schedule-preview');
        
        if (start && end && duration && checkedDays.length > 0) {
            let html = `
                <div style="display: grid; gap: 0.5rem;">
                    <div><strong>Días de trabajo:</strong> ${checkedDays.join(', ')}</div>
                    <div><strong>Horario:</strong> ${start} - ${end}</div>
                    <div><strong>Duración por consulta:</strong> ${duration} minutos</div>
            `;
            
            if (breakStart && breakEnd) {
                html += `<div><strong>Descanso:</strong> ${breakStart} - ${breakEnd}</div>`;
            }
            
            html += `</div>`;
            preview.innerHTML = html;
        } else {
            preview.innerHTML = '<p>Selecciona tus horarios para ver la vista previa</p>';
        }
    }
    
    // Add event listeners
    ['schedule_start', 'schedule_end', 'consultation_duration', 'break_start', 'break_end'].forEach(id => {
        document.getElementById(id).addEventListener('change', updatePreview);
    });
    
    document.querySelectorAll('input[name="work_days[]"]').forEach(cb => {
        cb.addEventListener('change', updatePreview);
    });
    
    // Initial preview
    updatePreview();
});
</script>
@endsection 