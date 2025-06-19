<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalExam;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MedicalExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MedicalExam::with(['patient', 'requestingDoctor']);
        
        // Non-admin users see limited exam data
        $user = auth()->user();
        if ($user->role !== 'admin') {
            if ($user->role === 'lab_technician') {
                // Lab technicians see all exams but with limited patient info
            } elseif ($user->role === 'doctor') {
                // Doctors see only exams they requested
                $doctor = $user->doctor;
                if ($doctor) {
                    $query->where('requesting_doctor_id', $doctor->id);
                } else {
                    $query->where('id', -1);
                }
            } else {
                // Other roles see no exams
                $query->where('id', -1);
            }
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by exam type
        if ($request->has('exam_type')) {
            $query->where('exam_type', 'like', '%' . $request->exam_type . '%');
        }

        // Filter by requesting doctor (only for admin)
        if ($request->has('doctor_id') && $user->role === 'admin') {
            $query->where('requesting_doctor_id', $request->doctor_id);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('scheduled_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('scheduled_date', '<=', $request->to_date);
        }

        // Search by patient name or exam type
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('exam_type', 'like', '%' . $search . '%')
                  ->orWhereHas('patient', function ($patientQuery) use ($search) {
                      $patientQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $exams = $query->orderBy('scheduled_date', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($exams);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // If user is not admin, set requesting_doctor_id to their own doctor record
        if ($user->role !== 'admin') {
            $doctor = $user->doctor;
            if (!$doctor) {
                return response()->json([
                    'message' => 'No tienes un perfil de doctor asociado',
                    'errors' => ['requesting_doctor_id' => ['Usuario no tiene perfil de doctor']]
                ], 403);
            }
            $request->merge(['requesting_doctor_id' => $doctor->id]);
        }
        
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'requesting_doctor_id' => 'required|exists:doctors,id',
            'exam_type' => 'required|string|max:255',
            'scheduled_date' => 'required|date',
            'laboratory_area' => 'required|string|max:255',
            'preparation_required' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $exam = MedicalExam::create([
            'patient_id' => $request->patient_id,
            'requesting_doctor_id' => $request->requesting_doctor_id,
            'exam_type' => $request->exam_type,
            'scheduled_date' => $request->scheduled_date,
            'laboratory_area' => $request->laboratory_area,
            'preparation_required' => $request->preparation_required,
            'notes' => $request->notes,
            'status' => 'scheduled',
        ]);

        return response()->json($exam->load(['patient', 'requestingDoctor']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(MedicalExam $medicalExam)
    {
        // Check if user can access this exam
        $user = auth()->user();
        if ($user->role !== 'admin') {
            $doctor = $user->doctor;
            if (!$doctor || $medicalExam->requesting_doctor_id !== $doctor->id) {
                return response()->json([
                    'message' => 'No tienes permisos para ver este examen'
                ], 403);
            }
        }
        
        $medicalExam->load(['patient', 'requestingDoctor', 'result']);

        return response()->json($medicalExam);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MedicalExam $medicalExam)
    {
        // Check if user can update this exam
        $user = auth()->user();
        if ($user->role !== 'admin') {
            $doctor = $user->doctor;
            if (!$doctor || $medicalExam->requesting_doctor_id !== $doctor->id) {
                return response()->json([
                    'message' => 'No tienes permisos para editar este examen'
                ], 403);
            }
            // Non-admin users cannot change requesting_doctor_id
            $request->request->remove('requesting_doctor_id');
        }
        
        $request->validate([
            'patient_id' => 'sometimes|exists:patients,id',
            'requesting_doctor_id' => 'sometimes|exists:doctors,id',
            'exam_type' => 'sometimes|string|max:255',
            'scheduled_date' => 'sometimes|date',
            'laboratory_area' => 'sometimes|string|max:255',
            'preparation_required' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'sometimes|in:scheduled,in_progress,completed,cancelled',
        ]);

        $medicalExam->update($request->all());

        return response()->json($medicalExam->load(['patient', 'requestingDoctor']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalExam $medicalExam)
    {
        // Check if user can delete this exam
        $user = auth()->user();
        if ($user->role !== 'admin') {
            $doctor = $user->doctor;
            if (!$doctor || $medicalExam->requesting_doctor_id !== $doctor->id) {
                return response()->json([
                    'message' => 'No tienes permisos para eliminar este examen'
                ], 403);
            }
        }
        
        $medicalExam->delete();

        return response()->json(['message' => 'Medical exam deleted successfully']);
    }

    /**
     * Update exam status.
     */
    public function updateStatus(Request $request, MedicalExam $medicalExam)
    {
        $request->validate([
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
        ]);

        $medicalExam->update(['status' => $request->status]);

        return response()->json($medicalExam->load(['patient', 'requestingDoctor']));
    }

    /**
     * Get exam result.
     */
    public function getResult(MedicalExam $medicalExam)
    {
        $result = $medicalExam->result;
        
        if (!$result) {
            return response()->json(['message' => 'No result found for this exam'], 404);
        }

        return response()->json($result);
    }

    /**
     * Add exam result.
     */
    public function addResult(Request $request, MedicalExam $medicalExam)
    {
        $request->validate([
            'result_data' => 'required|array',
            'interpretation' => 'nullable|string',
            'notes' => 'nullable|string',
            'abnormal_findings' => 'nullable|array',
            'recommendations' => 'nullable|string',
        ]);

        // Check if result already exists
        if ($medicalExam->result) {
            return response()->json([
                'message' => 'Result already exists for this exam. Use update instead.'
            ], 400);
        }

        $result = ExamResult::create([
            'medical_exam_id' => $medicalExam->id,
            'result_data' => $request->result_data,
            'interpretation' => $request->interpretation,
            'notes' => $request->notes,
            'abnormal_findings' => $request->abnormal_findings ?? [],
            'recommendations' => $request->recommendations,
            'result_date' => now(),
        ]);

        // Update exam status to completed
        $medicalExam->update(['status' => 'completed']);

        return response()->json($result, 201);
    }

    /**
     * Update exam result.
     */
    public function updateResult(Request $request, MedicalExam $medicalExam)
    {
        $result = $medicalExam->result;
        
        if (!$result) {
            return response()->json(['message' => 'No result found for this exam'], 404);
        }

        $request->validate([
            'result_data' => 'sometimes|array',
            'interpretation' => 'nullable|string',
            'notes' => 'nullable|string',
            'abnormal_findings' => 'nullable|array',
            'recommendations' => 'nullable|string',
        ]);

        $result->update($request->all());

        return response()->json($result);
    }

    /**
     * Get exam statistics.
     */
    public function stats(): JsonResponse
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            // Admin sees all exam stats
            $stats = [
                'total' => MedicalExam::count(),
                'scheduled' => MedicalExam::where('status', 'scheduled')->count(),
                'in_progress' => MedicalExam::where('status', 'in_progress')->count(),
                'completed' => MedicalExam::where('status', 'completed')->count(),
                'cancelled' => MedicalExam::where('status', 'cancelled')->count(),
                'today' => MedicalExam::whereDate('scheduled_date', today())->count(),
                'this_week' => MedicalExam::whereBetween('scheduled_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'this_month' => MedicalExam::whereMonth('scheduled_date', now()->month)->count(),
                'pending_results' => MedicalExam::where('status', 'completed')->whereNull('result')->count(),
            ];
        } elseif ($user->role === 'lab_technician') {
            // Lab technicians see all exams but limited details
            $stats = [
                'total' => MedicalExam::count(),
                'scheduled' => MedicalExam::where('status', 'scheduled')->count(),
                'in_progress' => MedicalExam::where('status', 'in_progress')->count(),
                'completed' => MedicalExam::where('status', 'completed')->count(),
                'cancelled' => MedicalExam::where('status', 'cancelled')->count(),
                'today' => MedicalExam::whereDate('scheduled_date', today())->count(),
                'this_week' => MedicalExam::whereBetween('scheduled_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'this_month' => MedicalExam::whereMonth('scheduled_date', now()->month)->count(),
                'pending_results' => MedicalExam::where('status', 'completed')->whereNull('result')->count(),
            ];
        } elseif ($user->role === 'doctor') {
            // Doctors see stats only for their requested exams
            $doctor = $user->doctor;
            
            if (!$doctor) {
                $stats = [
                    'total' => 0,
                    'scheduled' => 0,
                    'in_progress' => 0,
                    'completed' => 0,
                    'cancelled' => 0,
                    'today' => 0,
                    'this_week' => 0,
                    'this_month' => 0,
                    'pending_results' => 0,
                ];
            } else {
                $stats = [
                    'total' => MedicalExam::where('requesting_doctor_id', $doctor->id)->count(),
                    'scheduled' => MedicalExam::where('requesting_doctor_id', $doctor->id)->where('status', 'scheduled')->count(),
                    'in_progress' => MedicalExam::where('requesting_doctor_id', $doctor->id)->where('status', 'in_progress')->count(),
                    'completed' => MedicalExam::where('requesting_doctor_id', $doctor->id)->where('status', 'completed')->count(),
                    'cancelled' => MedicalExam::where('requesting_doctor_id', $doctor->id)->where('status', 'cancelled')->count(),
                    'today' => MedicalExam::where('requesting_doctor_id', $doctor->id)->whereDate('scheduled_date', today())->count(),
                    'this_week' => MedicalExam::where('requesting_doctor_id', $doctor->id)->whereBetween('scheduled_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                    'this_month' => MedicalExam::where('requesting_doctor_id', $doctor->id)->whereMonth('scheduled_date', now()->month)->count(),
                    'pending_results' => MedicalExam::where('requesting_doctor_id', $doctor->id)->where('status', 'completed')->whereNull('result')->count(),
                ];
            }
        } else {
            // Other roles see no exam stats
            $stats = [
                'total' => 0,
                'scheduled' => 0,
                'in_progress' => 0,
                'completed' => 0,
                'cancelled' => 0,
                'today' => 0,
                'this_week' => 0,
                'this_month' => 0,
                'pending_results' => 0,
            ];
        }
        
        return response()->json($stats);
    }
}
