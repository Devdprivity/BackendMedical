@if($paymentLink->paymentMethod->type === 'paypal')
<div class="payment-method-section" id="paypal-section">
    <div class="method-header">
        <div class="method-icon method-paypal">
            <i class="fab fa-paypal"></i>
        </div>
        <h4>PayPal</h4>
        <p class="text-muted">Pago seguro con PayPal</p>
    </div>
    
    <div class="payment-data-card">
        <h5><i class="fas fa-envelope me-2"></i>Información del pago</h5>
        
        <div class="data-grid">
            <div class="data-item">
                <span class="data-label">Email PayPal:</span>
                <span class="data-value" id="paypal-email">{{ $config['paypal_email'] ?? 'No configurado' }}</span>
                <button class="btn btn-sm btn-outline-primary copy-btn" onclick="copyToClipboard('paypal-email')">
                    <i class="fas fa-copy"></i> Copiar
                </button>
            </div>
            
            <div class="data-item">
                <span class="data-label">Monto:</span>
                <span class="data-value" id="amount-paypal">{{ $paymentLink->amount }} {{ $paymentLink->currency }}</span>
                <button class="btn btn-sm btn-outline-primary copy-btn" onclick="copyToClipboard('amount-paypal')">
                    <i class="fas fa-copy"></i> Copiar
                </button>
            </div>
            
            <div class="data-item">
                <span class="data-label">Concepto:</span>
                <span class="data-value" id="concept-paypal">{{ $paymentLink->concept }}</span>
                <button class="btn btn-sm btn-outline-primary copy-btn" onclick="copyToClipboard('concept-paypal')">
                    <i class="fas fa-copy"></i> Copiar
                </button>
            </div>
        </div>
        
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Instrucciones:</strong> 
            <ol class="mb-0 mt-2">
                <li>Abre tu app de PayPal o ve a paypal.com</li>
                <li>Selecciona "Enviar dinero"</li>
                <li>Ingresa el email del destinatario</li>
                <li>Ingresa el monto exacto</li>
                <li>Agrega el concepto en las notas</li>
                <li>Confirma el envío</li>
            </ol>
        </div>
    </div>
    
    <div class="payment-actions">
        <a href="https://www.paypal.com/myaccount/transfer/send" 
           target="_blank" 
           class="btn btn-primary btn-lg w-100 mb-3">
            <i class="fab fa-paypal me-2"></i>Abrir PayPal
        </a>
        
        <button type="button" class="btn btn-success btn-lg w-100" onclick="showPayPalConfirmation()">
            <i class="fas fa-check me-2"></i>Ya realicé el pago
        </button>
    </div>
</div>

<!-- Modal de confirmación -->
<div class="modal fade" id="paypalConfirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar pago PayPal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Has completado el pago a través de PayPal?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Importante:</strong> Solo confirma si ya enviaste el dinero. El doctor verificará el pago en su cuenta PayPal.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="confirmPayPalPayment()">
                    <i class="fas fa-check me-2"></i>Sí, ya pagué
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.method-paypal {
    background: #0070ba;
}

.payment-actions {
    margin-top: 2rem;
}

.btn-primary {
    background: linear-gradient(135deg, #0070ba, #005ea6);
    border: none;
    font-weight: 600;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #005ea6, #0070ba);
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0, 112, 186, 0.3);
}
</style>

<script>
function showPayPalConfirmation() {
    const modal = new bootstrap.Modal(document.getElementById('paypalConfirmModal'));
    modal.show();
}

function confirmPayPalPayment() {
    // Cerrar modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('paypalConfirmModal'));
    modal.hide();
    
    // Procesar confirmación automática
    processAutomaticPayment('paypal');
}

function processAutomaticPayment(method) {
    const token = '{{ $paymentLink->token }}';
    
    // Mostrar loading
    const button = document.querySelector('.btn-success');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
    button.disabled = true;
    
    fetch(`/api/payment-links/${token}/process`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        credentials: 'include',
        body: JSON.stringify({
            payment_method: method
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessMessage('Pago confirmado exitosamente. El doctor verificará tu pago.');
            
            // Redirigir después de 3 segundos
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        } else {
            throw new Error(data.message || 'Error al procesar el pago');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage('Error al confirmar el pago: ' + error.message);
        
        // Restaurar botón
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function showSuccessMessage(message) {
    const alert = document.createElement('div');
    alert.className = 'alert alert-success alert-dismissible fade show';
    alert.innerHTML = `
        <i class="fas fa-check-circle me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.payment-method-section');
    container.insertBefore(alert, container.firstChild);
}

function showErrorMessage(message) {
    const alert = document.createElement('div');
    alert.className = 'alert alert-danger alert-dismissible fade show';
    alert.innerHTML = `
        <i class="fas fa-exclamation-circle me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.payment-method-section');
    container.insertBefore(alert, container.firstChild);
}
</script>
@endif 