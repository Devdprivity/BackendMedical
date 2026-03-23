@extends('layouts.onboarding')

@section('title', 'Configuración Inicial - MediCare Pro')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">¡Bienvenido a MediCare Pro!</h1>
        <p class="page-subtitle">Configuremos tu cuenta paso a paso para comenzar</p>
    </div>
</div>

<!-- Progress Section -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-rocket"></i>
            </div>
            <div>
                <h4 style="margin: 0; color: var(--dark);">Progreso de Configuración</h4>
                <p style="margin: 0; color: var(--gray-500); font-size: 0.875rem;">{{ array_sum($progress) }} de {{ count($progress) }} pasos completados</p>
            </div>
        </div>
        
        <div style="background: var(--gray-200); height: 8px; border-radius: 4px; overflow: hidden;">
            <div style="width: {{ (array_sum($progress) / count($progress)) * 100 }}%; height: 100%; background: linear-gradient(90deg, var(--primary), var(--secondary)); transition: width 0.5s ease;"></div>
        </div>
    </div>
</div>

<!-- Steps Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
    @foreach($steps as $stepKey => $step)
        <div class="card {{ $progress[$stepKey] ? 'step-completed' : ($currentStep === $stepKey ? 'step-current' : 'step-pending') }}">
            <div class="card-body">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                    <div style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; color: white;
                        @if($progress[$stepKey])
                            background: var(--success);
                        @elseif($currentStep === $stepKey)
                            background: var(--primary);
                        @else
                            background: var(--gray-400);
                        @endif
                    ">
                        @if($progress[$stepKey])
                            <i class="fas fa-check"></i>
                        @else
                            <i class="{{ $step['icon'] }}"></i>
                        @endif
                    </div>
                    
                    <div>
                        @if($progress[$stepKey])
                            <span class="status-badge" style="background: var(--success); color: white;">Completado</span>
                        @elseif($currentStep === $stepKey)
                            <span class="status-badge" style="background: var(--primary); color: white;">Actual</span>
                        @else
                            <span class="status-badge" style="background: var(--gray-400); color: white;">Pendiente</span>
                        @endif
                    </div>
                </div>
                
                <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--dark);">{{ $step['title'] }}</h3>
                <p style="color: var(--gray-500); margin-bottom: 1.5rem; line-height: 1.5;">{{ $step['description'] }}</p>
                
                <div>
                    @if($progress[$stepKey])
                        <a href="{{ route($step['route']) }}" class="btn btn-outline">
                            <i class="fas fa-edit"></i>
                            Editar
                        </a>
                    @elseif($currentStep === $stepKey)
                        <a href="{{ route($step['route']) }}" class="btn btn-primary">
                            <i class="fas fa-arrow-right"></i>
                            Comenzar
                        </a>
                    @else
                        <button class="btn btn-outline" disabled style="opacity: 0.5;">
                            <i class="fas fa-lock"></i>
                            Bloqueado
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Skip Option -->
<div class="card" style="margin-top: 2rem;">
    <div class="card-body" style="text-align: center;">
        <p style="color: var(--gray-500); margin-bottom: 1rem;">
            <small>¿Ya tienes experiencia con plataformas similares?</small>
        </p>
        <form method="POST" action="{{ route('onboarding.skip') }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-outline" onclick="return confirm('¿Estás seguro de que quieres omitir la configuración inicial? Podrás configurar todo más tarde desde tu perfil.')">
                <i class="fas fa-forward"></i>
                Omitir configuración
            </button>
        </form>
    </div>
</div>

<style>
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.step-completed {
    border: 2px solid var(--success);
}

.step-current {
    border: 2px solid var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 83, 155, 0.1);
}

.step-pending {
    opacity: 0.7;
}

.card {
    transition: var(--transition);
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}
</style>
@endsection 