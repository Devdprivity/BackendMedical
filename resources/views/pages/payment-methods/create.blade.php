@extends('layouts.app')

@section('title', 'Agregar Método de Pago')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Agregar Método de Pago</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('payment-methods.web.index') }}">Métodos de Pago</a></li>
                        <li class="breadcrumb-item active">Agregar</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Configurar Nuevo Método de Pago</h4>
                    <p class="text-muted mb-0">Selecciona y configura cómo quieres recibir los pagos de tus consultas</p>
                </div>
                <div class="card-body">
                    <form id="paymentMethodForm">
                        <!-- Paso 1: Selección de tipo -->
                        <div id="step-1" class="form-step">
                            <h5 class="mb-3">Paso 1: Selecciona el tipo de pago</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="payment-type-card" data-type="paypal">
                                        <div class="method-icon paypal">
                                            <i class="fab fa-paypal"></i>
                                        </div>
                                        <div class="method-info">
                                            <h6>PayPal</h6>
                                            <p class="text-muted">Pago automático con PayPal</p>
                                            <span class="badge bg-success">Automático</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="payment-type-card" data-type="stripe">
                                        <div class="method-icon stripe">
                                            <i class="fab fa-stripe"></i>
                                        </div>
                                        <div class="method-info">
                                            <h6>Stripe</h6>
                                            <p class="text-muted">Tarjetas de crédito/débito</p>
                                            <span class="badge bg-success">Automático</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="payment-type-card" data-type="pago_movil">
                                        <div class="method-icon pago_movil">
                                            <i class="fas fa-mobile-alt"></i>
                                        </div>
                                        <div class="method-info">
                                            <h6>Pago Móvil</h6>
                                            <p class="text-muted">Transferencia bancaria móvil</p>
                                            <span class="badge bg-warning">Manual</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="payment-type-card" data-type="binance_pay">
                                        <div class="method-icon binance_pay">
                                            <i class="fab fa-bitcoin"></i>
                                        </div>
                                        <div class="method-info">
                                            <h6>Binance Pay</h6>
                                            <p class="text-muted">Pagos con criptomonedas</p>
                                            <span class="badge bg-warning">Manual</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Paso 2: Configuración básica -->
                        <div id="step-2" class="form-step" style="display: none;">
                            <h5 class="mb-3">Paso 2: Configuración básica</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tarifa por consulta <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="consultation_fee" step="0.01" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Moneda <span class="text-danger">*</span></label>
                                        <select class="form-select" id="currency" required>
                                            <option value="">Seleccionar moneda</option>
                                            <option value="USD">USD - Dólar Estadounidense</option>
                                            <option value="EUR">EUR - Euro</option>
                                            <option value="VES">VES - Bolívar Venezolano</option>
                                            <option value="USDT">USDT - Tether USD</option>
                                            <option value="BTC">BTC - Bitcoin</option>
                                            <option value="BNB">BNB - Binance Coin</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Instrucciones para el paciente</label>
                                <textarea class="form-control" id="instructions" rows="3" placeholder="Instrucciones adicionales que verá el paciente al realizar el pago..."></textarea>
                                <div class="form-text">
                                    Puedes usar variables: {doctor_name}, {appointment_date}, {amount}, {currency}, {appointment_id}
                                </div>
                            </div>
                        </div>

                        <!-- Paso 3: Configuración específica -->
                        <div id="step-3" class="form-step" style="display: none;">
                            <h5 class="mb-3">Paso 3: Configuración específica</h5>
                            
                            <!-- PayPal Config -->
                            <div id="config-paypal" class="payment-config" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">Email de PayPal <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="paypal_email" placeholder="tu-email@paypal.com">
                                    <div class="form-text">El email asociado a tu cuenta de PayPal Business</div>
                                </div>
                            </div>

                            <!-- Stripe Config -->
                            <div id="config-stripe" class="payment-config" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">Stripe Account ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="stripe_account_id" placeholder="acct_...">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Webhook Secret <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="webhook_secret" placeholder="whsec_...">
                                </div>
                            </div>

                            <!-- Pago Móvil Config -->
                            <div id="config-pago_movil" class="payment-config" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Teléfono receptor <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="receiver_phone" placeholder="04241234567" maxlength="11">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Cédula receptor <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="receiver_cedula" placeholder="V-12345678">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Banco receptor <span class="text-danger">*</span></label>
                                            <select class="form-select" id="receiver_bank">
                                                <option value="">Seleccionar banco</option>
                                                <option value="0102">Banco de Venezuela</option>
                                                <option value="0104">Venezolano de Crédito</option>
                                                <option value="0105">Banco Mercantil</option>
                                                <option value="0108">Banco Provincial</option>
                                                <option value="0114">Bancaribe</option>
                                                <option value="0115">Banco Exterior</option>
                                                <option value="0128">Banco Caroní</option>
                                                <option value="0134">Banesco</option>
                                                <option value="0137">Banco Sofitasa</option>
                                                <option value="0138">Banco Plaza</option>
                                                <option value="0151">Banco Fondo Común (BFC)</option>
                                                <option value="0156">100% Banco</option>
                                                <option value="0157">DelSur Banco</option>
                                                <option value="0163">Banco del Tesoro</option>
                                                <option value="0166">Banco Agrícola de Venezuela</option>
                                                <option value="0168">Bancrecer</option>
                                                <option value="0169">Mi Banco</option>
                                                <option value="0171">Banco Activo</option>
                                                <option value="0172">Bancamiga</option>
                                                <option value="0174">Banplus</option>
                                                <option value="0175">Banco Bicentenario</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nombre del titular <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="receiver_name" placeholder="Nombre completo del titular">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Binance Pay Config -->
                            <div id="config-binance_pay" class="payment-config" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">Binance ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="binance_id" placeholder="Tu ID de Binance Pay">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Redes soportadas</label>
                                    <div class="form-check-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="BSC" id="network_bsc" checked>
                                            <label class="form-check-label" for="network_bsc">BSC (Binance Smart Chain)</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="ETH" id="network_eth">
                                            <label class="form-check-label" for="network_eth">Ethereum</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="TRX" id="network_trx">
                                            <label class="form-check-label" for="network_trx">TRON</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Criptomonedas aceptadas</label>
                                    <div class="form-check-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="USDT" id="crypto_usdt" checked>
                                            <label class="form-check-label" for="crypto_usdt">USDT</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="BTC" id="crypto_btc">
                                            <label class="form-check-label" for="crypto_btc">Bitcoin</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="BNB" id="crypto_bnb">
                                            <label class="form-check-label" for="crypto_bnb">BNB</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="ETH" id="crypto_eth">
                                            <label class="form-check-label" for="crypto_eth">Ethereum</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary" id="prevBtn" onclick="changeStep(-1)" style="display: none;">
                                <i class="fas fa-arrow-left me-1"></i> Anterior
                            </button>
                            <button type="button" class="btn btn-primary" id="nextBtn" onclick="changeStep(1)">
                                Siguiente <i class="fas fa-arrow-right ms-1"></i>
                            </button>
                            <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                                <i class="fas fa-save me-1"></i> Crear Método de Pago
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Información</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Tipos de Pago</h6>
                        <p class="mb-2"><strong>Automáticos:</strong> Los pagos se procesan automáticamente (PayPal, Stripe).</p>
                        <p class="mb-0"><strong>Manuales:</strong> Requieren verificación manual de tu parte (Pago Móvil, Binance Pay).</p>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">Importante</h6>
                        <p class="mb-0">Asegúrate de tener las cuentas configuradas correctamente antes de activar los métodos de pago.</p>
                    </div>
                </div>
            </div>

            <!-- Preview card -->
            <div class="card mt-3" id="previewCard" style="display: none;">
                <div class="card-header">
                    <h5 class="card-title mb-0">Vista Previa</h5>
                </div>
                <div class="card-body">
                    <div id="previewContent">
                        <!-- Preview content will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.payment-type-card {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    height: 100%;
}

.payment-type-card:hover {
    border-color: #0d6efd;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
}

.payment-type-card.selected {
    border-color: #0d6efd;
    background: linear-gradient(135deg, rgba(13, 110, 253, 0.05), rgba(13, 110, 253, 0.1));
}

.method-icon {
    width: 64px;
    height: 64px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    margin: 0 auto 1rem;
}

.method-icon.paypal { background: #0070ba; }
.method-icon.binance_pay { background: #f3ba2f; color: #000; }
.method-icon.pago_movil { background: #e74c3c; }
.method-icon.stripe { background: #635bff; }

.form-check-group {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.5rem;
}

.form-step {
    min-height: 400px;
}
</style>
@endpush

@push('scripts')
<script>
let currentStep = 1;
let selectedType = null;

document.addEventListener('DOMContentLoaded', function() {
    initializeForm();
});

function initializeForm() {
    // Payment type selection
    document.querySelectorAll('.payment-type-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.payment-type-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            selectedType = this.dataset.type;
            document.getElementById('nextBtn').disabled = false;
            updatePreview();
        });
    });

    // Form inputs
    document.querySelectorAll('#step-2 input, #step-2 select, #step-2 textarea').forEach(input => {
        input.addEventListener('input', updatePreview);
    });

    // Form submission
    document.getElementById('paymentMethodForm').addEventListener('submit', handleSubmit);
}

function changeStep(direction) {
    const currentStepEl = document.getElementById(`step-${currentStep}`);
    
    if (direction === 1) {
        // Validate current step
        if (!validateStep(currentStep)) {
            return;
        }
        
        if (currentStep < 3) {
            currentStep++;
            showStep(currentStep);
        }
    } else {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    }
}

function showStep(step) {
    // Hide all steps
    document.querySelectorAll('.form-step').forEach(el => el.style.display = 'none');
    
    // Show current step
    document.getElementById(`step-${step}`).style.display = 'block';
    
    // Update buttons
    document.getElementById('prevBtn').style.display = step > 1 ? 'block' : 'none';
    document.getElementById('nextBtn').style.display = step < 3 ? 'block' : 'none';
    document.getElementById('submitBtn').style.display = step === 3 ? 'block' : 'none';
    
    // Show specific config for step 3
    if (step === 3 && selectedType) {
        document.querySelectorAll('.payment-config').forEach(config => {
            config.style.display = 'none';
        });
        document.getElementById(`config-${selectedType}`).style.display = 'block';
    }
    
    updatePreview();
}

function validateStep(step) {
    switch (step) {
        case 1:
            if (!selectedType) {
                alert('Por favor selecciona un tipo de pago');
                return false;
            }
            break;
        case 2:
            const fee = document.getElementById('consultation_fee').value;
            const currency = document.getElementById('currency').value;
            
            if (!fee || fee <= 0) {
                alert('Por favor ingresa una tarifa válida');
                return false;
            }
            
            if (!currency) {
                alert('Por favor selecciona una moneda');
                return false;
            }
            break;
        case 3:
            return validateConfigStep();
    }
    return true;
}

function validateConfigStep() {
    switch (selectedType) {
        case 'paypal':
            const paypalEmail = document.getElementById('paypal_email').value;
            if (!paypalEmail || !isValidEmail(paypalEmail)) {
                alert('Por favor ingresa un email de PayPal válido');
                return false;
            }
            break;
            
        case 'stripe':
            const accountId = document.getElementById('stripe_account_id').value;
            const webhookSecret = document.getElementById('webhook_secret').value;
            if (!accountId || !webhookSecret) {
                alert('Por favor completa todos los campos de Stripe');
                return false;
            }
            break;
            
        case 'pago_movil':
            const phone = document.getElementById('receiver_phone').value;
            const cedula = document.getElementById('receiver_cedula').value;
            const bank = document.getElementById('receiver_bank').value;
            const name = document.getElementById('receiver_name').value;
            
            if (!phone || phone.length !== 11) {
                alert('Por favor ingresa un teléfono válido (11 dígitos)');
                return false;
            }
            if (!cedula || !bank || !name) {
                alert('Por favor completa todos los campos de Pago Móvil');
                return false;
            }
            break;
            
        case 'binance_pay':
            const binanceId = document.getElementById('binance_id').value;
            if (!binanceId) {
                alert('Por favor ingresa tu Binance ID');
                return false;
            }
            break;
    }
    return true;
}

function updatePreview() {
    if (currentStep < 2) return;
    
    const previewCard = document.getElementById('previewCard');
    const previewContent = document.getElementById('previewContent');
    
    const fee = document.getElementById('consultation_fee').value;
    const currency = document.getElementById('currency').value;
    
    if (selectedType && fee && currency) {
        previewCard.style.display = 'block';
        
        const methodName = getMethodName(selectedType);
        const isManual = ['pago_movil', 'binance_pay'].includes(selectedType);
        
        previewContent.innerHTML = `
            <div class="d-flex align-items-center mb-3">
                <div class="method-icon ${selectedType} me-3" style="width: 40px; height: 40px; font-size: 1.2rem;">
                    ${getMethodIcon(selectedType)}
                </div>
                <div>
                    <h6 class="mb-1">${methodName}</h6>
                    <span class="badge ${isManual ? 'bg-warning' : 'bg-success'}">${isManual ? 'Manual' : 'Automático'}</span>
                </div>
            </div>
            <div class="mb-2">
                <strong>Tarifa:</strong> ${parseFloat(fee || 0).toFixed(2)} ${currency}
            </div>
            ${getConfigPreview()}
        `;
    } else {
        previewCard.style.display = 'none';
    }
}

function getMethodName(type) {
    const names = {
        'paypal': 'PayPal',
        'binance_pay': 'Binance Pay',
        'pago_movil': 'Pago Móvil',
        'stripe': 'Stripe'
    };
    return names[type] || type;
}

function getMethodIcon(type) {
    const icons = {
        'paypal': '<i class="fab fa-paypal"></i>',
        'binance_pay': '<i class="fab fa-bitcoin"></i>',
        'pago_movil': '<i class="fas fa-mobile-alt"></i>',
        'stripe': '<i class="fab fa-stripe"></i>'
    };
    return icons[type] || '<i class="fas fa-credit-card"></i>';
}

function getConfigPreview() {
    if (currentStep < 3) return '';
    
    switch (selectedType) {
        case 'paypal':
            const email = document.getElementById('paypal_email').value;
            return email ? `<div><strong>Email:</strong> ${email}</div>` : '';
            
        case 'pago_movil':
            const phone = document.getElementById('receiver_phone').value;
            const bank = document.getElementById('receiver_bank').value;
            return (phone || bank) ? `<div><strong>Teléfono:</strong> ${phone}<br><strong>Banco:</strong> ${bank}</div>` : '';
            
        case 'binance_pay':
            const binanceId = document.getElementById('binance_id').value;
            return binanceId ? `<div><strong>Binance ID:</strong> ${binanceId}</div>` : '';
            
        default:
            return '';
    }
}

async function handleSubmit(e) {
    e.preventDefault();
    
    if (!validateStep(3)) {
        return;
    }
    
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Creando...';
    submitBtn.disabled = true;
    
    try {
        const formData = buildFormData();
        
        const response = await fetch('/api/payment-methods', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (!response.ok) {
            throw new Error(result.message || 'Error al crear método de pago');
        }
        
        showAlert('Método de pago creado exitosamente', 'success');
        setTimeout(() => {
            window.location.href = '{{ route("payment-methods.web.index") }}';
        }, 1500);
        
    } catch (error) {
        console.error('Error:', error);
        showAlert(error.message || 'Error al crear el método de pago', 'error');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

function buildFormData() {
    const data = {
        type: selectedType,
        consultation_fee: document.getElementById('consultation_fee').value,
        currency: document.getElementById('currency').value,
        instructions: document.getElementById('instructions').value,
        is_active: true
    };
    
    // Add type-specific configuration
    switch (selectedType) {
        case 'paypal':
            data.paypal_email = document.getElementById('paypal_email').value;
            break;
            
        case 'stripe':
            data.stripe_account_id = document.getElementById('stripe_account_id').value;
            data.webhook_secret = document.getElementById('webhook_secret').value;
            break;
            
        case 'pago_movil':
            data.receiver_phone = document.getElementById('receiver_phone').value;
            data.receiver_cedula = document.getElementById('receiver_cedula').value;
            data.receiver_bank = document.getElementById('receiver_bank').value;
            data.receiver_name = document.getElementById('receiver_name').value;
            break;
            
        case 'binance_pay':
            data.binance_id = document.getElementById('binance_id').value;
            data.supported_networks = getCheckedValues('network_');
            data.supported_currencies = getCheckedValues('crypto_');
            break;
    }
    
    return data;
}

function getCheckedValues(prefix) {
    const checkboxes = document.querySelectorAll(`input[type="checkbox"][id^="${prefix}"]:checked`);
    return Array.from(checkboxes).map(cb => cb.value);
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
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