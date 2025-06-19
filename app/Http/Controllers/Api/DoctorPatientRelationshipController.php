<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DoctorPatientRelationship;
use App\Models\Doctor;
use App\Models\Patient;
use App\Traits\FiltersUserData;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DoctorPatientRelationshipController extends Controller
{
    use FiltersUserData;

    /**
     * Display a listing of doctor-patient relationships.
     */
    public function index(Request $request): JsonResponse
    {
        $query = DoctorPatientRelationship::with(['doctor', 'patient', 'clinic']);
        
        // Apply user-specific filters
        $query = $this->applyUserFilters($query, $request, 'relationships');
        
        // Apply additional filters
        if ($request->has('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }
        
        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }
        
        if ($request->has('relationship_type')) {
            $query->where('relationship_type', $request->relationship_type);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('active_only') && $request->active_only) {
            $query->active();
        }
        
        if ($request->has('current_only') && $request->current_only) {
            $query->current();
        }
        
        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('patient', function($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%")
                       ->orWhere('dni', 'like', "%{$search}%");
                })
                ->orWhereHas('doctor', function($dq) use ($search) {
                    $dq->where('name', 'like', "%{$search}%")
                       ->orWhere('specialty', 'like', "%{$search}%");
                });
            });
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $relationships = $query->paginate($request->get('per_page', 15));
        
        return response()->json([
            'success' => true,
            'data' => $relationships
        ]);
    }

    /**
     * Store a newly created doctor-patient relationship.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'patient_id' => 'required|exists:patients,id',
            'relationship_type' => 'required|in:primary,consulting,specialist,emergency',
            'started_at' => 'required|date',
            'ended_at' => 'nullable|date|after_or_equal:started_at',
            'notes' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:view_history,prescribe,update_records,schedule_appointments,emergency_access,update_specialty_records,emergency_prescribe'
        ]);

        // Check if user can create relationships
        if (!$this->canUserManageRelationships()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para crear relaciones médico-paciente'
            ], 403);
        }

        // Check if relationship already exists
        $existingRelationship = DoctorPatientRelationship::where([
            'doctor_id' => $request->doctor_id,
            'patient_id' => $request->patient_id,
            'relationship_type' => $request->relationship_type
        ])->first();

        if ($existingRelationship) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe una relación de este tipo entre el médico y paciente'
            ], 409);
        }

        $user = auth()->user();
        $relationship = DoctorPatientRelationship::create([
            'doctor_id' => $request->doctor_id,
            'patient_id' => $request->patient_id,
            'clinic_id' => $user->clinic_id ?? null,
            'relationship_type' => $request->relationship_type,
            'started_at' => $request->started_at,
            'ended_at' => $request->ended_at,
            'status' => 'active',
            'notes' => $request->notes,
            'permissions' => $request->permissions,
        ]);

        $relationship->load(['doctor', 'patient', 'clinic']);

        return response()->json([
            'success' => true,
            'message' => 'Relación médico-paciente creada exitosamente',
            'data' => $relationship
        ], 201);
    }

    /**
     * Display the specified relationship.
     */
    public function show(DoctorPatientRelationship $relationship): JsonResponse
    {
        if (!$this->canUserAccessRelationship($relationship)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver esta relación'
            ], 403);
        }

        $relationship->load(['doctor', 'patient', 'clinic']);

        return response()->json([
            'success' => true,
            'data' => $relationship
        ]);
    }

    /**
     * Update the specified relationship.
     */
    public function update(Request $request, DoctorPatientRelationship $relationship): JsonResponse
    {
        if (!$this->canUserAccessRelationship($relationship)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para actualizar esta relación'
            ], 403);
        }

        $request->validate([
            'relationship_type' => 'sometimes|in:primary,consulting,specialist,emergency',
            'started_at' => 'sometimes|date',
            'ended_at' => 'sometimes|nullable|date|after_or_equal:started_at',
            'status' => 'sometimes|in:active,inactive,transferred',
            'notes' => 'sometimes|nullable|string',
            'permissions' => 'sometimes|nullable|array',
            'permissions.*' => 'string|in:view_history,prescribe,update_records,schedule_appointments,emergency_access,update_specialty_records,emergency_prescribe'
        ]);

        $relationship->update($request->only([
            'relationship_type', 'started_at', 'ended_at', 'status', 'notes', 'permissions'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Relación actualizada exitosamente',
            'data' => $relationship
        ]);
    }

    /**
     * Remove the specified relationship.
     */
    public function destroy(DoctorPatientRelationship $relationship): JsonResponse
    {
        if (!$this->canUserAccessRelationship($relationship)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para eliminar esta relación'
            ], 403);
        }

        $relationship->delete();

        return response()->json([
            'success' => true,
            'message' => 'Relación eliminada exitosamente'
        ]);
    }

    /**
     * Get patients for a specific doctor.
     */
    public function getDoctorPatients(Doctor $doctor, Request $request): JsonResponse
    {
        if (!$this->canUserAccessDoctor($doctor)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver los pacientes de este doctor'
            ], 403);
        }

        $query = $doctor->patients();
        
        if ($request->has('relationship_type')) {
            $query->wherePivot('relationship_type', $request->relationship_type);
        }
        
        if ($request->has('status')) {
            $query->wherePivot('status', $request->status);
        }
        
        $patients = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $patients
        ]);
    }

    /**
     * Get doctors for a specific patient.
     */
    public function getPatientDoctors(Patient $patient, Request $request): JsonResponse
    {
        if (!$this->canUserAccessPatient($patient)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver los doctores de este paciente'
            ], 403);
        }

        $query = $patient->doctors();
        
        if ($request->has('relationship_type')) {
            $query->wherePivot('relationship_type', $request->relationship_type);
        }
        
        if ($request->has('status')) {
            $query->wherePivot('status', $request->status);
        }
        
        $doctors = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $doctors
        ]);
    }

    /**
     * Transfer patient from one doctor to another.
     */
    public function transferPatient(Request $request): JsonResponse
    {
        $request->validate([
            'from_doctor_id' => 'required|exists:doctors,id',
            'to_doctor_id' => 'required|exists:doctors,id|different:from_doctor_id',
            'patient_id' => 'required|exists:patients,id',
            'relationship_type' => 'required|in:primary,consulting,specialist,emergency',
            'transfer_notes' => 'nullable|string',
            'effective_date' => 'required|date'
        ]);

        if (!$this->canUserManageRelationships()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para transferir pacientes'
            ], 403);
        }

        // Find existing relationship
        $oldRelationship = DoctorPatientRelationship::where([
            'doctor_id' => $request->from_doctor_id,
            'patient_id' => $request->patient_id,
            'relationship_type' => $request->relationship_type,
            'status' => 'active'
        ])->first();

        if (!$oldRelationship) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró la relación médico-paciente a transferir'
            ], 404);
        }

        // End old relationship
        $oldRelationship->update([
            'status' => 'transferred',
            'ended_at' => $request->effective_date,
            'notes' => ($oldRelationship->notes ?? '') . "\nTransferido: " . $request->transfer_notes
        ]);

        // Create new relationship
        $newRelationship = DoctorPatientRelationship::create([
            'doctor_id' => $request->to_doctor_id,
            'patient_id' => $request->patient_id,
            'clinic_id' => auth()->user()->clinic_id ?? null,
            'relationship_type' => $request->relationship_type,
            'started_at' => $request->effective_date,
            'status' => 'active',
            'notes' => 'Transferido desde Dr. ' . $oldRelationship->doctor->name . ': ' . $request->transfer_notes,
            'permissions' => $oldRelationship->permissions
        ]);

        $newRelationship->load(['doctor', 'patient', 'clinic']);

        return response()->json([
            'success' => true,
            'message' => 'Paciente transferido exitosamente',
            'data' => $newRelationship
        ]);
    }

    /**
     * Get relationship statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        $query = DoctorPatientRelationship::query();
        $query = $this->applyUserFilters($query, $request, 'relationships');

        $stats = [
            'total' => $query->count(),
            'active' => (clone $query)->active()->count(),
            'by_type' => (clone $query)->selectRaw('relationship_type, COUNT(*) as count')
                                     ->groupBy('relationship_type')
                                     ->pluck('count', 'relationship_type'),
            'by_status' => (clone $query)->selectRaw('status, COUNT(*) as count')
                                        ->groupBy('status')
                                        ->pluck('count', 'status'),
            'this_month' => (clone $query)->whereMonth('created_at', now()->month)
                                         ->whereYear('created_at', now()->year)
                                         ->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Check if user can manage relationships.
     */
    private function canUserManageRelationships(): bool
    {
        $user = auth()->user();
        return in_array($user->role, ['admin', 'doctor']);
    }

    /**
     * Check if user can access specific relationship.
     */
    private function canUserAccessRelationship(DoctorPatientRelationship $relationship): bool
    {
        $user = auth()->user();

        switch ($user->role) {
            case 'admin':
                return true;
            case 'doctor':
                return $relationship->doctor_id === $user->doctor->id;
            case 'nurse':
            case 'receptionist':
                return $relationship->clinic_id === $user->clinic_id;
            default:
                return false;
        }
    }

    /**
     * Check if user can access specific doctor.
     */
    private function canUserAccessDoctor(Doctor $doctor): bool
    {
        $user = auth()->user();

        switch ($user->role) {
            case 'admin':
                return true;
            case 'doctor':
                return $doctor->id === $user->doctor->id;
            case 'nurse':
            case 'receptionist':
                return $doctor->clinics()->where('clinic_id', $user->clinic_id)->exists();
            default:
                return false;
        }
    }

    /**
     * Check if user can access specific patient.
     */
    private function canUserAccessPatient(Patient $patient): bool
    {
        $user = auth()->user();

        switch ($user->role) {
            case 'admin':
                return true;
            case 'doctor':
                return $user->doctor->canTreatPatient($patient->id);
            case 'nurse':
            case 'receptionist':
                return $patient->preferred_clinic_id === $user->clinic_id;
            default:
                return false;
        }
    }
} 