<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionFeature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = auth()->user();
        
        // Admin bypasses all subscription checks
        if ($user && $user->role === 'admin') {
            return $next($request);
        }
        
        // Check if user has an active subscription
        if (!$user || !$user->currentSubscription) {
            return $this->denyAccess($request, 'No tienes una suscripción activa para acceder a esta funcionalidad.');
        }
        
        $subscription = $user->currentSubscription;
        
        // Check if subscription is active or in trial
        if (!$subscription->isActive() && !$subscription->isTrial()) {
            return $this->denyAccess($request, 'Tu suscripción ha expirado. Renueva tu plan para continuar.');
        }
        
        // Check if the plan includes the required feature
        if (!$subscription->plan->hasFeature($feature)) {
            $featureNames = [
                'inventory_management' => 'Gestión de Inventario de Medicamentos',
                'advanced_reports' => 'Reportes Avanzados',
                'lab_integration' => 'Integración de Laboratorio',
                'integrated_billing' => 'Facturación Integrada',
                'staff_management' => 'Gestión de Personal',
                'multi_specialty' => 'Múltiples Especialidades',
                'patient_portal' => 'Portal del Paciente',
                'prescription_management' => 'Gestión de Prescripciones',
            ];
            
            $featureName = $featureNames[$feature] ?? $feature;
            
            return $this->denyAccess($request, 
                "Tu plan actual ({$subscription->plan->name}) no incluye {$featureName}. " .
                "Actualiza tu suscripción para acceder a esta funcionalidad."
            );
        }
        
        return $next($request);
    }
    
    /**
     * Handle access denial
     */
    private function denyAccess(Request $request, string $message): Response
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'upgrade_required' => true,
                'current_plan' => auth()->user()->currentSubscription?->plan->name ?? 'Sin Plan',
            ], 403);
        }
        
        // For web routes, show custom subscription required page
        return response()->view('errors.subscription-required', [
            'message' => $message,
            'current_plan' => auth()->user()->currentSubscription?->plan->name ?? 'Plan Gratuito',
        ], 403);
    }
}
