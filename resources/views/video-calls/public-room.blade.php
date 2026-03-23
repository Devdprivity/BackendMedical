@extends('layouts.app')

@section('title', 'Videollamada - DrOrganiza')

@section('content')
<div class="video-call-container">
    <!-- Pre-call setup for guests -->
    <div id="pre-call-setup" class="pre-call-setup">
        <div class="setup-card">
            <div class="setup-header">
                <h2>
                    <i class="fas fa-video"></i>
                    @if($videoCall->is_instant)
                        Videollamada Instantánea
                    @else
                        Videoconsulta Médica
                    @endif
                </h2>
                <p>Bienvenido a DrOrganiza</p>
            </div>
            
            <div class="setup-body">
                @if($appointment)
                    <!-- Appointment info -->
                    <div class="appointment-info">
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Doctor:</label>
                                <span>{{ $appointment->doctor->name ?? 'Doctor' }}</span>
                            </div>
                            <div class="info-item">
                                <label>Paciente:</label>
                                <span>{{ $appointment->patient_name ?? 'Paciente' }}</span>
                            </div>
                            <div class="info-item">
                                <label>Fecha:</label>
                                <span>{{ $appointment->appointment_date ?? 'Hoy' }}</span>
                            </div>
                            <div class="info-item">
                                <label>Hora:</label>
                                <span>{{ $appointment->appointment_time ?? 'Ahora' }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Instant call info -->
                    <div class="appointment-info">
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Sala ID:</label>
                                <span style="font-family: monospace;">{{ $videoCall->room_name }}</span>
                            </div>
                            <div class="info-item">
                                <label>Creada por:</label>
                                <span>{{ $videoCall->createdBy->name ?? 'Doctor' }}</span>
                            </div>
                            <div class="info-item">
                                <label>Fecha:</label>
                                <span>{{ $videoCall->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="info-item">
                                <label>Estado:</label>
                                <span class="status-badge status-success">
                                    <i class="fas fa-circle"></i>
                                    Activa
                                </span>
                            </div>
                        </div>
                        
                        <!-- Share info for instant calls -->
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>Acceso Público:</strong> Cualquier persona con este enlace puede unirse a la videollamada.
                            No necesitas crear una cuenta para participar.
                        </div>
                    </div>
                @endif
                
                <!-- Guest Information Form -->
                <div class="guest-form">
                    <h3>
                        <i class="fas fa-user"></i>
                        Información del Participante
                    </h3>
                    <form id="guest-form">
                        <div class="form-group">
                            <label for="guest_name">Nombre completo *</label>
                            <input type="text" id="guest_name" name="guest_name" class="form-control" 
                                   placeholder="Ingresa tu nombre completo" required>
                        </div>
                        <div class="form-group">
                            <label for="guest_email">Email (opcional)</label>
                            <input type="email" id="guest_email" name="guest_email" class="form-control" 
                                   placeholder="tu@email.com">
                            <small class="form-text">El email es opcional pero ayuda al doctor a contactarte si es necesario.</small>
                        </div>
                    </form>
                </div>
                
                <!-- Join Controls -->
                <div class="join-controls">
                    <button id="join-call-btn" class="btn btn-primary btn-lg">
                        <i class="fas fa-video"></i>
                        Unirse a la Videollamada
                    </button>
                    
                    <button id="cancel-btn" class="btn btn-outline">
                        <i class="fas fa-times"></i>
                        Cerrar
                    </button>
                </div>
                
                <!-- Privacy Notice -->
                <div class="privacy-notice">
                    <p class="small text-muted">
                        <i class="fas fa-shield-alt"></i>
                        Esta videollamada es segura y privada. La información compartida está protegida por las políticas de privacidad médica.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Jitsi Meet Container -->
    <div id="jitsi-container" class="jitsi-container" style="display: none;">
        <div class="call-header">
            <div class="call-info">
                @if($appointment)
                    <h3>Videoconsulta en curso</h3>
                    <p>{{ $appointment->doctor->name ?? 'Doctor' }} - {{ $appointment->patient_name ?? 'Paciente' }}</p>
                @else
                    <h3>Videollamada Instantánea</h3>
                    <p>Sala: {{ $videoCall->room_name }} - Participando como invitado</p>
                @endif
            </div>
            <div class="call-controls">
                <button id="leave-call-btn" class="btn btn-outline">
                    <i class="fas fa-sign-out-alt"></i>
                    Salir
                </button>
            </div>
        </div>
        <div id="jitsi-meet"></div>
    </div>
</div>
@endsection

@push('styles')
<style>
.video-call-container {
    min-height: 100vh;
    background: #f8f9fa;
}

.pre-call-setup {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 2rem;
}

.setup-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    max-width: 800px;
    width: 100%;
    overflow: hidden;
}

.setup-header {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    color: white;
    padding: 2rem;
    text-align: center;
}

.setup-header h2 {
    margin: 0 0 0.5rem 0;
    font-size: 1.75rem;
    font-weight: 600;
}

.setup-header p {
    margin: 0;
    opacity: 0.9;
}

.setup-body {
    padding: 2rem;
}

.appointment-info {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 12px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.info-item label {
    font-weight: 600;
    color: #6b7280;
    font-size: 0.875rem;
}

.info-item span {
    color: #1f2937;
    font-weight: 500;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    width: fit-content;
}

.status-badge.status-success {
    background: #d1fae5;
    color: #065f46;
}

.guest-form {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 12px;
    border: 2px solid #e9ecef;
}

.guest-form h3 {
    margin-bottom: 1rem;
    color: #1f2937;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #374151;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-text {
    color: #6b7280;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.join-controls {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 1rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn-outline {
    background: white;
    color: #6b7280;
    border: 1px solid #d1d5db;
}

.btn-outline:hover {
    background: #f9fafb;
}

.btn-lg {
    padding: 1rem 2rem;
    font-size: 1.125rem;
}

.privacy-notice {
    text-align: center;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
}

.jitsi-container {
    height: 100vh;
    display: flex;
    flex-direction: column;
}

.call-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 2rem;
    background: white;
    border-bottom: 1px solid #e5e7eb;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.call-info h3 {
    margin: 0 0 0.25rem 0;
    color: #1f2937;
}

.call-info p {
    margin: 0;
    color: #6b7280;
    font-size: 0.875rem;
}

.call-controls {
    display: flex;
    gap: 1rem;
}

#jitsi-meet {
    flex: 1;
    background: #111827;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin: 1rem 0;
}

.alert-info {
    background: #dbeafe;
    color: #1e40af;
    border: 1px solid #93c5fd;
}

.mt-3 {
    margin-top: 1rem;
}

@media (max-width: 768px) {
    .pre-call-setup {
        padding: 1rem;
    }
    
    .setup-body {
        padding: 1rem;
    }
    
    .join-controls {
        flex-direction: column;
    }
    
    .call-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://meet.jit.si/external_api.js"></script>
<script>
let jitsiApi = null;
let userJoinedConference = false;
let guestName = '';
let guestEmail = '';

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - initializing public video call page');
    setupEventListeners();
});

function setupEventListeners() {
    document.getElementById('join-call-btn')?.addEventListener('click', joinCallAsGuest);
    document.getElementById('leave-call-btn')?.addEventListener('click', leaveCall);
    document.getElementById('cancel-btn')?.addEventListener('click', cancelCall);
}

async function joinCallAsGuest() {
    // Validate guest form
    const nameInput = document.getElementById('guest_name');
    const emailInput = document.getElementById('guest_email');
    
    if (!nameInput.value.trim()) {
        alert('Por favor ingresa tu nombre para unirte a la videollamada');
        nameInput.focus();
        return;
    }
    
    guestName = nameInput.value.trim();
    guestEmail = emailInput.value.trim();
    
    try {
        console.log('Joining as guest:', { name: guestName, email: guestEmail });
        
        const response = await fetch(`/room/api/{{ $videoCall->id }}/join-guest`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                guest_name: guestName,
                guest_email: guestEmail
            })
        });
        
        const result = await response.json();
        console.log('Guest join response:', result);
        
        if (result.success) {
            initializeJitsi();
        } else {
            alert(result.error || 'Error al unirse a la videollamada');
        }
    } catch (error) {
        console.error('Error joining as guest:', error);
        alert('Error al unirse a la videollamada');
    }
}

function initializeJitsi() {
    console.log('Initializing Jitsi for guest');
    userJoinedConference = false;
    
    // Hide pre-call setup
    document.getElementById('pre-call-setup').style.display = 'none';
    document.getElementById('jitsi-container').style.display = 'flex';
    
    // Configure Jitsi
    const domain = 'meet.jit.si';
    const options = {
        roomName: '{{ $videoCall->room_name }}',
        width: '100%',
        height: '100%',
        parentNode: document.querySelector('#jitsi-meet'),
        userInfo: {
            displayName: guestName,
            email: guestEmail
        },
        configOverwrite: {
            startWithAudioMuted: false,
            startWithVideoMuted: false,
            enableWelcomePage: false,
            prejoinPageEnabled: false,
            disableModeratorIndicator: false,
            startScreenSharing: false,
            enableEmailInStats: false,
            disableProfile: false,
            hideLobbyButton: true,
            enableLobbyChat: false,
            enableInsecureRoomNameWarning: false,
            enableAutomaticUrlCaching: false,
            startSilent: false,
            disableDeepLinking: true,
            // Disable Google integration
            googleAnalyticsTrackingId: null,
            enableGoogleLogin: false,
            enableCalendarIntegration: false,
            disableThirdPartyRequests: true,
            enableNoAudioDetection: false,
            enableNoisyMicDetection: false,
            defaultLocalDisplayName: guestName,
            defaultRemoteDisplayName: 'Participante',
            requireDisplayName: false,
            enableUserRolesBasedOnToken: false,
        },
        interfaceConfigOverwrite: {
            TOOLBAR_BUTTONS: [
                'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
                'fodeviceselection', 'hangup', 'profile', 'info', 'chat', 
                'settings', 'raisehand', 'videoquality', 'filmstrip', 
                'tileview', 'videobackgroundblur', 'download', 'help'
            ],
            SETTINGS_SECTIONS: ['devices', 'language', 'profile'],
            SHOW_JITSI_WATERMARK: false,
            SHOW_WATERMARK_FOR_GUESTS: false,
            SHOW_BRAND_WATERMARK: false,
            BRAND_WATERMARK_LINK: '',
            SHOW_POWERED_BY: false,
            DISPLAY_WELCOME_PAGE_CONTENT: false,
            DISPLAY_WELCOME_PAGE_TOOLBAR_ADDITIONAL_CONTENT: false,
            APP_NAME: 'DrOrganiza',
            NATIVE_APP_NAME: 'DrOrganiza',
            DEFAULT_BACKGROUND: '#2c3e50',
            INITIAL_TOOLBAR_TIMEOUT: 20000,
            TOOLBAR_TIMEOUT: 4000,
            DEFAULT_REMOTE_DISPLAY_NAME: 'Participante',
            HIDE_INVITE_MORE_HEADER: true,
            DISABLE_PRESENCE_STATUS: true,
            SHOW_CHROME_EXTENSION_BANNER: false,
            MOBILE_APP_PROMO: false,
        }
    };
    
    console.log('Creating Jitsi API with options:', options);
    jitsiApi = new JitsiMeetExternalAPI(domain, options);
    console.log('Jitsi API created successfully');
    
    // Event listeners
    jitsiApi.addEventListener('videoConferenceJoined', () => {
        console.log('Guest joined the conference');
        userJoinedConference = true;
        
        // Set display name explicitly
        jitsiApi.executeCommand('displayName', guestName);
        if (guestEmail) {
            jitsiApi.executeCommand('email', guestEmail);
        }
    });
    
    jitsiApi.addEventListener('videoConferenceLeft', () => {
        console.log('Guest left the conference');
        if (userJoinedConference) {
            handleCallEnd();
        }
    });
    
    jitsiApi.addEventListener('participantJoined', (participant) => {
        console.log('Participant joined:', participant);
    });
    
    jitsiApi.addEventListener('participantLeft', (participant) => {
        console.log('Participant left:', participant);
    });
}

function leaveCall() {
    if (confirm('¿Estás seguro de que quieres salir de la videollamada?')) {
        if (jitsiApi) {
            jitsiApi.dispose();
        }
        window.close();
    }
}

function cancelCall() {
    window.close();
}

function handleCallEnd() {
    console.log('Handling call end for guest');
    userJoinedConference = false;
    if (jitsiApi) {
        jitsiApi.dispose();
        jitsiApi = null;
    }
    
    if (confirm('La videollamada ha terminado. ¿Deseas cerrar esta ventana?')) {
        window.close();
    } else {
        // Show pre-call setup again
        document.getElementById('jitsi-container').style.display = 'none';
        document.getElementById('pre-call-setup').style.display = 'flex';
    }
}

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    if (jitsiApi) {
        jitsiApi.dispose();
    }
});
</script>
@endpush
