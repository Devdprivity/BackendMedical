@extends('layouts.app')

@section('title', 'Citas Médicas - DrOrganiza')

@section('content')
<div class="appointments-container">
    <!-- Enhanced Header -->
    <div class="page-header-enhanced">
        <div class="header-content">
            <div class="header-text">
                <h1 class="page-title-enhanced">
                    <i class="fas fa-calendar-alt"></i>
                    Gestión de Citas Médicas
                </h1>
                <p class="page-subtitle-enhanced">Control completo de tu agenda médica y videoconsultas</p>
    </div>
            <div class="header-actions">
                @if(auth()->user()->role === 'doctor' || auth()->user()->role === 'admin')
                <button class="btn-enhanced btn-instant-video" onclick="createInstantVideoCall()" id="instantVideoCallBtn">
                    <i class="fas fa-video"></i>
                    <span>Crear Sala Instantánea</span>
                </button>
                @endif
                <a href="{{ route('appointments.create') }}" class="btn-enhanced btn-primary-enhanced">
                    <i class="fas fa-plus"></i>
                    <span>Nueva Cita</span>
    </a>
</div>
            </div>
            </div>

    <!-- Enhanced Stats Grid -->
    <div class="stats-grid-enhanced">
        <div class="stat-card today">
            <div class="stat-icon">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value" id="todayAppointments">-</div>
                <div class="stat-label">Citas Hoy</div>
        </div>
    </div>
    
        <div class="stat-card completed">
            <div class="stat-icon">
                <i class="fas fa-check-double"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value" id="completedAppointments">-</div>
                <div class="stat-label">Completadas</div>
        </div>
    </div>
    
        <div class="stat-card pending">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value" id="pendingAppointments">-</div>
                <div class="stat-label">Pendientes</div>
        </div>
    </div>
    
        <div class="stat-card cancelled">
            <div class="stat-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value" id="cancelledAppointments">-</div>
                <div class="stat-label">Canceladas</div>
        </div>
    </div>
</div>

    <!-- Compact Public Booking Section -->
@if(auth()->user()->role !== 'admin')
    <div class="booking-section-compact">
        @if(auth()->user()->booking_enabled && auth()->user()->booking_slug)
            <div class="booking-info-compact">
                <div class="booking-header">
            <i class="fas fa-link"></i>
                    <span>Link de Reservas Públicas</span>
    </div>
                <div class="booking-content">
                    <div class="booking-url-container">
                        <input type="text" id="bookingLink" readonly value="{{ url('/booking/' . auth()->user()->booking_slug) }}">
                        <button type="button" onclick="copyBookingLink()" id="copyBtn">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <div class="booking-actions">
                        <button type="button" onclick="shareBookingLink()" title="Compartir">
                        <i class="fas fa-share-alt"></i>
                    </button>
                        <button type="button" onclick="generateQRCode()" title="QR Code">
                        <i class="fas fa-qrcode"></i>
                    </button>
                        <a href="{{ url('/booking/' . auth()->user()->booking_slug) }}" target="_blank" title="Ver página">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="booking-setup-compact">
                <div class="booking-setup-content">
                    <i class="fas fa-rocket"></i>
                    <span>Activa las reservas públicas para que los pacientes puedan agendar citas online</span>
                    <button type="button" onclick="enableBooking()">Activar</button>
                </div>
            </div>
        @endif
</div>
@endif

    <!-- Quick Actions Bar -->
    <div class="quick-actions-bar">
        <div class="quick-filters">
            <div class="filter-group">
                <div class="search-enhanced">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Buscar paciente, doctor o motivo..." id="searchInput">
                </div>
            </div>
            
            <div class="filter-group">
                <select class="select-enhanced" id="dateFilter">
                    <option value="">Todas las fechas</option>
                </select>
            </div>
            
            <div class="filter-group">
                <select class="select-enhanced" id="statusFilter">
                    <option value="">Todos los estados</option>
                    <option value="scheduled">Programada</option>
                    <option value="completed">Completada</option>
                    <option value="cancelled">Cancelada</option>
                    <option value="no_show">No asistió</option>
                </select>
            </div>
            
            <div class="filter-group">
                <select class="select-enhanced" id="doctorFilter">
                    <option value="">Todos los doctores</option>
                </select>
            </div>
            </div>
            
        <div class="action-buttons">
            <button class="btn-action" onclick="clearFilters()">
                <i class="fas fa-filter-circle-xmark"></i>
                Limpiar
            </button>
            <button class="btn-action" onclick="toggleCalendarView()" id="viewToggleBtn">
                <i class="fas fa-calendar-alt"></i>
                Calendario
            </button>
            <button class="btn-action" onclick="exportAppointments()">
                <i class="fas fa-download"></i>
                Exportar
            </button>
    </div>
</div>

    <!-- Enhanced Appointments Table -->
    <div class="table-container-enhanced">
        <div class="table-header">
            <h3>Agenda de Citas</h3>
            <div class="table-controls">
                <div class="pagination-info">
                    <span>Mostrando</span>
                    <select class="per-page-select" id="perPageSelect">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                    <span>por página</span>
            </div>
        </div>
    </div>
        
        <div class="table-wrapper" id="appointmentsTableContainer">
            <div class="loading-state">
                <div class="loading-spinner"></div>
                <p>Cargando citas médicas...</p>
            </div>
        </div>
    </div>

    <!-- Enhanced Pagination -->
    <div id="paginationContainer" class="pagination-enhanced"></div>
</div>

<!-- Instant Video Call Modal -->
<div class="modal fade" id="instantVideoCallModal" tabindex="-1" aria-labelledby="instantVideoCallModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="instantVideoCallModalLabel">
                    <i class="fas fa-video text-success"></i>
                    Sala de Videollamada Instantánea
                </h5>
                <button type="button" class="btn-close" onclick="closeInstantVideoCallModal()" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <div id="instantVideoCallContent">
                    <div class="text-center" style="padding: 2rem;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Creando sala...</span>
                        </div>
                        <p class="mt-3">Creando sala de videollamada...</p>
                    </div>
                </div>
                
                <div id="instantVideoCallResult" style="display: none;">
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        ¡Sala de videollamada creada exitosamente!
                    </div>
                    
                    <div class="card border-success">
                        <div class="card-header bg-success bg-opacity-10">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-link"></i>
                                Información de la Sala
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">URL de la Videollamada:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="instantVideoCallUrl" readonly style="font-family: monospace;">
                                    <button class="btn btn-outline-primary" onclick="copyInstantVideoCallUrl()" id="copyInstantUrlBtn">
                                        <i class="fas fa-copy"></i>
                                        Copiar
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">ID de la Sala:</label>
                                <input type="text" class="form-control" id="instantVideoCallRoomId" readonly style="font-family: monospace;">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="text-muted small mb-1">
                                        <i class="fas fa-info-circle"></i>
                                        Creada: <span id="instantVideoCallCreatedAt"></span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted small mb-1">
                                        <i class="fas fa-user"></i>
                                        Por: <span id="instantVideoCallCreatedBy">{{ auth()->user()->name }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <h6>¿Cómo compartir la sala?</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <button class="btn btn-outline-success w-100" onclick="shareViaWhatsApp()">
                                    <i class="fab fa-whatsapp"></i>
                                    WhatsApp
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-outline-primary w-100" onclick="shareViaEmail()">
                                    <i class="fas fa-envelope"></i>
                                    Email
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-outline-info w-100" onclick="generateInstantQR()">
                                    <i class="fas fa-qrcode"></i>
                                    QR Code
                                </button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <button class="btn btn-warning w-100" onclick="cancelAutoNavigation()" id="cancelAutoNavBtn">
                                    <i class="fas fa-pause"></i>
                                    Detener navegación automática (compartir primero)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeInstantVideoCallModal()">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="joinInstantVideoCall()" id="joinInstantCallBtn" style="display: none;">
                    <i class="fas fa-video"></i>
                    Unirse Ahora
                </button>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
/* Modern Base Styles */
.appointments-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 0;
}

/* Enhanced Header */
.page-header-enhanced {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.page-header-enhanced::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="20" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="60" r="1.5" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="70" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
    pointer-events: none;
}

.header-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 1;
}

.page-title-enhanced {
    font-size: 2.25rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.page-title-enhanced i {
    font-size: 2rem;
    opacity: 0.9;
}

.page-subtitle-enhanced {
    font-size: 1.1rem;
    margin: 0.5rem 0 0 0;
    opacity: 0.9;
    font-weight: 400;
}

.header-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

/* Enhanced Buttons */
.btn-enhanced {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.btn-enhanced::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-enhanced:hover::before {
    left: 100%;
}

.btn-primary-enhanced {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
}

.btn-primary-enhanced:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5);
}

.btn-instant-video {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
}

.btn-instant-video:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(245, 158, 11, 0.5);
}

/* Enhanced Stats Grid */
.stats-grid-enhanced {
    max-width: 1200px;
    margin: 0 auto 1rem auto;
    padding: 0 2rem;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    min-height: 85px;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--accent-color);
    transition: width 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
}

.stat-card:hover::before {
    width: 100%;
    opacity: 0.05;
}

.stat-card.today {
    --accent-color: #3b82f6;
}

.stat-card.completed {
    --accent-color: #10b981;
}

.stat-card.pending {
    --accent-color: #f59e0b;
}

.stat-card.cancelled {
    --accent-color: #ef4444;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
    background: var(--accent-color);
    flex-shrink: 0;
}

.stat-content {
    flex: 1;
    text-align: center;
}

.stat-value {
    font-size: 2.25rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.stat-trend {
    font-size: 0.75rem;
    color: var(--accent-color);
    font-weight: 600;
}

/* Quick Actions Bar */
.quick-actions-bar {
    max-width: 1200px;
    margin: 0 auto 1.5rem auto;
    padding: 0 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
    flex-wrap: wrap;
}

.quick-filters {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    flex: 1;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    min-width: 200px;
}

.search-enhanced {
    position: relative;
    display: flex;
    align-items: center;
}

.search-enhanced i {
    position: absolute;
    left: 1rem;
    color: #9ca3af;
    z-index: 1;
}

.search-enhanced input {
    width: 100%;
    padding: 0.875rem 1rem 0.875rem 2.75rem;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 0.95rem;
    background: white;
    transition: all 0.3s ease;
}

.search-enhanced input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.select-enhanced {
    padding: 0.875rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 0.95rem;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.select-enhanced:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.action-buttons {
    display: flex;
    gap: 0.75rem;
}

.btn-action {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    color: #6b7280;
    font-weight: 500;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-action:hover {
    border-color: #667eea;
    color: #667eea;
    background: rgba(102, 126, 234, 0.05);
}

/* Enhanced Table Container */
.table-container-enhanced {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.table-header {
    padding: 1.5rem;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.table-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
}

.table-controls {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.pagination-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.per-page-select {
    padding: 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
}

.table-wrapper {
    overflow-x: auto;
}

/* Loading State */
.loading-state {
    padding: 4rem 2rem;
    text-align: center;
    color: #6b7280;
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #f3f4f6;
    border-top: 3px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Enhanced Table Styles */
.appointments-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

.appointments-table th {
    padding: 1rem 1.5rem;
    text-align: left;
    background: #f8fafc;
    color: #374151;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    border-bottom: 1px solid #e5e7eb;
}

.appointments-table td {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}

.appointments-table tr:hover {
    background: #f8fafc;
}

.appointment-time {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0.75rem;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border-radius: 12px;
    min-width: 90px;
    text-align: center;
}

.time-display {
    font-weight: 700;
    font-size: 1.1rem;
    line-height: 1;
}

.date-display {
    font-size: 0.75rem;
    opacity: 0.9;
    margin-top: 0.25rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.patient-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.patient-avatar {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: white;
    font-size: 16px;
    flex-shrink: 0;
}

.patient-details h4 {
    margin: 0 0 0.25rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
}

.patient-details p {
    margin: 0;
    font-size: 0.875rem;
    color: #6b7280;
}

.doctor-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.status-scheduled {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

.status-completed {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.status-cancelled {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.status-no-show {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

/* Video Call Buttons Enhanced */
.btn-sm {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.btn-success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border: none;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    border: none;
}

.btn-outline {
    background: transparent;
    border: 2px solid #d1d5db;
    color: #6b7280;
}

.btn-sm:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Enhanced Pagination */
.pagination-enhanced {
    max-width: 1200px;
    margin: 2rem auto 0 auto;
    padding: 0 2rem 2rem 2rem;
    display: flex;
    justify-content: center;
    gap: 0.5rem;
}

.pagination-btn {
    padding: 0.75rem 1rem;
    border: 2px solid #e5e7eb;
    background: white;
    color: #6b7280;
        border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}

.pagination-btn:hover {
    border-color: #667eea;
    color: #667eea;
}

.pagination-btn.active {
    background: #667eea;
    border-color: #667eea;
    color: white;
}

/* Native Modal Styles */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1050;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.modal.show {
    opacity: 1;
}

.modal-dialog {
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    margin: 0;
}

.modal-lg {
    max-width: 800px;
}

.modal-content {
    background: white;
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8fafc;
}

.modal-title {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #6b7280;
    cursor: pointer;
    padding: 0.25rem;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.btn-close:hover {
    background: #f3f4f6;
    color: #1f2937;
}

.modal-body {
    padding: 1.5rem;
    overflow-y: auto;
    flex: 1;
}

.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    background: #f8fafc;
}

/* Spinner styles */
.spinner-border {
    display: inline-block;
    width: 2rem;
    height: 2rem;
    vertical-align: text-bottom;
    border: 0.25em solid currentColor;
    border-right-color: transparent;
    border-radius: 50%;
    animation: spinner-border 0.75s linear infinite;
}

@keyframes spinner-border {
    to {
        transform: rotate(360deg);
    }
}

.text-primary {
    color: #667eea !important;
}

.visually-hidden {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    padding: 0 !important;
    margin: -1px !important;
    overflow: hidden !important;
    clip: rect(0, 0, 0, 0) !important;
    white-space: nowrap !important;
    border: 0 !important;
}

/* Alert styles */
.alert {
    padding: 1rem;
    margin-bottom: 1rem;
    border: 1px solid transparent;
    border-radius: 0.5rem;
}

.alert-success {
    color: #0f5132;
    background-color: #d1e7dd;
    border-color: #badbcc;
}

.d-flex {
    display: flex !important;
}

.align-items-center {
    align-items: center !important;
}

.me-2 {
    margin-right: 0.5rem !important;
}

/* Card styles */
.card {
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.5rem;
}

.card-header {
    padding: 0.75rem 1rem;
    margin-bottom: 0;
    background-color: rgba(0, 0, 0, 0.03);
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    border-top-left-radius: calc(0.5rem - 1px);
    border-top-right-radius: calc(0.5rem - 1px);
}

.card-body {
    flex: 1 1 auto;
    padding: 1rem;
}

.border-success {
    border-color: #198754 !important;
}

.bg-success {
    background-color: #198754 !important;
}

.bg-opacity-10 {
    --bs-bg-opacity: 0.1;
    background-color: rgba(var(--bs-success-rgb), var(--bs-bg-opacity)) !important;
}

.card-title {
    margin-bottom: 0.5rem;
    font-size: 1rem;
    font-weight: 500;
}

.mb-0 {
    margin-bottom: 0 !important;
}

/* Form styles */
.form-label {
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #374151;
}

.fw-bold {
    font-weight: 700 !important;
}

.form-control {
    display: block;
    width: 100%;
    padding: 0.5rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #212529;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus {
    color: #212529;
    background-color: #fff;
    border-color: #667eea;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.input-group {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    align-items: stretch;
    width: 100%;
}

.input-group > .form-control {
    position: relative;
    flex: 1 1 auto;
    width: 1%;
    min-width: 0;
    margin-bottom: 0;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.input-group .btn {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

/* Button styles */
.btn {
    display: inline-block;
    font-weight: 500;
    line-height: 1.5;
    color: #212529;
    text-align: center;
    text-decoration: none;
    vertical-align: middle;
    cursor: pointer;
    user-select: none;
    background-color: transparent;
    border: 1px solid transparent;
    padding: 0.5rem 1rem;
    font-size: 1rem;
    border-radius: 0.375rem;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.btn-outline-primary {
    color: #667eea;
    border-color: #667eea;
}

.btn-outline-primary:hover {
    color: #fff;
    background-color: #667eea;
    border-color: #667eea;
}

.btn-outline-success {
    color: #198754;
    border-color: #198754;
}

.btn-outline-success:hover {
    color: #fff;
    background-color: #198754;
    border-color: #198754;
}

.btn-outline-info {
    color: #0dcaf0;
    border-color: #0dcaf0;
}

.btn-outline-info:hover {
    color: #000;
    background-color: #0dcaf0;
    border-color: #0dcaf0;
}

.btn-secondary {
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    color: #fff;
    background-color: #5c636a;
    border-color: #565e64;
}

.btn-success {
    color: #fff;
    background-color: #198754;
    border-color: #198754;
}

.btn-success:hover {
    color: #fff;
    background-color: #157347;
    border-color: #146c43;
}

.w-100 {
    width: 100% !important;
}

/* Utility classes */
.text-muted {
    color: #6c757d !important;
}

.small {
    font-size: 0.875em;
}

.mb-1 {
    margin-bottom: 0.25rem !important;
}

.mb-3 {
    margin-bottom: 1rem !important;
}

.mt-3 {
    margin-top: 1rem !important;
}

.row {
    display: flex;
    flex-wrap: wrap;
    margin-right: -0.75rem;
    margin-left: -0.75rem;
}

.col-md-4 {
    flex: 0 0 auto;
    width: 33.33333333%;
    padding-right: 0.75rem;
    padding-left: 0.75rem;
}

.col-md-6 {
    flex: 0 0 auto;
    width: 50%;
    padding-right: 0.75rem;
    padding-left: 0.75rem;
}

@media (max-width: 768px) {
    .col-md-4,
    .col-md-6 {
        width: 100%;
        margin-bottom: 0.75rem;
    }
    
    .modal-dialog {
        width: 95%;
    }
    
    .modal-header,
    .modal-body,
    .modal-footer {
        padding: 1rem;
    }
    
    /* Responsive Design for main layout */
    .header-content {
        flex-direction: column;
        gap: 1.5rem;
        text-align: center;
    }
    
    .stats-grid-enhanced {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .booking-section-compact {
        padding: 0 1rem;
    }
    
    .booking-content {
        flex-direction: column;
        align-items: stretch;
    }
    
    .booking-url-container {
        min-width: auto;
    }
    
    .booking-actions {
    justify-content: center;
    }
    
    .booking-setup-content {
        flex-direction: column;
        text-align: center;
    }
    
    .booking-setup-content span {
        min-width: auto;
    }
    
    .quick-actions-bar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .quick-filters {
        flex-direction: column;
    }
    
    .filter-group {
        min-width: auto;
    }
    
    .action-buttons {
        justify-content: center;
    }
    
    .table-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
}

@media (max-width: 480px) {
    .appointments-container {
        padding: 0;
    }
    
    .page-header-enhanced {
        padding: 1.5rem 0;
    }
    
    .header-content {
        padding: 0 1rem;
    }
    
    .stats-grid-enhanced,
    .quick-actions-bar,
    .table-container-enhanced,
    .pagination-enhanced {
        margin-left: 0;
        margin-right: 0;
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .stats-grid-enhanced {
        grid-template-columns: 1fr;
    }
    
    .page-title-enhanced {
        font-size: 1.75rem;
    }
    
    .stat-card {
        padding: 1rem;
        min-height: 80px;
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        font-size: 1.25rem;
    }
    
    .stat-value {
        font-size: 2rem;
    }
}

/* Compact Booking Section */
.booking-section-compact {
    max-width: 1200px;
    margin: 0 auto 1.5rem auto;
    padding: 0 2rem;
}

.booking-info-compact {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
    border: 1px solid rgba(102, 126, 234, 0.2);
    border-radius: 12px;
    padding: 1rem;
}

.booking-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #667eea;
    font-size: 0.875rem;
    margin-bottom: 0.75rem;
}

.booking-content {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.booking-url-container {
    flex: 1;
    min-width: 250px;
    display: flex;
    align-items: center;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
}

.booking-url-container input {
    flex: 1;
    padding: 0.5rem 0.75rem;
    border: none;
    outline: none;
    font-family: monospace;
    font-size: 0.875rem;
    color: #374151;
}

.booking-url-container button {
    padding: 0.5rem 0.75rem;
    background: #667eea;
    color: white;
    border: none;
    cursor: pointer;
    transition: background 0.3s ease;
}

.booking-url-container button:hover {
    background: #5a67d8;
}

.booking-actions {
    display: flex;
    gap: 0.5rem;
}

.booking-actions button,
.booking-actions a {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    color: #6b7280;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.booking-actions button:hover,
.booking-actions a:hover {
    background: #f3f4f6;
    color: #667eea;
    border-color: #667eea;
}

.booking-setup-compact {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.05), rgba(217, 119, 6, 0.05));
    border: 1px solid rgba(245, 158, 11, 0.2);
    border-radius: 12px;
    padding: 1rem;
}

.booking-setup-content {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.booking-setup-content i {
    color: #f59e0b;
    font-size: 1.25rem;
}

.booking-setup-content span {
    flex: 1;
    min-width: 250px;
    color: #6b7280;
    font-size: 0.875rem;
}

.booking-setup-content button {
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.booking-setup-content button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}
</style>
@endpush

@push('scripts')
<script>
let currentPage = 1;
let currentFilters = {};

document.addEventListener('DOMContentLoaded', function() {
    loadAppointments();
    loadAppointmentsStats();
    loadDoctorsForFilter();
    
    // Setup event listeners
    document.getElementById('searchInput').addEventListener('input', debounce(handleSearch, 300));
    document.getElementById('dateFilter').addEventListener('change', handleDateFilter);
    document.getElementById('statusFilter').addEventListener('change', handleStatusFilter);
    document.getElementById('doctorFilter').addEventListener('change', handleDoctorFilter);
    document.getElementById('perPageSelect').addEventListener('change', handlePerPageChange);
    
    // Modal event listeners
    const modal = document.getElementById('instantVideoCallModal');
    if (modal) {
        // Close modal when clicking on backdrop
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeInstantVideoCallModal();
            }
        });
        
        // Close modal when pressing Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.classList.contains('show')) {
                closeInstantVideoCallModal();
            }
        });
    }
});

// Enhanced authentication and API handling
async function checkAuthentication() {
    try {
        const response = await fetch('/api/auth/check', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const authData = await response.json();
            return authData.authenticated;
        }
        return false;
    } catch (error) {
        console.error('Error checking authentication:', error);
        return false;
    }
}

async function makeAuthenticatedRequest(url, options = {}) {
    const defaultOptions = {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...options.headers
        },
        credentials: 'same-origin',
        ...options
    };
    
    try {
        const response = await fetch(url, defaultOptions);
        
        if (response.status === 401 || response.status === 419) {
            // Authentication failed - check if user is actually logged in
            const isAuthenticated = await checkAuthentication();
            if (!isAuthenticated) {
                console.warn('User not authenticated, redirecting to login');
                window.location.href = '/login';
                return null;
            }
            // If authenticated but still getting 401, it might be a CSRF issue
            console.warn('Authentication issue detected, please refresh the page');
            return null;
        }
        
        return response;
    } catch (error) {
        console.error('Request failed:', error);
        return null;
    }
}

async function loadAppointmentsStats() {
    try {
        const response = await makeAuthenticatedRequest('/api/appointments/stats');
        
        if (response && response.ok) {
            const stats = await response.json();
            document.getElementById('todayAppointments').textContent = stats.today || '0';
            document.getElementById('completedAppointments').textContent = stats.completed || '0';
            document.getElementById('pendingAppointments').textContent = stats.pending || '0';
            document.getElementById('cancelledAppointments').textContent = stats.cancelled || '0';
        } else {
            console.error('Failed to load appointment stats');
            setDefaultStats();
        }
    } catch (error) {
        console.error('Error loading stats:', error);
        setDefaultStats();
    }
}

async function loadDoctorsForFilter() {
    try {
        const response = await makeAuthenticatedRequest('/api/users/basic?role=doctor&per_page=100');
        
        if (response && response.ok) {
            const data = await response.json();
            const doctorSelect = document.getElementById('doctorFilter');
            
            // Handle the real API response format
            const doctorsData = data.data || [];
            doctorsData.forEach(doctor => {
                const option = document.createElement('option');
                option.value = doctor.id;
                option.textContent = `Dr. ${doctor.name}`;
                doctorSelect.appendChild(option);
            });
        } else {
            console.error('Failed to load doctors for filter');
            // Leave the filter empty if we can't load doctors
        }
    } catch (error) {
        console.error('Error loading doctors:', error);
        // Leave the filter empty if we can't load doctors
    }
}

async function loadAppointments(page = 1) {
    try {
        const params = new URLSearchParams({
            page: page,
            per_page: document.getElementById('perPageSelect').value,
            ...currentFilters
        });
        
        const response = await makeAuthenticatedRequest(`/api/appointments?${params}`);
        
        if (response && response.ok) {
            const data = await response.json();
            // Handle Laravel pagination format
            const appointmentsData = data.data?.data || data.data || [];
            renderAppointmentsTable(appointmentsData);
            renderPagination(data.data || data);
        } else {
            throw new Error('Error al cargar citas');
        }
    } catch (error) {
        console.error('Error:', error);
        showErrorState();
    }
}

function renderAppointmentsTable(appointments) {
    const container = document.getElementById('appointmentsTableContainer');
    
    if (!appointments || appointments.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
                <i class="fas fa-calendar-times" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                <h3 style="margin-bottom: 0.5rem;">No hay citas programadas</h3>
                <p>No se encontraron citas que coincidan con los filtros seleccionados.</p>
                <a href="{{ route('appointments.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                    <i class="fas fa-calendar-plus"></i>
                    Nueva Cita
                </a>
            </div>
        `;
        return;
    }
    
    const table = `
        <table class="appointments-table">
            <thead>
                <tr>
                    <th>Hora</th>
                    <th>Paciente</th>
                    <th>Doctor</th>
                    <th>Motivo</th>
                    <th>Estado</th>
                    <th>Duración</th>
                    <th>Videollamada</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                ${appointments.map(appointment => `
                    <tr>
                        <td data-label="Hora">
                            <div class="appointment-time">
                                <div class="time-display">${formatTime(appointment.time)}</div>
                                <div class="date-display">${formatDate(appointment.date)}</div>
                            </div>
                        </td>
                        <td data-label="Paciente">
                            <div class="patient-info">
                                <div class="patient-avatar" style="background: ${getPatientColor(appointment.patient_id)};">
                                    ${getPatientInitials(appointment.patient_name)}
                                </div>
                                <div class="patient-details">
                                    <h4>${appointment.patient_name || 'Paciente no asignado'}</h4>
                                    <p>${appointment.patient_phone || ''}</p>
                                </div>
                            </div>
                        </td>
                        <td data-label="Doctor">
                            <div class="doctor-tag">
                                <i class="fas fa-user-md"></i>
                                <span>${appointment.doctor_name || 'No asignado'}</span>
                            </div>
                        </td>
                        <td data-label="Motivo">
                            <div class="reason-cell" title="${appointment.reason || 'Sin especificar'}">
                                ${appointment.reason || 'Sin especificar'}
                            </div>
                        </td>
                        <td data-label="Estado">
                            <span class="status-badge status-${appointment.status || 'scheduled'}">
                                ${formatStatus(appointment.status || 'scheduled')}
                            </span>
                        </td>
                        <td data-label="Duración">
                            <span style="font-weight: 500;">${appointment.duration || 30} min</span>
                        </td>
                        <td data-label="Videollamada">
                            ${renderVideoCallButton(appointment)}
                        </td>
                        <td data-label="Acciones">
                            <div class="actions-dropdown">
                                <button class="actions-btn" onclick="showAppointmentActions(${appointment.id}, ${JSON.stringify(appointment).replace(/"/g, '&quot;')})">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
    
    container.innerHTML = table;
}

function renderVideoCallButton(appointment) {
    const userRole = '{{ auth()->user()->role }}';
    const userId = {{ auth()->user()->id }};
    
    // Check if this is a video consultation type
    const isVideoConsultation = appointment.type === 'video_consultation' || appointment.type === 'online';
    
    if (!isVideoConsultation) {
        return '<span style="color: var(--gray-400); font-size: 0.875rem;">N/A</span>';
    }
    
    // Check if user can access this appointment's video call
    const canAccess = userRole === 'admin' || 
                     (userRole === 'doctor' && appointment.doctor_id === userId) ||
                     (userRole === 'patient' && appointment.patient_id === userId);
    
    if (!canAccess) {
        return '<span style="color: var(--gray-400); font-size: 0.875rem;">No autorizado</span>';
    }
    
    // Check if appointment is today and within time window
    const appointmentDate = new Date(appointment.date + 'T' + appointment.time);
    const now = new Date();
    const timeDiff = (appointmentDate - now) / (1000 * 60); // difference in minutes
    const canStart = timeDiff >= -60 && timeDiff <= 15; // 15 min before to 60 min after
    
    if (appointment.video_call) {
        // Video call exists
        const status = appointment.video_call.status;
        
        if (status === 'active') {
            return `
                <button class="btn btn-success btn-sm" onclick="joinVideoCall(${appointment.video_call.id})" title="Unirse a videollamada">
                    <i class="fas fa-video"></i>
                    En curso
                </button>
            `;
        } else if (status === 'completed') {
            return `
                <span class="video-call-status completed" title="Videollamada completada">
                    <i class="fas fa-check-circle"></i>
                    Completada (${appointment.video_call.formatted_duration || '0min'})
                </span>
            `;
        } else if (status === 'pending') {
            if (userRole === 'doctor' || userRole === 'admin') {
                return canStart ? `
                    <button class="btn btn-primary btn-sm" onclick="startVideoCall(${appointment.video_call.id})" title="Iniciar videollamada">
                        <i class="fas fa-play"></i>
                        Iniciar
                    </button>
                ` : `
                    <span class="video-call-status pending" title="Videollamada programada">
                        <i class="fas fa-clock"></i>
                        Programada
                    </span>
                `;
            } else {
                return `
                    <span class="video-call-status waiting" title="Esperando que el doctor inicie">
                        <i class="fas fa-hourglass-half"></i>
                        Esperando
                    </span>
                `;
            }
        }
    } else {
        // No video call created yet
        if (userRole === 'doctor' || userRole === 'admin') {
            return `
                <button class="btn btn-outline btn-sm" onclick="createVideoCall(${appointment.id})" title="Crear videollamada">
                    <i class="fas fa-video-plus"></i>
                    Crear
                </button>
            `;
        } else {
            return `
                <span class="video-call-status not-created" title="Videollamada no creada">
                    <i class="fas fa-minus-circle"></i>
                    No creada
                </span>
            `;
        }
    }
}

function renderPagination(data) {
    const container = document.getElementById('paginationContainer');
    
    if (!data.last_page || data.last_page <= 1) {
        container.innerHTML = '';
        return;
    }
    
    let pagination = '<div class="pagination">';
    
    if (data.current_page > 1) {
        pagination += `<button class="pagination-btn" onclick="loadAppointments(${data.current_page - 1})">
            <i class="fas fa-chevron-left"></i>
        </button>`;
    }
    
    const startPage = Math.max(1, data.current_page - 2);
    const endPage = Math.min(data.last_page, data.current_page + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        pagination += `<button class="pagination-btn ${i === data.current_page ? 'active' : ''}" 
            onclick="loadAppointments(${i})">${i}</button>`;
    }
    
    if (data.current_page < data.last_page) {
        pagination += `<button class="pagination-btn" onclick="loadAppointments(${data.current_page + 1})">
            <i class="fas fa-chevron-right"></i>
        </button>`;
    }
    
    pagination += '</div>';
    container.innerHTML = pagination;
}

// Event Handlers
function handleSearch(event) {
    const searchTerm = event.target.value.trim();
    if (searchTerm) {
        currentFilters.search = searchTerm;
    } else {
        delete currentFilters.search;
    }
    loadAppointments(1);
}

function handleDateFilter(event) {
    const date = event.target.value;
    if (date) {
        currentFilters.date = date;
    } else {
        delete currentFilters.date;
    }
    loadAppointments(1);
}

function handleStatusFilter(event) {
    const status = event.target.value;
    if (status) {
        currentFilters.status = status;
    } else {
        delete currentFilters.status;
    }
    loadAppointments(1);
}

function handleDoctorFilter(event) {
    const doctorId = event.target.value;
    if (doctorId) {
        currentFilters.doctor_id = doctorId;
    } else {
        delete currentFilters.doctor_id;
    }
    loadAppointments(1);
}

function handlePerPageChange() {
    loadAppointments(1);
}

function clearFilters() {
    currentFilters = {};
    document.getElementById('searchInput').value = '';
    document.getElementById('dateFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('doctorFilter').value = '';
    loadAppointments(1);
}

function exportAppointments() {
    alert('Función de exportación en desarrollo');
}

function toggleCalendarView() {
    alert('Vista de calendario en desarrollo');
}

function showAppointmentActions(appointmentId, appointmentData) {
    const appointment = JSON.parse(appointmentData.replace(/&quot;/g, '"'));
    const userRole = '{{ auth()->user()->role }}';
    
    let actions = [];
    
    // Basic actions
    actions.push({
        icon: 'fas fa-eye',
        text: 'Ver detalles',
        action: `viewAppointment(${appointmentId})`
    });
    
    if (userRole === 'admin' || userRole === 'doctor') {
        actions.push({
            icon: 'fas fa-edit',
            text: 'Editar cita',
            action: `editAppointment(${appointmentId})`
        });
        
        if (appointment.status === 'scheduled') {
            actions.push({
                icon: 'fas fa-check',
                text: 'Marcar como completada',
                action: `completeAppointment(${appointmentId})`
            });
        }
        
        actions.push({
            icon: 'fas fa-times',
            text: 'Cancelar cita',
            action: `cancelAppointment(${appointmentId})`,
            class: 'text-danger'
        });
        
        actions.push({
            icon: 'fas fa-calendar-alt',
            text: 'Reagendar',
            action: `rescheduleAppointment(${appointmentId})`
        });
    }
    
    // Video call actions
    const isVideoConsultation = appointment.type === 'video_consultation' || appointment.type === 'online';
    if (isVideoConsultation) {
        actions.push({ separator: true });
        
        if (appointment.video_call) {
            const status = appointment.video_call.status;
            
            if (status === 'active') {
                actions.push({
                    icon: 'fas fa-video',
                    text: 'Unirse a videollamada',
                    action: `joinVideoCall(${appointment.video_call.id})`,
                    class: 'text-success'
                });
                
                if (userRole === 'doctor' || userRole === 'admin') {
                    actions.push({
                        icon: 'fas fa-phone-slash',
                        text: 'Finalizar videollamada',
                        action: `endVideoCall(${appointment.video_call.id})`,
                        class: 'text-danger'
                    });
                }
            } else if (status === 'pending' && (userRole === 'doctor' || userRole === 'admin')) {
                actions.push({
                    icon: 'fas fa-play',
                    text: 'Iniciar videollamada',
                    action: `startVideoCall(${appointment.video_call.id})`,
                    class: 'text-primary'
                });
            }
            
            if (status === 'completed') {
                actions.push({
                    icon: 'fas fa-history',
                    text: 'Ver historial de videollamada',
                    action: `viewVideoCallHistory(${appointment.video_call.id})`
                });
            }
        } else if (userRole === 'doctor' || userRole === 'admin') {
            actions.push({
                icon: 'fas fa-video-plus',
                text: 'Crear videollamada',
                action: `createVideoCall(${appointmentId})`,
                class: 'text-primary'
            });
        }
    }
    
    // Other actions
    actions.push({ separator: true });
    actions.push({
        icon: 'fas fa-bell',
        text: 'Enviar recordatorio',
        action: `sendReminder(${appointmentId})`
    });
    
    actions.push({
        icon: 'fas fa-share',
        text: 'Compartir información',
        action: `shareAppointment(${appointmentId})`
    });
    
    // Create and show modal
    showActionsModal(actions);
}

function showActionsModal(actions) {
    // Remove existing modal
    const existingModal = document.querySelector('.actions-modal');
    if (existingModal) {
        existingModal.remove();
    }
    
    const modal = document.createElement('div');
    modal.className = 'actions-modal';
    modal.innerHTML = `
        <div class="actions-modal-content">
            <div class="actions-modal-header">
                <h4>Acciones de Cita</h4>
                <button class="close-modal" onclick="closeActionsModal()">&times;</button>
            </div>
            <div class="actions-modal-body">
                ${actions.map(action => {
                    if (action.separator) {
                        return '<div class="action-separator"></div>';
                    }
                    return `
                        <button class="action-item ${action.class || ''}" onclick="${action.action}; closeActionsModal();">
                            <i class="${action.icon}"></i>
                            <span>${action.text}</span>
                        </button>
                    `;
                }).join('')}
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeActionsModal();
        }
    });
}

function closeActionsModal() {
    const modal = document.querySelector('.actions-modal');
    if (modal) {
        modal.remove();
    }
}

// Video call functions
async function createVideoCall(appointmentId) {
    try {
        showToast('Creando videollamada...', 'info');
        
        const response = await makeAuthenticatedRequest(`/api/appointments/${appointmentId}/video-call`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response && response.ok) {
            const data = await response.json();
            showToast(data.message || 'Videollamada creada exitosamente', 'success');
            loadAppointments(); // Refresh the table
        } else if (response) {
            const errorData = await response.json();
            showToast(errorData.error || 'Error al crear la videollamada', 'error');
        } else {
            showToast('Error de conexión al crear la videollamada', 'error');
        }
    } catch (error) {
        console.error('Error creating video call:', error);
        showToast('Error al crear la videollamada', 'error');
    }
}

async function startVideoCall(videoCallId) {
    try {
        showToast('Iniciando videollamada...', 'info');
        
        const response = await makeAuthenticatedRequest(`/api/video-calls/${videoCallId}/start`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response && response.ok) {
            const data = await response.json();
            showToast(data.message || 'Videollamada iniciada', 'success');
            
            // Redirect to video call room
            window.open(`/video-calls/${videoCallId}`, '_blank');
            
            // Refresh the table after a short delay
            setTimeout(() => {
                loadAppointments();
            }, 1000);
        } else if (response) {
            const errorData = await response.json();
            showToast(errorData.error || 'Error al iniciar la videollamada', 'error');
        } else {
            showToast('Error de conexión al iniciar la videollamada', 'error');
        }
    } catch (error) {
        console.error('Error starting video call:', error);
        showToast('Error al iniciar la videollamada', 'error');
    }
}

function joinVideoCall(videoCallId) {
    // Open video call room in new tab (public access)
    window.open(`/room/${videoCallId}`, '_blank');
}

async function endVideoCall(videoCallId) {
    if (!confirm('¿Estás seguro de que quieres finalizar la videollamada?')) {
        return;
    }
    
    try {
        showToast('Finalizando videollamada...', 'info');
        
        const response = await makeAuthenticatedRequest(`/api/video-calls/${videoCallId}/end`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response && response.ok) {
            const data = await response.json();
            showToast(data.message || 'Videollamada finalizada', 'success');
            loadAppointments(); // Refresh the table
        } else if (response) {
            const errorData = await response.json();
            showToast(errorData.error || 'Error al finalizar la videollamada', 'error');
        } else {
            showToast('Error de conexión al finalizar la videollamada', 'error');
        }
    } catch (error) {
        console.error('Error ending video call:', error);
        showToast('Error al finalizar la videollamada', 'error');
    }
}

function viewVideoCallHistory(videoCallId) {
    // This would show video call history/details
    alert('Función de historial de videollamadas en desarrollo');
}

// Placeholder functions for other actions
function viewAppointment(appointmentId) {
    window.location.href = `/appointments/${appointmentId}`;
}

function editAppointment(appointmentId) {
    window.location.href = `/appointments/${appointmentId}/edit`;
}

function completeAppointment(appointmentId) {
    if (confirm('¿Marcar esta cita como completada?')) {
        // Implementation for completing appointment
        alert('Función en desarrollo');
    }
}

function cancelAppointment(appointmentId) {
    if (confirm('¿Estás seguro de que quieres cancelar esta cita?')) {
        // Implementation for canceling appointment
        alert('Función en desarrollo');
    }
}

function rescheduleAppointment(appointmentId) {
    alert('Función de reagendamiento en desarrollo');
}

function sendReminder(appointmentId) {
    alert('Función de recordatorios en desarrollo');
}

function shareAppointment(appointmentId) {
    alert('Función de compartir en desarrollo');
}

function showErrorState() {
    document.getElementById('appointmentsTableContainer').innerHTML = `
        <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 1rem; color: var(--warning);"></i>
            <h3 style="margin-bottom: 0.5rem;">Error al cargar citas</h3>
            <p>Hubo un problema al cargar la información. Por favor, intenta de nuevo.</p>
            <button class="btn btn-primary" onclick="loadAppointments()" style="margin-top: 1rem;">
                <i class="fas fa-redo"></i>
                Reintentar
            </button>
        </div>
    `;
}

// Utility functions
function formatTime(time) {
    if (!time) return 'N/A';
    const [hours, minutes] = time.split(':');
    return `${hours}:${minutes}`;
}

function formatDate(date) {
    if (!date) return 'N/A';
    const dateObj = new Date(date);
    return dateObj.toLocaleDateString('es-ES', { 
        day: '2-digit', 
        month: 'short' 
    });
}

function formatStatus(status) {
    const statuses = {
        'scheduled': 'Programada',
        'completed': 'Completada',
        'cancelled': 'Cancelada',
        'no_show': 'No asistió'
    };
    return statuses[status] || status;
}

function getPatientColor(patientId) {
    const colors = ['#667eea', '#764ba2', '#00AEEF', '#FF6B6B', '#10B981', '#F59E0B'];
    return colors[(patientId || 0) % colors.length];
}

function getPatientInitials(name) {
    if (!name) return 'P';
    const parts = name.trim().split(' ');
    if (parts.length >= 2) {
        return parts[0].charAt(0).toUpperCase() + parts[1].charAt(0).toUpperCase();
    }
    return parts[0].charAt(0).toUpperCase();
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Booking link functions
function copyBookingLink() {
    const linkInput = document.getElementById('bookingLink');
    const copyBtn = document.getElementById('copyBtn');
    
    // Select and copy the text
    linkInput.select();
    linkInput.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        
        // Visual feedback
        const originalContent = copyBtn.innerHTML;
        copyBtn.innerHTML = '<i class="fas fa-check"></i> ¡Copiado!';
        copyBtn.classList.remove('btn-primary');
        copyBtn.classList.add('btn-success');
        
        setTimeout(() => {
            copyBtn.innerHTML = originalContent;
            copyBtn.classList.remove('btn-success');
            copyBtn.classList.add('btn-primary');
        }, 2000);
        
        // Show toast notification
        showToast('Link copiado al portapapeles', 'success');
        
    } catch (err) {
        console.error('Error copying text: ', err);
        showToast('Error al copiar el link', 'error');
    }
    
    // Deselect the text
    linkInput.blur();
}

function shareBookingLink() {
    const bookingUrl = document.getElementById('bookingLink').value;
    const doctorName = '{{ auth()->user()->name }}';
    const shareText = `Reserva tu cita médica con Dr. ${doctorName}`;
    
    // Check if Web Share API is supported
    if (navigator.share) {
        navigator.share({
            title: shareText,
            text: 'Haz click en el link para reservar tu cita médica online',
            url: bookingUrl
        }).then(() => {
            showToast('Link compartido exitosamente', 'success');
        }).catch((error) => {
            console.log('Error sharing:', error);
            fallbackShare(bookingUrl, shareText);
        });
    } else {
        fallbackShare(bookingUrl, shareText);
    }
}

function fallbackShare(url, text) {
    // Fallback sharing options
    const shareOptions = [
        {
            name: 'WhatsApp',
            icon: 'fab fa-whatsapp',
            color: '#25D366',
            url: `https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`
        },
        {
            name: 'Telegram',
            icon: 'fab fa-telegram',
            color: '#0088cc',
            url: `https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}`
        },
        {
            name: 'Email',
            icon: 'fas fa-envelope',
            color: '#6c757d',
            url: `mailto:?subject=${encodeURIComponent(text)}&body=${encodeURIComponent('Reserva tu cita médica en: ' + url)}`
        },
        {
            name: 'Copiar Link',
            icon: 'fas fa-copy',
            color: '#667eea',
            action: 'copy'
        }
    ];
    
    // Create modal for share options
    const modal = document.createElement('div');
    modal.className = 'share-modal';
    modal.innerHTML = `
        <div class="share-modal-content">
            <div class="share-modal-header">
                <h4>Compartir Link de Reservas</h4>
                <button class="close-modal" onclick="closeShareModal()">&times;</button>
            </div>
            <div class="share-modal-body">
                <div class="share-options">
                    ${shareOptions.map(option => `
                        <button class="share-option" 
                                style="color: ${option.color};" 
                                onclick="${option.action === 'copy' ? 'copyBookingLink(); closeShareModal();' : `window.open('${option.url}', '_blank'); closeShareModal();`}">
                            <i class="${option.icon}"></i>
                            <span>${option.name}</span>
                        </button>
                    `).join('')}
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeShareModal();
        }
    });
}

function closeShareModal() {
    const modal = document.querySelector('.share-modal');
    if (modal) {
        modal.remove();
    }
}

function generateQRCode() {
    const bookingUrl = document.getElementById('bookingLink').value;
    const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(bookingUrl)}`;
    
    // Create modal for QR code
    const modal = document.createElement('div');
    modal.className = 'qr-modal';
    modal.innerHTML = `
        <div class="qr-modal-content">
            <div class="qr-modal-header">
                <h4>Código QR - Link de Reservas</h4>
                <button class="close-modal" onclick="closeQRModal()">&times;</button>
            </div>
            <div class="qr-modal-body">
                <div style="text-align: center;">
                    <img src="${qrCodeUrl}" alt="QR Code" style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                    <p style="margin-top: 1rem; color: var(--gray-600);">
                        Los pacientes pueden escanear este código QR para acceder directamente a tu página de reservas
                    </p>
                    <div style="margin-top: 1rem; display: flex; gap: 0.5rem; justify-content: center;">
                        <button class="btn btn-primary" onclick="downloadQRCode('${qrCodeUrl}')">
                            <i class="fas fa-download"></i>
                            Descargar
                        </button>
                        <button class="btn btn-secondary" onclick="printQRCode('${qrCodeUrl}')">
                            <i class="fas fa-print"></i>
                            Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeQRModal();
        }
    });
}

function closeQRModal() {
    const modal = document.querySelector('.qr-modal');
    if (modal) {
        modal.remove();
    }
}

function downloadQRCode(qrUrl) {
    const link = document.createElement('a');
    link.href = qrUrl;
    link.download = 'qr-code-reservas.png';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    showToast('Código QR descargado', 'success');
}

function printQRCode(qrUrl) {
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Código QR - Reservas Médicas</title>
                <style>
                    body { 
                        display: flex; 
                        justify-content: center; 
                        align-items: center; 
                        min-height: 100vh; 
                        margin: 0; 
                        font-family: Arial, sans-serif;
                        text-align: center;
                    }
                    .qr-container {
                        padding: 2rem;
                    }
                    h2 { color: #333; margin-bottom: 1rem; }
                    p { color: #666; margin-top: 1rem; }
                </style>
            </head>
            <body>
                <div class="qr-container">
                    <h2>Reservas Médicas</h2>
                    <h3>Dr. {{ auth()->user()->name }}</h3>
                    <img src="${qrUrl}" alt="QR Code">
                    <p>Escanea este código para reservar tu cita médica</p>
                    <p style="font-size: 0.8em;">${document.getElementById('bookingLink').value}</p>
                </div>
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function showToast(message, type = 'info') {
    // Create toast notification
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Show toast
    setTimeout(() => toast.classList.add('show'), 100);
    
    // Hide and remove toast
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Load public booking stats for doctors
@if(auth()->user()->role !== 'admin' && auth()->user()->booking_enabled)
async function loadPublicBookingStats() {
    try {
        const response = await makeAuthenticatedRequest('/api/appointments?type=public_booking&doctor_id={{ auth()->user()->id }}');
        
        if (response && response.ok) {
            const data = await response.json();
            const appointments = data.data?.data || data.data || [];
            
            const today = new Date().toISOString().split('T')[0];
            const startOfWeek = getStartOfWeek();
            const startOfMonth = getStartOfMonth();
            
            const todayCount = appointments.filter(apt => apt.appointment_date === today).length;
            const weekCount = appointments.filter(apt => apt.appointment_date >= startOfWeek).length;
            const monthCount = appointments.filter(apt => apt.appointment_date >= startOfMonth).length;
            
            // Only update elements if they exist
            const todayElement = document.getElementById('publicBookingsToday');
            const weekElement = document.getElementById('publicBookingsWeek');
            const monthElement = document.getElementById('publicBookingsMonth');
            
            if (todayElement) todayElement.textContent = todayCount;
            if (weekElement) weekElement.textContent = weekCount;
            if (monthElement) monthElement.textContent = monthCount;
        } else {
            console.error('Failed to load public booking stats');
            // Set default values only if elements exist
            const todayElement = document.getElementById('publicBookingsToday');
            const weekElement = document.getElementById('publicBookingsWeek');
            const monthElement = document.getElementById('publicBookingsMonth');
            
            if (todayElement) todayElement.textContent = '0';
            if (weekElement) weekElement.textContent = '0';
            if (monthElement) monthElement.textContent = '0';
        }
    } catch (error) {
        console.error('Error loading public booking stats:', error);
        // Set default values only if elements exist
        const todayElement = document.getElementById('publicBookingsToday');
        const weekElement = document.getElementById('publicBookingsWeek');
        const monthElement = document.getElementById('publicBookingsMonth');
        
        if (todayElement) todayElement.textContent = '0';
        if (weekElement) weekElement.textContent = '0';
        if (monthElement) monthElement.textContent = '0';
    }
}

function getStartOfWeek() {
    const now = new Date();
    const day = now.getDay();
    const diff = now.getDate() - day + (day === 0 ? -6 : 1); // Adjust for Monday start
    const monday = new Date(now.setDate(diff));
    return monday.toISOString().split('T')[0];
}

function getStartOfMonth() {
    const now = new Date();
    return new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
}

// Load public booking stats on page load
document.addEventListener('DOMContentLoaded', function() {
    loadPublicBookingStats();
});
@endif

// Enable booking function
async function enableBooking() {
    try {
        showToast('Configurando reservas públicas...', 'info');
        
        const response = await makeAuthenticatedRequest('/api/users/{{ auth()->user()->id }}/enable-booking', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response && response.ok) {
            const data = await response.json();
            showToast('¡Reservas públicas activadas exitosamente!', 'success');
            
            // Reload the page to show the new booking link
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else if (response) {
            const errorData = await response.json();
            showToast(errorData.message || 'Error al activar las reservas públicas', 'error');
        } else {
            showToast('Error de conexión al activar las reservas públicas', 'error');
        }
    } catch (error) {
        console.error('Error enabling booking:', error);
        showToast('Error al activar las reservas públicas', 'error');
    }
}

function setDefaultStats() {
    // Set appropriate default values when API fails
    document.getElementById('todayAppointments').textContent = '0';
    document.getElementById('completedAppointments').textContent = '0';
    document.getElementById('pendingAppointments').textContent = '0';
    document.getElementById('cancelledAppointments').textContent = '0';
}

// Instant Video Call Functions
let currentInstantVideoCall = null;
let autoNavigationTimeout = null; // Variable to store the timeout

async function createInstantVideoCall() {
    // Show confirmation first
    const userChoice = confirm('¿Deseas crear una sala de videollamada instantánea?\n\n' +
                              '✓ Se creará inmediatamente\n' +
                              '✓ Podrás compartir el enlace con pacientes\n' +
                              '✓ Navegarás automáticamente a la sala en 5 segundos\n' +
                              '✓ Puedes cancelar la navegación automática\n\n' +
                              'Presiona OK para continuar o Cancelar para abortar.');
    
    if (!userChoice) {
        return; // User cancelled
    }
    
    // Show modal using native JavaScript instead of Bootstrap
    const modal = document.getElementById('instantVideoCallModal');
    modal.style.display = 'flex';
    modal.classList.add('show');
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
    
    // Reset modal content
    document.getElementById('instantVideoCallContent').style.display = 'block';
    document.getElementById('instantVideoCallResult').style.display = 'none';
    document.getElementById('joinInstantCallBtn').style.display = 'none';
    
    try {
        const response = await makeAuthenticatedRequest('/api/video-calls/instant', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response && response.ok) {
            const data = await response.json();
            currentInstantVideoCall = data.video_call;
            
            // Store the video call info for easy access
            sessionStorage.setItem('currentInstantVideoCall', JSON.stringify(currentInstantVideoCall));
            
            // Option 1: Auto-join the created room (immediate experience)
            showToast('¡Sala creada exitosamente!', 'success');
            showToast('Navegando a la videollamada en 5 segundos... Puedes cancelar la navegación automática.', 'info');
            
            // Show brief sharing info before navigating
            document.getElementById('instantVideoCallContent').style.display = 'none';
            document.getElementById('instantVideoCallResult').style.display = 'block';
            document.getElementById('joinInstantCallBtn').style.display = 'block';
            
            // Fill in the details
            document.getElementById('instantVideoCallUrl').value = window.location.origin + `/room/${currentInstantVideoCall.id}`;
            document.getElementById('instantVideoCallRoomId').value = data.video_call.room_name;
            document.getElementById('instantVideoCallCreatedAt').textContent = formatDateTime(data.video_call.created_at);
            
            // Start countdown and auto-navigate
            startAutoNavigationCountdown();
            
        } else {
            const errorData = await response.json();
            showToast(errorData.error || 'Error al crear la sala de videollamada', 'error');
            closeInstantVideoCallModal();
        }
    } catch (error) {
        console.error('Error creating instant video call:', error);
        showToast('Error al crear la sala de videollamada', 'error');
        closeInstantVideoCallModal();
    }
}

function startAutoNavigationCountdown() {
    let countdown = 5;
    const cancelBtn = document.getElementById('cancelAutoNavBtn');
    const originalText = cancelBtn.innerHTML;
    
    const updateCountdown = () => {
        cancelBtn.innerHTML = `<i class="fas fa-pause"></i> Cancelar navegación automática (${countdown}s)`;
        countdown--;
        
        if (countdown < 0) {
            // Navigate to video call
            closeInstantVideoCallModal();
            window.location.href = `/video-calls/${currentInstantVideoCall.id}`;
        } else {
            autoNavigationTimeout = setTimeout(updateCountdown, 1000);
        }
    };
    
    autoNavigationTimeout = setTimeout(updateCountdown, 1000);
}

function cancelAutoNavigation() {
    if (autoNavigationTimeout) {
        clearTimeout(autoNavigationTimeout);
        autoNavigationTimeout = null;
        
        const cancelBtn = document.getElementById('cancelAutoNavBtn');
        cancelBtn.innerHTML = '<i class="fas fa-check"></i> Navegación automática cancelada';
        cancelBtn.disabled = true;
        cancelBtn.classList.remove('btn-warning');
        cancelBtn.classList.add('btn-success');
        
        showToast('Navegación automática cancelada. Puedes compartir la sala y unirte cuando gustes.', 'info');
    }
}

// Function to close the instant video call modal
function closeInstantVideoCallModal() {
    const modal = document.getElementById('instantVideoCallModal');
    modal.style.display = 'none';
    modal.classList.remove('show');
    document.body.style.overflow = ''; // Restore scrolling
}

function copyInstantVideoCallUrl() {
    const urlInput = document.getElementById('instantVideoCallUrl');
    urlInput.select();
    document.execCommand('copy');
    
    // Update button temporarily
    const copyBtn = document.getElementById('copyInstantUrlBtn');
    const originalText = copyBtn.innerHTML;
    copyBtn.innerHTML = '<i class="fas fa-check"></i> ¡Copiado!';
    copyBtn.classList.add('btn-success');
    copyBtn.classList.remove('btn-outline-primary');
    
    setTimeout(() => {
        copyBtn.innerHTML = originalText;
        copyBtn.classList.remove('btn-success');
        copyBtn.classList.add('btn-outline-primary');
    }, 2000);
    
    showToast('URL copiada al portapapeles', 'success');
}

function shareViaWhatsApp() {
    if (!currentInstantVideoCall) return;
    
    const videoCallUrl = `${window.location.origin}/room/${currentInstantVideoCall.id}`;
    
    const message = `🎥 Te invito a una videollamada médica

📅 Fecha: ${new Date().toLocaleDateString('es-ES')}
🕒 Hora: ${new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })}
👨‍⚕️ Doctor: {{ auth()->user()->name }}

Para unirte, haz clic en el siguiente enlace:
${videoCallUrl}

ℹ️ Esta sala estará activa por las próximas 2 horas.
💡 Asegúrate de tener una buena conexión a internet y permisos de cámara/micrófono en tu navegador.
🏥 Sistema DrOrganiza - Videoconsultas seguras`;
    
    const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;
    window.open(whatsappUrl, '_blank');
    
    showToast('Compartiendo por WhatsApp...', 'info');
}

function shareViaEmail() {
    if (!currentInstantVideoCall) return;
    
    const videoCallUrl = `${window.location.origin}/room/${currentInstantVideoCall.id}`;
    
    const subject = `Invitación a videollamada médica - Dr. {{ auth()->user()->name }}`;
    const body = `Estimado/a paciente,

Te invito a una videollamada médica:

📅 Fecha: ${new Date().toLocaleDateString('es-ES')}
🕒 Hora: ${new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })}
👨‍⚕️ Doctor: {{ auth()->user()->name }}

Para unirte a la videollamada, haz clic en el siguiente enlace:
${videoCallUrl}

IMPORTANTE:
- Esta sala estará activa por las próximas 2 horas
- Asegúrate de tener una buena conexión a internet
- Tu navegador debe tener permisos para acceder a la cámara y micrófono
- Recomendamos utilizar Chrome, Firefox o Safari actualizado
- No necesitas instalar ningún software adicional
- El sistema es completamente seguro y privado

Si tienes problemas técnicos, no dudes en contactarme.

Saludos cordiales,
Dr. {{ auth()->user()->name }}
{{ auth()->user()->email }}

---
Sistema DrOrganiza - Videoconsultas Médicas Seguras`;
    
    const mailtoUrl = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
    window.open(mailtoUrl);
    
    showToast('Abriendo cliente de email...', 'info');
}

function generateInstantQR() {
    if (!currentInstantVideoCall) return;
    
    const videoCallUrl = `${window.location.origin}/room/${currentInstantVideoCall.id}`;
    
    // Show loading toast
    showToast('Generando código QR...', 'info');
    
    // Create QR modal
    const qrModal = document.createElement('div');
    qrModal.className = 'qr-modal';
    qrModal.innerHTML = `
        <div class="qr-modal-content">
            <div class="qr-modal-header">
                <h3>
                    <i class="fas fa-qrcode"></i>
                    Código QR - Videollamada Instantánea
                </h3>
                <button type="button" onclick="closeInstantQRModal()" class="btn-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="qr-modal-body">
                <div class="text-center mb-3">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(videoCallUrl)}" 
                         alt="QR Code" class="qr-code-image">
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Los pacientes pueden escanear este código QR con su teléfono para unirse directamente a la videollamada en el sistema DrOrganiza.
                </div>
                <div class="text-center">
                    <p class="small text-muted">Sala ID: ${currentInstantVideoCall.room_name}</p>
                    <p class="small text-muted">Creada: ${formatDateTime(currentInstantVideoCall.created_at)}</p>
                    <p class="small text-muted">URL: ${videoCallUrl}</p>
                </div>
            </div>
            <div class="qr-modal-footer">
                <button class="btn btn-primary" onclick="downloadInstantQRCode('${encodeURIComponent(videoCallUrl)}')">
                    <i class="fas fa-download"></i>
                    Descargar
                </button>
                <button class="btn btn-secondary" onclick="printInstantQRCode('${encodeURIComponent(videoCallUrl)}')">
                    <i class="fas fa-print"></i>
                    Imprimir
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(qrModal);
    
    // Close modal when clicking outside
    qrModal.addEventListener('click', function(e) {
        if (e.target === qrModal) {
            closeInstantQRModal();
        }
    });
}

function closeInstantQRModal() {
    const modal = document.querySelector('.qr-modal');
    if (modal) {
        modal.remove();
    }
}

function downloadInstantQRCode(videoCallUrl) {
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${videoCallUrl}`;
    const link = document.createElement('a');
    link.href = qrUrl;
    link.download = `qr-videollamada-${currentInstantVideoCall.room_name}.png`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    showToast('Código QR descargado', 'success');
}

function printInstantQRCode(videoCallUrl) {
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${videoCallUrl}`;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Videollamada Instantánea - Dr. {{ auth()->user()->name }}</title>
                <style>
                    body { 
                        display: flex; 
                        justify-content: center; 
                        align-items: center; 
                        min-height: 100vh; 
                        margin: 0; 
                        font-family: Arial, sans-serif;
                        text-align: center;
                    }
                    .qr-container {
                        padding: 2rem;
                    }
                    h2 { color: #333; margin-bottom: 1rem; }
                    p { color: #666; margin-top: 1rem; }
                </style>
            </head>
            <body>
                <div class="qr-container">
                    <h2>🎥 Videollamada Instantánea</h2>
                    <h3>Dr. {{ auth()->user()->name }}</h3>
                    <img src="${qrUrl}" alt="QR Code">
                    <p>Escanea este código para unirte a la videollamada</p>
                    <p style="font-size: 0.9em; font-weight: bold;">Sala: ${currentInstantVideoCall.room_name}</p>
                    <p style="font-size: 0.8em;">${decodeURIComponent(videoCallUrl)}</p>
                    <p style="font-size: 0.7em; color: #999;">Creada: ${formatDateTime(currentInstantVideoCall.created_at)}</p>
                    <p style="font-size: 0.7em; color: #999;">Sistema DrOrganiza - Videoconsultas Seguras</p>
                </div>
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function joinInstantVideoCall() {
    if (!currentInstantVideoCall) return;
    
    // Navigate to the video call room within the system instead of opening external window
    window.location.href = `/video-calls/${currentInstantVideoCall.id}`;
    
    showToast('Navegando a la videollamada...', 'info');
}

function formatDateTime(dateTimeString) {
    try {
        const date = new Date(dateTimeString);
        return date.toLocaleString('es-ES', {
            year: 'numeric',
            month: '2-digit', 
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (error) {
        return dateTimeString;
    }
}
</script>
@endpush 