<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Obtener información del proveedor (médico o clínica)
     */
    public function getProviderInfo($slug)
    {
        try {
            // Buscar en usuarios (médicos independientes - Plan Free/Basic/Doctor)
            $user = DB::table('users')
                ->where('booking_slug', $slug)
                ->where('status', 'active')
                ->where('role', 'doctor')
                ->where('booking_enabled', true)
                ->first();

            if ($user) {
                // Obtener el plan de suscripción del usuario
                $subscription = DB::table('user_subscriptions')
                    ->join('subscription_plans', 'user_subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
                    ->where('user_subscriptions.user_id', $user->id)
                    ->where('user_subscriptions.status', 'active')
                    ->select('subscription_plans.slug as plan_slug', 'subscription_plans.name as plan_name')
                    ->first();

                // Determinar si es un plan individual (free, basic, doctor) o clínica
                $isIndividualPlan = !$subscription || in_array($subscription->plan_slug, ['free', 'doctor']);

                if ($isIndividualPlan) {
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'type' => 'doctor',
                            'id' => $user->id,
                            'name' => $user->name,
                            'specialty' => $user->specialty ?? 'Medicina General',
                            'bio' => $user->bio ?? 'Médico profesional disponible para consultas.',
                            'phone' => $user->phone ?? 'No disponible',
                            'email' => $user->email,
                            'schedule_start' => $user->schedule_start ?? '08:00',
                            'schedule_end' => $user->schedule_end ?? '17:00',
                            'work_days' => json_decode($user->work_days ?? '["monday","tuesday","wednesday","thursday","friday"]'),
                            'consultation_fee' => $user->consultation_fee ?? 0,
                            'booking_enabled' => true,
                            'plan_type' => $subscription->plan_slug ?? 'free',
                            'plan_name' => $subscription->plan_name ?? 'Plan Gratuito'
                        ]
                    ]);
                }
            }

            // Buscar en clínicas (Plan Clínica)
            $clinic = DB::table('clinics')
                ->where('booking_slug', $slug)
                ->where('status', 'active')
                ->where('booking_enabled', true)
                ->first();

            if ($clinic) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'type' => 'clinic',
                        'id' => $clinic->id,
                        'name' => $clinic->name,
                        'description' => $clinic->description ?? 'Centro médico profesional',
                        'phone' => $clinic->phone,
                        'email' => $clinic->email,
                        'website' => $clinic->website ?? null,
                        'booking_enabled' => true,
                        'has_multiple_locations' => $clinic->has_multiple_locations ?? false,
                        'plan_type' => 'clinic'
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Proveedor no encontrado o no disponible para reservas'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error in getProviderInfo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener información del proveedor'
            ], 500);
        }
    }

    /**
     * Obtener sucursales de una clínica
     */
    public function getLocations($slug)
    {
        try {
            $clinic = DB::table('clinics')
                ->where('booking_slug', $slug)
                ->where('status', 'active')
                ->first();

            if (!$clinic) {
                return response()->json([
                    'success' => false,
                    'message' => 'Clínica no encontrada'
                ], 404);
            }

            // Obtener sucursales (locations) de la clínica
            $locations = DB::table('clinic_locations')
                ->where('clinic_id', $clinic->id)
                ->where('status', 'active')
                ->select('id', 'name', 'address', 'phone', 'schedule_start', 'schedule_end', 'work_days')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $locations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener sucursales'
            ], 500);
        }
    }

    /**
     * Obtener médicos disponibles
     */
    public function getDoctors(Request $request, $slug)
    {
        try {
            $locationId = $request->get('location_id');
            $specialty = $request->get('specialty');

            // Para médico independiente (Plan Free/Basic)
            $user = DB::table('users')
                ->where('booking_slug', $slug)
                ->where('role', 'doctor')
                ->where('status', 'active')
                ->where('booking_enabled', true)
                ->first();

            if ($user) {
                return response()->json([
                    'success' => true,
                    'data' => [[
                        'id' => $user->id,
                        'name' => $user->name,
                        'specialty' => $user->specialty ?? 'Medicina General',
                        'bio' => $user->bio ?? 'Médico profesional disponible para consultas.',
                        'consultation_fee' => $user->consultation_fee ?? 0,
                        'schedule_start' => $user->schedule_start ?? '08:00',
                        'schedule_end' => $user->schedule_end ?? '17:00',
                        'work_days' => json_decode($user->work_days ?? '["monday","tuesday","wednesday","thursday","friday"]')
                    ]]
                ]);
            }

            // Para clínica (Plan Clínica) - buscar en tabla de doctores separada
            $clinic = DB::table('clinics')
                ->where('booking_slug', $slug)
                ->where('status', 'active')
                ->where('booking_enabled', true)
                ->first();

            if (!$clinic) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proveedor no encontrado'
                ], 404);
            }

            // Para clínicas, buscar doctores asociados en tabla separada 'doctors'
            // (Esta tabla existiría solo para planes Clínica)
            $query = DB::table('doctors')
                ->where('clinic_id', $clinic->id)
                ->where('status', 'active');

            if ($locationId) {
                $query->where('location_id', $locationId);
            }

            if ($specialty) {
                $query->where('specialty', $specialty);
            }

            $doctors = $query->select(
                'id', 'name', 'specialty', 'bio', 
                'consultation_fee', 'schedule_start', 'schedule_end', 'work_days'
            )->get();

            return response()->json([
                'success' => true,
                'data' => $doctors->map(function($doctor) {
                    return [
                        'id' => $doctor->id,
                        'name' => $doctor->name,
                        'specialty' => $doctor->specialty ?? 'Medicina General',
                        'bio' => $doctor->bio ?? 'Médico profesional especializado.',
                        'consultation_fee' => $doctor->consultation_fee ?? 0,
                        'schedule_start' => $doctor->schedule_start ?? '08:00',
                        'schedule_end' => $doctor->schedule_end ?? '17:00',
                        'work_days' => json_decode($doctor->work_days ?? '["monday","tuesday","wednesday","thursday","friday"]')
                    ];
                })
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getDoctors: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener médicos'
            ], 500);
        }
    }

    /**
     * Obtener horarios disponibles
     */
    public function getAvailability(Request $request, $slug)
    {
        try {
            $doctorId = $request->get('doctor_id');
            $date = $request->get('date');

            if (!$doctorId || !$date) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doctor ID y fecha son requeridos'
                ], 400);
            }

            // Primero verificar si es un médico independiente (Plan Free/Basic)
            $user = DB::table('users')
                ->where('id', $doctorId)
                ->where('role', 'doctor')
                ->where('status', 'active')
                ->where('booking_enabled', true)
                ->first();

            if ($user) {
                // Es un médico independiente - usar lógica de disponibilidad del sistema interno
                $appointmentController = new \App\Http\Controllers\Api\AppointmentController();
                $availabilityRequest = new \Illuminate\Http\Request([
                    'doctor_id' => $doctorId,
                    'date' => $date
                ]);

                $response = $appointmentController->getAvailableSlots($availabilityRequest);
                $responseData = json_decode($response->getContent(), true);

                return response()->json($responseData);
            }

            // Si no es usuario, buscar en tabla de doctores (Plan Clínica)
            $doctor = DB::table('doctors')
                ->where('id', $doctorId)
                ->where('status', 'active')
                ->first();

            if (!$doctor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doctor no encontrado'
                ], 404);
            }

            // Para doctores de clínica, implementar lógica de disponibilidad específica
            // Por ahora, usar horarios básicos
            $workDays = json_decode($doctor->work_days ?? '["monday","tuesday","wednesday","thursday","friday"]');
            $scheduleStart = $doctor->schedule_start ?? '08:00';
            $scheduleEnd = $doctor->schedule_end ?? '17:00';
            $consultationDuration = $doctor->consultation_duration ?? 30;
            $breakStart = $doctor->break_start;
            $breakEnd = $doctor->break_end;
            
            $dayOfWeek = strtolower(date('l', strtotime($date)));
            
            if (!in_array($dayOfWeek, $workDays)) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            // Generar slots basados en la duración de consulta configurada
            $slots = [];
            $start = strtotime($date . ' ' . $scheduleStart);
            $end = strtotime($date . ' ' . $scheduleEnd);
            $slotDuration = $consultationDuration * 60; // Convert minutes to seconds
            
            for ($time = $start; $time < $end; $time += $slotDuration) {
                $timeSlot = date('H:i', $time);
                $timeSlotEnd = date('H:i', $time + $slotDuration);
                
                // Skip slots during break time
                if ($breakStart && $breakEnd) {
                    $breakStartTime = strtotime($date . ' ' . $breakStart);
                    $breakEndTime = strtotime($date . ' ' . $breakEnd);
                    
                    // Check if slot overlaps with break time
                    if ($time < $breakEndTime && ($time + $slotDuration) > $breakStartTime) {
                        continue;
                    }
                }
                
                // Verificar si el slot está ocupado
                $isOccupied = DB::table('appointments')
                    ->where('doctor_id', $doctorId)
                    ->where('appointment_date', $date)
                    ->where('appointment_time', $timeSlot)
                    ->where('status', '!=', 'cancelled')
                    ->exists();
                
                if (!$isOccupied) {
                    $slots[] = [
                        'time' => $timeSlot,
                        'display_time' => $timeSlot,
                        'available' => true
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $slots
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getAvailability: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener horarios disponibles'
            ], 500);
        }
    }

    /**
     * Crear reserva pública
     */
    public function createReservation(Request $request, $slug)
    {
        try {
            \Log::info('Starting reservation creation', [
                'slug' => $slug,
                'request_data' => $request->all()
            ]);

            $request->validate([
                'doctor_id' => 'required|integer',
                'appointment_date' => 'required|date|after_or_equal:today',
                'appointment_time' => 'required',
                'patient_name' => 'required|string|max:255',
                'patient_phone' => 'required|string|max:20',
                'patient_email' => 'required|email|max:255',
                'reason' => 'required|string|max:500',
                'patient_age' => 'nullable|integer|min:1|max:120',
                'patient_gender' => 'nullable|in:male,female,other'
            ]);

            \Log::info('Validation passed');

            // Verificar que el doctor existe y está disponible
            // Primero buscar en usuarios (Plan Free/Basic)
            $user = DB::table('users')
                ->where('id', $request->doctor_id)
                ->where('role', 'doctor')
                ->where('status', 'active')
                ->where('booking_enabled', true)
                ->first();

            $isDoctorUser = true; // Flag para saber si es usuario o doctor de clínica
            $actualDoctorId = null; // ID real del doctor en la tabla doctors

            \Log::info('Doctor search in users table', [
                'doctor_id' => $request->doctor_id,
                'found' => $user ? true : false
            ]);

            if ($user) {
                // Es un usuario doctor individual - buscar o crear registro en tabla doctors
                $doctorRecord = DB::table('doctors')
                    ->where('user_id', $user->id)
                    ->first();
                
                if (!$doctorRecord) {
                    // Crear registro en tabla doctors para el usuario
                    \Log::info('Creating doctor record for user');
                    
                    $actualDoctorId = DB::table('doctors')->insertGetId([
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'specialty' => $user->specialty ?? 'Medicina General',
                        'license_number' => 'LIC-' . $user->id . '-' . time(),
                        'email' => $user->email,
                        'phone' => $user->phone ?? 'No disponible',
                        'address' => 'Dirección no especificada',
                        'experience_years' => 5, // Default
                        'education' => json_encode(['Título de Médico Cirujano']),
                        'certifications' => json_encode([]),
                        'languages' => json_encode(['Español']),
                        'status' => 'active',
                        'bio' => $user->bio,
                        'rating' => 5.00,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                } else {
                    $actualDoctorId = $doctorRecord->id;
                }
                
                \Log::info('Using doctor record', ['doctor_id' => $actualDoctorId]);
            } else {
                // Si no es usuario, buscar en tabla de doctores (Plan Clínica)
                $doctor = DB::table('doctors')
                    ->where('id', $request->doctor_id)
                    ->where('status', 'active')
                    ->first();
                
                $isDoctorUser = false;
                $actualDoctorId = $doctor ? $doctor->id : null;
                
                \Log::info('Doctor search in doctors table', [
                    'found' => $doctor ? true : false
                ]);
            }

            if (!$actualDoctorId) {
                \Log::warning('Doctor not found', ['doctor_id' => $request->doctor_id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Doctor no encontrado'
                ], 404);
            }

            \Log::info('Doctor found', [
                'original_doctor_id' => $request->doctor_id,
                'actual_doctor_id' => $actualDoctorId,
                'is_user' => $isDoctorUser
            ]);

            // Use the same availability checking logic as internal system for users
            // For clinic doctors, implement basic availability check
            if ($isDoctorUser) {
                \Log::info('Checking availability for user doctor');
                
                $appointmentController = new \App\Http\Controllers\Api\AppointmentController();
                $availabilityRequest = new \Illuminate\Http\Request([
                    'doctor_id' => $request->doctor_id, // Use original user ID for availability check
                    'date' => $request->appointment_date,
                    'time' => $request->appointment_time,
                    'duration' => 30
                ]);

                $availabilityResponse = $appointmentController->checkAvailability($availabilityRequest);
                $availabilityData = json_decode($availabilityResponse->getContent(), true);

                \Log::info('Availability check result', $availabilityData);

                if (!$availabilityData['available']) {
                    return response()->json([
                        'success' => false,
                        'message' => $availabilityData['message'] ?? 'Este horario ya no está disponible'
                    ], 409);
                }
            } else {
                \Log::info('Checking availability for clinic doctor');
                
                // Para doctores de clínica, verificación básica de disponibilidad
                $isOccupied = DB::table('appointments')
                    ->where('doctor_id', $actualDoctorId)
                    ->whereRaw('DATE(date_time) = ?', [$request->appointment_date])
                    ->whereRaw('TIME(date_time) = ?', [$request->appointment_time . ':00'])
                    ->where('status', '!=', 'cancelled')
                    ->exists();
                
                if ($isOccupied) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Este horario ya no está disponible'
                    ], 409);
                }
            }

            \Log::info('Availability check passed, creating patient');

            // Buscar o crear paciente
            $patient = DB::table('patients')
                ->where('email', $request->patient_email)
                ->orWhere('phone', $request->patient_phone)
                ->first();

            if (!$patient) {
                \Log::info('Creating new patient');
                
                // Generate a temporary DNI if not provided (for public bookings)
                $tempDni = 'PUB-' . time() . '-' . rand(1000, 9999);
                
                $patientId = DB::table('patients')->insertGetId([
                    'name' => $request->patient_name,
                    'dni' => $tempDni,
                    'birth_date' => $request->patient_age ? 
                        now()->subYears($request->patient_age)->format('Y-m-d') : 
                        now()->subYears(30)->format('Y-m-d'), // Default age if not provided
                    'gender' => $request->patient_gender ?? 'other',
                    'address' => 'Dirección no especificada', // Default address for public bookings
                    'phone' => $request->patient_phone,
                    'email' => $request->patient_email,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                \Log::info('Patient created', ['patient_id' => $patientId]);
            } else {
                $patientId = $patient->id;
                \Log::info('Using existing patient', ['patient_id' => $patientId]);
            }

            // Get doctor's clinic_id - fix for user doctors
            $clinicId = 1; // Default clinic
            if ($isDoctorUser) {
                // For user doctors, use default clinic or get from user profile if available
                $clinicId = $user->clinic_id ?? 1;
                
                // Ensure the clinic exists, create default if needed
                $clinicExists = DB::table('clinics')->where('id', $clinicId)->exists();
                if (!$clinicExists) {
                    \Log::info('Creating default clinic');
                    $clinicId = DB::table('clinics')->insertGetId([
                        'name' => 'Clínica Principal',
                        'address' => 'Dirección no especificada',
                        'phone' => 'Teléfono no especificado',
                        'email' => 'info@clinica.com',
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            } else {
                // For clinic doctors, get clinic_id from doctors table
                $doctorRecord = DB::table('doctors')->where('id', $actualDoctorId)->first();
                $clinicId = $doctorRecord->clinic_id ?? 1;
            }

            \Log::info('Creating appointment', [
                'patient_id' => $patientId,
                'doctor_id' => $actualDoctorId,
                'clinic_id' => $clinicId
            ]);

            // Crear la cita
            $confirmationToken = \Illuminate\Support\Str::random(32);
            
            // Combine date and time into datetime format
            $dateTime = $request->appointment_date . ' ' . $request->appointment_time . ':00';
            
            $appointmentId = DB::table('appointments')->insertGetId([
                'patient_id' => $patientId,
                'doctor_id' => $actualDoctorId,
                'clinic_id' => $clinicId,
                'date_time' => $dateTime,
                'duration' => 30,
                'type' => 'public_booking',
                'reason' => $request->reason,
                'notes' => 'Confirmation Token: ' . $confirmationToken,
                'status' => 'scheduled',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            \Log::info('Appointment created successfully', [
                'appointment_id' => $appointmentId,
                'confirmation_token' => $confirmationToken
            ]);

            // TODO: Enviar email de confirmación

            return response()->json([
                'success' => true,
                'message' => 'Cita reservada exitosamente',
                'data' => [
                    'appointment_id' => $appointmentId,
                    'confirmation_token' => $confirmationToken,
                    'confirmation_url' => route('booking.confirmation', [
                        'slug' => $slug,
                        'token' => $confirmationToken
                    ])
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in createReservation', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in createReservation', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la reserva: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Página de confirmación
     */
    public function confirmation($slug, $token)
    {
        $appointment = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->where('appointments.notes', 'like', '%Confirmation Token: ' . $token . '%')
            ->select(
                'appointments.*',
                'patients.name as patient_name',
                'patients.email as patient_email',
                'patients.phone as patient_phone',
                'doctors.name as doctor_name',
                'doctors.specialty as doctor_specialty'
            )
            ->first();

        if (!$appointment) {
            abort(404, 'Cita no encontrada');
        }

        return view('public.booking.confirmation', compact('appointment', 'slug'));
    }
} 