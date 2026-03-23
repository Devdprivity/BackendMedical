<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Configuración Inicial - MediCare Pro')</title>

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

    <style>
        :root {
            --primary: #6366f1;
            --secondary: #8b5cf6;
            --success: #10b981;
            --danger: #ef4444;
            --dark: #1e293b;
            --gray-200: #e2e8f0;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            color: var(--dark);
        }

        .onboarding-wrapper {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }

        .onboarding-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .onboarding-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--dark);
            font-weight: 700;
            font-size: 1.25rem;
        }

        .onboarding-logo span {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .card {
            background: white;
            border-radius: 16px;
            border: 1px solid var(--gray-200);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .card-body { padding: 1.5rem; }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark);
            margin: 0;
        }

        .page-subtitle {
            color: var(--gray-500);
            margin: 0.25rem 0 0;
            font-size: 0.95rem;
        }

        .btn-outline-danger {
            border: 1px solid var(--danger);
            color: var(--danger);
            background: transparent;
            padding: 0.4rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s;
        }

        .btn-outline-danger:hover {
            background: var(--danger);
            color: white;
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="onboarding-wrapper">
        <div class="onboarding-topbar">
            <a href="{{ route('dashboard') }}" class="onboarding-logo">
                <i class="fas fa-heartbeat"></i>
                <span>MediCare Pro</span>
            </a>
            <a href="#" onclick="logout()" class="btn-outline-danger">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </div>

        @yield('content')
    </div>

    <script>
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
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
