<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Doctor::query();

        // Filter by specialty
        if ($request->has('specialty')) {
            $query->where('specialty', 'like', '%' . $request->specialty . '%');
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $doctors = $query->with(['user', 'clinics'])
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $doctors,
            'current_page' => $doctors->currentPage(),
            'last_page' => $doctors->lastPage(),
            'per_page' => $doctors->perPage(),
            'total' => $doctors->total()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'specialty' => 'required|string|max:100',
            'license_number' => 'required|string|max:50|unique:doctors',
            'email' => 'required|email|unique:doctors',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'experience_years' => 'required|integer|min:0',
            'status' => 'in:active,inactive,vacation,leave',
        ]);

        $doctor = Doctor::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Doctor creado exitosamente',
            'data' => $doctor
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Doctor $doctor): JsonResponse
    {
        $doctor->load(['user', 'clinics']);

        return response()->json([
            'success' => true,
            'data' => $doctor
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Doctor $doctor): JsonResponse
    {
        $request->validate([
            'name' => 'string|max:255',
            'specialty' => 'string|max:100',
            'license_number' => 'string|max:50|unique:doctors,license_number,' . $doctor->id,
            'email' => 'email|unique:doctors,email,' . $doctor->id,
            'phone' => 'string|max:20',
            'address' => 'string',
            'experience_years' => 'integer|min:0',
            'status' => 'in:active,inactive,vacation,leave',
        ]);

        $doctor->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Doctor actualizado exitosamente'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor): JsonResponse
    {
        $doctor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Doctor eliminado exitosamente'
        ]);
    }

    /**
     * Get appointments for a specific doctor.
     */
    public function appointments(Doctor $doctor, Request $request)
    {
        $query = $doctor->appointments()
            ->with(['patient', 'clinic']);

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('date_time', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('date_time', '<=', $request->to_date);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->orderBy('date_time', 'desc')
            ->paginate(15);

        return response()->json($appointments);
    }

    /**
     * Get today's appointments for a specific doctor.
     */
    public function todayAppointments(Doctor $doctor)
    {
        $appointments = $doctor->appointments()
            ->with(['patient', 'clinic'])
            ->whereDate('date_time', today())
            ->orderBy('date_time')
            ->get();

        return response()->json($appointments);
    }

    /**
     * Get surgeries for a specific doctor.
     */
    public function surgeries(Doctor $doctor)
    {
        $surgeries = $doctor->surgeries()
            ->with(['patient', 'clinic'])
            ->orderBy('surgery_date', 'desc')
            ->paginate(15);

        return response()->json($surgeries);
    }

    /**
     * Get requested exams for a specific doctor.
     */
    public function requestedExams(Doctor $doctor)
    {
        $exams = $doctor->requestedExams()
            ->with(['patient', 'clinic'])
            ->orderBy('exam_date', 'desc')
            ->paginate(15);

        return response()->json($exams);
    }

    /**
     * Get doctor statistics.
     */
    public function stats(): JsonResponse
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            // Admin sees all doctor stats
            $stats = [
                'total_doctors' => Doctor::count(),
                'active_doctors' => Doctor::where('status', 'active')->count(),
                'inactive_doctors' => Doctor::where('status', 'inactive')->count(),
                'on_vacation' => Doctor::where('status', 'vacation')->count(),
                'on_leave' => Doctor::where('status', 'leave')->count(),
                'specialties' => Doctor::distinct('specialty')->count('specialty'),
                'average_experience' => Doctor::avg('experience_years') ?? 0,
                'doctors_with_appointments_today' => Doctor::whereHas('appointments', function($q) {
                    $q->whereDate('date_time', today());
                })->count(),
            ];
        } else {
            // Non-admin users see very limited stats
            $stats = [
                'total_doctors' => Doctor::where('status', 'active')->count(),
                'active_doctors' => Doctor::where('status', 'active')->count(),
                'inactive_doctors' => 0, // Hidden
                'on_vacation' => 0, // Hidden
                'on_leave' => 0, // Hidden
                'specialties' => Doctor::where('status', 'active')->distinct('specialty')->count('specialty'),
                'average_experience' => 0, // Hidden
                'doctors_with_appointments_today' => Doctor::where('status', 'active')->whereHas('appointments', function($q) {
                    $q->whereDate('date_time', today());
                })->count(),
            ];
        }
        
        return response()->json($stats);
    }
}
