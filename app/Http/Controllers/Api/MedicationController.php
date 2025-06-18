<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medication;
use App\Models\MedicationMovement;
use Illuminate\Http\Request;

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
    public function store(Request $request)
    {
        $request->validate([
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

        return response()->json($medication, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Medication $medication)
    {
        $medication->load(['movements' => function ($query) {
            $query->orderBy('movement_date', 'desc')->limit(10);
        }]);

        return response()->json($medication);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Medication $medication)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'active_ingredient' => 'sometimes|string|max:255',
            'dosage' => 'sometimes|string|max:100',
            'form' => 'sometimes|string|max:100',
            'manufacturer' => 'sometimes|string|max:255',
            'category' => 'sometimes|string|max:100',
            'barcode' => 'nullable|string|unique:medications,barcode,' . $medication->id,
            'description' => 'nullable|string',
            'contraindications' => 'nullable|string',
            'side_effects' => 'nullable|string',
            'storage_conditions' => 'nullable|string',
            'unit_price' => 'sometimes|numeric|min:0',
            'minimum_stock' => 'sometimes|integer|min:0',
            'expiration_date' => 'sometimes|date',
            'requires_prescription' => 'boolean',
        ]);

        // Don't allow direct stock updates through this endpoint
        $updateData = $request->except(['current_stock']);
        $medication->update($updateData);

        return response()->json($medication);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medication $medication)
    {
        $medication->delete();

        return response()->json(['message' => 'Medication deleted successfully']);
    }

    /**
     * Get medications with low stock.
     */
    public function lowStock(Request $request)
    {
        $query = Medication::whereColumn('current_stock', '<=', 'minimum_stock');

        // Filter by clinic if specified
        if ($request->has('clinic_id')) {
            $query->where('clinic_id', $request->clinic_id);
        }

        $lowStockMedications = $query->orderBy('current_stock')
            ->paginate($request->get('per_page', 15));

        return response()->json($lowStockMedications);
    }

    /**
     * Get medications expiring soon.
     */
    public function expiring(Request $request)
    {
        $days = $request->get('days', 30);
        
        $query = Medication::where('expiration_date', '<=', now()->addDays($days))
            ->where('expiration_date', '>', now());

        $expiringMedications = $query->orderBy('expiration_date')
            ->paginate($request->get('per_page', 15));

        return response()->json($expiringMedications);
    }

    /**
     * Add stock movement.
     */
    public function addMovement(Request $request, Medication $medication)
    {
        $request->validate([
            'movement_type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string',
            'movement_date' => 'required|date',
        ]);

        // Check if there's enough stock for outgoing movements
        if ($request->movement_type === 'out' && $medication->current_stock < $request->quantity) {
            return response()->json([
                'message' => 'Insufficient stock'
            ], 400);
        }

        $movement = MedicationMovement::create([
            'medication_id' => $medication->id,
            'movement_type' => $request->movement_type,
            'quantity' => $request->quantity,
            'reason' => $request->reason,
            'movement_date' => $request->movement_date,
        ]);

        // Update medication stock
        if ($request->movement_type === 'in') {
            $medication->increment('current_stock', $request->quantity);
        } else {
            $medication->decrement('current_stock', $request->quantity);
        }

        return response()->json($movement, 201);
    }

    /**
     * Get stock movements for a medication.
     */
    public function movements(Request $request, Medication $medication)
    {
        $query = $medication->movements();

        // Filter by movement type
        if ($request->has('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('movement_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('movement_date', '<=', $request->to_date);
        }

        $movements = $query->orderBy('movement_date', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($movements);
    }
}
