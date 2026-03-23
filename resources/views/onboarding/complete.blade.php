@extends('layouts.onboarding')

@section('title', '¡Felicitaciones! - MediCare Pro')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">¡Felicitaciones!</h1>
        <p class="page-subtitle">Tu cuenta está completamente configurada y lista para usar</p>
    </div>
</div>

<!-- Success Card -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body" style="text-align: center; padding: 3rem 2rem;">
        <!-- Success Icon -->
        <div style="width: 80px; height: 80px; background: var(--success); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem;">
            <i class="fas fa-check" style="font-size: 2rem; color: white;"></i>
        </div>
        
        <h2 style="color: var(--success); margin-bottom: 1rem;">¡Todo listo para comenzar!</h2>
        <p style="color: var(--gray-500); font-size: 1.1rem; max-width: 500px; margin: 0 auto 2rem;">Has completado exitosamente la configuración inicial. Tu cuenta de MediCare Pro está lista para usar.</p>
        
        <!-- Action Button -->
        <form method="POST" action="{{ route('onboarding.finish') }}">
            @csrf
            <button type="submit" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1.1rem;">
                <i class="fas fa-play-circle"></i>
                ¡Comenzar a usar MediCare Pro!
            </button>
        </form>
    </div>
</div>

<!-- Next Steps -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-rocket" style="color: var(--primary);"></i>
            ¿Qué puedes hacer ahora?
        </h3>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
            <div style="text-align: center; padding: 1.5rem; border: 1px solid var(--gray-200); border-radius: 12px; transition: var(--transition);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                <div style="width: 50px; height: 50px; background: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; margin: 0 auto 1rem;">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h5 style="color: var(--dark); margin-bottom: 0.5rem;">Gestionar Citas</h5>
                <p style="color: var(--gray-500); font-size: 0.9rem; margin: 0;">Programa y administra citas con tus pacientes</p>
            </div>
            
            <div style="text-align: center; padding: 1.5rem; border: 1px solid var(--gray-200); border-radius: 12px; transition: var(--transition);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                <div style="width: 50px; height: 50px; background: var(--secondary); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; margin: 0 auto 1rem;">
                    <i class="fas fa-users"></i>
                </div>
                <h5 style="color: var(--dark); margin-bottom: 0.5rem;">Ver Pacientes</h5>
                <p style="color: var(--gray-500); font-size: 0.9rem; margin: 0;">Accede al historial médico completo</p>
            </div>
            
            <div style="text-align: center; padding: 1.5rem; border: 1px solid var(--gray-200); border-radius: 12px; transition: var(--transition);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                <div style="width: 50px; height: 50px; background: var(--accent); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; margin: 0 auto 1rem;">
                    <i class="fas fa-video"></i>
                </div>
                <h5 style="color: var(--dark); margin-bottom: 0.5rem;">Videollamadas</h5>
                <p style="color: var(--gray-500); font-size: 0.9rem; margin: 0;">Consultas virtuales instantáneas</p>
            </div>
            
            <div style="text-align: center; padding: 1.5rem; border: 1px solid var(--gray-200); border-radius: 12px; transition: var(--transition);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                <div style="width: 50px; height: 50px; background: var(--warning); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; margin: 0 auto 1rem;">
                    <i class="fas fa-share-alt"></i>
                </div>
                <h5 style="color: var(--dark); margin-bottom: 0.5rem;">Compartir Enlace</h5>
                <p style="color: var(--gray-500); font-size: 0.9rem; margin: 0;">Comparte tu enlace de reservas</p>
            </div>
        </div>
    </div>
</div>
@endsection 