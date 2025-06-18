<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Surgery;
use Illuminate\Http\Request;

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
    public function store(Request $request)
    {
        $request->validate([
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

        return response()->json($surgery->load(['patient', 'mainSurgeon', 'clinic']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Surgery $surgery)
    {
        $surgery->load(['patient', 'mainSurgeon', 'clinic']);

        return response()->json($surgery);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Surgery $surgery)
    {
        $request->validate([
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

        $surgery->update($request->all());

        return response()->json($surgery->load(['patient', 'mainSurgeon', 'clinic']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Surgery $surgery)
    {
        $surgery->delete();

        return response()->json(['message' => 'Surgery deleted successfully']);
    }

    /**
     * Get today's surgeries.
     */
    public function today(Request $request)
    {
        $query = Surgery::with(['patient', 'mainSurgeon', 'clinic'])
            ->whereDate('surgery_date', today());

        // Filter by clinic if specified
        if ($request->has('clinic_id')) {
            $query->where('clinic_id', $request->clinic_id);
        }

        // Filter by surgeon if specified
        if ($request->has('surgeon_id')) {
            $query->where('main_surgeon_id', $request->surgeon_id);
        }

        $surgeries = $query->orderBy('surgery_date')
            ->get();

        return response()->json($surgeries);
    }

    /**
     * Update surgery status.
     */
    public function updateStatus(Request $request, Surgery $surgery)
    {
        $request->validate([
            'status' => 'required|in:scheduled,in_progress,completed,cancelled,postponed',
            'notes' => 'nullable|string',
        ]);

        $surgery->update([
            'status' => $request->status,
            'post_operative_notes' => $request->notes ?? $surgery->post_operative_notes,
        ]);

        return response()->json($surgery->load(['patient', 'mainSurgeon', 'clinic']));
    }
}
