<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cita Confirmada - DrOrganiza</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #1f2937;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--gray-800);
            background: linear-gradient(135deg, var(--success) 0%, var(--primary) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .confirmation-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 600px;
            width: 100%;
        }

        .confirmation-header {
            background: var(--success);
            color: white;
            text-align: center;
            padding: 3rem 2rem;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
        }

        .confirmation-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .confirmation-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .confirmation-body {
            padding: 2rem;
        }

        .appointment-details {
            background: var(--gray-50);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--gray-200);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 500;
            color: var(--gray-600);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-value {
            font-weight: 600;
            color: var(--gray-900);
            text-align: right;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .important-info {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.2);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .important-info h3 {
            color: var(--warning);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .important-info ul {
            list-style: none;
            padding: 0;
        }

        .important-info li {
            padding: 0.25rem 0;
            color: var(--gray-700);
        }

        .important-info li::before {
            content: '•';
            color: var(--warning);
            margin-right: 0.5rem;
        }

        .actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            flex: 1;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            color: var(--gray-600);
            border: 2px solid var(--gray-300);
        }

        .btn-outline:hover {
            background: var(--gray-100);
            border-color: var(--gray-400);
        }

        .contact-info {
            text-align: center;
            padding: 1.5rem;
            background: var(--gray-50);
            border-top: 1px solid var(--gray-200);
        }

        .contact-info h4 {
            margin-bottom: 1rem;
            color: var(--gray-900);
        }

        .contact-details {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--gray-600);
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .confirmation-header {
                padding: 2rem 1rem;
            }

            .confirmation-title {
                font-size: 1.5rem;
            }

            .confirmation-body {
                padding: 1.5rem;
            }

            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.25rem;
            }

            .detail-value {
                text-align: left;
            }

            .actions {
                flex-direction: column;
            }

            .contact-details {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="confirmation-card">
        <div class="confirmation-header">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1 class="confirmation-title">¡Cita Confirmada!</h1>
            <p class="confirmation-subtitle">Tu cita médica ha sido reservada exitosamente</p>
        </div>

        <div class="confirmation-body">
            <div class="appointment-details">
                <div class="detail-row">
                    <span class="detail-label">
                        <i class="fas fa-calendar-alt"></i>
                        Fecha
                    </span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($appointment->date_time)->format('d/m/Y') }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">
                        <i class="fas fa-clock"></i>
                        Hora
                    </span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($appointment->date_time)->format('g:i A') }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">
                        <i class="fas fa-user-md"></i>
                        Médico
                    </span>
                    <span class="detail-value">Dr. {{ $appointment->doctor_name }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">
                        <i class="fas fa-stethoscope"></i>
                        Especialidad
                    </span>
                    <span class="detail-value">{{ $appointment->doctor_specialty ?? 'Medicina General' }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">
                        <i class="fas fa-user"></i>
                        Paciente
                    </span>
                    <span class="detail-value">{{ $appointment->patient_name }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">
                        <i class="fas fa-notes-medical"></i>
                        Motivo
                    </span>
                    <span class="detail-value">{{ $appointment->reason }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">
                        <i class="fas fa-info-circle"></i>
                        Estado
                    </span>
                    <span class="detail-value">
                        <span class="status-badge">Confirmada</span>
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">
                        <i class="fas fa-hashtag"></i>
                        Número de Cita
                    </span>
                    <span class="detail-value">#{{ str_pad($appointment->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>

            <div class="important-info">
                <h3>
                    <i class="fas fa-exclamation-triangle"></i>
                    Información Importante
                </h3>
                <ul>
                    <li>Llega 15 minutos antes de tu cita</li>
                    <li>Trae tu identificación y tarjeta de seguro médico</li>
                    <li>Si necesitas cancelar o reprogramar, hazlo con al menos 24 horas de anticipación</li>
                    <li>Prepara una lista de tus medicamentos actuales</li>
                    <li>Si tienes síntomas de COVID-19, contacta antes de venir</li>
                </ul>
            </div>

            <div class="actions">
                <button class="btn btn-primary" onclick="addToCalendar()">
                    <i class="fas fa-calendar-plus"></i>
                    Agregar al Calendario
                </button>
                <button class="btn btn-outline" onclick="window.print()">
                    <i class="fas fa-print"></i>
                    Imprimir
                </button>
            </div>
        </div>

        <div class="contact-info">
            <h4>¿Necesitas ayuda?</h4>
            <div class="contact-details">
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <span>Contacta a tu médico para cualquier consulta</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <span>{{ $appointment->patient_email }}</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        function addToCalendar() {
            const appointment = {
                title: 'Cita Médica - Dr. {{ $appointment->doctor_name }}',
                start: '{{ $appointment->date_time }}',
                description: 'Motivo: {{ $appointment->reason }}\nEspecialidad: {{ $appointment->doctor_specialty ?? "Medicina General" }}\nNúmero de cita: #{{ str_pad($appointment->id, 6, "0", STR_PAD_LEFT) }}'
            };

            // Create Google Calendar URL
            const startDate = new Date(appointment.start);
            const endDate = new Date(startDate.getTime() + (60 * 60 * 1000)); // 1 hour duration
            
            const formatDate = (date) => {
                return date.toISOString().replace(/[-:]/g, '').replace(/\.\d{3}/, '');
            };

            const googleCalendarUrl = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(appointment.title)}&dates=${formatDate(startDate)}/${formatDate(endDate)}&details=${encodeURIComponent(appointment.description)}`;

            // Open Google Calendar
            window.open(googleCalendarUrl, '_blank');
        }

        // Auto-scroll to top on load
        window.addEventListener('load', function() {
            window.scrollTo(0, 0);
        });
    </script>
</body>
</html> 