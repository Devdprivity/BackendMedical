<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Enable booking for a user
     */
    public function enableBooking($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Check if user is a doctor and has completed professional information
            if ($user->role === 'doctor') {
                // Check basic professional info
                if (!$user->specialty || !$user->consultation_fee) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Para habilitar las reservas públicas, primero debes completar tu especialidad y precio de consulta en tu perfil.',
                        'redirect' => route('profile.edit')
                    ], 422);
                }
                
                // Check schedule configuration
                if (!$user->schedule_start || !$user->schedule_end || !$user->work_days) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Para habilitar las reservas públicas, primero debes configurar tus horarios de disponibilidad.',
                        'redirect' => route('profile.schedule')
                    ], 422);
                }
                
                // Validate work_days is not empty
                $workDays = json_decode($user->work_days, true);
                if (empty($workDays)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Debes seleccionar al menos un día de trabajo.',
                        'redirect' => route('profile.schedule')
                    ], 422);
                }
            }

            // Generate unique booking slug if not exists
            if (!$user->booking_slug) {
                $baseSlug = Str::slug($user->name);
                $slug = $baseSlug;
                $counter = 1;
                
                while (User::where('booking_slug', $slug)->where('id', '!=', $user->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $user->booking_slug = $slug;
            }
            
            $user->booking_enabled = true;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Reservas públicas habilitadas exitosamente',
                'data' => [
                    'booking_slug' => $user->booking_slug,
                    'booking_url' => url('/booking/' . $user->booking_slug)
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error enabling booking: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al habilitar las reservas'
            ], 500);
        }
    }
} 