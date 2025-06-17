<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalExam;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class MedicalExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = MedicalExam::with(['patient.user', 'requestingDoctor.user', 'clinic']);

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by exam type
            if ($request->has('exam_type')) {
                $query->where('exam_type', 'like', '%' . $request->exam_type . '%');
            }

            // Filter by date range
            if ($request->has('start_date')) {
                $query->whereDate('exam_date', '>=', $request->start_date);
            }
            if ($request->has('end_date')) {
                $query->whereDate('exam_date', '<=', $request->end_date);
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
                'message' => 'Error fetching medical exams: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
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

            $exam->load(['patient.user', 'requestingDoctor.user', 'clinic']);

            return response()->json([
                'success' => true,
                'message' => 'Medical exam scheduled successfully',
                'data' => $exam
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating medical exam: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $exam = MedicalExam::with(['patient.user', 'requestingDoctor.user', 'clinic', 'result'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $exam
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Medical exam not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $exam = MedicalExam::findOrFail($id);

            $validator = Validator::make($request->all(), [
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

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $exam->update($request->all());
            $exam->load(['patient.user', 'requestingDoctor.user', 'clinic']);

            return response()->json([
                'success' => true,
                'message' => 'Medical exam updated successfully',
                'data' => $exam
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating medical exam: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $exam = MedicalExam::findOrFail($id);
            
            // Only allow deletion of scheduled exams
            if ($exam->status !== 'scheduled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete exam that is not in scheduled status'
                ], 400);
            }

            $exam->delete();

            return response()->json([
                'success' => true,
                'message' => 'Medical exam deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting medical exam: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get exam result
     */
    public function getResult(string $id): JsonResponse
    {
        try {
            $exam = MedicalExam::with(['result.reportedBy'])->findOrFail($id);

            if (!$exam->result) {
                return response()->json([
                    'success' => false,
                    'message' => 'No result found for this exam'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $exam->result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Medical exam not found'
            ], 404);
        }
    }

    /**
     * Add exam result
     */
    public function addResult(Request $request, string $id): JsonResponse
    {
        try {
            $exam = MedicalExam::findOrFail($id);

            if ($exam->result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Result already exists for this exam. Use PUT to update.'
                ], 400);
            }

            $validator = Validator::make($request->all(), [
                'results' => 'required|string',
                'interpretation' => 'nullable|string',
                'recommendations' => 'nullable|string',
                'reported_by' => 'required|exists:doctors,id',
                'report_date' => 'required|date',
                'attachments' => 'nullable|array',
                'abnormal_findings' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $result = ExamResult::create([
                'medical_exam_id' => $exam->id,
                'results' => $request->results,
                'interpretation' => $request->interpretation,
                'recommendations' => $request->recommendations,
                'reported_by' => $request->reported_by,
                'report_date' => $request->report_date,
                'attachments' => $request->attachments ?? [],
                'abnormal_findings' => $request->abnormal_findings,
            ]);

            // Update exam status to completed
            $exam->update(['status' => 'completed']);

            $result->load(['reportedBy.user']);

            return response()->json([
                'success' => true,
                'message' => 'Exam result added successfully',
                'data' => $result
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding exam result: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update exam result
     */
    public function updateResult(Request $request, string $id): JsonResponse
    {
        try {
            $exam = MedicalExam::findOrFail($id);

            if (!$exam->result) {
                return response()->json([
                    'success' => false,
                    'message' => 'No result found for this exam. Use POST to create.'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'results' => 'sometimes|string',
                'interpretation' => 'nullable|string',
                'recommendations' => 'nullable|string',
                'reported_by' => 'sometimes|exists:doctors,id',
                'report_date' => 'sometimes|date',
                'attachments' => 'nullable|array',
                'abnormal_findings' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $exam->result->update($request->all());
            $exam->result->load(['reportedBy.user']);

            return response()->json([
                'success' => true,
                'message' => 'Exam result updated successfully',
                'data' => $exam->result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating exam result: ' . $e->getMessage()
            ], 500);
        }
    }
}
