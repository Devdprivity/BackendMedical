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
        try {
            // Simple test first - just return a basic response to verify controller works
            $query = Doctor::with(['user', 'clinics']);

            // Filter by clinic if provided
            if ($request->has('clinic_id')) {
                $query->whereHas('clinics', function ($q) use ($request) {
                    $q->where('clinic_id', $request->clinic_id);
                });
            }

            // Filter by specialty if provided
            if ($request->has('specialty')) {
                $query->where('specialty', 'like', '%' . $request->specialty . '%');
            }

            // Search by name
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%')
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', '%' . $search . '%')
                                   ->orWhere('email', 'like', '%' . $search . '%');
                      });
                });
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $doctors = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'message' => 'Doctors endpoint is working! 🎉',
                'data' => $doctors->items(),
                'pagination' => [
                    'current_page' => $doctors->currentPage(),
                    'per_page' => $doctors->perPage(),
                    'total' => $doctors->total(),
                    'last_page' => $doctors->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error in doctors endpoint: ' . $e->getMessage(),
                'error_details' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|string|email|max:255|unique:users,email',
            'user_password' => 'required|string|min:8',
            'user_phone' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'specialty' => 'required|string|max:100',
            'license_number' => 'required|string|max:50|unique:doctors',
            'email' => 'required|string|email|max:255|unique:doctors',
            'phone' => 'required|string|max:20',
            'emergency_phone' => 'nullable|string|max:20',
            'address' => 'required|string',
            'experience_years' => 'required|integer|min:0|max:80',
            'education' => 'required|array',
            'certifications' => 'nullable|array',
            'languages' => 'nullable|array',
            'bio' => 'nullable|string',
            'photo_url' => 'nullable|string|url',
            'status' => 'nullable|in:active,inactive,vacation,leave',
            'clinic_ids' => 'required|array',
            'clinic_ids.*' => 'exists:clinics,id',
            'schedules' => 'nullable|array',
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
                'name' => $request->user_name,
                'email' => $request->user_email,
                'password' => Hash::make($request->user_password),
                'phone' => $request->user_phone,
                'role' => 'doctor',
            ]);

            // Create doctor
            $doctor = Doctor::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'specialty' => $request->specialty,
                'license_number' => $request->license_number,
                'email' => $request->email,
                'phone' => $request->phone,
                'emergency_phone' => $request->emergency_phone,
                'address' => $request->address,
                'experience_years' => $request->experience_years,
                'education' => $request->education,
                'certifications' => $request->certifications ?? [],
                'languages' => $request->languages ?? [],
                'bio' => $request->bio,
                'photo_url' => $request->photo_url,
                'status' => $request->status ?? 'active',
            ]);

            // Attach clinics
            if ($request->has('clinic_ids')) {
                $clinicData = [];
                foreach ($request->clinic_ids as $index => $clinicId) {
                    $clinicData[$clinicId] = [
                        'status' => 'active',
                        'schedule' => $request->schedules[$index] ?? null,
                    ];
                }
                $doctor->clinics()->attach($clinicData);
            }

            $doctor->load(['user', 'clinics']);

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
            $doctor = Doctor::with(['user', 'clinics'])->findOrFail($id);

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
                'user_name' => 'sometimes|string|max:255',
                'user_email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($doctor->user_id)],
                'user_password' => 'sometimes|string|min:8',
                'user_phone' => 'sometimes|string|max:20',
                'name' => 'sometimes|string|max:255',
                'specialty' => 'sometimes|string|max:100',
                'license_number' => ['sometimes', 'string', 'max:50', Rule::unique('doctors')->ignore($id)],
                'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('doctors')->ignore($id)],
                'phone' => 'sometimes|string|max:20',
                'emergency_phone' => 'nullable|string|max:20',
                'address' => 'sometimes|string',
                'experience_years' => 'sometimes|integer|min:0|max:80',
                'education' => 'sometimes|array',
                'certifications' => 'nullable|array',
                'languages' => 'nullable|array',
                'bio' => 'nullable|string',
                'photo_url' => 'nullable|string|url',
                'status' => 'sometimes|in:active,inactive,vacation,leave',
                'clinic_ids' => 'sometimes|array',
                'clinic_ids.*' => 'exists:clinics,id',
                'schedules' => 'nullable|array',
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
            if ($request->has('user_name')) $userData['name'] = $request->user_name;
            if ($request->has('user_email')) $userData['email'] = $request->user_email;
            if ($request->has('user_phone')) $userData['phone'] = $request->user_phone;
            if ($request->has('user_password')) $userData['password'] = Hash::make($request->user_password);

            if (!empty($userData)) {
                $doctor->user->update($userData);
            }

            // Update doctor data
            $doctorData = $request->except(['user_name', 'user_email', 'user_phone', 'user_password', 'clinic_ids', 'schedules']);
            $doctor->update($doctorData);

            // Update clinic associations if provided
            if ($request->has('clinic_ids')) {
                $clinicData = [];
                foreach ($request->clinic_ids as $index => $clinicId) {
                    $clinicData[$clinicId] = [
                        'status' => 'active',
                        'schedule' => $request->schedules[$index] ?? null,
                    ];
                }
                $doctor->clinics()->sync($clinicData);
            }

            $doctor->load(['user', 'clinics']);

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
            $doctor->clinics()->detach(); // Remove clinic associations
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

            $query = Appointment::with(['patient.user'])
                ->where('doctor_id', $id);

            // Filter by date range
            if ($request->has('start_date')) {
                $query->whereDate('date_time', '>=', $request->start_date);
            }
            if ($request->has('end_date')) {
                $query->whereDate('date_time', '<=', $request->end_date);
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $appointments = $query->orderBy('date_time', 'desc')
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

            $appointments = Appointment::with(['patient.user'])
                ->where('doctor_id', $id)
                ->whereDate('date_time', today())
                ->orderBy('date_time')
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

            $query = Surgery::with(['patient.user'])
                ->where('main_surgeon_id', $id);

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

            $query = MedicalExam::with(['patient.user'])
                ->where('requesting_doctor_id', $id);

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
