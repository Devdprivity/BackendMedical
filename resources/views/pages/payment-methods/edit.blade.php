@extends('layouts.app')

@section('title', 'Editar Método de Pago')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Editar Método de Pago</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('payment-methods.web.index') }}">Métodos de Pago</a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Editar Método de Pago</h4>
                    <p class="text-muted mb-0">Actualiza la configuración de tu método de pago</p>
                </div>
                <div class="card-body">
                    <form id="editPaymentMethodForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tarifa por consulta <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="consultation_fee" name="consultation_fee" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Moneda <span class="text-danger">*</span></label>
                                    <select class="form-select" id="currency" name="currency" required>
                                        <option value="">Seleccionar moneda</option>
                                        <option value="USD">USD - Dólar Estadounidense</option>
                                        <option value="EUR">EUR - Euro</option>
                                        <option value="VES">VES - Bolívar Venezolano</option>
                                        <option value="USDT">USDT - Tether USD</option>
                                        <option value="BTC">BTC - Bitcoin</option>
                                        <option value="BNB">BNB - Binance Coin</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active">
                                <label class="form-check-label" for="is_active">
                                    Método activo
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Instrucciones para el paciente</label>
                            <textarea class="form-control" id="instructions" name="instructions" rows="3" placeholder="Instrucciones adicionales que verá el paciente al realizar el pago..."></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('payment-methods.web.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Actualizar Método
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const methodId = window.location.pathname.split('/')[2];
    const form = document.getElementById('editPaymentMethodForm');
    
    // Cargar datos del método de pago
    loadPaymentMethod(methodId);
    
    // Manejar envío del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        updatePaymentMethod(methodId);
    });
});

async function loadPaymentMethod(methodId) {
    try {
        const response = await fetch(`/api/payment-methods/${methodId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error('Error al cargar el método de pago');
        }
        
        const method = await response.json();
        populateForm(method);
        
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar el método de pago');
    }
}

function populateForm(method) {
    document.getElementById('consultation_fee').value = method.consultation_fee || '';
    document.getElementById('currency').value = method.currency || '';
    document.getElementById('is_active').checked = method.is_active;
    document.getElementById('instructions').value = method.instructions || '';
}

async function updatePaymentMethod(methodId) {
    try {
        const formData = new FormData(document.getElementById('editPaymentMethodForm'));
        
        const response = await fetch(`/api/payment-methods/${methodId}`, {
            method: 'PUT',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        });
        
        if (!response.ok) {
            throw new Error('Error al actualizar el método de pago');
        }
        
        alert('Método de pago actualizado correctamente');
        window.location.href = '/payment-methods';
        
    } catch (error) {
        console.error('Error:', error);
        alert('Error al actualizar el método de pago');
    }
}
</script>
@endsection 