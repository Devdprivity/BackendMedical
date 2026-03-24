<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistema de gestión médica integral para clínicas y hospitales. Administra pacientes, citas, historiales médicos y más.">
    
    <title>@yield('title', 'DrOrganiza - Sistema de Gestión Médica')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ secure_asset('images/logo-icon.svg') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Styles -->
    <style>
        :root {
            --primary: #00539B;
            --primary-light: #0080FF;
            --primary-dark: #003366;
            --secondary: #00AEEF;
            --accent: #FF6B6B;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --dark: #1A202C;
            --darker: #0F172A;
            --light: #F8FAFC;
            --lighter: #FFFFFF;
            --gray-100: #F1F5F9;
            --gray-200: #E2E8F0;
            --gray-300: #CBD5E1;
            --gray-400: #94A3B8;
            --gray-500: #64748B;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1E293B;
            --gray-900: #0F172A;
            --sidebar-width: 280px;
            --header-height: 80px;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-md: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 25px 50px -12px rgb(0 0 0 / 0.25);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            
            /* Aliases for consistency */
            --primary-color: var(--primary);
            --secondary-color: var(--secondary);
            --success-color: var(--success);
            --danger-color: var(--danger);
            --dark-color: var(--dark);
            --border-color: var(--gray-200);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        /* Base Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light);
            color: var(--gray-800);
            line-height: 1.7;
            font-size: 0.9375rem;
            font-weight: 400;
            overflow-x: hidden;
        }
        
        h1, h2, h3, h4, h5, h6,
        .h1, .h2, .h3, .h4, .h5, .h6 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            line-height: 1.3;
            color: var(--dark);
        }
        
        /* Layout */
        .app-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--header-height);
            background: var(--lighter);
            display: flex;
            align-items: center;
            padding: 0 2rem;
            z-index: 1000;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }
        
        .header.scrolled {
            box-shadow: var(--shadow);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            transition: var(--transition);
        }
        
        .logo-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            box-shadow: 0 4px 15px rgba(0, 83, 155, 0.2);
            transition: var(--transition);
        }
        
        .logo-text {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            margin-left: auto;
            gap: 1.5rem;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            transition: var(--transition);
            padding: 0.5rem 1rem;
            border-radius: 12px;
            background: transparent;
            border: none;
            position: relative;
        }
        
        .user-menu:hover {
            background: var(--gray-100);
        }
        
        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.125rem;
            box-shadow: 0 4px 15px rgba(0, 131, 255, 0.2);
            transition: var(--transition);
        }
        
        /* User Dropdown Styles */
        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-200);
            min-width: 280px;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: var(--transition);
        }
        
        .user-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-avatar-small {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.25rem;
        }
        
        .user-name {
            font-weight: 600;
            color: var(--dark);
            font-size: 1rem;
        }
        
        .user-email {
            font-size: 0.875rem;
            color: var(--gray-600);
        }
        
        .user-plan-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 0.5rem;
            transition: var(--transition);
        }
        
        .user-plan-badge.free {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }
        
        .user-plan-badge.doctor {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .user-plan-badge.small_clinic {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }
        
        .user-plan-badge.large_clinic {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }
        
        .user-plan-badge.enterprise {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
        }
        
        .user-plan-badge.trial {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
            animation: pulse 2s infinite;
        }
        
        .user-plan-badge.expired {
            background: linear-gradient(135deg, #6b7280, #4b5563);
            color: white;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        .plan-status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
            opacity: 0.8;
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.5rem;
            color: var(--gray-700);
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.9375rem;
        }
        
        .dropdown-item:hover {
            background: var(--gray-100);
            color: var(--primary);
        }
        
        .dropdown-item.text-danger {
            color: var(--danger);
        }
        
        .dropdown-item.text-danger:hover {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }
        
        .dropdown-divider {
            height: 1px;
            background: var(--gray-200);
            margin: 0.5rem 0;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: var(--header-height);
            left: 0;
            width: var(--sidebar-width);
            height: calc(100vh - var(--header-height));
            background: var(--lighter);
            border-right: 1px solid var(--gray-200);
            z-index: 900;
            transition: var(--transition);
            overflow-y: auto;
            padding: 0;
        }
        
        .sidebar-menu {
            padding: 1.5rem 0;
        }
        
        .menu-section {
            margin-bottom: 2rem;
        }
        
        .menu-title {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--gray-500);
            margin-bottom: 0.75rem;
            padding: 0 1.5rem;
        }
        
        .menu-item {
            display: flex;
            align-items: center;
            padding: 0.875rem 1.5rem;
            color: var(--gray-700);
            text-decoration: none;
            transition: var(--transition);
            font-weight: 500;
            font-size: 0.9375rem;
            position: relative;
        }
        
        .menu-icon {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            color: var(--gray-500);
            transition: var(--transition);
            font-size: 1.1em;
        }
        
        .menu-item:hover {
            background: var(--gray-100);
            color: var(--primary);
            padding-left: 2rem;
        }
        
        .menu-item:hover .menu-icon {
            color: var(--primary);
            transform: translateX(3px);
        }
        
        .menu-item.active {
            background: linear-gradient(to right, rgba(0, 83, 155, 0.1), transparent);
            color: var(--primary);
            font-weight: 600;
            border-right: 3px solid var(--primary);
        }
        
        .menu-item.active .menu-icon {
            color: var(--primary);
        }
        
        .upgrade-badge {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            font-size: 0.625rem;
            font-weight: 700;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            animation: pulse-upgrade 2s infinite;
        }
        
        @keyframes pulse-upgrade {
            0%, 100% { 
                opacity: 1;
                transform: translateY(-50%) scale(1);
            }
            50% { 
                opacity: 0.8;
                transform: translateY(-50%) scale(1.05);
            }
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 2rem;
            min-height: calc(100vh - var(--header-height));
            background-color: var(--light);
            transition: var(--transition);
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .page-title i {
            color: var(--primary);
            font-size: 1.5rem;
        }
        
        .page-subtitle {
            color: var(--gray-600);
            font-size: 1rem;
            margin-top: 0.5rem;
        }
        
        .page-actions {
            display: flex;
            gap: 0.75rem;
        }
        
        /* Cards */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            overflow: hidden;
            transition: var(--transition);
        }
        
        .card:hover {
            box-shadow: var(--shadow);
        }
        
        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            background: var(--gray-50);
        }
        
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.875rem;
            line-height: 1.5;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }
        
        .btn-secondary {
            background: var(--gray-600);
            color: white;
        }
        
        .btn-secondary:hover {
            background: var(--gray-700);
            transform: translateY(-1px);
        }
        
        .btn-success {
            background: var(--success);
            color: white;
        }
        
        .btn-success:hover {
            background: #059669;
            transform: translateY(-1px);
        }
        
        .btn-danger {
            background: var(--danger);
            color: white;
        }
        
        .btn-danger:hover {
            background: #DC2626;
            transform: translateY(-1px);
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
        
        /* Forms */
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            font-size: 0.875rem;
            transition: var(--transition);
            background: white;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 83, 155, 0.1);
        }
        
        /* Tables */
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .table th {
            font-weight: 600;
            color: var(--dark);
            background: var(--gray-100);
            font-size: 0.875rem;
        }
        
        .table tr:hover {
            background: var(--gray-50);
        }
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }
        
        .pagination-btn {
            padding: 0.5rem 1rem;
            border: 1px solid var(--gray-300);
            background: white;
            color: var(--gray-600);
            border-radius: 6px;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            font-size: 0.875rem;
        }
        
        .pagination-btn:hover {
            background: var(--gray-100);
            border-color: var(--gray-400);
        }
        
        .pagination-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .actions-dropdown {
            position: relative;
        }
        
        .actions-btn {
            background: none;
            border: none;
            padding: 0.5rem;
            border-radius: 6px;
            cursor: pointer;
            color: var(--gray-500);
            transition: var(--transition);
        }
        
        .actions-btn:hover {
            background: var(--gray-100);
            color: var(--dark);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .header {
                padding: 0 1rem;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }
        
        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--gray-700);
            cursor: pointer;
            padding: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: block;
            }
            
            .logo-text {
                display: none;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-left">
            <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <a href="{{ route('dashboard') }}" class="logo">
                <div class="logo-icon">
                    <i class="fas fa-user-md"></i>
                </div>
                <span class="logo-text">DrOrganiza</span>
            </a>
        </div>
        
        <div class="header-right">
            <div class="user-menu" onclick="toggleUserMenu()">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <span>{{ Auth::user()->name ?? 'Usuario' }}</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            
            <!-- User Dropdown Menu -->
            <div class="user-dropdown" id="userDropdown">
                <div class="dropdown-header">
                    <div class="user-info">
                        <div class="user-avatar-small">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <div class="user-name">{{ Auth::user()->name ?? 'Usuario' }}</div>
                            <div class="user-email">{{ Auth::user()->email ?? 'usuario@email.com' }}</div>
                            <div id="userPlanBadge" class="user-plan-badge">
                                <div class="plan-status-indicator"></div>
                                <span>Cargando plan...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dropdown-divider"></div>
                <a href="{{ route('subscription.dashboard') }}" class="dropdown-item">
                    <i class="fas fa-crown"></i>
                    Mi Suscripción
                </a>
                <a href="{{ route('subscription.plans') }}" class="dropdown-item">
                    <i class="fas fa-list"></i>
                    Ver Planes
                </a>
                <a href="{{ route('profile.show') }}" class="dropdown-item">
                    <i class="fas fa-user"></i>
                    Mi Perfil
                </a>
                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                    <i class="fas fa-edit"></i>
                    Editar Perfil
                </a>
                <a href="{{ route('profile.settings') }}" class="dropdown-item">
                    <i class="fas fa-cog"></i>
                    Configuración
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" onclick="logout()" class="dropdown-item text-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-menu">
            <div class="menu-section">
                <div class="menu-title">Principal</div>
                <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie menu-icon"></i>
                    Dashboard
                </a>
            </div>
            
            <div class="menu-section">
                <div class="menu-title">Gestión</div>
                
                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'doctor' || Auth::user()->role === 'nurse' || Auth::user()->role === 'receptionist')
                <a href="{{ route('patients.index') }}" class="menu-item {{ request()->routeIs('patients.*') ? 'active' : '' }}">
                    <i class="fas fa-users menu-icon"></i>
                    Pacientes
                </a>
                @endif
                
                @if(Auth::user()->role === 'admin')
                <a href="{{ route('doctors.index') }}" class="menu-item {{ request()->routeIs('doctors.*') ? 'active' : '' }}">
                    <i class="fas fa-user-md menu-icon"></i>
                    Doctores
                </a>
                @endif
                
                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'doctor' || Auth::user()->role === 'nurse' || Auth::user()->role === 'receptionist')
                <a href="{{ route('appointments.index') }}" class="menu-item {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check menu-icon"></i>
                    Citas
                </a>
                @endif
                
                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'doctor' || Auth::user()->role === 'lab_technician')
                <a href="{{ route('exams.index') }}" class="menu-item {{ request()->routeIs('exams.*') ? 'active' : '' }}">
                    <i class="fas fa-flask menu-icon"></i>
                    Exámenes
                </a>
                @endif
                
                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'doctor')
                <a href="{{ route('surgeries.index') }}" class="menu-item {{ request()->routeIs('surgeries.*') ? 'active' : '' }}">
                    <i class="fas fa-procedures menu-icon"></i>
                    Cirugías
                </a>
                @endif
            </div>
            
            <div class="menu-section">
                <div class="menu-title">Administración</div>
                
                @if(Auth::user()->role === 'admin')
                <a href="{{ route('clinics.index') }}" class="menu-item {{ request()->routeIs('clinics.*') ? 'active' : '' }}">
                    <i class="fas fa-hospital menu-icon"></i>
                    Clínicas
                </a>
                @endif
                
                @if((Auth::user()->role === 'admin' || Auth::user()->role === 'doctor' || Auth::user()->role === 'nurse') && 
                    (Auth::user()->role === 'admin' || (Auth::user()->currentSubscription && Auth::user()->currentSubscription->plan->hasFeature('inventory_management'))))
                <a href="{{ route('medications.index') }}" class="menu-item {{ request()->routeIs('medications.*') ? 'active' : '' }}">
                    <i class="fas fa-pills menu-icon"></i>
                    Medicamentos
                    @if(Auth::user()->role !== 'admin' && (!Auth::user()->currentSubscription || !Auth::user()->currentSubscription->plan->hasFeature('inventory_management')))
                    <span class="upgrade-badge">PRO</span>
                    @endif
                </a>
                @endif
                
                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'accountant')
                <a href="{{ route('invoices.index') }}" class="menu-item {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar menu-icon"></i>
                    Facturas
                </a>
                @endif
            </div>
            
            <div class="menu-section">
                <div class="menu-title">Sistema</div>
                
                @if(Auth::user()->role === 'admin')
                <a href="{{ route('users.index') }}" class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fas fa-users-cog menu-icon"></i>
                    Usuarios
                </a>
                @endif
                
                <a href="{{ route('documentation') }}" class="menu-item">
                    <i class="fas fa-book menu-icon"></i>
                    Documentación
                </a>
                <a href="#" onclick="logout()" class="menu-item">
                    <i class="fas fa-sign-out-alt menu-icon"></i>
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Scripts -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
        }
        
        function toggleUserMenu() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('show');
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.querySelector('.user-menu');
            const dropdown = document.getElementById('userDropdown');
            
            if (!userMenu.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });
        
        async function logout() {
            try {
                await fetch('/logout', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
            } catch (error) {
                console.error('Error al cerrar sesión:', error);
            } finally {
                window.location.href = '/';
            }
        }
        
        // Auto-close sidebar on mobile when clicking outside
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.mobile-menu-toggle');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(event.target) && 
                !toggle.contains(event.target) && 
                sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth > 768) {
                sidebar.classList.remove('open');
            }
        });
        
        // Load user subscription status
        async function loadUserPlan() {
            try {
                const response = await fetch('/api/subscription/status', {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    updatePlanBadge(data);
                } else {
                    updatePlanBadge({ status: 'none' });
                }
            } catch (error) {
                console.error('Error loading user plan:', error);
                updatePlanBadge({ status: 'error' });
            }
        }
        
        function updatePlanBadge(subscriptionData) {
            const badge = document.getElementById('userPlanBadge');
            if (!badge) return;
            
            let badgeClass = '';
            let badgeText = '';
            let badgeIcon = '';
            
            switch (subscriptionData.status) {
                case 'trial':
                    const hours = subscriptionData.trial_hours_remaining || 0;
                    const days  = subscriptionData.trial_days_remaining  || 0;
                    const timeStr = days > 0 ? `${days}d` : `${hours}h`;
                    badgeClass = hours <= 2 ? 'expired' : 'trial';
                    badgeText  = `Prueba (${timeStr})`;
                    badgeIcon  = '⚡';
                    break;
                case 'active':
                    const daysLeft = subscriptionData.days_remaining || 0;
                    badgeClass = daysLeft <= 7 ? 'trial' : (subscriptionData.plan?.slug || 'active');
                    badgeText  = subscriptionData.plan?.name || 'Plan Activo';
                    badgeIcon  = '✓';
                    break;
                case 'expired':
                    badgeClass = 'expired';
                    badgeText  = 'Plan Expirado';
                    badgeIcon  = '⚠';
                    break;
                case 'cancelled':
                    badgeClass = 'expired';
                    badgeText  = 'Plan Cancelado';
                    badgeIcon  = '⚠';
                    break;
                case 'none':
                    badgeClass = 'free';
                    badgeText  = 'Sin Plan';
                    badgeIcon  = '🆓';
                    break;
                default:
                    badgeClass = 'free';
                    badgeText  = 'Sin Plan';
                    badgeIcon  = '🆓';
                    break;
            }
            
            badge.className = `user-plan-badge ${badgeClass}`;
            badge.innerHTML = `
                <div class="plan-status-indicator"></div>
                <span>${badgeIcon} ${badgeText}</span>
            `;
        }
        
        // Load plan on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadUserPlan();
        });
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>