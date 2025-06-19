<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opciones de Pago - MediCare Pro</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #00539B;
            --secondary: #00AEEF;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --dark: #1A202C;
            --light: #F8FAFC;
            --gray-100: #F1F5F9;
            --gray-200: #E2E8F0;
            --gray-600: #475569;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--light), var(--gray-100));
            min-height: 100vh;
            color: var(--dark);
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .payment-header {
            text-align: center;
            margin-bottom: 3rem;
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .payment-header h1 {
            color: var(--primary);
            margin-bottom: 1rem;
            font-size: 2rem;
        }
        
        .appointment-info {
            background: var(--gray-100);
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 1rem;
        }
        
        .appointment-info h3 {
            margin-bottom: 1rem;
            color: var(--dark);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .info-item i {
            color: var(--primary);
            width: 20px;
        }
        
        .payment-methods {
            display: grid;
            gap: 1.5rem;
        }
        
        .payment-method {
            background: white;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .payment-method:hover {
            border-color: var(--primary);
            box-shadow: 0 8px 25px rgba(0, 83, 155, 0.15);
            transform: translateY(-2px);
        }
        
        .payment-method.selected {
            border-color: var(--primary);
            background: linear-gradient(135deg, rgba(0, 83, 155, 0.05), rgba(0, 174, 239, 0.05));
        }
        
        .method-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        
        .method-info {
            display: flex;
            align-items: center;
            gap: 1rem;
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
        
        .method-details h4 {
            margin-bottom: 0.5rem;
            color: var(--dark);
        }
        
        .method-fee {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary);
        }
        
        .method-description {
            color: var(--gray-600);
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
        
        .method-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .method-badge.manual {
            background: var(--warning);
            color: white;
        }
        
        .method-badge.automatic {
            background: var(--success);
            color: white;
        }
        
        .payment-form {
            margin-top: 2rem;
            text-align: center;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 83, 155, 0.3);
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }
        
        .instructions {
            background: var(--gray-100);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.875rem;
            color: var(--gray-600);
            display: none;
        }
        
        .instructions.show {
            display: block;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .method-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .method-fee {
                align-self: flex-end;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-header">
            <h1><i class="fas fa-credit-card"></i> Pagar Consulta Médica</h1>
            <p>Selecciona tu método de pago preferido para completar el pago de tu consulta</p>
            
            <div class="appointment-info">
                <h3><i class="fas fa-calendar-check"></i> Información de la Cita</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <i class="fas fa-user-md"></i>
                        <span><strong>Doctor:</strong> {{ $appointment->doctor->name ?? 'Dr. Nombre' }}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-user"></i>
                        <span><strong>Paciente:</strong> {{ $appointment->patient->name ?? 'Paciente' }}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-calendar"></i>
                        <span><strong>Fecha:</strong> {{ $appointment->date_time->format('d/m/Y') ?? 'Fecha' }}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <span><strong>Hora:</strong> {{ $appointment->date_time->format('H:i') ?? 'Hora' }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <form id="paymentForm" action="{{ route('payments.create', $appointment->id) }}" method="POST">
            @csrf
            <input type="hidden" id="selectedPaymentMethod" name="payment_method_id" value="">
            
            <div class="payment-methods">
                @foreach($paymentMethods as $method)
                <div class="payment-method" data-method-id="{{ $method->id }}" onclick="selectPaymentMethod({{ $method->id }})">
                    <div class="method-badge {{ $method->isManualPayment() ? 'manual' : 'automatic' }}">
                        {{ $method->isManualPayment() ? 'Manual' : 'Automático' }}
                    </div>
                    
                    <div class="method-header">
                        <div class="method-info">
                            <div class="method-icon {{ $method->type }}">
                                @switch($method->type)
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
                            <div class="method-details">
                                <h4>{{ $method->type_name }}</h4>
                                <div class="method-description">
                                    @switch($method->type)
                                        @case('paypal')
                                            Pago seguro con PayPal
                                            @break
                                        @case('binance_pay')
                                            Pago con criptomonedas
                                            @break
                                        @case('pago_movil')
                                            Transferencia bancaria móvil
                                            @break
                                        @case('stripe')
                                            Pago con tarjeta de crédito/débito
                                            @break
                                    @endswitch
                                </div>
                            </div>
                        </div>
                        <div class="method-fee">
                            {{ number_format($method->consultation_fee, 2) }} {{ $method->currency }}
                        </div>
                    </div>
                    
                    @if($method->instructions)
                    <div class="instructions" id="instructions-{{ $method->id }}">
                        <strong>Instrucciones:</strong><br>
                        {{ $method->getFormattedInstructions($appointment) }}
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            
            <div class="payment-form">
                <button type="submit" class="btn btn-primary" id="payButton" disabled>
                    <i class="fas fa-lock"></i>
                    Proceder al Pago
                </button>
            </div>
        </form>
    </div>
    
    <script>
        let selectedMethodId = null;
        
        function selectPaymentMethod(methodId) {
            // Remove previous selection
            document.querySelectorAll('.payment-method').forEach(method => {
                method.classList.remove('selected');
            });
            
            document.querySelectorAll('.instructions').forEach(instruction => {
                instruction.classList.remove('show');
            });
            
            // Add new selection
            const selectedMethod = document.querySelector(`[data-method-id="${methodId}"]`);
            selectedMethod.classList.add('selected');
            
            const instructions = document.getElementById(`instructions-${methodId}`);
            if (instructions) {
                instructions.classList.add('show');
            }
            
            // Update form
            selectedMethodId = methodId;
            document.getElementById('selectedPaymentMethod').value = methodId;
            document.getElementById('payButton').disabled = false;
        }
        
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            if (!selectedMethodId) {
                e.preventDefault();
                alert('Por favor selecciona un método de pago');
                return false;
            }
        });
    </script>
</body>
</html> 