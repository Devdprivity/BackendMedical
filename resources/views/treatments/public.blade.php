<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tratamiento Médico - {{ $treatment->title }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #4285f4;
            --primary-dark: #1a73e8;
            --secondary: #34a853;
            --accent: #ea4335;
            --warning: #fbbc04;
            --surface: #ffffff;
            --surface-variant: #f8f9fa;
            --on-surface: #202124;
            --on-surface-variant: #5f6368;
            --outline: #dadce0;
            --shadow: rgba(60, 64, 67, 0.3);
            --gradient-primary: linear-gradient(135deg, #4285f4 0%, #34a853 50%, #1a73e8 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(145deg, #f8f9fa 0%, #e8f0fe 100%);
            min-height: 100vh;
            color: var(--on-surface);
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px 0;
        }

        .logo {
            width: 80px;
            height: 80px;
            background: var(--gradient-primary);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 32px;
            box-shadow: 0 8px 32px rgba(66, 133, 244, 0.3);
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 10px;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header p {
            color: var(--on-surface-variant);
            font-size: 1.1rem;
            font-weight: 500;
        }

        .treatment-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(60, 64, 67, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
            border: 1px solid var(--outline);
        }

        .treatment-header {
            background: var(--gradient-primary);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .treatment-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .treatment-type {
            background: rgba(255, 255, 255, 0.2);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 15px;
        }

        .priority-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .priority-high { background: #fee; color: #d93025; }
        .priority-urgent { background: #fce8e6; color: #d93025; }
        .priority-normal { background: #e8f5e8; color: #137333; }
        .priority-low { background: #e3f2fd; color: #1565c0; }

        .treatment-content {
            padding: 30px;
        }

        .section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--on-surface);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--primary);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            background: var(--surface-variant);
            padding: 20px;
            border-radius: 16px;
            border: 1px solid var(--outline);
        }

        .info-label {
            font-size: 14px;
            font-weight: 600;
            color: var(--on-surface-variant);
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 500;
            color: var(--on-surface);
        }

        .description-box {
            background: var(--surface-variant);
            padding: 25px;
            border-radius: 16px;
            border-left: 4px solid var(--primary);
            margin-bottom: 20px;
        }

        .medications-list {
            background: var(--surface-variant);
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--outline);
        }

        .medication-item {
            padding: 20px;
            border-bottom: 1px solid var(--outline);
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 15px;
            align-items: start;
        }

        .medication-item:last-child {
            border-bottom: none;
        }

        .medication-info h4 {
            font-weight: 700;
            color: var(--on-surface);
            margin-bottom: 8px;
        }

        .medication-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            font-size: 14px;
        }

        .medication-detail {
            background: white;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid var(--outline);
        }

        .medication-detail strong {
            color: var(--primary);
            font-weight: 600;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending { background: #fff3e0; color: #f57c00; }
        .status-dispensed { background: #e8f5e8; color: #2e7d32; }
        .status-completed { background: #e3f2fd; color: #1565c0; }

        .alert-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-left: 4px solid var(--warning);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .alert-box h4 {
            color: #856404;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .alert-box p {
            color: #856404;
            margin: 0;
        }

        .doctor-info {
            background: white;
            border-radius: 16px;
            padding: 25px;
            border: 1px solid var(--outline);
            margin-top: 30px;
        }

        .doctor-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .doctor-avatar {
            width: 60px;
            height: 60px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: 700;
        }

        .doctor-details h3 {
            font-weight: 700;
            color: var(--on-surface);
            margin-bottom: 5px;
        }

        .doctor-details p {
            color: var(--on-surface-variant);
            font-size: 14px;
        }

        .footer {
            text-align: center;
            padding: 40px 20px;
            color: var(--on-surface-variant);
            font-size: 14px;
        }

        .footer p {
            margin-bottom: 10px;
        }

        .footer .powered-by {
            font-weight: 600;
            color: var(--primary);
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .treatment-content {
                padding: 20px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .medication-item {
                grid-template-columns: 1fr;
            }

            .medication-details {
                grid-template-columns: 1fr;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .treatment-card {
            animation: fadeIn 0.6s ease-out;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <i class="fas fa-heartbeat"></i>
            </div>
            <h1>MediCare Pro</h1>
            <p>Tratamiento Médico Digital</p>
        </div>

        <div class="treatment-card">
            <div class="treatment-header">
                <h2 class="treatment-title">{{ $treatment->title }}</h2>
                <div class="treatment-type">
                    {{ ucfirst(str_replace('_', ' ', $treatment->type)) }}
                </div>
                <span class="priority-badge priority-{{ $treatment->priority }}">
                    {{ ucfirst($treatment->priority) }}
                </span>
            </div>

            <div class="treatment-content">
                <!-- Información básica -->
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Paciente</div>
                        <div class="info-value">{{ $treatment->patient->name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Fecha de inicio</div>
                        <div class="info-value">{{ $treatment->start_date->format('d/m/Y') }}</div>
                    </div>
                    @if($treatment->end_date)
                    <div class="info-item">
                        <div class="info-label">Fecha de finalización</div>
                        <div class="info-value">{{ $treatment->end_date->format('d/m/Y') }}</div>
                    </div>
                    @endif
                    @if($treatment->duration_days)
                    <div class="info-item">
                        <div class="info-label">Duración</div>
                        <div class="info-value">{{ $treatment->duration_days }} días</div>
                    </div>
                    @endif
                </div>

                <!-- Descripción -->
                <div class="section">
                    <h3 class="section-title">
                        <i class="fas fa-file-medical"></i>
                        Descripción del Tratamiento
                    </h3>
                    <div class="description-box">
                        {{ $treatment->description }}
                    </div>
                </div>

                <!-- Instrucciones -->
                <div class="section">
                    <h3 class="section-title">
                        <i class="fas fa-list-check"></i>
                        Instrucciones
                    </h3>
                    <div class="description-box">
                        {!! nl2br(e($treatment->instructions)) !!}
                    </div>
                </div>

                <!-- Medicamentos -->
                @if($treatment->treatmentMedications->count() > 0)
                <div class="section">
                    <h3 class="section-title">
                        <i class="fas fa-pills"></i>
                        Medicamentos Prescritos
                    </h3>
                    <div class="medications-list">
                        @foreach($treatment->treatmentMedications as $treatmentMed)
                        <div class="medication-item">
                            <div class="medication-info">
                                <h4>{{ $treatmentMed->medication->commercial_name }}</h4>
                                <p style="color: var(--on-surface-variant); margin-bottom: 10px;">
                                    {{ $treatmentMed->medication->generic_name }}
                                </p>
                                <div class="medication-details">
                                    <div class="medication-detail">
                                        <strong>Dosis:</strong> {{ $treatmentMed->dosage }}
                                    </div>
                                    <div class="medication-detail">
                                        <strong>Frecuencia:</strong> {{ $treatmentMed->frequency }}
                                    </div>
                                    <div class="medication-detail">
                                        <strong>Duración:</strong> {{ $treatmentMed->duration }}
                                    </div>
                                    <div class="medication-detail">
                                        <strong>Cantidad:</strong> {{ $treatmentMed->quantity_prescribed }}
                                    </div>
                                </div>
                                @if($treatmentMed->administration_instructions)
                                <p style="margin-top: 10px; font-style: italic; color: var(--on-surface-variant);">
                                    <strong>Instrucciones:</strong> {{ $treatmentMed->administration_instructions }}
                                </p>
                                @endif
                            </div>
                            <span class="status-badge status-{{ $treatmentMed->status }}">
                                {{ ucfirst($treatmentMed->status) }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Precauciones -->
                @if($treatment->precautions)
                <div class="section">
                    <div class="alert-box">
                        <h4><i class="fas fa-exclamation-triangle"></i> Precauciones Importantes</h4>
                        <p>{{ $treatment->precautions }}</p>
                    </div>
                </div>
                @endif

                <!-- Efectos secundarios -->
                @if($treatment->side_effects_to_watch)
                <div class="section">
                    <div class="alert-box">
                        <h4><i class="fas fa-eye"></i> Efectos Secundarios a Vigilar</h4>
                        <p>{{ $treatment->side_effects_to_watch }}</p>
                    </div>
                </div>
                @endif

                <!-- Notas adicionales -->
                @if($treatment->notes)
                <div class="section">
                    <h3 class="section-title">
                        <i class="fas fa-sticky-note"></i>
                        Notas Adicionales
                    </h3>
                    <div class="description-box">
                        {!! nl2br(e($treatment->notes)) !!}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Información del médico -->
        <div class="doctor-info">
            <div class="doctor-header">
                <div class="doctor-avatar">
                    {{ strtoupper(substr($treatment->doctor->name, 0, 1)) }}
                </div>
                <div class="doctor-details">
                    <h3>Dr. {{ $treatment->doctor->name }}</h3>
                    <p>{{ $treatment->doctor->specialty }}</p>
                    @if($treatment->clinic)
                    <p><i class="fas fa-hospital"></i> {{ $treatment->clinic->name }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Este documento contiene información médica confidencial.</p>
            <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
            <p class="powered-by">Powered by MediCare Pro</p>
        </div>
    </div>
</body>
</html> 