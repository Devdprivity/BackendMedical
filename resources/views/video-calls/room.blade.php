@extends('layouts.app')

@section('title', 'Videoconsulta - MediCare Pro')

@section('content')
<div class="video-call-container">
    <!-- Pre-call Setup -->
    <div id="pre-call-setup" class="pre-call-setup">
        <div class="setup-card">
            <div class="setup-header">
                <h2>
                    <i class="fas fa-video"></i>
                    @if($appointment)
                        Preparar Videoconsulta
                    @else
                        Sala de Videollamada Instantánea
                    @endif
                </h2>
                @if($appointment)
                    <p>Cita programada para: {{ $appointment->date_time->format('d/m/Y H:i') }}</p>
                @else
                    <p>Sala creada por: {{ $videoCall->createdBy->name ?? 'Usuario' }} - {{ $videoCall->created_at->format('d/m/Y H:i') }}</p>
                @endif
            </div>
            
            <div class="setup-body">
                @if($appointment)
                    <!-- Appointment info for scheduled calls -->
                    <div class="appointment-info">
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Doctor:</label>
                                <span>{{ $appointment->doctor->name ?? 'No asignado' }}</span>
                            </div>
                            <div class="info-item">
                                <label>Paciente:</label>
                                <span>{{ $appointment->patient_name ?? $appointment->patient->name ?? 'No asignado' }}</span>
                            </div>
                            <div class="info-item">
                                <label>Duración:</label>
                                <span>{{ $appointment->duration ?? 30 }} minutos</span>
                            </div>
                            <div class="info-item">
                                <label>Estado:</label>
                                <span class="status-badge status-{{ $videoCall->status_color }}">
                                    {{ $videoCall->status_label }}
                                </span>
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
                                <span>{{ $videoCall->createdBy->name ?? 'Usuario' }}</span>
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
                            <strong>Sala Instantánea:</strong> Cualquier persona con este enlace puede unirse a la videollamada.
                            Comparte el URL de esta página para invitar a otros participantes.
                        </div>
                    </div>
                @endif
                
                <!-- Device Test Section -->
                <div class="device-test">
                    <h3>Verificar Dispositivos</h3>
                    <div class="test-grid">
                        <div class="test-item">
                            <div class="test-icon">
                                <i class="fas fa-video" id="camera-icon"></i>
                            </div>
                            <div class="test-info">
                                <h4>Cámara</h4>
                                <p id="camera-status">Verificando...</p>
                            </div>
                            <button id="test-camera" class="btn btn-outline btn-sm">Probar</button>
                        </div>
                        
                        <div class="test-item">
                            <div class="test-icon">
                                <i class="fas fa-microphone" id="mic-icon"></i>
                            </div>
                            <div class="test-info">
                                <h4>Micrófono</h4>
                                <p id="mic-status">Verificando...</p>
                            </div>
                            <button id="test-mic" class="btn btn-outline btn-sm">Probar</button>
                        </div>
                        
                        <div class="test-item">
                            <div class="test-icon">
                                <i class="fas fa-volume-up" id="speaker-icon"></i>
                            </div>
                            <div class="test-info">
                                <h4>Altavoces</h4>
                                <p id="speaker-status">Verificando...</p>
                            </div>
                            <button id="test-speaker" class="btn btn-outline btn-sm">Probar</button>
                        </div>
                    </div>
                </div>
                
                <!-- Video Preview -->
                <div class="video-preview">
                    <video id="preview-video" autoplay muted></video>
                    <div class="preview-overlay">
                        <p>Vista previa de tu cámara</p>
                    </div>
                </div>
                
                <!-- Join Controls -->
                <div class="join-controls">
                    @if($videoCall->is_instant)
                        <!-- Instant call controls -->
                        <button id="join-call-btn" class="btn btn-primary btn-lg">
                            <i class="fas fa-video"></i>
                            Unirse a la Videollamada
                        </button>
                    @else
                        <!-- Appointment-based controls -->
                        @if($user->role === 'doctor' || $user->role === 'admin')
                            @if($videoCall->status === 'pending')
                                <button id="start-call-btn" class="btn btn-success btn-lg">
                                    <i class="fas fa-play"></i>
                                    Iniciar Videoconsulta
                                </button>
                            @else
                                <button id="join-call-btn" class="btn btn-primary btn-lg">
                                    <i class="fas fa-video"></i>
                                    Unirse a la Videoconsulta
                                </button>
                            @endif
                        @else
                            <button id="join-call-btn" class="btn btn-primary btn-lg" 
                                    @if($videoCall->status === 'pending') disabled @endif>
                                <i class="fas fa-video"></i>
                                @if($videoCall->status === 'pending')
                                    Esperando que el doctor inicie la consulta
                                @else
                                    Unirse a la Videoconsulta
                                @endif
                            </button>
                        @endif
                    @endif
                    
                    <button id="cancel-btn" class="btn btn-outline">
                        <i class="fas fa-times"></i>
                        @if($videoCall->is_instant)
                            Cerrar
                        @else
                            Cancelar
                        @endif
                    </button>
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
                    <p>Sala: {{ $videoCall->room_name }} - Creada por: {{ $videoCall->createdBy->name ?? 'Usuario' }}</p>
                @endif
            </div>
            <div class="call-controls">
                @if($videoCall->is_instant || ($user->role === 'doctor' || $user->role === 'admin'))
                    <button id="end-call-btn" class="btn btn-danger">
                        <i class="fas fa-phone-slash"></i>
                        @if($videoCall->is_instant)
                            Finalizar Sala
                        @else
                            Finalizar Consulta
                        @endif
                    </button>
                @endif
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
    background: var(--gray-50);
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
    background: linear-gradient(135deg, var(--primary), var(--secondary));
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
    background: var(--gray-50);
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
    color: var(--gray-600);
    font-size: 0.875rem;
}

.info-item span {
    color: var(--gray-800);
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

.status-badge.status-warning {
    background: var(--warning-light, #fff3cd);
    color: var(--warning-dark, #856404);
}

.status-badge.status-success {
    background: var(--success-light, #d1ecf1);
    color: var(--success-dark, #0c5460);
}

.device-test {
    margin-bottom: 2rem;
}

.device-test h3 {
    margin-bottom: 1rem;
    color: var(--gray-800);
}

.test-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.test-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: white;
    border: 2px solid var(--gray-200);
    border-radius: 12px;
    transition: border-color 0.3s ease;
}

.test-item.success {
    border-color: var(--success);
}

.test-item.error {
    border-color: var(--danger);
}

.test-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--gray-100);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: var(--gray-600);
}

.test-icon.success {
    background: var(--success-light);
    color: var(--success);
}

.test-icon.error {
    background: var(--danger-light);
    color: var(--danger);
}

.test-info {
    flex: 1;
}

.test-info h4 {
    margin: 0 0 0.25rem 0;
    font-size: 1rem;
    color: var(--gray-800);
}

.test-info p {
    margin: 0;
    font-size: 0.875rem;
    color: var(--gray-600);
}

.video-preview {
    position: relative;
    margin-bottom: 2rem;
    border-radius: 12px;
    overflow: hidden;
    background: var(--gray-900);
    aspect-ratio: 16/9;
}

#preview-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.preview-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
    color: white;
    padding: 1rem;
    text-align: center;
}

.join-controls {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
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
    border-bottom: 1px solid var(--gray-200);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.call-info h3 {
    margin: 0 0 0.25rem 0;
    color: var(--gray-800);
}

.call-info p {
    margin: 0;
    color: var(--gray-600);
    font-size: 0.875rem;
}

.call-controls {
    display: flex;
    gap: 1rem;
}

#jitsi-meet {
    flex: 1;
    background: var(--gray-900);
}

@media (max-width: 768px) {
    .pre-call-setup {
        padding: 1rem;
    }
    
    .setup-body {
        padding: 1rem;
    }
    
    .test-grid {
        grid-template-columns: 1fr;
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
let previewStream = null;

document.addEventListener('DOMContentLoaded', function() {
    initializeDeviceTests();
    setupEventListeners();
    pollCallStatus();
});

function initializeDeviceTests() {
    checkDevicePermissions();
    startVideoPreview();
}

function setupEventListeners() {
    document.getElementById('start-call-btn')?.addEventListener('click', startCall);
    document.getElementById('join-call-btn')?.addEventListener('click', joinCall);
    document.getElementById('end-call-btn')?.addEventListener('click', endCall);
    document.getElementById('leave-call-btn')?.addEventListener('click', leaveCall);
    document.getElementById('cancel-btn')?.addEventListener('click', cancelCall);
    
    document.getElementById('test-camera')?.addEventListener('click', testCamera);
    document.getElementById('test-mic')?.addEventListener('click', testMicrophone);
    document.getElementById('test-speaker')?.addEventListener('click', testSpeaker);
}

async function checkDevicePermissions() {
    try {
        // Check camera
        const cameraStatus = document.getElementById('camera-status');
        const cameraIcon = document.getElementById('camera-icon');
        
        try {
            await navigator.mediaDevices.getUserMedia({ video: true });
            cameraStatus.textContent = 'Disponible';
            cameraIcon.parentElement.classList.add('success');
        } catch (error) {
            cameraStatus.textContent = 'No disponible';
            cameraIcon.parentElement.classList.add('error');
        }
        
        // Check microphone
        const micStatus = document.getElementById('mic-status');
        const micIcon = document.getElementById('mic-icon');
        
        try {
            await navigator.mediaDevices.getUserMedia({ audio: true });
            micStatus.textContent = 'Disponible';
            micIcon.parentElement.classList.add('success');
        } catch (error) {
            micStatus.textContent = 'No disponible';
            micIcon.parentElement.classList.add('error');
        }
        
        // Speaker test is manual
        document.getElementById('speaker-status').textContent = 'Listo para probar';
        
    } catch (error) {
        console.error('Error checking device permissions:', error);
    }
}

async function startVideoPreview() {
    try {
        const video = document.getElementById('preview-video');
        previewStream = await navigator.mediaDevices.getUserMedia({ 
            video: true, 
            audio: false 
        });
        video.srcObject = previewStream;
    } catch (error) {
        console.error('Error starting video preview:', error);
        document.querySelector('.preview-overlay p').textContent = 'No se pudo acceder a la cámara';
    }
}

function stopVideoPreview() {
    if (previewStream) {
        previewStream.getTracks().forEach(track => track.stop());
        previewStream = null;
    }
}

async function startCall() {
    try {
        const response = await fetch(`/api/video-calls/{{ $videoCall->id }}/start`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            initializeJitsi();
        } else {
            alert(result.error || 'Error al iniciar la videollamada');
        }
    } catch (error) {
        console.error('Error starting call:', error);
        alert('Error al iniciar la videollamada');
    }
}

async function joinCall() {
    try {
        const response = await fetch(`/api/video-calls/{{ $videoCall->id }}/join`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            initializeJitsi();
        } else {
            alert(result.error || 'Error al unirse a la videollamada');
        }
    } catch (error) {
        console.error('Error joining call:', error);
        alert('Error al unirse a la videollamada');
    }
}

function initializeJitsi() {
    stopVideoPreview();
    
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
            displayName: '{{ $user->name ?? "Usuario" }}',
            email: '{{ $user->email ?? "" }}'
        },
        configOverwrite: {
            startWithAudioMuted: false,
            startWithVideoMuted: false,
            enableWelcomePage: false,
            prejoinPageEnabled: false, // Skip pre-join page
            disableModeratorIndicator: false,
            startScreenSharing: false,
            enableEmailInStats: false,
            disableProfile: false,
            hideLobbyButton: true, // Hide lobby for instant calls
            enableLobbyChat: false,
            enableInsecureRoomNameWarning: false,
            enableAutomaticUrlCaching: false,
            startSilent: false,
            disableDeepLinking: true,
            @if($videoCall->is_instant)
            // Additional config for instant calls
            requireDisplayName: false, // Don't require display name for instant calls
            enableUserRolesBasedOnToken: false,
            @endif
        },
        interfaceConfigOverwrite: {
            TOOLBAR_BUTTONS: [
                'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
                'fodeviceselection', 'hangup', 'profile', 'info', 'chat', 
                @if($videoCall->is_instant || ($user->role === 'doctor' || $user->role === 'admin'))
                'recording',
                @endif
                'settings', 'raisehand',
                'videoquality', 'filmstrip', 'invite', 'feedback', 'stats', 'shortcuts',
                'tileview', 'videobackgroundblur', 'download', 'help'
                @if($user->role === 'doctor' || $user->role === 'admin')
                , 'mute-everyone'
                @endif
            ],
            SETTINGS_SECTIONS: ['devices', 'language', 'moderator', 'profile', 'calendar'],
            SHOW_JITSI_WATERMARK: false,
            SHOW_WATERMARK_FOR_GUESTS: false,
            SHOW_BRAND_WATERMARK: false,
            BRAND_WATERMARK_LINK: '',
            SHOW_POWERED_BY: false,
            DISPLAY_WELCOME_PAGE_CONTENT: false,
            DISPLAY_WELCOME_PAGE_TOOLBAR_ADDITIONAL_CONTENT: false,
            APP_NAME: 'MediCare Pro',
            NATIVE_APP_NAME: 'MediCare Pro',
            DEFAULT_BACKGROUND: '#2c3e50',
            INITIAL_TOOLBAR_TIMEOUT: 20000,
            TOOLBAR_TIMEOUT: 4000,
            DEFAULT_REMOTE_DISPLAY_NAME: 'Usuario',
            @if($videoCall->is_instant)
            // Instant call specific interface settings
            SHOW_CHROME_EXTENSION_BANNER: false,
            MOBILE_APP_PROMO: false,
            @endif
        }
    };
    
    jitsiApi = new JitsiMeetExternalAPI(domain, options);
    
    // Event listeners
    jitsiApi.addEventListener('videoConferenceJoined', () => {
        console.log('User joined the conference');
        
        // Set display name explicitly if not set
        @if($user)
        jitsiApi.executeCommand('displayName', '{{ $user->name ?? "Usuario" }}');
        @endif
        
        // Send notification that user joined
        @if($videoCall->is_instant)
        fetch(`/api/video-calls/{{ $videoCall->id }}/join`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).catch(error => console.error('Error notifying join:', error));
        @endif
    });
    
    jitsiApi.addEventListener('videoConferenceLeft', () => {
        console.log('User left the conference');
        handleCallEnd();
    });
    
    jitsiApi.addEventListener('participantJoined', (participant) => {
        console.log('Participant joined:', participant);
    });
    
    jitsiApi.addEventListener('participantLeft', (participant) => {
        console.log('Participant left:', participant);
    });
    
    // Additional configuration for instant calls
    @if($videoCall->is_instant)
    jitsiApi.addEventListener('readyToClose', () => {
        console.log('Conference is ready to close');
        handleCallEnd();
    });
    
    // Auto-focus for better UX
    setTimeout(() => {
        const jitsiFrame = document.querySelector('iframe');
        if (jitsiFrame) {
            jitsiFrame.focus();
        }
    }, 2000);
    @endif
}

async function endCall() {
    if (confirm('¿Estás seguro de que quieres finalizar la videoconsulta?')) {
        try {
            const response = await fetch(`/api/video-calls/{{ $videoCall->id }}/end`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                if (jitsiApi) {
                    jitsiApi.dispose();
                }
                alert('Videoconsulta finalizada exitosamente');
                window.location.href = '/appointments';
            } else {
                alert(result.error || 'Error al finalizar la videollamada');
            }
        } catch (error) {
            console.error('Error ending call:', error);
            alert('Error al finalizar la videollamada');
        }
    }
}

function leaveCall() {
    if (confirm('¿Estás seguro de que quieres salir de la videoconsulta?')) {
        if (jitsiApi) {
            jitsiApi.dispose();
        }
        window.location.href = '/appointments';
    }
}

function cancelCall() {
    stopVideoPreview();
    window.location.href = '/appointments';
}

function handleCallEnd() {
    if (jitsiApi) {
        jitsiApi.dispose();
        jitsiApi = null;
    }
    window.location.href = '/appointments';
}

// Poll call status for patients waiting for doctor to start
function pollCallStatus() {
    @if($user->role === 'patient' && $videoCall->status === 'pending')
    const pollInterval = setInterval(async () => {
        try {
            const response = await fetch(`/api/video-calls/{{ $videoCall->id }}/status`);
            const result = await response.json();
            
            if (result.success && result.video_call.status === 'active') {
                clearInterval(pollInterval);
                const joinBtn = document.getElementById('join-call-btn');
                joinBtn.disabled = false;
                joinBtn.innerHTML = '<i class="fas fa-video"></i> Unirse a la Videoconsulta';
                
                // Show notification
                if (Notification.permission === 'granted') {
                    new Notification('Videoconsulta iniciada', {
                        body: 'El doctor ha iniciado la videoconsulta. Puedes unirte ahora.',
                        icon: '/favicon.ico'
                    });
                }
            }
        } catch (error) {
            console.error('Error polling call status:', error);
        }
    }, 5000); // Poll every 5 seconds
    @endif
}

// Device testing functions
function testCamera() {
    alert('La vista previa de la cámara está activa arriba');
}

function testMicrophone() {
    // Simple microphone test
    navigator.mediaDevices.getUserMedia({ audio: true })
        .then(stream => {
            alert('Micrófono funcionando correctamente');
            stream.getTracks().forEach(track => track.stop());
        })
        .catch(error => {
            alert('Error al acceder al micrófono');
        });
}

function testSpeaker() {
    // Play a test sound
    const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzOa2O/CdSMFLofN8duJNwgUaLvq6Z9OEAl');
    audio.play()
        .then(() => {
            alert('Si escuchaste un sonido, los altavoces funcionan correctamente');
        })
        .catch(error => {
            alert('Error al reproducir el sonido de prueba');
        });
}

// Request notification permission
if (Notification.permission === 'default') {
    Notification.requestPermission();
}

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    stopVideoPreview();
    if (jitsiApi) {
        jitsiApi.dispose();
    }
});
</script>
@endpush 