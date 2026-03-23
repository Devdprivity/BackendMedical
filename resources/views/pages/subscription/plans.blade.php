@extends('layouts.app')

@section('title', 'Planes de Suscripción - DrOrganiza')

@section('content')
<div class="pricing-page">
    <!-- Header Section -->
    <div class="pricing-header">
        <div class="container">
            <h1 class="pricing-title">Elige el plan perfecto para tu práctica médica</h1>
            <p class="pricing-subtitle">Comienza con una prueba gratuita de 1 hora. Sin compromisos, cancela cuando quieras.</p>
            
            <!-- Billing Toggle -->
            <div class="billing-toggle">
                <span class="billing-label" id="monthlyLabel">Mensual</span>
                <div class="toggle-switch" onclick="toggleBilling()">
                    <div class="toggle-slider" id="toggleSlider"></div>
                </div>
                <span class="billing-label" id="yearlyLabel">Anual</span>
                <span class="save-badge">Ahorra hasta 17%</span>
            </div>
        </div>
    </div>

    <!-- Plans Grid -->
    <div class="container">
        <div class="plans-grid" id="plansGrid">
            <!-- Plans will be loaded here -->
            <div class="loading-plans">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Cargando planes...</p>
            </div>
        </div>
    </div>

    <!-- Features Comparison -->
    <div class="features-section">
        <div class="container">
            <h2 class="features-title">Comparación detallada de características</h2>
            <div class="features-table-container">
                <table class="features-table" id="featuresTable">
                    <!-- Features table will be loaded here -->
                </table>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="faq-section">
        <div class="container">
            <h2 class="faq-title">Preguntas Frecuentes</h2>
            <div class="faq-grid">
                <div class="faq-item">
                    <h3 class="faq-question">¿Puedo cambiar de plan en cualquier momento?</h3>
                    <p class="faq-answer">Sí, puedes actualizar o degradar tu plan en cualquier momento. Los cambios se aplicarán inmediatamente y se prorrateará la facturación.</p>
                </div>
                <div class="faq-item">
                    <h3 class="faq-question">¿Qué incluye la prueba gratuita?</h3>
                    <p class="faq-answer">La prueba gratuita de 1 hora incluye acceso completo a todas las funciones básicas: 1 doctor, hasta 10 pacientes y 5 citas por mes.</p>
                </div>
                <div class="faq-item">
                    <h3 class="faq-question">¿Hay costos ocultos?</h3>
                    <p class="faq-answer">No, todos nuestros precios son transparentes. Lo que ves es lo que pagas. No hay tarifas de configuración ni costos ocultos.</p>
                </div>
                <div class="faq-item">
                    <h3 class="faq-question">¿Puedo cancelar en cualquier momento?</h3>
                    <p class="faq-answer">Absolutamente. Puedes cancelar tu suscripción en cualquier momento desde tu panel de control. No hay penalizaciones por cancelación.</p>
                </div>
                <div class="faq-item">
                    <h3 class="faq-question">¿Ofrecen soporte técnico?</h3>
                    <p class="faq-answer">Sí, todos los planes incluyen soporte por email. Los planes pagos incluyen soporte prioritario con tiempos de respuesta más rápidos.</p>
                </div>
                <div class="faq-item">
                    <h3 class="faq-question">¿Los datos están seguros?</h3>
                    <p class="faq-answer">Absolutamente. Utilizamos cifrado de nivel bancario y cumplimos con todas las regulaciones de privacidad médica para proteger tus datos.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.pricing-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}

.pricing-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4rem 0;
    text-align: center;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.pricing-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.pricing-subtitle {
    font-size: 1.25rem;
    opacity: 0.9;
    margin-bottom: 3rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.billing-toggle {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    margin-top: 2rem;
}

.billing-label {
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.billing-label.active {
    opacity: 1;
}

.billing-label:not(.active) {
    opacity: 0.6;
}

.toggle-switch {
    width: 60px;
    height: 30px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
}

.toggle-slider {
    width: 26px;
    height: 26px;
    background: white;
    border-radius: 50%;
    position: absolute;
    top: 2px;
    left: 2px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.toggle-slider.yearly {
    transform: translateX(30px);
}

.save-badge {
    background: #10b981;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
}

.plans-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin: -2rem 0 4rem 0;
    position: relative;
    z-index: 10;
}

.loading-plans {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem;
    color: var(--gray-500);
}

.loading-plans i {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.plan-card {
    background: white;
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    position: relative;
    border: 2px solid transparent;
}

.plan-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.plan-card.popular {
    border-color: #667eea;
    transform: scale(1.05);
}

.plan-card.popular::before {
    content: 'MÁS POPULAR';
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 0.5rem 1.5rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 1px;
}

.plan-card.current-plan-card {
    border-color: #10b981;
    box-shadow: 0 10px 30px rgba(16, 185, 129, 0.2);
    transform: scale(1.02);
}

.current-plan-indicator {
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 0.5rem 1.5rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 1px;
    z-index: 10;
}

.trial-info {
    color: #06b6d4;
    font-size: 0.875rem;
    font-weight: 600;
    margin-top: 0.5rem;
    padding: 0.5rem;
    background: rgba(6, 182, 212, 0.1);
    border-radius: 8px;
    text-align: center;
    border: 1px solid rgba(6, 182, 212, 0.2);
}

.plan-card.trial-plan {
    border: 2px solid #06b6d4;
    position: relative;
}

.plan-card.trial-plan::before {
    content: 'PRUEBA TEMPORAL';
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, #06b6d4, #0891b2);
    color: white;
    padding: 0.5rem 1.5rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 1px;
    z-index: 10;
}

.plan-button.current-plan {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    cursor: default;
    opacity: 0.8;
}

.plan-button.current-plan:hover {
    transform: none;
    box-shadow: none;
}

.plan-button:disabled {
    cursor: not-allowed;
    opacity: 0.7;
}

.plan-button:disabled:hover {
    transform: none;
    box-shadow: none;
}

.plan-header {
    text-align: center;
    margin-bottom: 2rem;
}

.plan-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto 1rem;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.plan-icon.free { background: linear-gradient(135deg, #06b6d4, #0891b2); }
.plan-icon.doctor { background: linear-gradient(135deg, #667eea, #764ba2); }
.plan-icon.small { background: linear-gradient(135deg, #f59e0b, #d97706); }
.plan-icon.large { background: linear-gradient(135deg, #ef4444, #dc2626); }
.plan-icon.enterprise { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

.plan-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 0.5rem;
}

.plan-description {
    color: var(--gray-600);
    font-size: 0.875rem;
    line-height: 1.5;
}

.plan-pricing {
    text-align: center;
    margin-bottom: 2rem;
}

.plan-price {
    font-size: 3rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 0.5rem;
}

.plan-price .currency {
    font-size: 1.5rem;
    vertical-align: top;
}

.plan-price .period {
    font-size: 1rem;
    color: var(--gray-500);
    font-weight: 400;
}

.plan-savings {
    color: #10b981;
    font-size: 0.875rem;
    font-weight: 600;
}

.plan-features {
    margin-bottom: 2rem;
}

.plan-features h4 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 1rem;
}

.feature-list {
    list-style: none;
    padding: 0;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0;
    font-size: 0.875rem;
    color: var(--gray-700);
}

.feature-item i {
    color: #10b981;
    font-size: 1rem;
}

.feature-item.limit {
    color: var(--gray-500);
}

.plan-button {
    width: 100%;
    padding: 1rem;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.plan-button.primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.plan-button.primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}

.plan-button.secondary {
    background: white;
    color: var(--dark);
    border: 2px solid #e2e8f0;
}

.plan-button.secondary:hover {
    border-color: #667eea;
    color: #667eea;
}

.features-section {
    background: white;
    padding: 4rem 0;
}

.features-title {
    text-align: center;
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 3rem;
}

.features-table-container {
    overflow-x: auto;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.features-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

.features-table th,
.features-table td {
    padding: 1rem;
    text-align: center;
    border-bottom: 1px solid #e2e8f0;
}

.features-table th {
    background: var(--gray-100);
    font-weight: 600;
    color: var(--dark);
}

.features-table .feature-name {
    text-align: left;
    font-weight: 500;
}

.features-table .check {
    color: #10b981;
    font-size: 1.2rem;
}

.features-table .cross {
    color: #ef4444;
    font-size: 1.2rem;
}

.faq-section {
    background: var(--gray-100);
    padding: 4rem 0;
}

.faq-title {
    text-align: center;
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 3rem;
}

.faq-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
}

.faq-item {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.faq-question {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 1rem;
}

.faq-answer {
    color: var(--gray-600);
    line-height: 1.6;
}

@media (max-width: 768px) {
    .pricing-title {
        font-size: 2rem;
    }
    
    .plans-grid {
        grid-template-columns: 1fr;
        margin-top: 1rem;
    }
    
    .plan-card.popular {
        transform: none;
    }
    
    .billing-toggle {
        flex-direction: column;
        gap: 1rem;
    }
    
    .faq-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@push('scripts')
<script>
let currentBilling = 'monthly';
let plans = [];
let userSubscription = null;

document.addEventListener('DOMContentLoaded', function() {
    initializePage();
});

async function initializePage() {
    await Promise.all([loadPlans(), loadUserSubscription()]);
    renderPlans();
    renderFeaturesTable();
}

async function loadUserSubscription() {
    try {
        const response = await fetch('/api/subscription/status');
        if (response.ok) {
            userSubscription = await response.json();
            console.log('User subscription:', userSubscription);
        }
    } catch (error) {
        console.error('Error loading user subscription:', error);
    }
}

async function loadPlans() {
    try {
        const response = await fetch('/api/plans');
        if (response.ok) {
            plans = await response.json();
        } else {
            showError('Error al cargar los planes');
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Error de conexión');
    }
}

function renderPlans() {
    const plansGrid = document.getElementById('plansGrid');
    
    const planCards = plans.map((plan, index) => {
        const isPopular = plan.slug === 'doctor';
        const price = currentBilling === 'yearly' ? plan.price_yearly : plan.price_monthly;
        const originalPrice = currentBilling === 'yearly' ? plan.price_monthly * 12 : plan.price_monthly;
        const savings = currentBilling === 'yearly' && plan.price_yearly > 0 ? 
            Math.round(((originalPrice - plan.price_yearly) / originalPrice) * 100) : 0;
        
        // Check if this is the user's current plan
        const isCurrentPlan = userSubscription && 
                             userSubscription.plan && 
                             userSubscription.plan.slug === plan.slug &&
                             (userSubscription.status === 'active' || userSubscription.status === 'trial');
        
        const isExpiredPlan = userSubscription && 
                             userSubscription.plan && 
                             userSubscription.plan.slug === plan.slug &&
                             (userSubscription.status === 'expired' || userSubscription.status === 'cancelled');
        
        // Determine button text and state
        let buttonText = '';
        let buttonClass = '';
        let buttonDisabled = false;
        
        if (isCurrentPlan) {
            if (userSubscription.status === 'trial') {
                const hoursRemaining = userSubscription.trial_hours_remaining || 0;
                const timeText = hoursRemaining < 24 ? `${hoursRemaining} horas restantes` : `${userSubscription.trial_days_remaining || 0} días restantes`;
                buttonText = `Tu Plan Actual (${timeText})`;
                buttonClass = 'current-plan';
                buttonDisabled = true;
            } else {
                buttonText = 'Tu Plan Actual';
                buttonClass = 'current-plan';
                buttonDisabled = true;
            }
        } else if (isExpiredPlan) {
            buttonText = 'Reactivar Plan';
            buttonClass = 'secondary';
        } else if (plan.is_free) {
            buttonText = 'Comenzar Prueba Gratuita';
            buttonClass = isPopular ? 'primary' : 'secondary';
        } else {
            // Check if it's an upgrade or downgrade
            if (userSubscription && userSubscription.plan) {
                const currentPlanIndex = plans.findIndex(p => p.slug === userSubscription.plan.slug);
                const thisPlanIndex = plans.findIndex(p => p.slug === plan.slug);
                
                if (thisPlanIndex > currentPlanIndex) {
                    buttonText = 'Actualizar Plan';
                    buttonClass = 'primary';
                } else if (thisPlanIndex < currentPlanIndex) {
                    buttonText = 'Cambiar a este Plan';
                    buttonClass = 'secondary';
                } else {
                    buttonText = 'Elegir Plan';
                    buttonClass = isPopular ? 'primary' : 'secondary';
                }
            } else {
                buttonText = 'Elegir Plan';
                buttonClass = isPopular ? 'primary' : 'secondary';
            }
        }
        
        return `
            <div class="plan-card ${isPopular ? 'popular' : ''} ${isCurrentPlan ? 'current-plan-card' : ''} ${plan.is_free ? 'trial-plan' : ''}">
                ${isCurrentPlan ? '<div class="current-plan-indicator">Tu Plan Actual</div>' : ''}
                <div class="plan-header">
                    <div class="plan-icon ${plan.slug}">
                        <i class="${getPlanIcon(plan.slug)}"></i>
                    </div>
                    <h3 class="plan-name">${plan.name}</h3>
                    <p class="plan-description">${plan.description}</p>
                </div>
                
                <div class="plan-pricing">
                    <div class="plan-price">
                        ${price > 0 ? `<span class="currency">$</span>${Math.floor(price)}` : 'Gratis'}
                        ${price > 0 ? `<span class="period">/${currentBilling === 'yearly' ? 'año' : 'mes'}</span>` : ''}
                    </div>
                    ${savings > 0 ? `<div class="plan-savings">Ahorra ${savings}%</div>` : ''}
                    ${isCurrentPlan && userSubscription.status === 'trial' ? 
                        `<div class="trial-info">Prueba termina en ${userSubscription.trial_hours_remaining || 0} horas</div>` : ''}
                </div>
                
                <div class="plan-features">
                    <h4>Características principales:</h4>
                    <ul class="feature-list">
                        ${renderPlanFeatures(plan)}
                    </ul>
                </div>
                
                <button class="plan-button ${buttonClass}" 
                        onclick="selectPlan('${plan.slug}')"
                        ${buttonDisabled ? 'disabled' : ''}>
                    ${buttonText}
                </button>
            </div>
        `;
    }).join('');
    
    plansGrid.innerHTML = planCards;
}

function renderPlanFeatures(plan) {
    const features = [
        {
            key: 'max_doctors',
            label: plan.max_doctors ? `${plan.max_doctors} doctor${plan.max_doctors > 1 ? 'es' : ''}` : 'Doctores ilimitados',
            icon: 'fas fa-user-md'
        },
        {
            key: 'max_patients',
            label: plan.max_patients ? `Hasta ${plan.max_patients} pacientes` : 'Pacientes ilimitados',
            icon: 'fas fa-users'
        },
        {
            key: 'max_appointments_per_month',
            label: plan.max_appointments_per_month ? `${plan.max_appointments_per_month} citas/mes` : 'Citas ilimitadas',
            icon: 'fas fa-calendar-check'
        },
        {
            key: 'support',
            label: plan.features.includes('priority_support') ? 'Soporte prioritario' : 
                   plan.features.includes('dedicated_support') ? 'Soporte dedicado' : 'Soporte por email',
            icon: 'fas fa-headset'
        }
    ];
    
    return features.map(feature => `
        <li class="feature-item">
            <i class="${feature.icon}"></i>
            ${feature.label}
        </li>
    `).join('');
}

function renderFeaturesTable() {
    const featuresTable = document.getElementById('featuresTable');
    
    const allFeatures = [
        { key: 'max_doctors', label: 'Número de doctores' },
        { key: 'max_patients', label: 'Número de pacientes' },
        { key: 'max_appointments_per_month', label: 'Citas por mes' },
        { key: 'basic_reports', label: 'Reportes básicos' },
        { key: 'advanced_reports', label: 'Reportes avanzados' },
        { key: 'lab_integration', label: 'Integración con laboratorios' },
        { key: 'integrated_billing', label: 'Facturación integrada' },
        { key: 'multi_specialty', label: 'Multi-especialidades' },
        { key: 'multiple_locations', label: 'Múltiples ubicaciones' },
        { key: 'custom_api', label: 'API personalizada' },
        { key: 'dedicated_support', label: 'Soporte dedicado' }
    ];
    
    const tableHeader = `
        <thead>
            <tr>
                <th class="feature-name">Característica</th>
                ${plans.map(plan => `<th>${plan.name}</th>`).join('')}
            </tr>
        </thead>
    `;
    
    const tableBody = `
        <tbody>
            ${allFeatures.map(feature => `
                <tr>
                    <td class="feature-name">${feature.label}</td>
                    ${plans.map(plan => {
                        if (feature.key.startsWith('max_')) {
                            const value = plan[feature.key];
                            return `<td>${value ? value.toLocaleString() : 'Ilimitado'}</td>`;
                        } else {
                            const hasFeature = plan.features.includes(feature.key);
                            return `<td>${hasFeature ? '<i class="fas fa-check check"></i>' : '<i class="fas fa-times cross"></i>'}</td>`;
                        }
                    }).join('')}
                </tr>
            `).join('')}
        </tbody>
    `;
    
    featuresTable.innerHTML = tableHeader + tableBody;
}

function toggleBilling() {
    currentBilling = currentBilling === 'monthly' ? 'yearly' : 'monthly';
    
    const toggleSlider = document.getElementById('toggleSlider');
    const monthlyLabel = document.getElementById('monthlyLabel');
    const yearlyLabel = document.getElementById('yearlyLabel');
    
    if (currentBilling === 'yearly') {
        toggleSlider.classList.add('yearly');
        monthlyLabel.classList.remove('active');
        yearlyLabel.classList.add('active');
    } else {
        toggleSlider.classList.remove('yearly');
        monthlyLabel.classList.add('active');
        yearlyLabel.classList.remove('active');
    }
    
    renderPlans();
}

function selectPlan(planSlug) {
    const plan = plans.find(p => p.slug === planSlug);
    if (!plan) return;
    
    // Check if user is not logged in
    if (!@json(auth()->check())) {
        window.location.href = `/login?plan=${planSlug}&billing=${currentBilling}`;
        return;
    }
    
    // Check if this is the current plan
    const isCurrentPlan = userSubscription && 
                         userSubscription.plan && 
                         userSubscription.plan.slug === planSlug &&
                         (userSubscription.status === 'active' || userSubscription.status === 'trial');
    
    if (isCurrentPlan) {
        // Redirect to subscription management
        window.location.href = '/subscription';
        return;
    }
    
    // Check if it's a reactivation
    const isExpiredPlan = userSubscription && 
                         userSubscription.plan && 
                         userSubscription.plan.slug === planSlug &&
                         (userSubscription.status === 'expired' || userSubscription.status === 'cancelled');
    
    if (isExpiredPlan) {
        reactivateSubscription();
        return;
    }
    
    // Proceed with new subscription
    subscribeToPlan(planSlug);
}

async function reactivateSubscription() {
    if (!confirm('¿Deseas reactivar tu suscripción?')) return;
    
    try {
        const response = await fetch('/api/subscription/reactivate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (response.ok) {
            alert('¡Suscripción reactivada exitosamente!');
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al reactivar la suscripción');
    }
}

async function subscribeToPlan(planSlug) {
    const plan = plans.find(p => p.slug === planSlug);
    if (!plan) return;
    
    try {
        const response = await fetch('/api/subscription/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                plan_id: plan.id,
                billing_cycle: currentBilling
            })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            alert('¡Suscripción activada exitosamente!');
            window.location.href = '/dashboard';
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al procesar la suscripción');
    }
}

function getPlanIcon(slug) {
    const icons = {
        'free': 'fas fa-clock',
        'doctor': 'fas fa-user-md',
        'small_clinic': 'fas fa-clinic-medical',
        'large_clinic': 'fas fa-hospital',
        'enterprise': 'fas fa-building'
    };
    return icons[slug] || 'fas fa-star';
}

function showError(message) {
    document.getElementById('plansGrid').innerHTML = `
        <div class="error-message" style="grid-column: 1 / -1; text-align: center; padding: 4rem; color: var(--danger);">
            <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 1rem;"></i>
            <p>${message}</p>
            <button onclick="loadPlans()" style="margin-top: 1rem; padding: 0.5rem 1rem; background: var(--primary); color: white; border: none; border-radius: 6px; cursor: pointer;">
                Reintentar
            </button>
        </div>
    `;
}

// Initialize labels
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('monthlyLabel').classList.add('active');
});
</script>
@endpush 