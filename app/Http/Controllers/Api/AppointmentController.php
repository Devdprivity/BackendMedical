<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor', 'clinic']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('date_time', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('date_time', '<=', $request->to_date);
        }

        // Filter by doctor
        if ($request->has('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Filter by clinic
        if ($request->has('clinic_id')) {
            $query->where('clinic_id', $request->clinic_id);
        }

        // Search by patient name
        if ($request->has('search')) {
            $query->whereHas('patient', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $appointments = $query->orderBy('date_time', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($appointments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'clinic_id' => 'required|exists:clinics,id',
            'date_time' => 'required|date|after:now',
            'duration' => 'nullable|integer|min:15|max:480',
            'type' => 'required|string',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // Check for scheduling conflicts
        $conflictingAppointment = Appointment::where('doctor_id', $request->doctor_id)
            ->where('status', 'scheduled')
            ->where(function($query) use ($request) {
                $appointmentStart = $request->date_time;
                $appointmentEnd = date('Y-m-d H:i:s', strtotime($appointmentStart . ' +' . ($request->duration ?? 30) . ' minutes'));
                
                $query->whereBetween('date_time', [$appointmentStart, $appointmentEnd])
                    ->orWhere(function($q) use ($appointmentStart, $appointmentEnd) {
                        $q->where('date_time', '<=', $appointmentStart)
                          ->whereRaw('DATE_ADD(date_time, INTERVAL duration MINUTE) > ?', [$appointmentStart]);
                    });
            })
            ->first();

        if ($conflictingAppointment) {
            return response()->json([
                'message' => 'Doctor is not available at the selected time',
                'errors' => ['date_time' => ['The selected time slot conflicts with another appointment']]
            ], 422);
        }

        $appointment = Appointment::create($request->all());

        return response()->json($appointment->load(['patient', 'doctor', 'clinic']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor', 'clinic']);

        return response()->json($appointment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
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
                              ->whereRaw('DATE_ADD(date_time, INTERVAL duration MINUTE) > ?', [$appointmentStart]);
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

        return response()->json($appointment->load(['patient', 'doctor', 'clinic']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return response()->json(['message' => 'Appointment deleted successfully']);
    }

    /**
     * Get today's appointments.
     */
    public function today(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor', 'clinic'])
            ->whereDate('date_time', today());

        // Filter by clinic if specified
        if ($request->has('clinic_id')) {
            $query->where('clinic_id', $request->clinic_id);
        }

        // Filter by doctor if specified
        if ($request->has('doctor_id')) {
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
}
