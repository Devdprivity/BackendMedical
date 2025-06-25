<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago - {{ $paymentLink->doctor->name }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #007bff;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .payment-container {
            max-width: 500px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .payment-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .payment-header {
            background: linear-gradient(135deg, var(--primary), #0056b3);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .doctor-avatar {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: bold;
            margin: 0 auto 1rem;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }
        
        .payment-body {
            padding: 2rem;
        }
        
        .amount-display {
            background: var(--light);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 2rem;
            border: 2px solid #e9ecef;
        }
        
        .amount-value {
            font-size: 3rem;
            font-weight: bold;
            color: var(--primary);
            margin: 0;
        }
        
        .amount-currency {
            color: #6c757d;
            font-size: 1.2rem;
            margin-top: 0.5rem;
        }
        
        .payment-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            color: #6c757d;
            font-weight: 500;
        }
        
        .info-value {
            color: #343a40;
            font-weight: 600;
        }
        
        .payment-method-card {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .payment-method-card:hover {
            border-color: var(--primary);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.1);
        }
        
        .method-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }
        
        .method-paypal { background: #0070ba; }
        .method-binance_pay { background: #f3ba2f; }
        .method-pago_movil { background: #28a745; }
        .method-stripe { background: #635bff; }
        
        .btn-pay {
            background: linear-gradient(135deg, var(--success), #1e7e34);
            border: none;
            border-radius: 50px;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.3);
            color: white;
        }
        
        .timer-display {
            background: #fff3cd;
            color: #856404;
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 1rem;
            border: 1px solid #ffeaa7;
        }
        
        .timer-icon {
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }
        
        .security-note {
            background: #d1ecf1;
            color: #0c5460;
            padding: 1rem;
            border-radius: 10px;
            font-size: 0.9rem;
            margin-top: 1rem;
            border: 1px solid #bee5eb;
        }
        
        .manual-payment-form {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 2rem;
            margin-top: 2rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        @media (max-width: 576px) {
            .payment-container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }
            
            .payment-header {
                padding: 1.5rem;
            }
            
            .payment-body {
                padding: 1.5rem;
            }
            
            .amount-value {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-card">
            <!-- Header -->
            <div class="payment-header">
                <div class="doctor-avatar">
                    {{ strtoupper(substr($paymentLink->doctor->name, 0, 1)) }}
                </div>
                <h3 class="mb-1">{{ $paymentLink->doctor->name }}</h3>
                <p class="mb-0 opacity-75">{{ $paymentLink->doctor->specialty ?? 'Médico' }}</p>
            </div>
            
            <!-- Body -->
            <div class="payment-body">
                <!-- Amount Display -->
                <div class="amount-display">
                    <div class="amount-value">${{ number_format($paymentLink->amount, 2) }}</div>
                    <div class="amount-currency">{{ $paymentLink->currency }}</div>
                </div>
                
                <!-- Payment Info -->
                <div class="payment-info">
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-file-medical me-2"></i>Concepto:
                        </span>
                        <span class="info-value">{{ $paymentLink->concept }}</span>
                    </div>
                    
                    @if($paymentLink->patient)
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-user me-2"></i>Paciente:
                        </span>
                        <span class="info-value">{{ $paymentLink->patient->name }}</span>
                    </div>
                    @endif
                    
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-clock me-2"></i>Válido hasta:
                        </span>
                        <span class="info-value" id="expiry-date">{{ $paymentLink->expires_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                
                <!-- Timer -->
                <div class="timer-display" id="timer-display">
                    <i class="fas fa-hourglass-half timer-icon"></i>
                    <span id="countdown-timer">Calculando tiempo restante...</span>
                </div>
                
                <!-- Payment Method -->
                <div class="payment-method-card">
                    <div class="d-flex align-items-center mb-3">
                        <div class="method-icon method-{{ $paymentLink->paymentMethod->type }}">
                            @switch($paymentLink->paymentMethod->type)
                                @case('paypal')
                                    <i class="fab fa-paypal"></i>
                                    @break
                                @case('binance_pay')
                                    <i class="fab fa-bitcoin"></i>
                                    @break
                                @case('pago_movil')
                                    <i class="fas fa-mobile-alt"></i>
                                    @break
                                @case('stripe')
                                    <i class="fab fa-stripe"></i>
                                    @break
                                @default
                                    <i class="fas fa-credit-card"></i>
                            @endswitch
                        </div>
                        <div class="ms-3">
                            <h5 class="mb-1">{{ $paymentLink->paymentMethod->type_name }}</h5>
                            <small class="text-muted">
                                @if($paymentLink->paymentMethod->isManualPayment())
                                    Verificación manual requerida
                                @else
                                    Procesamiento automático
                                @endif
                            </small>
                        </div>
                    </div>
                    
                    @if($paymentLink->description)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ $paymentLink->description }}
                    </div>
                    @endif
                    
                    <!-- Payment Instructions -->
                    @if($paymentLink->paymentMethod->type === 'pago_movil')
                        @include('payment-links.methods.pago-movil', ['config' => $paymentLink->getPaymentConfig()])
                    @elseif($paymentLink->paymentMethod->type === 'binance_pay')
                        @include('payment-links.methods.binance-pay', ['config' => $paymentLink->getPaymentConfig()])
                    @elseif($paymentLink->paymentMethod->type === 'paypal')
                        @include('payment-links.methods.paypal', ['config' => $paymentLink->getPaymentConfig()])
                    @elseif($paymentLink->paymentMethod->type === 'stripe')
                        @include('payment-links.methods.stripe', ['config' => $paymentLink->getPaymentConfig()])
                    @endif
                </div>
                
                <!-- Security Note -->
                <div class="security-note">
                    <i class="fas fa-shield-alt me-2"></i>
                    <strong>Enlace seguro:</strong> Este link expirará automáticamente por tu seguridad. 
                    No compartas este enlace con terceros.
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Countdown timer
        function updateCountdown() {
            const expiryDate = new Date('{{ $paymentLink->expires_at->toISOString() }}');
            const now = new Date();
            const timeDiff = expiryDate - now;
            
            if (timeDiff <= 0) {
                document.getElementById('countdown-timer').textContent = 'Este enlace ha expirado';
                document.getElementById('timer-display').className = 'timer-display alert-danger';
                // Disable payment buttons or redirect
                const payButtons = document.querySelectorAll('.btn-pay');
                payButtons.forEach(btn => {
                    btn.disabled = true;
                    btn.textContent = 'Enlace Expirado';
                });
                return;
            }
            
            const hours = Math.floor(timeDiff / (1000 * 60 * 60));
            const minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);
            
            let timeString = '';
            if (hours > 0) {
                timeString = `${hours}h ${minutes}m ${seconds}s`;
            } else if (minutes > 0) {
                timeString = `${minutes}m ${seconds}s`;
            } else {
                timeString = `${seconds}s`;
            }
            
            document.getElementById('countdown-timer').textContent = `Expira en: ${timeString}`;
            
            // Change color when less than 5 minutes
            if (timeDiff < 5 * 60 * 1000) {
                document.getElementById('timer-display').className = 'timer-display alert-warning';
            }
            
            // Change color when less than 1 minute
            if (timeDiff < 60 * 1000) {
                document.getElementById('timer-display').className = 'timer-display alert-danger';
            }
        }
        
        // Update countdown every second
        updateCountdown();
        setInterval(updateCountdown, 1000);
        
        // Show success message function
        function showSuccess(message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const container = document.querySelector('.payment-body');
            container.insertBefore(alertDiv, container.firstChild);
            
            // Auto dismiss after 5 seconds
            setTimeout(() => {
                if (alertDiv && alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
        
        // Show error message function
        function showError(message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const container = document.querySelector('.payment-body');
            container.insertBefore(alertDiv, container.firstChild);
            
            // Auto dismiss after 8 seconds
            setTimeout(() => {
                if (alertDiv && alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 8000);
        }
        
        // Confirm manual payment function
        function confirmPayment(event, paymentType) {
            event.preventDefault();
            
            const form = event.target;
            const formData = new FormData(form);
            const token = '{{ $paymentLink->token }}';
            
            // Convert FormData to JSON
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });
            
            // Find submit button
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
            
            // Send confirmation
            fetch(`/api/payment-links/${token}/confirm-manual`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'include',
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess(data.message);
                    
                    // Hide form and show success state
                    form.style.display = 'none';
                    
                    // Redirect after delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                } else {
                    throw new Error(data.message || 'Error al procesar el pago');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Error al confirmar el pago: ' + error.message);
                
                // Restore button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        }
    </script>
</body>
</html> 