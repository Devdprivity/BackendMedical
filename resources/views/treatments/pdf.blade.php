<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tratamiento Médico - {{ $treatment->title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #4285f4;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #4285f4;
            margin-bottom: 10px;
        }
        
        .header h1 {
            font-size: 20px;
            margin: 10px 0 5px 0;
            color: #202124;
        }
        
        .header p {
            color: #5f6368;
            margin: 0;
        }
        
        .treatment-header {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #4285f4;
        }
        
        .treatment-title {
            font-size: 18px;
            font-weight: bold;
            color: #202124;
            margin: 0 0 10px 0;
        }
        
        .treatment-meta {
            display: table;
            width: 100%;
        }
        
        .treatment-meta-item {
            display: table-cell;
            padding: 5px 10px 5px 0;
            vertical-align: top;
        }
        
        .meta-label {
            font-weight: bold;
            color: #5f6368;
            font-size: 11px;
        }
        
        .meta-value {
            color: #202124;
            font-size: 12px;
        }
        
        .priority-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .priority-high { background: #fee; color: #d93025; }
        .priority-urgent { background: #fce8e6; color: #d93025; }
        .priority-normal { background: #e8f5e8; color: #137333; }
        .priority-low { background: #e3f2fd; color: #1565c0; }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #4285f4;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #dadce0;
        }
        
        .content-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border-left: 3px solid #34a853;
        }
        
        .medications-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .medications-table th {
            background: #4285f4;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
        }
        
        .medications-table td {
            padding: 8px;
            border-bottom: 1px solid #dadce0;
            font-size: 11px;
            vertical-align: top;
        }
        
        .medications-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .medication-name {
            font-weight: bold;
            color: #202124;
        }
        
        .medication-generic {
            color: #5f6368;
            font-style: italic;
            font-size: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending { background: #fff3e0; color: #f57c00; }
        .status-dispensed { background: #e8f5e8; color: #2e7d32; }
        .status-completed { background: #e3f2fd; color: #1565c0; }
        
        .alert-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-left: 4px solid #fbbc04;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        
        .alert-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 8px;
        }
        
        .alert-content {
            color: #856404;
        }
        
        .doctor-info {
            background: #e8f0fe;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
            border: 1px solid #4285f4;
        }
        
        .doctor-name {
            font-size: 16px;
            font-weight: bold;
            color: #1a73e8;
            margin-bottom: 5px;
        }
        
        .doctor-specialty {
            color: #5f6368;
            margin-bottom: 10px;
        }
        
        .clinic-info {
            color: #202124;
            font-size: 11px;
        }
        
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 10px;
            color: #5f6368;
            border-top: 1px solid #dadce0;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .qr-info {
            text-align: center;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            border: 1px dashed #4285f4;
        }
        
        .qr-text {
            font-size: 11px;
            color: #5f6368;
            margin-bottom: 5px;
        }
        
        .qr-url {
            font-family: monospace;
            font-size: 10px;
            color: #1a73e8;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">🏥 MediCare Pro</div>
        <h1>Prescripción Médica</h1>
        <p>Sistema de Gestión Clínica Inteligente</p>
    </div>

    <!-- Treatment Header -->
    <div class="treatment-header">
        <div class="treatment-title">{{ $treatment->title }}</div>
        <div class="treatment-meta">
            <div class="treatment-meta-item">
                <div class="meta-label">PACIENTE</div>
                <div class="meta-value">{{ $treatment->patient->name }}</div>
            </div>
            <div class="treatment-meta-item">
                <div class="meta-label">TIPO</div>
                <div class="meta-value">{{ ucfirst(str_replace('_', ' ', $treatment->type)) }}</div>
            </div>
            <div class="treatment-meta-item">
                <div class="meta-label">PRIORIDAD</div>
                <div class="meta-value">
                    <span class="priority-badge priority-{{ $treatment->priority }}">
                        {{ ucfirst($treatment->priority) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="treatment-meta" style="margin-top: 10px;">
            <div class="treatment-meta-item">
                <div class="meta-label">FECHA INICIO</div>
                <div class="meta-value">{{ $treatment->start_date->format('d/m/Y') }}</div>
            </div>
            @if($treatment->end_date)
            <div class="treatment-meta-item">
                <div class="meta-label">FECHA FIN</div>
                <div class="meta-value">{{ $treatment->end_date->format('d/m/Y') }}</div>
            </div>
            @endif
            @if($treatment->duration_days)
            <div class="treatment-meta-item">
                <div class="meta-label">DURACIÓN</div>
                <div class="meta-value">{{ $treatment->duration_days }} días</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Description -->
    <div class="section">
        <div class="section-title">DESCRIPCIÓN DEL TRATAMIENTO</div>
        <div class="content-box">
            {{ $treatment->description }}
        </div>
    </div>

    <!-- Instructions -->
    <div class="section">
        <div class="section-title">INSTRUCCIONES PARA EL PACIENTE</div>
        <div class="content-box">
            {!! nl2br(e($treatment->instructions)) !!}
        </div>
    </div>

    <!-- Medications -->
    @if($treatment->treatmentMedications->count() > 0)
    <div class="section">
        <div class="section-title">MEDICAMENTOS PRESCRITOS</div>
        <table class="medications-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Medicamento</th>
                    <th style="width: 15%;">Dosis</th>
                    <th style="width: 15%;">Frecuencia</th>
                    <th style="width: 15%;">Duración</th>
                    <th style="width: 10%;">Cantidad</th>
                    <th style="width: 20%;">Instrucciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($treatment->treatmentMedications as $treatmentMed)
                <tr>
                    <td>
                        <div class="medication-name">{{ $treatmentMed->medication->commercial_name }}</div>
                        <div class="medication-generic">{{ $treatmentMed->medication->generic_name }}</div>
                    </td>
                    <td>{{ $treatmentMed->dosage }}</td>
                    <td>{{ $treatmentMed->frequency }}</td>
                    <td>{{ $treatmentMed->duration }}</td>
                    <td>{{ $treatmentMed->quantity_prescribed }}</td>
                    <td>{{ $treatmentMed->administration_instructions ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Precautions -->
    @if($treatment->precautions)
    <div class="section">
        <div class="section-title">PRECAUCIONES IMPORTANTES</div>
        <div class="alert-box">
            <div class="alert-title">⚠️ ATENCIÓN</div>
            <div class="alert-content">{{ $treatment->precautions }}</div>
        </div>
    </div>
    @endif

    <!-- Side Effects -->
    @if($treatment->side_effects_to_watch)
    <div class="section">
        <div class="section-title">EFECTOS SECUNDARIOS A VIGILAR</div>
        <div class="alert-box">
            <div class="alert-title">👁️ OBSERVAR</div>
            <div class="alert-content">{{ $treatment->side_effects_to_watch }}</div>
        </div>
    </div>
    @endif

    <!-- Additional Notes -->
    @if($treatment->notes)
    <div class="section">
        <div class="section-title">NOTAS ADICIONALES</div>
        <div class="content-box">
            {!! nl2br(e($treatment->notes)) !!}
        </div>
    </div>
    @endif

    <!-- Doctor Information -->
    <div class="doctor-info">
        <div class="doctor-name">Dr. {{ $treatment->doctor->name }}</div>
        <div class="doctor-specialty">{{ $treatment->doctor->specialty }}</div>
        @if($treatment->doctor->license_number)
        <div class="clinic-info">Registro Médico: {{ $treatment->doctor->license_number }}</div>
        @endif
        @if($treatment->clinic)
        <div class="clinic-info">{{ $treatment->clinic->name }}</div>
        @if($treatment->clinic->address)
        <div class="clinic-info">{{ $treatment->clinic->address }}</div>
        @endif
        @if($treatment->clinic->phone)
        <div class="clinic-info">Tel: {{ $treatment->clinic->phone }}</div>
        @endif
        @endif
    </div>

    <!-- QR Code Information -->
    <div class="qr-info">
        <div class="qr-text">Para ver este tratamiento en línea, escanee el código QR o visite:</div>
        <div class="qr-url">{{ $treatment->qr_url }}</div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div>
            Este documento contiene información médica confidencial | 
            Generado el {{ now()->format('d/m/Y H:i') }} | 
            Powered by MediCare Pro
        </div>
    </div>
</body>
</html> 