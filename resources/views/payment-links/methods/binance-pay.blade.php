@if($paymentLink->paymentMethod->type === 'binance_pay')
<div class="payment-method-section" id="binance-pay-section">
    <div class="method-header">
        <div class="method-icon method-binance_pay">
            <i class="fab fa-bitcoin"></i>
        </div>
        <h4>Binance Pay</h4>
        <p class="text-muted">Paga con criptomonedas</p>
    </div>
    
    <div class="payment-data-card">
        <h5><i class="fas fa-wallet me-2"></i>Datos para la transferencia</h5>
        
        <div class="data-grid">
            <div class="data-item">
                <span class="data-label">Binance ID:</span>
                <span class="data-value" id="binance-id">{{ $config['binance_id'] ?? 'No configurado' }}</span>
                <button class="btn btn-sm btn-outline-primary copy-btn" onclick="copyToClipboard('binance-id')">
                    <i class="fas fa-copy"></i> Copiar
                </button>
            </div>
            
            <div class="data-item">
                <span class="data-label">Monto:</span>
                <span class="data-value" id="amount-crypto">{{ $paymentLink->amount }} {{ $paymentLink->currency }}</span>
                <button class="btn btn-sm btn-outline-primary copy-btn" onclick="copyToClipboard('amount-crypto')">
                    <i class="fas fa-copy"></i> Copiar
                </button>
            </div>
        </div>
        
        <div class="alert alert-warning mt-3">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Importante:</strong> Envía exactamente la cantidad mostrada. Las transferencias con montos diferentes serán rechazadas.
        </div>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Redes soportadas:</strong> BEP20 (BSC), TRC20 (TRON), ERC20 (Ethereum)
        </div>
    </div>
    
    <div class="payment-form-card">
        <h5><i class="fas fa-receipt me-2"></i>Confirmar tu pago</h5>
        <p class="text-muted">Completa la transferencia y luego proporciona los datos:</p>
        
        <form id="binance-pay-form" onsubmit="confirmPayment(event, 'binance_pay')">
            <div class="mb-3">
                <label for="transaction_hash" class="form-label">Hash de transacción <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="transaction_hash" name="transaction_hash" 
                       placeholder="0x..." required>
                <div class="form-text">Hash completo de la transacción en blockchain</div>
            </div>
            
            <div class="mb-3">
                <label for="network" class="form-label">Red utilizada <span class="text-danger">*</span></label>
                <select class="form-select" id="network" name="network" required>
                    <option value="">Selecciona la red</option>
                    <option value="BEP20">BEP20 (Binance Smart Chain)</option>
                    <option value="TRC20">TRC20 (TRON)</option>
                    <option value="ERC20">ERC20 (Ethereum)</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="sender_wallet" class="form-label">Tu wallet <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="sender_wallet" name="sender_wallet" 
                       placeholder="0x..." required>
                <div class="form-text">Dirección de tu wallet desde donde enviaste</div>
            </div>
            
            <button type="submit" class="btn btn-warning btn-lg w-100">
                <i class="fab fa-bitcoin me-2"></i>Confirmar Pago Crypto
            </button>
        </form>
    </div>
</div>

<style>
.method-binance_pay {
    background: linear-gradient(135deg, #f3ba2f, #f0b90b);
}

.btn-warning {
    background: linear-gradient(135deg, #f3ba2f, #f0b90b);
    border: none;
    color: #000;
    font-weight: 600;
}

.btn-warning:hover {
    background: linear-gradient(135deg, #f0b90b, #f3ba2f);
    color: #000;
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(243, 186, 47, 0.3);
}
</style>

<script>
// Validación de hash de transacción
document.getElementById('transaction_hash')?.addEventListener('input', function(e) {
    let value = e.target.value.trim();
    // Remover espacios y caracteres no válidos para hash
    value = value.replace(/[^a-fA-F0-9x]/g, '');
    e.target.value = value;
});

// Validación de wallet address
document.getElementById('sender_wallet')?.addEventListener('input', function(e) {
    let value = e.target.value.trim();
    // Remover espacios y caracteres no válidos para address
    value = value.replace(/[^a-fA-F0-9x]/g, '');
    e.target.value = value;
});
</script>
@endif 