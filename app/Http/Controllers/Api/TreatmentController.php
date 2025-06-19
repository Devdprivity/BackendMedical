<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Treatment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Medication;
use App\Models\DoctorPatientRelationship;
use App\Traits\FiltersUserData;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class TreatmentController extends Controller
{
    use FiltersUserData;

    /**
     * Display a listing of treatments.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Treatment::with(['patient', 'doctor', 'clinic', 'treatmentMedications.medication']);
        
        // Apply user-specific filters
        $query = $this->applyUserFilters($query, $request, 'treatments');
        
        // Apply additional filters
        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }
        
        if ($request->has('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }
        
        if ($request->has('active_only') && $request->active_only) {
            $query->active();
        }
        
        if ($request->has('current_only') && $request->current_only) {
            $query->current();
        }
        
        // Date range filters
        if ($request->has('start_date_from')) {
            $query->whereDate('start_date', '>=', $request->start_date_from);
        }
        
        if ($request->has('start_date_to')) {
            $query->whereDate('start_date', '<=', $request->start_date_to);
        }
        
        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('instructions', 'like', "%{$search}%")
                  ->orWhereHas('patient', function($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('doctor', function($dq) use ($search) {
                      $dq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $treatments = $query->paginate($request->get('per_page', 15));
        
        return response()->json([
            'success' => true,
            'data' => $treatments
        ]);
    }

    /**
     * Store a newly created treatment.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:medication,therapy,procedure,lifestyle,diet,exercise,follow_up',
            'priority' => 'sometimes|in:low,normal,high,urgent',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'duration_days' => 'nullable|integer|min:1',
            'instructions' => 'required|string',
            'precautions' => 'nullable|string',
            'side_effects_to_watch' => 'nullable|string',
            'notes' => 'nullable|string',
            'medications' => 'nullable|array',
            'medications.*.medication_id' => 'required|exists:medications,id',
            'medications.*.dosage' => 'required|string',
            'medications.*.frequency' => 'required|string',
            'medications.*.duration' => 'required|string',
            'medications.*.administration_instructions' => 'required|string',
            'medications.*.quantity_prescribed' => 'required|integer|min:1',
            'medications.*.notes' => 'nullable|string',
        ]);

        // Check if current user can create treatments for this patient
        if (!$this->canUserAccessPatient($request->patient_id)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para crear tratamientos para este paciente'
            ], 403);
        }

        $user = auth()->user();
        $doctorId = $user->role === 'doctor' ? $user->doctor->id : $request->doctor_id;
        
        if (!$doctorId) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor no especificado'
            ], 400);
        }

        // Create treatment
        $treatment = Treatment::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $doctorId,
            'clinic_id' => $user->clinic_id ?? null,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'priority' => $request->priority ?? 'normal',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'duration_days' => $request->duration_days,
            'instructions' => $request->instructions,
            'precautions' => $request->precautions,
            'side_effects_to_watch' => $request->side_effects_to_watch,
            'notes' => $request->notes,
        ]);

        // Add medications if provided
        if ($request->has('medications') && is_array($request->medications)) {
            foreach ($request->medications as $medicationData) {
                $treatment->treatmentMedications()->create($medicationData);
            }
        }

        // Create or update doctor-patient relationship if it doesn't exist
        DoctorPatientRelationship::firstOrCreate([
            'doctor_id' => $doctorId,
            'patient_id' => $request->patient_id,
            'relationship_type' => 'primary'
        ], [
            'clinic_id' => $user->clinic_id ?? null,
            'started_at' => now(),
            'status' => 'active'
        ]);

        $treatment->load(['patient', 'doctor', 'clinic', 'treatmentMedications.medication']);

        return response()->json([
            'success' => true,
            'message' => 'Tratamiento creado exitosamente',
            'data' => $treatment
        ], 201);
    }

    /**
     * Display the specified treatment.
     */
    public function show(Treatment $treatment): JsonResponse
    {
        if (!$this->canUserAccessTreatment($treatment)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver este tratamiento'
            ], 403);
        }

        $treatment->load([
            'patient.emergencyContact',
            'doctor',
            'clinic',
            'treatmentMedications.medication'
        ]);

        return response()->json([
            'success' => true,
            'data' => $treatment
        ]);
    }

    /**
     * Update the specified treatment.
     */
    public function update(Request $request, Treatment $treatment): JsonResponse
    {
        if (!$this->canUserAccessTreatment($treatment)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para actualizar este tratamiento'
            ], 403);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'type' => 'sometimes|in:medication,therapy,procedure,lifestyle,diet,exercise,follow_up',
            'priority' => 'sometimes|in:low,normal,high,urgent',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|nullable|date|after_or_equal:start_date',
            'duration_days' => 'sometimes|nullable|integer|min:1',
            'instructions' => 'sometimes|string',
            'precautions' => 'sometimes|nullable|string',
            'side_effects_to_watch' => 'sometimes|nullable|string',
            'status' => 'sometimes|in:active,completed,suspended,cancelled',
            'notes' => 'sometimes|nullable|string',
        ]);

        $treatment->update($request->only([
            'title', 'description', 'type', 'priority', 'start_date', 'end_date',
            'duration_days', 'instructions', 'precautions', 'side_effects_to_watch',
            'status', 'notes'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Tratamiento actualizado exitosamente',
            'data' => $treatment
        ]);
    }

    /**
     * Remove the specified treatment.
     */
    public function destroy(Treatment $treatment): JsonResponse
    {
        if (!$this->canUserAccessTreatment($treatment)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para eliminar este tratamiento'
            ], 403);
        }

        $treatment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tratamiento eliminado exitosamente'
        ]);
    }

    /**
     * Share treatment via email.
     */
    public function shareViaEmail(Request $request, Treatment $treatment): JsonResponse
    {
        if (!$this->canUserAccessTreatment($treatment)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para compartir este tratamiento'
            ], 403);
        }

        $request->validate([
            'email' => 'required|email',
            'message' => 'nullable|string|max:500'
        ]);

        try {
            // Generate PDF
            $pdf = $this->generateTreatmentPDF($treatment);
            
            // Send email
            Mail::send('emails.treatment-share', [
                'treatment' => $treatment,
                'message' => $request->message,
                'qr_url' => $treatment->qr_url
            ], function ($mail) use ($request, $treatment, $pdf) {
                $mail->to($request->email)
                     ->subject("Tratamiento Médico - {$treatment->title}")
                     ->attachData($pdf->output(), "tratamiento_{$treatment->id}.pdf");
            });

            // Mark as shared
            $treatment->markAsShared('email', [
                'recipient' => $request->email,
                'message' => $request->message
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tratamiento enviado por email exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate QR code for treatment.
     */
    public function generateQR(Treatment $treatment): JsonResponse
    {
        if (!$this->canUserAccessTreatment($treatment)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para generar QR de este tratamiento'
            ], 403);
        }

        try {
            $qrCode = QrCode::format('png')
                           ->size(300)
                           ->margin(2)
                           ->generate($treatment->qr_url);

            $treatment->markAsShared('qr');

            return response()->json([
                'success' => true,
                'qr_code' => 'data:image/png;base64,' . base64_encode($qrCode),
                'qr_url' => $treatment->qr_url
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar código QR: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get WhatsApp share URL.
     */
    public function getWhatsAppUrl(Treatment $treatment): JsonResponse
    {
        if (!$this->canUserAccessTreatment($treatment)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para compartir este tratamiento'
            ], 403);
        }

        $treatment->markAsShared('whatsapp');

        return response()->json([
            'success' => true,
            'whatsapp_url' => $treatment->whatsapp_url
        ]);
    }

    /**
     * Public view for treatment (via QR code).
     */
    public function publicView(Request $request)
    {
        $treatment = Treatment::where('qr_code', $request->qr)->first();

        if (!$treatment) {
            abort(404, 'Tratamiento no encontrado');
        }

        $treatment->load([
            'patient' => function($query) {
                // Only load essential patient info for privacy
                $query->select('id', 'name', 'age');
            },
            'doctor',
            'clinic',
            'treatmentMedications.medication'
        ]);

        return view('treatments.public', compact('treatment'));
    }

    /**
     * Download treatment as PDF.
     */
    public function downloadPDF(Treatment $treatment)
    {
        if (!$this->canUserAccessTreatment($treatment)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para descargar este tratamiento'
            ], 403);
        }

        $pdf = $this->generateTreatmentPDF($treatment);
        
        return $pdf->download("tratamiento_{$treatment->id}.pdf");
    }

    /**
     * Get treatment statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        $query = Treatment::query();
        $query = $this->applyUserFilters($query, $request, 'treatments');

        $stats = [
            'total' => $query->count(),
            'active' => (clone $query)->active()->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'overdue' => (clone $query)->overdue()->count(),
            'by_type' => (clone $query)->selectRaw('type, COUNT(*) as count')
                                     ->groupBy('type')
                                     ->pluck('count', 'type'),
            'by_priority' => (clone $query)->selectRaw('priority, COUNT(*) as count')
                                          ->groupBy('priority')
                                          ->pluck('count', 'priority'),
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
     * Generate treatment PDF.
     */
    private function generateTreatmentPDF(Treatment $treatment)
    {
        $treatment->load([
            'patient.emergencyContact',
            'doctor',
            'clinic',
            'treatmentMedications.medication'
        ]);

        return Pdf::loadView('treatments.pdf', compact('treatment'));
    }

    /**
     * Check if user can access specific treatment.
     */
    private function canUserAccessTreatment(Treatment $treatment): bool
    {
        $user = auth()->user();

        switch ($user->role) {
            case 'admin':
                return true;
            case 'doctor':
                return $treatment->doctor_id === $user->doctor->id ||
                       $user->doctor->canTreatPatient($treatment->patient_id);
            case 'nurse':
            case 'receptionist':
                return $treatment->clinic_id === $user->clinic_id;
            default:
                return false;
        }
    }

    /**
     * Check if user can access specific patient.
     */
    private function canUserAccessPatient($patientId): bool
    {
        $user = auth()->user();

        switch ($user->role) {
            case 'admin':
                return true;
            case 'doctor':
                return $user->doctor->canTreatPatient($patientId);
            case 'nurse':
            case 'receptionist':
                $patient = Patient::find($patientId);
                return $patient && $patient->preferred_clinic_id === $user->clinic_id;
            default:
                return false;
        }
    }
} 