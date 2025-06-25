@extends('layouts.app')

@section('title', 'Métodos de Pago')

@section('content')
<div class="container-fluid">
    <!-- Header mejorado -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-text">
                <h1 class="page-title">
                    <i class="fas fa-credit-card me-3"></i>
                    Métodos de Pago
                </h1>
                <p class="page-subtitle">Gestiona tus métodos de pago y configura cómo recibir pagos de tus pacientes</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-outline-secondary me-2" onclick="loadPaymentMethods()">
                    <i class="fas fa-sync-alt me-2"></i>
                    Actualizar
                </button>
                <a href="{{ route('payment-methods.web.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Nuevo Método
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid mb-4">
        <div class="stat-card stat-primary">
            <div class="stat-icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="total-methods">-</div>
                <div class="stat-label">Métodos Totales</div>
            </div>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="active-methods">-</div>
                <div class="stat-label">Métodos Activos</div>
            </div>
        </div>
        <div class="stat-card stat-warning">
            <div class="stat-icon">
                <i class="fas fa-hand-paper"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="manual-methods">-</div>
                <div class="stat-label">Métodos Manuales</div>
            </div>
        </div>
        <div class="stat-card stat-info">
            <div class="stat-icon">
                <i class="fas fa-robot"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number" id="automatic-methods">-</div>
                <div class="stat-label">Métodos Automáticos</div>
            </div>
        </div>
    </div>

    <!-- Payment Methods List -->
    <div class="main-card">
        <div class="card-header-modern">
            <div class="header-left">
                <h5 class="card-title">
                    <i class="fas fa-list me-2"></i>
                    Mis Métodos de Pago
                </h5>
                <span class="methods-count" id="methods-count">0 métodos</span>
            </div>
            <div class="header-right">
                <div class="view-toggle">
                    <button class="toggle-btn active" data-view="grid" title="Vista en grilla">
                        <i class="fas fa-th-large"></i>
                    </button>
                    <button class="toggle-btn" data-view="list" title="Vista en lista">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card-body-modern">
            <div id="payment-methods-container" class="methods-grid">
                <div class="loading-state">
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                    </div>
                    <p class="loading-text">Cargando métodos de pago...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal mejorado -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modern-modal">
            <div class="modal-header border-0">
                <div class="modal-icon delete-icon">
                    <i class="fas fa-trash-alt"></i>
                </div>
            </div>
            <div class="modal-body text-center">
                <h4 class="modal-title mb-3">¿Eliminar método de pago?</h4>
                <p class="modal-text">Esta acción no se puede deshacer. El método de pago será eliminado permanentemente.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="fas fa-trash me-2"></i>Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="successToast" class="toast" role="alert">
        <div class="toast-header bg-success text-white">
            <i class="fas fa-check-circle me-2"></i>
            <strong class="me-auto">Éxito</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body" id="successMessage"></div>
    </div>
    
    <div id="errorToast" class="toast" role="alert">
        <div class="toast-header bg-danger text-white">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong class="me-auto">Error</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body" id="errorMessage"></div>
    </div>
</div>
@endsection

@push('styles')
<style>
:root {
    --primary: #007bff;
    --success: #28a745;
    --danger: #dc3545;
    --warning: #ffc107;
    --info: #17a2b8;
    --gray-50: #f8f9fa;
    --gray-100: #f8f9fa;
    --gray-200: #e9ecef;
    --gray-300: #dee2e6;
    --gray-400: #ced4da;
    --gray-500: #6c757d;
    --gray-600: #495057;
    --gray-700: #495057;
    --gray-800: #343a40;
    --gray-900: #212529;
    --white: #ffffff;
    --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    --shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    --shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.175);
    --border-radius: 0.5rem;
    --border-radius-lg: 0.75rem;
    --transition: all 0.15s ease-in-out;
}

/* Page Header */
.page-header {
    background: linear-gradient(135deg, var(--white) 0%, var(--gray-50) 100%);
    border-radius: var(--border-radius-lg);
    padding: 2rem;
    margin-bottom: 2rem;
    border: 1px solid var(--gray-200);
    box-shadow: var(--shadow-sm);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--gray-800);
    margin: 0;
    display: flex;
    align-items: center;
}

.page-title i {
    color: var(--primary);
}

.page-subtitle {
    color: var(--gray-600);
    margin: 0.5rem 0 0 0;
    font-size: 1.1rem;
}

.header-actions {
    display: flex;
    align-items: center;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.stat-card {
    background: var(--white);
    border-radius: var(--border-radius-lg);
    padding: 1.5rem;
    border: 1px solid var(--gray-200);
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--white);
}

.stat-primary .stat-icon { background: linear-gradient(135deg, var(--primary), #0056b3); }
.stat-success .stat-icon { background: linear-gradient(135deg, var(--success), #1e7e34); }
.stat-warning .stat-icon { background: linear-gradient(135deg, var(--warning), #e0a800); }
.stat-info .stat-icon { background: linear-gradient(135deg, var(--info), #138496); }

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--gray-800);
    line-height: 1;
}

.stat-label {
    color: var(--gray-600);
    font-size: 0.875rem;
    font-weight: 500;
    margin-top: 0.25rem;
}

/* Toast Styles */
.toast-container .toast {
    display: none;
}

.toast-container .toast.show {
    display: block;
}

/* Main Card */
.main-card {
    background: var(--white);
    border-radius: var(--border-radius-lg);
    border: 1px solid var(--gray-200);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}

.card-header-modern {
    background: linear-gradient(135deg, var(--gray-50) 0%, var(--white) 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--gray-800);
    margin: 0;
    display: flex;
    align-items: center;
}

.methods-count {
    background: var(--gray-100);
    color: var(--gray-600);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
    margin-left: 1rem;
}

.view-toggle {
    display: flex;
    background: var(--gray-100);
    border-radius: var(--border-radius);
    padding: 0.25rem;
}

.toggle-btn {
    background: transparent;
    border: none;
    padding: 0.5rem 0.75rem;
    border-radius: calc(var(--border-radius) - 0.25rem);
    color: var(--gray-600);
    transition: var(--transition);
    cursor: pointer;
}

.toggle-btn.active {
    background: var(--white);
    color: var(--primary);
    box-shadow: var(--shadow-sm);
}

.card-body-modern {
    padding: 2rem;
}

/* Methods Grid */
.methods-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 1.5rem;
}

.methods-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

/* Payment Method Card */
.payment-method-card {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius-lg);
    padding: 1.5rem;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.payment-method-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary), var(--success));
    opacity: 0;
    transition: var(--transition);
}

.payment-method-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary);
}

.payment-method-card:hover::before {
    opacity: 1;
}

.payment-method-card.inactive {
    opacity: 0.7;
    background: var(--gray-50);
}

.payment-method-card.inactive:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

/* Method Header */
.method-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
}

.method-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex: 1;
}

.method-icon {
    width: 56px;
    height: 56px;
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--white);
    font-weight: bold;
    box-shadow: var(--shadow);
}

.method-icon.paypal { background: linear-gradient(135deg, #0070ba, #003087); }
.method-icon.binance_pay { background: linear-gradient(135deg, #f3ba2f, #f0b90b); color: #000; }
.method-icon.pago_movil { background: linear-gradient(135deg, #e74c3c, #c0392b); }
.method-icon.stripe { background: linear-gradient(135deg, #635bff, #4f46e5); }
.method-icon.wepay { background: linear-gradient(135deg, #0099cc, #0077aa); }

.method-details h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--gray-800);
}

.method-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.method-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.method-badge.manual {
    background: linear-gradient(135deg, #fff3cd, #ffeaa7);
    color: #856404;
    border: 1px solid #f6d55c;
}

.method-badge.automatic {
    background: linear-gradient(135deg, #d1ecf1, #a8e6cf);
    color: #0c5460;
    border: 1px solid #7fcdcd;
}

/* Method Body */
.method-body {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.method-field {
    text-align: center;
    padding: 1rem;
    background: var(--gray-50);
    border-radius: var(--border-radius);
    border: 1px solid var(--gray-200);
    transition: var(--transition);
}

.method-field:hover {
    background: var(--white);
    border-color: var(--primary);
    transform: translateY(-1px);
}

.method-field label {
    display: block;
    font-size: 0.75rem;
    color: var(--gray-500);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.method-field .value {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-800);
}

.method-field .value.amount {
    color: var(--success);
    font-size: 1.25rem;
}

/* Method Actions */
.method-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
}

.action-btn {
    width: 40px;
    height: 40px;
    border-radius: var(--border-radius);
    border: 1px solid var(--gray-300);
    background: var(--white);
    color: var(--gray-600);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
    cursor: pointer;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.action-btn.edit:hover {
    background: var(--primary);
    color: var(--white);
    border-color: var(--primary);
}

.action-btn.delete:hover {
    background: var(--danger);
    color: var(--white);
    border-color: var(--danger);
}

/* Method Instructions */
.method-instructions {
    background: var(--gray-50);
    border-radius: var(--border-radius);
    padding: 1rem;
    border-left: 4px solid var(--info);
}

.method-instructions label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
    display: block;
}

.method-instructions p {
    margin: 0;
    color: var(--gray-600);
    font-style: italic;
}

/* Loading State */
.loading-state {
    text-align: center;
    padding: 4rem 2rem;
}

.loading-spinner {
    margin-bottom: 1rem;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid var(--gray-200);
    border-top: 4px solid var(--primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-text {
    color: var(--gray-500);
    font-size: 1.1rem;
    margin: 0;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-state i {
    font-size: 4rem;
    color: var(--gray-300);
    margin-bottom: 1.5rem;
}

.empty-state h4 {
    color: var(--gray-700);
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.empty-state p {
    color: var(--gray-500);
    margin-bottom: 2rem;
    font-size: 1.1rem;
}

/* Modal Improvements */
.modern-modal {
    border: none;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-lg);
}

.modal-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 2rem;
}

.delete-icon {
    background: linear-gradient(135deg, #ffebee, #ffcdd2);
    color: var(--danger);
}

.modal-title {
    color: var(--gray-800);
    font-weight: 600;
}

.modal-text {
    color: var(--gray-600);
    font-size: 1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .methods-grid {
        grid-template-columns: 1fr;
    }
    
    .method-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .method-actions {
        align-self: stretch;
        justify-content: center;
    }
    
    .method-body {
        grid-template-columns: 1fr;
    }
    
    .card-header-modern {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
}

@media (max-width: 576px) {
    .page-header {
        padding: 1.5rem;
    }
    
    .card-body-modern {
        padding: 1rem;
    }
    
    .payment-method-card {
        padding: 1rem;
    }
}

/* Animations */
.fade-in {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.slide-up {
    animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
</style>
@endpush

@push('scripts')
<script>
let paymentMethods = [];
let deleteMethodId = null;
let currentView = 'grid';

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadPaymentMethods();
    initializeEventListeners();
});

function initializeEventListeners() {
    // Delete confirmation
    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (deleteMethodId) {
            deletePaymentMethod(deleteMethodId);
        }
    });
    
    // View toggle
    document.querySelectorAll('.toggle-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            switchView(view);
        });
    });
}

function switchView(view) {
    currentView = view;
    
    // Update toggle buttons
    document.querySelectorAll('.toggle-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.view === view);
    });
    
    // Update container class
    const container = document.getElementById('payment-methods-container');
    container.className = view === 'grid' ? 'methods-grid' : 'methods-list';
    
    // Re-render if methods are loaded
    if (paymentMethods.length > 0) {
        renderPaymentMethods();
    }
}

async function loadPaymentMethods() {
    try {
        showLoadingState();
        
        const response = await fetch('/api/payment-methods', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) {
            throw new Error('Error al cargar métodos de pago');
        }
        
        const data = await response.json();
        paymentMethods = data.data.data || [];
        
        renderPaymentMethods();
        updateStats();
        
    } catch (error) {
        console.error('Error:', error);
        showError('Error al cargar los métodos de pago');
        showErrorState();
    }
}

function showLoadingState() {
    const container = document.getElementById('payment-methods-container');
    container.innerHTML = `
        <div class="loading-state">
            <div class="loading-spinner">
                <div class="spinner"></div>
            </div>
            <p class="loading-text">Cargando métodos de pago...</p>
        </div>
    `;
}

function showErrorState() {
    const container = document.getElementById('payment-methods-container');
    container.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-exclamation-triangle" style="color: var(--danger);"></i>
            <h4 style="color: var(--danger);">Error al cargar métodos</h4>
            <p>Hubo un problema al cargar tus métodos de pago. Por favor, intenta de nuevo.</p>
            <button class="btn btn-primary" onclick="loadPaymentMethods()">
                <i class="fas fa-redo me-2"></i>
                Reintentar
            </button>
        </div>
    `;
}

function updateStats() {
    const total = paymentMethods.length;
    const active = paymentMethods.filter(m => m.is_active).length;
    const manual = paymentMethods.filter(m => isManualPayment(m.type)).length;
    const automatic = paymentMethods.filter(m => !isManualPayment(m.type)).length;
    
    document.getElementById('total-methods').textContent = total;
    document.getElementById('active-methods').textContent = active;
    document.getElementById('manual-methods').textContent = manual;
    document.getElementById('automatic-methods').textContent = automatic;
    document.getElementById('methods-count').textContent = `${total} método${total !== 1 ? 's' : ''}`;
}

function renderPaymentMethods() {
    const container = document.getElementById('payment-methods-container');
    
    if (paymentMethods.length === 0) {
        container.innerHTML = `
            <div class="empty-state fade-in">
                <i class="fas fa-credit-card"></i>
                <h4>No hay métodos de pago configurados</h4>
                <p>Agrega tu primer método de pago para comenzar a recibir pagos de tus pacientes.</p>
                <a href="{{ route('payment-methods.web.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>
                    Agregar Método de Pago
                </a>
            </div>
        `;
        return;
    }
    
    const html = paymentMethods.map((method, index) => `
        <div class="payment-method-card ${method.is_active ? '' : 'inactive'} slide-up" style="animation-delay: ${index * 0.1}s">
            <div class="method-header">
                <div class="method-info">
                    <div class="method-icon ${method.type}">
                        ${getMethodIcon(method.type)}
                    </div>
                    <div class="method-details">
                        <h4>${getMethodName(method.type)}</h4>
                        <div class="method-badges">
                            <span class="method-badge ${isManualPayment(method.type) ? 'manual' : 'automatic'}">
                                ${isManualPayment(method.type) ? 'Manual' : 'Automático'}
                            </span>
                            ${!method.is_active ? '<span class="badge bg-secondary">Inactivo</span>' : ''}
                        </div>
                    </div>
                </div>
                <div class="method-actions">
                    <button class="action-btn edit" onclick="editMethod(${method.id})" title="Editar método">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn delete" onclick="confirmDelete(${method.id})" title="Eliminar método">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            
            <div class="method-body">
                <div class="method-field">
                    <label>Tarifa</label>
                    <div class="value amount">
                        $${parseFloat(method.consultation_fee || 0).toFixed(2)}
                    </div>
                </div>
                <div class="method-field">
                    <label>Moneda</label>
                    <div class="value">
                        ${method.currency || 'USD'}
                    </div>
                </div>
                <div class="method-field">
                    <label>Estado</label>
                    <div class="value">
                        <span class="badge ${method.is_active ? 'bg-success' : 'bg-secondary'}">
                            ${method.is_active ? 'Activo' : 'Inactivo'}
                        </span>
                    </div>
                </div>
                <div class="method-field">
                    <label>Configuración</label>
                    <div class="value" style="font-size: 0.875rem;">
                        ${getConfigSummary(method)}
                    </div>
                </div>
            </div>
            
            ${method.instructions ? `
                <div class="method-instructions">
                    <label>Instrucciones para el paciente:</label>
                    <p>${method.instructions}</p>
                </div>
            ` : ''}
        </div>
    `).join('');
    
    container.innerHTML = html;
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
    if (!method.config) return 'No configurado';
    
    switch (method.type) {
        case 'paypal':
            return method.config.paypal_email || 'No configurado';
        case 'pago_movil':
            const phone = method.config.receiver_phone || 'N/A';
            const bank = method.config.receiver_bank || 'N/A';
            return `${phone} - ${bank}`;
        case 'binance_pay':
            return method.config.binance_id || 'No configurado';
        case 'stripe':
            return method.config.stripe_account_id ? 'Configurado' : 'No configurado';
        default:
            return 'Configurado';
    }
}

function editMethod(methodId) {
    window.location.href = `/payment-methods/${methodId}/edit`;
}

function confirmDelete(methodId) {
    deleteMethodId = methodId;
    // Verificar si Bootstrap está disponible
    if (typeof bootstrap !== 'undefined') {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    } else {
        // Fallback si Bootstrap no está disponible
        if (confirm('¿Estás seguro de que quieres eliminar este método de pago? Esta acción no se puede deshacer.')) {
            deletePaymentMethod(methodId);
        }
    }
}

async function deletePaymentMethod(methodId) {
    try {
        const response = await fetch(`/api/payment-methods/${methodId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) {
            throw new Error('Error al eliminar el método de pago');
        }
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
        modal.hide();
        
        // Reload methods
        await loadPaymentMethods();
        
        showSuccess('Método de pago eliminado correctamente');
        
    } catch (error) {
        console.error('Error:', error);
        showError('Error al eliminar el método de pago');
    }
}

function showSuccess(message) {
    if (typeof bootstrap !== 'undefined') {
        document.getElementById('successMessage').textContent = message;
        const toast = new bootstrap.Toast(document.getElementById('successToast'));
        toast.show();
    } else {
        alert('✓ ' + message);
    }
}

function showError(message) {
    if (typeof bootstrap !== 'undefined') {
        document.getElementById('errorMessage').textContent = message;
        const toast = new bootstrap.Toast(document.getElementById('errorToast'));
        toast.show();
    } else {
        alert('✗ ' + message);
    }
}
</script>
@endpush 