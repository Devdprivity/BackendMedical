<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Doctor;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->isActive()) {
            throw ValidationException::withMessages([
                'email' => ['Your account is not active.'],
            ]);
        }

        // Update last login
        $user->update(['last_login' => now()]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user->load('doctor'),
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,doctor,nurse,receptionist,accountant,lab_technician',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'active',
        ]);

        // Crear registro específico del rol si es necesario
        if ($request->role === 'doctor') {
            Doctor::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'specialty' => 'Medicina General', // Por defecto
                'license_number' => 'DOC' . $user->id . time(),
                'phone' => '+58414-000-0000', // Por defecto
                'status' => 'active',
                'experience_years' => 1,
                'bio' => 'Doctor registrado en el sistema',
                'rating' => 4.0
            ]);
        }

        // Asignar plan free trial al nuevo usuario
        $freePlan = SubscriptionPlan::where('slug', 'free')->first();
        if ($freePlan) {
            $trialEndDate = $freePlan->trial_days <= 1
                ? now()->addHour()
                : now()->addDays($freePlan->trial_days);

            UserSubscription::create([
                'user_id'                          => $user->id,
                'subscription_plan_id'             => $freePlan->id,
                'status'                           => 'trial',
                'starts_at'                        => now(),
                'ends_at'                          => $trialEndDate,
                'trial_ends_at'                    => $trialEndDate,
                'billing_cycle'                    => 'monthly',
                'current_doctors_count'            => 0,
                'current_patients_count'           => 0,
                'current_appointments_this_month'  => 0,
                'current_locations_count'          => 0,
                'current_staff_count'              => 1,
                'last_monthly_reset'               => now()->startOfMonth(),
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user->load('doctor'),
            'token' => $token,
            'token_type' => 'Bearer',
            'requires_onboarding' => !($user->onboarding_completed ?? false),
            'onboarding_url' => route('onboarding.index'),
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user()->load('doctor'));
    }
}
