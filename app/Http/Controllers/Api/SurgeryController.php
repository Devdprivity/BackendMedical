<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Surgery;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Clinic;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class SurgeryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Surgery::with(['patient', 'mainSurgeon', 'clinic']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('surgery_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('surgery_date', '<=', $request->to_date);
        }

        // Filter by surgeon
        if ($request->has('surgeon_id')) {
            $query->where('main_surgeon_id', $request->surgeon_id);
        }

        // Filter by clinic
        if ($request->has('clinic_id')) {
            $query->where('clinic_id', $request->clinic_id);
        }

        // Search by patient name or surgery type
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('surgery_type', 'like', '%' . $search . '%')
                  ->orWhereHas('patient', function ($patientQuery) use ($search) {
                      $patientQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $surgeries = $query->orderBy('surgery_date', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($surgeries);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'main_surgeon_id' => 'required|exists:doctors,id',
            'clinic_id' => 'required|exists:clinics,id',
            'surgery_type' => 'required|string|max:255',
            'surgery_date' => 'required|date|after:now',
            'estimated_duration' => 'required|integer|min:30|max:720',
            'operating_room' => 'required|string|max:50',
            'anesthesia_type' => 'required|string|max:100',
            'pre_operative_notes' => 'nullable|string',
            'urgency_level' => 'required|in:elective,urgent,emergency',
            'assistant_surgeons' => 'nullable|array',
            'assistant_surgeons.*' => 'exists:doctors,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $surgery = Surgery::create([
                'patient_id' => $request->patient_id,
                'main_surgeon_id' => $request->main_surgeon_id,
                'clinic_id' => $request->clinic_id,
                'surgery_type' => $request->surgery_type,
                'surgery_date' => $request->surgery_date,
                'estimated_duration' => $request->estimated_duration,
                'operating_room' => $request->operating_room,
                'anesthesia_type' => $request->anesthesia_type,
                'pre_operative_notes' => $request->pre_operative_notes,
                'urgency_level' => $request->urgency_level,
                'assistant_surgeons' => $request->assistant_surgeons ?? [],
                'status' => 'scheduled',
            ]);

            $surgery->load(['patient.user', 'mainSurgeon.user', 'clinic']);

            return response()->json([
                'success' => true,
                'message' => 'Surgery scheduled successfully',
                'data' => $surgery
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating surgery: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $surgery = Surgery::with(['patient.user', 'mainSurgeon.user', 'clinic'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $surgery
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Surgery not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $surgery = Surgery::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'patient_id' => 'sometimes|exists:patients,id',
                'main_surgeon_id' => 'sometimes|exists:doctors,id',
                'clinic_id' => 'sometimes|exists:clinics,id',
                'surgery_type' => 'sometimes|string|max:255',
                'surgery_date' => 'sometimes|date',
                'estimated_duration' => 'sometimes|integer|min:30|max:720',
                'actual_duration' => 'nullable|integer|min:1',
                'operating_room' => 'sometimes|string|max:50',
                'anesthesia_type' => 'sometimes|string|max:100',
                'pre_operative_notes' => 'nullable|string',
                'post_operative_notes' => 'nullable|string',
                'complications' => 'nullable|string',
                'outcome' => 'nullable|string',
                'urgency_level' => 'sometimes|in:elective,urgent,emergency',
                'status' => 'sometimes|in:scheduled,in_progress,completed,cancelled,postponed',
                'assistant_surgeons' => 'nullable|array',
                'assistant_surgeons.*' => 'exists:doctors,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $surgery->update($request->all());
            $surgery->load(['patient.user', 'mainSurgeon.user', 'clinic']);

            return response()->json([
                'success' => true,
                'message' => 'Surgery updated successfully',
                'data' => $surgery
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating surgery: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $surgery = Surgery::findOrFail($id);
            
            // Only allow deletion of scheduled surgeries
            if ($surgery->status !== 'scheduled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete surgery that is not in scheduled status'
                ], 400);
            }

            $surgery->delete();

            return response()->json([
                'success' => true,
                'message' => 'Surgery deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting surgery: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get today's surgeries
     */
    public function today(Request $request): JsonResponse
    {
        try {
            $query = Surgery::with(['patient.user', 'mainSurgeon.user', 'clinic'])
                ->whereDate('surgery_date', today());

            // Filter by clinic if provided
            if ($request->has('clinic_id')) {
                $query->where('clinic_id', $request->clinic_id);
            }

            // Filter by surgeon if provided
            if ($request->has('surgeon_id')) {
                $query->where('main_surgeon_id', $request->surgeon_id);
            }

            $surgeries = $query->orderBy('surgery_date')->get();

            return response()->json([
                'success' => true,
                'data' => $surgeries
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching today\'s surgeries: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update surgery status
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        try {
            $surgery = Surgery::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:scheduled,in_progress,completed,cancelled,postponed',
                'notes' => 'nullable|string',
                'actual_duration' => 'nullable|integer|min:1',
                'complications' => 'nullable|string',
                'outcome' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = ['status' => $request->status];
            
            if ($request->has('notes')) {
                $updateData['post_operative_notes'] = $request->notes;
            }
            if ($request->has('actual_duration')) {
                $updateData['actual_duration'] = $request->actual_duration;
            }
            if ($request->has('complications')) {
                $updateData['complications'] = $request->complications;
            }
            if ($request->has('outcome')) {
                $updateData['outcome'] = $request->outcome;
            }

            $surgery->update($updateData);
            $surgery->load(['patient.user', 'mainSurgeon.user', 'clinic']);

            return response()->json([
                'success' => true,
                'message' => 'Surgery status updated successfully',
                'data' => $surgery
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating surgery status: ' . $e->getMessage()
            ], 500);
        }
    }
}
