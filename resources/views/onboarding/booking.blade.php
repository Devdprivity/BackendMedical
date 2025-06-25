@extends('layouts.app')

@section('title', 'Paso 3: Link de Citas Públicas - MediCare Pro')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Paso 3: Link de Citas Públicas</h1>
        <p class="page-subtitle">Permite que los pacientes reserven citas contigo directamente</p>
    </div>
</div>

<!-- Progress Indicator -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <div style="display: flex; align-items: center; justify-content: center; gap: 1rem;">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--success); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">✓</div>
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--success); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">✓</div>
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">3</div>
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--gray-300); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">4</div>
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--gray-300); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">5</div>
        </div>
    </div>
</div>

<!-- Form Card -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Configuración de Reservas Públicas</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('onboarding.booking.enable') }}">
            @csrf
            
            <!-- Booking URL -->
            <h4 style="margin-bottom: 1.5rem; color: var(--primary); display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-link"></i>
                Tu Link de Reservas
            </h4>
            
            <div style="margin-bottom: 2rem;">
                <div class="form-group">
                    <label for="booking_slug" class="form-label required">URL Personalizada</label>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--gray-500); white-space: nowrap;">{{ config('app.url') }}/booking/</span>
                        <input type="text" class="form-control @error('booking_slug') is-invalid @enderror" 
                               id="booking_slug" name="booking_slug" 
                               value="{{ old('booking_slug', $user->booking_slug ?? Str::slug($user->name)) }}" 
                               placeholder="tu-nombre" required>
                    </div>
                    <small class="form-text">Solo letras, números y guiones. Ej: dr-juan-perez</small>
                    @error('booking_slug')
                        <small class="form-text" style="color: var(--danger);">{{ $message }}</small>
                    @enderror
                </div>
                
                <div style="background: var(--gray-100); padding: 1rem; border-radius: 8px; margin-top: 1rem;">
                    <p style="margin: 0; color: var(--gray-600);">
                        <strong>Tu link será:</strong> 
                        <span id="preview-url" style="color: var(--primary); font-family: monospace;">
                            {{ config('app.url') }}/booking/{{ old('booking_slug', $user->booking_slug ?? Str::slug($user->name)) }}
                        </span>
                    </p>
                </div>
            </div>
            
            <!-- Booking Settings -->
            <h4 style="margin-bottom: 1.5rem; color: var(--primary); display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-cog"></i>
                Configuración de Reservas
            </h4>
            
            <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; border: 1px solid var(--gray-200); border-radius: 8px;">
                    <input type="checkbox" id="booking_enabled" name="booking_enabled" value="1" 
                           {{ old('booking_enabled', $user->booking_enabled) ? 'checked' : '' }}
                           style="margin: 0;">
                    <div>
                        <label for="booking_enabled" style="margin: 0; cursor: pointer; font-weight: 600;">
                            Habilitar reservas públicas
                        </label>
                        <p style="margin: 0; color: var(--gray-500); font-size: 0.9rem;">
                            Los pacientes podrán reservar citas contigo usando tu link público
                        </p>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="booking_message" class="form-label">Mensaje de Bienvenida</label>
                    <textarea class="form-control @error('booking_message') is-invalid @enderror" 
                              id="booking_message" name="booking_message" rows="3" 
                              placeholder="Ej: ¡Hola! Soy el Dr. Juan Pérez, especialista en cardiología. Reserva tu cita conmigo de forma fácil y rápida.">{{ old('booking_message', $user->booking_message ?? '') }}</textarea>
                    <small class="form-text">Este mensaje aparecerá en tu página de reservas</small>
                    @error('booking_message')
                        <small class="form-text" style="color: var(--danger);">{{ $message }}</small>
                    @enderror
                </div>
            </div>
            
            <!-- Preview -->
            <div style="background: var(--gray-100); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                <h5 style="color: var(--dark); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-eye"></i>
                    Vista Previa de tu Página de Reservas
                </h5>
                
                <div style="background: white; padding: 1.5rem; border-radius: 8px; border: 1px solid var(--gray-200);">
                    <div style="text-align: center; margin-bottom: 1.5rem;">
                        <div style="width: 60px; height: 60px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; margin: 0 auto 1rem;">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <h3 style="margin: 0 0 0.5rem 0; color: var(--dark);">{{ $user->name }}</h3>
                        <p style="margin: 0; color: var(--gray-500);">{{ $user->specialty ?? 'Médico General' }}</p>
                    </div>
                    
                    <div id="preview-message" style="color: var(--gray-600); text-align: center; margin-bottom: 1.5rem; font-style: italic;">
                        {{ old('booking_message', $user->booking_message ?? 'Mensaje de bienvenida aparecerá aquí...') }}
                    </div>
                    
                    <div style="text-align: center;">
                        <button type="button" class="btn btn-primary" disabled>
                            <i class="fas fa-calendar-plus"></i>
                            Reservar Cita
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1.5rem; border-top: 1px solid var(--gray-200);">
                <a href="{{ route('onboarding.schedule') }}" class="btn btn-outline">
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
    const slugInput = document.getElementById('booking_slug');
    const previewUrl = document.getElementById('preview-url');
    const messageInput = document.getElementById('booking_message');
    const previewMessage = document.getElementById('preview-message');
    const baseUrl = '{{ config('app.url') }}/booking/';
    
    // Update URL preview
    function updateUrlPreview() {
        const slug = slugInput.value || 'tu-nombre';
        previewUrl.textContent = baseUrl + slug;
    }
    
    // Update message preview
    function updateMessagePreview() {
        const message = messageInput.value || 'Mensaje de bienvenida aparecerá aquí...';
        previewMessage.textContent = message;
    }
    
    // Clean slug input
    function cleanSlug() {
        let value = slugInput.value;
        value = value.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
                    .replace(/\s+/g, '-') // Replace spaces with hyphens
                    .replace(/-+/g, '-') // Replace multiple hyphens with single
                    .replace(/^-|-$/g, ''); // Remove leading/trailing hyphens
        slugInput.value = value;
        updateUrlPreview();
    }
    
    // Event listeners
    slugInput.addEventListener('input', cleanSlug);
    messageInput.addEventListener('input', updateMessagePreview);
    
    // Initial updates
    updateUrlPreview();
    updateMessagePreview();
});
</script>
@endsection 