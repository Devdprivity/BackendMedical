<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sistema Médico - Backend API</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: 'Inter', sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                overflow-x: hidden;
                position: relative;
            }
            
            /* Animated background particles */
            .particles {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
                z-index: 1;
            }
            
            .particle {
                position: absolute;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 50%;
                animation: float 20s infinite linear;
            }
            
            .particle:nth-child(1) { width: 80px; height: 80px; left: 10%; animation-delay: 0s; }
            .particle:nth-child(2) { width: 60px; height: 60px; left: 20%; animation-delay: 2s; }
            .particle:nth-child(3) { width: 40px; height: 40px; left: 35%; animation-delay: 4s; }
            .particle:nth-child(4) { width: 100px; height: 100px; left: 50%; animation-delay: 6s; }
            .particle:nth-child(5) { width: 50px; height: 50px; left: 70%; animation-delay: 8s; }
            .particle:nth-child(6) { width: 70px; height: 70px; left: 85%; animation-delay: 10s; }
            
            @keyframes float {
                0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
                10% { opacity: 1; }
                90% { opacity: 1; }
                100% { transform: translateY(-100px) rotate(360deg); opacity: 0; }
            }
            
            /* Glass morphism container */
            .glass-container {
                position: relative;
                z-index: 10;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem;
            }
            
            .glass-card {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 24px;
                padding: 3rem;
                max-width: 800px;
                width: 100%;
                box-shadow: 
                    0 25px 45px rgba(0, 0, 0, 0.1),
                    0 0 0 1px rgba(255, 255, 255, 0.05),
                    inset 0 1px 0 rgba(255, 255, 255, 0.1);
                animation: slideUp 1s ease-out;
            }
            
            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(50px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            /* Header */
            .header {
                text-align: center;
                margin-bottom: 3rem;
            }
            
            .logo {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 80px;
                height: 80px;
                background: linear-gradient(135deg, #ff6b6b, #4ecdc4);
                border-radius: 20px;
                margin-bottom: 1.5rem;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
                animation: pulse 2s infinite;
            }
            
            @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.05); }
            }
            
            .logo i {
                font-size: 2.5rem;
                color: white;
            }
            
            .title {
                font-size: 3rem;
                font-weight: 700;
                color: white;
                margin-bottom: 0.5rem;
                text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            }
            
            .subtitle {
                font-size: 1.25rem;
                color: rgba(255, 255, 255, 0.8);
                font-weight: 300;
                margin-bottom: 2rem;
            }
            
            /* Features grid */
            .features {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 1.5rem;
                margin-bottom: 3rem;
            }
            
            .feature {
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 16px;
                padding: 2rem;
                text-align: center;
                backdrop-filter: blur(10px);
                transition: all 0.3s ease;
            }
            
            .feature:hover {
                transform: translateY(-5px);
                background: rgba(255, 255, 255, 0.1);
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            }
            
            .feature-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 60px;
                height: 60px;
                background: linear-gradient(135deg, #667eea, #764ba2);
                border-radius: 12px;
                margin-bottom: 1rem;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            }
            
            .feature-icon i {
                font-size: 1.5rem;
                color: white;
            }
            
            .feature h3 {
                font-size: 1.25rem;
                color: white;
                margin-bottom: 0.5rem;
                font-weight: 600;
            }
            
            .feature p {
                color: rgba(255, 255, 255, 0.7);
                font-size: 0.9rem;
                line-height: 1.5;
            }
            
            /* Action buttons */
            .actions {
                display: flex;
                gap: 1rem;
                justify-content: center;
                flex-wrap: wrap;
                margin-bottom: 2rem;
            }
            
            .btn {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 1rem 2rem;
                border-radius: 12px;
                text-decoration: none;
                font-weight: 500;
                transition: all 0.3s ease;
                border: none;
                cursor: pointer;
                font-size: 1rem;
            }
            
            .btn-primary {
                background: linear-gradient(135deg, #667eea, #764ba2);
                color: white;
                box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
            }
            
            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
            }
            
            .btn-secondary {
                background: rgba(255, 255, 255, 0.1);
                color: white;
                border: 1px solid rgba(255, 255, 255, 0.2);
                backdrop-filter: blur(10px);
            }
            
            .btn-secondary:hover {
                background: rgba(255, 255, 255, 0.2);
                transform: translateY(-2px);
            }
            
            /* Stats */
            .stats {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
                gap: 1rem;
                margin-top: 2rem;
            }
            
            .stat {
                text-align: center;
                padding: 1rem;
                background: rgba(255, 255, 255, 0.05);
                border-radius: 12px;
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            .stat-number {
                font-size: 2rem;
                font-weight: 700;
                color: #4ecdc4;
                display: block;
            }
            
            .stat-label {
                font-size: 0.8rem;
                color: rgba(255, 255, 255, 0.7);
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            /* Navigation */
            .nav {
                position: absolute;
                top: 2rem;
                right: 2rem;
                z-index: 20;
            }
            
            .nav a {
                color: white;
                text-decoration: none;
                padding: 0.5rem 1rem;
                margin-left: 1rem;
                border-radius: 8px;
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                transition: all 0.3s ease;
            }
            
            .nav a:hover {
                background: rgba(255, 255, 255, 0.2);
            }
            
            /* Footer */
            .footer {
                text-align: center;
                margin-top: 2rem;
                padding-top: 2rem;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            .footer p {
                color: rgba(255, 255, 255, 0.6);
                font-size: 0.9rem;
            }
            
            .api-url {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                background: rgba(0, 0, 0, 0.2);
                padding: 0.5rem 1rem;
                border-radius: 8px;
                color: #4ecdc4;
                font-family: 'Courier New', monospace;
                font-size: 0.9rem;
                margin: 1rem 0;
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            /* Responsive */
            @media (max-width: 768px) {
                .glass-card {
                    padding: 2rem;
                    margin: 1rem;
                }
                
                .title {
                    font-size: 2rem;
                }
                
                .features {
                    grid-template-columns: 1fr;
                }
                
                .actions {
                    flex-direction: column;
                }
                
                .nav {
                    position: relative;
                    top: auto;
                    right: auto;
                    text-align: center;
                    margin-bottom: 2rem;
                }
                
                .nav a {
                    margin: 0.25rem;
                }
            }
        </style>
    </head>
    <body>
        <!-- Animated background particles -->
        <div class="particles">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>
        
        <!-- Navigation -->
        @if (Route::has('login'))
            <nav class="nav">
                @auth
                    <a href="{{ url('/dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}">
                        <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}">
                            <i class="fas fa-user-plus"></i> Registrarse
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
        
        <!-- Main content -->
        <div class="glass-container">
            <div class="glass-card">
                <div class="header">
                    <div class="logo">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h1 class="title">Sistema Médico</h1>
                    <p class="subtitle">Backend API - Gestión Hospitalaria Integral</p>
                </div>
                
                <div class="features">
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h3>Gestión de Doctores</h3>
                        <p>Sistema completo para administrar perfiles médicos, especialidades y horarios</p>
                    </div>
                    
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Pacientes</h3>
                        <p>Registro integral de pacientes con historiales médicos completos</p>
                    </div>
                    
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3>Citas Médicas</h3>
                        <p>Programación y gestión avanzada de citas con notificaciones</p>
                    </div>
                    
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-pills"></i>
                        </div>
                        <h3>Medicamentos</h3>
                        <p>Control de inventario farmacéutico con alertas de stock</p>
                    </div>
                    
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-procedures"></i>
                        </div>
                        <h3>Cirugías</h3>
                        <p>Programación y seguimiento de procedimientos quirúrgicos</p>
                    </div>
                    
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <h3>Facturación</h3>
                        <p>Sistema completo de facturación y gestión financiera</p>
                    </div>
                </div>
                
                <div class="actions">
                    <a href="/api/documentation" class="btn btn-primary">
                        <i class="fas fa-book"></i>
                        Documentación API
                    </a>
                    <a href="/api/dashboard/stats" class="btn btn-secondary">
                        <i class="fas fa-chart-bar"></i>
                        Ver Estadísticas
                    </a>
                </div>
                
                <div class="stats">
                    <div class="stat">
                        <span class="stat-number">10</span>
                        <span class="stat-label">Módulos</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">50+</span>
                        <span class="stat-label">Endpoints</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">24/7</span>
                        <span class="stat-label">Disponible</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">REST</span>
                        <span class="stat-label">API</span>
                    </div>
                </div>
                
                <div class="footer">
                    <div class="api-url">
                        <i class="fas fa-link"></i>
                        {{ config('app.url') }}/api
                    </div>
                    <p>Sistema desarrollado con Laravel • Arquitectura REST • Autenticación JWT</p>
                    <p style="margin-top: 0.5rem; font-size: 0.8rem;">
                        <i class="fas fa-shield-alt"></i> Seguro • 
                        <i class="fas fa-rocket"></i> Escalable • 
                        <i class="fas fa-cogs"></i> Mantenible
                    </p>
                </div>
            </div>
        </div>
        
        <script>
            // Add some interactivity
            document.addEventListener('DOMContentLoaded', function() {
                // Animate stats on scroll
                const stats = document.querySelectorAll('.stat-number');
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.style.animationPlayState = 'running';
                        }
                    });
                });
                
                stats.forEach(stat => {
                    observer.observe(stat);
                });
                
                // Add click effect to buttons
                document.querySelectorAll('.btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        let ripple = document.createElement('span');
                        ripple.classList.add('ripple');
                        this.appendChild(ripple);
                        
                        setTimeout(() => {
                            ripple.remove();
                        }, 600);
                    });
                });
            });
        </script>
    </body>
</html>
