@extends('layouts.app')

@section('title', 'Paso 1: Completa tu Perfil - MediCare Pro')

@section('content')
<div class="onboarding-container">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-6">
                
                <!-- Step Header -->
                <div class="step-header text-center mb-4">
                    <div class="step-number">1</div>
                    <h2 class="step-title">Completa tu Perfil Profesional</h2>
                    <p class="step-subtitle">Esta información ayudará a los pacientes a conocerte mejor</p>
                </div>

                <!-- Progress Indicator -->
                <div class="progress-indicator mb-4">
                    <div class="progress-step active">1</div>
                    <div class="progress-step">2</div>
                    <div class="progress-step">3</div>
                    <div class="progress-step">4</div>
                    <div class="progress-step">5</div>
                </div>

                <!-- Form Card -->
                <div class="form-card">
                    <form method="POST" action="{{ route('onboarding.profile.update') }}">
                        @csrf
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-12 mb-4">
                                <h5 class="section-title">
                                    <i class="fas fa-user-md text-primary"></i>
                                    Información Básica
                                </h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="specialty" class="form-label required">Especialidad Médica</label>
                                <select class="form-select @error('specialty') is-invalid @enderror" id="specialty" name="specialty" required>
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
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="medical_license" class="form-label required">Número de Colegiatura</label>
                                <input type="text" class="form-control @error('medical_license') is-invalid @enderror" 
                                       id="medical_license" name="medical_license" 
                                       value="{{ old('medical_license', $user->medical_license) }}" 
                                       placeholder="Ej: 12345678" required>
                                @error('medical_license')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label required">Teléfono</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" 
                                       value="{{ old('phone', $user->phone) }}" 
                                       placeholder="Ej: +34 600 000 000" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="years_experience" class="form-label">Años de Experiencia</label>
                                <input type="number" class="form-control @error('years_experience') is-invalid @enderror" 
                                       id="years_experience" name="years_experience" 
                                       value="{{ old('years_experience', $user->years_experience) }}" 
                                       placeholder="Ej: 5" min="0" max="100">
                                @error('years_experience')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Professional Information -->
                            <div class="col-12 mb-4 mt-3">
                                <h5 class="section-title">
                                    <i class="fas fa-briefcase text-primary"></i>
                                    Información Profesional
                                </h5>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="bio" class="form-label">Biografía Profesional</label>
                                <textarea class="form-control @error('bio') is-invalid @enderror" 
                                          id="bio" name="bio" rows="4" 
                                          placeholder="Cuéntanos sobre tu experiencia, especialidades y enfoque médico...">{{ old('bio', $user->bio) }}</textarea>
                                <div class="form-text">Esta información será visible para los pacientes. Máximo 1000 caracteres.</div>
                                @error('bio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="consultation_fee" class="form-label">Tarifa de Consulta (€)</label>
                                <input type="number" class="form-control @error('consultation_fee') is-invalid @enderror" 
                                       id="consultation_fee" name="consultation_fee" 
                                       value="{{ old('consultation_fee', $user->consultation_fee) }}" 
                                       placeholder="Ej: 50" min="0" step="0.01">
                                <div class="form-text">Precio por consulta en euros (opcional)</div>
                                @error('consultation_fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="form-actions">
                            <a href="{{ route('onboarding.index') }}" class="btn btn-outline-secondary">
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
        </div>
    </div>
</div>

<style>
.onboarding-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem 0;
}

.step-header {
    color: white;
    margin-bottom: 2rem;
}

.step-number {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0 auto 1rem;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.step-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.step-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
}

.progress-indicator {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.progress-step {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
    position: relative;
}

.progress-step.active {
    background: #28a745;
    box-shadow: 0 0 20px rgba(40, 167, 69, 0.5);
}

.progress-step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 100%;
    width: 1rem;
    height: 2px;
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-50%);
}

.form-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.section-title {
    color: #495057;
    font-weight: 600;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e9ecef;
}

.section-title i {
    margin-right: 0.5rem;
}

.form-label.required::after {
    content: '*';
    color: #dc3545;
    margin-left: 0.2rem;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    padding: 0.75rem;
    font-size: 0.95rem;
}

.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e9ecef;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
}

.btn-outline-secondary {
    border: 1px solid #6c757d;
    color: #6c757d;
}

.btn-outline-secondary:hover {
    background: #6c757d;
    color: white;
}

@media (max-width: 768px) {
    .form-card {
        padding: 1.5rem;
    }
    
    .step-title {
        font-size: 1.5rem;
    }
    
    .form-actions {
        flex-direction: column;
        gap: 1rem;
    }
    
    .form-actions .btn {
        width: 100%;
    }
}
</style>
@endsection 