@extends('layouts.app')

@section('title', 'Medicamentos - DrOrganiza')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Medicamentos</h1>
        <p class="page-subtitle">Inventario y gestión farmacéutica</p>
    </div>
    <a href="{{ route('medications.create') }}" class="btn btn-primary">
        <i class="fas fa-pills"></i>
        Nuevo Medicamento
    </a>
</div>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-pills"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Total Medicamentos</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="totalMedications">-</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--success); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">En Stock</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="inStockMedications">-</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--warning); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Stock Bajo</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="lowStockMedications">-</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--danger); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Por Vencer</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="expiringMedications">-</div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 1fr auto auto auto auto auto; gap: 1rem; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Buscar medicamentos</label>
                <div style="position: relative;">
                    <input type="text" class="form-control" placeholder="Buscar por nombre, principio activo o código..." id="searchInput">
                    <i class="fas fa-search" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: var(--gray-400);"></i>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Categoría</label>
                <select class="form-control" id="categoryFilter">
                    <option value="">Todas las categorías</option>
                    <option value="analgesico">Analgésico</option>
                    <option value="antibiotico">Antibiótico</option>
                    <option value="antiinflamatorio">Antiinflamatorio</option>
                    <option value="antihipertensivo">Antihipertensivo</option>
                    <option value="vitamina">Vitamina</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Estado</label>
                <select class="form-control" id="statusFilter">
                    <option value="">Todos los estados</option>
                    <option value="available">Disponible</option>
                    <option value="low_stock">Stock bajo</option>
                    <option value="out_of_stock">Agotado</option>
                    <option value="expired">Vencido</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Forma</label>
                <select class="form-control" id="formFilter">
                    <option value="">Todas las formas</option>
                    <option value="tableta">Tableta</option>
                    <option value="capsula">Cápsula</option>
                    <option value="jarabe">Jarabe</option>
                    <option value="inyeccion">Inyección</option>
                    <option value="crema">Crema</option>
                    <option value="suspension">Suspensión</option>
                </select>
            </div>
            
            <button class="btn btn-outline" onclick="clearFilters()">
                <i class="fas fa-times"></i>
                Limpiar
            </button>
            
            <button class="btn btn-secondary" onclick="exportMedications()">
                <i class="fas fa-download"></i>
                Exportar
            </button>
        </div>
    </div>
</div>

<!-- Medications Table -->
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title">Inventario Farmacéutico</h3>
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
        <div id="medicationsTableContainer">
            <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                <p>Cargando medicamentos...</p>
            </div>
        </div>
    </div>
</div>

<!-- Pagination -->
<div id="paginationContainer" style="margin-top: 2rem;"></div>
@endsection

@push('styles')
<style>
.medications-table {
    width: 100%;
    border-collapse: collapse;
}

.medications-table th,
.medications-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--gray-200);
    vertical-align: middle;
}

.medications-table th {
    font-weight: 600;
    color: var(--dark);
    background: var(--gray-100);
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.medications-table tr:hover {
    background: var(--gray-100);
}

.medication-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.medication-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
}

.medication-details h4 {
    margin: 0 0 0.25rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: var(--dark);
}

.medication-details p {
    margin: 0;
    font-size: 0.875rem;
    color: var(--gray-500);
}

.category-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: rgba(0, 174, 239, 0.1);
    color: var(--secondary);
}

.form-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 500;
    background: rgba(0, 83, 155, 0.1);
    color: var(--primary);
}

.stock-info {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.stock-quantity {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--dark);
}

.stock-unit {
    font-size: 0.75rem;
    color: var(--gray-500);
    text-transform: uppercase;
}

.status-available {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.status-low-stock {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.status-out-of-stock {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.status-expired {
    background: rgba(107, 114, 128, 0.1);
    color: var(--gray-600);
}

.expiry-date {
    font-size: 0.875rem;
    font-weight: 500;
}

.expiry-warning {
    color: var(--warning);
}

.expiry-danger {
    color: var(--danger);
}

.expiry-normal {
    color: var(--gray-600);
}

.price-cell {
    text-align: right;
    font-weight: 500;
    color: var(--primary);
}

@media (max-width: 768px) {
    .medications-table,
    .medications-table tbody,
    .medications-table tr,
    .medications-table td {
        display: block;
    }
    
    .medications-table thead {
        display: none;
    }
    
    .medications-table tr {
        margin-bottom: 1rem;
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        padding: 1rem;
    }
    
    .medications-table td {
        border: none;
        padding: 0.5rem 0;
        display: flex;
        justify-content: space-between;
    }
    
    .medications-table td:before {
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
    loadMedications();
    loadMedicationsStats();
    
    // Setup event listeners
    document.getElementById('searchInput').addEventListener('input', debounce(handleSearch, 300));
    document.getElementById('categoryFilter').addEventListener('change', handleCategoryFilter);
    document.getElementById('statusFilter').addEventListener('change', handleStatusFilter);
    document.getElementById('formFilter').addEventListener('change', handleFormFilter);
    document.getElementById('perPageSelect').addEventListener('change', handlePerPageChange);
});

async function loadMedications(page = 1) {
    try {
        const params = new URLSearchParams({
            page: page,
            per_page: document.getElementById('perPageSelect').value,
            ...currentFilters
        });
        
        const response = await fetch(`/api/medications?${params}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const data = await response.json();
            // Handle Laravel pagination format
            const medicationsData = data.data?.data || data.data || [];
            renderMedicationsTable(medicationsData);
            renderPagination(data.data || data);
        } else {
            throw new Error('Error al cargar medicamentos');
        }
    } catch (error) {
        console.error('Error:', error);
        showErrorState();
    }
}

async function loadMedicationsStats() {
    try {
        const response = await fetch('/api/medications/stats', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const stats = await response.json();
            document.getElementById('totalMedications').textContent = stats.total || '0';
            document.getElementById('inStockMedications').textContent = stats.in_stock || '0';
            document.getElementById('lowStockMedications').textContent = stats.low_stock || '0';
            document.getElementById('expiringMedications').textContent = stats.expiring_soon || '0';
        } else {
            // Fallback to default values
            document.getElementById('totalMedications').textContent = '0';
            document.getElementById('inStockMedications').textContent = '0';
            document.getElementById('lowStockMedications').textContent = '0';
            document.getElementById('expiringMedications').textContent = '0';
        }
    } catch (error) {
        console.error('Error loading stats:', error);
        // Fallback to default values
        document.getElementById('totalMedications').textContent = '0';
        document.getElementById('inStockMedications').textContent = '0';
        document.getElementById('lowStockMedications').textContent = '0';
        document.getElementById('expiringMedications').textContent = '0';
    }
}

function renderMedicationsTable(medications) {
    const container = document.getElementById('medicationsTableContainer');
    
    if (medications.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
                <i class="fas fa-pills" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                <h3 style="margin-bottom: 0.5rem;">No se encontraron medicamentos</h3>
                <p>No hay medicamentos registrados o que coincidan con los filtros.</p>
                <a href="{{ route('medications.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                    <i class="fas fa-pills"></i>
                    Registrar Primer Medicamento
                </a>
            </div>
        `;
        return;
    }
    
    const table = `
        <table class="medications-table">
            <thead>
                <tr>
                    <th>Medicamento</th>
                    <th>Categoría</th>
                    <th>Presentación</th>
                    <th>Stock</th>
                    <th>Estado</th>
                    <th>Fecha Vencimiento</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                ${medications.map(medication => `
                    <tr>
                        <td data-label="Medicamento">
                            <div class="medication-info">
                                <div class="medication-icon" style="background: ${getMedicationColor(medication.category)};">
                                    <i class="${getMedicationIcon(medication.category)}"></i>
                                </div>
                                <div class="medication-details">
                                    <h4>${medication.commercial_name || 'Medicamento sin nombre'}</h4>
                                    <p>${medication.generic_name || 'Principio activo no especificado'} - ${medication.concentration || ''}</p>
                                </div>
                            </div>
                        </td>
                        <td data-label="Categoría">
                            <span class="category-badge">${formatCategory(medication.category) || 'Sin categoría'}</span>
                        </td>
                        <td data-label="Presentación">
                            <span class="form-badge">${formatPresentation(medication.presentation) || 'Sin presentación'}</span>
                        </td>
                        <td data-label="Stock">
                            <div class="stock-info">
                                <div class="stock-quantity">${medication.current_stock || 0}</div>
                                <div class="stock-unit">unidades</div>
                            </div>
                        </td>
                        <td data-label="Estado">
                            <span class="category-badge status-${getStockStatus(medication)}">
                                ${formatStockStatus(getStockStatus(medication))}
                            </span>
                        </td>
                        <td data-label="Fecha Vencimiento">
                            <div class="expiry-date ${getExpiryClass(medication.expiration_date)}">
                                ${formatExpiryDate(medication.expiration_date)}
                            </div>
                        </td>
                        <td data-label="Precio" class="price-cell">
                            $${medication.sale_price || '0.00'}
                        </td>
                        <td data-label="Acciones">
                            <div class="actions-dropdown">
                                <button class="actions-btn" onclick="showMedicationActions(${medication.id})">
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
        pagination += `<button class="pagination-btn" onclick="loadMedications(${data.current_page - 1})">
            <i class="fas fa-chevron-left"></i>
        </button>`;
    }
    
    const startPage = Math.max(1, data.current_page - 2);
    const endPage = Math.min(data.last_page, data.current_page + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        pagination += `<button class="pagination-btn ${i === data.current_page ? 'active' : ''}" 
            onclick="loadMedications(${i})">${i}</button>`;
    }
    
    if (data.current_page < data.last_page) {
        pagination += `<button class="pagination-btn" onclick="loadMedications(${data.current_page + 1})">
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
    loadMedications(1);
}

function handleCategoryFilter(event) {
    const category = event.target.value;
    if (category) {
        currentFilters.category = category;
    } else {
        delete currentFilters.category;
    }
    loadMedications(1);
}

function handleStatusFilter(event) {
    const status = event.target.value;
    if (status) {
        currentFilters.status = status;
    } else {
        delete currentFilters.status;
    }
    loadMedications(1);
}

function handleFormFilter(event) {
    const form = event.target.value;
    if (form) {
        currentFilters.form = form;
    } else {
        delete currentFilters.form;
    }
    loadMedications(1);
}

function handlePerPageChange() {
    loadMedications(1);
}

function clearFilters() {
    currentFilters = {};
    document.getElementById('searchInput').value = '';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('formFilter').value = '';
    loadMedications(1);
}

function exportMedications() {
    alert('Función de exportación en desarrollo');
}

function showMedicationActions(medicationId) {
    const actions = [
        `Ver detalles: /medications/${medicationId}`,
        `Editar información`,
        `Actualizar stock`,
        `Ver historial`,
        `Generar reporte`,
        `Configurar alertas`
    ];
    alert(actions.join('\n'));
}

function showErrorState() {
    document.getElementById('medicationsTableContainer').innerHTML = `
        <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 1rem; color: var(--warning);"></i>
            <h3 style="margin-bottom: 0.5rem;">Error al cargar medicamentos</h3>
            <p>Hubo un problema al cargar la información. Por favor, intenta de nuevo.</p>
            <button class="btn btn-primary" onclick="loadMedications()" style="margin-top: 1rem;">
                <i class="fas fa-redo"></i>
                Reintentar
            </button>
        </div>
    `;
}

// Utility functions
function getMedicationColor(category) {
    const colors = {
        'analgesico': '#FF6B6B',
        'antibiotico': '#4ECDC4',
        'antiinflamatorio': '#45B7D1',
        'antihipertensivo': '#96CEB4',
        'vitamina': '#FFEAA7',
        'otro': '#DDA0DD'
    };
    return colors[category] || '#667eea';
}

function getMedicationIcon(category) {
    const icons = {
        'analgesico': 'fas fa-tablets',
        'antibiotico': 'fas fa-pills',
        'antiinflamatorio': 'fas fa-capsules',
        'antihipertensivo': 'fas fa-heartbeat',
        'vitamina': 'fas fa-leaf',
        'otro': 'fas fa-prescription-bottle-alt'
    };
    return icons[category] || 'fas fa-pills';
}

function formatCategory(category) {
    const categories = {
        'analgesico': 'Analgésico',
        'antibiotico': 'Antibiótico',
        'antiinflamatorio': 'Antiinflamatorio',
        'antihipertensivo': 'Antihipertensivo',
        'vitamina': 'Vitamina',
        'otro': 'Otro'
    };
    return categories[category] || category;
}

function formatPresentation(presentation) {
    const presentations = {
        'Tableta': 'Tableta',
        'Cápsula': 'Cápsula',
        'Jarabe': 'Jarabe',
        'Inyección': 'Inyección',
        'Crema': 'Crema',
        'Suspensión': 'Suspensión',
        'Solución': 'Solución',
        'Gotas': 'Gotas'
    };
    return presentations[presentation] || presentation;
}

function getStockStatus(medication) {
    const stock = medication.current_stock || 0;
    const minStock = medication.min_stock || 10;
    
    if (stock === 0) return 'out-of-stock';
    if (stock <= minStock) return 'low-stock';
    if (medication.expiration_date && isExpired(medication.expiration_date)) return 'expired';
    return 'available';
}

function formatStockStatus(status) {
    const statuses = {
        'available': 'Disponible',
        'low-stock': 'Stock bajo',
        'out-of-stock': 'Agotado',
        'expired': 'Vencido'
    };
    return statuses[status] || status;
}

function formatExpiryDate(date) {
    if (!date) return 'Sin fecha';
    const expiryDate = new Date(date);
    return expiryDate.toLocaleDateString('es-ES');
}

function getExpiryClass(date) {
    if (!date) return 'expiry-normal';
    
    const today = new Date();
    const expiryDate = new Date(date);
    const daysUntilExpiry = Math.ceil((expiryDate - today) / (1000 * 60 * 60 * 24));
    
    if (daysUntilExpiry < 0) return 'expiry-danger';
    if (daysUntilExpiry <= 30) return 'expiry-warning';
    return 'expiry-normal';
}

function isExpired(date) {
    if (!date) return false;
    const today = new Date();
    const expiryDate = new Date(date);
    return expiryDate < today;
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
</script>
@endpush 