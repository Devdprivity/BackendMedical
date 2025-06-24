@extends('layouts.app')

@section('title', 'Configuración Inicial - MediCare Pro')

@section('content')
<div class="onboarding-container">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                
                <!-- Onboarding Header -->
                <div class="onboarding-header text-center mb-5">
                    <div class="welcome-badge mb-3">
                        <i class="fas fa-rocket"></i>
                        <span>¡Bienvenido!</span>
                    </div>
                    <h1 class="onboarding-title">Configuremos tu cuenta paso a paso</h1>
                    <p class="onboarding-subtitle">Solo tomará unos minutos configurar todo lo necesario para que puedas comenzar a atender pacientes</p>
                </div>

                <!-- Progress Bar -->
                <div class="progress-section mb-5">
                    <div class="progress-wrapper">
                        <div class="progress-bar-container">
                            <div class="progress-bar" style="width: {{ (array_sum($progress) / count($progress)) * 100 }}%"></div>
                        </div>
                        <div class="progress-text">
                            {{ array_sum($progress) }} de {{ count($progress) }} pasos completados
                        </div>
                    </div>
                </div>

                <!-- Steps Grid -->
                <div class="steps-grid">
                    @foreach($steps as $stepKey => $step)
                        <div class="step-card {{ $progress[$stepKey] ? 'completed' : ($currentStep === $stepKey ? 'current' : 'pending') }}">
                            <div class="step-card-header">
                                <div class="step-icon">
                                    @if($progress[$stepKey])
                                        <i class="fas fa-check"></i>
                                    @else
                                        <i class="{{ $step['icon'] }}"></i>
                                    @endif
                                </div>
                                <div class="step-status">
                                    @if($progress[$stepKey])
                                        <span class="badge badge-success">Completado</span>
                                    @elseif($currentStep === $stepKey)
                                        <span class="badge badge-primary">Actual</span>
                                    @else
                                        <span class="badge badge-secondary">Pendiente</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="step-content">
                                <h3 class="step-title">{{ $step['title'] }}</h3>
                                <p class="step-description">{{ $step['description'] }}</p>
                            </div>
                            
                            <div class="step-action">
                                @if($progress[$stepKey])
                                    <a href="{{ route($step['route']) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                        Editar
                                    </a>
                                @elseif($currentStep === $stepKey)
                                    <a href="{{ route($step['route']) }}" class="btn btn-primary">
                                        <i class="fas fa-arrow-right"></i>
                                        Comenzar
                                    </a>
                                @else
                                    <button class="btn btn-secondary" disabled>
                                        <i class="fas fa-lock"></i>
                                        Bloqueado
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Skip Option -->
                <div class="skip-section text-center mt-5">
                    <p class="text-muted mb-3">
                        <small>¿Ya tienes experiencia con plataformas similares?</small>
                    </p>
                    <form method="POST" action="{{ route('onboarding.skip') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-link text-muted" onclick="return confirm('¿Estás seguro de que quieres omitir la configuración inicial? Podrás configurar todo más tarde desde tu perfil.')">
                            <i class="fas fa-forward"></i>
                            Omitir configuración
                        </button>
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

.onboarding-header {
    color: white;
}

.welcome-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    font-weight: 600;
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.onboarding-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: white;
}

.onboarding-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    max-width: 600px;
    margin: 0 auto;
    color: white;
}

.progress-section {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.progress-wrapper {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.progress-bar-container {
    flex: 1;
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #28a745, #20c997);
    border-radius: 4px;
    transition: width 0.5s ease;
}

.progress-text {
    font-weight: 600;
    color: #495057;
    white-space: nowrap;
}

.steps-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.step-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.step-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.step-card.completed {
    border-color: #28a745;
    background: linear-gradient(135deg, #f8f9fa 0%, #e8f5e8 100%);
}

.step-card.current {
    border-color: #007bff;
    background: linear-gradient(135deg, #f8f9fa 0%, #e3f2fd 100%);
}

.step-card.pending {
    opacity: 0.7;
}

.step-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.step-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: white;
}

.step-card.completed .step-icon {
    background: #28a745;
}

.step-card.current .step-icon {
    background: #007bff;
}

.step-card.pending .step-icon {
    background: #6c757d;
}

.step-title {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #212529;
}

.step-description {
    color: #6c757d;
    margin-bottom: 1.5rem;
    line-height: 1.5;
}

.step-action .btn {
    width: 100%;
    padding: 0.75rem;
    font-weight: 600;
    border-radius: 8px;
}

.skip-section {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.skip-section .btn-link {
    text-decoration: none;
    color: rgba(255, 255, 255, 0.8) !important;
}

.skip-section .btn-link:hover {
    color: white !important;
    text-decoration: underline;
}

.badge {
    font-size: 0.75rem;
    padding: 0.4rem 0.8rem;
    border-radius: 12px;
}

.badge-success {
    background: #28a745;
    color: white;
}

.badge-primary {
    background: #007bff;
    color: white;
}

.badge-secondary {
    background: #6c757d;
    color: white;
}

@media (max-width: 768px) {
    .onboarding-title {
        font-size: 2rem;
    }
    
    .progress-wrapper {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .steps-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection 