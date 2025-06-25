@if($paymentLink->paymentMethod->type === 'pago_movil')
<div class="payment-method-section" id="pago-movil-section">
    <div class="method-header">
        <div class="method-icon method-pago_movil">
            <i class="fas fa-mobile-alt"></i>
        </div>
        <h4>Pago Móvil</h4>
        <p class="text-muted">Transfiere desde tu banco móvil</p>
    </div>
    
    <div class="payment-data-card">
        <h5><i class="fas fa-info-circle me-2"></i>Datos para la transferencia</h5>
        
        <div class="data-grid">
            <div class="data-item">
                <span class="data-label">Teléfono:</span>
                <span class="data-value" id="receiver-phone">{{ $config['receiver_phone'] ?? 'No configurado' }}</span>
                <button class="btn btn-sm btn-outline-primary copy-btn" onclick="copyToClipboard('receiver-phone')">
                    <i class="fas fa-copy"></i> Copiar
                </button>
            </div>
            
            <div class="data-item">
                <span class="data-label">Banco:</span>
                <span class="data-value" id="receiver-bank">{{ $config['receiver_bank'] ?? 'No configurado' }}</span>
                <button class="btn btn-sm btn-outline-primary copy-btn" onclick="copyToClipboard('receiver-bank')">
                    <i class="fas fa-copy"></i> Copiar
                </button>
            </div>
            
            <div class="data-item">
                <span class="data-label">Titular:</span>
                <span class="data-value" id="receiver-name">{{ $config['receiver_name'] ?? 'No configurado' }}</span>
                <button class="btn btn-sm btn-outline-primary copy-btn" onclick="copyToClipboard('receiver-name')">
                    <i class="fas fa-copy"></i> Copiar
                </button>
            </div>
            
            <div class="data-item">
                <span class="data-label">Cédula:</span>
                <span class="data-value" id="receiver-cedula">{{ $config['receiver_cedula'] ?? 'No configurado' }}</span>
                <button class="btn btn-sm btn-outline-primary copy-btn" onclick="copyToClipboard('receiver-cedula')">
                    <i class="fas fa-copy"></i> Copiar
                </button>
            </div>
        </div>
        
        <div class="alert alert-info mt-3">
            <i class="fas fa-clock me-2"></i>
            <strong>Importante:</strong> Tienes 5 minutos desde que confirmes el pago para completar la transferencia.
        </div>
    </div>
    
    <div class="payment-form-card">
        <h5><i class="fas fa-receipt me-2"></i>Confirmar tu pago</h5>
        <p class="text-muted">Completa la transferencia y luego llena estos datos:</p>
        
        <form id="pago-movil-form" onsubmit="confirmPayment(event, 'pago_movil')">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="sender_phone" class="form-label">Tu teléfono <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="sender_phone" name="sender_phone" 
                           placeholder="04121234567" maxlength="11" required>
                    <div class="form-text">11 dígitos sin espacios</div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="sender_cedula" class="form-label">Tu cédula <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="sender_cedula" name="sender_cedula" 
                           placeholder="V-12345678" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="sender_bank" class="form-label">Tu banco <span class="text-danger">*</span></label>
                <select class="form-select" id="sender_bank" name="sender_bank" required>
                    <option value="">Selecciona tu banco</option>
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
                    <option value="0177">Banco de la Fuerza Armada Nacional Bolivariana</option>
                </select>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="reference" class="form-label">Referencia <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="reference" name="reference" 
                           placeholder="123456" maxlength="6" required>
                    <div class="form-text">6 dígitos de la referencia</div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="transaction_date" class="form-label">Fecha <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="transaction_date" name="transaction_date" 
                           max="{{ date('Y-m-d') }}" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="transaction_time" class="form-label">Hora <span class="text-danger">*</span></label>
                <input type="time" class="form-control" id="transaction_time" name="transaction_time" required>
            </div>
            
            <button type="submit" class="btn btn-success btn-lg w-100">
                <i class="fas fa-check me-2"></i>Confirmar Pago
            </button>
        </form>
    </div>
</div>

<style>
.payment-method-section {
    margin-bottom: 2rem;
}

.method-header {
    text-align: center;
    margin-bottom: 2rem;
}

.payment-data-card, .payment-form-card {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid #e9ecef;
}

.data-grid {
    display: grid;
    gap: 1rem;
}

.data-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    background: white;
    border-radius: 10px;
    border: 1px solid #e9ecef;
}

.data-label {
    font-weight: 600;
    color: #495057;
    min-width: 80px;
}

.data-value {
    font-family: 'Courier New', monospace;
    font-weight: 600;
    color: #28a745;
    flex: 1;
    margin: 0 1rem;
}

.copy-btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .data-item {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
    
    .data-label, .data-value {
        margin: 0;
    }
}
</style>

<script>
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    const text = element.textContent;
    
    navigator.clipboard.writeText(text).then(() => {
        // Mostrar feedback visual
        const button = element.nextElementSibling;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> Copiado';
        button.classList.remove('btn-outline-primary');
        button.classList.add('btn-success');
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-primary');
        }, 2000);
    }).catch(() => {
        // Fallback para navegadores que no soportan clipboard API
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        alert('Datos copiados: ' + text);
    });
}

// Establecer fecha actual por defecto
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('transaction_date');
    if (dateInput) {
        dateInput.value = new Date().toISOString().split('T')[0];
    }
    
    const timeInput = document.getElementById('transaction_time');
    if (timeInput) {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        timeInput.value = `${hours}:${minutes}`;
    }
});

// Validación de teléfono
document.getElementById('sender_phone')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 11) value = value.slice(0, 11);
    e.target.value = value;
});

// Validación de referencia
document.getElementById('reference')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 6) value = value.slice(0, 6);
    e.target.value = value;
});
</script>
@endif 