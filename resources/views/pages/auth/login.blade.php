<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Iniciar Sesión - MediCare Pro</title>
    
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
            --surface-container: #f1f3f4;
            --on-surface: #202124;
            --on-surface-variant: #5f6368;
            --outline: #dadce0;
            --outline-variant: #e8eaed;
            --shadow: rgba(60, 64, 67, 0.3);
            --shadow-light: rgba(60, 64, 67, 0.15);
            --gradient-primary: linear-gradient(135deg, #4285f4 0%, #34a853 50%, #1a73e8 100%);
            --gradient-surface: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--gradient-surface);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(66, 133, 244, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(52, 168, 83, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(234, 67, 53, 0.05) 0%, transparent 50%);
            z-index: 0;
        }
        
        .auth-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 
                0 8px 32px var(--shadow-light),
                0 2px 8px var(--shadow-light);
            padding: 48px;
            width: 100%;
            max-width: 480px;
            position: relative;
            z-index: 1;
            border: 1px solid var(--outline-variant);
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 32px;
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
            box-shadow: 0 4px 16px rgba(66, 133, 244, 0.3);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        
        .auth-title {
            text-align: center;
            color: var(--on-surface);
            font-size: 2.25rem;
            font-weight: 800;
            margin-bottom: 8px;
            background: linear-gradient(135deg, var(--on-surface) 0%, var(--on-surface-variant) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .auth-subtitle {
            text-align: center;
            color: var(--on-surface-variant);
            font-size: 1rem;
            margin-bottom: 40px;
            font-weight: 500;
        }
        
        .auth-tabs {
            display: flex;
            margin-bottom: 32px;
            background: var(--surface-container);
            border-radius: 16px;
            padding: 6px;
            box-shadow: inset 0 1px 3px var(--shadow-light);
        }
        
        .auth-tab {
            flex: 1;
            padding: 14px 20px;
            text-align: center;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 600;
            color: var(--on-surface-variant);
            font-size: 14px;
            position: relative;
        }
        
        .auth-tab.active {
            background: white;
            color: var(--primary);
            box-shadow: 
                0 2px 8px var(--shadow-light),
                0 1px 3px var(--shadow-light);
            transform: translateY(-1px);
        }

        .auth-tab:hover:not(.active) {
            background: rgba(255, 255, 255, 0.7);
            color: var(--on-surface);
        }
        
        .auth-form {
            display: none;
            animation: fadeIn 0.5s ease-in-out;
        }
        
        .auth-form.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            color: var(--on-surface);
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-control {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid var(--outline);
            border-radius: 16px;
            font-size: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: var(--surface);
            color: var(--on-surface);
            font-family: inherit;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(66, 133, 244, 0.1);
            background: white;
            transform: translateY(-1px);
        }

        .form-control:hover:not(:focus) {
            border-color: var(--primary);
        }
        
        .form-control-icon {
            position: relative;
        }
        
        .form-control-icon input {
            padding-left: 56px;
        }
        
        .form-control-icon .icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--on-surface-variant);
            font-size: 16px;
            transition: color 0.3s ease;
        }

        .form-control-icon input:focus + .icon,
        .form-control-icon:hover .icon {
            color: var(--primary);
        }
        
        .btn-primary {
            width: 100%;
            padding: 16px 24px;
            background: var(--gradient-primary);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(66, 133, 244, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(66, 133, 244, 0.4);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .btn-google {
            width: 100%;
            padding: 16px 24px;
            background: white;
            color: var(--on-surface);
            border: 2px solid var(--outline);
            border-radius: 16px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 1px 3px var(--shadow-light);
        }
        
        .btn-google:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px var(--shadow-light);
            background: var(--surface-variant);
        }
        
        .btn-google svg {
            width: 20px;
            height: 20px;
        }
        
        .divider {
            text-align: center;
            margin: 32px 0;
            position: relative;
            color: var(--on-surface-variant);
            font-size: 14px;
            font-weight: 500;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--outline);
            z-index: 1;
        }
        
        .divider span {
            background: var(--surface);
            padding: 0 20px;
            position: relative;
            z-index: 2;
        }
        
        .loading {
            display: none;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .loading.show {
            display: flex;
        }
        
        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert-error {
            background: rgba(234, 67, 53, 0.1);
            color: var(--accent);
            border: 1px solid rgba(234, 67, 53, 0.2);
        }
        
        .alert-success {
            background: rgba(52, 168, 83, 0.1);
            color: var(--secondary);
            border: 1px solid rgba(52, 168, 83, 0.2);
        }
        
        .alert-info {
            background: rgba(66, 133, 244, 0.1);
            color: var(--primary);
            border: 1px solid rgba(66, 133, 244, 0.2);
        }
        
        .back-link {
            text-align: center;
            margin-top: 32px;
        }
        
        .back-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            padding: 8px 16px;
            border-radius: 8px;
        }
        
        .back-link a:hover {
            background: rgba(66, 133, 244, 0.1);
            transform: translateX(-2px);
        }
        
        .trial-badge {
            background: var(--gradient-primary);
            color: white;
            padding: 12px 20px;
            border-radius: 24px;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(66, 133, 244, 0.3);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }
        
        .password-strength {
            margin-top: 8px;
            font-size: 12px;
        }
        
        .strength-bar {
            height: 4px;
            background: var(--outline);
            border-radius: 2px;
            margin: 6px 0;
            overflow: hidden;
        }
        
        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .strength-weak { background: var(--accent); width: 25%; }
        .strength-fair { background: var(--warning); width: 50%; }
        .strength-good { background: var(--secondary); width: 75%; }
        .strength-strong { background: var(--secondary); width: 100%; }

        .feature-highlight {
            background: var(--surface-variant);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
            border: 1px solid var(--outline-variant);
        }

        .feature-highlight h4 {
            color: var(--on-surface);
            margin-bottom: 12px;
            font-size: 16px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .feature-list {
            list-style: none;
            padding: 0;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 0;
            color: var(--on-surface-variant);
            font-size: 14px;
        }

        .feature-list i {
            color: var(--secondary);
            font-size: 12px;
        }
        
        @media (max-width: 480px) {
            .auth-container {
                padding: 32px 24px;
                margin: 10px;
            }
            
            .auth-title {
                font-size: 1.75rem;
            }

            .logo {
                width: 64px;
                height: 64px;
                font-size: 24px;
            }

            .form-control {
                padding: 14px 16px;
            }

            .form-control-icon input {
                padding-left: 48px;
            }

            .form-control-icon .icon {
                left: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="logo-container">
            <div class="logo">
                <i class="fas fa-heartbeat"></i>
            </div>
            <h1 class="auth-title">MediCare Pro</h1>
            <p class="auth-subtitle">Sistema de Gestión Clínica Inteligente</p>
        </div>
        
        <!-- Auth Tabs -->
        <div class="auth-tabs">
            <div class="auth-tab active" onclick="switchTab('login')">Iniciar Sesión</div>
            <div class="auth-tab" onclick="switchTab('register')">Registrarse</div>
        </div>
        
        <!-- Alert Container -->
        <div id="alertContainer"></div>
        
        <!-- Login Form -->
        <div id="loginForm" class="auth-form active">
            <!-- Google Login -->
            <button class="btn-google" onclick="loginWithGoogle()">
                <svg width="20" height="20" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Continuar con Google
            </button>
            
            <div class="divider">
                <span>o continúa con email</span>
            </div>
            
            <form id="emailLoginForm">
                <div class="form-group">
                    <label class="form-label" for="loginEmail">Correo Electrónico</label>
                    <div class="form-control-icon">
                        <input type="email" id="loginEmail" class="form-control" placeholder="tu@email.com" required>
                        <i class="fas fa-envelope icon"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="loginPassword">Contraseña</label>
                    <div class="form-control-icon">
                        <input type="password" id="loginPassword" class="form-control" placeholder="••••••••" required>
                        <i class="fas fa-lock icon"></i>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary" id="loginBtn">
                    <span id="loginText">Iniciar Sesión</span>
                    <div class="loading" id="loginLoading">
                        <div class="spinner"></div>
                        <span>Iniciando sesión...</span>
                    </div>
                </button>
            </form>
        </div>
        
        <!-- Register Form -->
        <div id="registerForm" class="auth-form">
            <div class="trial-badge">
                <i class="fas fa-gift"></i>
                ¡Prueba gratuita de 7 días!
            </div>

            <div class="feature-highlight">
                <h4>
                    <i class="fas fa-star"></i>
                    Lo que obtienes gratis:
                </h4>
                <ul class="feature-list">
                    <li><i class="fas fa-check"></i> 1 Doctor incluido</li>
                    <li><i class="fas fa-check"></i> 50 Pacientes</li>
                    <li><i class="fas fa-check"></i> 100 Citas por mes</li>
                    <li><i class="fas fa-check"></i> Soporte por email</li>
                    <li><i class="fas fa-check"></i> Acceso móvil completo</li>
                </ul>
            </div>
            
            <!-- Google Register -->
            <button class="btn-google" onclick="loginWithGoogle()">
                <svg width="20" height="20" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Registrarse con Google
            </button>
            
            <div class="divider">
                <span>o regístrate con email</span>
            </div>
            
            <form id="emailRegisterForm">
                <div class="form-group">
                    <label class="form-label" for="registerName">Nombre Completo</label>
                    <div class="form-control-icon">
                        <input type="text" id="registerName" class="form-control" placeholder="Dr. Juan Pérez" required>
                        <i class="fas fa-user icon"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="registerEmail">Correo Electrónico</label>
                    <div class="form-control-icon">
                        <input type="email" id="registerEmail" class="form-control" placeholder="tu@email.com" required>
                        <i class="fas fa-envelope icon"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="registerPassword">Contraseña</label>
                    <div class="form-control-icon">
                        <input type="password" id="registerPassword" class="form-control" placeholder="Mínimo 8 caracteres" required minlength="8">
                        <i class="fas fa-lock icon"></i>
                    </div>
                    <div class="password-strength" id="passwordStrength" style="display: none;">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <span id="strengthText"></span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="registerPasswordConfirm">Confirmar Contraseña</label>
                    <div class="form-control-icon">
                        <input type="password" id="registerPasswordConfirm" class="form-control" placeholder="Repite tu contraseña" required>
                        <i class="fas fa-lock icon"></i>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary" id="registerBtn">
                    <span id="registerText">Crear Cuenta Gratuita</span>
                    <div class="loading" id="registerLoading">
                        <div class="spinner"></div>
                        <span>Creando cuenta...</span>
                    </div>
                </button>
            </form>
        </div>
        
        <div class="back-link">
            <a href="/">
                <i class="fas fa-arrow-left"></i>
                Volver al inicio
            </a>
        </div>
    </div>

    <script>
        // Tab switching
        function switchTab(tab) {
            const tabs = document.querySelectorAll('.auth-tab');
            const forms = document.querySelectorAll('.auth-form');
            
            tabs.forEach(t => t.classList.remove('active'));
            forms.forEach(f => f.classList.remove('active'));
            
            document.querySelector(`[onclick="switchTab('${tab}')"]`).classList.add('active');
            document.getElementById(tab + 'Form').classList.add('active');
            
            // Clear alerts when switching tabs
            document.getElementById('alertContainer').innerHTML = '';
        }
        
        // Google OAuth
        function loginWithGoogle() {
            // Check if we're in the right environment
            if (window.location.hostname === 'localhost' && window.location.href.includes('laravel.cloud')) {
                showAlert('error', 'Error de configuración: Contacta al administrador.');
                return;
            }
            
            try {
                window.location.href = '/auth/google';
            } catch (error) {
                console.error('Error redirecting to Google OAuth:', error);
                showAlert('error', 'Error al conectar con Google. Por favor, intenta de nuevo.');
            }
        }
        
        // Email Login
        document.getElementById('emailLoginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            const loginBtn = document.getElementById('loginBtn');
            const loginText = document.getElementById('loginText');
            const loginLoading = document.getElementById('loginLoading');
            
            setLoadingState(loginBtn, loginText, loginLoading, true);
            clearAlerts();
            
            try {
                const response = await fetch('{{ route("login") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ email, password })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    showAlert('¡Inicio de sesión exitoso! Redirigiendo...', 'success');
                    setTimeout(() => window.location.href = '/dashboard', 1500);
                } else {
                    showAlert(data.message || 'Error al iniciar sesión', 'error');
                }
                
            } catch (error) {
                console.error('Error:', error);
                showAlert('Error de conexión. Por favor, intenta de nuevo.', 'error');
            } finally {
                setLoadingState(loginBtn, loginText, loginLoading, false);
            }
        });
        
        // Email Registration
        document.getElementById('emailRegisterForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const name = document.getElementById('registerName').value;
            const email = document.getElementById('registerEmail').value;
            const password = document.getElementById('registerPassword').value;
            const passwordConfirm = document.getElementById('registerPasswordConfirm').value;
            
            if (password !== passwordConfirm) {
                showAlert('Las contraseñas no coinciden', 'error');
                return;
            }
            
            const registerBtn = document.getElementById('registerBtn');
            const registerText = document.getElementById('registerText');
            const registerLoading = document.getElementById('registerLoading');
            
            setLoadingState(registerBtn, registerText, registerLoading, true);
            clearAlerts();
            
            try {
                const response = await fetch('/auth/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        name,
                        email,
                        password,
                        password_confirmation: passwordConfirm
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    showAlert(`¡Cuenta creada exitosamente! Tu prueba gratuita de ${data.trial_days || 7} días ha comenzado. Redirigiendo...`, 'success');
                    setTimeout(() => window.location.href = '/dashboard', 2000);
                } else {
                    const message = data.errors ? 
                        Object.values(data.errors).flat().join(', ') : 
                        (data.message || 'Error al crear la cuenta');
                    showAlert(message, 'error');
                }
                
            } catch (error) {
                console.error('Error:', error);
                showAlert('Error de conexión. Por favor, intenta de nuevo.', 'error');
            } finally {
                setLoadingState(registerBtn, registerText, registerLoading, false);
            }
        });
        
        // Password strength checker
        document.getElementById('registerPassword').addEventListener('input', function(e) {
            const password = e.target.value;
            const strengthDiv = document.getElementById('passwordStrength');
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthText');
            
            if (password.length === 0) {
                strengthDiv.style.display = 'none';
                return;
            }
            
            strengthDiv.style.display = 'block';
            
            let strength = 0;
            let text = '';
            let className = '';
            
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            switch (strength) {
                case 0:
                case 1:
                    text = 'Muy débil';
                    className = 'strength-weak';
                    break;
                case 2:
                    text = 'Débil';
                    className = 'strength-weak';
                    break;
                case 3:
                    text = 'Regular';
                    className = 'strength-fair';
                    break;
                case 4:
                    text = 'Buena';
                    className = 'strength-good';
                    break;
                case 5:
                    text = 'Muy fuerte';
                    className = 'strength-strong';
                    break;
            }
            
            strengthFill.className = 'strength-fill ' + className;
            strengthText.textContent = text;
        });
        
        // Utility functions
        function setLoadingState(btn, text, loading, isLoading) {
            btn.disabled = isLoading;
            text.style.display = isLoading ? 'none' : 'inline';
            loading.classList.toggle('show', isLoading);
        }
        
        function clearAlerts() {
            document.getElementById('alertContainer').innerHTML = '';
        }
        
        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            const alertClass = `alert-${type}`;
            const iconMap = {
                error: 'fas fa-exclamation-circle',
                success: 'fas fa-check-circle',
                info: 'fas fa-info-circle'
            };
            
            alertContainer.innerHTML = `
                <div class="alert ${alertClass}">
                    <i class="${iconMap[type]}"></i> ${message}
                </div>
            `;
            
            if (type === 'success') {
                setTimeout(() => alertContainer.innerHTML = '', 3000);
            }
        }
        
        // Check for URL parameters (success/error messages)
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const success = urlParams.get('success');
            const error = urlParams.get('error');
            
            if (success) {
                showAlert(decodeURIComponent(success), 'success');
            }
            if (error) {
                showAlert(decodeURIComponent(error), 'error');
            }
        });

        // Add floating particles effect
        function createParticle() {
            const particle = document.createElement('div');
            particle.style.cssText = `
                position: absolute;
                width: 4px;
                height: 4px;
                background: rgba(66, 133, 244, 0.3);
                border-radius: 50%;
                pointer-events: none;
                animation: floatUp 3s linear infinite;
                left: ${Math.random() * 100}vw;
                top: 100vh;
                z-index: 0;
            `;
            
            document.body.appendChild(particle);
            
            setTimeout(() => {
                particle.remove();
            }, 3000);
        }

        // Add CSS for particles
        const style = document.createElement('style');
        style.textContent = `
            @keyframes floatUp {
                to {
                    transform: translateY(-100vh) rotate(360deg);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Create particles periodically
        setInterval(createParticle, 2000);
    </script>
</body>
</html> 