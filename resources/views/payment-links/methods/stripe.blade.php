@if($paymentLink->paymentMethod->type === 'stripe')
<div class="payment-method-section" id="stripe-section">
    <div class="method-header">
        <div class="method-icon method-stripe">
            <i class="fab fa-stripe"></i>
        </div>
        <h4>Stripe</h4>
        <p class="text-muted">Pago seguro con tarjeta</p>
    </div>
    
    <div class="payment-data-card">
        <h5><i class="fas fa-credit-card me-2"></i>Pago con tarjeta</h5>
        
        <div class="alert alert-info">
            <i class="fas fa-shield-alt me-2"></i>
            <strong>Pago seguro:</strong> Tus datos de tarjeta están protegidos con encriptación SSL de nivel bancario.
        </div>
        
        <div class="payment-summary">
            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                <span class="fw-bold">Total a pagar:</span>
                <span class="h4 mb-0 text-primary">{{ $paymentLink->amount }} {{ $paymentLink->currency }}</span>
            </div>
        </div>
    </div>
    
    <div class="payment-form-card">
        <div id="stripe-payment-element">
            <!-- Stripe Elements se insertará aquí -->
        </div>
        
        <button id="stripe-submit-btn" class="btn btn-primary btn-lg w-100 mt-3">
            <i class="fas fa-lock me-2"></i>Pagar con Stripe
        </button>
        
        <div id="stripe-error-message" class="alert alert-danger mt-3" style="display: none;"></div>
        <div id="stripe-success-message" class="alert alert-success mt-3" style="display: none;"></div>
    </div>
</div>

<style>
.method-stripe {
    background: #635bff;
}

.payment-summary {
    margin: 1.5rem 0;
}

.btn-primary {
    background: linear-gradient(135deg, #635bff, #5a52ff);
    border: none;
    font-weight: 600;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a52ff, #635bff);
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(99, 91, 255, 0.3);
}

.btn-primary:disabled {
    background: #6c757d;
    transform: none;
    box-shadow: none;
}

/* Estilos para Stripe Elements */
.StripeElement {
    background-color: white;
    padding: 12px 16px;
    border-radius: 8px;
    border: 1px solid #e6e6e6;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: border-color 0.3s ease;
}

.StripeElement--focus {
    border-color: #635bff;
    box-shadow: 0 1px 3px rgba(99, 91, 255, 0.1);
}

.StripeElement--invalid {
    border-color: #dc3545;
}
</style>

<script src="https://js.stripe.com/v3/"></script>
<script>
// Inicializar Stripe solo si es el método seleccionado
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('stripe-section')) {
        initializeStripe();
    }
});

function initializeStripe() {
    // Nota: En producción, la clave pública debe venir del backend
    // Por ahora usamos una clave de prueba
    const stripe = Stripe('pk_test_...'); // Esto debe ser configurado desde el backend
    
    const elements = stripe.elements();
    const paymentElement = elements.create('payment');
    paymentElement.mount('#stripe-payment-element');
    
    const submitButton = document.getElementById('stripe-submit-btn');
    const errorMessage = document.getElementById('stripe-error-message');
    const successMessage = document.getElementById('stripe-success-message');
    
    submitButton.addEventListener('click', async (event) => {
        event.preventDefault();
        
        // Deshabilitar botón y mostrar loading
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
        
        // Ocultar mensajes previos
        errorMessage.style.display = 'none';
        successMessage.style.display = 'none';
        
        try {
            // Crear PaymentIntent en el backend
            const response = await fetch(`/api/payment-links/{{ $paymentLink->token }}/process`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'include',
                body: JSON.stringify({
                    payment_method: 'stripe'
                })
            });
            
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Error al procesar el pago');
            }
            
            // Confirmar el pago con Stripe
            const {error} = await stripe.confirmPayment({
                elements,
                clientSecret: data.client_secret,
                confirmParams: {
                    return_url: `${window.location.origin}/pay/{{ $paymentLink->token }}?success=1`,
                }
            });
            
            if (error) {
                throw new Error(error.message);
            }
            
        } catch (error) {
            console.error('Error:', error);
            errorMessage.textContent = error.message;
            errorMessage.style.display = 'block';
            
            // Restaurar botón
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fas fa-lock me-2"></i>Pagar con Stripe';
        }
    });
}

// Manejar respuesta de éxito de Stripe
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('success') === '1') {
    document.getElementById('stripe-success-message').innerHTML = 
        '<i class="fas fa-check-circle me-2"></i>¡Pago completado exitosamente!';
    document.getElementById('stripe-success-message').style.display = 'block';
    
    // Ocultar formulario de pago
    document.getElementById('stripe-payment-element').style.display = 'none';
    document.getElementById('stripe-submit-btn').style.display = 'none';
    
    // Redirigir después de 3 segundos
    setTimeout(() => {
        window.location.href = window.location.pathname; // Remover parámetros de URL
    }, 3000);
}
</script>
@endif 