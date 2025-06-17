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
        try {
            Log::info('DoctorController@index called');
            
            // Simple test response first
            return response()->json([
                'success' => true,
                'message' => 'Doctor endpoint working',
                'data' => [],
                'debug' => [
                    'user' => auth()->user() ? auth()->user()->toArray() : 'No user',
                    'guard' => auth()->getDefaultDriver(),
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('DoctorController@index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error in doctor controller: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Store method working',
            'data' => null
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Show method working',
            'data' => ['id' => $id]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Update method working',
            'data' => ['id' => $id]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Destroy method working',
            'data' => ['id' => $id]
        ]);
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
