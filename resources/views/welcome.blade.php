<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MediCare Pro - Sistema de Gestión Clínica Inteligente</title>
        
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        
        <!-- Font Awesome -->
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
                line-height: 1.5;
                color: var(--on-surface);
                background: var(--surface);
                overflow-x: hidden;
                scroll-behavior: smooth;
            }

            /* Navigation */
            .navbar {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1000;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                border-bottom: 1px solid var(--outline-variant);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .navbar.scrolled {
                background: rgba(255, 255, 255, 0.98);
                box-shadow: 0 2px 16px var(--shadow-light);
            }

            .nav-container {
                max-width: 1440px;
                margin: 0 auto;
                padding: 0 24px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                height: 64px;
            }

            .logo {
                display: flex;
                align-items: center;
                gap: 12px;
                text-decoration: none;
                font-size: 20px;
                font-weight: 600;
                color: var(--on-surface);
                transition: opacity 0.2s ease;
            }

            .logo:hover {
                opacity: 0.8;
            }

            .logo-icon {
                width: 32px;
                height: 32px;
                background: var(--gradient-primary);
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 16px;
            }

            .nav-links {
                display: flex;
                list-style: none;
                gap: 32px;
                align-items: center;
            }

            .nav-links a {
                text-decoration: none;
                color: var(--on-surface-variant);
                font-weight: 500;
                font-size: 14px;
                padding: 8px 16px;
                border-radius: 20px;
                transition: all 0.2s ease;
                position: relative;
            }

            .nav-links a:hover {
                background: var(--surface-container);
                color: var(--on-surface);
            }

            .nav-actions {
                display: flex;
                gap: 12px;
                align-items: center;
            }

            .btn {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 10px 24px;
                border: none;
                border-radius: 24px;
                font-size: 14px;
                font-weight: 500;
                text-decoration: none;
                cursor: pointer;
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
                overflow: hidden;
            }

            .btn-primary {
                background: var(--primary);
                color: white;
                box-shadow: 0 1px 3px var(--shadow-light);
            }

            .btn-primary:hover {
                background: var(--primary-dark);
                box-shadow: 0 2px 8px var(--shadow);
                transform: translateY(-1px);
            }

            .btn-outline {
                background: transparent;
                color: var(--primary);
                border: 1px solid var(--outline);
            }

            .btn-outline:hover {
                background: var(--surface-container);
                border-color: var(--primary);
            }

            /* Mobile Menu */
            .mobile-menu-btn {
                display: none;
                background: none;
                border: none;
                font-size: 20px;
                color: var(--on-surface-variant);
                cursor: pointer;
                padding: 8px;
                border-radius: 8px;
            }

            .mobile-menu-btn:hover {
                background: var(--surface-container);
            }

            /* Hero Section */
            .hero {
                min-height: 100vh;
                display: flex;
                align-items: center;
                background: var(--gradient-surface);
                position: relative;
                overflow: hidden;
            }

            .hero::before {
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
            }

            .hero-container {
                max-width: 1440px;
                margin: 0 auto;
                padding: 0 24px;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 80px;
                align-items: center;
                position: relative;
                z-index: 1;
            }

            .hero-content {
                max-width: 600px;
            }

            .hero-badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 6px 16px;
                background: rgba(66, 133, 244, 0.1);
                color: var(--primary);
                border-radius: 20px;
                font-size: 12px;
                font-weight: 500;
                margin-bottom: 24px;
                border: 1px solid rgba(66, 133, 244, 0.2);
            }

            .hero-title {
                font-size: clamp(2.5rem, 5vw, 4rem);
                font-weight: 800;
                line-height: 1.1;
                margin-bottom: 24px;
                background: linear-gradient(135deg, var(--on-surface) 0%, var(--on-surface-variant) 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .hero-subtitle {
                font-size: 20px;
                color: var(--on-surface-variant);
                line-height: 1.6;
                margin-bottom: 32px;
            }

            .hero-actions {
                display: flex;
                gap: 16px;
                flex-wrap: wrap;
                margin-bottom: 48px;
            }

            .btn-hero {
                padding: 16px 32px;
                font-size: 16px;
                border-radius: 28px;
            }

            .hero-stats {
                display: flex;
                gap: 32px;
                flex-wrap: wrap;
            }

            .stat {
                text-align: left;
            }

            .stat-number {
                font-size: 24px;
                font-weight: 700;
                color: var(--on-surface);
                margin-bottom: 4px;
            }

            .stat-label {
                font-size: 14px;
                color: var(--on-surface-variant);
            }

            /* Hero Visual */
            .hero-visual {
                position: relative;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .dashboard-mockup {
                background: white;
                border-radius: 16px;
                padding: 24px;
                box-shadow: 
                    0 4px 6px -1px var(--shadow-light),
                    0 2px 4px -1px var(--shadow-light);
                transform: perspective(1000px) rotateY(-5deg) rotateX(2deg);
                transition: transform 0.3s ease;
                max-width: 500px;
                width: 100%;
            }

            .dashboard-mockup:hover {
                transform: perspective(1000px) rotateY(-2deg) rotateX(1deg);
            }

            .mockup-header {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 20px;
                padding-bottom: 16px;
                border-bottom: 1px solid var(--outline-variant);
            }

            .mockup-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
            }

            .dot-red { background: #ff5f56; }
            .dot-yellow { background: #ffbd2e; }
            .dot-green { background: #27ca3f; }

            .mockup-content {
                space-y: 16px;
            }

            .mockup-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 12px 0;
                border-bottom: 1px solid var(--outline-variant);
            }

            .mockup-row:last-child {
                border-bottom: none;
            }

            .mockup-label {
                font-size: 14px;
                color: var(--on-surface-variant);
            }

            .mockup-value {
                font-size: 16px;
                font-weight: 600;
                color: var(--on-surface);
            }

            .mockup-chart {
                height: 100px;
                background: linear-gradient(135deg, rgba(66, 133, 244, 0.1) 0%, rgba(52, 168, 83, 0.1) 100%);
                border-radius: 8px;
                margin: 16px 0;
                position: relative;
                overflow: hidden;
            }

            .mockup-chart::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                height: 60%;
                background: linear-gradient(45deg, var(--primary) 0%, var(--secondary) 100%);
                opacity: 0.8;
                clip-path: polygon(0 100%, 20% 80%, 40% 90%, 60% 70%, 80% 75%, 100% 60%, 100% 100%);
            }

            /* Features Section */
            .features {
                padding: 120px 0;
                background: var(--surface);
            }

            .section-container {
                max-width: 1440px;
                margin: 0 auto;
                padding: 0 24px;
            }

            .section-header {
                text-align: center;
                margin-bottom: 80px;
            }

            .section-badge {
                display: inline-block;
                padding: 8px 16px;
                background: rgba(52, 168, 83, 0.1);
                color: var(--secondary);
                border-radius: 20px;
                font-size: 14px;
                font-weight: 500;
                margin-bottom: 16px;
            }

            .section-title {
                font-size: clamp(2rem, 4vw, 3rem);
                font-weight: 700;
                margin-bottom: 16px;
                color: var(--on-surface);
            }

            .section-subtitle {
                font-size: 18px;
                color: var(--on-surface-variant);
                max-width: 600px;
                margin: 0 auto;
            }

            .features-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
                gap: 32px;
            }

            .feature-card {
                background: white;
                border-radius: 16px;
                padding: 32px;
                box-shadow: 0 1px 3px var(--shadow-light);
                border: 1px solid var(--outline-variant);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
                overflow: hidden;
            }

            .feature-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: var(--gradient-primary);
                transform: scaleX(0);
                transition: transform 0.3s ease;
            }

            .feature-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 8px 25px var(--shadow-light);
            }

            .feature-card:hover::before {
                transform: scaleX(1);
            }

            .feature-icon {
                width: 56px;
                height: 56px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 20px;
                font-size: 24px;
                color: white;
                background: var(--gradient-primary);
            }

            .feature-title {
                font-size: 20px;
                font-weight: 600;
                margin-bottom: 12px;
                color: var(--on-surface);
            }

            .feature-description {
                color: var(--on-surface-variant);
                line-height: 1.6;
            }

            /* Stats Section */
            .stats-section {
                padding: 80px 0;
                background: var(--surface-variant);
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 48px;
                text-align: center;
            }

            .stat-card {
                background: white;
                padding: 32px 24px;
                border-radius: 16px;
                box-shadow: 0 1px 3px var(--shadow-light);
            }

            .stat-card-number {
                font-size: 2.5rem;
                font-weight: 800;
                margin-bottom: 8px;
                background: var(--gradient-primary);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .stat-card-label {
                color: var(--on-surface-variant);
                font-weight: 500;
            }

            /* CTA Section */
            .cta-section {
                padding: 120px 0;
                background: var(--surface);
            }

            .cta-container {
                max-width: 800px;
                margin: 0 auto;
                text-align: center;
                background: var(--gradient-primary);
                color: white;
                padding: 64px 48px;
                border-radius: 24px;
                box-shadow: 0 8px 32px var(--shadow);
            }

            .cta-title {
                font-size: 2.5rem;
                font-weight: 700;
                margin-bottom: 16px;
            }

            .cta-subtitle {
                font-size: 18px;
                opacity: 0.9;
                margin-bottom: 32px;
            }

            .btn-cta {
                background: white;
                color: var(--primary);
                padding: 16px 32px;
                font-size: 16px;
                font-weight: 600;
                border-radius: 28px;
            }

            .btn-cta:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            }

            /* Footer */
            .footer {
                background: var(--on-surface);
                color: white;
                padding: 80px 0 32px;
            }

            .footer-content {
                display: grid;
                grid-template-columns: 2fr 1fr 1fr 1fr;
                gap: 48px;
                margin-bottom: 48px;
            }

            .footer-brand {
                max-width: 300px;
            }

            .footer-logo {
                color: white;
                margin-bottom: 16px;
            }

            .footer-description {
                color: rgba(255, 255, 255, 0.7);
                line-height: 1.6;
                margin-bottom: 24px;
            }

            .footer-social {
                display: flex;
                gap: 12px;
            }

            .social-link {
                width: 40px;
                height: 40px;
                border-radius: 8px;
                background: rgba(255, 255, 255, 0.1);
                display: flex;
                align-items: center;
                justify-content: center;
                color: rgba(255, 255, 255, 0.7);
                text-decoration: none;
                transition: all 0.2s ease;
            }

            .social-link:hover {
                background: rgba(255, 255, 255, 0.2);
                color: white;
            }

            .footer-section h3 {
                font-size: 16px;
                font-weight: 600;
                margin-bottom: 16px;
                color: white;
            }

            .footer-links {
                list-style: none;
            }

            .footer-links li {
                margin-bottom: 12px;
            }

            .footer-links a {
                color: rgba(255, 255, 255, 0.7);
                text-decoration: none;
                transition: color 0.2s ease;
            }

            .footer-links a:hover {
                color: white;
            }

            .footer-bottom {
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                padding-top: 32px;
                text-align: center;
                color: rgba(255, 255, 255, 0.7);
            }

            /* Animations */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
            }

            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.7; }
            }

            @keyframes rotate {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }

            .animate-fade-in {
                opacity: 0;
                animation: fadeInUp 0.6s ease forwards;
            }

            .animate-delay-1 { animation-delay: 0.1s; }
            .animate-delay-2 { animation-delay: 0.2s; }
            .animate-delay-3 { animation-delay: 0.3s; }
            .animate-delay-4 { animation-delay: 0.4s; }

            /* Benefits Section */
            .benefits-section {
                padding: 120px 0;
                background: var(--surface-variant);
            }

            .benefits-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 32px;
                margin-top: 64px;
            }

            .benefit-item {
                text-align: center;
                padding: 32px 24px;
                background: white;
                border-radius: 16px;
                box-shadow: 0 4px 6px var(--shadow-light);
                transition: all 0.3s ease;
            }

            .benefit-item:hover {
                transform: translateY(-8px);
                box-shadow: 0 12px 24px var(--shadow);
            }

            .benefit-icon {
                width: 64px;
                height: 64px;
                margin: 0 auto 20px;
                background: var(--gradient-primary);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 24px;
                color: white;
                animation: float 3s ease-in-out infinite;
            }

            .benefit-title {
                font-size: 18px;
                font-weight: 600;
                margin-bottom: 12px;
                color: var(--on-surface);
            }

            .benefit-description {
                color: var(--on-surface-variant);
                line-height: 1.6;
            }

            /* Pricing Section */
            .pricing-section {
                padding: 120px 0;
                background: var(--surface);
            }

            .pricing-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 16px;
                margin: 48px 0 64px;
            }

            .toggle-label {
                font-weight: 600;
                color: var(--on-surface-variant);
                cursor: pointer;
                transition: color 0.3s ease;
            }

            .toggle-label.active {
                color: var(--primary);
            }

            .toggle-switch {
                width: 60px;
                height: 32px;
                background: var(--outline);
                border-radius: 16px;
                position: relative;
                cursor: pointer;
                transition: background 0.3s ease;
            }

            .toggle-switch.active {
                background: var(--primary);
            }

            .toggle-slider {
                width: 28px;
                height: 28px;
                background: white;
                border-radius: 50%;
                position: absolute;
                top: 2px;
                left: 2px;
                transition: transform 0.3s ease;
                box-shadow: 0 2px 4px var(--shadow-light);
            }

            .toggle-slider.yearly {
                transform: translateX(28px);
            }

            .save-badge {
                background: var(--secondary);
                color: white;
                padding: 6px 12px;
                border-radius: 16px;
                font-size: 12px;
                font-weight: 600;
            }

            .pricing-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 32px;
                max-width: 1200px;
                margin: 0 auto;
            }

            .price-card {
                background: white;
                border-radius: 20px;
                padding: 40px 32px;
                box-shadow: 0 4px 6px var(--shadow-light);
                border: 2px solid transparent;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .price-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 16px 32px var(--shadow);
            }

            .price-card.popular {
                border-color: var(--primary);
                transform: scale(1.05);
            }

            .price-card.popular:hover {
                transform: scale(1.05) translateY(-8px);
            }

            .popular-badge {
                position: absolute;
                top: -1px;
                left: 50%;
                transform: translateX(-50%);
                background: var(--primary);
                color: white;
                padding: 8px 24px;
                border-radius: 0 0 12px 12px;
                font-size: 12px;
                font-weight: 600;
            }

            .price-header {
                text-align: center;
                margin-bottom: 32px;
            }

            .price-title {
                font-size: 24px;
                font-weight: 700;
                margin-bottom: 8px;
                color: var(--on-surface);
            }

            .price-subtitle {
                color: var(--on-surface-variant);
                font-size: 14px;
            }

            .price-amount {
                text-align: center;
                margin-bottom: 32px;
            }

            .price-currency {
                font-size: 20px;
                font-weight: 600;
                color: var(--on-surface-variant);
                vertical-align: top;
            }

            .price-value {
                font-size: 48px;
                font-weight: 800;
                color: var(--on-surface);
                margin: 0 4px;
            }

            .price-period {
                font-size: 16px;
                color: var(--on-surface-variant);
            }

            .price-features {
                list-style: none;
                margin-bottom: 32px;
            }

            .price-features li {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 8px 0;
                color: var(--on-surface-variant);
            }

            .price-features i {
                color: var(--secondary);
                font-size: 14px;
            }

            .price-btn {
                display: block;
                width: 100%;
                padding: 16px;
                background: var(--surface-container);
                color: var(--on-surface);
                text-align: center;
                text-decoration: none;
                border-radius: 12px;
                font-weight: 600;
                transition: all 0.3s ease;
                border: 2px solid transparent;
            }

            .price-btn:hover {
                background: var(--outline);
                transform: translateY(-2px);
            }

            .price-btn.primary {
                background: var(--primary);
                color: white;
            }

            .price-btn.primary:hover {
                background: var(--primary-dark);
            }

            /* Testimonials Section */
            .testimonials-section {
                padding: 120px 0;
                background: var(--surface-variant);
            }

            .testimonials-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
                gap: 32px;
                margin-top: 64px;
            }

            .testimonial-card {
                background: white;
                border-radius: 20px;
                padding: 32px;
                box-shadow: 0 4px 6px var(--shadow-light);
                transition: all 0.3s ease;
            }

            .testimonial-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 16px 32px var(--shadow);
            }

            .testimonial-content {
                margin-bottom: 24px;
            }

            .stars {
                display: flex;
                gap: 4px;
                margin-bottom: 16px;
            }

            .stars i {
                color: #fbbf24;
                font-size: 16px;
            }

            .testimonial-text {
                color: var(--on-surface-variant);
                line-height: 1.6;
                font-style: italic;
                font-size: 16px;
            }

            .testimonial-author {
                display: flex;
                align-items: center;
                gap: 16px;
            }

            .author-avatar {
                width: 48px;
                height: 48px;
                border-radius: 50%;
                overflow: hidden;
            }

            .author-avatar img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .author-name {
                font-size: 16px;
                font-weight: 600;
                color: var(--on-surface);
                margin-bottom: 4px;
            }

            .author-title {
                font-size: 14px;
                color: var(--on-surface-variant);
            }

            /* Advanced Features Section */
            .advanced-features-section {
                padding: 120px 0;
                background: var(--surface);
            }

            .advanced-features-content {
                margin-top: 64px;
            }

            .feature-showcase {
                background: white;
                border-radius: 20px;
                padding: 48px;
                box-shadow: 0 8px 16px var(--shadow-light);
                margin-bottom: 32px;
            }

            .showcase-item {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 48px;
                align-items: center;
            }

            .showcase-content h3 {
                font-size: 28px;
                font-weight: 700;
                margin-bottom: 16px;
                color: var(--on-surface);
            }

            .showcase-content p {
                color: var(--on-surface-variant);
                line-height: 1.6;
                margin-bottom: 24px;
                font-size: 16px;
            }

            .showcase-content ul {
                list-style: none;
            }

            .showcase-content li {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 8px 0;
                color: var(--on-surface-variant);
            }

            .showcase-content i {
                color: var(--secondary);
            }

            .showcase-visual {
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .ai-demo {
                position: relative;
                width: 200px;
                height: 200px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .ai-brain {
                width: 120px;
                height: 120px;
                background: var(--gradient-primary);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 48px;
                color: white;
                animation: pulse 2s ease-in-out infinite;
                position: relative;
                z-index: 2;
            }

            .ai-particles {
                position: absolute;
                width: 100%;
                height: 100%;
                border: 2px solid var(--primary);
                border-radius: 50%;
                opacity: 0.3;
                animation: rotate 10s linear infinite;
            }

            .ai-particles::before,
            .ai-particles::after {
                content: '';
                position: absolute;
                width: 8px;
                height: 8px;
                background: var(--primary);
                border-radius: 50%;
                top: -4px;
            }

            .ai-particles::before {
                left: 20px;
            }

            .ai-particles::after {
                right: 20px;
            }

            .feature-tabs {
                display: flex;
                justify-content: center;
                gap: 16px;
                flex-wrap: wrap;
            }

            .tab-btn {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 12px 24px;
                background: white;
                border: 2px solid var(--outline);
                border-radius: 12px;
                color: var(--on-surface-variant);
                cursor: pointer;
                transition: all 0.3s ease;
                font-weight: 500;
            }

            .tab-btn:hover,
            .tab-btn.active {
                border-color: var(--primary);
                color: var(--primary);
                background: rgba(66, 133, 244, 0.1);
            }

            /* FAQ Section */
            .faq-section {
                padding: 120px 0;
                background: var(--surface-variant);
            }

            .faq-container {
                max-width: 800px;
                margin: 64px auto 0;
            }

            .faq-item {
                background: white;
                border-radius: 12px;
                margin-bottom: 16px;
                overflow: hidden;
                box-shadow: 0 2px 4px var(--shadow-light);
            }

            .faq-question {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 24px 32px;
                cursor: pointer;
                transition: background 0.3s ease;
            }

            .faq-question:hover {
                background: var(--surface-container);
            }

            .faq-question h3 {
                font-size: 18px;
                font-weight: 600;
                color: var(--on-surface);
                margin: 0;
            }

            .faq-question i {
                color: var(--on-surface-variant);
                transition: transform 0.3s ease;
            }

            .faq-question.active i {
                transform: rotate(180deg);
            }

            .faq-answer {
                padding: 0 32px;
                max-height: 0;
                overflow: hidden;
                transition: all 0.3s ease;
            }

            .faq-answer.active {
                padding: 0 32px 24px;
                max-height: 200px;
            }

            .faq-answer p {
                color: var(--on-surface-variant);
                line-height: 1.6;
                margin: 0;
            }

            /* Responsive Design */
            @media (max-width: 1024px) {
                .hero-container {
                    grid-template-columns: 1fr;
                    gap: 48px;
                    text-align: center;
                }

                .features-grid {
                    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                    gap: 24px;
                }

                .footer-content {
                    grid-template-columns: 1fr 1fr;
                    gap: 32px;
                }

                .showcase-item {
                    grid-template-columns: 1fr;
                    gap: 32px;
                    text-align: center;
                }

                .pricing-grid {
                    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                }
            }

            @media (max-width: 768px) {
                .nav-links {
                    display: none;
                }

                .mobile-menu-btn {
                    display: block;
                }

                .hero-container {
                    padding: 0 16px;
                }

                .hero-actions {
                    flex-direction: column;
                    align-items: center;
                }

                .hero-stats {
                    justify-content: center;
                }

                .features-grid {
                    grid-template-columns: 1fr;
                }

                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 24px;
                }

                .footer-content {
                    grid-template-columns: 1fr;
                    gap: 32px;
                }

                .cta-container {
                    padding: 48px 24px;
                }

                .benefits-grid {
                    grid-template-columns: 1fr;
                }

                .pricing-grid {
                    grid-template-columns: 1fr;
                }

                .testimonials-grid {
                    grid-template-columns: 1fr;
                }

                .feature-tabs {
                    flex-direction: column;
                    align-items: center;
                }

                .pricing-toggle {
                    flex-wrap: wrap;
                    gap: 12px;
                }

                .price-card.popular {
                    transform: none;
                }

                .price-card.popular:hover {
                    transform: translateY(-8px);
                }
            }
        </style>
    </head>
    <body>
        <!-- Navigation -->
        <nav class="navbar" id="navbar">
            <div class="nav-container">
                <a href="/" class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    MediCare Pro
                </a>

                <ul class="nav-links">
                    <li><a href="#inicio">Inicio</a></li>
                    <li><a href="#funciones">Funciones</a></li>
                    <li><a href="#precios">Precios</a></li>
                    <li><a href="#testimonios">Testimonios</a></li>
                    <li><a href="#empresa">Empresa</a></li>
                </ul>

                <div class="nav-actions">
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i>
                        Acceder
                    </a>
                </div>

                <button class="mobile-menu-btn">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero" id="inicio">
            <div class="hero-container">
                <div class="hero-content">
                    <div class="hero-badge">
                        <i class="fas fa-sparkles"></i>
                        Nuevo: IA integrada para diagnósticos
                    </div>
                    
                    <h1 class="hero-title">El futuro de la gestión clínica</h1>
                    
                    <p class="hero-subtitle">
                        Plataforma integral que revoluciona la manera en que los profesionales de la salud gestionan sus consultorios, optimizan procesos y brindan atención excepcional.
                    </p>

                    <div class="hero-actions">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-hero">
                            <i class="fas fa-rocket"></i>
                            Comenzar ahora
                        </a>
                        <a href="#funciones" class="btn btn-outline btn-hero">
                            <i class="fas fa-play"></i>
                            Ver demo
                        </a>
                    </div>

                    <div class="hero-stats">
                        <div class="stat">
                            <div class="stat-number">10,000+</div>
                            <div class="stat-label">Profesionales activos</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">1M+</div>
                            <div class="stat-label">Pacientes atendidos</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">99.9%</div>
                            <div class="stat-label">Tiempo de actividad</div>
                        </div>
                    </div>
                </div>

                <div class="hero-visual">
                    <div class="dashboard-mockup">
                        <div class="mockup-header">
                            <div class="mockup-dot dot-red"></div>
                            <div class="mockup-dot dot-yellow"></div>
                            <div class="mockup-dot dot-green"></div>
                        </div>
                        
                        <div class="mockup-content">
                            <div class="mockup-row">
                                <span class="mockup-label">Citas hoy</span>
                                <span class="mockup-value">24</span>
                            </div>
                            <div class="mockup-row">
                                <span class="mockup-label">Pacientes nuevos</span>
                                <span class="mockup-value">8</span>
                            </div>
                            <div class="mockup-row">
                                <span class="mockup-label">Ingresos del mes</span>
                                <span class="mockup-value">$45,280</span>
                            </div>
                            
                            <div class="mockup-chart"></div>
                            
                            <div class="mockup-row">
                                <span class="mockup-label">Próxima cita</span>
                                <span class="mockup-value">10:30 AM</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features" id="funciones">
            <div class="section-container">
                <div class="section-header">
                    <div class="section-badge">Funcionalidades</div>
                    <h2 class="section-title">Todo lo que necesitas en una plataforma</h2>
                    <p class="section-subtitle">
                        Herramientas avanzadas diseñadas para optimizar cada aspecto de tu práctica médica
                    </p>
                </div>

                <div class="features-grid">
                    <div class="feature-card animate-fade-in">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3 class="feature-title">Gestión Inteligente de Citas</h3>
                        <p class="feature-description">
                            Sistema de reservas con IA que optimiza horarios, reduce cancelaciones y mejora la experiencia del paciente con recordatorios automáticos.
                        </p>
                    </div>

                    <div class="feature-card animate-fade-in animate-delay-1">
                        <div class="feature-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h3 class="feature-title">Expedientes Clínicos Digitales</h3>
                        <p class="feature-description">
                            Historiales médicos completos y seguros con acceso instantáneo, integración con dispositivos médicos y análisis predictivo.
                        </p>
                    </div>

                    <div class="feature-card animate-fade-in animate-delay-2">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="feature-title">Analytics y Reportes Avanzados</h3>
                        <p class="feature-description">
                            Dashboards interactivos con métricas clave, análisis de tendencias y reportes personalizables para tomar decisiones informadas.
                        </p>
                    </div>

                    <div class="feature-card animate-fade-in animate-delay-3">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3 class="feature-title">Apps Móviles Nativas</h3>
                        <p class="feature-description">
                            Aplicaciones para iOS y Android que permiten gestionar tu práctica desde cualquier lugar con sincronización en tiempo real.
                        </p>
                    </div>

                    <div class="feature-card animate-fade-in animate-delay-4">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="feature-title">Seguridad de Nivel Empresarial</h3>
                        <p class="feature-description">
                            Cumplimiento HIPAA, encriptación end-to-end, autenticación multifactor y copias de seguridad automáticas en la nube.
                        </p>
                    </div>

                    <div class="feature-card animate-fade-in animate-delay-1">
                        <div class="feature-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h3 class="feature-title">Asistente de IA Médica</h3>
                        <p class="feature-description">
                            Inteligencia artificial que asiste en diagnósticos, sugiere tratamientos y automatiza tareas administrativas repetitivas.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="stats-section">
            <div class="section-container">
                <div class="stats-grid">
                    <div class="stat-card animate-fade-in">
                        <div class="stat-card-number">10,000+</div>
                        <div class="stat-card-label">Médicos Activos</div>
                    </div>
                    <div class="stat-card animate-fade-in animate-delay-1">
                        <div class="stat-card-number">1M+</div>
                        <div class="stat-card-label">Pacientes Atendidos</div>
                    </div>
                    <div class="stat-card animate-fade-in animate-delay-2">
                        <div class="stat-card-number">99.9%</div>
                        <div class="stat-card-label">Uptime Garantizado</div>
                    </div>
                    <div class="stat-card animate-fade-in animate-delay-3">
                        <div class="stat-card-number">24/7</div>
                        <div class="stat-card-label">Soporte Premium</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Benefits Section -->
        <section class="benefits-section">
            <div class="section-container">
                <div class="section-header">
                    <div class="section-badge">Beneficios</div>
                    <h2 class="section-title">¿Por qué elegir MediCare Pro?</h2>
                    <p class="section-subtitle">
                        Descubre las ventajas que nos convierten en la plataforma líder en gestión clínica
                    </p>
                </div>

                <div class="benefits-grid">
                    <div class="benefit-item animate-fade-in">
                        <div class="benefit-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3 class="benefit-title">Ahorra 5+ horas diarias</h3>
                        <p class="benefit-description">Automatización inteligente que reduce tareas administrativas</p>
                    </div>
                    <div class="benefit-item animate-fade-in animate-delay-1">
                        <div class="benefit-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="benefit-title">Incrementa ingresos 40%</h3>
                        <p class="benefit-description">Optimización de horarios y reducción de cancelaciones</p>
                    </div>
                    <div class="benefit-item animate-fade-in animate-delay-2">
                        <div class="benefit-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="benefit-title">Mejora satisfacción pacientes</h3>
                        <p class="benefit-description">Experiencia digital moderna y comunicación fluida</p>
                    </div>
                    <div class="benefit-item animate-fade-in animate-delay-3">
                        <div class="benefit-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="benefit-title">100% Seguro y Confiable</h3>
                        <p class="benefit-description">Cumplimiento normativo y protección de datos médicos</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section class="pricing-section" id="precios">
            <div class="section-container">
                <div class="section-header">
                    <div class="section-badge">Planes y Precios</div>
                    <h2 class="section-title">Elige el plan perfecto para ti</h2>
                    <p class="section-subtitle">
                        Planes flexibles que se adaptan a cualquier tamaño de práctica médica
                    </p>
                </div>

                <div class="pricing-toggle">
                    <span class="toggle-label active" id="monthly-label">Mensual</span>
                    <div class="toggle-switch" onclick="togglePricing()">
                        <div class="toggle-slider" id="pricing-slider"></div>
                    </div>
                    <span class="toggle-label" id="yearly-label">Anual</span>
                    <span class="save-badge">Ahorra 17%</span>
                </div>

                <div class="pricing-grid">
                    <div class="price-card">
                        <div class="price-header">
                            <h3 class="price-title">Gratuito</h3>
                            <p class="price-subtitle">Para probar el sistema</p>
                        </div>
                        <div class="price-amount">
                            <span class="price-currency">$</span>
                            <span class="price-value">0</span>
                            <span class="price-period">/mes</span>
                        </div>
                        <ul class="price-features">
                            <li><i class="fas fa-check"></i> 1 Doctor</li>
                            <li><i class="fas fa-check"></i> 50 Pacientes</li>
                            <li><i class="fas fa-check"></i> 100 Citas/mes</li>
                            <li><i class="fas fa-check"></i> Soporte básico</li>
                        </ul>
                        <a href="{{ route('login') }}" class="price-btn">Comenzar gratis</a>
                    </div>

                    <div class="price-card popular">
                        <div class="popular-badge">Más Popular</div>
                        <div class="price-header">
                            <h3 class="price-title">Doctor Independiente</h3>
                            <p class="price-subtitle">Ideal para médicos independientes</p>
                        </div>
                        <div class="price-amount">
                            <span class="price-currency">$</span>
                            <span class="price-value" id="doctor-price">29</span>
                            <span class="price-period" id="doctor-period">/mes</span>
                        </div>
                        <ul class="price-features">
                            <li><i class="fas fa-check"></i> 1 Doctor</li>
                            <li><i class="fas fa-check"></i> Pacientes ilimitados</li>
                            <li><i class="fas fa-check"></i> Citas ilimitadas</li>
                            <li><i class="fas fa-check"></i> Soporte prioritario</li>
                            <li><i class="fas fa-check"></i> App móvil</li>
                        </ul>
                        <a href="{{ route('login') }}" class="price-btn primary">Elegir plan</a>
                    </div>

                    <div class="price-card">
                        <div class="price-header">
                            <h3 class="price-title">Clínica Pequeña</h3>
                            <p class="price-subtitle">Para clínicas con múltiples doctores</p>
                        </div>
                        <div class="price-amount">
                            <span class="price-currency">$</span>
                            <span class="price-value" id="clinic-price">79</span>
                            <span class="price-period" id="clinic-period">/mes</span>
                        </div>
                        <ul class="price-features">
                            <li><i class="fas fa-check"></i> 5 Doctores</li>
                            <li><i class="fas fa-check"></i> Todo ilimitado</li>
                            <li><i class="fas fa-check"></i> Reportes avanzados</li>
                            <li><i class="fas fa-check"></i> Integración laboratorio</li>
                            <li><i class="fas fa-check"></i> Facturación integrada</li>
                        </ul>
                        <a href="{{ route('login') }}" class="price-btn">Elegir plan</a>
                    </div>

                    <div class="price-card">
                        <div class="price-header">
                            <h3 class="price-title">Enterprise</h3>
                            <p class="price-subtitle">Solución empresarial completa</p>
                        </div>
                        <div class="price-amount">
                            <span class="price-currency">$</span>
                            <span class="price-value" id="enterprise-price">199</span>
                            <span class="price-period" id="enterprise-period">/mes</span>
                        </div>
                        <ul class="price-features">
                            <li><i class="fas fa-check"></i> Doctores ilimitados</li>
                            <li><i class="fas fa-check"></i> Múltiples ubicaciones</li>
                            <li><i class="fas fa-check"></i> API personalizada</li>
                            <li><i class="fas fa-check"></i> Soporte dedicado</li>
                            <li><i class="fas fa-check"></i> Implementación asistida</li>
                        </ul>
                        <a href="{{ route('login') }}" class="price-btn">Contactar ventas</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="testimonials-section" id="testimonios">
            <div class="section-container">
                <div class="section-header">
                    <div class="section-badge">Testimonios</div>
                    <h2 class="section-title">Lo que dicen nuestros usuarios</h2>
                    <p class="section-subtitle">
                        Miles de profesionales ya confían en MediCare Pro
                    </p>
                </div>

                <div class="testimonials-grid">
                    <div class="testimonial-card animate-fade-in">
                        <div class="testimonial-content">
                            <div class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="testimonial-text">
                                "MediCare Pro transformó completamente mi práctica. Ahorro 6 horas diarias en tareas administrativas y mis pacientes están más satisfechos que nunca."
                            </p>
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">
                                <img src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=100&h=100&fit=crop&crop=face" alt="Dr. María González">
                            </div>
                            <div class="author-info">
                                <h4 class="author-name">Dr. María González</h4>
                                <p class="author-title">Cardióloga - Clínica del Corazón</p>
                            </div>
                        </div>
                    </div>

                    <div class="testimonial-card animate-fade-in animate-delay-1">
                        <div class="testimonial-content">
                            <div class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="testimonial-text">
                                "La implementación fue súper fácil y el soporte técnico es excepcional. Nuestros ingresos aumentaron 35% en los primeros 6 meses."
                            </p>
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">
                                <img src="https://images.unsplash.com/photo-1582750433449-648ed127bb54?w=100&h=100&fit=crop&crop=face" alt="Dr. Carlos Mendoza">
                            </div>
                            <div class="author-info">
                                <h4 class="author-name">Dr. Carlos Mendoza</h4>
                                <p class="author-title">Director - Centro Médico Integral</p>
                            </div>
                        </div>
                    </div>

                    <div class="testimonial-card animate-fade-in animate-delay-2">
                        <div class="testimonial-content">
                            <div class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="testimonial-text">
                                "Como pediatra, necesitaba un sistema que fuera fácil de usar. MediCare Pro es intuitivo y mis asistentes lo adoptaron inmediatamente."
                            </p>
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">
                                <img src="https://images.unsplash.com/photo-1594824475317-8b9d0e9a1d0e?w=100&h=100&fit=crop&crop=face" alt="Dra. Ana Rodríguez">
                            </div>
                            <div class="author-info">
                                <h4 class="author-name">Dra. Ana Rodríguez</h4>
                                <p class="author-title">Pediatra - Consulta Privada</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Advanced Features Section -->
        <section class="advanced-features-section">
            <div class="section-container">
                <div class="section-header">
                    <div class="section-badge">Características Avanzadas</div>
                    <h2 class="section-title">Tecnología de vanguardia</h2>
                    <p class="section-subtitle">
                        Funciones innovadoras que marcan la diferencia
                    </p>
                </div>

                <div class="advanced-features-content">
                    <div class="feature-showcase">
                        <div class="showcase-item active" data-feature="ai">
                            <div class="showcase-content">
                                <h3>Inteligencia Artificial Médica</h3>
                                <p>Asistente de IA que analiza síntomas, sugiere diagnósticos y optimiza tratamientos basado en las mejores prácticas médicas.</p>
                                <ul>
                                    <li><i class="fas fa-check"></i> Análisis predictivo de síntomas</li>
                                    <li><i class="fas fa-check"></i> Sugerencias de tratamiento</li>
                                    <li><i class="fas fa-check"></i> Detección de interacciones medicamentosas</li>
                                    <li><i class="fas fa-check"></i> Alertas de seguimiento automático</li>
                                </ul>
                            </div>
                            <div class="showcase-visual">
                                <div class="ai-demo">
                                    <div class="ai-brain">
                                        <i class="fas fa-brain"></i>
                                    </div>
                                    <div class="ai-particles"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="feature-tabs">
                        <button class="tab-btn active" data-feature="ai">
                            <i class="fas fa-robot"></i>
                            IA Médica
                        </button>
                        <button class="tab-btn" data-feature="telemedicine">
                            <i class="fas fa-video"></i>
                            Telemedicina
                        </button>
                        <button class="tab-btn" data-feature="analytics">
                            <i class="fas fa-chart-pie"></i>
                            Analytics
                        </button>
                        <button class="tab-btn" data-feature="integration">
                            <i class="fas fa-plug"></i>
                            Integraciones
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="faq-section">
            <div class="section-container">
                <div class="section-header">
                    <div class="section-badge">Preguntas Frecuentes</div>
                    <h2 class="section-title">¿Tienes dudas?</h2>
                    <p class="section-subtitle">
                        Resolvemos las preguntas más comunes sobre MediCare Pro
                    </p>
                </div>

                <div class="faq-container">
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFaq(this)">
                            <h3>¿Cómo puedo migrar mis datos existentes?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Ofrecemos migración gratuita de datos desde cualquier sistema. Nuestro equipo técnico se encarga de todo el proceso sin interrumpir tu práctica médica.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFaq(this)">
                            <h3>¿Es seguro almacenar datos médicos en la nube?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Absolutamente. Utilizamos encriptación de nivel bancario, cumplimos con HIPAA y todas las regulaciones locales. Tus datos están más seguros que en servidores locales.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFaq(this)">
                            <h3>¿Qué pasa si necesito cancelar mi suscripción?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Puedes cancelar en cualquier momento sin penalizaciones. Te proporcionamos una exportación completa de todos tus datos en formatos estándar.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFaq(this)">
                            <h3>¿Ofrecen capacitación para mi equipo?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Sí, incluimos capacitación completa para tu equipo. Webinars en vivo, documentación detallada y soporte personalizado durante la implementación.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFaq(this)">
                            <h3>¿Funciona en dispositivos móviles?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Tenemos apps nativas para iOS y Android, además de una versión web completamente responsive. Accede desde cualquier dispositivo, en cualquier momento.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="section-container">
                <div class="cta-container animate-fade-in">
                    <h2 class="cta-title">Transforma tu práctica médica hoy</h2>
                    <p class="cta-subtitle">
                        Únete a miles de profesionales que ya confían en MediCare Pro para revolucionar su atención médica
                    </p>
                    <a href="{{ route('login') }}" class="btn btn-cta">
                        <i class="fas fa-arrow-right"></i>
                        Comenzar gratis
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="section-container">
                <div class="footer-content">
                    <div class="footer-brand">
                        <a href="/" class="logo footer-logo">
                            <div class="logo-icon">
                                <i class="fas fa-heartbeat"></i>
                            </div>
                            MediCare Pro
                        </a>
                        <p class="footer-description">
                            La plataforma de gestión clínica más avanzada del mercado, diseñada para profesionales que buscan excelencia en la atención médica.
                        </p>
                        <div class="footer-social">
                            <a href="#" class="social-link">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="#" class="social-link">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="social-link">
                                <i class="fab fa-youtube"></i>
                            </a>
                            <a href="#" class="social-link">
                                <i class="fab fa-github"></i>
                            </a>
                        </div>
                    </div>

                    <div class="footer-section">
                        <h3>Producto</h3>
                        <ul class="footer-links">
                            <li><a href="#funciones">Funciones</a></li>
                            <li><a href="#">Integraciones</a></li>
                            <li><a href="#">API</a></li>
                            <li><a href="#">Seguridad</a></li>
                        </ul>
                    </div>

                    <div class="footer-section">
                        <h3>Recursos</h3>
                        <ul class="footer-links">
                            <li><a href="#">Documentación</a></li>
                            <li><a href="#">Centro de Ayuda</a></li>
                            <li><a href="#">Blog</a></li>
                            <li><a href="#">Webinars</a></li>
                        </ul>
                    </div>

                    <div class="footer-section">
                        <h3>Empresa</h3>
                        <ul class="footer-links">
                            <li><a href="#">Acerca de</a></li>
                            <li><a href="#">Carreras</a></li>
                            <li><a href="#">Contacto</a></li>
                            <li><a href="#">Privacidad</a></li>
                        </ul>
                    </div>
                </div>

                <div class="footer-bottom">
                    <p>&copy; {{ date('Y') }} MediCare Pro. Todos los derechos reservados.</p>
                </div>
            </div>
        </footer>

        <script>
            // Navigation scroll effect
            window.addEventListener('scroll', function() {
                const navbar = document.getElementById('navbar');
                if (window.scrollY > 20) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });

            // Smooth scrolling
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Intersection Observer for animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe all animated elements
            document.querySelectorAll('.animate-fade-in').forEach(el => {
                observer.observe(el);
            });

            // Mobile menu toggle
            document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
                // Simple mobile menu implementation
                const navLinks = document.querySelector('.nav-links');
                navLinks.style.display = navLinks.style.display === 'flex' ? 'none' : 'flex';
            });

            // Add some interactive effects
            document.querySelectorAll('.feature-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Pricing toggle functionality
            let currentBilling = 'monthly';
            const pricingData = {
                monthly: {
                    doctor: { price: 70, period: '/mes' },
                    clinic: { price: 340, period: '/mes' },
                    enterprise: { price: 1500, period: '/mes' }
                },
                yearly: {
                    doctor: { price: 700, period: '/año' },
                    clinic: { price: 3400, period: '/año' },
                    enterprise: { price: 15000, period: '/año' }
                }
            };

            function togglePricing() {
                currentBilling = currentBilling === 'monthly' ? 'yearly' : 'monthly';
                
                const slider = document.getElementById('pricing-slider');
                const monthlyLabel = document.getElementById('monthly-label');
                const yearlyLabel = document.getElementById('yearly-label');
                const toggleSwitch = document.querySelector('.toggle-switch');
                
                if (currentBilling === 'yearly') {
                    slider.classList.add('yearly');
                    toggleSwitch.classList.add('active');
                    monthlyLabel.classList.remove('active');
                    yearlyLabel.classList.add('active');
                } else {
                    slider.classList.remove('yearly');
                    toggleSwitch.classList.remove('active');
                    monthlyLabel.classList.add('active');
                    yearlyLabel.classList.remove('active');
                }
                
                // Update pricing display
                updatePricingDisplay();
            }

            function updatePricingDisplay() {
                const data = pricingData[currentBilling];
                
                document.getElementById('doctor-price').textContent = data.doctor.price;
                document.getElementById('doctor-period').textContent = data.doctor.period;
                document.getElementById('clinic-price').textContent = data.clinic.price;
                document.getElementById('clinic-period').textContent = data.clinic.period;
                document.getElementById('enterprise-price').textContent = data.enterprise.price;
                document.getElementById('enterprise-period').textContent = data.enterprise.period;
            }

            // FAQ accordion functionality
            function toggleFaq(element) {
                const faqItem = element.parentElement;
                const answer = faqItem.querySelector('.faq-answer');
                const isActive = element.classList.contains('active');
                
                // Close all other FAQ items
                document.querySelectorAll('.faq-question').forEach(q => {
                    q.classList.remove('active');
                    q.parentElement.querySelector('.faq-answer').classList.remove('active');
                });
                
                // Toggle current item
                if (!isActive) {
                    element.classList.add('active');
                    answer.classList.add('active');
                }
            }

            // Advanced scroll animations
            const advancedObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                        
                        // Special animations for specific elements
                        if (entry.target.classList.contains('benefit-icon')) {
                            entry.target.style.animationDelay = Math.random() * 0.5 + 's';
                        }
                        
                        if (entry.target.classList.contains('price-card')) {
                            entry.target.style.animationDelay = Array.from(entry.target.parentElement.children).indexOf(entry.target) * 0.1 + 's';
                        }
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            // Observe all animated elements
            document.querySelectorAll('.animate-fade-in, .benefit-item, .price-card, .testimonial-card').forEach(el => {
                advancedObserver.observe(el);
            });

            // Smooth scroll for navigation links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        const headerOffset = 80;
                        const elementPosition = target.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Parallax effect for hero section
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                const parallax = document.querySelector('.hero');
                if (parallax) {
                    const speed = scrolled * 0.5;
                    parallax.style.transform = `translateY(${speed}px)`;
                }
            });

            // Counter animation for stats
            function animateCounter(element, target, duration = 2000) {
                let start = 0;
                const increment = target / (duration / 16);
                
                const timer = setInterval(() => {
                    start += increment;
                    if (start >= target) {
                        element.textContent = target.toLocaleString() + (element.dataset.suffix || '');
                        clearInterval(timer);
                    } else {
                        element.textContent = Math.floor(start).toLocaleString() + (element.dataset.suffix || '');
                    }
                }, 16);
            }

            // Animate counters when they come into view
            const statsObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                        const target = parseInt(entry.target.dataset.target);
                        animateCounter(entry.target, target);
                        entry.target.classList.add('animated');
                    }
                });
            });

            // Set up counter data
            document.querySelectorAll('.stat-card-number').forEach((el, index) => {
                const values = [10000, 1000000, 99.9, 24];
                const suffixes = ['+', '+', '%', '/7'];
                el.dataset.target = values[index];
                el.dataset.suffix = suffixes[index];
                statsObserver.observe(el);
            });

            // Typing animation for hero title
            function typeWriter(element, text, speed = 100) {
                let i = 0;
                element.innerHTML = '';
                
                function type() {
                    if (i < text.length) {
                        element.innerHTML += text.charAt(i);
                        i++;
                        setTimeout(type, speed);
                    }
                }
                type();
            }

            // Initialize typing animation
            setTimeout(() => {
                const heroTitle = document.querySelector('.hero-title');
                if (heroTitle) {
                    const originalText = heroTitle.textContent;
                    typeWriter(heroTitle, originalText, 50);
                }
            }, 1000);

            // Add floating animation to dashboard mockup
            const dashboardMockup = document.querySelector('.dashboard-mockup');
            if (dashboardMockup) {
                setInterval(() => {
                    dashboardMockup.style.transform = 'perspective(1000px) rotateY(-5deg) rotateX(2deg) translateY(-5px)';
                    setTimeout(() => {
                        dashboardMockup.style.transform = 'perspective(1000px) rotateY(-5deg) rotateX(2deg) translateY(0px)';
                    }, 2000);
                }, 4000);
            }

            // Add hover effects to testimonial cards
            document.querySelectorAll('.testimonial-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-12px) scale(1.02)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Lazy loading for images
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        observer.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });

            // Add click ripple effect to buttons
            document.querySelectorAll('.btn, .price-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = x + 'px';
                    ripple.style.top = y + 'px';
                    ripple.classList.add('ripple');
                    
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });

            // Add CSS for ripple effect
            const style = document.createElement('style');
            style.textContent = `
                .btn, .price-btn {
                    position: relative;
                    overflow: hidden;
                }
                
                .ripple {
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.3);
                    pointer-events: none;
                    transform: scale(0);
                    animation: ripple-animation 0.6s linear;
                }
                
                @keyframes ripple-animation {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        </script>
    </body>
</html>
