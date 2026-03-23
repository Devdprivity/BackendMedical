@extends('layouts.onboarding')

@section('title', 'Paso 4: Métodos de Pago - DrOrganiza')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Paso 4: Métodos de Pago</h1>
        <p class="page-subtitle">Configura cómo recibirás los pagos de tus consultas</p>
    </div>
</div>

<!-- Progress Indicator -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <div style="display: flex; align-items: center; justify-content: center; gap: 1rem;">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--success); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">✓</div>
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--success); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">✓</div>
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--success); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">✓</div>
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">4</div>
            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--gray-300); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">5</div>
        </div>
    </div>
</div>

<!-- Form Card -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Configuración de Pagos</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('onboarding.payments.update') }}">
            @csrf
            
            <!-- Payment Methods -->
            <h4 style="margin-bottom: 1.5rem; color: var(--primary); display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-credit-card"></i>
                Métodos de Pago Aceptados
            </h4>
            
            @php
                $userPaymentMethods = old('payment_methods', $user->payment_methods ? json_decode($user->payment_methods, true) : []);
            @endphp
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; border: 1px solid var(--gray-200); border-radius: 8px;">
                    <input type="checkbox" id="payment_cash" name="payment_methods[]" value="cash" 
                           {{ in_array('cash', $userPaymentMethods) ? 'checked' : '' }}
                           style="margin: 0;">
                    <div>
                        <label for="payment_cash" style="margin: 0; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-money-bill-wave" style="color: var(--success); margin-right: 0.5rem;"></i>
                            Efectivo
                        </label>
                        <p style="margin: 0; color: var(--gray-500); font-size: 0.8rem;">Pago en efectivo presencial</p>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; border: 1px solid var(--gray-200); border-radius: 8px;">
                    <input type="checkbox" id="payment_stripe" name="payment_methods[]" value="stripe" 
                           {{ in_array('stripe', $userPaymentMethods) ? 'checked' : '' }}
                           style="margin: 0;">
                    <div>
                        <label for="payment_stripe" style="margin: 0; cursor: pointer; font-weight: 600;">
                            <i class="fab fa-stripe" style="color: var(--primary); margin-right: 0.5rem;"></i>
                            Stripe
                        </label>
                        <p style="margin: 0; color: var(--gray-500); font-size: 0.8rem;">Tarjeta de crédito/débito vía Stripe</p>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; border: 1px solid var(--gray-200); border-radius: 8px;">
                    <input type="checkbox" id="payment_binance_pay" name="payment_methods[]" value="binance_pay" 
                           {{ in_array('binance_pay', $userPaymentMethods) ? 'checked' : '' }}
                           style="margin: 0;">
                    <div>
                        <label for="payment_binance_pay" style="margin: 0; cursor: pointer; font-weight: 600;">
                            <i class="fab fa-bitcoin" style="color: var(--warning); margin-right: 0.5rem;"></i>
                            Binance Pay
                        </label>
                        <p style="margin: 0; color: var(--gray-500); font-size: 0.8rem;">Pagos con criptomonedas</p>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; border: 1px solid var(--gray-200); border-radius: 8px;">
                    <input type="checkbox" id="payment_paypal" name="payment_methods[]" value="paypal" 
                           {{ in_array('paypal', $userPaymentMethods) ? 'checked' : '' }}
                           style="margin: 0;">
                    <div>
                        <label for="payment_paypal" style="margin: 0; cursor: pointer; font-weight: 600;">
                            <i class="fab fa-paypal" style="color: var(--warning); margin-right: 0.5rem;"></i>
                            PayPal
                        </label>
                        <p style="margin: 0; color: var(--gray-500); font-size: 0.8rem;">Pagos online con PayPal</p>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; border: 1px solid var(--gray-200); border-radius: 8px;">
                    <input type="checkbox" id="payment_pago_movil" name="payment_methods[]" value="pago_movil" 
                           {{ in_array('pago_movil', $userPaymentMethods) ? 'checked' : '' }}
                           style="margin: 0;">
                    <div>
                        <label for="payment_pago_movil" style="margin: 0; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-mobile-alt" style="color: var(--accent); margin-right: 0.5rem;"></i>
                            Pago Móvil
                        </label>
                        <p style="margin: 0; color: var(--gray-500); font-size: 0.8rem;">Pago móvil interbancario</p>
                    </div>
                </div>
            </div>
            
            <!-- Additional Configuration Fields -->
            <div id="payment-config" style="margin-top: 2rem;">
                <!-- Bank Account for Transfers -->
                <div id="transfer-config" style="display: none; margin-bottom: 1.5rem;">
                    <h5 style="color: var(--gray-700); margin-bottom: 1rem;">
                        <i class="fas fa-university"></i>
                        Información de Transferencia
                    </h5>
                    <div class="form-group">
                        <label for="bank_account">Número de Cuenta Bancaria</label>
                        <input type="text" id="bank_account" name="bank_account" 
                               value="{{ old('bank_account', $user->bank_account) }}"
                               placeholder="Ej: 0102-1234-56-7890123456"
                               style="width: 100%; padding: 0.75rem; border: 1px solid var(--gray-300); border-radius: 8px;">
                    </div>
                </div>
                
                <!-- PayPal Configuration -->
                <div id="paypal-config" style="display: none; margin-bottom: 1.5rem;">
                    <h5 style="color: var(--gray-700); margin-bottom: 1rem;">
                        <i class="fab fa-paypal"></i>
                        Configuración de PayPal
                    </h5>
                    <div class="form-group">
                        <label for="paypal_email">Email de PayPal</label>
                        <input type="email" id="paypal_email" name="paypal_email" 
                               value="{{ old('paypal_email', $user->paypal_email) }}"
                               placeholder="tu-email@paypal.com"
                               style="width: 100%; padding: 0.75rem; border: 1px solid var(--gray-300); border-radius: 8px;">
                    </div>
                </div>
                
                <!-- Pago Móvil Configuration -->
                <div id="pago_movil-config" style="display: none; margin-bottom: 1.5rem;">
                    <h5 style="color: var(--gray-700); margin-bottom: 1rem;">
                        <i class="fas fa-mobile-alt"></i>
                        Configuración de Pago Móvil
                    </h5>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="pago_movil_phone">Teléfono</label>
                            <input type="text" id="pago_movil_phone" name="pago_movil_phone" 
                                   value="{{ old('pago_movil_phone') }}"
                                   placeholder="0414-1234567"
                                   style="width: 100%; padding: 0.75rem; border: 1px solid var(--gray-300); border-radius: 8px;">
                        </div>
                        <div class="form-group">
                            <label for="pago_movil_bank">Banco</label>
                            <select id="pago_movil_bank" name="pago_movil_bank" 
                                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--gray-300); border-radius: 8px;">
                                <option value="">Seleccionar banco</option>
                                <option value="0102">Banco de Venezuela</option>
                                <option value="0104">Venezolano de Crédito</option>
                                <option value="0105">Banco Mercantil</option>
                                <option value="0108">Banco Provincial</option>
                                <option value="0114">Bancaribe</option>
                                <option value="0115">Banco Exterior</option>
                                <option value="0128">Banco Caroní</option>
                                <option value="0134">Banesco</option>
                                <option value="0138">Banco Plaza</option>
                                <option value="0151">Banco Fondo Común</option>
                                <option value="0156">100% Banco</option>
                                <option value="0157">DelSur</option>
                                <option value="0163">Banco del Tesoro</option>
                                <option value="0166">Banco Agrícola</option>
                                <option value="0168">Bancrecer</option>
                                <option value="0169">Mi Banco</option>
                                <option value="0171">Banco Activo</option>
                                <option value="0172">Bancamiga</option>
                                <option value="0174">Banplus</option>
                                <option value="0175">Banco Bicentenario</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1.5rem; border-top: 1px solid var(--gray-200);">
                <a href="{{ route('onboarding.booking') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i>
                    Volver
                </a>
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" name="skip" value="1" class="btn btn-outline">
                        <i class="fas fa-forward"></i>
                        Omitir por ahora
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-arrow-right"></i>
                        Continuar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
input[type="checkbox"] {
    accent-color: var(--primary);
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all payment method checkboxes
    const paymentCheckboxes = document.querySelectorAll('input[name="payment_methods[]"]');
    
    // Add event listeners
    paymentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', togglePaymentConfig);
    });
    
    // Initial check
    togglePaymentConfig();
});

function togglePaymentConfig() {
    const transferChecked = document.getElementById('payment_transfer').checked;
    const paypalChecked = document.getElementById('payment_paypal').checked;
    const pagoMovilChecked = document.getElementById('payment_pago_movil').checked;
    
    // Show/hide transfer config
    const transferConfig = document.getElementById('transfer-config');
    if (transferConfig) {
        transferConfig.style.display = transferChecked ? 'block' : 'none';
    }
    
    // Show/hide PayPal config
    const paypalConfig = document.getElementById('paypal-config');
    if (paypalConfig) {
        paypalConfig.style.display = paypalChecked ? 'block' : 'none';
    }
    
    // Show/hide Pago Móvil config
    const pagoMovilConfig = document.getElementById('pago_movil-config');
    if (pagoMovilConfig) {
        pagoMovilConfig.style.display = pagoMovilChecked ? 'block' : 'none';
    }
}
</script>
@endsection 