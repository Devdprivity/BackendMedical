<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalExam;
use App\Models\ExamResult;
use Illuminate\Http\Request;

class MedicalExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MedicalExam::with(['patient', 'requestingDoctor', 'clinic']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by exam type
        if ($request->has('exam_type')) {
            $query->where('exam_type', 'like', '%' . $request->exam_type . '%');
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('exam_date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('exam_date', '<=', $request->to_date);
        }

        // Filter by requesting doctor
        if ($request->has('doctor_id')) {
            $query->where('requesting_doctor_id', $request->doctor_id);
        }

        // Filter by clinic
        if ($request->has('clinic_id')) {
            $query->where('clinic_id', $request->clinic_id);
        }

        // Search by patient name
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('patient', function ($patientQuery) use ($search) {
                $patientQuery->where('name', 'like', '%' . $search . '%');
            });
        }

        $exams = $query->orderBy('exam_date', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($exams);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'requesting_doctor_id' => 'required|exists:doctors,id',
            'clinic_id' => 'required|exists:clinics,id',
            'exam_type' => 'required|string|max:255',
            'exam_date' => 'required|date',
            'urgency' => 'required|in:routine,urgent,stat',
            'instructions' => 'nullable|string',
            'preparation_notes' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
        ]);

        $exam = MedicalExam::create([
            'patient_id' => $request->patient_id,
            'requesting_doctor_id' => $request->requesting_doctor_id,
            'clinic_id' => $request->clinic_id,
            'exam_type' => $request->exam_type,
            'exam_date' => $request->exam_date,
            'urgency' => $request->urgency,
            'instructions' => $request->instructions,
            'preparation_notes' => $request->preparation_notes,
            'cost' => $request->cost,
            'status' => 'scheduled',
        ]);

        return response()->json($exam->load(['patient', 'requestingDoctor', 'clinic']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(MedicalExam $medicalExam)
    {
        $medicalExam->load(['patient', 'requestingDoctor', 'clinic', 'result']);

        return response()->json($medicalExam);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MedicalExam $medicalExam)
    {
        $request->validate([
            'patient_id' => 'sometimes|exists:patients,id',
            'requesting_doctor_id' => 'sometimes|exists:doctors,id',
            'clinic_id' => 'sometimes|exists:clinics,id',
            'exam_type' => 'sometimes|string|max:255',
            'exam_date' => 'sometimes|date',
            'urgency' => 'sometimes|in:routine,urgent,stat',
            'instructions' => 'nullable|string',
            'preparation_notes' => 'nullable|string',
            'cost' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:scheduled,in_progress,completed,cancelled',
        ]);

        $medicalExam->update($request->all());

        return response()->json($medicalExam->load(['patient', 'requestingDoctor', 'clinic']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalExam $medicalExam)
    {
        $medicalExam->delete();

        return response()->json(['message' => 'Medical exam deleted successfully']);
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
}
