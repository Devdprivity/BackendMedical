<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Surgery;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SurgeryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Surgery::with(['patient', 'mainSurgeon']);
        
        // If user is not admin, only show surgeries they are involved in
        $user = auth()->user();
        if ($user->role !== 'admin') {
            $doctor = $user->doctor;
            if ($doctor) {
                // Show surgeries where this doctor is the main surgeon
                $query->where('main_surgeon_id', $doctor->id);
            } else {
                // Non-doctor users see no surgeries
                $query->where('id', -1); // This will return empty result
            }
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by surgeon (only for admin)
        if ($request->has('surgeon_id') && $user->role === 'admin') {
            $query->where('main_surgeon_id', $request->surgeon_id);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('date_time', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('date_time', '<=', $request->to_date);
        }

        // Search by patient name
        if ($request->has('search')) {
            $query->whereHas('patient', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $surgeries = $query->orderBy('date_time', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($surgeries);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        // If user is not admin, set main_surgeon_id to their own doctor record
        if ($user->role !== 'admin') {
            $doctor = $user->doctor;
            if (!$doctor) {
                return response()->json([
                    'message' => 'No tienes un perfil de doctor asociado',
                    'errors' => ['main_surgeon_id' => ['Usuario no tiene perfil de doctor']]
                ], 403);
            }
            $request->merge(['main_surgeon_id' => $doctor->id]);
        }
        
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'main_surgeon_id' => 'required|exists:doctors,id',
            'surgery_type' => 'required|string|max:255',
            'date_time' => 'required|date|after:now',
            'estimated_duration' => 'required|integer|min:30|max:720',
            'operating_room' => 'required|string|max:50',
            'anesthesia_type' => 'required|string|max:100',
            'preop_notes' => 'nullable|string',
            'assistant_surgeons' => 'nullable|array',
            'required_equipment' => 'nullable|array',
        ]);

        $surgery = Surgery::create([
            'patient_id' => $request->patient_id,
            'main_surgeon_id' => $request->main_surgeon_id,
            'surgery_type' => $request->surgery_type,
            'date_time' => $request->date_time,
            'estimated_duration' => $request->estimated_duration,
            'operating_room' => $request->operating_room,
            'anesthesia_type' => $request->anesthesia_type,
            'preop_notes' => $request->preop_notes,
            'assistant_surgeons' => $request->assistant_surgeons ?? [],
            'required_equipment' => $request->required_equipment ?? [],
            'status' => 'scheduled',
        ]);

        return response()->json($surgery->load(['patient', 'mainSurgeon']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $surgery = Surgery::findOrFail($id);
        
        // Check if user can access this surgery
        $user = auth()->user();
        if ($user->role !== 'admin') {
            $doctor = $user->doctor;
            if (!$doctor || $surgery->main_surgeon_id !== $doctor->id) {
                return response()->json([
                    'message' => 'No tienes permisos para ver esta cirugía'
                ], 403);
            }
        }
        
        $surgery->load(['patient', 'mainSurgeon']);

        return response()->json($surgery);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $surgery = Surgery::findOrFail($id);
        
        // Check if user can update this surgery
        $user = auth()->user();
        if ($user->role !== 'admin') {
            $doctor = $user->doctor;
            if (!$doctor || $surgery->main_surgeon_id !== $doctor->id) {
                return response()->json([
                    'message' => 'No tienes permisos para editar esta cirugía'
                ], 403);
            }
            // Non-admin users cannot change main_surgeon_id
            $request->request->remove('main_surgeon_id');
        }
        
        $request->validate([
            'patient_id' => 'sometimes|exists:patients,id',
            'main_surgeon_id' => 'sometimes|exists:doctors,id',
            'surgery_type' => 'sometimes|string|max:255',
            'date_time' => 'sometimes|date',
            'estimated_duration' => 'sometimes|integer|min:30|max:720',
            'operating_room' => 'sometimes|string|max:50',
            'anesthesia_type' => 'sometimes|string|max:100',
            'preop_notes' => 'nullable|string',
            'status' => 'sometimes|in:scheduled,in_progress,completed,cancelled,postponed',
            'assistant_surgeons' => 'nullable|array',
            'required_equipment' => 'nullable|array',
        ]);

        $surgery->update($request->all());

        return response()->json($surgery->load(['patient', 'mainSurgeon']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $surgery = Surgery::findOrFail($id);
        
        // Check if user can delete this surgery
        $user = auth()->user();
        if ($user->role !== 'admin') {
            $doctor = $user->doctor;
            if (!$doctor || $surgery->main_surgeon_id !== $doctor->id) {
                return response()->json([
                    'message' => 'No tienes permisos para eliminar esta cirugía'
                ], 403);
            }
        }
        
        $surgery->delete();

        return response()->json(['message' => 'Surgery deleted successfully']);
    }

    /**
     * Get today's surgeries.
     */
    public function today(Request $request)
    {
        $query = Surgery::with(['patient', 'mainSurgeon'])
            ->whereDate('date_time', today());
            
        // If user is not admin, only show their own surgeries
        $user = auth()->user();
        if ($user->role !== 'admin') {
            $doctor = $user->doctor;
            if ($doctor) {
                $query->where('main_surgeon_id', $doctor->id);
            } else {
                $query->where('id', -1); // Return empty result
            }
        }

        // Filter by surgeon if specified (only for admin)
        if ($request->has('surgeon_id') && $user->role === 'admin') {
            $query->where('main_surgeon_id', $request->surgeon_id);
        }

        $surgeries = $query->orderBy('date_time')
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
        ]);

        $surgery->update(['status' => $request->status]);

        return response()->json($surgery->load(['patient', 'mainSurgeon']));
    }

    /**
     * Get surgery statistics.
     */
    public function stats(): JsonResponse
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            // Admin sees all surgeries
            $stats = [
                'total' => Surgery::count(),
                'scheduled' => Surgery::where('status', 'scheduled')->count(),
                'in_progress' => Surgery::where('status', 'in_progress')->count(),
                'completed' => Surgery::where('status', 'completed')->count(),
                'cancelled' => Surgery::where('status', 'cancelled')->count(),
                'today' => Surgery::whereDate('date_time', today())->count(),
                'this_week' => Surgery::whereBetween('date_time', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'this_month' => Surgery::whereMonth('date_time', now()->month)->count(),
            ];
        } else {
            // Non-admin users see only their surgeries as main surgeon
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
                ];
            } else {
                $stats = [
                    'total' => Surgery::where('main_surgeon_id', $doctor->id)->count(),
                    'scheduled' => Surgery::where('main_surgeon_id', $doctor->id)->where('status', 'scheduled')->count(),
                    'in_progress' => Surgery::where('main_surgeon_id', $doctor->id)->where('status', 'in_progress')->count(),
                    'completed' => Surgery::where('main_surgeon_id', $doctor->id)->where('status', 'completed')->count(),
                    'cancelled' => Surgery::where('main_surgeon_id', $doctor->id)->where('status', 'cancelled')->count(),
                    'today' => Surgery::where('main_surgeon_id', $doctor->id)->whereDate('date_time', today())->count(),
                    'this_week' => Surgery::where('main_surgeon_id', $doctor->id)->whereBetween('date_time', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                    'this_month' => Surgery::where('main_surgeon_id', $doctor->id)->whereMonth('date_time', now()->month)->count(),
                ];
            }
        }
        
        return response()->json($stats);
    }
}
