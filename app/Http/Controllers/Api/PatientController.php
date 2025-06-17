<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\EmergencyContact;
use App\Models\MedicalHistory;
use App\Models\VitalSign;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Patient::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by name or DNI
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('dni', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by clinic
        if ($request->has('clinic_id')) {
            $query->where('preferred_clinic_id', $request->clinic_id);
        }

        $patients = $query->with(['preferredClinic', 'emergencyContact'])
            ->withCount('appointments')
            ->paginate($request->get('per_page', 15));

        // Add additional info
        $patients->getCollection()->transform(function ($patient) {
            $patient->age = $patient->age;
            $patient->last_visit = $patient->appointments()
                ->where('status', 'completed')
                ->latest('date_time')
                ->value('date_time');
            $patient->next_appointment = $patient->appointments()
                ->where('status', 'scheduled')
                ->where('date_time', '>', now())
                ->orderBy('date_time')
                ->value('date_time');
            return $patient;
        });

        return response()->json($patients);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'dni' => 'required|string|unique:patients',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'blood_type' => 'nullable|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'email' => 'nullable|email',
            'preferred_clinic_id' => 'nullable|exists:clinics,id',
            
            // Emergency contact
            'emergency_contact.name' => 'required|string',
            'emergency_contact.phone' => 'required|string',
            'emergency_contact.relationship' => 'required|string',
            
            // Medical history (optional)
            'medical_history.allergies' => 'nullable|array',
            'medical_history.conditions' => 'nullable|array',
            'medical_history.medications' => 'nullable|array',
            'medical_history.surgeries' => 'nullable|array',
        ]);

        $patient = Patient::create($request->only([
            'name', 'dni', 'birth_date', 'gender', 'blood_type',
            'address', 'phone', 'email', 'preferred_clinic_id'
        ]));

        // Create emergency contact
        $patient->emergencyContact()->create($request->emergency_contact);

        // Create medical history if provided
        if ($request->has('medical_history')) {
            $patient->medicalHistory()->create($request->medical_history);
        }

        return response()->json($patient->load(['emergencyContact', 'medicalHistory']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        $patient->load([
            'preferredClinic',
            'emergencyContact',
            'medicalHistory',
            'appointments.doctor',
            'surgeries.mainSurgeon',
            'medicalExams.requestingDoctor',
            'invoices'
        ]);

        // Add calculated fields
        $patient->age = $patient->age;
        $patient->latest_vital_signs = $patient->latestVitalSigns();

        return response()->json($patient);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'name' => 'string|max:255',
            'dni' => 'string|unique:patients,dni,' . $patient->id,
            'birth_date' => 'date',
            'gender' => 'in:male,female,other',
            'blood_type' => 'nullable|string',
            'address' => 'string',
            'phone' => 'string',
            'email' => 'nullable|email',
            'status' => 'in:active,inactive',
            'preferred_clinic_id' => 'nullable|exists:clinics,id',
        ]);

        $patient->update($request->all());

        return response()->json($patient);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();

        return response()->json(['message' => 'Patient deleted successfully']);
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
        $appointments = $patient->appointments()
            ->with(['doctor', 'clinic'])
            ->orderBy('date_time', 'desc')
            ->paginate(15);

        return response()->json($appointments);
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
}
