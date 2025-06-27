<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\EmergencyContact;
use App\Models\MedicalHistory;
use App\Models\VitalSign;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Appointment;
use App\Traits\FiltersUserData;

class PatientController extends Controller
{
    use FiltersUserData;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // Check if user can access patients
        if (!$this->canUserAccess('view', 'patients')) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver pacientes'
            ], 403);
        }

        $query = Patient::query();
        
        // Apply user-specific filters
        $query = $this->applyUserFilters($query, $request, 'patients');
        
        // Apply subscription limits
        $query = $this->applySubscriptionLimits($query, 'patients');

        // Additional filters
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('identification_number', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->has('age_min')) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= ?', [$request->get('age_min')]);
        }

        if ($request->has('age_max')) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) <= ?', [$request->get('age_max')]);
        }

        $patients = $query->with(['preferredClinic'])
            ->paginate($request->get('per_page', 15));

        // Hide sensitive data based on user role
        $this->hideSensitiveData($patients->items());

        return response()->json([
            'success' => true,
            'data' => $patients,
            'current_page' => $patients->currentPage(),
            'last_page' => $patients->lastPage(),
            'per_page' => $patients->perPage(),
            'total' => $patients->total(),
            'user_role' => auth()->user()->role
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Check if user can create patients
        if (!$this->canUserAccess('create', 'patients')) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para crear pacientes'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:patients',
            'phone' => 'required|string|max:20',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'dni' => 'required|string|max:50|unique:patients',
            'address' => 'required|string',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'preferred_clinic_id' => 'nullable|exists:clinics,id',
        ]);

        $patientData = $request->all();
        $patientData['created_by'] = auth()->id(); // Assign the current user as creator
        
        $patient = Patient::create($patientData);

        return response()->json([
            'success' => true,
            'message' => 'Paciente creado exitosamente',
            'data' => $patient
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient): JsonResponse
    {
        // Check if user can access this specific patient
        if (!$this->canUserAccessPatient($patient)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver este paciente'
            ], 403);
        }

        $patient->load(['preferredClinic', 'appointments.doctor', 'medicalExams', 'invoices']);

        // Hide sensitive data based on user role
        $this->hideSensitiveData([$patient]);

        return response()->json([
            'success' => true,
            'data' => $patient
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient): JsonResponse
    {
        // Check if user can update this specific patient
        if (!$this->canUserAccessPatient($patient)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para actualizar este paciente'
            ], 403);
        }

        $request->validate([
            'name' => 'string|max:255',
            'email' => 'nullable|email|unique:patients,email,' . $patient->id,
            'phone' => 'string|max:20',
            'birth_date' => 'date',
            'gender' => 'in:male,female,other',
            'identification_type' => 'in:cedula,passport,other',
            'identification_number' => 'string|max:50|unique:patients,identification_number,' . $patient->id,
            'address' => 'string',
            'emergency_contact_name' => 'string|max:255',
            'emergency_contact_phone' => 'string|max:20',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'preferred_clinic_id' => 'nullable|exists:clinics,id',
            'status' => 'in:active,inactive',
        ]);

        $patient->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Paciente actualizado exitosamente'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient): JsonResponse
    {
        // Only admin and doctors can delete patients
        if (!in_array(auth()->user()->role, ['admin', 'doctor'])) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para eliminar pacientes'
            ], 403);
        }

        // Check if user can access this specific patient
        if (!$this->canUserAccessPatient($patient)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para eliminar este paciente'
            ], 403);
        }

        $patient->delete();

        return response()->json([
            'success' => true,
            'message' => 'Paciente eliminado exitosamente'
        ]);
    }

    /**
     * Get medical history for a patient.
     */
    public function medicalHistory(Patient $patient)
    {
        $medicalHistory = $patient->medicalHistory;
        
        if (!$medicalHistory) {
            return response()->json(['message' => 'No medical history found'], 404);
        }

        return response()->json($medicalHistory);
    }

    /**
     * Update medical history for a patient.
     */
    public function updateMedicalHistory(Request $request, Patient $patient)
    {
        $request->validate([
            'allergies' => 'nullable|array',
            'conditions' => 'nullable|array',
            'medications' => 'nullable|array',
            'surgeries' => 'nullable|array',
        ]);

        $medicalHistory = $patient->medicalHistory()->updateOrCreate(
            ['patient_id' => $patient->id],
            $request->all()
        );

        return response()->json($medicalHistory);
    }

    /**
     * Get vital signs for a patient.
     */
    public function vitalSigns(Patient $patient, Request $request)
    {
        $query = $patient->vitalSigns();

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('measured_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('measured_at', '<=', $request->to_date);
        }

        $vitalSigns = $query->orderBy('measured_at', 'desc')->paginate(15);

        return response()->json($vitalSigns);
    }

    /**
     * Add vital signs for a patient.
     */
    public function addVitalSigns(Request $request, Patient $patient)
    {
        $request->validate([
            'weight' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'blood_pressure' => 'nullable|string',
            'heart_rate' => 'nullable|integer|min:0',
            'temperature' => 'nullable|numeric|min:0',
            'measured_at' => 'required|date',
        ]);

        $vitalSigns = $patient->vitalSigns()->create($request->all());

        return response()->json($vitalSigns, 201);
    }

    /**
     * Get appointments for a patient.
     */
    public function appointments(Patient $patient)
    {
        // Check if user can access this patient
        if (!$this->canUserAccessPatient($patient)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver las citas de este paciente'
            ], 403);
        }

        $appointments = $patient->appointments()
            ->with(['doctor', 'clinic'])
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'appointment_date' => $appointment->appointment_date,
                    'appointment_time' => $appointment->appointment_time,
                    'reason' => $appointment->reason,
                    'status' => $appointment->status,
                    'notes' => $appointment->notes,
                    'doctor_name' => $appointment->doctor ? $appointment->doctor->name : 'No asignado',
                    'clinic_name' => $appointment->clinic ? $appointment->clinic->name : 'No asignada',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $appointments
        ]);
    }

    /**
     * Get surgeries for a patient.
     */
    public function surgeries(Patient $patient)
    {
        $surgeries = $patient->surgeries()
            ->with('mainSurgeon')
            ->orderBy('date_time', 'desc')
            ->paginate(15);

        return response()->json($surgeries);
    }

    /**
     * Get medical exams for a patient.
     */
    public function medicalExams(Patient $patient)
    {
        $exams = $patient->medicalExams()
            ->with(['requestingDoctor', 'result'])
            ->orderBy('scheduled_date', 'desc')
            ->paginate(15);

        return response()->json($exams);
    }

    /**
     * Get invoices for a patient.
     */
    public function invoices(Patient $patient)
    {
        $invoices = $patient->invoices()
            ->orderBy('issue_date', 'desc')
            ->paginate(15);

        return response()->json($invoices);
    }

    /**
     * Get patient statistics.
     */
    public function stats(): JsonResponse
    {
        $user = auth()->user();
        $query = Patient::query();
        
        // Apply user-specific filters
        $query = $this->applyUserFilters($query, request(), 'patients');
        
        $stats = [
            // Fields expected by frontend
            'total' => $query->count(),
            'active' => (clone $query)->where('status', 'active')->count(),
            'with_appointments_today' => (clone $query)->whereHas('appointments', function($q) {
                $q->whereDate('date_time', today());
            })->count(),
            'with_allergies' => (clone $query)->whereHas('medicalHistory', function($q) {
                if (DB::getDriverName() === 'pgsql') {
                    // PostgreSQL JSON handling
                    $q->whereNotNull('allergies')
                      ->whereRaw("allergies::text != '[]'")
                      ->whereRaw("allergies::text != '\"\"'")
                      ->whereRaw("allergies::text != 'null'");
                } else {
                    // MySQL handling
                    $q->whereNotNull('allergies')
                      ->where('allergies', '!=', '[]')
                      ->where('allergies', '!=', '');
                }
            })->count(),
            
            // Additional stats for backend compatibility
            'total_patients' => $query->count(),
            'active_patients' => (clone $query)->where('status', 'active')->count(),
            'inactive_patients' => (clone $query)->where('status', 'inactive')->count(),
            'patients_today' => (clone $query)->whereDate('created_at', today())->count(),
            'patients_this_week' => (clone $query)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'patients_this_month' => (clone $query)->whereMonth('created_at', now()->month)->count(),
            'average_age' => $this->getAverageAge($query),
            'gender_distribution' => $this->getGenderDistribution($query),
            'blood_type_distribution' => $this->getBloodTypeDistribution($query),
        ];

        // Add role-specific information
        $stats['user_role'] = $user->role;
        $stats['is_admin'] = $user->role === 'admin';
        
        if ($user->role === 'doctor' && $user->doctor) {
            $stats['doctor_name'] = $user->doctor->name;
            $stats['doctor_specialty'] = $user->doctor->specialty;
        }

        return response()->json($stats);
    }

    /**
     * Check if current user can access specific patient
     */
    private function canUserAccessPatient(Patient $patient): bool
    {
        $user = auth()->user();
        
        // Admin can access all patients
        if ($user->role === 'admin') {
            return true;
        }
        
        // Apply role-specific access checks
        switch ($user->role) {
            case 'doctor':
                // Check if user has a subscription and what type
                $subscription = $user->currentSubscription;
                
                // If no subscription or free plan, doctor can access all patients
                if (!$subscription || ($subscription->plan && $subscription->plan->slug === 'free')) {
                    return true;
                }
                
                // For paid plans, doctor can access patients they created or have treated
                if (!$user->doctor) return false;
                return $patient->created_by === $user->id || 
                       $patient->appointments()->where('doctor_id', $user->doctor->id)->exists();
                
            case 'nurse':
                // Nurse can access patients with appointments in next 2 days
                return $patient->appointments()
                    ->whereBetween('date_time', [now(), now()->addDays(2)])
                    ->exists();
                    
            case 'receptionist':
                // Receptionist can access all patients (for scheduling)
                return true;
                
            case 'lab_technician':
                // Lab tech can access patients with pending exams
                return $patient->medicalExams()
                    ->whereIn('status', ['scheduled', 'in_progress'])
                    ->exists();
                    
            case 'accountant':
                // Accountant can access patients with invoices
                return $patient->invoices()->exists();
                
            default:
                return false;
        }
    }

    /**
     * Hide sensitive data based on user role
     */
    private function hideSensitiveData(array $patients): void
    {
        $user = auth()->user();
        
        // Admin sees everything
        if ($user->role === 'admin') {
            return;
        }
        
        foreach ($patients as $patient) {
            switch ($user->role) {
                case 'receptionist':
                    // Hide medical information
                    $patient->makeHidden(['medical_history', 'allergies', 'blood_type']);
                    break;
                    
                case 'accountant':
                    // Hide medical information, show only billing-relevant data
                    $patient->makeHidden(['medical_history', 'allergies', 'blood_type', 'emergency_contact_name', 'emergency_contact_phone']);
                    break;
                    
                case 'lab_technician':
                    // Hide personal contact information
                    $patient->makeHidden(['emergency_contact_name', 'emergency_contact_phone', 'address']);
                    break;
            }
        }
    }

    /**
     * Get average age of patients
     */
    private function getAverageAge($query): float
    {
        $patients = $query->whereNotNull('birth_date')->get();
        if ($patients->isEmpty()) return 0;
        
        $totalAge = $patients->sum(function($patient) {
            return now()->diffInYears($patient->birth_date);
        });
        
        return round($totalAge / $patients->count(), 1);
    }

    /**
     * Get gender distribution
     */
    private function getGenderDistribution($query): array
    {
        return [
            'male' => (clone $query)->where('gender', 'male')->count(),
            'female' => (clone $query)->where('gender', 'female')->count(),
            'other' => (clone $query)->where('gender', 'other')->count(),
        ];
    }

    /**
     * Get blood type distribution
     */
    private function getBloodTypeDistribution($query): array
    {
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $distribution = [];
        
        foreach ($bloodTypes as $type) {
            $distribution[$type] = (clone $query)->where('blood_type', $type)->count();
        }
        
        return $distribution;
    }
}
