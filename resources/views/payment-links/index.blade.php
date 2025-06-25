@extends('layouts.app')

@section('title', 'Gestión de Links de Pago')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="header-content">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-link me-3"></i>
                    Gestión de Links de Pago
                </h1>
                <p class="page-subtitle">
                    Crea y gestiona links de pago personalizados para enviar a tus pacientes
                </p>
            </div>
            <div class="header-actions">
                <x-buttons.create-payment-link />
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid mb-4">
        <div class="stat-card stat-primary">
            <div class="stat-icon">
                <i class="fas fa-link"></i>
            </div>
            <div>
                <div class="stat-number" id="total-links-count">0</div>
                <div class="stat-label">Total Links</div>
            </div>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <div class="stat-number" id="active-links-count">0</div>
                <div class="stat-label">Links Activos</div>
            </div>
        </div>
        <div class="stat-card stat-warning">
            <div class="stat-icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <div>
                <div class="stat-number" id="used-links-count">0</div>
                <div class="stat-label">Links Usados</div>
            </div>
        </div>
        <div class="stat-card stat-info">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div>
                <div class="stat-number" id="total-amount">$0</div>
                <div class="stat-label">Monto Total</div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="main-card">
        <div class="card-header-modern">
            <div class="card-title">
                <i class="fas fa-list me-2"></i>
                Mis Links de Pago
                <span class="methods-count" id="links-count">0 links</span>
            </div>
        </div>
        
        <div class="card-body-modern">
            <!-- Loading State -->
            <div id="loading-state" class="loading-state">
                <div class="spinner"></div>
                <p>Cargando links de pago...</p>
            </div>

            <!-- Links Container -->
            <div id="links-container" style="display: none;">
                <div class="empty-state">
                    <i class="fas fa-link"></i>
                    <h4>No hay links de pago</h4>
                    <p>Crea tu primer link de pago para comenzar a recibir pagos de tus pacientes.</p>
                                         <x-buttons.create-payment-link size="lg" />
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir componente modal -->
<x-modals.create-payment-link />

<!-- Modal QR -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrModalLabel">
                    <i class="fas fa-qrcode me-2"></i>Código QR del Link de Pago
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center">
                <div class="qr-container">
                    <img id="qr-image" src="" alt="Código QR" class="qr-image">
                </div>
                
                <div class="url-container mt-3">
                    <label class="form-label fw-bold">URL del link de pago:</label>
                    <div class="url-display">
                        <code id="qr-payment-url" class="url-text"></code>
                        <button type="button" class="btn btn-outline-primary btn-sm copy-url-btn" onclick="copyPaymentUrl()">
                            <i class="fas fa-copy"></i> Copiar
                        </button>
                    </div>
                </div>
                
                <div class="qr-instructions mt-3">
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Comparte este QR o URL con tu paciente para que pueda realizar el pago
                    </p>
                </div>
                
                <input type="hidden" id="qr-link-token" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cerrar
                </button>
                <button type="button" class="btn btn-primary" onclick="downloadQR()">
                    <i class="fas fa-download me-2"></i>Descargar QR
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Basic styles for the payment links page */
.page-header {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: #343a40;
    margin: 0;
    display: flex;
    align-items: center;
}

.page-title i {
    color: #007bff;
}

.page-subtitle {
    color: #6c757d;
    margin: 0.5rem 0 0 0;
    font-size: 1.1rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.stat-primary .stat-icon { background: #007bff; }
.stat-success .stat-icon { background: #28a745; }
.stat-warning .stat-icon { background: #ffc107; }
.stat-info .stat-icon { background: #17a2b8; }

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #343a40;
    line-height: 1;
}

.stat-label {
    color: #6c757d;
    font-size: 0.875rem;
    font-weight: 500;
    margin-top: 0.25rem;
}

.main-card {
    background: white;
    border-radius: 12px;
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.card-header-modern {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e9ecef;
}

.card-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #343a40;
    margin: 0;
    display: flex;
    align-items: center;
}

.methods-count {
    background: #f8f9fa;
    color: #6c757d;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
    margin-left: 1rem;
}

.card-body-modern {
    padding: 2rem;
}

.loading-state {
    text-align: center;
    padding: 3rem;
    color: #6c757d;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #f8f9fa;
    border-top: 3px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 1rem;
}

.empty-state h4 {
    color: #495057;
    margin-bottom: 0.5rem;
}

/* Links List Styles */
.links-list {
    display: grid;
    gap: 1rem;
}

.link-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1.5rem;
    transition: all 0.2s ease;
}

.link-card:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-color: #007bff;
}

.link-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.link-info h5 {
    margin: 0 0 0.5rem 0;
    color: #343a40;
    font-weight: 600;
}

.link-details {
    margin: 0;
    color: #6c757d;
    font-size: 0.875rem;
}

.amount {
    font-weight: 600;
    color: #28a745;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-completed {
    background: #d1edff;
    color: #0c5460;
}

.status-expired {
    background: #f8d7da;
    color: #721c24;
}

.status-cancelled {
    background: #f5f5f5;
    color: #6c757d;
}

.link-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.link-actions .btn {
    font-size: 0.875rem;
}

/* Pagination Styles */
.pagination-wrapper {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e9ecef;
}

.pagination {
    margin: 0;
}

.page-link {
    color: #007bff;
    border-color: #dee2e6;
    padding: 0.5rem 0.75rem;
}

.page-link:hover {
    color: #0056b3;
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
}

.page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
}

/* QR Modal Styles */
.qr-container {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 12px;
    display: inline-block;
    border: 2px solid #e9ecef;
}

.qr-image {
    max-width: 250px;
    max-height: 250px;
    width: 100%;
    height: auto;
    border-radius: 8px;
    background: white;
    padding: 10px;
}

.url-container {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    border: 1px solid #e9ecef;
}

.url-display {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
    justify-content: center;
}

.url-text {
    background: white;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    border: 1px solid #dee2e6;
    font-size: 0.875rem;
    color: #495057;
    word-break: break-all;
    flex: 1;
    min-width: 200px;
}

.copy-url-btn {
    white-space: nowrap;
}

.qr-instructions {
    background: #e7f3ff;
    border: 1px solid #b8daff;
    border-radius: 8px;
    padding: 0.75rem;
}

@media (max-width: 576px) {
    .qr-image {
        max-width: 200px;
        max-height: 200px;
    }
    
    .url-display {
        flex-direction: column;
    }
    
    .url-text {
        min-width: auto;
        width: 100%;
        text-align: center;
    }
}

@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .link-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .link-actions {
        justify-content: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadInitialData();
    setupEventListeners();
});

function setupEventListeners() {
    // Escuchar evento de link creado desde el componente
    document.addEventListener('paymentLinkCreated', function(e) {
        loadInitialData(); // Recargar estadísticas y links cuando se crea un link
    });
}

async function loadInitialData() {
    await loadStats();
    await loadLinks();
}

async function loadStats() {
    try {
        const response = await fetch('/api/payment-links/stats', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) throw new Error('Error al cargar estadísticas');
        
        const data = await response.json();
        renderStats(data.data);
    } catch (error) {
        console.error('Error:', error);
    }
}

function renderStats(stats) {
    document.getElementById('total-links-count').textContent = stats.total || 0;
    document.getElementById('active-links-count').textContent = stats.active || 0;
    document.getElementById('used-links-count').textContent = stats.used || 0;
    document.getElementById('total-amount').textContent = `$${stats.total_amount || 0}`;
}

async function loadLinks() {
    try {
        const response = await fetch('/api/payment-links', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) throw new Error('Error al cargar links');
        
        const data = await response.json();
        renderLinks(data.data);
    } catch (error) {
        console.error('Error:', error);
        showError('Error al cargar los links de pago');
    } finally {
        // Ocultar loading y mostrar contenido
        document.getElementById('loading-state').style.display = 'none';
        document.getElementById('links-container').style.display = 'block';
    }
}

function renderLinks(linksData) {
    const container = document.getElementById('links-container');
    const linksCount = linksData.data ? linksData.data.length : 0;
    
    // Actualizar contador
    document.getElementById('links-count').textContent = `${linksCount} links`;
    
    if (linksCount === 0) {
        // Mostrar estado vacío
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-link"></i>
                <h4>No hay links de pago</h4>
                <p>Crea tu primer link de pago para comenzar a recibir pagos de tus pacientes.</p>
                <x-buttons.create-payment-link size="lg" />
            </div>
        `;
    } else {
        // Mostrar lista de links
        let html = '<div class="links-list">';
        
        linksData.data.forEach(link => {
            html += renderLinkCard(link);
        });
        
        html += '</div>';
        
        // Agregar paginación si existe
        if (linksData.links) {
            html += renderPagination(linksData);
        }
        
        container.innerHTML = html;
    }
}

function renderLinkCard(link) {
    const statusClass = getStatusClass(link.payment_status);
    const statusIcon = getStatusIcon(link.payment_status);
    const expiryDate = new Date(link.expires_at).toLocaleDateString('es-ES');
    
    return `
        <div class="link-card">
            <div class="link-header">
                <div class="link-info">
                    <h5 class="link-concept">${link.concept}</h5>
                    <p class="link-details">
                        <span class="amount">$${link.amount} ${link.currency}</span>
                        ${link.patient ? `• ${link.patient.name}` : ''}
                        • Expira: ${expiryDate}
                    </p>
                </div>
                <div class="link-status">
                    <span class="status-badge ${statusClass}">
                        <i class="${statusIcon}"></i>
                        ${link.status_name || link.payment_status}
                    </span>
                </div>
            </div>
            
            <div class="link-actions">
                <button class="btn btn-sm btn-outline-primary" onclick="copyLink('${link.token}')">
                    <i class="fas fa-copy"></i> Copiar Link
                </button>
                <button class="btn btn-sm btn-outline-secondary" onclick="generateQR('${link.id}', '${link.token}')">
                    <i class="fas fa-qrcode"></i> QR
                </button>
                <button class="btn btn-sm btn-outline-info" onclick="viewLink('${link.token}')">
                    <i class="fas fa-eye"></i> Ver
                </button>
                ${link.is_active ? `
                    <button class="btn btn-sm btn-outline-warning" onclick="deactivateLink('${link.id}')">
                        <i class="fas fa-pause"></i> Desactivar
                    </button>
                ` : ''}
                <button class="btn btn-sm btn-outline-danger" onclick="deleteLink('${link.id}')">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    `;
}

function getStatusClass(status) {
    switch (status) {
        case 'pending': return 'status-pending';
        case 'completed': return 'status-completed';
        case 'expired': return 'status-expired';
        case 'cancelled': return 'status-cancelled';
        default: return 'status-pending';
    }
}

function getStatusIcon(status) {
    switch (status) {
        case 'pending': return 'fas fa-clock';
        case 'completed': return 'fas fa-check-circle';
        case 'expired': return 'fas fa-times-circle';
        case 'cancelled': return 'fas fa-ban';
        default: return 'fas fa-clock';
    }
}

function copyLink(token) {
    const url = `${window.location.origin}/pay/${token}`;
    navigator.clipboard.writeText(url).then(() => {
        showSuccess('Link copiado al portapapeles');
    }).catch(() => {
        showError('Error al copiar el link');
    });
}

function viewLink(token) {
    window.open(`/pay/${token}`, '_blank');
}

function generateQR(linkId, linkToken) {
    showQRModal(linkId, linkToken);
}

async function deactivateLink(linkId) {
    if (!confirm('¿Estás seguro de que quieres desactivar este link?')) return;
    
    try {
        const response = await fetch(`/api/payment-links/${linkId}/deactivate`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) throw new Error('Error al desactivar link');
        
        showSuccess('Link desactivado exitosamente');
        await loadInitialData(); // Recargar datos
    } catch (error) {
        console.error('Error:', error);
        showError('Error al desactivar el link');
    }
}

async function deleteLink(linkId) {
    if (!confirm('¿Estás seguro de que quieres eliminar este link? Esta acción no se puede deshacer.')) return;
    
    try {
        const response = await fetch(`/api/payment-links/${linkId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) throw new Error('Error al eliminar link');
        
        showSuccess('Link eliminado exitosamente');
        await loadInitialData(); // Recargar datos
    } catch (error) {
        console.error('Error:', error);
        showError('Error al eliminar el link');
    }
}

function showSuccess(message) {
    // Implementar notificación de éxito
    alert(message); // Temporal, puedes reemplazar con una librería de notificaciones
}

function showError(message) {
    // Implementar notificación de error
    alert(message); // Temporal, puedes reemplazar con una librería de notificaciones
}

function showQRModal(linkId, linkToken) {
    const paymentUrl = `${window.location.origin}/pay/${linkToken}`;
    
    // Actualizar contenido del modal
    document.getElementById('qr-payment-url').textContent = paymentUrl;
    document.getElementById('qr-image').src = `/pay/${linkToken}/qr`;
    document.getElementById('qr-link-token').value = linkToken;
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('qrModal'));
    modal.show();
}

function copyPaymentUrl() {
    const urlInput = document.getElementById('qr-payment-url');
    const url = urlInput.textContent;
    
    navigator.clipboard.writeText(url).then(() => {
        const button = document.querySelector('#qrModal .copy-url-btn');
        const originalText = button.innerHTML;
        
        button.innerHTML = '<i class="fas fa-check"></i> ¡Copiado!';
        button.classList.remove('btn-outline-primary');
        button.classList.add('btn-success');
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-primary');
        }, 2000);
        
        showSuccess('URL copiada al portapapeles');
    }).catch(() => {
        showError('Error al copiar la URL');
    });
}

function downloadQR() {
    const linkToken = document.getElementById('qr-link-token').value;
    const qrImageSrc = document.getElementById('qr-image').src;
    
    // Crear un link temporal para descargar
    const link = document.createElement('a');
    link.href = qrImageSrc;
    link.download = `qr-payment-${linkToken}.png`;
    link.target = '_blank';
    
    // Agregar al DOM, hacer clic y remover
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showSuccess('Descargando código QR...');
}

function renderPagination(linksData) {
    if (!linksData.links || !linksData.meta) return '';
    
    const meta = linksData.meta;
    if (meta.last_page <= 1) return '';
    
    let html = '<div class="pagination-wrapper">';
    html += '<nav aria-label="Paginación de links">';
    html += '<ul class="pagination justify-content-center">';
    
    // Botón anterior
    if (linksData.links.prev) {
        html += `<li class="page-item">
            <a class="page-link" href="#" onclick="loadLinksPage(${meta.current_page - 1}); return false;">
                <i class="fas fa-chevron-left"></i> Anterior
            </a>
        </li>`;
    } else {
        html += `<li class="page-item disabled">
            <span class="page-link"><i class="fas fa-chevron-left"></i> Anterior</span>
        </li>`;
    }
    
    // Números de página
    const startPage = Math.max(1, meta.current_page - 2);
    const endPage = Math.min(meta.last_page, meta.current_page + 2);
    
    if (startPage > 1) {
        html += `<li class="page-item">
            <a class="page-link" href="#" onclick="loadLinksPage(1); return false;">1</a>
        </li>`;
        if (startPage > 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        if (i === meta.current_page) {
            html += `<li class="page-item active">
                <span class="page-link">${i}</span>
            </li>`;
        } else {
            html += `<li class="page-item">
                <a class="page-link" href="#" onclick="loadLinksPage(${i}); return false;">${i}</a>
            </li>`;
        }
    }
    
    if (endPage < meta.last_page) {
        if (endPage < meta.last_page - 1) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        html += `<li class="page-item">
            <a class="page-link" href="#" onclick="loadLinksPage(${meta.last_page}); return false;">${meta.last_page}</a>
        </li>`;
    }
    
    // Botón siguiente
    if (linksData.links.next) {
        html += `<li class="page-item">
            <a class="page-link" href="#" onclick="loadLinksPage(${meta.current_page + 1}); return false;">
                Siguiente <i class="fas fa-chevron-right"></i>
            </a>
        </li>`;
    } else {
        html += `<li class="page-item disabled">
            <span class="page-link">Siguiente <i class="fas fa-chevron-right"></i></span>
        </li>`;
    }
    
    html += '</ul>';
    html += '</nav>';
    html += '</div>';
    
    return html;
}

async function loadLinksPage(page) {
    try {
        const response = await fetch(`/api/payment-links?page=${page}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) throw new Error('Error al cargar página');
        
        const data = await response.json();
        renderLinks(data.data);
    } catch (error) {
        console.error('Error:', error);
        showError('Error al cargar la página');
    }
}

// Función para abrir el modal (llamada desde el botón)
function openCreateModal() {
    if (typeof openCreateLinkModal === 'function') {
        openCreateLinkModal();
    } else {
        const modal = new bootstrap.Modal(document.getElementById('createLinkModal'));
        modal.show();
    }
}
</script>
@endpush
