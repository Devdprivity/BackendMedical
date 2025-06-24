<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OnboardingController extends Controller
{
    /**
     * Steps for onboarding process
     */
    private $steps = [
        'profile' => [
            'title' => 'Completa tu Perfil',
            'description' => 'Información básica para que los pacientes te conozcan',
            'icon' => 'fas fa-user-md',
            'route' => 'onboarding.profile'
        ],
        'schedule' => [
            'title' => 'Configura tus Horarios',
            'description' => 'Define cuándo estás disponible para consultas',
            'icon' => 'fas fa-clock',
            'route' => 'onboarding.schedule'
        ],
        'booking' => [
            'title' => 'Link de Citas Públicas',
            'description' => 'Permite que pacientes reserven citas online',
            'icon' => 'fas fa-calendar-plus',
            'route' => 'onboarding.booking'
        ],
        'payments' => [
            'title' => 'Métodos de Pago',
            'description' => 'Configura cómo recibirás los pagos',
            'icon' => 'fas fa-credit-card',
            'route' => 'onboarding.payments'
        ],
        'complete' => [
            'title' => '¡Felicitaciones!',
            'description' => 'Tu cuenta está lista para usar',
            'icon' => 'fas fa-check-circle',
            'route' => 'onboarding.complete'
        ]
    ];

    /**
     * Show onboarding overview
     */
    public function index()
    {
        $user = Auth::user();
        
        // Check if onboarding is already completed
        if ($this->isOnboardingCompleted($user)) {
            return redirect()->route('dashboard')->with('info', 'El onboarding ya está completado.');
        }

        $progress = $this->getOnboardingProgress($user);
        
        return view('onboarding.index', [
            'steps' => $this->steps,
            'progress' => $progress,
            'currentStep' => $this->getCurrentStep($progress)
        ]);
    }

    /**
     * Show profile completion step
     */
    public function profile()
    {
        $user = Auth::user();
        
        return view('onboarding.profile', [
            'user' => $user,
            'progress' => $this->getOnboardingProgress($user),
            'steps' => $this->steps
        ]);
    }

    /**
     * Update profile information
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'specialty' => 'required|string|max:255',
            'medical_license' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'consultation_fee' => 'nullable|numeric|min:0',
            'years_experience' => 'nullable|integer|min:0|max:100'
        ]);

        $user = Auth::user();
        $user->update([
            'specialty' => $request->specialty,
            'medical_license' => $request->medical_license,
            'phone' => $request->phone,
            'bio' => $request->bio,
            'consultation_fee' => $request->consultation_fee,
            'years_experience' => $request->years_experience,
            'onboarding_profile_completed' => true
        ]);

        return redirect()->route('onboarding.schedule')->with('success', '¡Perfil completado exitosamente!');
    }

    /**
     * Show schedule configuration step
     */
    public function schedule()
    {
        $user = Auth::user();
        
        return view('onboarding.schedule', [
            'user' => $user,
            'progress' => $this->getOnboardingProgress($user),
            'steps' => $this->steps
        ]);
    }

    /**
     * Update schedule configuration
     */
    public function updateSchedule(Request $request)
    {
        $request->validate([
            'schedule_start' => 'required|date_format:H:i',
            'schedule_end' => 'required|date_format:H:i|after:schedule_start',
            'work_days' => 'required|array|min:1',
            'work_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'consultation_duration' => 'required|integer|min:15|max:240',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i|after:break_start'
        ]);

        $user = Auth::user();
        $user->update([
            'schedule_start' => $request->schedule_start,
            'schedule_end' => $request->schedule_end,
            'work_days' => json_encode($request->work_days),
            'consultation_duration' => $request->consultation_duration,
            'break_start' => $request->break_start,
            'break_end' => $request->break_end,
            'onboarding_schedule_completed' => true
        ]);

        return redirect()->route('onboarding.booking')->with('success', '¡Horarios configurados exitosamente!');
    }

    /**
     * Show booking link configuration step
     */
    public function booking()
    {
        $user = Auth::user();
        
        return view('onboarding.booking', [
            'user' => $user,
            'progress' => $this->getOnboardingProgress($user),
            'steps' => $this->steps
        ]);
    }

    /**
     * Enable booking link
     */
    public function enableBooking(Request $request)
    {
        $user = Auth::user();
        
        // Generate unique booking slug if not exists
        if (!$user->booking_slug) {
            $baseSlug = Str::slug($user->name);
            $slug = $baseSlug;
            $counter = 1;
            
            while (DB::table('users')->where('booking_slug', $slug)->where('id', '!=', $user->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            
            $user->booking_slug = $slug;
        }

        $user->update([
            'booking_enabled' => true,
            'onboarding_booking_completed' => true
        ]);

        return redirect()->route('onboarding.payments')->with('success', '¡Link de reservas activado exitosamente!');
    }

    /**
     * Show payment methods configuration step
     */
    public function payments()
    {
        $user = Auth::user();
        
        return view('onboarding.payments', [
            'user' => $user,
            'progress' => $this->getOnboardingProgress($user),
            'steps' => $this->steps
        ]);
    }

    /**
     * Update payment configuration
     */
    public function updatePayments(Request $request)
    {
        $request->validate([
            'payment_methods' => 'required|array|min:1',
            'payment_methods.*' => 'in:cash,transfer,card,paypal',
            'bank_account' => 'nullable|string|max:255',
            'paypal_email' => 'nullable|email|max:255'
        ]);

        $user = Auth::user();
        $user->update([
            'payment_methods' => json_encode($request->payment_methods),
            'bank_account' => $request->bank_account,
            'paypal_email' => $request->paypal_email,
            'onboarding_payments_completed' => true
        ]);

        return redirect()->route('onboarding.complete')->with('success', '¡Métodos de pago configurados exitosamente!');
    }

    /**
     * Show completion step
     */
    public function complete()
    {
        $user = Auth::user();
        
        // Mark onboarding as completed
        $user->update([
            'onboarding_completed' => true,
            'onboarding_completed_at' => now()
        ]);
        
        return view('onboarding.complete', [
            'user' => $user,
            'progress' => $this->getOnboardingProgress($user),
            'steps' => $this->steps
        ]);
    }

    /**
     * Finish onboarding and redirect to dashboard
     */
    public function finish()
    {
        return redirect()->route('dashboard')->with('success', '¡Bienvenido a MediCare Pro! Tu cuenta está lista para usar.');
    }

    /**
     * Get onboarding progress for user
     */
    private function getOnboardingProgress($user)
    {
        return [
            'profile' => $user->onboarding_profile_completed ?? false,
            'schedule' => $user->onboarding_schedule_completed ?? false,
            'booking' => $user->onboarding_booking_completed ?? false,
            'payments' => $user->onboarding_payments_completed ?? false,
            'complete' => $user->onboarding_completed ?? false,
        ];
    }

    /**
     * Get current step based on progress
     */
    private function getCurrentStep($progress)
    {
        if (!$progress['profile']) return 'profile';
        if (!$progress['schedule']) return 'schedule';
        if (!$progress['booking']) return 'booking';
        if (!$progress['payments']) return 'payments';
        if (!$progress['complete']) return 'complete';
        
        return 'complete';
    }

    /**
     * Check if onboarding is completed
     */
    private function isOnboardingCompleted($user)
    {
        return $user->onboarding_completed ?? false;
    }

    /**
     * Skip onboarding (for existing users or special cases)
     */
    public function skip()
    {
        $user = Auth::user();
        $user->update([
            'onboarding_completed' => true,
            'onboarding_completed_at' => now()
        ]);

        return redirect()->route('dashboard')->with('info', 'Onboarding omitido. Puedes configurar tu perfil desde la configuración.');
    }
}
