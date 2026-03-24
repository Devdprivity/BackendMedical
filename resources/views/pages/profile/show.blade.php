@extends('layouts.app')

@section('title', 'Mi Perfil - DrOrganiza')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-user-circle"></i>
            Mi Perfil
        </h1>
        <p class="page-subtitle">Información personal y configuración de cuenta</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('profile.edit') }}" class="btn btn-primary">
            <i class="fas fa-edit"></i>
            Editar Perfil
        </a>
        <a href="{{ route('profile.settings') }}" class="btn btn-outline">
            <i class="fas fa-cog"></i>
            Configuración
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <!-- Left Column -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        <!-- Profile Card -->
        <div class="card">
            <div class="card-body" style="text-align: center;">
                <div style="width: 120px; height: 120px; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem; font-weight: 700; margin: 0 auto 1.5rem;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h2 style="margin-bottom: 0.5rem; color: var(--dark);">{{ $user->name }}</h2>
                <p style="color: var(--gray-600); margin-bottom: 1rem;">{{ $user->email }}</p>
                
                <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: var(--success); color: white; border-radius: 20px; font-size: 0.875rem;">
                    <i class="fas fa-check-circle"></i>
                    {{ ucfirst($user->status ?? 'Activo') }}
                </div>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user"></i>
                    Información Básica
                </h3>
            </div>
            <div class="card-body">
                <div style="display: grid; gap: 1.5rem;">
                    <div>
                        <label style="display: block; font-weight: 600; color: var(--gray-700); margin-bottom: 0.5rem;">Rol</label>
                        <div style="padding: 0.75rem 1rem; background: var(--gray-100); border-radius: 8px; color: var(--gray-800);">
                            {{ ucfirst($user->role ?? 'Usuario') }}
                        </div>
                    </div>
                    
                    <div>
                        <label style="display: block; font-weight: 600; color: var(--gray-700); margin-bottom: 0.5rem;">Estado</label>
                        <div style="padding: 0.75rem 1rem; background: var(--gray-100); border-radius: 8px;">
                            <span style="display: inline-flex; align-items: center; gap: 0.5rem; color: var(--success);">
                                <i class="fas fa-check-circle"></i>
                                {{ ucfirst($user->status ?? 'Activo') }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <label style="display: block; font-weight: 600; color: var(--gray-700); margin-bottom: 0.5rem;">Miembro desde</label>
                        <div style="padding: 0.75rem 1rem; background: var(--gray-100); border-radius: 8px; color: var(--gray-800);">
                            {{ $user->created_at ? $user->created_at->format('d/m/Y') : 'No disponible' }}
                        </div>
                    </div>
                    
                    <div>
                        <label style="display: block; font-weight: 600; color: var(--gray-700); margin-bottom: 0.5rem;">Último acceso</label>
                        <div style="padding: 0.75rem 1rem; background: var(--gray-100); border-radius: 8px; color: var(--gray-800);">
                            {{ $user->last_login ? $user->last_login->format('d/m/Y H:i') : 'No disponible' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        @if($user->role === 'doctor')
        <!-- Professional Information for Doctors -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-stethoscope"></i>
                    Información Profesional
                </h3>
            </div>
            <div class="card-body">
                <div style="display: grid; gap: 1.5rem;">
                    <div>
                        <label style="display: block; font-weight: 600; color: var(--gray-700); margin-bottom: 0.5rem;">Especialidad</label>
                        <div style="padding: 0.75rem 1rem; background: var(--gray-100); border-radius: 8px; color: var(--gray-800);">
                            {{ $user->specialty ?? 'No especificada' }}
                        </div>
                    </div>
                    
                    <div>
                        <label style="display: block; font-weight: 600; color: var(--gray-700); margin-bottom: 0.5rem;">Precio por Consulta</label>
                        <div style="padding: 0.75rem 1rem; background: var(--gray-100); border-radius: 8px; color: var(--gray-800);">
                            @if($user->consultation_fee)
                                ${{ number_format($user->consultation_fee, 2) }}
                            @else
                                No configurado
                            @endif
                        </div>
                    </div>
                    
                    @if($user->bio)
                    <div>
                        <label style="display: block; font-weight: 600; color: var(--gray-700); margin-bottom: 0.5rem;">Biografía Profesional</label>
                        <div style="padding: 0.75rem 1rem; background: var(--gray-100); border-radius: 8px; color: var(--gray-800); line-height: 1.6;">
                            {{ $user->bio }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Payment Methods Section -->
        <div class="card">
            <div class="card-header">
                <div style="display: flex; align-items: center; justify-content: between; width: 100%;">
                    <h3 class="card-title">
                        <i class="fas fa-credit-card"></i>
                        Métodos de Pago
                    </h3>
                                            <a href="{{ route('payment-methods.web.index') }}" class="btn btn-primary" style="font-size: 0.875rem; margin-left: auto;">
                        <i class="fas fa-cog"></i>
                        Gestionar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div id="payment-methods-summary">
                    <div style="display: flex; justify-content: center; padding: 1rem;">
                        <div style="width: 24px; height: 24px; border: 2px solid var(--primary); border-top: 2px solid transparent; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-top: 1rem;">
                    <a href="{{ route('payment-methods.web.create') }}" class="btn btn-outline" style="font-size: 0.875rem; text-align: center;">
                        <i class="fas fa-plus"></i>
                        Agregar Método
                    </a>
                                            <a href="{{ route('payment-methods.web.index') }}" class="btn btn-outline" style="font-size: 0.875rem; text-align: center;">
                        <i class="fas fa-list"></i>
                        Ver Todos
                    </a>
                </div>
            </div>
        </div>

        <!-- Payment Links Section -->
        <div class="card">
            <div class="card-header">
                <div style="display: flex; align-items: center; justify-content: between; width: 100%;">
                    <h3 class="card-title">
                        <i class="fas fa-link"></i>
                        Links de Pago
                    </h3>
                    <a href="{{ route('payment-links.index') }}" class="btn btn-primary" style="font-size: 0.875rem; margin-left: auto;">
                        <i class="fas fa-external-link-alt"></i>
                        Gestionar Links
                    </a>
                </div>
            </div>
            <div class="card-body">
                <p style="color: var(--gray-600); margin-bottom: 1rem; font-size: 0.9rem;">
                    Crea links de pago personalizados para enviar a tus pacientes por WhatsApp, email o cualquier medio.
                </p>
                
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1rem;">
                    <div style="text-align: center; padding: 1rem; background: var(--gray-50); border-radius: 8px;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary);" id="total-links">-</div>
                        <div style="font-size: 0.8rem; color: var(--gray-600);">Total Links</div>
                    </div>
                    <div style="text-align: center; padding: 1rem; background: var(--gray-50); border-radius: 8px;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--success);" id="active-links">-</div>
                        <div style="font-size: 0.8rem; color: var(--gray-600);">Activos</div>
                    </div>
                    <div style="text-align: center; padding: 1rem; background: var(--gray-50); border-radius: 8px;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--warning);" id="used-links">-</div>
                        <div style="font-size: 0.8rem; color: var(--gray-600);">Usados</div>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <x-buttons.create-payment-link size="sm" variant="outline-primary" text="Crear Link" style="font-size: 0.875rem;" />
                    <a href="{{ route('payment-links.index') }}" class="btn btn-outline-secondary" style="font-size: 0.875rem; text-align: center;">
                        <i class="fas fa-chart-bar"></i>
                        Ver Estadísticas
                    </a>
                </div>
            </div>
        </div>

        <!-- Booking Status -->
        @if($user->booking_enabled)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-check"></i>
                    Estado de Reservas
                </h3>
            </div>
            <div class="card-body">
                <div style="padding: 1rem; background: var(--success-light, #e8f5e8); border-radius: 8px;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                        <span style="display: inline-flex; align-items: center; gap: 0.5rem; color: var(--success); font-weight: 600;">
                            <i class="fas fa-check-circle"></i>
                            Reservas públicas activadas
                        </span>
                    </div>
                    @if($user->booking_slug)
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-weight: 600; color: var(--gray-700); margin-bottom: 0.5rem;">Link de reservas:</label>
                        <div style="padding: 0.75rem; background: white; border-radius: 6px; border: 1px solid var(--gray-200);">
                            <a href="{{ url('/booking/' . $user->booking_slug) }}" target="_blank" style="color: var(--primary); text-decoration: none; word-break: break-all;">
                                {{ url('/booking/' . $user->booking_slug) }}
                            </a>
                        </div>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ url('/booking/' . $user->booking_slug) }}" target="_blank" class="btn btn-success" style="font-size: 0.875rem; flex: 1;">
                            <i class="fas fa-external-link-alt"></i>
                            Ver Página de Reservas
                        </a>
                        <button onclick="copyBookingUrl(this)" class="btn btn-outline" style="font-size: 0.875rem;">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Schedule Information -->
        @if($user->schedule_start && $user->schedule_end && $user->work_days)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clock"></i>
                    Horarios Configurados
                </h3>
            </div>
            <div class="card-body">
                @php
                    $workDays = json_decode($user->work_days ?? '[]', true);
                    $dayNames = [
                        'monday' => 'Lunes',
                        'tuesday' => 'Martes',
                        'wednesday' => 'Miércoles',
                        'thursday' => 'Jueves',
                        'friday' => 'Viernes',
                        'saturday' => 'Sábado',
                        'sunday' => 'Domingo'
                    ];
                    $workDaysText = collect($workDays)->map(fn($day) => $dayNames[$day] ?? $day)->join(', ');
                @endphp
                
                <div style="background: var(--gray-50); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                    <div style="display: grid; gap: 0.75rem; font-size: 0.875rem;">
                        <div><strong>Días de trabajo:</strong> {{ $workDaysText }}</div>
                        <div><strong>Horario:</strong> {{ $user->schedule_start }} - {{ $user->schedule_end }}</div>
                        @if($user->break_start && $user->break_end)
                        <div><strong>Descanso:</strong> {{ $user->break_start }} - {{ $user->break_end }}</div>
                        @endif
                        <div><strong>Duración por consulta:</strong> {{ $user->consultation_duration ?? 30 }} minutos</div>
                    </div>
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <a href="{{ route('profile.schedule') }}" class="btn btn-outline" style="font-size: 0.875rem; flex: 1;">
                        <i class="fas fa-edit"></i>
                        Modificar Horarios
                    </a>
                    
                    @if(!$user->booking_enabled)
                    <button type="button" class="btn btn-success" onclick="enableBookingFromProfile()" style="font-size: 0.875rem; flex: 1;">
                        <i class="fas fa-rocket"></i>
                        Activar Reservas
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Alerts for incomplete setup -->
        @if(!$user->specialty && $user->role === 'doctor')
        <div class="card">
            <div class="card-body">
                <div style="padding: 1rem; background: var(--warning-light, #fff3cd); border: 1px solid var(--warning, #ffc107); border-radius: 8px;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--warning-dark, #856404); margin-bottom: 0.5rem;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Perfil Incompleto</strong>
                    </div>
                    <p style="margin: 0 0 1rem 0; color: var(--warning-dark, #856404); font-size: 0.875rem;">
                        Para habilitar las reservas públicas, necesitas completar tu especialidad y precio de consulta.
                    </p>
                    <a href="{{ route('profile.edit') }}" class="btn btn-warning" style="font-size: 0.875rem;">
                        <i class="fas fa-edit"></i>
                        Completar Perfil
                    </a>
                </div>
            </div>
        </div>
        @elseif($user->role === 'doctor' && (!$user->schedule_start || !$user->schedule_end || !$user->work_days))
        <div class="card">
            <div class="card-body">
                <div style="padding: 1rem; background: var(--info-light, #e1f5fe); border: 1px solid var(--info, #0288d1); border-radius: 8px;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--info-dark, #01579b); margin-bottom: 0.5rem;">
                        <i class="fas fa-calendar-alt"></i>
                        <strong>Configurar Horarios</strong>
                    </div>
                    <p style="margin: 0 0 1rem 0; color: var(--info-dark, #01579b); font-size: 0.875rem;">
                        Para habilitar las reservas públicas, necesitas configurar tus horarios de disponibilidad.
                    </p>
                    <a href="{{ route('profile.schedule') }}" class="btn btn-info" style="font-size: 0.875rem;">
                        <i class="fas fa-calendar-alt"></i>
                        Configurar Horarios
                    </a>
                </div>
            </div>
        </div>
        @endif
        @else
        <!-- For non-doctor users -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    Información de Cuenta
                </h3>
            </div>
            <div class="card-body">
                <div style="text-align: center; padding: 2rem;">
                    <div style="width: 80px; height: 80px; background: var(--gray-200); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--gray-500); font-size: 2rem; margin: 0 auto 1rem;">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h4 style="color: var(--gray-700); margin-bottom: 0.5rem;">{{ ucfirst($user->role ?? 'Usuario') }}</h4>
                    <p style="color: var(--gray-600); font-size: 0.875rem;">
                        Tu cuenta está configurada como {{ strtolower($user->role ?? 'usuario') }} en el sistema.
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Activity Section -->
<div class="card" style="margin-top: 2rem;">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-history"></i>
            Actividad Reciente
        </h3>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--gray-50); border-radius: 8px;">
                <div style="width: 40px; height: 40px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
                <div>
                    <div style="font-weight: 600; color: var(--dark);">Último inicio de sesión</div>
                    <div style="font-size: 0.875rem; color: var(--gray-600);">
                        {{ $user->last_login ? $user->last_login->format('d/m/Y H:i') : 'No disponible' }}
                    </div>
                </div>
            </div>
            
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--gray-50); border-radius: 8px;">
                <div style="width: 40px; height: 40px; background: var(--success); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                    <i class="fas fa-user-check"></i>
                </div>
                <div>
                    <div style="font-weight: 600; color: var(--dark);">Cuenta creada</div>
                    <div style="font-size: 0.875rem; color: var(--gray-600);">
                        {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'No disponible' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Incluir componente modal -->
<x-modals.create-payment-link />

@endsection

@push('styles')
<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.payment-method-mini {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: var(--gray-50);
    border-radius: 8px;
    border: 1px solid var(--gray-200);
    margin-bottom: 0.5rem;
}

.payment-method-mini:last-child {
    margin-bottom: 0;
}

.payment-method-mini.inactive {
    opacity: 0.6;
}

.method-icon-mini {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: white;
}

.method-icon-mini.paypal { background: #0070ba; }
.method-icon-mini.binance_pay { background: #f3ba2f; color: #000; }
.method-icon-mini.pago_movil { background: #e74c3c; }
.method-icon-mini.stripe { background: #635bff; }
.method-icon-mini.wepay { background: #0099cc; }

.method-badge-mini {
    padding: 0.125rem 0.5rem;
    border-radius: 12px;
    font-size: 0.6875rem;
    font-weight: 600;
    text-transform: uppercase;
}

.method-badge-mini.manual {
    background: #fff3cd;
    color: #856404;
}

.method-badge-mini.automatic {
    background: #d1ecf1;
    color: #0c5460;
}
</style>
@endpush

<script>
// Load payment methods summary on page load
document.addEventListener('DOMContentLoaded', function() {
    @if($user->role === 'doctor')
    loadPaymentMethodsSummary();
    loadPaymentLinksStats();
    @endif
});

async function loadPaymentMethodsSummary() {
    try {
        const response = await fetch('/api/payment-methods', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        if (!response.ok) throw new Error('Error al cargar métodos de pago');

        const data = await response.json();
        const paymentMethods = data.data.data || [];
        renderPaymentMethodsSummary(paymentMethods);
    } catch (error) {
        console.error('Error:', error);
        renderPaymentMethodsError();
    }
}

function renderPaymentMethodsSummary(methods) {
    const container = document.getElementById('payment-methods-summary');
    
    if (methods.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 1.5rem; color: var(--gray-600);">
                <i class="fas fa-credit-card" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                <p style="margin: 0; font-size: 0.875rem;">No tienes métodos de pago configurados</p>
                <p style="margin: 0.25rem 0 0 0; font-size: 0.75rem;">Agrega tu primer método para empezar a recibir pagos</p>
            </div>
        `;
        return;
    }

    const activeMethods = methods.filter(m => m.is_active);
    const inactiveMethods = methods.filter(m => !m.is_active);

    container.innerHTML = `
        <div style="margin-bottom: 1rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                <span style="font-weight: 600; color: var(--gray-700); font-size: 0.875rem;">
                    ${methods.length} método${methods.length !== 1 ? 's' : ''} configurado${methods.length !== 1 ? 's' : ''}
                </span>
                <span style="font-size: 0.75rem; color: var(--gray-500);">
                    ${activeMethods.length} activo${activeMethods.length !== 1 ? 's' : ''}
                </span>
            </div>
            
            ${methods.slice(0, 3).map(method => `
                <div class="payment-method-mini ${method.is_active ? '' : 'inactive'}">
                    <div class="method-icon-mini ${method.type}">
                        ${getMethodIconMini(method.type)}
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                            <span style="font-weight: 600; font-size: 0.875rem; color: var(--gray-800);">
                                ${getMethodName(method.type)}
                            </span>
                            <span class="method-badge-mini ${isManualPayment(method.type) ? 'manual' : 'automatic'}">
                                ${isManualPayment(method.type) ? 'Manual' : 'Auto'}
                            </span>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--gray-600);">
                            ${parseFloat(method.consultation_fee).toFixed(2)} ${method.currency}
                        </div>
                    </div>
                    <div style="color: var(--gray-400);">
                        <i class="fas fa-${method.is_active ? 'check-circle' : 'pause-circle'}"></i>
                    </div>
                </div>
            `).join('')}
            
            ${methods.length > 3 ? `
                <div style="text-align: center; margin-top: 0.75rem;">
                    <span style="font-size: 0.75rem; color: var(--gray-500);">
                        y ${methods.length - 3} más...
                    </span>
                </div>
            ` : ''}
        </div>
    `;
}

function renderPaymentMethodsError() {
    const container = document.getElementById('payment-methods-summary');
    container.innerHTML = `
        <div style="text-align: center; padding: 1.5rem; color: var(--gray-600);">
            <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem; margin-bottom: 0.5rem; color: var(--warning);"></i>
            <p style="margin: 0; font-size: 0.875rem;">Error al cargar métodos de pago</p>
            <button onclick="loadPaymentMethodsSummary()" style="background: none; border: none; color: var(--primary); font-size: 0.75rem; cursor: pointer; margin-top: 0.25rem;">
                Reintentar
            </button>
        </div>
    `;
}

function getMethodIconMini(type) {
    const icons = {
        'paypal': '<i class="fab fa-paypal"></i>',
        'binance_pay': '<i class="fab fa-bitcoin"></i>',
        'pago_movil': '<i class="fas fa-mobile-alt"></i>',
        'stripe': '<i class="fab fa-stripe"></i>',
        'wepay': '<i class="fas fa-credit-card"></i>'
    };
    return icons[type] || '<i class="fas fa-credit-card"></i>';
}

function getMethodName(type) {
    const names = {
        'paypal': 'PayPal',
        'binance_pay': 'Binance Pay',
        'pago_movil': 'Pago Móvil',
        'stripe': 'Stripe',
        'wepay': 'WePay'
    };
    return names[type] || type;
}

function isManualPayment(type) {
    return ['pago_movil', 'binance_pay'].includes(type);
}

async function loadPaymentLinksStats() {
    try {
        const response = await fetch('/api/payment-links/stats', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        if (!response.ok) throw new Error('Error al cargar estadísticas de links');

        const data = await response.json();
        renderPaymentLinksStats(data.data);
    } catch (error) {
        console.error('Error:', error);
        renderPaymentLinksStatsError();
    }
}

function renderPaymentLinksStats(stats) {
    document.getElementById('total-links').textContent = stats.total || 0;
    document.getElementById('active-links').textContent = stats.active || 0;
    document.getElementById('used-links').textContent = stats.used || 0;
}

function renderPaymentLinksStatsError() {
    document.getElementById('total-links').textContent = '-';
    document.getElementById('active-links').textContent = '-';
    document.getElementById('used-links').textContent = '-';
}

async function enableBookingFromProfile() {
    try {
        const response = await fetch('/api/users/{{ $user->id }}/enable-booking', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('¡Reservas públicas activadas exitosamente!');
            window.location.reload();
        } else {
            alert(result.message || 'Error al activar las reservas públicas');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al activar las reservas públicas');
    }
}

function copyBookingUrl(button) {
    const url = '{{ $user->booking_slug ? url("/booking/" . $user->booking_slug) : "" }}';
    if (url) {
        navigator.clipboard.writeText(url).then(() => {
            // Show temporary success message
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i>';
            button.style.background = 'var(--success)';
            button.style.color = 'white';
            
            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.style.background = '';
                button.style.color = '';
            }, 2000);
        }).catch(err => {
            console.error('Error copying to clipboard:', err);
            alert('Error al copiar el enlace');
        });
    }
}
</script> 