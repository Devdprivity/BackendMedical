<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Medical Management System</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@300;400;500;600&display=swap" rel="stylesheet">
        
        <!-- Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        
        <style>
            :root {
                --vp-c-white: #ffffff;
                --vp-c-black: #000000;
                --vp-c-gray-1: #f6f6f7;
                --vp-c-gray-2: #e7e7e9;
                --vp-c-gray-3: #c5c5c5;
                --vp-c-gray-soft: #f6f6f7;
                --vp-c-indigo-1: #3451b2;
                --vp-c-indigo-2: #3a5ccc;
                --vp-c-indigo-3: #5672cd;
                --vp-c-indigo-soft: #f0f4f8;
                --vp-c-green-1: #18794e;
                --vp-c-green-2: #299764;
                --vp-c-green-3: #30a46c;
                --vp-c-green-soft: #f4f9f6;
                --vp-c-yellow-1: #915930;
                --vp-c-yellow-2: #946300;
                --vp-c-yellow-3: #9f6a00;
                --vp-c-yellow-soft: #fef5e7;
                --vp-c-red-1: #b8272c;
                --vp-c-red-2: #d5393e;
                --vp-c-red-3: #e0575b;
                --vp-c-red-soft: #fef1f1;
                --vp-c-bg: #ffffff;
                --vp-c-bg-alt: #f6f6f7;
                --vp-c-bg-elv: #ffffff;
                --vp-c-bg-soft: #f6f6f7;
                --vp-c-border: #c2c2c4;
                --vp-c-divider: #e2e2e3;
                --vp-c-gutter: #e2e2e3;
                --vp-c-neutral: #8e8e93;
                --vp-c-neutral-inverse: #ffffff;
                --vp-c-text-1: #213547;
                --vp-c-text-2: #476582;
                --vp-c-text-3: #7c8b9c;
                --vp-c-text-code: #476582;
                --vp-sidebar-width: 272px;
                --vp-layout-max-width: 1440px;
            }

            [data-theme="dark"] {
                --vp-c-bg: #1b1b1f;
                --vp-c-bg-alt: #161618;
                --vp-c-bg-elv: #202127;
                --vp-c-bg-soft: #202127;
                --vp-c-border: #3c3f44;
                --vp-c-divider: #2e2e32;
                --vp-c-gutter: #000000;
                --vp-c-neutral: #6a6a6f;
                --vp-c-neutral-inverse: #1b1b1f;
                --vp-c-text-1: rgba(255, 255, 245, 0.86);
                --vp-c-text-2: rgba(235, 235, 245, 0.6);
                --vp-c-text-3: rgba(235, 235, 245, 0.38);
                --vp-c-text-code: #c9def1;
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                background: var(--vp-c-bg);
                color: var(--vp-c-text-1);
                line-height: 1.7;
                font-size: 16px;
                transition: background-color 0.2s ease, color 0.2s ease;
            }
            
            /* Layout */
            .layout {
                position: relative;
                min-height: 100vh;
            }
            
            /* Header */
            .header {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                height: 60px;
                background: var(--vp-c-bg);
                border-bottom: 1px solid var(--vp-c-divider);
                z-index: 1000;
                display: flex;
                align-items: center;
                padding: 0 24px;
                backdrop-filter: blur(8px);
            }
            
            .header-container {
                display: flex;
                align-items: center;
                justify-content: space-between;
                width: 100%;
                max-width: var(--vp-layout-max-width);
                margin: 0 auto;
            }
            
            .logo {
                display: flex;
                align-items: center;
                gap: 12px;
                font-size: 20px;
                font-weight: 600;
                color: var(--vp-c-text-1);
                text-decoration: none;
            }
            
            .logo-icon {
                width: 32px;
                height: 32px;
                background: linear-gradient(135deg, var(--vp-c-indigo-2), var(--vp-c-green-2));
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 16px;
            }
            
            .nav-items {
                display: flex;
                align-items: center;
                gap: 24px;
            }
            
            .nav-link {
                color: var(--vp-c-text-2);
                text-decoration: none;
                font-size: 14px;
                font-weight: 500;
                padding: 8px 12px;
                border-radius: 6px;
                transition: all 0.2s ease;
                display: flex;
                align-items: center;
                gap: 6px;
            }
            
            .nav-link:hover {
                color: var(--vp-c-text-1);
                background: var(--vp-c-bg-soft);
            }
            
            .nav-link.primary {
                background: var(--vp-c-indigo-2);
                color: white;
            }
            
            .nav-link.primary:hover {
                background: var(--vp-c-indigo-1);
            }
            
            .theme-toggle {
                background: none;
                border: none;
                color: var(--vp-c-text-2);
                cursor: pointer;
                padding: 8px;
                border-radius: 6px;
                transition: all 0.2s ease;
            }
            
            .theme-toggle:hover {
                color: var(--vp-c-text-1);
                background: var(--vp-c-bg-soft);
            }
            
            /* Sidebar */
            .sidebar {
                position: fixed;
                top: 60px;
                left: 0;
                width: var(--vp-sidebar-width);
                height: calc(100vh - 60px);
                background: var(--vp-c-bg-alt);
                border-right: 1px solid var(--vp-c-divider);
                overflow-y: auto;
                padding: 24px 0;
                z-index: 100;
            }
            
            .sidebar-group {
                margin-bottom: 32px;
            }
            
            .sidebar-group-title {
                font-size: 13px;
                font-weight: 600;
                color: var(--vp-c-text-1);
                padding: 0 24px 12px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .sidebar-item {
                display: block;
                color: var(--vp-c-text-2);
                text-decoration: none;
                padding: 8px 24px;
                font-size: 14px;
                transition: all 0.2s ease;
                border-left: 3px solid transparent;
            }
            
            .sidebar-item:hover {
                color: var(--vp-c-text-1);
                background: var(--vp-c-bg-soft);
            }
            
            .sidebar-item.active {
                color: var(--vp-c-indigo-2);
                background: var(--vp-c-indigo-soft);
                border-left-color: var(--vp-c-indigo-2);
                font-weight: 500;
            }
            
            /* Main Content */
            .main {
                margin-left: var(--vp-sidebar-width);
                padding-top: 60px;
                min-height: 100vh;
            }
            
            .content {
                max-width: 768px;
                margin: 0 auto;
                padding: 48px 24px;
            }
            
            .content h1 {
                font-size: 40px;
                font-weight: 700;
                color: var(--vp-c-text-1);
                margin-bottom: 16px;
                line-height: 1.2;
            }
            
            .content h2 {
                font-size: 28px;
                font-weight: 600;
                color: var(--vp-c-text-1);
                margin: 48px 0 16px;
                padding-bottom: 8px;
                border-bottom: 1px solid var(--vp-c-divider);
            }
            
            .content h3 {
                font-size: 20px;
                font-weight: 600;
                color: var(--vp-c-text-1);
                margin: 32px 0 16px;
            }
            
            .content p {
                color: var(--vp-c-text-2);
                margin-bottom: 16px;
                line-height: 1.7;
            }
            
            .content ul {
                margin: 16px 0;
                padding-left: 20px;
            }
            
            .content li {
                color: var(--vp-c-text-2);
                margin-bottom: 8px;
            }
            
            .content code {
                background: var(--vp-c-bg-soft);
                color: var(--vp-c-text-code);
                font-family: 'JetBrains Mono', 'Menlo', 'Monaco', 'Consolas', monospace;
                font-size: 14px;
                padding: 2px 6px;
                border-radius: 4px;
                border: 1px solid var(--vp-c-border);
            }
            
            .code-block {
                background: var(--vp-c-bg-soft);
                border: 1px solid var(--vp-c-border);
                border-radius: 8px;
                margin: 24px 0;
                overflow: hidden;
            }
            
            .code-header {
                background: var(--vp-c-bg-alt);
                border-bottom: 1px solid var(--vp-c-divider);
                padding: 12px 16px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .code-title {
                font-family: 'JetBrains Mono', monospace;
                font-size: 13px;
                color: var(--vp-c-text-2);
            }
            
            .copy-btn {
                background: none;
                border: none;
                color: var(--vp-c-text-3);
                cursor: pointer;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 12px;
                transition: all 0.2s ease;
            }
            
            .copy-btn:hover {
                color: var(--vp-c-text-1);
                background: var(--vp-c-bg-soft);
            }
            
            .code-content {
                padding: 16px;
                font-family: 'JetBrains Mono', monospace;
                font-size: 14px;
                line-height: 1.5;
                color: var(--vp-c-text-2);
                overflow-x: auto;
            }
            
            .code-content .comment {
                color: var(--vp-c-text-3);
            }
            
            .code-content .string {
                color: var(--vp-c-green-2);
            }
            
            .code-content .method {
                color: var(--vp-c-indigo-2);
                font-weight: 500;
            }
            
            .code-content .keyword {
                color: var(--vp-c-red-2);
                font-weight: 500;
            }
            
            /* Cards */
            .api-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 16px;
                margin: 24px 0;
            }
            
            .api-card {
                background: var(--vp-c-bg-soft);
                border: 1px solid var(--vp-c-border);
                border-radius: 8px;
                padding: 20px;
                transition: all 0.2s ease;
            }
            
            .api-card:hover {
                border-color: var(--vp-c-indigo-2);
                box-shadow: 0 4px 12px rgba(52, 81, 178, 0.1);
            }
            
            .api-card-header {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 12px;
            }
            
            .api-card-icon {
                width: 40px;
                height: 40px;
                background: var(--vp-c-indigo-soft);
                color: var(--vp-c-indigo-2);
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 18px;
            }
            
            .api-card h3 {
                font-size: 16px;
                font-weight: 600;
                color: var(--vp-c-text-1);
                margin: 0;
            }
            
            .api-card p {
                color: var(--vp-c-text-2);
                font-size: 14px;
                margin-bottom: 16px;
            }
            
            .api-card-footer {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding-top: 12px;
                border-top: 1px solid var(--vp-c-divider);
            }
            
            .endpoint-count {
                font-family: 'JetBrains Mono', monospace;
                font-size: 12px;
                color: var(--vp-c-text-3);
            }
            
            .api-link {
                color: var(--vp-c-indigo-2);
                text-decoration: none;
                font-size: 13px;
                font-weight: 500;
                display: flex;
                align-items: center;
                gap: 4px;
            }
            
            .api-link:hover {
                text-decoration: underline;
            }
            
            /* Status Badge */
            .status-badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: var(--vp-c-green-soft);
                color: var(--vp-c-green-1);
                padding: 6px 12px;
                border-radius: 16px;
                font-size: 13px;
                font-weight: 500;
                margin-bottom: 24px;
            }
            
            .status-dot {
                width: 6px;
                height: 6px;
                background: var(--vp-c-green-2);
                border-radius: 50%;
                animation: pulse 2s infinite;
            }
            
            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.5; }
            }
            
            /* TOC */
            .toc {
                position: fixed;
                top: 120px;
                right: 24px;
                width: 200px;
                background: var(--vp-c-bg-alt);
                border: 1px solid var(--vp-c-border);
                border-radius: 8px;
                padding: 16px;
                font-size: 13px;
            }
            
            .toc-title {
                font-weight: 600;
                color: var(--vp-c-text-1);
                margin-bottom: 12px;
            }
            
            .toc-item {
                display: block;
                color: var(--vp-c-text-2);
                text-decoration: none;
                padding: 4px 0;
                transition: color 0.2s ease;
            }
            
            .toc-item:hover {
                color: var(--vp-c-indigo-2);
            }
            
            /* Modal Styles */
            .modal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 2000;
                backdrop-filter: blur(4px);
            }
            
            .modal.show {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .modal-content {
                background: var(--vp-c-bg);
                border: 1px solid var(--vp-c-border);
                border-radius: 12px;
                padding: 24px;
                max-width: 500px;
                width: 90%;
                max-height: 80vh;
                overflow-y: auto;
                position: relative;
                animation: modalSlideIn 0.3s ease;
            }
            
            @keyframes modalSlideIn {
                from {
                    opacity: 0;
                    transform: scale(0.9) translateY(-20px);
                }
                to {
                    opacity: 1;
                    transform: scale(1) translateY(0);
                }
            }
            
            .modal-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
                padding-bottom: 12px;
                border-bottom: 1px solid var(--vp-c-divider);
            }
            
            .modal-title {
                font-size: 18px;
                font-weight: 600;
                color: var(--vp-c-text-1);
            }
            
            .modal-close {
                background: none;
                border: none;
                color: var(--vp-c-text-3);
                cursor: pointer;
                padding: 4px;
                border-radius: 4px;
                font-size: 16px;
                transition: all 0.2s ease;
            }
            
            .modal-close:hover {
                background: var(--vp-c-bg-soft);
                color: var(--vp-c-text-1);
            }
            
            .form-group {
                margin-bottom: 16px;
            }
            
            .form-label {
                display: block;
                font-size: 14px;
                font-weight: 500;
                color: var(--vp-c-text-1);
                margin-bottom: 6px;
            }
            
            .form-input {
                width: 100%;
                padding: 10px 12px;
                border: 1px solid var(--vp-c-border);
                border-radius: 6px;
                background: var(--vp-c-bg-soft);
                color: var(--vp-c-text-1);
                font-size: 14px;
                transition: all 0.2s ease;
            }
            
            .form-input:focus {
                outline: none;
                border-color: var(--vp-c-indigo-2);
                box-shadow: 0 0 0 3px rgba(58, 92, 204, 0.1);
            }
            
            .btn {
                padding: 10px 16px;
                border: none;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                text-decoration: none;
            }
            
            .btn-primary {
                background: var(--vp-c-indigo-2);
                color: white;
            }
            
            .btn-primary:hover {
                background: var(--vp-c-indigo-1);
            }
            
            .btn-primary:disabled {
                background: var(--vp-c-neutral);
                cursor: not-allowed;
            }
            
            .btn-secondary {
                background: var(--vp-c-bg-soft);
                color: var(--vp-c-text-2);
                border: 1px solid var(--vp-c-border);
            }
            
            .btn-secondary:hover {
                background: var(--vp-c-bg-alt);
                color: var(--vp-c-text-1);
            }
            
            .alert {
                padding: 12px;
                border-radius: 6px;
                margin-bottom: 16px;
                font-size: 14px;
            }
            
            .alert-error {
                background: var(--vp-c-red-soft);
                color: var(--vp-c-red-1);
                border: 1px solid rgba(184, 39, 44, 0.2);
            }
            
            .alert-success {
                background: var(--vp-c-green-soft);
                color: var(--vp-c-green-1);
                border: 1px solid rgba(24, 121, 78, 0.2);
            }
            
            .loading {
                display: inline-block;
                width: 16px;
                height: 16px;
                border: 2px solid transparent;
                border-top: 2px solid currentColor;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }
            
            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }
            
            .json-viewer {
                background: var(--vp-c-bg-soft);
                border: 1px solid var(--vp-c-border);
                border-radius: 8px;
                padding: 16px;
                font-family: 'JetBrains Mono', monospace;
                font-size: 13px;
                line-height: 1.5;
                overflow-x: auto;
                max-height: 400px;
                overflow-y: auto;
            }
            
            .json-viewer pre {
                margin: 0;
                color: var(--vp-c-text-2);
            }
            
            .auth-status {
                display: none;
                align-items: center;
                gap: 8px;
                padding: 8px 12px;
                background: var(--vp-c-green-soft);
                color: var(--vp-c-green-1);
                border-radius: 6px;
                font-size: 13px;
                margin-bottom: 16px;
            }
            
            .auth-status.show {
                display: flex;
            }
            
            .auth-user {
                font-weight: 500;
            }
            
            .logout-btn {
                background: none;
                border: none;
                color: var(--vp-c-green-2);
                cursor: pointer;
                font-size: 12px;
                text-decoration: underline;
            }
            
            /* Responsive */
            @media (max-width: 960px) {
                .sidebar {
                    transform: translateX(-100%);
                    transition: transform 0.3s ease;
                }
                
                .sidebar.open {
                    transform: translateX(0);
                }
                
                .main {
                    margin-left: 0;
                }
                
                .toc {
                    display: none;
                }
                
                .content {
                    padding: 24px 16px;
                }
                
                .content h1 {
                    font-size: 32px;
                }
                
                .api-grid {
                    grid-template-columns: 1fr;
                }
            }
            
            @media (max-width: 768px) {
                .header {
                    padding: 0 16px;
                }
                
                .nav-items {
                    gap: 12px;
                }
                
                .nav-link {
                    font-size: 13px;
                    padding: 6px 8px;
                }
            }
        </style>
    </head>
    <body data-theme="light">
        <div class="layout">
            <!-- Header -->
            <header class="header">
                <div class="header-container">
                    <a href="/" class="logo">
                        <div class="logo-icon">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                        Medical Management System
                    </a>
                    
                    <nav class="nav-items">
                        <button class="theme-toggle" onclick="toggleTheme()">
                            <i class="fas fa-moon"></i>
                        </button>
                    </nav>
                </div>
            </header>

            <!-- Sidebar -->
            <aside class="sidebar">
                <div class="sidebar-group">
                    <div class="sidebar-group-title">Introduction</div>
                    <a href="#getting-started" class="sidebar-item active">Getting Started</a>
                    <a href="#authentication" class="sidebar-item">Authentication</a>
                    <a href="#quick-start" class="sidebar-item">Quick Start</a>
                </div>
                
                <div class="sidebar-group">
                    <div class="sidebar-group-title">API Reference</div>
                    <a href="#doctors" class="sidebar-item">Doctor Management</a>
                    <a href="#patients" class="sidebar-item">Patient Registry</a>
                    <a href="#appointments" class="sidebar-item">Appointments</a>
                    <a href="#surgeries" class="sidebar-item">Surgery Management</a>
                    <a href="#medications" class="sidebar-item">Pharmacy System</a>
                    <a href="#invoices" class="sidebar-item">Billing & Invoicing</a>
                </div>
                
                <div class="sidebar-group">
                    <div class="sidebar-group-title">Advanced</div>
                    <a href="#webhooks" class="sidebar-item">Webhooks</a>
                    <a href="#rate-limiting" class="sidebar-item">Rate Limiting</a>
                    <a href="#errors" class="sidebar-item">Error Handling</a>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="main">
                <div class="content">
                    <div class="status-badge">
                        <div class="status-dot"></div>
                        API Status: Online
                    </div>
                    
                    <h1 id="getting-started">Getting Started</h1>
                    <p>
                        Welcome to the Medical Management System API documentation. This comprehensive RESTful API 
                        provides everything you need to build modern healthcare management applications with Laravel's 
                        robust and secure architecture.
                    </p>

                    <h2 id="try-it-online">Try It Online</h2>
                    <p>
                        You can try the Medical Management API directly in your browser using our interactive 
                        API explorer at <code>{{ config('app.url') }}/api</code>.
                    </p>

                    <h2 id="installation">Installation</h2>
                    
                    <h3>Prerequisites</h3>
                    <ul>
                        <li><strong>PHP</strong> version 8.1 or higher</li>
                        <li><strong>Laravel</strong> framework for accessing the API via its command line interface (CLI)</li>
                        <li><strong>Database</strong> with PostgreSQL support</li>
                        <li><strong>Composer</strong> is recommended, along with the <code>laravel/sanctum</code> package</li>
                    </ul>

                    <p>
                        The Medical Management API can be used on its own, or be installed into an existing project. 
                        In both cases, you can integrate it with:
                    </p>

                    <div class="code-block">
                        <div class="code-header">
                            <span class="code-title">Authentication & Basic Usage</span>
                            <button class="copy-btn" onclick="copyCode('auth-code')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <div class="code-content" id="auth-code">
<span class="comment"># Authenticate with the API</span>
<span class="method">POST</span> {{ config('app.url') }}/api/auth/login
{
  <span class="string">"email"</span>: <span class="string">"admin@example.com"</span>,
  <span class="string">"password"</span>: <span class="string">"password"</span>
}

<span class="comment"># Response</span>
{
  <span class="string">"access_token"</span>: <span class="string">"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."</span>,
  <span class="string">"token_type"</span>: <span class="string">"bearer"</span>,
  <span class="string">"expires_in"</span>: <span class="keyword">3600</span>
}

<span class="comment"># Use the token in subsequent requests</span>
<span class="method">GET</span> {{ config('app.url') }}/api/dashboard/stats
<span class="keyword">Authorization:</span> Bearer {access_token}
                        </div>
                    </div>

                    <h2 id="api-modules">API Modules</h2>
                    <p>
                        The Medical Management System provides comprehensive endpoints organized into logical modules:
                    </p>

                    <div class="api-grid">
                        <div class="api-card">
                            <div class="api-card-header">
                                <div class="api-card-icon">
                                    <i class="fas fa-user-md"></i>
                                </div>
                                <h3>Doctor Management</h3>
                            </div>
                            <p>Complete CRUD operations for medical professionals, specialties, schedules, and credentials management.</p>
                            <div class="api-card-footer">
                                <span class="endpoint-count">8 endpoints</span>
                                <a href="#" class="api-link" onclick="showApiData('doctors')">
                                    View API <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>

                        <div class="api-card">
                            <div class="api-card-header">
                                <div class="api-card-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h3>Patient Registry</h3>
                            </div>
                            <p>Patient information management with medical history, demographics, and insurance data handling.</p>
                            <div class="api-card-footer">
                                <span class="endpoint-count">10 endpoints</span>
                                <a href="#" class="api-link" onclick="showApiData('patients')">
                                    View API <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>

                        <div class="api-card">
                            <div class="api-card-header">
                                <div class="api-card-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <h3>Appointment System</h3>
                            </div>
                            <p>Advanced scheduling system with conflict detection, automated reminders, and status management.</p>
                            <div class="api-card-footer">
                                <span class="endpoint-count">12 endpoints</span>
                                <a href="#" class="api-link" onclick="showApiData('appointments')">
                                    View API <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>

                        <div class="api-card">
                            <div class="api-card-header">
                                <div class="api-card-icon">
                                    <i class="fas fa-procedures"></i>
                                </div>
                                <h3>Surgery Management</h3>
                            </div>
                            <p>Surgical procedure scheduling, operating room management, and post-operative tracking.</p>
                            <div class="api-card-footer">
                                <span class="endpoint-count">9 endpoints</span>
                                <a href="#" class="api-link" onclick="showApiData('surgeries')">
                                    View API <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>

                        <div class="api-card">
                            <div class="api-card-header">
                                <div class="api-card-icon">
                                    <i class="fas fa-pills"></i>
                                </div>
                                <h3>Pharmacy System</h3>
                            </div>
                            <p>Medication inventory management with stock alerts, prescription tracking, and expiration monitoring.</p>
                            <div class="api-card-footer">
                                <span class="endpoint-count">7 endpoints</span>
                                <a href="#" class="api-link" onclick="showApiData('medications')">
                                    View API <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>

                        <div class="api-card">
                            <div class="api-card-header">
                                <div class="api-card-icon">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </div>
                                <h3>Billing & Invoicing</h3>
                            </div>
                            <p>Comprehensive billing system with insurance processing, payment tracking, and financial reporting.</p>
                            <div class="api-card-footer">
                                <span class="endpoint-count">11 endpoints</span>
                                <a href="#" class="api-link" onclick="showApiData('invoices')">
                                    View API <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <h2 id="quick-start">Quick Start</h2>
                    <p>Here's a simple example to get you started with the Medical Management API:</p>

                    <div class="code-block">
                        <div class="code-header">
                            <span class="code-title">Example: List Patients</span>
                            <button class="copy-btn" onclick="copyCode('patients-code')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <div class="code-content" id="patients-code">
<span class="comment"># Get all patients with pagination</span>
<span class="method">GET</span> {{ config('app.url') }}/api/patients?page=1&limit=10
<span class="keyword">Authorization:</span> Bearer {access_token}

<span class="comment"># Create a new patient</span>
<span class="method">POST</span> {{ config('app.url') }}/api/patients
<span class="keyword">Authorization:</span> Bearer {access_token}
<span class="keyword">Content-Type:</span> application/json

{
  <span class="string">"first_name"</span>: <span class="string">"John"</span>,
  <span class="string">"last_name"</span>: <span class="string">"Doe"</span>,
  <span class="string">"email"</span>: <span class="string">"john.doe@example.com"</span>,
  <span class="string">"phone"</span>: <span class="string">"+1234567890"</span>,
  <span class="string">"date_of_birth"</span>: <span class="string">"1990-01-15"</span>
}
                        </div>
                    </div>

                    <h2 id="whats-next">What's Next?</h2>
                    <p>
                        Now that you have the Medical Management API up and running, here are some next steps:
                    </p>
                    <ul>
                        <li>Explore the <a href="#api-modules" class="api-link">API modules</a> to understand available endpoints</li>
                        <li>Check out the <a href="/api/documentation" class="api-link">complete API documentation</a></li>
                        <li>Learn about <a href="#authentication" class="api-link">authentication and security</a></li>
                        <li>Review <a href="#errors" class="api-link">error handling</a> best practices</li>
                    </ul>

                    <h2 id="authentication">Authentication</h2>
                    <p>
                        The Medical Management API uses Bearer token authentication. You need to authenticate 
                        first to obtain a token, then include it in all subsequent requests.
                    </p>

                    <div class="code-block">
                        <div class="code-header">
                            <span class="code-title">Login Request</span>
                            <button class="copy-btn" onclick="copyCode('login-code')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <div class="code-content" id="login-code">
<span class="method">POST</span> {{ config('app.url') }}/api/auth/login
<span class="keyword">Content-Type:</span> application/json

{
  <span class="string">"email"</span>: <span class="string">"admin@example.com"</span>,
  <span class="string">"password"</span>: <span class="string">"password"</span>
}

<span class="comment"># Response</span>
{
  <span class="string">"user"</span>: {
    <span class="string">"id"</span>: <span class="keyword">1</span>,
    <span class="string">"name"</span>: <span class="string">"Administrador"</span>,
    <span class="string">"email"</span>: <span class="string">"admin@example.com"</span>,
    <span class="string">"role"</span>: <span class="string">"admin"</span>
  },
  <span class="string">"token"</span>: <span class="string">"1|abc123..."</span>,
  <span class="string">"token_type"</span>: <span class="string">"Bearer"</span>
}
                        </div>
                    </div>

                    <h3>Available Endpoints</h3>
                    <ul>
                        <li><code>POST /auth/login</code> - Authenticate user</li>
                        <li><code>POST /auth/register</code> - Register new user</li>
                        <li><code>POST /auth/logout</code> - Logout user</li>
                        <li><code>GET /auth/user</code> - Get current user info</li>
                    </ul>

                    <h2 id="doctors">Doctor Management</h2>
                    <p>
                        Comprehensive doctor management system with specialty tracking, scheduling, 
                        and credential management.
                    </p>

                    <div class="code-block">
                        <div class="code-header">
                            <span class="code-title">Doctor Operations</span>
                            <button class="copy-btn" onclick="copyCode('doctors-code')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <div class="code-content" id="doctors-code">
<span class="comment"># List all doctors</span>
<span class="method">GET</span> {{ config('app.url') }}/api/doctors
<span class="keyword">Authorization:</span> Bearer {token}

<span class="comment"># Create new doctor</span>
<span class="method">POST</span> {{ config('app.url') }}/api/doctors
<span class="keyword">Authorization:</span> Bearer {token}
<span class="keyword">Content-Type:</span> application/json

{
  <span class="string">"user_id"</span>: <span class="keyword">2</span>,
  <span class="string">"specialty"</span>: <span class="string">"Cardiología"</span>,
  <span class="string">"license_number"</span>: <span class="string">"MD12345"</span>,
  <span class="string">"clinic_id"</span>: <span class="keyword">1</span>,
  <span class="string">"phone"</span>: <span class="string">"555-0456"</span>,
  <span class="string">"schedule"</span>: {
    <span class="string">"monday"</span>: <span class="string">"08:00-17:00"</span>,
    <span class="string">"tuesday"</span>: <span class="string">"08:00-17:00"</span>,
    <span class="string">"wednesday"</span>: <span class="string">"08:00-17:00"</span>
  }
}

<span class="comment"># Get doctor appointments</span>
<span class="method">GET</span> {{ config('app.url') }}/api/doctors/{id}/appointments
<span class="keyword">Authorization:</span> Bearer {token}
                        </div>
                    </div>

                    <h3>Doctor Endpoints</h3>
                    <ul>
                        <li><code>GET /doctors</code> - List all doctors</li>
                        <li><code>POST /doctors</code> - Create new doctor</li>
                        <li><code>GET /doctors/{id}</code> - Get specific doctor</li>
                        <li><code>PUT /doctors/{id}</code> - Update doctor</li>
                        <li><code>DELETE /doctors/{id}</code> - Delete doctor</li>
                        <li><code>GET /doctors/{id}/appointments</code> - Doctor's appointments</li>
                        <li><code>GET /doctors/{id}/today-appointments</code> - Today's appointments</li>
                        <li><code>GET /doctors/{id}/surgeries</code> - Doctor's surgeries</li>
                    </ul>

                    <h2 id="patients">Patient Registry</h2>
                    <p>
                        Complete patient management with medical history, vital signs, and comprehensive 
                        demographic information tracking.
                    </p>

                    <div class="code-block">
                        <div class="code-header">
                            <span class="code-title">Patient Management</span>
                            <button class="copy-btn" onclick="copyCode('patients-detail-code')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <div class="code-content" id="patients-detail-code">
<span class="comment"># Create comprehensive patient record</span>
<span class="method">POST</span> {{ config('app.url') }}/api/patients
<span class="keyword">Authorization:</span> Bearer {token}
<span class="keyword">Content-Type:</span> application/json

{
  <span class="string">"first_name"</span>: <span class="string">"Juan"</span>,
  <span class="string">"last_name"</span>: <span class="string">"Pérez"</span>,
  <span class="string">"email"</span>: <span class="string">"juan.perez@email.com"</span>,
  <span class="string">"phone"</span>: <span class="string">"555-0123"</span>,
  <span class="string">"date_of_birth"</span>: <span class="string">"1990-01-15"</span>,
  <span class="string">"gender"</span>: <span class="string">"male"</span>,
  <span class="string">"address"</span>: <span class="string">"Calle 456, Ciudad"</span>,
  <span class="string">"emergency_contact_name"</span>: <span class="string">"María Pérez"</span>,
  <span class="string">"emergency_contact_phone"</span>: <span class="string">"555-0124"</span>,
  <span class="string">"blood_type"</span>: <span class="string">"O+"</span>,
  <span class="string">"identification_number"</span>: <span class="string">"12345678"</span>
}

<span class="comment"># Add vital signs</span>
<span class="method">POST</span> {{ config('app.url') }}/api/patients/{id}/vital-signs
<span class="keyword">Authorization:</span> Bearer {token}

{
  <span class="string">"blood_pressure"</span>: <span class="string">"120/80"</span>,
  <span class="string">"heart_rate"</span>: <span class="keyword">75</span>,
  <span class="string">"temperature"</span>: <span class="keyword">36.5</span>,
  <span class="string">"weight"</span>: <span class="keyword">70.5</span>,
  <span class="string">"height"</span>: <span class="keyword">175</span>
}
                        </div>
                    </div>

                    <h3>Patient Endpoints</h3>
                    <ul>
                        <li><code>GET /patients</code> - List all patients</li>
                        <li><code>POST /patients</code> - Create new patient</li>
                        <li><code>GET /patients/{id}</code> - Get specific patient</li>
                        <li><code>PUT /patients/{id}</code> - Update patient</li>
                        <li><code>DELETE /patients/{id}</code> - Delete patient</li>
                        <li><code>GET /patients/{id}/medical-history</code> - Medical history</li>
                        <li><code>PUT /patients/{id}/medical-history</code> - Update medical history</li>
                        <li><code>GET /patients/{id}/vital-signs</code> - Vital signs</li>
                        <li><code>POST /patients/{id}/vital-signs</code> - Add vital signs</li>
                        <li><code>GET /patients/{id}/appointments</code> - Patient appointments</li>
                    </ul>

                    <h2 id="appointments">Appointments</h2>
                    <p>
                        Advanced appointment scheduling system with conflict detection, status management, 
                        and automated workflow support.
                    </p>

                    <div class="code-block">
                        <div class="code-header">
                            <span class="code-title">Appointment Scheduling</span>
                            <button class="copy-btn" onclick="copyCode('appointments-code')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <div class="code-content" id="appointments-code">
<span class="comment"># Schedule new appointment</span>
<span class="method">POST</span> {{ config('app.url') }}/api/appointments
<span class="keyword">Authorization:</span> Bearer {token}
<span class="keyword">Content-Type:</span> application/json

{
  <span class="string">"patient_id"</span>: <span class="keyword">1</span>,
  <span class="string">"doctor_id"</span>: <span class="keyword">1</span>,
  <span class="string">"appointment_date"</span>: <span class="string">"2024-01-15"</span>,
  <span class="string">"appointment_time"</span>: <span class="string">"10:00:00"</span>,
  <span class="string">"reason"</span>: <span class="string">"Consulta general"</span>,
  <span class="string">"status"</span>: <span class="string">"scheduled"</span>,
  <span class="string">"notes"</span>: <span class="string">"Primera consulta del paciente"</span>
}

<span class="comment"># Update appointment status</span>
<span class="method">PATCH</span> {{ config('app.url') }}/api/appointments/{id}/status
<span class="keyword">Authorization:</span> Bearer {token}

{
  <span class="string">"status"</span>: <span class="string">"completed"</span>
}

<span class="comment"># Get today's appointments</span>
<span class="method">GET</span> {{ config('app.url') }}/api/appointments/today
<span class="keyword">Authorization:</span> Bearer {token}
                        </div>
                    </div>

                    <h3>Appointment Status Values</h3>
                    <ul>
                        <li><code>scheduled</code> - Appointment scheduled</li>
                        <li><code>in_progress</code> - Currently in session</li>
                        <li><code>completed</code> - Appointment completed</li>
                        <li><code>cancelled</code> - Appointment cancelled</li>
                        <li><code>no_show</code> - Patient didn't show up</li>
                    </ul>

                    <h2 id="surgeries">Surgery Management</h2>
                    <p>
                        Comprehensive surgical procedure management with operating room scheduling, 
                        complications tracking, and outcome monitoring.
                    </p>

                    <div class="code-block">
                        <div class="code-header">
                            <span class="code-title">Surgery Operations</span>
                            <button class="copy-btn" onclick="copyCode('surgeries-code')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <div class="code-content" id="surgeries-code">
<span class="comment"># Schedule surgery</span>
<span class="method">POST</span> {{ config('app.url') }}/api/surgeries
<span class="keyword">Authorization:</span> Bearer {token}
<span class="keyword">Content-Type:</span> application/json

{
  <span class="string">"patient_id"</span>: <span class="keyword">1</span>,
  <span class="string">"doctor_id"</span>: <span class="keyword">1</span>,
  <span class="string">"surgery_type"</span>: <span class="string">"Apendicectomía"</span>,
  <span class="string">"scheduled_date"</span>: <span class="string">"2024-01-20"</span>,
  <span class="string">"scheduled_start_time"</span>: <span class="string">"08:00:00"</span>,
  <span class="string">"estimated_duration"</span>: <span class="keyword">120</span>,
  <span class="string">"operating_room"</span>: <span class="string">"OR-1"</span>,
  <span class="string">"status"</span>: <span class="string">"scheduled"</span>
}

<span class="comment"># Update surgery status with outcome</span>
<span class="method">PATCH</span> {{ config('app.url') }}/api/surgeries/{id}/status
<span class="keyword">Authorization:</span> Bearer {token}

{
  <span class="string">"status"</span>: <span class="string">"completed"</span>,
  <span class="string">"actual_start_time"</span>: <span class="string">"08:15:00"</span>,
  <span class="string">"actual_end_time"</span>: <span class="string">"09:30:00"</span>,
  <span class="string">"complications"</span>: <span class="string">"Ninguna"</span>,
  <span class="string">"outcome"</span>: <span class="string">"Exitosa"</span>
}
                        </div>
                    </div>

                    <h3>Surgery Status Values</h3>
                    <ul>
                        <li><code>scheduled</code> - Surgery scheduled</li>
                        <li><code>in_progress</code> - Surgery in progress</li>
                        <li><code>completed</code> - Surgery completed</li>
                        <li><code>cancelled</code> - Surgery cancelled</li>
                        <li><code>postponed</code> - Surgery postponed</li>
                    </ul>

                    <h2 id="medications">Pharmacy System</h2>
                    <p>
                        Complete pharmacy management with inventory control, stock alerts, 
                        expiration monitoring, and movement tracking.
                    </p>

                    <div class="code-block">
                        <div class="code-header">
                            <span class="code-title">Medication Management</span>
                            <button class="copy-btn" onclick="copyCode('medications-code')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <div class="code-content" id="medications-code">
<span class="comment"># Add new medication to inventory</span>
<span class="method">POST</span> {{ config('app.url') }}/api/medications
<span class="keyword">Authorization:</span> Bearer {token}
<span class="keyword">Content-Type:</span> application/json

{
  <span class="string">"name"</span>: <span class="string">"Paracetamol"</span>,
  <span class="string">"generic_name"</span>: <span class="string">"Acetaminofén"</span>,
  <span class="string">"brand"</span>: <span class="string">"Tylenol"</span>,
  <span class="string">"dosage"</span>: <span class="string">"500mg"</span>,
  <span class="string">"form"</span>: <span class="string">"Tableta"</span>,
  <span class="string">"manufacturer"</span>: <span class="string">"Johnson & Johnson"</span>,
  <span class="string">"stock_quantity"</span>: <span class="keyword">100</span>,
  <span class="string">"min_stock_level"</span>: <span class="keyword">20</span>,
  <span class="string">"unit_price"</span>: <span class="keyword">0.50</span>,
  <span class="string">"expiration_date"</span>: <span class="string">"2025-12-31"</span>
}

<span class="comment"># Track inventory movement</span>
<span class="method">POST</span> {{ config('app.url') }}/api/medications/{id}/movement
<span class="keyword">Authorization:</span> Bearer {token}

{
  <span class="string">"type"</span>: <span class="string">"in"</span>,
  <span class="string">"quantity"</span>: <span class="keyword">50</span>,
  <span class="string">"reason"</span>: <span class="string">"Compra"</span>,
  <span class="string">"reference"</span>: <span class="string">"PO-2024-001"</span>
}

<span class="comment"># Check low stock medications</span>
<span class="method">GET</span> {{ config('app.url') }}/api/medications/low-stock
<span class="keyword">Authorization:</span> Bearer {token}
                        </div>
                    </div>

                    <h3>Pharmacy Endpoints</h3>
                    <ul>
                        <li><code>GET /medications</code> - List all medications</li>
                        <li><code>POST /medications</code> - Add new medication</li>
                        <li><code>GET /medications/{id}</code> - Get specific medication</li>
                        <li><code>PUT /medications/{id}</code> - Update medication</li>
                        <li><code>DELETE /medications/{id}</code> - Delete medication</li>
                        <li><code>GET /medications/low-stock</code> - Low stock alerts</li>
                        <li><code>GET /medications/expiring</code> - Expiring medications</li>
                        <li><code>POST /medications/{id}/movement</code> - Track inventory</li>
                    </ul>

                    <h2 id="invoices">Billing & Invoicing</h2>
                    <p>
                        Comprehensive billing system with payment tracking, overdue management, 
                        and detailed invoice itemization.
                    </p>

                    <div class="code-block">
                        <div class="code-header">
                            <span class="code-title">Invoice Management</span>
                            <button class="copy-btn" onclick="copyCode('invoices-code')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <div class="code-content" id="invoices-code">
<span class="comment"># Create detailed invoice</span>
<span class="method">POST</span> {{ config('app.url') }}/api/invoices
<span class="keyword">Authorization:</span> Bearer {token}
<span class="keyword">Content-Type:</span> application/json

{
  <span class="string">"patient_id"</span>: <span class="keyword">1</span>,
  <span class="string">"appointment_id"</span>: <span class="keyword">1</span>,
  <span class="string">"invoice_number"</span>: <span class="string">"INV-2024-001"</span>,
  <span class="string">"issue_date"</span>: <span class="string">"2024-01-15"</span>,
  <span class="string">"due_date"</span>: <span class="string">"2024-02-15"</span>,
  <span class="string">"subtotal"</span>: <span class="keyword">100.00</span>,
  <span class="string">"tax"</span>: <span class="keyword">15.00</span>,
  <span class="string">"total"</span>: <span class="keyword">115.00</span>,
  <span class="string">"payment_status"</span>: <span class="string">"pending"</span>,
  <span class="string">"items"</span>: [
    {
      <span class="string">"description"</span>: <span class="string">"Consulta médica"</span>,
      <span class="string">"quantity"</span>: <span class="keyword">1</span>,
      <span class="string">"unit_price"</span>: <span class="keyword">100.00</span>,
      <span class="string">"total"</span>: <span class="keyword">100.00</span>
    }
  ]
}

<span class="comment"># Update payment status</span>
<span class="method">PATCH</span> {{ config('app.url') }}/api/invoices/{id}/payment-status
<span class="keyword">Authorization:</span> Bearer {token}

{
  <span class="string">"payment_status"</span>: <span class="string">"paid"</span>,
  <span class="string">"payment_date"</span>: <span class="string">"2024-01-15"</span>,
  <span class="string">"payment_method"</span>: <span class="string">"credit_card"</span>
}
                        </div>
                    </div>

                    <h3>Payment Status Values</h3>
                    <ul>
                        <li><code>pending</code> - Payment pending</li>
                        <li><code>paid</code> - Payment completed</li>
                        <li><code>overdue</code> - Payment overdue</li>
                        <li><code>cancelled</code> - Invoice cancelled</li>
                    </ul>

                    <h2 id="webhooks">Webhooks</h2>
                    <p>
                        Real-time event notifications for appointment changes, payment updates, 
                        and critical system events.
                    </p>

                    <div class="code-block">
                        <div class="code-header">
                            <span class="code-title">Webhook Configuration</span>
                            <button class="copy-btn" onclick="copyCode('webhooks-code')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <div class="code-content" id="webhooks-code">
<span class="comment"># Webhook payload example for appointment created</span>
{
  <span class="string">"event"</span>: <span class="string">"appointment.created"</span>,
  <span class="string">"timestamp"</span>: <span class="string">"2024-01-15T10:30:00Z"</span>,
  <span class="string">"data"</span>: {
    <span class="string">"appointment_id"</span>: <span class="keyword">123</span>,
    <span class="string">"patient_id"</span>: <span class="keyword">456</span>,
    <span class="string">"doctor_id"</span>: <span class="keyword">789</span>,
    <span class="string">"appointment_date"</span>: <span class="string">"2024-01-20"</span>,
    <span class="string">"appointment_time"</span>: <span class="string">"14:00:00"</span>,
    <span class="string">"status"</span>: <span class="string">"scheduled"</span>
  }
}

<span class="comment"># Available webhook events</span>
<span class="comment"># - appointment.created</span>
<span class="comment"># - appointment.updated</span>
<span class="comment"># - appointment.cancelled</span>
<span class="comment"># - patient.created</span>
<span class="comment"># - surgery.scheduled</span>
<span class="comment"># - invoice.paid</span>
<span class="comment"># - medication.low_stock</span>
                        </div>
                    </div>

                    <h2 id="rate-limiting">Rate Limiting</h2>
                    <p>
                        API rate limits are enforced to ensure fair usage and system stability. 
                        Different limits apply based on user role and endpoint type.
                    </p>

                    <div class="code-block">
                        <div class="code-header">
                            <span class="code-title">Rate Limit Headers</span>
                            <button class="copy-btn" onclick="copyCode('rate-limit-code')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <div class="code-content" id="rate-limit-code">
<span class="comment"># Rate limit information in response headers</span>
<span class="keyword">X-RateLimit-Limit:</span> 1000
<span class="keyword">X-RateLimit-Remaining:</span> 999
<span class="keyword">X-RateLimit-Reset:</span> 1640995200

<span class="comment"># Rate limits by user role</span>
<span class="comment"># Admin: 1000 requests/hour</span>
<span class="comment"># Doctor: 500 requests/hour</span>
<span class="comment"># Staff: 200 requests/hour</span>

<span class="comment"># When rate limit exceeded (HTTP 429)</span>
{
  <span class="string">"error"</span>: <span class="string">"Too Many Requests"</span>,
  <span class="string">"message"</span>: <span class="string">"Rate limit exceeded. Try again later."</span>,
  <span class="string">"retry_after"</span>: <span class="keyword">3600</span>
}
                        </div>
                    </div>

                    <h2 id="errors">Error Handling</h2>
                    <p>
                        The API uses conventional HTTP response codes and provides detailed error 
                        information in a consistent JSON format.
                    </p>

                    <div class="code-block">
                        <div class="code-header">
                            <span class="code-title">Error Response Format</span>
                            <button class="copy-btn" onclick="copyCode('errors-code')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <div class="code-content" id="errors-code">
<span class="comment"># Validation Error (HTTP 422)</span>
{
  <span class="string">"message"</span>: <span class="string">"The given data was invalid."</span>,
  <span class="string">"errors"</span>: {
    <span class="string">"email"</span>: [
      <span class="string">"The email field is required."</span>
    ],
    <span class="string">"phone"</span>: [
      <span class="string">"The phone format is invalid."</span>
    ]
  }
}

<span class="comment"># Authentication Error (HTTP 401)</span>
{
  <span class="string">"message"</span>: <span class="string">"Unauthenticated."</span>
}

<span class="comment"># Authorization Error (HTTP 403)</span>
{
  <span class="string">"message"</span>: <span class="string">"This action is unauthorized."</span>
}

<span class="comment"># Not Found Error (HTTP 404)</span>
{
  <span class="string">"message"</span>: <span class="string">"Resource not found."</span>
}

<span class="comment"># Server Error (HTTP 500)</span>
{
  <span class="string">"message"</span>: <span class="string">"Internal server error."</span>,
  <span class="string">"error_id"</span>: <span class="string">"ERR_2024_001"</span>
}
                        </div>
                    </div>

                    <h3>HTTP Status Codes</h3>
                    <ul>
                        <li><code>200</code> - OK: Request successful</li>
                        <li><code>201</code> - Created: Resource created successfully</li>
                        <li><code>400</code> - Bad Request: Invalid request data</li>
                        <li><code>401</code> - Unauthorized: Authentication required</li>
                        <li><code>403</code> - Forbidden: Insufficient permissions</li>
                        <li><code>404</code> - Not Found: Resource not found</li>
                        <li><code>422</code> - Unprocessable Entity: Validation errors</li>
                        <li><code>429</code> - Too Many Requests: Rate limit exceeded</li>
                        <li><code>500</code> - Internal Server Error: Server error</li>
                    </ul>
                </div>
            </main>

            <!-- Table of Contents -->
            <div class="toc">
                <div class="toc-title">On this page</div>
                <a href="#getting-started" class="toc-item">Getting Started</a>
                <a href="#try-it-online" class="toc-item">Try It Online</a>
                <a href="#installation" class="toc-item">Installation</a>
                <a href="#authentication" class="toc-item">Authentication</a>
                <a href="#api-modules" class="toc-item">API Modules</a>
                <a href="#quick-start" class="toc-item">Quick Start</a>
                <a href="#doctors" class="toc-item">Doctor Management</a>
                <a href="#patients" class="toc-item">Patient Registry</a>
                <a href="#appointments" class="toc-item">Appointments</a>
                <a href="#surgeries" class="toc-item">Surgery Management</a>
                <a href="#medications" class="toc-item">Pharmacy System</a>
                <a href="#invoices" class="toc-item">Billing & Invoicing</a>
                <a href="#webhooks" class="toc-item">Webhooks</a>
                <a href="#rate-limiting" class="toc-item">Rate Limiting</a>
                <a href="#errors" class="toc-item">Error Handling</a>
                <a href="#whats-next" class="toc-item">What's Next?</a>
            </div>
        </div>

        <!-- Authentication Modal -->
        <div id="authModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">API Authentication Required</h3>
                    <button class="modal-close" onclick="closeModal('authModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div id="authAlert"></div>
                
                <div class="auth-status" id="authStatus">
                    <i class="fas fa-check-circle"></i>
                    <span>Authenticated as: <span class="auth-user" id="authUser"></span></span>
                    <button class="logout-btn" onclick="logout()">Logout</button>
                </div>
                
                <form id="loginForm" onsubmit="handleLogin(event)">
                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" id="email" class="form-input" value="admin@example.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" class="form-input" value="password" required>
                    </div>
                    
                    <div style="display: flex; gap: 12px; justify-content: flex-end;">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('authModal')">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="loginBtn">
                            <span id="loginText">Login</span>
                            <span id="loginLoading" class="loading" style="display: none;"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- API Data Modal -->
        <div id="dataModal" class="modal">
            <div class="modal-content" style="max-width: 800px;">
                <div class="modal-header">
                    <h3 class="modal-title" id="dataModalTitle">API Data</h3>
                    <button class="modal-close" onclick="closeModal('dataModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div id="dataAlert"></div>
                
                <div id="dataContent">
                    <div class="json-viewer">
                        <pre id="jsonData">Loading...</pre>
                    </div>
                </div>
                
                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 16px;">
                    <button type="button" class="btn btn-secondary" onclick="copyJsonData()">
                        <i class="fas fa-copy"></i> Copy JSON
                    </button>
                    <button type="button" class="btn btn-primary" onclick="closeModal('dataModal')">
                        Close
                    </button>
                </div>
            </div>
        </div>

        <script>
            // Global variables
            let authToken = localStorage.getItem('api_token');
            let currentUser = JSON.parse(localStorage.getItem('current_user') || 'null');
            const API_BASE_URL = '{{ url("/api") }}';
            const AUTH_BASE_URL = '{{ url("/") }}';
            
            function toggleTheme() {
                const body = document.body;
                const currentTheme = body.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                body.setAttribute('data-theme', newTheme);
                
                const icon = document.querySelector('.theme-toggle i');
                icon.className = newTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
                
                localStorage.setItem('theme', newTheme);
            }

            function copyCode(elementId) {
                const codeContent = document.getElementById(elementId);
                const text = codeContent.textContent;
                
                navigator.clipboard.writeText(text).then(() => {
                    const btn = event.target.closest('.copy-btn');
                    const icon = btn.querySelector('i');
                    icon.className = 'fas fa-check';
                    
                    setTimeout(() => {
                        icon.className = 'fas fa-copy';
                    }, 2000);
                });
            }

            // Load saved theme
            document.addEventListener('DOMContentLoaded', function() {
                const savedTheme = localStorage.getItem('theme') || 'light';
                document.body.setAttribute('data-theme', savedTheme);
                
                const icon = document.querySelector('.theme-toggle i');
                icon.className = savedTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
                
                // Check if user is already authenticated with Laravel session
                checkAuthStatus();
            });

            // Sidebar navigation
            document.querySelectorAll('.sidebar-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    document.querySelectorAll('.sidebar-item').forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Smooth scrolling for anchor links
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

            // Modal functions
            function showModal(modalId) {
                document.getElementById(modalId).classList.add('show');
                document.body.style.overflow = 'hidden';
            }

            function closeModal(modalId) {
                document.getElementById(modalId).classList.remove('show');
                document.body.style.overflow = '';
            }

            // Close modal when clicking outside
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal')) {
                    e.target.classList.remove('show');
                    document.body.style.overflow = '';
                }
            });

            // Check authentication status with Laravel
            async function checkAuthStatus() {
                try {
                    const response = await fetch('{{ route("api.user") }}', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        credentials: 'same-origin'
                    });
                    
                    if (response.ok) {
                        const userData = await response.json();
                        currentUser = userData;
                        authToken = 'laravel_session'; // Use session-based auth
                        updateAuthStatus();
                    }
                } catch (error) {
                    console.log('Not authenticated');
                }
            }

            // API Data functions
            function showApiData(endpoint) {
                if (!authToken) {
                    showModal('authModal');
                    // Store the endpoint to fetch after login
                    localStorage.setItem('pending_endpoint', endpoint);
                    return;
                }
                
                fetchApiData(endpoint);
            }

            async function fetchApiData(endpoint) {
                showModal('dataModal');
                document.getElementById('dataModalTitle').textContent = `${endpoint.charAt(0).toUpperCase() + endpoint.slice(1)} API Data`;
                document.getElementById('jsonData').textContent = 'Loading...';
                
                try {
                    const response = await fetch(`{{ url("/api") }}/${endpoint}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        credentials: 'same-origin'
                    });
                    
                    if (!response.ok) {
                        if (response.status === 401) {
                            // Not authenticated, clear auth and show login
                            logout();
                            showAlert('dataAlert', 'Authentication required. Please login.', 'error');
                            return;
                        }
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    const data = await response.json();
                    displayJsonData(data);
                    
                } catch (error) {
                    console.error('Error fetching API data:', error);
                    showAlert('dataAlert', `Error: ${error.message}`, 'error');
                    document.getElementById('jsonData').textContent = `Error: ${error.message}`;
                }
            }

            function displayJsonData(data) {
                const jsonElement = document.getElementById('jsonData');
                jsonElement.textContent = JSON.stringify(data, null, 2);
                
                // Clear any previous alerts
                document.getElementById('dataAlert').innerHTML = '';
                
                // Show success message
                const count = Array.isArray(data.data) ? data.data.length : (data.data ? 1 : 0);
                showAlert('dataAlert', `Successfully loaded ${count} records from the API`, 'success');
            }

            function copyJsonData() {
                const jsonText = document.getElementById('jsonData').textContent;
                navigator.clipboard.writeText(jsonText).then(() => {
                    showAlert('dataAlert', 'JSON data copied to clipboard!', 'success');
                });
            }

            // Authentication functions using Laravel's default auth
            async function handleLogin(event) {
                event.preventDefault();
                
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                
                setLoginLoading(true);
                clearAlert('authAlert');
                
                try {
                    const response = await fetch('{{ route("login") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            email: email,
                            password: password
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.message || 'Login failed');
                    }
                    
                    // Store auth data
                    authToken = 'laravel_session';
                    currentUser = data.user || { email: email };
                    
                    updateAuthStatus();
                    showAlert('authAlert', 'Login successful!', 'success');
                    
                    // Check if there's a pending endpoint to fetch
                    const pendingEndpoint = localStorage.getItem('pending_endpoint');
                    if (pendingEndpoint) {
                        localStorage.removeItem('pending_endpoint');
                        setTimeout(() => {
                            closeModal('authModal');
                            fetchApiData(pendingEndpoint);
                        }, 1000);
                    }
                    
                } catch (error) {
                    console.error('Login error:', error);
                    showAlert('authAlert', error.message, 'error');
                } finally {
                    setLoginLoading(false);
                }
            }

            function logout() {
                // Laravel logout
                fetch('{{ route("logout") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                
                authToken = null;
                currentUser = null;
                localStorage.removeItem('pending_endpoint');
                
                updateAuthStatus();
                showAlert('authAlert', 'Logged out successfully', 'success');
            }

            function updateAuthStatus() {
                const authStatus = document.getElementById('authStatus');
                const loginForm = document.getElementById('loginForm');
                
                if (authToken && currentUser) {
                    document.getElementById('authUser').textContent = currentUser.email;
                    authStatus.classList.add('show');
                    loginForm.style.display = 'none';
                } else {
                    authStatus.classList.remove('show');
                    loginForm.style.display = 'block';
                }
            }

            function setLoginLoading(loading) {
                const loginBtn = document.getElementById('loginBtn');
                const loginText = document.getElementById('loginText');
                const loginLoading = document.getElementById('loginLoading');
                
                if (loading) {
                    loginBtn.disabled = true;
                    loginText.style.display = 'none';
                    loginLoading.style.display = 'inline-block';
                } else {
                    loginBtn.disabled = false;
                    loginText.style.display = 'inline';
                    loginLoading.style.display = 'none';
                }
            }

            function showAlert(containerId, message, type) {
                const container = document.getElementById(containerId);
                const alertClass = type === 'error' ? 'alert-error' : 'alert-success';
                
                container.innerHTML = `
                    <div class="alert ${alertClass}">
                        <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'}"></i>
                        ${message}
                    </div>
                `;
                
                // Auto-hide success messages after 3 seconds
                if (type === 'success') {
                    setTimeout(() => {
                        container.innerHTML = '';
                    }, 3000);
                }
            }

            function clearAlert(containerId) {
                document.getElementById(containerId).innerHTML = '';
            }
        </script>
    </body>
</html>
