@extends('layouts.app')

@section('title', 'Mi Suscripción - DrOrganiza')

@section('content')
<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="mb-1"><i class="fas fa-crown me-2 text-warning"></i>Mi Suscripción</h2>
            <p class="text-muted mb-0">Estado actual de tu plan y uso de recursos</p>
        </div>
        <a href="{{ route('subscription.plans') }}" class="btn btn-primary">
            <i class="fas fa-arrow-up me-2"></i>Cambiar Plan
        </a>
    </div>

    {{-- Status Banner --}}
    <div id="statusBanner" class="alert d-none mb-4"></div>

    <div class="row g-4">

        {{-- Plan & Time Card --}}
        <div class="col-lg-5">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-id-card me-2 text-primary"></i>Plan Actual</h5>

                    <div id="planInfo" class="text-center py-3">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Usage Card --}}
        <div class="col-lg-7">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-chart-bar me-2 text-primary"></i>Uso de Recursos</h5>

                    <div id="usageInfo">
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary" role="status"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Upgrade CTA (shown when limits near) --}}
    <div id="upgradeCta" class="d-none mt-4">
        <div class="card border-warning shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="fas fa-exclamation-triangle text-warning fs-3"></i>
                <div class="flex-grow-1">
                    <h6 class="mb-1 fw-bold">Estás cerca del límite de tu plan</h6>
                    <p class="mb-0 text-muted small" id="upgradeCtaText"></p>
                </div>
                <a href="{{ route('subscription.plans') }}" class="btn btn-warning text-dark flex-shrink-0">
                    <i class="fas fa-arrow-up me-1"></i>Actualizar Plan
                </a>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    loadSubscriptionData();
});

async function loadSubscriptionData() {
    try {
        const [statusRes, usageRes] = await Promise.all([
            fetch('/api/subscription/status', { credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }),
            fetch('/api/subscription/usage',  { credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }),
        ]);

        const status = await statusRes.json();
        const usage  = await usageRes.json();

        renderPlanCard(status);
        renderUsageCard(usage);
        renderUpgradeCta(usage);
    } catch (e) {
        console.error('Error loading subscription data:', e);
        document.getElementById('statusBanner').className = 'alert alert-danger';
        document.getElementById('statusBanner').textContent = 'Error al cargar la información de suscripción.';
        document.getElementById('statusBanner').classList.remove('d-none');
    }
}

function renderPlanCard(s) {
    const isTrial   = s.status === 'trial';
    const isActive  = s.status === 'active';
    const isExpired = s.status === 'expired' || s.status === 'cancelled';

    // Status badge
    let badge = '', timeHtml = '';

    if (isTrial) {
        badge = '<span class="badge bg-info fs-6 mb-3">Prueba Gratuita</span>';

        const hours = s.trial_hours_remaining ?? 0;
        const days  = Math.floor(hours / 24);
        const rem   = hours % 24;

        let timeLabel, timeClass;
        if (hours <= 2) {
            timeLabel = `${hours}h restantes`;
            timeClass = 'text-danger fw-bold';
        } else if (hours < 24) {
            timeLabel = `${hours}h restantes`;
            timeClass = 'text-warning fw-bold';
        } else {
            timeLabel = days > 0 ? `${days}d ${rem}h restantes` : `${hours}h restantes`;
            timeClass = 'text-success fw-bold';
        }

        timeHtml = `
            <div class="mt-3 p-3 bg-light rounded">
                <div class="small text-muted mb-1">Tiempo restante</div>
                <div class="fs-4 ${timeClass}">
                    <i class="fas fa-clock me-1"></i>${timeLabel}
                </div>
                <div class="small text-muted mt-1">
                    Vence: ${formatDate(s.trial_ends_at ?? s.ends_at)}
                </div>
            </div>`;

        // Banner
        const banner = document.getElementById('statusBanner');
        banner.className = hours <= 2 ? 'alert alert-danger' : 'alert alert-info';
        banner.innerHTML = hours <= 2
            ? `<i class="fas fa-exclamation-triangle me-2"></i>Tu prueba vence en <strong>${hours} horas</strong>. <a href="${route('subscription.plans')}">Actualiza tu plan</a> para no perder acceso.`
            : `<i class="fas fa-info-circle me-2"></i>Estás en prueba gratuita. Te quedan <strong>${timeLabel}</strong>.`;
        banner.classList.remove('d-none');

    } else if (isActive) {
        badge = '<span class="badge bg-success fs-6 mb-3">Activo</span>';
        const days = s.days_remaining ?? 0;
        const dClass = days <= 7 ? 'text-warning fw-bold' : 'text-success fw-bold';
        timeHtml = `
            <div class="mt-3 p-3 bg-light rounded">
                <div class="small text-muted mb-1">Días restantes</div>
                <div class="fs-4 ${dClass}"><i class="fas fa-calendar me-1"></i>${days} días</div>
                <div class="small text-muted mt-1">Vence: ${formatDate(s.ends_at)}</div>
            </div>`;
    } else {
        badge = '<span class="badge bg-danger fs-6 mb-3">Expirado / Cancelado</span>';
        timeHtml = `<div class="alert alert-danger mt-3 mb-0"><i class="fas fa-times-circle me-2"></i>Tu suscripción ha expirado. <a href="${route('subscription.plans')}">Renueva aquí</a>.</div>`;
    }

    document.getElementById('planInfo').innerHTML = `
        ${badge}
        <h4 class="fw-bold mb-1">${s.plan?.name ?? 'Sin plan'}</h4>
        <div class="text-muted small mb-2">${s.plan?.slug?.toUpperCase() ?? ''}</div>
        ${timeHtml}
    `;
}

function renderUsageCard(data) {
    const items = [
        { key: 'appointments', icon: 'calendar-check', label: 'Citas este mes',   color: 'primary' },
        { key: 'patients',     icon: 'user-injured',   label: 'Pacientes',        color: 'success' },
        { key: 'doctors',      icon: 'user-md',        label: 'Doctores',         color: 'info'    },
        { key: 'staff',        icon: 'users',          label: 'Personal',         color: 'secondary'},
        { key: 'locations',    icon: 'map-marker-alt', label: 'Ubicaciones',      color: 'warning' },
    ];

    let html = '<div class="row g-3">';
    let needsUpgrade = false;
    const upgradeReasons = [];

    items.forEach(item => {
        const u = data.usage?.[item.key];
        if (!u) return;

        const current = u.current ?? 0;
        const max     = u.max;
        const pct     = u.percentage ?? 0;

        let barClass = 'bg-' + item.color;
        let maxLabel = u.unlimited ? '∞' : max;

        if (!u.unlimited) {
            if (pct >= 100) { barClass = 'bg-danger'; needsUpgrade = true; upgradeReasons.push(`${item.label}: ${current}/${max}`); }
            else if (pct >= 80) { barClass = 'bg-warning'; }
        }

        html += `
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small fw-semibold"><i class="fas fa-${item.icon} me-1 text-${item.color}"></i>${item.label}</span>
                    <span class="small text-muted">${current} / ${maxLabel}</span>
                </div>
                <div class="progress" style="height:8px">
                    <div class="progress-bar ${barClass}" style="width:${u.unlimited ? 0 : Math.min(pct,100)}%"></div>
                </div>
            </div>`;
    });

    html += '</div>';
    document.getElementById('usageInfo').innerHTML = html;

    if (needsUpgrade) {
        document.getElementById('upgradeCta').classList.remove('d-none');
        document.getElementById('upgradeCtaText').textContent = 'Límites alcanzados: ' + upgradeReasons.join(', ');
    }
}

function renderUpgradeCta(data) {
    // Already handled in renderUsageCard
}

function formatDate(dateStr) {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString('es-CL', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function route(name) {
    const routes = {
        'subscription.plans': '/plans',
    };
    return routes[name] ?? '/plans';
}
</script>
@endpush
@endsection
