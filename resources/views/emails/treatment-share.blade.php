<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tratamiento Médico - {{ $treatment->title }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        
        .email-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #4285f4 0%, #34a853 50%, #1a73e8 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
            font-weight: 700;
        }
        
        .header p {
            margin: 0;
            opacity: 0.9;
            font-size: 16px;
        }
        
        .content {
            padding: 30px;
        }
        
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #202124;
        }
        
        .treatment-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            border-left: 4px solid #4285f4;
        }
        
        .treatment-title {
            font-size: 20px;
            font-weight: 700;
            color: #202124;
            margin-bottom: 15px;
        }
        
        .treatment-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .meta-item {
            background: white;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #dadce0;
        }
        
        .meta-label {
            font-size: 12px;
            font-weight: 600;
            color: #5f6368;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        
        .meta-value {
            font-size: 14px;
            font-weight: 500;
            color: #202124;
        }
        
        .priority-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .priority-high { background: #fee; color: #d93025; }
        .priority-urgent { background: #fce8e6; color: #d93025; }
        .priority-normal { background: #e8f5e8; color: #137333; }
        .priority-low { background: #e3f2fd; color: #1565c0; }
        
        .description {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #dadce0;
            margin: 20px 0;
        }
        
        .description h3 {
            margin: 0 0 12px 0;
            color: #4285f4;
            font-size: 16px;
        }
        
        .doctor-info {
            background: #e8f0fe;
            padding: 20px;
            border-radius: 12px;
            margin: 25px 0;
            border: 1px solid #4285f4;
        }
        
        .doctor-name {
            font-size: 18px;
            font-weight: 700;
            color: #1a73e8;
            margin-bottom: 5px;
        }
        
        .doctor-specialty {
            color: #5f6368;
            margin-bottom: 10px;
        }
        
        .clinic-info {
            color: #202124;
            font-size: 14px;
            margin: 5px 0;
        }
        
        .message-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-left: 4px solid #fbbc04;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }
        
        .message-box h3 {
            margin: 0 0 10px 0;
            color: #856404;
            font-size: 16px;
        }
        
        .message-content {
            color: #856404;
            font-style: italic;
        }
        
        .cta-section {
            text-align: center;
            margin: 30px 0;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 12px;
        }
        
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #4285f4 0%, #1a73e8 100%);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 16px;
            margin: 10px;
            transition: all 0.3s ease;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(66, 133, 244, 0.3);
        }
        
        .qr-info {
            text-align: center;
            padding: 20px;
            background: #f1f3f4;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .qr-text {
            font-size: 14px;
            color: #5f6368;
            margin-bottom: 10px;
        }
        
        .qr-url {
            font-family: monospace;
            font-size: 12px;
            color: #1a73e8;
            word-break: break-all;
            background: white;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #dadce0;
        }
        
        .footer {
            background: #f1f3f4;
            padding: 25px;
            text-align: center;
            border-top: 1px solid #dadce0;
        }
        
        .footer p {
            margin: 5px 0;
            font-size: 14px;
            color: #5f6368;
        }
        
        .footer .powered-by {
            font-weight: 600;
            color: #4285f4;
        }
        
        .important-notice {
            background: #fce8e6;
            border: 1px solid #ea4335;
            border-left: 4px solid #ea4335;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .important-notice h4 {
            margin: 0 0 8px 0;
            color: #d93025;
            font-size: 14px;
        }
        
        .important-notice p {
            margin: 0;
            color: #d93025;
            font-size: 13px;
        }
        
        @media (max-width: 600px) {
            .treatment-meta {
                grid-template-columns: 1fr;
            }
            
            .content {
                padding: 20px;
            }
            
            .header {
                padding: 20px;
            }
            
            .cta-button {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>🏥 DrOrganiza</h1>
            <p>Tratamiento Médico Compartido</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Estimado/a paciente,
            </div>
            
            <p>Se ha compartido con usted un tratamiento médico. A continuación encontrará todos los detalles importantes:</p>
            
            <!-- Treatment Card -->
            <div class="treatment-card">
                <div class="treatment-title">{{ $treatment->title }}</div>
                
                <div class="treatment-meta">
                    <div class="meta-item">
                        <div class="meta-label">Paciente</div>
                        <div class="meta-value">{{ $treatment->patient->name }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Tipo</div>
                        <div class="meta-value">{{ ucfirst(str_replace('_', ' ', $treatment->type)) }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Prioridad</div>
                        <div class="meta-value">
                            <span class="priority-badge priority-{{ $treatment->priority }}">
                                {{ ucfirst($treatment->priority) }}
                            </span>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Fecha Inicio</div>
                        <div class="meta-value">{{ $treatment->start_date->format('d/m/Y') }}</div>
                    </div>
                    @if($treatment->end_date)
                    <div class="meta-item">
                        <div class="meta-label">Fecha Fin</div>
                        <div class="meta-value">{{ $treatment->end_date->format('d/m/Y') }}</div>
                    </div>
                    @endif
                    @if($treatment->duration_days)
                    <div class="meta-item">
                        <div class="meta-label">Duración</div>
                        <div class="meta-value">{{ $treatment->duration_days }} días</div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Description -->
            <div class="description">
                <h3>📋 Descripción del Tratamiento</h3>
                <p>{{ $treatment->description }}</p>
            </div>
            
            <!-- Instructions -->
            <div class="description">
                <h3>📝 Instrucciones Importantes</h3>
                <p>{!! nl2br(e($treatment->instructions)) !!}</p>
            </div>
            
            <!-- Custom Message -->
            @if($message)
            <div class="message-box">
                <h3>💬 Mensaje del Doctor</h3>
                <div class="message-content">{{ $message }}</div>
            </div>
            @endif
            
            <!-- Doctor Information -->
            <div class="doctor-info">
                <div class="doctor-name">Dr. {{ $treatment->doctor->name }}</div>
                <div class="doctor-specialty">{{ $treatment->doctor->specialty }}</div>
                @if($treatment->clinic)
                <div class="clinic-info">🏥 {{ $treatment->clinic->name }}</div>
                @if($treatment->clinic->phone)
                <div class="clinic-info">📞 {{ $treatment->clinic->phone }}</div>
                @endif
                @endif
            </div>
            
            <!-- Call to Action -->
            <div class="cta-section">
                <h3 style="margin-bottom: 15px; color: #202124;">Ver Tratamiento Completo</h3>
                <p style="margin-bottom: 20px; color: #5f6368;">Para acceder a todos los detalles, medicamentos y seguimiento:</p>
                <a href="{{ $qr_url }}" class="cta-button">
                    🔗 Ver Tratamiento Online
                </a>
            </div>
            
            <!-- QR Information -->
            <div class="qr-info">
                <div class="qr-text">
                    También puede acceder escaneando el código QR adjunto o visitando:
                </div>
                <div class="qr-url">{{ $qr_url }}</div>
            </div>
            
            <!-- Important Notice -->
            <div class="important-notice">
                <h4>🔒 Información Confidencial</h4>
                <p>Este email contiene información médica confidencial. Si no es el destinatario previsto, por favor elimine este mensaje y notifique al remitente.</p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>DrOrganiza</strong> - Sistema de Gestión Clínica Inteligente</p>
            <p>Este email fue generado automáticamente el {{ now()->format('d/m/Y H:i') }}</p>
            <p class="powered-by">Powered by DrOrganiza</p>
        </div>
    </div>
</body>
</html> 