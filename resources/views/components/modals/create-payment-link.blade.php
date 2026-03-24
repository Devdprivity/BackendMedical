<!-- Modal Crear Link de Pago -->
<div class="modal fade" id="createLinkModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>
                    Crear Link de Pago
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createLinkForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Método de Pago <span class="text-danger">*</span></label>
                                <select class="form-select" id="payment_method_id" name="payment_method_id" required>
                                    <option value="">Seleccionar método</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Paciente (Opcional)</label>
                                <select class="form-select" id="patient_id" name="patient_id">
                                    <option value="">Sin paciente específico</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Monto <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0.01" required>
                                    <span class="input-group-text" id="currency-display">USD</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Expira en <span class="text-danger">*</span></label>
                                <select class="form-select" id="expires_in_hours" name="expires_in_hours" required>
                                    <option value="1">1 hora</option>
                                    <option value="6">6 horas</option>
                                    <option value="24" selected>24 horas</option>
                                    <option value="72">3 días</option>
                                    <option value="168">7 días</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Concepto <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="concept" name="concept" value="Consulta médica" readonly required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción (Opcional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3" readonly></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Crear Link
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Variables globales para el componente
let paymentMethods = [];
let patients = [];
let doctorName = '{{ auth()->user()->first_name ?? "" }} {{ auth()->user()->last_name ?? "" }}';

document.addEventListener('DOMContentLoaded', function() {
    initializeCreateLinkModal();
});

function openCreateModal() {
    const modal = document.getElementById('createLinkModal');
    if (modal && typeof bootstrap !== 'undefined') {
        bootstrap.Modal.getOrCreateInstance(modal).show();
    }
}

function initializeCreateLinkModal() {
    setupModalEventListeners();
    
    // Cargar datos cuando se abre el modal
    const modal = document.getElementById('createLinkModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function() {
            loadCreateData();
        });
    }
}

function setupModalEventListeners() {
    // Formulario crear link
    const form = document.getElementById('createLinkForm');
    if (form) {
        form.addEventListener('submit', handleCreateLink);
    }
    
    // Cambio de método de pago
    const methodSelect = document.getElementById('payment_method_id');
    if (methodSelect) {
        methodSelect.addEventListener('change', function() {
            const selectedMethod = paymentMethods.find(m => m.id == this.value);
            if (selectedMethod) {
                document.getElementById('amount').value = selectedMethod.consultation_fee;
                document.getElementById('currency-display').textContent = selectedMethod.currency;
            }
            updateDescription();
        });
    }
    
    // Cambio de paciente
    const patientSelect = document.getElementById('patient_id');
    if (patientSelect) {
        patientSelect.addEventListener('change', updateDescription);
    }
}

async function loadCreateData() {
    try {
        const response = await fetch('/api/payment-links/create-data', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) throw new Error('Error al cargar datos');
        
        const data = await response.json();
        populateCreateForm(data.data);
    } catch (error) {
        console.error('Error:', error);
        showToast('Error al cargar los datos del formulario', 'error');
    }
}

function populateCreateForm(data) {
    paymentMethods = data.payment_methods;
    patients = data.patients;
    
    console.log('Payment methods:', data.payment_methods);
    console.log('Patients:', data.patients);
    
    // Llenar métodos de pago
    const methodSelect = document.getElementById('payment_method_id');
    if (methodSelect) {
        methodSelect.innerHTML = '<option value="">Seleccionar método</option>';
        data.payment_methods.forEach(method => {
            methodSelect.innerHTML += `<option value="${method.id}">${method.type_name} - ${method.consultation_fee} ${method.currency}</option>`;
        });
    }
    
    // Llenar pacientes
    const patientSelect = document.getElementById('patient_id');
    if (patientSelect) {
        patientSelect.innerHTML = '<option value="">Sin paciente específico</option>';
        data.patients.forEach(patient => {
            patientSelect.innerHTML += `<option value="${patient.id}">${patient.name}</option>`;
        });
    }
    
    // Establecer descripción por defecto
    updateDescription();
}

function updateDescription() {
    const patientSelect = document.getElementById('patient_id');
    const descriptionField = document.getElementById('description');
    
    if (!descriptionField) return;
    
    let description = `Pago de consulta médica`;
    
    if (doctorName.trim()) {
        description += ` del Dr. ${doctorName.trim()}`;
    }
    
    if (patientSelect && patientSelect.value) {
        const selectedPatient = patients.find(p => p.id == patientSelect.value);
        if (selectedPatient) {
            description += ` para ${selectedPatient.name}`;
        }
    }
    
    descriptionField.value = description;
}

async function handleCreateLink(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    
    // Mostrar loading
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creando...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('/api/payment-links', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify(data)
        });
        
        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message || 'Error al crear el link');
        }
        
        const result = await response.json();
        
        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('createLinkModal'));
        modal.hide();
        
        // Limpiar formulario
        e.target.reset();
        document.getElementById('concept').value = 'Consulta médica';
        updateDescription();
        
        // Mostrar modal de éxito con el link
        showSuccessModal(result.data);
        
        // Disparar evento personalizado para que la página padre pueda recargar datos
        document.dispatchEvent(new CustomEvent('paymentLinkCreated', { 
            detail: result.data 
        }));
        
    } catch (error) {
        console.error('Error:', error);
        showToast('Error al crear el link de pago: ' + error.message, 'error');
    } finally {
        // Restaurar botón
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

function showSuccessModal(linkData) {
    const modalHtml = `
        <div class="modal fade" id="successLinkModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-check-circle me-2"></i>
                            ¡Link de Pago Creado!
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            El link de pago se ha creado exitosamente y está listo para compartir.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Link de Pago</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="generated-link" value="${linkData.payment_url}" readonly>
                                        <button class="btn btn-outline-primary" onclick="copyToClipboard('generated-link')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <label class="form-label fw-bold">Código QR</label>
                                    <div class="mb-2">
                                        <img src="${linkData.qr_url}" class="img-fluid" style="max-width: 150px; border: 1px solid #dee2e6; border-radius: 8px;">
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary" onclick="downloadQR('${linkData.qr_url}', '${linkData.token}')">
                                        <i class="fas fa-download me-1"></i>
                                        Descargar QR
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h6 class="fw-bold mb-3">Compartir por:</h6>
                            <div class="d-grid gap-2 d-md-flex">
                                <button class="btn btn-success flex-fill" onclick="shareWhatsApp('${linkData.payment_url}', '${linkData.concept}')">
                                    <i class="fab fa-whatsapp me-2"></i>
                                    WhatsApp
                                </button>
                                <button class="btn btn-primary flex-fill" onclick="shareEmail('${linkData.payment_url}', '${linkData.concept}')">
                                    <i class="fas fa-envelope me-2"></i>
                                    Email
                                </button>
                                <button class="btn btn-info flex-fill" onclick="shareTelegram('${linkData.payment_url}', '${linkData.concept}')">
                                    <i class="fab fa-telegram me-2"></i>
                                    Telegram
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal anterior si existe
    const existingModal = document.getElementById('successLinkModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Agregar nuevo modal
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('successLinkModal'));
    modal.show();
    
    // Remover modal del DOM cuando se cierre
    document.getElementById('successLinkModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

// Funciones de utilidad
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    element.select();
    document.execCommand('copy');
    showToast('Link copiado al portapapeles', 'success');
}

function downloadQR(qrUrl, token) {
    const link = document.createElement('a');
    link.href = qrUrl;
    link.download = `qr-pago-${token}.png`;
    link.click();
}

function shareWhatsApp(url, concept) {
    const message = `¡Hola! Te envío el link para realizar el pago de: ${concept}\n\n${url}`;
    const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;
    window.open(whatsappUrl, '_blank');
}

function shareEmail(url, concept) {
    const subject = `Link de Pago - ${concept}`;
    const body = `Estimado paciente,\n\nTe envío el link para realizar el pago de: ${concept}\n\n${url}\n\nSaludos cordiales.`;
    const emailUrl = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
    window.open(emailUrl);
}

function shareTelegram(url, concept) {
    const message = `Link de pago: ${concept}`;
    const telegramUrl = `https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(message)}`;
    window.open(telegramUrl, '_blank');
}

function showToast(message, type = 'info') {
    // Implementación simple de toast - puedes mejorarla
    const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info';
    const toast = document.createElement('div');
    toast.className = `alert ${alertClass} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}

// Función global para abrir el modal
window.openCreateLinkModal = function() {
    const modal = new bootstrap.Modal(document.getElementById('createLinkModal'));
    modal.show();
};
</script>
@endpush 