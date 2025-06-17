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

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Doctor::with(['user', 'clinic']);

        // Filter by clinic if provided
        if ($request->has('clinic_id')) {
            $query->where('clinic_id', $request->clinic_id);
        }

        // Filter by specialty if provided
        if ($request->has('specialty')) {
            $query->where('specialty', 'like', '%' . $request->specialty . '%');
        }

        // Search by name
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Filter by availability
        if ($request->has('available')) {
            $query->where('is_available', $request->boolean('available'));
        }

        $doctors = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $doctors->items(),
            'pagination' => [
                'current_page' => $doctors->currentPage(),
                'per_page' => $doctors->perPage(),
                'total' => $doctors->total(),
                'last_page' => $doctors->lastPage(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'required|string|max:20',
            'clinic_id' => 'required|exists:clinics,id',
            'specialty' => 'required|string|max:100',
            'license_number' => 'required|string|max:50|unique:doctors',
            'years_of_experience' => 'required|integer|min:0|max:80',
            'education' => 'nullable|string',
            'certifications' => 'nullable|string',
            'biography' => 'nullable|string',
            'consultation_fee' => 'required|numeric|min:0',
            'emergency_fee' => 'required|numeric|min:0',
            'follow_up_fee' => 'required|numeric|min:0',
            'schedule' => 'nullable|json',
            'is_available' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create user first
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => 'doctor',
            ]);

            // Create doctor
            $doctor = Doctor::create([
                'user_id' => $user->id,
                'clinic_id' => $request->clinic_id,
                'specialty' => $request->specialty,
                'license_number' => $request->license_number,
                'years_of_experience' => $request->years_of_experience,
                'education' => $request->education,
                'certifications' => $request->certifications,
                'biography' => $request->biography,
                'consultation_fee' => $request->consultation_fee,
                'emergency_fee' => $request->emergency_fee,
                'follow_up_fee' => $request->follow_up_fee,
                'schedule' => $request->schedule ? json_decode($request->schedule, true) : null,
                'is_available' => $request->boolean('is_available', true),
            ]);

            $doctor->load(['user', 'clinic']);

            return response()->json([
                'success' => true,
                'message' => 'Doctor created successfully',
                'data' => $doctor
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating doctor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $doctor = Doctor::with(['user', 'clinic'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $doctor
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $doctor = Doctor::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($doctor->user_id)],
                'password' => 'sometimes|string|min:8',
                'phone' => 'sometimes|string|max:20',
                'clinic_id' => 'sometimes|exists:clinics,id',
                'specialty' => 'sometimes|string|max:100',
                'license_number' => ['sometimes', 'string', 'max:50', Rule::unique('doctors')->ignore($id)],
                'years_of_experience' => 'sometimes|integer|min:0|max:80',
                'education' => 'nullable|string',
                'certifications' => 'nullable|string',
                'biography' => 'nullable|string',
                'consultation_fee' => 'sometimes|numeric|min:0',
                'emergency_fee' => 'sometimes|numeric|min:0',
                'follow_up_fee' => 'sometimes|numeric|min:0',
                'schedule' => 'nullable|json',
                'is_available' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update user data if provided
            $userData = [];
            if ($request->has('name')) $userData['name'] = $request->name;
            if ($request->has('email')) $userData['email'] = $request->email;
            if ($request->has('phone')) $userData['phone'] = $request->phone;
            if ($request->has('password')) $userData['password'] = Hash::make($request->password);

            if (!empty($userData)) {
                $doctor->user->update($userData);
            }

            // Update doctor data
            $doctorData = $request->except(['name', 'email', 'phone', 'password']);
            if ($request->has('schedule')) {
                $doctorData['schedule'] = json_decode($request->schedule, true);
            }

            $doctor->update($doctorData);
            $doctor->load(['user', 'clinic']);

            return response()->json([
                'success' => true,
                'message' => 'Doctor updated successfully',
                'data' => $doctor
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating doctor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $doctor = Doctor::findOrFail($id);
            
            // Check if doctor has appointments
            $hasAppointments = Appointment::where('doctor_id', $id)->exists();
            if ($hasAppointments) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete doctor with existing appointments'
                ], 400);
            }

            $user = $doctor->user;
            $doctor->delete();
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Doctor deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting doctor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get doctor's appointments
     */
    public function appointments(Request $request, string $id): JsonResponse
    {
        try {
            $doctor = Doctor::findOrFail($id);

            $query = Appointment::with(['patient.user', 'clinic'])
                ->where('doctor_id', $id);

            // Filter by date range
            if ($request->has('start_date')) {
                $query->whereDate('appointment_date', '>=', $request->start_date);
            }
            if ($request->has('end_date')) {
                $query->whereDate('appointment_date', '<=', $request->end_date);
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $appointments = $query->orderBy('appointment_date', 'desc')
                ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $appointments->items(),
                'pagination' => [
                    'current_page' => $appointments->currentPage(),
                    'per_page' => $appointments->perPage(),
                    'total' => $appointments->total(),
                    'last_page' => $appointments->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor not found'
            ], 404);
        }
    }

    /**
     * Get doctor's today appointments
     */
    public function todayAppointments(string $id): JsonResponse
    {
        try {
            $doctor = Doctor::findOrFail($id);

            $appointments = Appointment::with(['patient.user', 'clinic'])
                ->where('doctor_id', $id)
                ->whereDate('appointment_date', today())
                ->orderBy('appointment_time')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $appointments
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor not found'
            ], 404);
        }
    }

    /**
     * Get doctor's surgeries
     */
    public function surgeries(Request $request, string $id): JsonResponse
    {
        try {
            $doctor = Doctor::findOrFail($id);

            $query = Surgery::with(['patient.user', 'clinic'])
                ->where('doctor_id', $id);

            // Filter by date range
            if ($request->has('start_date')) {
                $query->whereDate('surgery_date', '>=', $request->start_date);
            }
            if ($request->has('end_date')) {
                $query->whereDate('surgery_date', '<=', $request->end_date);
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $surgeries = $query->orderBy('surgery_date', 'desc')
                ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $surgeries->items(),
                'pagination' => [
                    'current_page' => $surgeries->currentPage(),
                    'per_page' => $surgeries->perPage(),
                    'total' => $surgeries->total(),
                    'last_page' => $surgeries->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor not found'
            ], 404);
        }
    }

    /**
     * Get medical exams requested by this doctor
     */
    public function requestedExams(Request $request, string $id): JsonResponse
    {
        try {
            $doctor = Doctor::findOrFail($id);

            $query = MedicalExam::with(['patient.user', 'clinic'])
                ->where('requested_by', $id);

            // Filter by date range
            if ($request->has('start_date')) {
                $query->whereDate('exam_date', '>=', $request->start_date);
            }
            if ($request->has('end_date')) {
                $query->whereDate('exam_date', '<=', $request->end_date);
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $exams = $query->orderBy('exam_date', 'desc')
                ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $exams->items(),
                'pagination' => [
                    'current_page' => $exams->currentPage(),
                    'per_page' => $exams->perPage(),
                    'total' => $exams->total(),
                    'last_page' => $exams->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor not found'
            ], 404);
        }
    }
}
