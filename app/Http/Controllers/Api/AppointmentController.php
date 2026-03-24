<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor', 'clinic', 'videoCall']);

        // Non-admin users see limited appointment data
        $user = auth()->user();
        if ($user->role !== 'admin') {
            // Get doctor record for this user
            $doctor = $user->doctor;
            if ($doctor) {
                // Only show appointments for this doctor
                $query->where('doctor_id', $doctor->id);
            } else {
                // If user has no doctor record but has access, show limited data based on role
                if ($user->role === 'receptionist') {
                    // Receptionists can see appointments for scheduling purposes but limited time range
                    $query->whereBetween('date_time', [now()->subDays(1), now()->addDays(7)]);
                } elseif ($user->role === 'nurse') {
                    // Nurses can see today's and tomorrow's appointments only
                    $query->whereBetween('date_time', [now()->startOfDay(), now()->addDay()->endOfDay()]);
                } else {
                    // Other roles see no appointments
                    $query->where('id', -1);
                }
            }
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by doctor (only for admin and receptionist)
        if ($request->has('doctor_id') && ($user->role === 'admin' || $user->role === 'receptionist')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Filter by patient
        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('date_time', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('date_time', '<=', $request->to_date);
        }

        // Filter by date (for single date)
        if ($request->has('date')) {
            $query->whereDate('date_time', $request->date);
        }

        // Search by patient or doctor name, or reason
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($patientQuery) use ($search) {
                    $patientQuery->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('doctor', function ($doctorQuery) use ($search) {
                    $doctorQuery->where('name', 'like', '%' . $search . '%');
                })->orWhere('reason', 'like', '%' . $search . '%');
            });
        }

        $appointments = $query->orderBy('date_time', 'desc')
            ->paginate($request->get('per_page', 25));

        // Transform the data to include video call information
        $appointments->getCollection()->transform(function ($appointment) {
            // Add formatted fields for frontend compatibility
            $appointment->date = $appointment->appointment_date ?? $appointment->date_time->format('Y-m-d');
            $appointment->time = $appointment->appointment_time ?? $appointment->date_time->format('H:i');
            $appointment->patient_name = $appointment->patient->name ?? $appointment->patient_name;
            $appointment->doctor_name = $appointment->doctor->name ?? 'No asignado';
            $appointment->patient_phone = $appointment->patient->phone ?? '';

            // Include video call with formatted duration
            if ($appointment->videoCall) {
                $appointment->video_call = $appointment->videoCall;
                $appointment->video_call->formatted_duration = $appointment->videoCall->formatted_duration;
            }

            return $appointment;
        });

        return response()->json($appointments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        // If user is not admin, set doctor_id to their own doctor record
        if ($user->role !== 'admin' && $user->role !== 'receptionist') {
            if ($user->role === 'doctor') {
                $request->merge(['doctor_id' => $user->id]);
            } else {
                return response()->json([
                    'message' => 'No tienes permisos para crear citas',
                    'errors' => ['doctor_id' => ['Usuario no autorizado']]
                ], 403);
            }
        }

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'clinic_id' => 'nullable|exists:clinics,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'duration' => 'nullable|integer|min:15|max:480',
            'type' => 'nullable|string',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // Check availability using unified logic
        $availabilityRequest = new Request([
            'doctor_id' => $request->doctor_id,
            'date' => $request->appointment_date,
            'time' => $request->appointment_time,
            'duration' => $request->duration ?? 30
        ]);

        $availabilityResponse = $this->checkAvailability($availabilityRequest);
        $availabilityData = json_decode($availabilityResponse->getContent(), true);

        if (!$availabilityData['available']) {
            return response()->json([
                'message' => $availabilityData['message'] ?? 'Doctor no disponible en el horario seleccionado',
                'errors' => ['appointment_time' => [$availabilityData['message']]]
            ], 422);
        }

        // Resolve the actual doctors.id from the user_id
        // appointments.doctor_id references the doctors table, not users
        $doctorUser = \App\Models\User::find($request->doctor_id);
        $doctorRecord = $doctorUser?->doctor;

        // Auto-create doctors record if missing (uses profile data from users table)
        if (!$doctorRecord && $doctorUser) {
            $doctorRecord = \App\Models\Doctor::create([
                'user_id'          => $doctorUser->id,
                'name'             => trim(($doctorUser->first_name ?? '') . ' ' . ($doctorUser->last_name ?? '')),
                'specialty'        => $doctorUser->specialty ?? 'General',
                'license_number'   => $doctorUser->medical_license ?? 'LIC-' . $doctorUser->id,
                'email'            => $doctorUser->email,
                'phone'            => $doctorUser->phone ?? '',
                'address'          => $doctorUser->address ?? '',
                'experience_years' => $doctorUser->years_experience ?? 0,
                'education'        => json_encode([]),
                'certifications'   => json_encode([]),
                'languages'        => json_encode(['Español']),
                'status'           => 'active',
            ]);
        }

        if (!$doctorRecord) {
            return response()->json([
                'message' => 'No se pudo resolver el perfil del doctor.',
                'errors' => ['doctor_id' => ['Perfil de doctor no encontrado']]
            ], 422);
        }
        $resolvedDoctorId = $doctorRecord->id;

        // Get doctor's clinic if not provided
        $clinicId = $request->clinic_id;
        if (!$clinicId) {
            $clinicId = $doctorUser->clinic_id ?? 1; // Default clinic
        }

        // Create appointment with unified format
        $appointmentData = [
            'patient_id' => $request->patient_id,
            'doctor_id' => $resolvedDoctorId,
            'clinic_id' => $clinicId,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'duration' => $request->duration ?? 30,
            'type' => $request->type ?? 'consultation',
            'reason' => $request->reason,
            'notes' => $request->notes,
            'status' => 'scheduled'
        ];

        // Also set date_time for backward compatibility
        $appointmentData['date_time'] = $request->appointment_date . ' ' . $request->appointment_time;

        $appointment = Appointment::create($appointmentData);

        // Ensure doctor-patient relationship exists
        if ($doctorRecord) {
            $doctorRecord->ensurePatientRelationship($request->patient_id, 'consulting');
        }

        return response()->json([
            'success' => true,
            'message' => 'Cita creada exitosamente',
            'data' => $appointment->load(['patient', 'doctor', 'clinic'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);

        // Check if user can access this appointment
        $user = auth()->user();
        if ($user->role !== 'admin') {
            $doctor = $user->doctor;
            if (!$doctor || $appointment->doctor_id !== $doctor->id) {
                return response()->json([
                    'message' => 'No tienes permisos para ver esta cita'
                ], 403);
            }
        }

        $appointment->load(['patient', 'doctor', 'clinic', 'videoCall']);

        // Add formatted fields for frontend compatibility
        $appointment->date = $appointment->appointment_date ?? $appointment->date_time->format('Y-m-d');
        $appointment->time = $appointment->appointment_time ?? $appointment->date_time->format('H:i');
        $appointment->patient_name = $appointment->patient->name ?? $appointment->patient_name;
        $appointment->doctor_name = $appointment->doctor->name ?? 'No asignado';
        $appointment->patient_phone = $appointment->patient->phone ?? '';

        // Include video call with formatted duration
        if ($appointment->videoCall) {
            $appointment->video_call = $appointment->videoCall;
            $appointment->video_call->formatted_duration = $appointment->videoCall->formatted_duration;
        }

        return response()->json([
            'success' => true,
            'data' => $appointment
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);

        // Check if user can update this appointment
        $user = auth()->user();
        if ($user->role !== 'admin') {
            $doctor = $user->doctor;
            if (!$doctor || $appointment->doctor_id !== $doctor->id) {
                return response()->json([
                    'message' => 'No tienes permisos para editar esta cita'
                ], 403);
            }
            // Non-admin users cannot change doctor_id
            $request->request->remove('doctor_id');
        }

        $request->validate([
            'patient_id' => 'exists:patients,id',
            'doctor_id' => 'exists:doctors,id',
            'clinic_id' => 'exists:clinics,id',
            'date_time' => 'date',
            'duration' => 'nullable|integer|min:15|max:480',
            'type' => 'string',
            'reason' => 'string',
            'notes' => 'nullable|string',
            'status' => 'in:scheduled,completed,cancelled,pending',
        ]);

        // Check for conflicts only if date_time or doctor_id is being changed
        if ($request->has('date_time') || $request->has('doctor_id')) {
            $doctorId = $request->doctor_id ?? $appointment->doctor_id;
            $dateTime = $request->date_time ?? $appointment->date_time;
            $duration = $request->duration ?? $appointment->duration;

            $conflictingAppointment = Appointment::where('doctor_id', $doctorId)
                ->where('id', '!=', $appointment->id)
                ->where('status', 'scheduled')
                ->where(function($query) use ($dateTime, $duration) {
                    $appointmentStart = $dateTime;
                    $appointmentEnd = date('Y-m-d H:i:s', strtotime($appointmentStart . ' +' . $duration . ' minutes'));

                    $query->whereBetween('date_time', [$appointmentStart, $appointmentEnd])
                        ->orWhere(function($q) use ($appointmentStart, $appointmentEnd) {
                            $q->where('date_time', '<=', $appointmentStart)
                              ->whereRaw('date_time + INTERVAL \'1 minute\' * COALESCE(duration, 30) > ?', [$appointmentStart]);
                        });
                })
                ->first();

            if ($conflictingAppointment) {
                return response()->json([
                    'message' => 'Doctor is not available at the selected time',
                    'errors' => ['date_time' => ['The selected time slot conflicts with another appointment']]
                ], 422);
            }
        }

        $appointment->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Cita actualizada exitosamente',
            'data' => $appointment->load(['patient', 'doctor', 'clinic'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);

        // Check if user can delete this appointment
        $user = auth()->user();
        if ($user->role !== 'admin') {
            $doctor = $user->doctor;
            if (!$doctor || $appointment->doctor_id !== $doctor->id) {
                return response()->json([
                    'message' => 'No tienes permisos para eliminar esta cita'
                ], 403);
            }
        }

        $appointment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cita eliminada exitosamente'
        ]);
    }

    /**
     * Get today's appointments.
     */
    public function today(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor', 'clinic'])
            ->whereDate('date_time', today());

        // If user is not admin, only show their own appointments
        $user = auth()->user();
        if ($user->role !== 'admin') {
            $doctor = $user->doctor;
            if ($doctor) {
                $query->where('doctor_id', $doctor->id);
            } else {
                $query->where('id', -1); // Return empty result
            }
        }

        // Filter by clinic if specified
        if ($request->has('clinic_id')) {
            $query->where('clinic_id', $request->clinic_id);
        }

        // Filter by doctor if specified (only for admin)
        if ($request->has('doctor_id') && $user->role === 'admin') {
            $query->where('doctor_id', $request->doctor_id);
        }

        $appointments = $query->orderBy('date_time')
            ->get();

        return response()->json($appointments);
    }

    /**
     * Update appointment status.
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:scheduled,completed,cancelled,pending',
            'notes' => 'nullable|string',
        ]);

        $appointment->update([
            'status' => $request->status,
            'notes' => $request->notes ?? $appointment->notes,
        ]);

        return response()->json($appointment->load(['patient', 'doctor', 'clinic']));
    }

    /**
     * Get appointment statistics
     */
    public function stats(): JsonResponse
    {
        $user = auth()->user();
        $query = DB::table('appointments');

        // Filter by user role
        if ($user->role === 'doctor') {
            $query->where('doctor_id', $user->id);
        } elseif ($user->role === 'nurse' || $user->role === 'receptionist') {
            // Can see all appointments in their clinic/location
            if ($user->clinic_id) {
                $query->whereIn('doctor_id', function($subQuery) use ($user) {
                    $subQuery->select('id')
                            ->from('users')
                            ->where('clinic_id', $user->clinic_id);
                });
            }
        }
        // Admin can see all stats

        $today = now()->format('Y-m-d');

        $stats = [
            'today' => (clone $query)->whereDate('appointment_date', $today)->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'pending' => (clone $query)->where('status', 'scheduled')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            'total' => (clone $query)->count(),
            'this_week' => (clone $query)->whereBetween('appointment_date', [
                now()->startOfWeek()->format('Y-m-d'),
                now()->endOfWeek()->format('Y-m-d')
            ])->count(),
            'this_month' => (clone $query)->whereMonth('appointment_date', now()->month)
                                        ->whereYear('appointment_date', now()->year)
                                        ->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Check availability for appointment scheduling
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time' => 'required',
            'duration' => 'nullable|integer|min:15|max:480',
            'exclude_appointment_id' => 'nullable|exists:appointments,id'
        ]);

        $userId = $request->doctor_id; // This is actually the user_id
        $date = $request->date;
        $time = $request->time;
        $duration = $request->duration ?? 30;
        $excludeId = $request->exclude_appointment_id;

        // Get the doctor record to find the actual doctor_id
        $user = \App\Models\User::find($userId);
        if (!$user || $user->role !== 'doctor') {
            return response()->json([
                'available' => false,
                'message' => 'Doctor no encontrado'
            ]);
        }

        $doctorRecord = $user->doctor;
        $doctorId = $doctorRecord?->id;

        $conflictingAppointment = null;

        if ($doctorId) {
            // Combine date and time
            $dateTime = $date . ' ' . $time;
            $appointmentStart = $dateTime;
            $appointmentEnd = date('Y-m-d H:i:s', strtotime($appointmentStart . ' +' . $duration . ' minutes'));

            // Check for conflicts in both date_time and appointment_date/appointment_time formats
            $conflictQuery = Appointment::where('doctor_id', $doctorId)
                ->where('status', '!=', 'cancelled')
                ->where(function($query) use ($appointmentStart, $appointmentEnd, $date, $time, $duration) {
                    // Check old format (date_time)
                    $query->where(function($q) use ($appointmentStart, $appointmentEnd) {
                        $q->whereNotNull('date_time')
                          ->where(function($subQ) use ($appointmentStart, $appointmentEnd) {
                              $subQ->whereBetween('date_time', [$appointmentStart, $appointmentEnd])
                                   ->orWhere(function($conflictQ) use ($appointmentStart, $appointmentEnd) {
                                       $conflictQ->where('date_time', '<=', $appointmentStart)
                                                ->whereRaw("date_time + INTERVAL '1 minute' * COALESCE(duration, 30) > ?", [$appointmentStart]);
                                   });
                          });
                    })
                    // Check new format (appointment_date + appointment_time)
                    ->orWhere(function($q) use ($date, $time, $duration) {
                        $q->where('appointment_date', $date)
                          ->where('appointment_time', $time);
                    });
                });

            if ($excludeId) {
                $conflictQuery->where('id', '!=', $excludeId);
            }

            $conflictingAppointment = $conflictQuery->first();
        }

        if ($conflictingAppointment) {
            return response()->json([
                'available' => false,
                'message' => 'El horario seleccionado no está disponible',
                'conflict' => [
                    'appointment_id' => $conflictingAppointment->id,
                    'patient_name' => $conflictingAppointment->patient ?
                        $conflictingAppointment->patient->first_name . ' ' . $conflictingAppointment->patient->last_name :
                        'Paciente',
                    'time' => $conflictingAppointment->appointment_time ??
                        \Carbon\Carbon::parse($conflictingAppointment->date_time)->format('H:i')
                ]
            ]);
        }

        // Verify doctor's schedule
        if ($user->work_days) {
            $dayOfWeek = strtolower(\Carbon\Carbon::parse($date)->format('l'));
            $workDays = json_decode($user->work_days, true) ?? [];

            if (!in_array($dayOfWeek, $workDays)) {
                return response()->json([
                    'available' => false,
                    'message' => 'El doctor no trabaja este día de la semana'
                ]);
            }

            // Check work hours
            $scheduleStart = $user->schedule_start ?? '08:00';
            $scheduleEnd = $user->schedule_end ?? '17:00';

            if ($time < $scheduleStart || $time >= $scheduleEnd) {
                return response()->json([
                    'available' => false,
                    'message' => 'El horario está fuera del horario de trabajo del doctor'
                ]);
            }
        }

        return response()->json([
            'available' => true,
            'message' => 'Horario disponible'
        ]);
    }

    /**
     * Get available time slots for a doctor on a specific date
     */
    public function getAvailableSlots(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'doctor_id' => 'required|exists:users,id',
                'date' => 'required|date|after_or_equal:today'
            ]);

            $userId = $request->doctor_id; // Este es el user_id, no el doctor_id
            $date = $request->date;

            $user = \App\Models\User::find($userId);
            if (!$user || $user->role !== 'doctor') {
                return response()->json([
                    'success' => false,
                    'message' => 'Doctor no encontrado'
                ], 404);
            }

            // Check if doctor works on this day
            $dayOfWeek = strtolower(\Carbon\Carbon::parse($date)->format('l'));

            // Safe JSON decode
            $workDays = [];
            try {
                $workDays = json_decode($user->work_days ?? '[]', true) ?? [];
            } catch (\Exception $e) {
                $workDays = [];
            }

            // If no work days are set, default to a standard work week
            if (empty($workDays) || !is_array($workDays)) {
                $workDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
            }

            if (!in_array($dayOfWeek, $workDays)) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            // Generate time slots with safe parsing
            try {
                $startTime = \Carbon\Carbon::parse($user->schedule_start ?? '08:00');
                $endTime = \Carbon\Carbon::parse($user->schedule_end ?? '17:00');
            } catch (\Exception $e) {
                $startTime = \Carbon\Carbon::parse('08:00');
                $endTime = \Carbon\Carbon::parse('17:00');
            }

            $consultationDuration = intval($user->consultation_duration ?? 30);

            // Safe break time parsing
            $breakStart = null;
            $breakEnd = null;

            if ($user->break_start) {
                try {
                    $breakStart = \Carbon\Carbon::parse($user->break_start);
                } catch (\Exception $e) {
                    $breakStart = null;
                }
            }

            if ($user->break_end) {
                try {
                    $breakEnd = \Carbon\Carbon::parse($user->break_end);
                } catch (\Exception $e) {
                    $breakEnd = null;
                }
            }

            $slots = [];

            while ($startTime < $endTime) {
                $slotTime = $startTime->format('H:i');
                $slotEndTime = $startTime->copy()->addMinutes($consultationDuration);

                // Skip slots that overlap with break time
                if ($breakStart && $breakEnd) {
                    // Check if slot overlaps with break time
                    if ($startTime < $breakEnd && $slotEndTime > $breakStart) {
                        $startTime->addMinutes($consultationDuration);
                        continue;
                    }
                }

                // Check availability for this slot
                try {
                    $availabilityRequest = new Request([
                        'doctor_id' => $userId, // Usar user_id, no doctor_id
                        'date' => $date,
                        'time' => $slotTime,
                        'duration' => $consultationDuration
                    ]);

                    $availabilityResponse = $this->checkAvailability($availabilityRequest);
                    $availabilityData = json_decode($availabilityResponse->getContent(), true);

                    if ($availabilityData && isset($availabilityData['available']) && $availabilityData['available']) {
                        $slots[] = [
                            'time' => $slotTime,
                            'display_time' => $startTime->format('g:i A'),
                            'available' => true
                        ];
                    }
                } catch (\Exception $e) {
                    // Skip this slot if availability check fails
                    continue;
                }

                $startTime->addMinutes($consultationDuration);
            }

            return response()->json([
                'success' => true,
                'data' => $slots
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar horarios disponibles',
                'data' => []
            ], 500);
        }
    }
}
