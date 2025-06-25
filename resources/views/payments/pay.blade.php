<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagar Consulta - MediCare Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #00539B;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-500: #6c757d;
            --gray-700: #495057;
            --gray-900: #212529;
        }

        body {
            background: linear-gradient(135deg, var(--primary) 0%, #0066CC 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .payment-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .payment-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .payment-header {
            background: var(--primary);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .payment-header h1 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .payment-body {
            padding: 2rem;
        }

        .payment-info {
            background: var(--gray-100);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            color: var(--gray-700);
            font-weight: 500;
        }

        .info-value {
            font-weight: 600;
            color: var(--gray-900);
        }

        .amount-highlight {
            font-size: 1.5rem;
            color: var(--primary);
        }

        .method-card {
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .method-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }

        .method-icon.paypal { background: #0070ba; }
        .method-icon.pago_movil { background: #e74c3c; }
        .method-icon.binance_pay { background: #f3ba2f; color: #000; }
        .method-icon.stripe { background: #635bff; }

        .copy-btn:hover {
            background-color: var(--gray-200);
        }

        .loading {
            text-align: center;
            padding: 3rem;
        }

        .error {
            text-align: center;
            padding: 3rem;
            color: var(--danger);
        }

        @media (max-width: 768px) {
            .payment-container {
                margin: 1rem auto;
            }
            
            .payment-header {
                padding: 1.5rem;
            }
            
            .payment-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-card">
            <div class="payment-header">
                <i class="fas fa-credit-card fa-2x mb-3"></i>
                <h1>Pago de Consulta Médica</h1>
                <p class="mb-0">MediCare Pro</p>
            </div>
            
            <div class="payment-body">
                <div id="loading" class="loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3">Cargando información del pago...</p>
                </div>
                
                <div id="error" class="error" style="display: none;">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <h3>Error</h3>
                    <p id="error-message"></p>
                </div>
                
                <div id="payment-content" style="display: none;">
                    <!-- Información del pago -->
                    <div class="payment-info">
                        <div class="info-row">
                            <span class="info-label">Doctor:</span>
                            <span class="info-value" id="doctor-name"></span>
                        </div>
                        <div class="info-row" id="patient-row" style="display: none;">
                            <span class="info-label">Paciente:</span>
                            <span class="info-value" id="patient-name"></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Concepto:</span>
                            <span class="info-value" id="payment-concept"></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Monto:</span>
                            <span class="info-value amount-highlight" id="payment-amount"></span>
                        </div>
                    </div>
                    
                    <!-- Información del método de pago -->
                    <div class="method-card">
                        <div class="d-flex align-items-start">
                            <div id="method-icon" class="method-icon me-3">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 id="method-name" class="mb-2"></h5>
                                <div id="method-details">
                                    <!-- Se llenará dinámicamente -->
                                </div>
                                <div id="method-instructions" class="mt-3">
                                    <!-- Se llenará dinámicamente -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary btn-lg" id="proceed-payment">
                            <i class="fas fa-credit-card me-2"></i>
                            Proceder al Pago
                        </button>
                        
                        <button class="btn btn-success" id="whatsapp-contact" style="display: none;">
                            <i class="fab fa-whatsapp me-2"></i>
                            Contactar por WhatsApp
                        </button>
                    </div>
                    
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Este link expira el <span id="expiry-date"></span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadPaymentInfo();
        });

        async function loadPaymentInfo() {
            try {
                const token = window.location.pathname.split('/').pop();
                const response = await fetch(`/api/payment-links/${token}`);
                
                if (!response.ok) {
                    throw new Error('Link de pago no encontrado o expirado');
                }
                
                const result = await response.json();
                displayPaymentInfo(result.data);
                
            } catch (error) {
                showError(error.message);
            }
        }

        function displayPaymentInfo(data) {
            // Ocultar loading
            document.getElementById('loading').style.display = 'none';
            document.getElementById('payment-content').style.display = 'block';
            
            // Información básica
            document.getElementById('doctor-name').textContent = `${data.doctor.name}${data.doctor.specialty ? ' - ' + data.doctor.specialty : ''}`;
            document.getElementById('payment-concept').textContent = data.concept;
            document.getElementById('payment-amount').textContent = `$${data.amount} ${data.currency}`;
            
            // Paciente si existe
            if (data.patient) {
                document.getElementById('patient-name').textContent = data.patient.name;
                document.getElementById('patient-row').style.display = 'flex';
            }
            
            // Fecha de expiración
            const expiryDate = new Date(data.expires_at).toLocaleDateString('es-ES');
            document.getElementById('expiry-date').textContent = expiryDate;
            
            // Información del método de pago
            setupPaymentMethod(data.payment_method, data);
        }

        function setupPaymentMethod(method, data) {
            const methodIcon = document.getElementById('method-icon');
            const methodName = document.getElementById('method-name');
            const methodDetails = document.getElementById('method-details');
            const methodInstructions = document.getElementById('method-instructions');
            const proceedBtn = document.getElementById('proceed-payment');
            
            // Configurar icono y nombre
            methodIcon.className = `method-icon ${method.type} me-3`;
            methodName.textContent = method.type_name;
            
            // Configurar detalles específicos
            switch (method.type) {
                case 'paypal':
                    methodIcon.innerHTML = '<i class="fab fa-paypal"></i>';
                    methodDetails.innerHTML = `
                        <p class="mb-1"><strong>Email PayPal:</strong> ${method.config.email}</p>
                    `;
                    proceedBtn.onclick = () => window.open(getPayPalUrl(method.config.email, data), '_blank');
                    break;
                    
                case 'pago_movil':
                    methodIcon.innerHTML = '<i class="fas fa-mobile-alt"></i>';
                    methodDetails.innerHTML = `
                        <p class="mb-1"><strong>Teléfono:</strong> ${method.config.phone}</p>
                        <p class="mb-1"><strong>Banco:</strong> ${method.config.bank}</p>
                        <p class="mb-1"><strong>Titular:</strong> ${method.config.name}</p>
                        <p class="mb-1"><strong>CI:</strong> ${method.config.ci}</p>
                    `;
                    proceedBtn.innerHTML = '<i class="fas fa-copy me-2"></i>Copiar Datos';
                    proceedBtn.onclick = () => copyPagoMovilData(method.config, data);
                    break;
                    
                case 'binance_pay':
                    methodIcon.innerHTML = '<i class="fab fa-bitcoin"></i>';
                    methodDetails.innerHTML = `
                        <p class="mb-1"><strong>Binance ID:</strong> ${method.config.binance_id}</p>
                    `;
                    proceedBtn.innerHTML = '<i class="fas fa-copy me-2"></i>Copiar ID';
                    proceedBtn.onclick = () => copyToClipboard(method.config.binance_id);
                    break;
                    
                default:
                    methodDetails.innerHTML = '<p>Información del método de pago</p>';
            }
            
            // Instrucciones
            if (method.instructions) {
                methodInstructions.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        ${method.instructions}
                    </div>
                `;
            }
        }

        function getPayPalUrl(email, data) {
            const params = new URLSearchParams({
                cmd: '_xclick',
                business: email,
                item_name: data.concept,
                amount: data.amount,
                currency_code: data.currency
            });
            return `https://www.paypal.com/cgi-bin/webscr?${params}`;
        }

        function copyPagoMovilData(config, data) {
            const text = `Datos para Pago Móvil:
Teléfono: ${config.phone}
Banco: ${config.bank}
Titular: ${config.name}
CI: ${config.ci}
Monto: $${data.amount} ${data.currency}
Concepto: ${data.concept}`;
            
            copyToClipboard(text);
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                showAlert('Información copiada al portapapeles', 'success');
            }).catch(() => {
                showAlert('Error al copiar', 'error');
            });
        }

        function showError(message) {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('error').style.display = 'block';
            document.getElementById('error-message').textContent = message;
        }

        function showAlert(message, type) {
            const alertClass = type === 'error' ? 'alert-danger' : 'alert-success';
            const alert = document.createElement('div');
            alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
            alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alert);
            
            setTimeout(() => {
                alert.remove();
            }, 5000);
        }
    </script>
</body>
</html>
