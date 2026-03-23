@extends('layouts.onboarding')

@section('title', 'Paso 1: Completa tu Perfil - DrOrganiza')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Paso 1: Completa tu Perfil Profesional</h1>
        <p class="page-subtitle">Esta información ayudará a los pacientes a conocerte mejor</p>
    </div>
</div>

<!-- Progress Indicator -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <div style="display: flex; align-items: center; justify-content: center; gap: 1rem;">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--success); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">1</div>
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--gray-300); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">2</div>
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--gray-300); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">3</div>
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--gray-300); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">4</div>
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--gray-300); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">5</div>
        </div>
    </div>
</div>

<!-- Form Card -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Perfil</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('onboarding.profile.update') }}">
            @csrf
            
            <!-- Basic Information -->
            <h4 style="margin-bottom: 1.5rem; color: var(--primary); display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-user-md"></i>
                Información Básica
            </h4>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                <div class="form-group">
                    <label for="specialty" class="form-label required">Especialidad Médica</label>
                    <select class="form-control @error('specialty') is-invalid @enderror" id="specialty" name="specialty" required>
                        <option value="">Selecciona tu especialidad</option>
                        <option value="Medicina General" {{ old('specialty', $user->specialty) == 'Medicina General' ? 'selected' : '' }}>Medicina General</option>
                        <option value="Cardiología" {{ old('specialty', $user->specialty) == 'Cardiología' ? 'selected' : '' }}>Cardiología</option>
                        <option value="Dermatología" {{ old('specialty', $user->specialty) == 'Dermatología' ? 'selected' : '' }}>Dermatología</option>
                        <option value="Pediatría" {{ old('specialty', $user->specialty) == 'Pediatría' ? 'selected' : '' }}>Pediatría</option>
                        <option value="Ginecología" {{ old('specialty', $user->specialty) == 'Ginecología' ? 'selected' : '' }}>Ginecología</option>
                        <option value="Neurología" {{ old('specialty', $user->specialty) == 'Neurología' ? 'selected' : '' }}>Neurología</option>
                        <option value="Psiquiatría" {{ old('specialty', $user->specialty) == 'Psiquiatría' ? 'selected' : '' }}>Psiquiatría</option>
                        <option value="Traumatología" {{ old('specialty', $user->specialty) == 'Traumatología' ? 'selected' : '' }}>Traumatología</option>
                        <option value="Oftalmología" {{ old('specialty', $user->specialty) == 'Oftalmología' ? 'selected' : '' }}>Oftalmología</option>
                        <option value="Otorrinolaringología" {{ old('specialty', $user->specialty) == 'Otorrinolaringología' ? 'selected' : '' }}>Otorrinolaringología</option>
                        <option value="Otra" {{ old('specialty', $user->specialty) == 'Otra' ? 'selected' : '' }}>Otra</option>
                    </select>
                    @error('specialty')
                        <small class="form-text" style="color: var(--danger);">{{ $message }}</small>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="medical_license" class="form-label required">Número de Colegiatura</label>
                    <input type="text" class="form-control @error('medical_license') is-invalid @enderror" 
                           id="medical_license" name="medical_license" 
                           value="{{ old('medical_license', $user->medical_license) }}" 
                           placeholder="Ej: 12345678" required>
                    @error('medical_license')
                        <small class="form-text" style="color: var(--danger);">{{ $message }}</small>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="phone" class="form-label required">Teléfono</label>
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                           id="phone" name="phone" 
                           value="{{ old('phone', $user->phone) }}" 
                           placeholder="Ej: +34 600 000 000" required>
                    @error('phone')
                        <small class="form-text" style="color: var(--danger);">{{ $message }}</small>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="years_experience" class="form-label">Años de Experiencia</label>
                    <input type="number" class="form-control @error('years_experience') is-invalid @enderror" 
                           id="years_experience" name="years_experience" 
                           value="{{ old('years_experience', $user->years_experience) }}" 
                           placeholder="Ej: 5" min="0" max="100">
                    @error('years_experience')
                        <small class="form-text" style="color: var(--danger);">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            
            <!-- Professional Information -->
            <h4 style="margin-bottom: 1.5rem; color: var(--primary); display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-briefcase"></i>
                Información Profesional
            </h4>
            
            <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                <div class="form-group">
                    <label for="bio" class="form-label">Biografía Profesional</label>
                    <textarea class="form-control @error('bio') is-invalid @enderror" 
                              id="bio" name="bio" rows="4" 
                              placeholder="Cuéntanos sobre tu experiencia, especialidades y enfoque médico...">{{ old('bio', $user->bio) }}</textarea>
                    <small class="form-text">Esta información será visible para los pacientes. Máximo 1000 caracteres.</small>
                    @error('bio')
                        <small class="form-text" style="color: var(--danger);">{{ $message }}</small>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="consultation_fee" class="form-label">Tarifa de Consulta (€)</label>
                    <input type="number" class="form-control @error('consultation_fee') is-invalid @enderror" 
                           id="consultation_fee" name="consultation_fee" 
                           value="{{ old('consultation_fee', $user->consultation_fee) }}" 
                           placeholder="Ej: 50" min="0" step="0.01">
                    <small class="form-text">Precio por consulta en euros (opcional)</small>
                    @error('consultation_fee')
                        <small class="form-text" style="color: var(--danger);">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1.5rem; border-top: 1px solid var(--gray-200);">
                <a href="{{ route('onboarding.index') }}" class="btn btn-outline">
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

.progress-step {
    position: relative;
}

.progress-step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 100%;
    width: 1rem;
    height: 2px;
    background: var(--gray-300);
    transform: translateY(-50%);
}
</style>
@endsection 