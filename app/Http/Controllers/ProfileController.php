<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the user profile page
     */
    public function show()
    {
        $user = Auth::user();
        return view('pages.profile.show', compact('user'));
    }

    /**
     * Show the profile edit form
     */
    public function edit()
    {
        $user = Auth::user();
        return view('pages.profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ];

        // Add professional fields validation for doctors
        if ($user->role === 'doctor') {
            $rules['specialty'] = 'required|string|max:255';
            $rules['consultation_fee'] = 'required|numeric|min:0';
            $rules['bio'] = 'nullable|string|max:1000';
        }

        $request->validate($rules);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Add professional fields for doctors
        if ($user->role === 'doctor') {
            $updateData['specialty'] = $request->specialty;
            $updateData['consultation_fee'] = $request->consultation_fee;
            $updateData['bio'] = $request->bio;
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Perfil actualizado exitosamente'
        ]);
    }

    /**
     * Update the user's password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'La contraseña actual no es correcta'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada exitosamente'
        ]);
    }

    /**
     * Show the settings page
     */
    public function settings()
    {
        $user = Auth::user();
        return view('pages.profile.settings', compact('user'));
    }

    /**
     * Show the schedule configuration page for doctors
     */
    public function schedule()
    {
        $user = Auth::user();
        
        if ($user->role !== 'doctor') {
            return redirect()->route('profile.show')->with('error', 'Solo los médicos pueden configurar horarios');
        }
        
        return view('pages.profile.schedule', compact('user'));
    }

    /**
     * Update the user's schedule configuration
     */
    public function updateSchedule(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'doctor') {
            return response()->json([
                'success' => false,
                'message' => 'Solo los médicos pueden configurar horarios'
            ], 403);
        }

        $request->validate([
            'work_days' => 'required|array|min:1',
            'work_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'schedule_start' => 'required|date_format:H:i',
            'schedule_end' => 'required|date_format:H:i|after:schedule_start',
            'consultation_duration' => 'required|integer|in:15,30,45,60',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i|after:break_start',
        ]);

        // Validate break times are within work hours
        if ($request->break_start && $request->break_end) {
            if ($request->break_start < $request->schedule_start || 
                $request->break_end > $request->schedule_end) {
                return response()->json([
                    'success' => false,
                    'message' => 'Los horarios de descanso deben estar dentro del horario de trabajo'
                ], 422);
            }
        }

        $user->update([
            'work_days' => json_encode($request->work_days),
            'schedule_start' => $request->schedule_start,
            'schedule_end' => $request->schedule_end,
            'consultation_duration' => $request->consultation_duration,
            'break_start' => $request->break_start,
            'break_end' => $request->break_end,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Horarios configurados exitosamente'
        ]);
    }
} 