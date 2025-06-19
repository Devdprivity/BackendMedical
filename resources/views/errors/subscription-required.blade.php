@extends('layouts.app')

@section('title', 'Actualización de Plan Requerida - MediCare Pro')

@section('content')
<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 60vh; text-align: center; padding: 2rem;">
    <div style="background: linear-gradient(135deg, #f59e0b, #d97706); width: 120px; height: 120px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 2rem; box-shadow: 0 20px 40px rgba(245, 158, 11, 0.3);">
        <i class="fas fa-crown" style="font-size: 3rem; color: white;"></i>
    </div>
    
    <h1 style="font-size: 2.5rem; font-weight: 700; color: var(--dark); margin-bottom: 1rem;">
        🚀 Funcionalidad Premium
    </h1>
    
    <p style="font-size: 1.25rem; color: var(--gray-600); margin-bottom: 2rem; max-width: 600px; line-height: 1.6;">
        <strong>{{ $message ?? 'La gestión de medicamentos e inventario' }}</strong> es una funcionalidad avanzada disponible solo en nuestros planes profesionales.
    </p>
    
    <div style="background: var(--gray-50); border-radius: 12px; padding: 2rem; margin-bottom: 2rem; max-width: 500px;">
        <h3 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.25rem;">
            <i class="fas fa-info-circle"></i>
            Tu Plan Actual
        </h3>
        <div style="background: white; border-radius: 8px; padding: 1rem; border-left: 4px solid var(--warning);">
            <strong>{{ $current_plan ?? 'Plan Gratuito' }}</strong>
            <p style="margin: 0.5rem 0 0 0; color: var(--gray-600); font-size: 0.875rem;">
                Perfecto para comenzar, pero con funcionalidades limitadas
            </p>
        </div>
    </div>
    
    <div style="background: linear-gradient(135deg, rgba(0, 83, 155, 0.1), rgba(0, 83, 155, 0.05)); border-radius: 12px; padding: 2rem; margin-bottom: 2rem; max-width: 600px;">
        <h3 style="color: var(--primary); margin-bottom: 1.5rem; font-size: 1.25rem;">
            <i class="fas fa-pills"></i>
            ¿Qué incluye la Gestión de Medicamentos?
        </h3>
        <div style="display: grid; gap: 1rem; text-align: left;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-check-circle" style="color: var(--success); font-size: 1.25rem;"></i>
                <span>Control completo de inventario de medicamentos</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-check-circle" style="color: var(--success); font-size: 1.25rem;"></i>
                <span>Alertas de stock bajo y medicamentos por vencer</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-check-circle" style="color: var(--success); font-size: 1.25rem;"></i>
                <span>Gestión de precios y costos</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-check-circle" style="color: var(--success); font-size: 1.25rem;"></i>
                <span>Reportes de medicamentos y ventas</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-check-circle" style="color: var(--success); font-size: 1.25rem;"></i>
                <span>Integración con prescripciones médicas</span>
            </div>
        </div>
    </div>
    
    <div style="display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center;">
        <a href="{{ route('subscription.plans') }}" class="btn btn-primary" style="font-size: 1.125rem; padding: 1rem 2rem;">
            <i class="fas fa-rocket"></i>
            Ver Planes y Precios
        </a>
        
        <a href="{{ route('dashboard') }}" class="btn btn-outline" style="font-size: 1.125rem; padding: 1rem 2rem;">
            <i class="fas fa-arrow-left"></i>
            Volver al Dashboard
        </a>
    </div>
    
    <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--gray-200); max-width: 600px;">
        <p style="color: var(--gray-500); font-size: 0.875rem; margin-bottom: 1rem;">
            ¿Tienes preguntas sobre nuestros planes?
        </p>
        <div style="display: flex; gap: 1.5rem; justify-content: center; flex-wrap: wrap;">
            <a href="mailto:soporte@medicarepo.com" style="color: var(--primary); text-decoration: none; font-weight: 500;">
                <i class="fas fa-envelope"></i>
                Contactar Soporte
            </a>
            <a href="tel:+1234567890" style="color: var(--primary); text-decoration: none; font-weight: 500;">
                <i class="fas fa-phone"></i>
                Llamar Ahora
            </a>
            <a href="#" style="color: var(--primary); text-decoration: none; font-weight: 500;">
                <i class="fas fa-comments"></i>
                Chat en Vivo
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
@media (max-width: 768px) {
    .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>
@endpush
@endsection 