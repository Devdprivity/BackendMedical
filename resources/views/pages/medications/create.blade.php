@extends('layouts.app')

@section('title', 'Nuevo Medicamento - DrOrganiza')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Nuevo Medicamento</h1>
        <p class="page-subtitle">Registrar un nuevo medicamento en el inventario</p>
    </div>
</div>

<form id="medicationForm" class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Medicamento</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Basic Information -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name" class="form-label required">Nombre del Medicamento</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                    <small class="form-text">Nombre comercial o genérico del medicamento</small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="generic_name" class="form-label">Nombre Genérico</label>
                    <input type="text" id="generic_name" name="generic_name" class="form-control">
                    <small class="form-text">Nombre genérico del principio activo</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="dosage" class="form-label required">Dosificación</label>
                    <input type="text" id="dosage" name="dosage" class="form-control" required placeholder="ej: 500mg">
                    <small class="form-text">Concentración del medicamento</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="form" class="form-label required">Forma Farmacéutica</label>
                    <select id="form" name="form" class="form-control" required>
                        <option value="">Seleccionar forma...</option>
                        <option value="tablet">Tableta</option>
                        <option value="capsule">Cápsula</option>
                        <option value="syrup">Jarabe</option>
                        <option value="injection">Inyección</option>
                        <option value="cream">Crema</option>
                        <option value="ointment">Ungüento</option>
                        <option value="drops">Gotas</option>
                        <option value="spray">Spray</option>
                        <option value="patch">Parche</option>
                        <option value="suppository">Supositorio</option>
                        <option value="powder">Polvo</option>
                        <option value="solution">Solución</option>
                    </select>
                    <small class="form-text">Presentación del medicamento</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="category" class="form-label">Categoría</label>
                    <select id="category" name="category" class="form-control">
                        <option value="">Seleccionar categoría...</option>
                        <option value="analgesic">Analgésico</option>
                        <option value="antibiotic">Antibiótico</option>
                        <option value="antiviral">Antiviral</option>
                        <option value="anti_inflammatory">Antiinflamatorio</option>
                        <option value="cardiovascular">Cardiovascular</option>
                        <option value="respiratory">Respiratorio</option>
                        <option value="gastrointestinal">Gastrointestinal</option>
                        <option value="neurological">Neurológico</option>
                        <option value="endocrine">Endocrino</option>
                        <option value="dermatological">Dermatológico</option>
                        <option value="ophthalmological">Oftalmológico</option>
                        <option value="vitamin">Vitamina/Suplemento</option>
                        <option value="vaccine">Vacuna</option>
                        <option value="other">Otro</option>
                    </select>
                    <small class="form-text">Categoría terapéutica</small>
                </div>
            </div>
        </div>

        <!-- Inventory Information -->
        <h4 style="margin-top: 2rem; margin-bottom: 1rem; color: var(--primary);">Información de Inventario</h4>
        
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="current_stock" class="form-label required">Stock Actual</label>
                    <input type="number" id="current_stock" name="current_stock" class="form-control" required min="0">
                    <small class="form-text">Cantidad disponible actualmente</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="minimum_stock" class="form-label">Stock Mínimo</label>
                    <input type="number" id="minimum_stock" name="minimum_stock" class="form-control" min="0" value="10">
                    <small class="form-text">Nivel mínimo de alerta</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="unit_price" class="form-label">Precio Unitario</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--gray-500);">$</span>
                        <input type="number" id="unit_price" name="unit_price" class="form-control" step="0.01" min="0" style="padding-left: 25px;">
                    </div>
                    <small class="form-text">Precio por unidad</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="unit" class="form-label">Unidad de Medida</label>
                    <select id="unit" name="unit" class="form-control">
                        <option value="piece" selected>Pieza</option>
                        <option value="box">Caja</option>
                        <option value="bottle">Frasco</option>
                        <option value="vial">Vial</option>
                        <option value="ml">Mililitro</option>
                        <option value="mg">Miligramo</option>
                        <option value="g">Gramo</option>
                        <option value="kg">Kilogramo</option>
                    </select>
                    <small class="form-text">Unidad de medida del stock</small>
                </div>
            </div>
        </div>

        <!-- Expiration and Batch -->
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="batch_number" class="form-label">Número de Lote</label>
                    <input type="text" id="batch_number" name="batch_number" class="form-control">
                    <small class="form-text">Número de lote del fabricante</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="expiry_date" class="form-label">Fecha de Vencimiento</label>
                    <input type="date" id="expiry_date" name="expiry_date" class="form-control" min="{{ date('Y-m-d') }}">
                    <small class="form-text">Fecha de vencimiento del medicamento</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="manufacturer" class="form-label">Fabricante</label>
                    <input type="text" id="manufacturer" name="manufacturer" class="form-control">
                    <small class="form-text">Laboratorio fabricante</small>
                </div>
            </div>
        </div>

        <!-- Clinical Information -->
        <h4 style="margin-top: 2rem; margin-bottom: 1rem; color: var(--primary);">Información Clínica</h4>
        
        <div class="form-group">
            <label for="indications" class="form-label">Indicaciones</label>
            <textarea id="indications" name="indications" class="form-control" rows="2" placeholder="Para qué se usa este medicamento..."></textarea>
            <small class="form-text">Indicaciones terapéuticas principales</small>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="contraindications" class="form-label">Contraindicaciones</label>
                    <textarea id="contraindications" name="contraindications" class="form-control" rows="2" placeholder="Cuándo NO debe usarse..."></textarea>
                    <small class="form-text">Situaciones donde está contraindicado</small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="side_effects" class="form-label">Efectos Secundarios</label>
                    <textarea id="side_effects" name="side_effects" class="form-control" rows="2" placeholder="Efectos adversos posibles..."></textarea>
                    <small class="form-text">Efectos secundarios conocidos</small>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="dosage_instructions" class="form-label">Instrucciones de Dosificación</label>
            <textarea id="dosage_instructions" name="dosage_instructions" class="form-control" rows="2" placeholder="Cómo administrar el medicamento..."></textarea>
            <small class="form-text">Instrucciones de uso y dosificación</small>
        </div>

        <!-- Storage and Status -->
        <h4 style="margin-top: 2rem; margin-bottom: 1rem; color: var(--primary);">Almacenamiento y Estado</h4>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="storage_conditions" class="form-label">Condiciones de Almacenamiento</label>
                    <select id="storage_conditions" name="storage_conditions" class="form-control">
                        <option value="room_temperature" selected>Temperatura ambiente</option>
                        <option value="refrigerated">Refrigerado (2-8°C)</option>
                        <option value="frozen">Congelado (-20°C)</option>
                        <option value="controlled_temperature">Temperatura controlada</option>
                        <option value="dry_place">Lugar seco</option>
                        <option value="protected_from_light">Protegido de la luz</option>
                    </select>
                    <small class="form-text">Condiciones requeridas de almacenamiento</small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="status" class="form-label">Estado</label>
                    <select id="status" name="status" class="form-control">
                        <option value="available" selected>Disponible</option>
                        <option value="low_stock">Stock Bajo</option>
                        <option value="out_of_stock">Agotado</option>
                        <option value="expired">Vencido</option>
                        <option value="discontinued">Descontinuado</option>
                    </select>
                    <small class="form-text">Estado actual del medicamento</small>
                </div>
            </div>
        </div>

        <!-- Prescription Requirements -->
        <div class="form-group">
            <label class="form-label">Requisitos de Prescripción</label>
            <div class="form-check-group">
                <div class="form-check">
                    <input type="checkbox" id="requires_prescription" name="requires_prescription" class="form-check-input">
                    <label for="requires_prescription" class="form-check-label">Requiere prescripción médica</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" id="controlled_substance" name="controlled_substance" class="form-check-input">
                    <label for="controlled_substance" class="form-check-label">Sustancia controlada</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" id="high_alert" name="high_alert" class="form-check-input">
                    <label for="high_alert" class="form-check-label">Medicamento de alta vigilancia</label>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="form-group">
            <label for="notes" class="form-label">Notas Adicionales</label>
            <textarea id="notes" name="notes" class="form-control" rows="2" placeholder="Información adicional importante..."></textarea>
            <small class="form-text">Información adicional relevante</small>
        </div>

        <!-- Success/Error Messages -->
        <div id="successMessage" class="alert alert-success" style="display: none;">
            <i class="fas fa-check-circle"></i>
            <span>Medicamento creado exitosamente</span>
        </div>

        <div id="errorMessage" class="alert alert-danger" style="display: none;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Error al crear el medicamento</span>
        </div>
    </div>

    <div class="card-footer" style="display: flex; justify-content: space-between; align-items: center;">
        <a href="/medications" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i>
            Cancelar
        </a>
        <button type="submit" class="btn btn-primary" id="submitBtn">
            <i class="fas fa-pills"></i>
            Crear Medicamento
        </button>
    </div>
</form>
@endsection

@push('styles')
<style>
.required::after {
    content: ' *';
    color: var(--danger);
}

.form-check-group {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-top: 0.5rem;
}

.form-check {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.alert-success {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.alert-danger {
    background: rgba(239, 68, 68, 0.1);
    color: #DC2626;
    border: 1px solid rgba(239, 68, 68, 0.2);
}

.alert-warning {
    background: rgba(245, 158, 11, 0.1);
    color: #D97706;
    border: 1px solid rgba(245, 158, 11, 0.2);
}

.form-control.is-invalid {
    border-color: var(--danger);
}

.invalid-feedback {
    color: var(--danger);
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.stock-warning {
    background: rgba(245, 158, 11, 0.1);
    border: 1px solid rgba(245, 158, 11, 0.2);
    border-radius: 6px;
    padding: 0.75rem;
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: #D97706;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('medicationForm').addEventListener('submit', handleSubmit);
    document.getElementById('current_stock').addEventListener('input', checkStockLevel);
    document.getElementById('minimum_stock').addEventListener('input', checkStockLevel);
    document.getElementById('expiry_date').addEventListener('change', checkExpiryDate);
});

function checkStockLevel() {
    const currentStock = parseInt(document.getElementById('current_stock').value) || 0;
    const minimumStock = parseInt(document.getElementById('minimum_stock').value) || 0;
    
    // Remove existing warning
    const existingWarning = document.querySelector('.stock-warning');
    if (existingWarning) {
        existingWarning.remove();
    }
    
    if (currentStock > 0 && currentStock <= minimumStock) {
        const warning = document.createElement('div');
        warning.className = 'stock-warning';
        warning.innerHTML = '<i class="fas fa-exclamation-triangle"></i> El stock actual está en el nivel mínimo o por debajo de él';
        document.getElementById('current_stock').parentNode.appendChild(warning);
        
        // Update status automatically
        document.getElementById('status').value = 'low_stock';
    } else if (currentStock === 0) {
        document.getElementById('status').value = 'out_of_stock';
    } else {
        document.getElementById('status').value = 'available';
    }
}

function checkExpiryDate() {
    const expiryDate = new Date(document.getElementById('expiry_date').value);
    const today = new Date();
    const oneMonthFromNow = new Date();
    oneMonthFromNow.setMonth(today.getMonth() + 1);
    
    // Remove existing warning
    const existingWarning = document.querySelector('.expiry-warning');
    if (existingWarning) {
        existingWarning.remove();
    }
    
    if (expiryDate <= today) {
        const warning = document.createElement('div');
        warning.className = 'alert alert-danger expiry-warning';
        warning.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Este medicamento ya está vencido';
        document.getElementById('expiry_date').parentNode.appendChild(warning);
        
        document.getElementById('status').value = 'expired';
    } else if (expiryDate <= oneMonthFromNow) {
        const warning = document.createElement('div');
        warning.className = 'alert alert-warning expiry-warning';
        warning.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Este medicamento vence pronto';
        document.getElementById('expiry_date').parentNode.appendChild(warning);
    }
}

async function handleSubmit(e) {
    e.preventDefault();
    
    // Clear previous errors
    clearErrors();
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    
    // Handle checkboxes
    data.requires_prescription = document.getElementById('requires_prescription').checked;
    data.controlled_substance = document.getElementById('controlled_substance').checked;
    data.high_alert = document.getElementById('high_alert').checked;
    
    // Validate form
    if (!validateForm(data)) {
        return;
    }
    
    try {
        showLoading();
        
        const response = await fetch('/api/medications', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (response.ok) {
            showSuccess('Medicamento creado exitosamente. Redirigiendo...');
            setTimeout(() => {
                window.location.href = '/medications';
            }, 2000);
        } else {
            hideLoading();
            
            if (result.errors) {
                showValidationErrors(result.errors);
            } else {
                showError(result.message || 'Error al crear el medicamento');
            }
        }
    } catch (error) {
        hideLoading();
        console.error('Error:', error);
        showError('Error de conexión. Por favor, inténtelo de nuevo.');
    }
}

function validateForm(data) {
    let isValid = true;
    
    // Required fields validation
    const requiredFields = ['name', 'dosage', 'form', 'current_stock'];
    
    requiredFields.forEach(field => {
        if (!data[field] || data[field].toString().trim() === '') {
            showFieldError(field, 'Este campo es requerido');
            isValid = false;
        }
    });
    
    // Numeric validations
    if (data.current_stock && parseInt(data.current_stock) < 0) {
        showFieldError('current_stock', 'El stock no puede ser negativo');
        isValid = false;
    }
    
    if (data.minimum_stock && parseInt(data.minimum_stock) < 0) {
        showFieldError('minimum_stock', 'El stock mínimo no puede ser negativo');
        isValid = false;
    }
    
    if (data.unit_price && parseFloat(data.unit_price) < 0) {
        showFieldError('unit_price', 'El precio no puede ser negativo');
        isValid = false;
    }
    
    // Date validation
    if (data.expiry_date) {
        const expiryDate = new Date(data.expiry_date);
        const today = new Date();
        
        if (expiryDate < today) {
            showFieldError('expiry_date', 'La fecha de vencimiento no puede ser en el pasado');
            isValid = false;
        }
    }
    
    return isValid;
}

function showFieldError(fieldName, message) {
    const field = document.getElementById(fieldName);
    if (field) {
        field.classList.add('is-invalid');
        
        // Remove existing error message
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }
        
        // Add new error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }
}

function showValidationErrors(errors) {
    Object.keys(errors).forEach(field => {
        const messages = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
        showFieldError(field, messages[0]);
    });
}

function clearErrors() {
    // Remove invalid classes
    document.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
    });
    
    // Remove error messages
    document.querySelectorAll('.invalid-feedback').forEach(el => {
        el.remove();
    });
    
    // Hide alert messages
    document.getElementById('successMessage').style.display = 'none';
    document.getElementById('errorMessage').style.display = 'none';
}

function showSuccess(message) {
    const successEl = document.getElementById('successMessage');
    successEl.querySelector('span').textContent = message;
    successEl.style.display = 'flex';
    
    // Scroll to top to show message
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function showError(message) {
    const errorEl = document.getElementById('errorMessage');
    errorEl.querySelector('span').textContent = message;
    errorEl.style.display = 'flex';
    
    // Scroll to top to show message
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function showLoading() {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando...';
}

function hideLoading() {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="fas fa-pills"></i> Crear Medicamento';
}
</script>
@endpush 