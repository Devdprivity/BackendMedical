<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionLimits
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $action): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login.view');
        }

        $user = auth()->user();
        
        // Admin users bypass subscription limits
        if ($user->role === 'admin') {
            return $next($request);
        }

        $subscription = $user->currentSubscription;
        
        if (!$subscription) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Necesitas una suscripción activa para realizar esta acción.',
                    'redirect' => route('subscription.plans')
                ], 402);
            }
            
            return redirect()->route('subscription.plans')
                           ->with('error', 'Necesitas una suscripción activa para continuar.');
        }

        // Check if subscription is active
        if (!$subscription->isActive() && !$subscription->isTrial()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Tu suscripción ha expirado.',
                    'redirect' => route('subscription.plans')
                ], 402);
            }
            
            return redirect()->route('subscription.plans')
                           ->with('error', 'Tu suscripción ha expirado. Renueva para continuar.');
        }

        // Check specific action limits
        $canPerform = $user->canPerformAction($action);
        
        if (!$canPerform) {
            $message = $this->getLimitMessage($action, $subscription);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => $message,
                    'limit_reached' => true,
                    'upgrade_required' => true,
                    'redirect' => route('subscription.plans')
                ], 402);
            }
            
            return redirect()->back()
                           ->with('error', $message)
                           ->with('upgrade_required', true);
        }

        return $next($request);
    }

    /**
     * Get appropriate limit message based on action.
     */
    private function getLimitMessage(string $action, $subscription): string
    {
        $planName = $subscription->plan->name;
        
        switch ($action) {
            case 'add_doctor':
                $limit = $subscription->plan->max_doctors;
                return "Has alcanzado el límite de {$limit} doctores en tu {$planName}. Actualiza tu plan para agregar más.";
                
            case 'add_patient':
                $limit = $subscription->plan->max_patients;
                return "Has alcanzado el límite de {$limit} pacientes en tu {$planName}. Actualiza tu plan para agregar más.";
                
            case 'add_appointment':
                $limit = $subscription->plan->max_appointments_per_month;
                return "Has alcanzado el límite de {$limit} citas por mes en tu {$planName}. Actualiza tu plan para programar más citas.";
                
            case 'add_staff':
                $limit = $subscription->plan->max_staff;
                return "Has alcanzado el límite de {$limit} miembros del personal en tu {$planName}. Actualiza tu plan para agregar más.";
                
            case 'add_location':
                $limit = $subscription->plan->max_locations;
                return "Has alcanzado el límite de {$limit} ubicaciones en tu {$planName}. Actualiza tu plan para agregar más.";
                
            default:
                return "Has alcanzado el límite de tu plan actual. Actualiza para acceder a más funciones.";
        }
    }
}
