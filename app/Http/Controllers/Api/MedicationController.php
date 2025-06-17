<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medication;
use App\Models\MedicationMovement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class MedicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Medication::query();

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', 'like', '%' . $request->category . '%');
        }

        // Filter by manufacturer
        if ($request->has('manufacturer')) {
            $query->where('manufacturer', 'like', '%' . $request->manufacturer . '%');
        }

        // Search by name or active ingredient
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('active_ingredient', 'like', '%' . $search . '%')
                  ->orWhere('barcode', 'like', '%' . $search . '%');
            });
        }

        // Filter by stock status
        if ($request->has('stock_status')) {
            switch ($request->stock_status) {
                case 'low':
                    $query->whereColumn('current_stock', '<=', 'minimum_stock');
                    break;
                case 'out':
                    $query->where('current_stock', 0);
                    break;
                case 'available':
                    $query->where('current_stock', '>', 0);
                    break;
            }
        }

        $medications = $query->orderBy('name')
            ->paginate($request->get('per_page', 15));

        return response()->json($medications);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'active_ingredient' => 'required|string|max:255',
            'dosage' => 'required|string|max:100',
            'form' => 'required|string|max:100',
            'manufacturer' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'barcode' => 'nullable|string|unique:medications',
            'description' => 'nullable|string',
            'contraindications' => 'nullable|string',
            'side_effects' => 'nullable|string',
            'storage_conditions' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'current_stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'expiration_date' => 'required|date|after:today',
            'requires_prescription' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $medication = Medication::create($request->all());

            // Create initial stock movement
            if ($medication->current_stock > 0) {
                MedicationMovement::create([
                    'medication_id' => $medication->id,
                    'movement_type' => 'in',
                    'quantity' => $medication->current_stock,
                    'reason' => 'Initial stock',
                    'movement_date' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Medication created successfully',
                'data' => $medication
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating medication: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $medication = Medication::with(['movements' => function ($query) {
                $query->orderBy('movement_date', 'desc')->limit(10);
            }])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $medication
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Medication not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $medication = Medication::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'active_ingredient' => 'sometimes|string|max:255',
                'dosage' => 'sometimes|string|max:100',
                'form' => 'sometimes|string|max:100',
                'manufacturer' => 'sometimes|string|max:255',
                'category' => 'sometimes|string|max:100',
                'barcode' => 'nullable|string|unique:medications,barcode,' . $id,
                'description' => 'nullable|string',
                'contraindications' => 'nullable|string',
                'side_effects' => 'nullable|string',
                'storage_conditions' => 'nullable|string',
                'unit_price' => 'sometimes|numeric|min:0',
                'minimum_stock' => 'sometimes|integer|min:0',
                'expiration_date' => 'sometimes|date',
                'requires_prescription' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Don't allow direct stock updates through this endpoint
            $updateData = $request->except(['current_stock']);
            $medication->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Medication updated successfully',
                'data' => $medication
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating medication: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $medication = Medication::findOrFail($id);
            
            // Don't allow deletion if there's current stock
            if ($medication->current_stock > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete medication with current stock. Please reduce stock to zero first.'
                ], 400);
            }

            $medication->delete();

            return response()->json([
                'success' => true,
                'message' => 'Medication deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting medication: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get medications with low stock
     */
    public function lowStock(Request $request): JsonResponse
    {
        try {
            $query = Medication::whereColumn('current_stock', '<=', 'minimum_stock')
                ->where('current_stock', '>=', 0);

            $medications = $query->orderBy('current_stock')
                ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $medications->items(),
                'pagination' => [
                    'current_page' => $medications->currentPage(),
                    'per_page' => $medications->perPage(),
                    'total' => $medications->total(),
                    'last_page' => $medications->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching low stock medications: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get medications expiring soon
     */
    public function expiring(Request $request): JsonResponse
    {
        try {
            $days = $request->get('days', 30); // Default to 30 days
            
            $query = Medication::where('expiration_date', '<=', now()->addDays($days))
                ->where('expiration_date', '>', now())
                ->where('current_stock', '>', 0);

            $medications = $query->orderBy('expiration_date')
                ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $medications->items(),
                'pagination' => [
                    'current_page' => $medications->currentPage(),
                    'per_page' => $medications->perPage(),
                    'total' => $medications->total(),
                    'last_page' => $medications->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching expiring medications: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add stock movement
     */
    public function addMovement(Request $request, string $id): JsonResponse
    {
        try {
            $medication = Medication::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'movement_type' => 'required|in:in,out',
                'quantity' => 'required|integer|min:1',
                'reason' => 'required|string|max:255',
                'movement_date' => 'required|date',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if there's enough stock for outgoing movements
            if ($request->movement_type === 'out' && $medication->current_stock < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock. Current stock: ' . $medication->current_stock
                ], 400);
            }

            // Create movement record
            $movement = MedicationMovement::create([
                'medication_id' => $medication->id,
                'movement_type' => $request->movement_type,
                'quantity' => $request->quantity,
                'reason' => $request->reason,
                'movement_date' => $request->movement_date,
                'notes' => $request->notes,
            ]);

            // Update medication stock
            if ($request->movement_type === 'in') {
                $medication->increment('current_stock', $request->quantity);
            } else {
                $medication->decrement('current_stock', $request->quantity);
            }

            $medication->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Stock movement recorded successfully',
                'data' => [
                    'movement' => $movement,
                    'updated_stock' => $medication->current_stock
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error recording stock movement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get medication movements
     */
    public function movements(Request $request, string $id): JsonResponse
    {
        try {
            $medication = Medication::findOrFail($id);

            $query = MedicationMovement::where('medication_id', $id);

            // Filter by movement type
            if ($request->has('movement_type')) {
                $query->where('movement_type', $request->movement_type);
            }

            // Filter by date range
            if ($request->has('start_date')) {
                $query->whereDate('movement_date', '>=', $request->start_date);
            }
            if ($request->has('end_date')) {
                $query->whereDate('movement_date', '<=', $request->end_date);
            }

            $movements = $query->orderBy('movement_date', 'desc')
                ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $movements->items(),
                'pagination' => [
                    'current_page' => $movements->currentPage(),
                    'per_page' => $movements->perPage(),
                    'total' => $movements->total(),
                    'last_page' => $movements->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching medication movements: ' . $e->getMessage()
            ], 500);
        }
    }
}
