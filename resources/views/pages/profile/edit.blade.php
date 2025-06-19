@extends('layouts.app')

@section('title', 'Editar Perfil - MediCare Pro')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-user-edit"></i>
            Editar Perfil
        </h1>
        <p class="page-subtitle">Actualiza tu información personal y configuración</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('profile.show') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i>
            Volver al Perfil
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <!-- Update Profile Information -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Información Personal</h3>
        </div>
        <div class="card-body">
            <form id="profileForm">
                <div class="form-group">
                    <label class="form-label">Nombre Completo</label>
                    <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Rol</label>
                    <input type="text" class="form-control" value="{{ ucfirst($user->role ?? 'Usuario') }}" readonly style="background: var(--gray-100);">
                    <small style="color: var(--gray-600); font-size: 0.875rem;">El rol solo puede ser cambiado por un administrador</small>
                </div>
                
                @if($user->role === 'doctor')
                <!-- Professional Information for Doctors -->
                <div style="border-top: 1px solid var(--gray-200); margin: 1.5rem 0; padding-top: 1.5rem;">
                    <h4 style="margin-bottom: 1rem; color: var(--primary);">
                        <i class="fas fa-stethoscope"></i>
                        Información Profesional
                    </h4>
                    
                    <div class="form-group">
                        <label class="form-label">Especialidad *</label>
                        <select class="form-control" name="specialty" required>
                            <option value="">Seleccionar especialidad...</option>
                            <option value="Medicina General" {{ ($user->specialty ?? '') === 'Medicina General' ? 'selected' : '' }}>Medicina General</option>
                            <option value="Cardiología" {{ ($user->specialty ?? '') === 'Cardiología' ? 'selected' : '' }}>Cardiología</option>
                            <option value="Dermatología" {{ ($user->specialty ?? '') === 'Dermatología' ? 'selected' : '' }}>Dermatología</option>
                            <option value="Endocrinología" {{ ($user->specialty ?? '') === 'Endocrinología' ? 'selected' : '' }}>Endocrinología</option>
                            <option value="Gastroenterología" {{ ($user->specialty ?? '') === 'Gastroenterología' ? 'selected' : '' }}>Gastroenterología</option>
                            <option value="Ginecología" {{ ($user->specialty ?? '') === 'Ginecología' ? 'selected' : '' }}>Ginecología</option>
                            <option value="Neurología" {{ ($user->specialty ?? '') === 'Neurología' ? 'selected' : '' }}>Neurología</option>
                            <option value="Oftalmología" {{ ($user->specialty ?? '') === 'Oftalmología' ? 'selected' : '' }}>Oftalmología</option>
                            <option value="Ortopedia" {{ ($user->specialty ?? '') === 'Ortopedia' ? 'selected' : '' }}>Ortopedia</option>
                            <option value="Pediatría" {{ ($user->specialty ?? '') === 'Pediatría' ? 'selected' : '' }}>Pediatría</option>
                            <option value="Psiquiatría" {{ ($user->specialty ?? '') === 'Psiquiatría' ? 'selected' : '' }}>Psiquiatría</option>
                            <option value="Urología" {{ ($user->specialty ?? '') === 'Urología' ? 'selected' : '' }}>Urología</option>
                        </select>
                        <small style="color: var(--gray-600); font-size: 0.875rem;">Esta información aparecerá en tu página de reservas públicas</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Precio por Consulta *</label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--gray-500);">$</span>
                            <input type="number" class="form-control" name="consultation_fee" 
                                   value="{{ $user->consultation_fee ?? '' }}" 
                                   placeholder="0.00" 
                                   step="0.01" 
                                   min="0" 
                                   style="padding-left: 35px;" 
                                   required>
                        </div>
                        <small style="color: var(--gray-600); font-size: 0.875rem;">El precio se mostrará a los pacientes al reservar citas</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Biografía Profesional</label>
                        <textarea class="form-control" name="bio" rows="3" 
                                  placeholder="Describe brevemente tu experiencia y especialización...">{{ $user->bio ?? '' }}</textarea>
                        <small style="color: var(--gray-600); font-size: 0.875rem;">Esta información ayudará a los pacientes a conocerte mejor</small>
                    </div>
                </div>
                @endif
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-save"></i>
                    Guardar Cambios
                </button>
            </form>
        </div>
    </div>

    <!-- Change Password -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Cambiar Contraseña</h3>
        </div>
        <div class="card-body">
            <form id="passwordForm">
                <div class="form-group">
                    <label class="form-label">Contraseña Actual</label>
                    <input type="password" class="form-control" name="current_password" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Nueva Contraseña</label>
                    <input type="password" class="form-control" name="password" required>
                    <small style="color: var(--gray-600); font-size: 0.875rem;">Mínimo 8 caracteres</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Confirmar Nueva Contraseña</label>
                    <input type="password" class="form-control" name="password_confirmation" required>
                </div>
                
                <button type="submit" class="btn btn-danger" style="width: 100%;">
                    <i class="fas fa-key"></i>
                    Cambiar Contraseña
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Profile Picture Section -->
<div class="card" style="margin-top: 2rem;">
    <div class="card-header">
        <h3 class="card-title">Foto de Perfil</h3>
    </div>
    <div class="card-body">
        <div style="display: flex; align-items: center; gap: 2rem;">
            <div style="width: 100px; height: 100px; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 2.5rem; font-weight: 700;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h4 style="margin-bottom: 0.5rem;">Avatar Generado</h4>
                <p style="color: var(--gray-600); margin-bottom: 1rem;">Tu avatar se genera automáticamente con la primera letra de tu nombre.</p>
                <p style="color: var(--gray-500); font-size: 0.875rem;">
                    <i class="fas fa-info-circle"></i>
                    La funcionalidad de carga de imágenes estará disponible próximamente.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('profileForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('{{ route("profile.update") }}', {
            method: 'PUT',
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
            // Update header name if changed
            const headerName = document.querySelector('.user-menu span');
            if (headerName) {
                headerName.textContent = data.name;
            }
        } else {
            showAlert('error', result.message || 'Error al actualizar el perfil');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('error', 'Error al actualizar el perfil');
    }
});

document.getElementById('passwordForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    if (data.password !== data.password_confirmation) {
        showAlert('error', 'Las contraseñas no coinciden');
        return;
    }
    
    try {
        const response = await fetch('{{ route("profile.update-password") }}', {
            method: 'PUT',
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
            this.reset();
        } else {
            showAlert('error', result.message || 'Error al cambiar la contraseña');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('error', 'Error al cambiar la contraseña');
    }
});

function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create new alert
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
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

<style>
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}
</style>
@endpush 