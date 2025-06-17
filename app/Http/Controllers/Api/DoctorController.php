<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Surgery;
use App\Models\MedicalExam;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

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

        return response()->json($doctors);
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

        return response()->json($doctor, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Doctor $doctor): JsonResponse
    {
        $doctor->load(['user', 'clinics']);

        return response()->json($doctor);
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

        return response()->json($doctor);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor): JsonResponse
    {
        $doctor->delete();

        return response()->json(['message' => 'Doctor deleted successfully']);
    }

    /**
     * Get appointments for a specific doctor.
     */
    public function appointments(Doctor $doctor, Request $request): JsonResponse
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
    public function todayAppointments(Doctor $doctor): JsonResponse
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
    public function surgeries(Doctor $doctor): JsonResponse
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
    public function requestedExams(Doctor $doctor): JsonResponse
    {
        $exams = $doctor->requestedExams()
            ->with(['patient', 'clinic'])
            ->orderBy('exam_date', 'desc')
            ->paginate(15);

        return response()->json($exams);
    }
}
