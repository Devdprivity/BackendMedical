@extends('layouts.app')

@section('title', 'Facturas - MediCare Pro')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Facturas</h1>
        <p class="page-subtitle">Gestión de facturación y cobros médicos</p>
    </div>
    <a href="{{ route('invoices.create') }}" class="btn btn-primary">
        <i class="fas fa-file-invoice-dollar"></i>
        Nueva Factura
    </a>
</div>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Total Facturas</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="totalInvoices">-</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--success); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Pagadas</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="paidInvoices">-</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--warning); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Pendientes</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="pendingInvoices">-</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--danger); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Vencidas</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="overdueInvoices">-</div>
            </div>
        </div>
    </div>
</div>

<!-- Summary Card -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body" style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem;">
            <div style="text-align: center;">
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 0.5rem;">Ingresos del Mes</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--success);" id="monthlyRevenue">$0.00</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 0.5rem;">Por Cobrar</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--warning);" id="pendingAmount">$0.00</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 0.5rem;">Facturación Promedio</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--primary);" id="averageInvoice">$0.00</div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 1fr auto auto auto auto auto; gap: 1rem; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Buscar facturas</label>
                <div style="position: relative;">
                    <input type="text" class="form-control" placeholder="Buscar por número, paciente o concepto..." id="searchInput">
                    <i class="fas fa-search" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: var(--gray-400);"></i>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Estado</label>
                <select class="form-control" id="statusFilter">
                    <option value="">Todos los estados</option>
                    <option value="pending">Pendiente</option>
                    <option value="paid">Pagada</option>
                    <option value="overdue">Vencida</option>
                    <option value="cancelled">Cancelada</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Método de Pago</label>
                <select class="form-control" id="paymentMethodFilter">
                    <option value="">Todos los métodos</option>
                    <option value="cash">Efectivo</option>
                    <option value="card">Tarjeta</option>
                    <option value="transfer">Transferencia</option>
                    <option value="insurance">Seguro Médico</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Fecha</label>
                <input type="date" class="form-control" id="dateFilter" value="{{ date('Y-m-d') }}">
            </div>
            
            <button class="btn btn-outline" onclick="clearFilters()">
                <i class="fas fa-times"></i>
                Limpiar
            </button>
            
            <button class="btn btn-secondary" onclick="exportInvoices()">
                <i class="fas fa-download"></i>
                Exportar
            </button>
        </div>
    </div>
</div>

<!-- Invoices Table -->
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title">Registro de Facturas</h3>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <span style="font-size: 0.875rem; color: var(--gray-500);">Mostrando</span>
            <select class="form-control" style="width: auto; min-width: 80px;" id="perPageSelect">
                <option value="10">10</option>
                <option value="25" selected>25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span style="font-size: 0.875rem; color: var(--gray-500);">por página</span>
        </div>
    </div>
    <div class="card-body" style="padding: 0;">
        <div id="invoicesTableContainer">
            <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                <p>Cargando facturas...</p>
            </div>
        </div>
    </div>
</div>

<!-- Pagination -->
<div id="paginationContainer" style="margin-top: 2rem;"></div>
@endsection

@push('styles')
<style>
.invoices-table {
    width: 100%;
    border-collapse: collapse;
}

.invoices-table th,
.invoices-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--gray-200);
    vertical-align: middle;
}

.invoices-table th {
    font-weight: 600;
    color: var(--dark);
    background: var(--gray-100);
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.invoices-table tr:hover {
    background: var(--gray-100);
}

.invoice-number {
    font-family: 'Courier New', monospace;
    font-weight: 600;
    color: var(--primary);
    font-size: 1rem;
}

.patient-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.patient-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: white;
    font-size: 14px;
}

.patient-details h4 {
    margin: 0 0 0.25rem 0;
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--dark);
}

.patient-details p {
    margin: 0;
    font-size: 0.8rem;
    color: var(--gray-500);
}

.service-info {
    max-width: 200px;
}

.service-main {
    font-weight: 500;
    color: var(--dark);
    margin-bottom: 0.25rem;
}

.service-details {
    font-size: 0.8rem;
    color: var(--gray-500);
}

.amount-cell {
    text-align: right;
    font-weight: 600;
    color: var(--dark);
    font-size: 1.1rem;
}

.payment-method-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.payment-cash {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.payment-card {
    background: rgba(0, 174, 239, 0.1);
    color: var(--secondary);
}

.payment-transfer {
    background: rgba(0, 83, 155, 0.1);
    color: var(--primary);
}

.payment-insurance {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.status-pending {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.status-paid {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.status-overdue {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.status-cancelled {
    background: rgba(107, 114, 128, 0.1);
    color: var(--gray-600);
}

.due-date {
    font-size: 0.875rem;
    font-weight: 500;
}

.due-normal {
    color: var(--gray-600);
}

.due-warning {
    color: var(--warning);
}

.due-overdue {
    color: var(--danger);
}

@media (max-width: 768px) {
    .invoices-table,
    .invoices-table tbody,
    .invoices-table tr,
    .invoices-table td {
        display: block;
    }
    
    .invoices-table thead {
        display: none;
    }
    
    .invoices-table tr {
        margin-bottom: 1rem;
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        padding: 1rem;
    }
    
    .invoices-table td {
        border: none;
        padding: 0.5rem 0;
        display: flex;
        justify-content: space-between;
    }
    
    .invoices-table td:before {
        content: attr(data-label);
        font-weight: 600;
        color: var(--dark);
    }
}
</style>
@endpush

@push('scripts')
<script>
let currentPage = 1;
let currentFilters = {};

document.addEventListener('DOMContentLoaded', function() {
    loadInvoices();
    loadInvoicesStats();
    
    // Setup event listeners
    document.getElementById('searchInput').addEventListener('input', debounce(handleSearch, 300));
    document.getElementById('statusFilter').addEventListener('change', handleStatusFilter);
    document.getElementById('paymentMethodFilter').addEventListener('change', handlePaymentMethodFilter);
    document.getElementById('dateFilter').addEventListener('change', handleDateFilter);
    document.getElementById('perPageSelect').addEventListener('change', handlePerPageChange);
});

async function loadInvoices(page = 1) {
    try {
        const params = new URLSearchParams({
            page: page,
            per_page: document.getElementById('perPageSelect').value,
            ...currentFilters
        });
        
        const response = await fetch(`/api/invoices?${params}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const data = await response.json();
            // Handle Laravel pagination format
            const invoicesData = data.data?.data || data.data || [];
            renderInvoicesTable(invoicesData);
            renderPagination(data.data || data);
        } else {
            throw new Error('Error al cargar facturas');
        }
    } catch (error) {
        console.error('Error:', error);
        showErrorState();
    }
}

async function loadInvoicesStats() {
    try {
        // Simular estadísticas por ahora
        document.getElementById('totalInvoices').textContent = '168';
        document.getElementById('paidInvoices').textContent = '142';
        document.getElementById('pendingInvoices').textContent = '18';
        document.getElementById('overdueInvoices').textContent = '8';
        document.getElementById('monthlyRevenue').textContent = '$45,230.00';
        document.getElementById('pendingAmount').textContent = '$8,940.00';
        document.getElementById('averageInvoice').textContent = '$320.50';
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

function renderInvoicesTable(invoices) {
    const container = document.getElementById('invoicesTableContainer');
    
    if (invoices.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
                <i class="fas fa-file-invoice-dollar" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                <h3 style="margin-bottom: 0.5rem;">No se encontraron facturas</h3>
                <p>No hay facturas registradas o que coincidan con los filtros.</p>
                <a href="{{ route('invoices.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                    <i class="fas fa-file-invoice-dollar"></i>
                    Crear Primera Factura
                </a>
            </div>
        `;
        return;
    }
    
    const table = `
        <table class="invoices-table">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Paciente</th>
                    <th>Servicio</th>
                    <th>Monto</th>
                    <th>Método de Pago</th>
                    <th>Estado</th>
                    <th>Fecha Vencimiento</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                ${invoices.map(invoice => `
                    <tr>
                        <td data-label="Número">
                            <div class="invoice-number">#${invoice.invoice_number || 'INV-' + invoice.id}</div>
                        </td>
                        <td data-label="Paciente">
                            <div class="patient-info">
                                <div class="patient-avatar" style="background: ${getPatientColor(invoice.patient_id)};">
                                    ${getPatientInitials(invoice.patient?.name)}
                                </div>
                                <div class="patient-details">
                                    <h4>${invoice.patient?.name || 'Paciente no asignado'}</h4>
                                    <p>ID: ${invoice.patient?.id || 'N/A'}</p>
                                </div>
                            </div>
                        </td>
                        <td data-label="Servicio">
                            <div class="service-info">
                                <div class="service-main">${getFirstItemDescription(invoice.items)}</div>
                                <div class="service-details">${getItemsCount(invoice.items)} item(s)</div>
                            </div>
                        </td>
                        <td data-label="Monto" class="amount-cell">
                            $${invoice.total || '0.00'}
                        </td>
                        <td data-label="Método de Pago">
                            <span class="payment-method-badge payment-${invoice.payment_method || 'cash'}">
                                ${formatPaymentMethod(invoice.payment_method || 'cash')}
                            </span>
                        </td>
                        <td data-label="Estado">
                            <span class="payment-method-badge status-${invoice.payment_status || 'pending'}">
                                ${formatStatus(invoice.payment_status || 'pending')}
                            </span>
                        </td>
                        <td data-label="Fecha Vencimiento">
                            <div class="due-date ${getDueClass(invoice.due_date)}">
                                ${formatDueDate(invoice.due_date)}
                            </div>
                        </td>
                        <td data-label="Acciones">
                            <div class="actions-dropdown">
                                <button class="actions-btn" onclick="showInvoiceActions(${invoice.id})">
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

function renderPagination(data) {
    const container = document.getElementById('paginationContainer');
    
    if (!data.last_page || data.last_page <= 1) {
        container.innerHTML = '';
        return;
    }
    
    let pagination = '<div class="pagination">';
    
    if (data.current_page > 1) {
        pagination += `<button class="pagination-btn" onclick="loadInvoices(${data.current_page - 1})">
            <i class="fas fa-chevron-left"></i>
        </button>`;
    }
    
    const startPage = Math.max(1, data.current_page - 2);
    const endPage = Math.min(data.last_page, data.current_page + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        pagination += `<button class="pagination-btn ${i === data.current_page ? 'active' : ''}" 
            onclick="loadInvoices(${i})">${i}</button>`;
    }
    
    if (data.current_page < data.last_page) {
        pagination += `<button class="pagination-btn" onclick="loadInvoices(${data.current_page + 1})">
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
    loadInvoices(1);
}

function handleStatusFilter(event) {
    const status = event.target.value;
    if (status) {
        currentFilters.status = status;
    } else {
        delete currentFilters.status;
    }
    loadInvoices(1);
}

function handlePaymentMethodFilter(event) {
    const method = event.target.value;
    if (method) {
        currentFilters.payment_method = method;
    } else {
        delete currentFilters.payment_method;
    }
    loadInvoices(1);
}

function handleDateFilter(event) {
    const date = event.target.value;
    if (date) {
        currentFilters.date = date;
    } else {
        delete currentFilters.date;
    }
    loadInvoices(1);
}

function handlePerPageChange() {
    loadInvoices(1);
}

function clearFilters() {
    currentFilters = {};
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('paymentMethodFilter').value = '';
    document.getElementById('dateFilter').value = '{{ date("Y-m-d") }}';
    loadInvoices(1);
}

function exportInvoices() {
    alert('Función de exportación en desarrollo');
}

function showInvoiceActions(invoiceId) {
    const actions = [
        `Ver factura: /invoices/${invoiceId}`,
        `Editar información`,
        `Marcar como pagada`,
        `Enviar por email`,
        `Imprimir PDF`,
        `Registrar pago`,
        `Cancelar factura`
    ];
    alert(actions.join('\n'));
}

function showErrorState() {
    document.getElementById('invoicesTableContainer').innerHTML = `
        <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 1rem; color: var(--warning);"></i>
            <h3 style="margin-bottom: 0.5rem;">Error al cargar facturas</h3>
            <p>Hubo un problema al cargar la información. Por favor, intenta de nuevo.</p>
            <button class="btn btn-primary" onclick="loadInvoices()" style="margin-top: 1rem;">
                <i class="fas fa-redo"></i>
                Reintentar
            </button>
        </div>
    `;
}

// Utility functions
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

function formatPaymentMethod(method) {
    const methods = {
        'cash': 'Efectivo',
        'card': 'Tarjeta',
        'transfer': 'Transferencia',
        'insurance': 'Seguro Médico'
    };
    return methods[method] || method;
}

function formatStatus(status) {
    const statuses = {
        'pending': 'Pendiente',
        'paid': 'Pagada',
        'overdue': 'Vencida',
        'cancelled': 'Cancelada'
    };
    return statuses[status] || status;
}

function formatDueDate(date) {
    if (!date) return 'Sin fecha límite';
    const dueDate = new Date(date);
    return dueDate.toLocaleDateString('es-ES');
}

function getDueClass(date) {
    if (!date) return 'due-normal';
    
    const today = new Date();
    const dueDate = new Date(date);
    const daysUntilDue = Math.ceil((dueDate - today) / (1000 * 60 * 60 * 24));
    
    if (daysUntilDue < 0) return 'due-overdue';
    if (daysUntilDue <= 7) return 'due-warning';
    return 'due-normal';
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

// Utility functions for handling items
function getFirstItemDescription(items) {
    if (!items) return 'Consulta médica';
    
    try {
        const itemsArray = typeof items === 'string' ? JSON.parse(items) : items;
        if (Array.isArray(itemsArray) && itemsArray.length > 0) {
            return itemsArray[0].description || 'Servicio médico';
        }
    } catch (e) {
        console.error('Error parsing items:', e);
    }
    
    return 'Consulta médica';
}

function getItemsCount(items) {
    if (!items) return 1;
    
    try {
        const itemsArray = typeof items === 'string' ? JSON.parse(items) : items;
        if (Array.isArray(itemsArray)) {
            return itemsArray.length;
        }
    } catch (e) {
        console.error('Error parsing items:', e);
    }
    
    return 1;
}
</script>
@endpush 