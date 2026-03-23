<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reservar Cita - DrOrganiza</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #667eea;
            --primary-dark: #5a6fd8;
            --secondary: #764ba2;
            --accent: #f093fb;
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
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .booking-header {
            text-align: center;
            margin-bottom: 3rem;
            color: white;
        }

        .booking-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .booking-header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .booking-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 800px;
            margin: 0 auto;
        }

        .provider-info {
            background: var(--gray-50);
            padding: 2rem;
            border-bottom: 1px solid var(--gray-200);
            text-align: center;
        }

        .provider-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0 auto 1rem;
        }

        .provider-name {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--gray-900);
        }

        .provider-specialty {
            color: var(--gray-600);
            margin-bottom: 1rem;
        }

        .provider-bio {
            color: var(--gray-700);
            line-height: 1.6;
        }

        .booking-form {
            padding: 2rem;
        }

        .step {
            display: none;
        }

        .step.active {
            display: block;
        }

        .step-header {
            margin-bottom: 2rem;
        }

        .step-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        .step-subtitle {
            color: var(--gray-600);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: var(--gray-200);
            color: var(--gray-700);
        }

        .btn-secondary:hover {
            background: var(--gray-300);
        }

        .btn-outline {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-outline:hover {
            background: var(--primary);
            color: white;
        }

        .step-navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--gray-200);
        }

        .progress-bar {
            height: 4px;
            background: var(--gray-200);
            border-radius: 2px;
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            transition: width 0.3s ease;
        }

        .location-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .location-card {
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .location-card:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .location-card.selected {
            border-color: var(--primary);
            background: rgba(102, 126, 234, 0.05);
        }

        .doctor-grid {
            display: grid;
            gap: 1rem;
        }

        .doctor-card {
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .doctor-card:hover {
            border-color: var(--primary);
        }

        .doctor-card.selected {
            border-color: var(--primary);
            background: rgba(102, 126, 234, 0.05);
        }

        .doctor-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--secondary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0.5rem;
            margin: 1rem 0;
        }

        .calendar-day {
            aspect-ratio: 1;
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .calendar-day:hover {
            background: var(--gray-100);
        }

        .calendar-day.available {
            background: var(--success);
            color: white;
            border-color: var(--success);
        }

        .calendar-day.selected {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .time-slots {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .time-slot {
            padding: 0.75rem;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .time-slot:hover {
            border-color: var(--primary);
        }

        .time-slot.selected {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .loading {
            text-align: center;
            padding: 2rem;
            color: var(--gray-500);
        }

        .error {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .booking-header h1 {
                font-size: 2rem;
            }

            .booking-form {
                padding: 1.5rem;
            }

            .location-grid {
                grid-template-columns: 1fr;
            }

            .doctor-card {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="booking-header">
            <h1>Reservar Cita Médica</h1>
            <p>Selecciona tu médico y horario preferido</p>
        </div>

        <div class="booking-card">
            <!-- Provider Info -->
            <div class="provider-info" id="providerInfo">
                <div class="loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Cargando información...</p>
                </div>
            </div>

            <!-- Booking Form -->
            <div class="booking-form">
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill" style="width: 25%"></div>
                </div>

                <!-- Step 1: Location Selection (for clinics) -->
                <div class="step active" id="step1">
                    <div class="step-header">
                        <h2 class="step-title">Seleccionar Ubicación</h2>
                        <p class="step-subtitle">Elige la sucursal más conveniente para ti</p>
                    </div>
                    <div id="locationsContainer">
                        <div class="loading">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>Cargando ubicaciones...</p>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Doctor Selection -->
                <div class="step" id="step2">
                    <div class="step-header">
                        <h2 class="step-title">Seleccionar Médico</h2>
                        <p class="step-subtitle">Elige el especialista que necesitas</p>
                    </div>
                    <div id="doctorsContainer">
                        <div class="loading">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>Cargando médicos...</p>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Date & Time Selection -->
                <div class="step" id="step3">
                    <div class="step-header">
                        <h2 class="step-title">Seleccionar Fecha y Hora</h2>
                        <p class="step-subtitle">Elige el día y horario que mejor te convenga</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Fecha de la Cita</label>
                        <input type="date" id="appointmentDate" class="form-control" min="">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Horarios Disponibles</label>
                        <div id="timeSlotsContainer">
                            <p style="color: var(--gray-500); text-align: center; padding: 2rem;">
                                Selecciona una fecha para ver los horarios disponibles
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Patient Information -->
                <div class="step" id="step4">
                    <div class="step-header">
                        <h2 class="step-title">Información del Paciente</h2>
                        <p class="step-subtitle">Completa tus datos para confirmar la cita</p>
                    </div>
                    
                    <form id="patientForm">
                        <div class="form-group">
                            <label for="patientName" class="form-label">Nombre Completo *</label>
                            <input type="text" id="patientName" name="patient_name" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="patientPhone" class="form-label">Teléfono *</label>
                            <input type="tel" id="patientPhone" name="patient_phone" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="patientEmail" class="form-label">Email *</label>
                            <input type="email" id="patientEmail" name="patient_email" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="patientAge" class="form-label">Edad</label>
                            <input type="number" id="patientAge" name="patient_age" class="form-control" min="1" max="120">
                        </div>
                        
                        <div class="form-group">
                            <label for="patientGender" class="form-label">Género</label>
                            <select id="patientGender" name="patient_gender" class="form-control">
                                <option value="">Seleccionar...</option>
                                <option value="male">Masculino</option>
                                <option value="female">Femenino</option>
                                <option value="other">Otro</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="reason" class="form-label">Motivo de la Consulta *</label>
                            <textarea id="reason" name="reason" class="form-control" rows="3" required placeholder="Describe brevemente el motivo de tu consulta"></textarea>
                        </div>
                    </form>
                </div>

                <!-- Navigation -->
                <div class="step-navigation">
                    <button type="button" class="btn btn-secondary" id="prevBtn" onclick="previousStep()" style="display: none;">
                        <i class="fas fa-arrow-left"></i>
                        Anterior
                    </button>
                    
                    <div></div>
                    
                    <button type="button" class="btn btn-primary" id="nextBtn" onclick="nextStep()">
                        Siguiente
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentStep = 1;
        let totalSteps = 4;
        let providerData = null;
        let selectedLocation = null;
        let selectedDoctor = null;
        let selectedDate = null;
        let selectedTime = null;
        let bookingSlug = '{{ $slug }}';

        document.addEventListener('DOMContentLoaded', function() {
            loadProviderInfo();
            setMinDate();
        });

        async function loadProviderInfo() {
            try {
                const response = await fetch(`/api/booking/${bookingSlug}/info`);
                const data = await response.json();
                
                if (data.success) {
                    providerData = data.data;
                    displayProviderInfo(providerData);
                    
                    // Determine total steps based on provider type
                    if (providerData.type === 'doctor') {
                        // Individual doctor: skip location step
                        totalSteps = 4;
                        document.getElementById('step1').style.display = 'none';
                        currentStep = 2;
                        showStep(2);
                        loadDoctors();
                    } else {
                        // Clinic: show all steps
                        totalSteps = 4;
                        showStep(1);
                        loadLocations();
                    }
                } else {
                    showError('Error al cargar la información del proveedor');
                }
            } catch (error) {
                console.error('Error loading provider info:', error);
                showError('Error al cargar la información del proveedor');
            }
        }

        function displayProviderInfo(provider) {
            const container = document.getElementById('providerInfo');
            container.innerHTML = `
                <div class="provider-avatar">${provider.name.charAt(0).toUpperCase()}</div>
                <div class="provider-name">${provider.name}</div>
                <div class="provider-specialty">${provider.specialty || 'Médico General'}</div>
                <div class="provider-bio">${provider.bio || 'Profesional de la salud comprometido con brindar la mejor atención médica.'}</div>
                ${provider.consultation_fee ? `<p style="margin-top: 1rem; font-weight: 600; color: var(--primary);">Costo de consulta: $${provider.consultation_fee}</p>` : ''}
            `;
        }

        async function loadLocations() {
            try {
                const response = await fetch(`/api/booking/${bookingSlug}/locations`);
                const data = await response.json();
                
                if (data.success) {
                    displayLocations(data.data);
                } else {
                    document.getElementById('locationsContainer').innerHTML = '<p class="error">Error al cargar las sucursales</p>';
                }
            } catch (error) {
                console.error('Error loading locations:', error);
                document.getElementById('locationsContainer').innerHTML = '<p class="error">Error al cargar las sucursales</p>';
            }
        }

        function displayLocations(locations) {
            const container = document.getElementById('locationsContainer');
            
            if (locations.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: var(--gray-500);">No hay ubicaciones disponibles</p>';
                return;
            }
            
            const locationsHtml = locations.map(location => `
                <div class="location-card" onclick="selectLocation(${location.id})">
                    <h3>${location.name}</h3>
                    <p style="color: var(--gray-600); margin: 0.5rem 0;">${location.address}</p>
                    ${location.phone ? `<p style="color: var(--gray-500);"><i class="fas fa-phone"></i> ${location.phone}</p>` : ''}
                </div>
            `).join('');
            
            container.innerHTML = `<div class="location-grid">${locationsHtml}</div>`;
        }

        function selectLocation(locationId) {
            selectedLocation = locationId;
            
            // Update UI
            document.querySelectorAll('.location-card').forEach(card => {
                card.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
            
            // Enable next button
            document.getElementById('nextBtn').disabled = false;
        }

        async function loadDoctors() {
            try {
                let url = `/api/booking/${bookingSlug}/doctors`;
                const params = new URLSearchParams();
                
                if (selectedLocation) {
                    params.append('location_id', selectedLocation);
                }
                
                if (params.toString()) {
                    url += '?' + params.toString();
                }
                
                const response = await fetch(url);
                const data = await response.json();
                
                if (data.success) {
                    displayDoctors(data.data);
                } else {
                    document.getElementById('doctorsContainer').innerHTML = '<p class="error">Error al cargar los médicos</p>';
                }
            } catch (error) {
                console.error('Error loading doctors:', error);
                document.getElementById('doctorsContainer').innerHTML = '<p class="error">Error al cargar los médicos</p>';
            }
        }

        function displayDoctors(doctors) {
            const container = document.getElementById('doctorsContainer');
            
            if (doctors.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: var(--gray-500);">No hay médicos disponibles</p>';
                return;
            }
            
            const doctorsHtml = doctors.map(doctor => `
                <div class="doctor-card" onclick="selectDoctor(${doctor.id})">
                    <div class="doctor-avatar">${doctor.name.charAt(0).toUpperCase()}</div>
                    <div style="flex: 1;">
                        <h3>${doctor.name}</h3>
                        <p style="color: var(--gray-600);">${doctor.specialty}</p>
                        ${doctor.bio ? `<p style="color: var(--gray-500); font-size: 0.9rem; margin-top: 0.5rem;">${doctor.bio}</p>` : ''}
                        <p style="color: var(--primary); font-weight: 600; margin-top: 0.5rem;">$${parseFloat(doctor.consultation_fee).toFixed(2)}</p>
                    </div>
                </div>
            `).join('');
            
            container.innerHTML = `<div class="doctor-grid">${doctorsHtml}</div>`;
        }

        function selectDoctor(doctorId) {
            selectedDoctor = doctorId;
            
            // Update UI
            document.querySelectorAll('.doctor-card').forEach(card => {
                card.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
            
            // Enable next button
            document.getElementById('nextBtn').disabled = false;
        }

        function setMinDate() {
            const today = new Date();
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);
            
            const minDate = tomorrow.toISOString().split('T')[0];
            document.getElementById('appointmentDate').min = minDate;
            
            document.getElementById('appointmentDate').addEventListener('change', function() {
                selectedDate = this.value;
                loadTimeSlots();
            });
        }

        async function loadTimeSlots() {
            if (!selectedDoctor || !selectedDate) return;
            
            try {
                const response = await fetch(`/api/booking/${bookingSlug}/availability?doctor_id=${selectedDoctor}&date=${selectedDate}`);
                const data = await response.json();
                
                if (data.success) {
                    displayTimeSlots(data.data);
                } else {
                    document.getElementById('timeSlotsContainer').innerHTML = '<p class="error">Error al cargar los horarios</p>';
                }
            } catch (error) {
                console.error('Error loading availability:', error);
                document.getElementById('timeSlotsContainer').innerHTML = '<p class="error">Error al cargar los horarios</p>';
            }
        }

        function displayTimeSlots(slots) {
            const container = document.getElementById('timeSlotsContainer');
            
            if (slots.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: var(--gray-500);">No hay horarios disponibles para esta fecha</p>';
                return;
            }
            
            const slotsHtml = slots.map(slot => `
                <div class="time-slot" onclick="selectTime('${slot.time}')">
                    ${slot.time}
                </div>
            `).join('');
            
            container.innerHTML = `<div class="time-slots">${slotsHtml}</div>`;
        }

        function selectTime(time) {
            selectedTime = time;
            
            // Update UI
            document.querySelectorAll('.time-slot').forEach(slot => {
                slot.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
            
            // Enable next button
            document.getElementById('nextBtn').disabled = false;
        }

        function showStep(step) {
            // Hide all steps
            document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
            
            // Show current step
            document.getElementById(`step${step}`).classList.add('active');
            
            // Update progress
            let progressStep = step;
            if (providerData && providerData.type === 'doctor' && step > 1) {
                progressStep = step - 1;
            }
            const totalVisibleSteps = providerData && providerData.type === 'doctor' ? 3 : 4;
            const progress = (progressStep / totalVisibleSteps) * 100;
            document.getElementById('progressFill').style.width = `${progress}%`;
            
            // Update navigation buttons
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            
            const isFirstVisibleStep = (providerData && providerData.type === 'doctor' && step === 2) || step === 1;
            prevBtn.style.display = isFirstVisibleStep ? 'none' : 'inline-flex';
            
            if (step === totalSteps) {
                nextBtn.innerHTML = '<i class="fas fa-check"></i> Reservar Cita';
                nextBtn.onclick = submitBooking;
            } else {
                nextBtn.innerHTML = 'Siguiente <i class="fas fa-arrow-right"></i>';
                nextBtn.onclick = nextStep;
            }
            
            // Reset next button state
            nextBtn.disabled = false;
            
            // Disable next button for certain steps until selection is made
            if (step === 1 && !selectedLocation) {
                nextBtn.disabled = true;
            } else if (step === 2 && !selectedDoctor) {
                nextBtn.disabled = true;
            } else if (step === 3 && (!selectedDate || !selectedTime)) {
                nextBtn.disabled = true;
            }
        }

        function nextStep() {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
                
                // Load data for next step
                if (currentStep === 2 && providerData.type === 'clinic') {
                    loadDoctors();
                }
            }
        }

        function previousStep() {
            if (currentStep > 1) {
                if (providerData && providerData.type === 'doctor' && currentStep === 2) {
                    return;
                }
                
                currentStep--;
                showStep(currentStep);
            }
        }

        function validateForm() {
            const requiredFields = ['patientName', 'patientPhone', 'patientEmail', 'reason'];
            let isValid = true;
            
            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (!field.value.trim()) {
                    field.style.borderColor = 'var(--danger)';
                    isValid = false;
                } else {
                    field.style.borderColor = 'var(--gray-200)';
                }
            });
            
            if (!selectedDoctor || !selectedDate || !selectedTime) {
                showError('Por favor completa todos los pasos anteriores');
                return false;
            }
            
            if (!isValid) {
                showError('Por favor completa todos los campos requeridos');
            }
            
            return isValid;
        }

        async function submitBooking() {
            if (!validateForm()) return;
            
            const submitButton = document.getElementById('nextBtn');
            const originalText = submitButton.innerHTML;
            
            try {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
                
                const formData = {
                    doctor_id: selectedDoctor,
                    appointment_date: selectedDate,
                    appointment_time: selectedTime,
                    patient_name: document.getElementById('patientName').value,
                    patient_phone: document.getElementById('patientPhone').value,
                    patient_email: document.getElementById('patientEmail').value,
                    reason: document.getElementById('reason').value,
                    patient_age: document.getElementById('patientAge').value || null,
                    patient_gender: document.getElementById('patientGender').value || null
                };
                
                const response = await fetch(`/api/booking/${bookingSlug}/reserve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.location.href = data.data.confirmation_url;
                } else {
                    showError(data.message || 'Error al procesar la reserva');
                }
            } catch (error) {
                console.error('Error submitting booking:', error);
                showError('Error al procesar la reserva. Por favor, inténtalo de nuevo.');
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        }

        function showError(message) {
            alert(message);
        }
    </script>
</body>
</html> 