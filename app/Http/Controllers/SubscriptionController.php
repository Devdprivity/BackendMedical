<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Display subscription plans.
     */
    public function plans()
    {
        $plans = SubscriptionPlan::active()->ordered()->get();
        
        return view('pages.subscription.plans', compact('plans'));
    }

    /**
     * Get subscription plans via API.
     */
    public function getPlans()
    {
        $plans = SubscriptionPlan::active()->ordered()->get();
        
        return response()->json($plans);
    }

    /**
     * Display user's subscription dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $subscription = $user->currentSubscription;
        
        if (!$subscription) {
            return redirect()->route('subscription.plans')
                           ->with('info', 'Necesitas seleccionar un plan para continuar.');
        }
        
        return view('pages.subscription.dashboard', compact('subscription'));
    }

    /**
     * Get user's subscription status.
     */
    public function status()
    {
        $user = Auth::user();
        $subscription = $user->currentSubscription;
        
        if (!$subscription) {
            return response()->json([
                'status' => 'none',
                'message' => 'No tienes una suscripción activa.',
                'trial_available' => true,
            ]);
        }
        
        $data = [
            'id' => $subscription->id,
            'status' => $subscription->status,
            'plan' => [
                'id' => $subscription->plan->id,
                'name' => $subscription->plan->name,
                'slug' => $subscription->plan->slug,
                'price_monthly' => $subscription->plan->price_monthly,
                'price_yearly' => $subscription->plan->price_yearly,
                'features' => $subscription->plan->features,
            ],
            'starts_at' => $subscription->starts_at,
            'ends_at' => $subscription->ends_at,
            'days_remaining' => $subscription->days_remaining,
            'billing_cycle' => $subscription->billing_cycle,
        ];
        
        if ($subscription->isTrial()) {
            $data['trial_ends_at'] = $subscription->trial_ends_at;
            $data['trial_days_remaining'] = $subscription->trial_days_remaining;
            $data['message'] = "Tu prueba gratuita termina en {$subscription->trial_days_remaining} días.";
        } elseif ($subscription->isActive()) {
            $data['message'] = "Tu suscripción está activa hasta " . $subscription->ends_at->format('d/m/Y');
        } elseif ($subscription->isExpired()) {
            $data['message'] = "Tu suscripción ha expirado.";
        }
        
        return response()->json($data);
    }

    /**
     * Get user's usage statistics.
     */
    public function usage()
    {
        $user = Auth::user();
        $subscription = $user->currentSubscription;
        
        if (!$subscription) {
            return response()->json(['error' => 'No subscription found'], 404);
        }
        
        $subscription->resetMonthlyCountersIfNeeded();
        
        return response()->json([
            'plan' => [
                'name' => $subscription->plan->name,
                'slug' => $subscription->plan->slug,
            ],
            'usage' => [
                'doctors' => [
                    'current' => $subscription->current_doctors_count,
                    'max' => $subscription->plan->max_doctors,
                    'unlimited' => is_null($subscription->plan->max_doctors),
                    'percentage' => $this->getUsagePercentage($subscription->current_doctors_count, $subscription->plan->max_doctors),
                ],
                'patients' => [
                    'current' => $subscription->current_patients_count,
                    'max' => $subscription->plan->max_patients,
                    'unlimited' => is_null($subscription->plan->max_patients),
                    'percentage' => $this->getUsagePercentage($subscription->current_patients_count, $subscription->plan->max_patients),
                ],
                'appointments' => [
                    'current' => $subscription->current_appointments_this_month,
                    'max' => $subscription->plan->max_appointments_per_month,
                    'unlimited' => is_null($subscription->plan->max_appointments_per_month),
                    'percentage' => $this->getUsagePercentage($subscription->current_appointments_this_month, $subscription->plan->max_appointments_per_month),
                    'period' => 'monthly',
                ],
                'staff' => [
                    'current' => $subscription->current_staff_count,
                    'max' => $subscription->plan->max_staff,
                    'unlimited' => is_null($subscription->plan->max_staff),
                    'percentage' => $this->getUsagePercentage($subscription->current_staff_count, $subscription->plan->max_staff),
                ],
                'locations' => [
                    'current' => $subscription->current_locations_count,
                    'max' => $subscription->plan->max_locations,
                    'unlimited' => is_null($subscription->plan->max_locations),
                    'percentage' => $this->getUsagePercentage($subscription->current_locations_count, $subscription->plan->max_locations),
                ],
            ],
            'features' => $subscription->plan->features,
        ]);
    }

    /**
     * Subscribe to a plan.
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $user = Auth::user();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);
        
        // Cancel current subscription if exists
        $currentSubscription = $user->currentSubscription;
        if ($currentSubscription) {
            $currentSubscription->cancel();
        }
        
        // Calculate dates
        $startDate = now();
        $endDate = $request->billing_cycle === 'yearly' 
            ? $startDate->copy()->addYear() 
            : $startDate->copy()->addMonth();
        
        // Create new subscription
        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => $plan->is_free ? 'trial' : 'active',
            'starts_at' => $startDate,
            'ends_at' => $endDate,
            'trial_ends_at' => $plan->is_free ? $startDate->copy()->addDays($plan->trial_days) : null,
            'billing_cycle' => $request->billing_cycle,
            'amount_paid' => $request->billing_cycle === 'yearly' ? $plan->price_yearly : $plan->price_monthly,
            'current_doctors_count' => 0,
            'current_patients_count' => 0,
            'current_appointments_this_month' => 0,
            'current_locations_count' => 0,
            'current_staff_count' => 1,
            'last_monthly_reset' => now()->startOfMonth(),
        ]);
        
        return response()->json([
            'message' => 'Suscripción creada exitosamente',
            'subscription' => $subscription->load('plan'),
        ]);
    }

    /**
     * Cancel subscription.
     */
    public function cancel(Request $request)
    {
        $user = Auth::user();
        $subscription = $user->currentSubscription;
        
        if (!$subscription) {
            return response()->json(['error' => 'No subscription found'], 404);
        }
        
        $subscription->cancel();
        
        return response()->json([
            'message' => 'Suscripción cancelada exitosamente',
            'subscription' => $subscription->fresh(),
        ]);
    }

    /**
     * Reactivate subscription.
     */
    public function reactivate(Request $request)
    {
        $user = Auth::user();
        $subscription = $user->subscriptions()->cancelled()->latest()->first();
        
        if (!$subscription) {
            return response()->json(['error' => 'No cancelled subscription found'], 404);
        }
        
        $subscription->activate();
        
        return response()->json([
            'message' => 'Suscripción reactivada exitosamente',
            'subscription' => $subscription->fresh(),
        ]);
    }

    /**
     * Get billing history.
     */
    public function billingHistory()
    {
        $user = Auth::user();
        $subscriptions = $user->subscriptions()
                            ->with('plan')
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);
        
        return response()->json($subscriptions);
    }

    /**
     * Check if user can perform an action.
     */
    public function checkLimit(Request $request)
    {
        $request->validate([
            'action' => 'required|in:add_doctor,add_patient,add_appointment,add_staff,add_location',
        ]);

        $user = Auth::user();
        $canPerform = $user->canPerformAction($request->action);
        
        $subscription = $user->currentSubscription;
        $message = '';
        
        if (!$canPerform && $subscription) {
            switch ($request->action) {
                case 'add_doctor':
                    $message = "Has alcanzado el límite de {$subscription->plan->max_doctors} doctores en tu plan.";
                    break;
                case 'add_patient':
                    $message = "Has alcanzado el límite de {$subscription->plan->max_patients} pacientes en tu plan.";
                    break;
                case 'add_appointment':
                    $message = "Has alcanzado el límite de {$subscription->plan->max_appointments_per_month} citas por mes en tu plan.";
                    break;
                case 'add_staff':
                    $message = "Has alcanzado el límite de {$subscription->plan->max_staff} miembros del personal en tu plan.";
                    break;
                case 'add_location':
                    $message = "Has alcanzado el límite de {$subscription->plan->max_locations} ubicaciones en tu plan.";
                    break;
            }
        }
        
        return response()->json([
            'can_perform' => $canPerform,
            'message' => $message,
            'upgrade_required' => !$canPerform,
        ]);
    }

    /**
     * Calculate usage percentage.
     */
    private function getUsagePercentage($current, $max)
    {
        if (is_null($max)) {
            return 0; // Unlimited
        }
        
        if ($max == 0) {
            return 100;
        }
        
        return min(100, round(($current / $max) * 100, 1));
    }
}
