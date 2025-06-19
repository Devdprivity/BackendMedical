@extends('layouts.app')

@section('title', 'Configuración - MediCare Pro')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-cog"></i>
            Configuración
        </h1>
        <p class="page-subtitle">Personaliza tu experiencia en MediCare Pro</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('profile.show') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i>
            Volver al Perfil
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <!-- Appearance Settings -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Apariencia</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Tema</label>
                <select class="form-control" id="themeSelect">
                    <option value="light">Claro</option>
                    <option value="dark">Oscuro</option>
                    <option value="auto">Automático</option>
                </select>
                <small style="color: var(--gray-600); font-size: 0.875rem;">El tema oscuro estará disponible próximamente</small>
            </div>
            
            <div class="form-group">
                <label class="form-label">Idioma</label>
                <select class="form-control" id="languageSelect">
                    <option value="es">Español</option>
                    <option value="en">English</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Tamaño de fuente</label>
                <select class="form-control" id="fontSizeSelect">
                    <option value="small">Pequeña</option>
                    <option value="medium" selected>Mediana</option>
                    <option value="large">Grande</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Notification Settings -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Notificaciones</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <label class="form-label" style="margin: 0;">Notificaciones por email</label>
                        <small style="color: var(--gray-600); display: block;">Recibir notificaciones importantes por correo</small>
                    </div>
                    <label class="switch">
                        <input type="checkbox" id="emailNotifications" checked>
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <label class="form-label" style="margin: 0;">Notificaciones de citas</label>
                        <small style="color: var(--gray-600); display: block;">Recordatorios de citas próximas</small>
                    </div>
                    <label class="switch">
                        <input type="checkbox" id="appointmentNotifications" checked>
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <label class="form-label" style="margin: 0;">Notificaciones del sistema</label>
                        <small style="color: var(--gray-600); display: block;">Actualizaciones y mantenimiento</small>
                    </div>
                    <label class="switch">
                        <input type="checkbox" id="systemNotifications">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
    <!-- Privacy Settings -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Privacidad</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <label class="form-label" style="margin: 0;">Perfil público</label>
                        <small style="color: var(--gray-600); display: block;">Permitir que otros usuarios vean tu perfil</small>
                    </div>
                    <label class="switch">
                        <input type="checkbox" id="publicProfile">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <label class="form-label" style="margin: 0;">Mostrar última conexión</label>
                        <small style="color: var(--gray-600); display: block;">Mostrar cuándo fue tu último acceso</small>
                    </div>
                    <label class="switch">
                        <input type="checkbox" id="showLastLogin" checked>
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Settings -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Seguridad</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <label class="form-label" style="margin: 0;">Autenticación de dos factores</label>
                        <small style="color: var(--gray-600); display: block;">Seguridad adicional para tu cuenta</small>
                    </div>
                    <label class="switch">
                        <input type="checkbox" id="twoFactorAuth">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Tiempo de sesión</label>
                <select class="form-control" id="sessionTimeout">
                    <option value="30">30 minutos</option>
                    <option value="60" selected>1 hora</option>
                    <option value="120">2 horas</option>
                    <option value="480">8 horas</option>
                </select>
            </div>
            
            <div class="form-group">
                <button class="btn btn-danger" style="width: 100%;" onclick="showLogoutAllDevices()">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar sesión en todos los dispositivos
                </button>
            </div>
        </div>
    </div>
</div>

<!-- System Information -->
<div class="card" style="margin-top: 2rem;">
    <div class="card-header">
        <h3 class="card-title">Información del Sistema</h3>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
            <div>
                <label style="font-weight: 600; color: var(--gray-700);">Versión</label>
                <div style="color: var(--gray-600);">MediCare Pro v1.0.0</div>
            </div>
            <div>
                <label style="font-weight: 600; color: var(--gray-700);">Última actualización</label>
                <div style="color: var(--gray-600);">{{ date('d/m/Y') }}</div>
            </div>
            <div>
                <label style="font-weight: 600; color: var(--gray-700);">Estado del servidor</label>
                <div style="color: var(--success);">
                    <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                    En línea
                </div>
            </div>
            <div>
                <label style="font-weight: 600; color: var(--gray-700);">Soporte</label>
                <div style="color: var(--primary);">
                    <i class="fas fa-envelope"></i>
                    soporte@medicare.com
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Save Settings Button -->
<div style="text-align: center; margin-top: 2rem;">
    <button class="btn btn-primary" style="padding: 1rem 3rem;" onclick="saveSettings()">
        <i class="fas fa-save"></i>
        Guardar Configuración
    </button>
</div>
@endsection

@push('scripts')
<script>
function saveSettings() {
    // Collect all settings
    const settings = {
        theme: document.getElementById('themeSelect').value,
        language: document.getElementById('languageSelect').value,
        fontSize: document.getElementById('fontSizeSelect').value,
        emailNotifications: document.getElementById('emailNotifications').checked,
        appointmentNotifications: document.getElementById('appointmentNotifications').checked,
        systemNotifications: document.getElementById('systemNotifications').checked,
        publicProfile: document.getElementById('publicProfile').checked,
        showLastLogin: document.getElementById('showLastLogin').checked,
        twoFactorAuth: document.getElementById('twoFactorAuth').checked,
        sessionTimeout: document.getElementById('sessionTimeout').value
    };
    
    // Save to localStorage for now (in a real app, you'd send to server)
    localStorage.setItem('userSettings', JSON.stringify(settings));
    
    showAlert('success', 'Configuración guardada exitosamente');
}

function loadSettings() {
    const savedSettings = localStorage.getItem('userSettings');
    if (savedSettings) {
        const settings = JSON.parse(savedSettings);
        
        document.getElementById('themeSelect').value = settings.theme || 'light';
        document.getElementById('languageSelect').value = settings.language || 'es';
        document.getElementById('fontSizeSelect').value = settings.fontSize || 'medium';
        document.getElementById('emailNotifications').checked = settings.emailNotifications !== false;
        document.getElementById('appointmentNotifications').checked = settings.appointmentNotifications !== false;
        document.getElementById('systemNotifications').checked = settings.systemNotifications || false;
        document.getElementById('publicProfile').checked = settings.publicProfile || false;
        document.getElementById('showLastLogin').checked = settings.showLastLogin !== false;
        document.getElementById('twoFactorAuth').checked = settings.twoFactorAuth || false;
        document.getElementById('sessionTimeout').value = settings.sessionTimeout || '60';
    }
}

function showLogoutAllDevices() {
    if (confirm('¿Estás seguro de que quieres cerrar sesión en todos los dispositivos? Tendrás que volver a iniciar sesión.')) {
        // In a real app, you'd call an API endpoint
        showAlert('success', 'Sesión cerrada en todos los dispositivos');
    }
}

function showAlert(type, message) {
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
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
    
    setTimeout(() => {
        alert.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => alert.remove(), 300);
    }, 5000);
}

// Load settings on page load
document.addEventListener('DOMContentLoaded', loadSettings);
</script>

<style>
/* Toggle Switch Styles */
.switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: var(--gray-300);
    transition: var(--transition);
    border-radius: 24px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: var(--transition);
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

input:checked + .slider {
    background-color: var(--primary);
}

input:checked + .slider:before {
    transform: translateX(26px);
}

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