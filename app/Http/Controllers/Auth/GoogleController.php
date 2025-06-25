<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Carbon\Carbon;

class GoogleController extends Controller
{
    /**
     * Redirect to Google OAuth.
     */
    public function redirect()
    {
        try {
            \Log::info('Google OAuth redirect initiated', [
                'client_id' => config('services.google.client_id') ? 'configured' : 'missing',
                'redirect_uri' => config('services.google.redirect'),
                'app_url' => config('app.url'),
                'environment' => app()->environment(),
                'current_url' => request()->getSchemeAndHttpHost()
            ]);
            
        return Socialite::driver('google')->redirect();
        } catch (\Exception $e) {
            \Log::error('Google OAuth redirect error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('login.view')->with('error', 'Error al configurar la autenticación con Google.');
        }
    }

    /**
     * Handle Google OAuth callback.
     */
    public function callback()
    {
        try {
            \Log::info('Google OAuth callback received', [
                'request_url' => request()->fullUrl(),
                'callback_url' => config('services.google.redirect'),
                'environment' => app()->environment()
            ]);
            
            $googleUser = Socialite::driver('google')->user();
            
            \Log::info('Google user data received', [
                'google_id' => $googleUser->getId(),
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName()
            ]);
            
            // Check if user already exists
            $user = User::where('email', $googleUser->getEmail())->first();
            
            $isNewUser = false;
            
            if (!$user) {
                // Create new user
                $isNewUser = true;
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'role' => 'doctor', // Default role for new users
                    'status' => 'active',
                    'last_login' => now(),
                ]);
                
                \Log::info('New user created', ['user_id' => $user->id]);
            } else {
                // Update existing user's Google ID if not set
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
                
                \Log::info('Existing user logged in', ['user_id' => $user->id]);
            }

            // Login user
            Auth::login($user);
            
            // Redirect based on user status
            if ($isNewUser || !($user->onboarding_completed ?? false)) {
                \Log::info('Redirecting to onboarding', [
                    'user_id' => $user->id,
                    'is_new_user' => $isNewUser,
                    'onboarding_completed' => $user->onboarding_completed ?? false
                ]);
                
                return redirect()->route('onboarding.index')->with('success', 
                    $isNewUser ? 
                    '¡Bienvenido a MediCare Pro! Vamos a configurar tu cuenta paso a paso.' : 
                    'Completa tu configuración de cuenta para comenzar.'
                );
            }
            
            \Log::info('Redirecting to dashboard', ['user_id' => $user->id]);
            return redirect()->route('dashboard')->with('success', '¡Bienvenido de vuelta!');
            
        } catch (\Exception $e) {
            \Log::error('Google OAuth callback error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('login.view')->with('error', 'Error al procesar la autenticación con Google. Por favor, intenta de nuevo.');
        }
    }

    /**
     * Assign free trial subscription to new user.
     */
    private function assignFreeTrial(User $user)
    {
        $freePlan = SubscriptionPlan::where('slug', 'free')->first();
        
        if (!$freePlan) {
            \Log::error('Free plan not found when assigning trial to user: ' . $user->id);
            return;
        }
        
        $trialEndDate = now()->addDays($freePlan->trial_days);
        
        UserSubscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $freePlan->id,
            'status' => 'trial',
            'starts_at' => now(),
            'ends_at' => $trialEndDate,
            'trial_ends_at' => $trialEndDate,
            'billing_cycle' => 'monthly',
            'current_doctors_count' => 0,
            'current_patients_count' => 0,
            'current_appointments_this_month' => 0,
            'current_locations_count' => 0,
            'current_staff_count' => 1, // The user themselves
            'last_monthly_reset' => now()->startOfMonth(),
        ]);
        
        \Log::info('Free trial assigned to user: ' . $user->id . ' - Trial ends: ' . $trialEndDate);
    }

    /**
     * Handle manual registration (non-Google).
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'doctor', // Default role for new registrations (not admin)
            'status' => 'active',
            'email_verified_at' => now(),
            'last_login' => now(),
        ]);

        // Assign free trial
        $this->assignFreeTrial($user);

        Auth::login($user);

        // Check if it's an API request
        if ($request->expectsJson()) {
        return response()->json([
            'message' => 'Cuenta creada exitosamente',
            'user' => $user,
            'trial_days' => 7,
                'redirect' => route('onboarding.index')
            ]);
        }

        // Redirect to onboarding for new manual registrations
        return redirect()->route('onboarding.index')->with('success', 
            '¡Bienvenido a MediCare Pro! Vamos a configurar tu cuenta paso a paso.'
        );
    }

    /**
     * Get subscription plans for pricing page.
     */
    public function getPlans()
    {
        $plans = SubscriptionPlan::active()->ordered()->get();
        
        return response()->json($plans);
    }

    /**
     * Check user's subscription status.
     */
    public function subscriptionStatus()
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
            'status' => $subscription->status,
            'plan' => $subscription->plan->name,
            'ends_at' => $subscription->ends_at,
            'days_remaining' => $subscription->days_remaining,
        ];
        
        if ($subscription->isTrial()) {
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
    public function usageStats()
    {
        $user = Auth::user();
        $subscription = $user->currentSubscription;
        
        if (!$subscription) {
            return response()->json(['error' => 'No subscription found'], 404);
        }
        
        $subscription->resetMonthlyCountersIfNeeded();
        
        return response()->json([
            'plan' => $subscription->plan->name,
            'limits' => [
                'doctors' => [
                    'current' => $subscription->current_doctors_count,
                    'max' => $subscription->plan->max_doctors,
                    'unlimited' => is_null($subscription->plan->max_doctors),
                ],
                'patients' => [
                    'current' => $subscription->current_patients_count,
                    'max' => $subscription->plan->max_patients,
                    'unlimited' => is_null($subscription->plan->max_patients),
                ],
                'appointments' => [
                    'current' => $subscription->current_appointments_this_month,
                    'max' => $subscription->plan->max_appointments_per_month,
                    'unlimited' => is_null($subscription->plan->max_appointments_per_month),
                    'period' => 'monthly',
                ],
                'staff' => [
                    'current' => $subscription->current_staff_count,
                    'max' => $subscription->plan->max_staff,
                    'unlimited' => is_null($subscription->plan->max_staff),
                ],
                'locations' => [
                    'current' => $subscription->current_locations_count,
                    'max' => $subscription->plan->max_locations,
                    'unlimited' => is_null($subscription->plan->max_locations),
                ],
            ],
            'features' => $subscription->plan->features,
        ]);
    }
}
