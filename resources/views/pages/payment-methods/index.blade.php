@extends('layouts.app')

@section('title', 'Métodos de Pago')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Métodos de Pago</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Métodos de Pago</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">Mis Métodos de Pago</h4>
                            <p class="text-muted mb-0">Configura cómo quieres recibir los pagos de tus consultas</p>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('payment-methods.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Agregar Método
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="payment-methods-container">
                        <div class="d-flex justify-content-center p-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card card-height-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-3">
                                <i class="fas fa-credit-card"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title mb-1">Métodos Automáticos</h5>
                            <p class="text-muted mb-0">PayPal, Stripe - Procesamiento automático</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-height-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-3">
                                <i class="fas fa-mobile-alt"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title mb-1">Métodos Manuales</h5>
                            <p class="text-muted mb-0">Pago Móvil, Binance - Requieren verificación</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-height-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle text-success rounded-circle fs-3">
                                <i class="fas fa-qrcode"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title mb-1">Enlaces de Pago</h5>
                            <p class="text-muted mb-0">Genera QR y enlaces para compartir</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar método -->
<div class="modal fade" id="editPaymentMethodModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Método de Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editPaymentMethodForm">
                    <div id="edit-form-content">
                        <!-- Contenido dinámico -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="savePaymentMethod">
                    <i class="fas fa-save me-1"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.payment-method-card {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    transition: all 0.3s ease;
    margin-bottom: 1rem;
}

.payment-method-card:hover {
    border-color: #0d6efd;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
}

.payment-method-card.inactive {
    opacity: 0.6;
    background-color: #f8f9fa;
}

.method-icon {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.method-icon.paypal { background: #0070ba; }
.method-icon.binance_pay { background: #f3ba2f; color: #000; }
.method-icon.pago_movil { background: #e74c3c; }
.method-icon.stripe { background: #635bff; }
.method-icon.wepay { background: #0099cc; }

.method-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.method-badge.manual {
    background: #fff3cd;
    color: #856404;
}

.method-badge.automatic {
    background: #d1ecf1;
    color: #0c5460;
}

.sortable-handle {
    cursor: move;
    color: #6c757d;
}

.sortable-handle:hover {
    color: #0d6efd;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadPaymentMethods();
    initializeSortable();
});

let paymentMethods = [];
let currentEditingId = null;

async function loadPaymentMethods() {
    try {
        const response = await fetch('/api/payment-methods', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (!response.ok) throw new Error('Error al cargar métodos de pago');

        const data = await response.json();
        paymentMethods = data.data.data || [];
        renderPaymentMethods();
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error al cargar los métodos de pago', 'error');
    }
}

function renderPaymentMethods() {
    const container = document.getElementById('payment-methods-container');
    
    if (paymentMethods.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <div class="avatar-lg mx-auto mb-4">
                    <div class="avatar-title bg-primary-subtle text-primary rounded-circle">
                        <i class="fas fa-credit-card fa-2x"></i>
                    </div>
                </div>
                <h5>No tienes métodos de pago configurados</h5>
                <p class="text-muted">Agrega tu primer método de pago para empezar a recibir pagos de tus consultas</p>
                <a href="{{ route('payment-methods.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Agregar Primer Método
                </a>
            </div>
        `;
        return;
    }

    const html = `
        <div id="sortable-payment-methods">
            ${paymentMethods.map(method => `
                <div class="payment-method-card p-3 ${method.is_active ? '' : 'inactive'}" data-id="${method.id}">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="sortable-handle me-2">
                                <i class="fas fa-grip-vertical"></i>
                            </div>
                            <div class="method-icon ${method.type}">
                                ${getMethodIcon(method.type)}
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex align-items-center mb-2">
                                <h5 class="mb-0 me-2">${getMethodName(method.type)}</h5>
                                <span class="method-badge ${isManualPayment(method.type) ? 'manual' : 'automatic'}">
                                    ${isManualPayment(method.type) ? 'Manual' : 'Automático'}
                                </span>
                                ${!method.is_active ? '<span class="badge bg-secondary ms-2">Inactivo</span>' : ''}
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <small class="text-muted">Tarifa:</small><br>
                                    <strong>${parseFloat(method.consultation_fee).toFixed(2)} ${method.currency}</strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Configuración:</small><br>
                                    <span class="text-truncate d-block">${getConfigSummary(method)}</span>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Creado:</small><br>
                                    ${new Date(method.created_at).toLocaleDateString()}
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="dropdown">
                                <button class="btn btn-soft-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="editPaymentMethod(${method.id})">
                                        <i class="fas fa-edit me-2"></i>Editar
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="togglePaymentMethod(${method.id}, ${method.is_active})">
                                        <i class="fas fa-${method.is_active ? 'pause' : 'play'} me-2"></i>
                                        ${method.is_active ? 'Desactivar' : 'Activar'}
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="deletePaymentMethod(${method.id})">
                                        <i class="fas fa-trash me-2"></i>Eliminar
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
    
    container.innerHTML = html;
    initializeSortable();
}

function getMethodIcon(type) {
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

function getConfigSummary(method) {
    const config = method.config;
    switch (method.type) {
        case 'paypal':
            return config.paypal_email || 'No configurado';
        case 'binance_pay':
            return config.binance_id || 'No configurado';
        case 'pago_movil':
            return `${config.receiver_phone || 'N/A'} - ${config.receiver_bank || 'N/A'}`;
        case 'stripe':
            return config.stripe_account_id ? 'Configurado' : 'No configurado';
        default:
            return 'Configurado';
    }
}

function initializeSortable() {
    const sortableEl = document.getElementById('sortable-payment-methods');
    if (sortableEl) {
        new Sortable(sortableEl, {
            handle: '.sortable-handle',
            animation: 150,
            onEnd: function(evt) {
                updatePaymentMethodsOrder();
            }
        });
    }
}

async function updatePaymentMethodsOrder() {
    const elements = document.querySelectorAll('.payment-method-card');
    const orderedMethods = Array.from(elements).map((el, index) => ({
        id: parseInt(el.dataset.id),
        order: index
    }));

    try {
        const response = await fetch('/api/payment-methods/update-order', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                payment_methods: orderedMethods
            })
        });

        if (!response.ok) throw new Error('Error al actualizar orden');
        
        showAlert('Orden actualizado correctamente', 'success');
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error al actualizar el orden', 'error');
        loadPaymentMethods(); // Recargar para revertir cambios
    }
}

async function togglePaymentMethod(id, currentStatus) {
    try {
        const response = await fetch(`/api/payment-methods/${id}`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                is_active: !currentStatus
            })
        });

        if (!response.ok) throw new Error('Error al cambiar estado');

        showAlert(`Método ${!currentStatus ? 'activado' : 'desactivado'} correctamente`, 'success');
        loadPaymentMethods();
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error al cambiar el estado del método', 'error');
    }
}

async function deletePaymentMethod(id) {
    if (!confirm('¿Estás seguro de que quieres eliminar este método de pago?')) {
        return;
    }

    try {
        const response = await fetch(`/api/payment-methods/${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message || 'Error al eliminar método');
        }

        showAlert('Método de pago eliminado correctamente', 'success');
        loadPaymentMethods();
    } catch (error) {
        console.error('Error:', error);
        showAlert(error.message || 'Error al eliminar el método de pago', 'error');
    }
}

function showAlert(message, type = 'info') {
    const alertClass = type === 'error' ? 'alert-danger' : 
                      type === 'success' ? 'alert-success' : 'alert-info';
    
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.querySelector('.container-fluid').prepend(alert);
    
    setTimeout(() => {
        alert.remove();
    }, 5000);
}
</script>
@endpush 