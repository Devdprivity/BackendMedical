<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medication;
use App\Traits\FiltersUserData;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MedicationController extends Controller
{
    use FiltersUserData;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // Check if user can access medications
        if (!$this->canUserAccess('view', 'medications')) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver medicamentos'
            ], 403);
        }

        $query = Medication::query();
        
        // Apply user-specific filters
        $query = $this->applyUserFilters($query, $request, 'medications');
        
        // Apply subscription limits
        $query = $this->applySubscriptionLimits($query, 'medications');

        // Additional filters
        if ($request->has('category')) {
            $query->where('category', 'like', '%' . $request->category . '%');
        }

        if ($request->has('manufacturer')) {
            $query->where('manufacturer', 'like', '%' . $request->manufacturer . '%');
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('commercial_name', 'like', '%' . $search . '%')
                  ->orWhere('generic_name', 'like', '%' . $search . '%')
                  ->orWhere('barcode', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('stock_status')) {
            switch ($request->stock_status) {
                case 'low':
                    $query->whereColumn('current_stock', '<=', 'min_stock');
                    break;
                case 'out':
                    $query->where('current_stock', 0);
                    break;
                case 'available':
                    $query->where('current_stock', '>', 0);
                    break;
            }
        }

        $medications = $query->orderBy('commercial_name')
            ->paginate($request->get('per_page', 15));

        // Hide sensitive data based on user role
        $this->hideSensitiveData($medications->items());

        return response()->json([
            'success' => true,
            'data' => $medications,
            'current_page' => $medications->currentPage(),
            'last_page' => $medications->lastPage(),
            'per_page' => $medications->perPage(),
            'total' => $medications->total(),
            'user_role' => auth()->user()->role
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Check if user can create medications
        if (!$this->canUserAccess('create', 'medications')) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para crear medicamentos'
            ], 403);
        }

        $request->validate([
            'commercial_name' => 'required|string|max:255',
            'generic_name' => 'required|string|max:255',
            'concentration' => 'required|string|max:100',
            'presentation' => 'required|string|max:100',
            'manufacturer' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'barcode' => 'nullable|string|unique:medications',
            'administration_route' => 'required|string|max:100',
            'indications' => 'nullable|string',
            'contraindications' => 'nullable|string',
            'side_effects' => 'nullable|string',
            'storage_notes' => 'nullable|string',
            'unit_cost' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'current_stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'max_stock' => 'required|integer|min:0',
            'expiration_date' => 'required|date|after:today',
            'requires_prescription' => 'boolean',
            'controlled' => 'boolean',
        ]);

        $medicationData = $request->all();
        
        // Assign creator and clinic
        $medicationData['created_by'] = auth()->id();
        $medicationData['clinic_id'] = auth()->user()->clinic_id;

        $medication = Medication::create($medicationData);

        return response()->json([
            'success' => true,
            'message' => 'Medicamento creado exitosamente',
            'data' => $medication
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Medication $medication): JsonResponse
    {
        // Check if user can access this medication
        if (!$this->canUserAccessMedication($medication)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver este medicamento'
            ], 403);
        }

        // Hide sensitive data based on user role
        $this->hideSensitiveData([$medication]);

        return response()->json([
            'success' => true,
            'data' => $medication
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Medication $medication): JsonResponse
    {
        // Check if user can update medications
        if (!$this->canUserAccess('update', 'medications')) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para actualizar medicamentos'
            ], 403);
        }

        $request->validate([
            'commercial_name' => 'sometimes|string|max:255',
            'generic_name' => 'sometimes|string|max:255',
            'concentration' => 'sometimes|string|max:100',
            'presentation' => 'sometimes|string|max:100',
            'manufacturer' => 'sometimes|string|max:255',
            'category' => 'sometimes|string|max:100',
            'barcode' => 'nullable|string|unique:medications,barcode,' . $medication->id,
            'administration_route' => 'sometimes|string|max:100',
            'indications' => 'nullable|string',
            'contraindications' => 'nullable|string',
            'side_effects' => 'nullable|string',
            'storage_notes' => 'nullable|string',
            'unit_cost' => 'sometimes|numeric|min:0',
            'sale_price' => 'sometimes|numeric|min:0',
            'min_stock' => 'sometimes|integer|min:0',
            'max_stock' => 'sometimes|integer|min:0',
            'expiration_date' => 'sometimes|date',
            'requires_prescription' => 'boolean',
            'controlled' => 'boolean',
        ]);

        // Don't allow direct stock updates through this endpoint
        $updateData = $request->except(['current_stock']);
        $medication->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Medicamento actualizado exitosamente'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medication $medication): JsonResponse
    {
        // Check if user can delete medications
        if (!$this->canUserAccess('delete', 'medications')) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para eliminar medicamentos'
            ], 403);
        }

        $medication->delete();

        return response()->json([
            'success' => true,
            'message' => 'Medicamento eliminado exitosamente'
        ]);
    }

    /**
     * Get medications with low stock.
     */
    public function lowStock(Request $request): JsonResponse
    {
        // Check if user can access medications
        if (!$this->canUserAccess('view', 'medications')) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver stock de medicamentos'
            ], 403);
        }

        $query = Medication::whereColumn('current_stock', '<=', 'min_stock');
        
        // Apply user-specific filters
        $query = $this->applyUserFilters($query, $request, 'medications');

        $lowStockMedications = $query->orderBy('current_stock')
            ->paginate($request->get('per_page', 15));

        // Hide sensitive data based on user role
        $this->hideSensitiveData($lowStockMedications->items());

        return response()->json([
            'success' => true,
            'data' => $lowStockMedications,
            'user_role' => auth()->user()->role
        ]);
    }

    /**
     * Get medications expiring soon.
     */
    public function expiring(Request $request): JsonResponse
    {
        // Check if user can access medications
        if (!$this->canUserAccess('view', 'medications')) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver medicamentos'
            ], 403);
        }

        $days = $request->get('days', 30);
        $query = Medication::whereDate('expiration_date', '<=', now()->addDays($days));
        
        // Apply user-specific filters
        $query = $this->applyUserFilters($query, $request, 'medications');

        $expiringMedications = $query->orderBy('expiration_date')
            ->paginate($request->get('per_page', 15));

        // Hide sensitive data based on user role
        $this->hideSensitiveData($expiringMedications->items());

        return response()->json([
            'success' => true,
            'data' => $expiringMedications,
            'user_role' => auth()->user()->role
        ]);
    }

    /**
     * Update medication stock.
     */
    public function updateStock(Request $request, Medication $medication): JsonResponse
    {
        // Only admin can update stock directly
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Solo el administrador puede actualizar el stock directamente'
            ], 403);
        }

        $request->validate([
            'current_stock' => 'required|integer|min:0',
            'reason' => 'required|string|max:255',
        ]);

        $oldStock = $medication->current_stock;
        $medication->update(['current_stock' => $request->current_stock]);

        // Log the stock change (you might want to create a stock_movements table)
        \Log::info("Stock updated for medication {$medication->id}: {$oldStock} -> {$request->current_stock}. Reason: {$request->reason}");

        return response()->json([
            'success' => true,
            'message' => 'Stock actualizado exitosamente',
            'old_stock' => $oldStock,
            'new_stock' => $request->current_stock
        ]);
    }

    /**
     * Get medication statistics.
     */
    public function stats(): JsonResponse
    {
        $user = auth()->user();
        $query = Medication::query();
        
        // Apply user-specific filters
        $query = $this->applyUserFilters($query, request(), 'medications');
        
        $stats = [
            'total_medications' => $query->count(),
            'available_medications' => (clone $query)->where('current_stock', '>', 0)->count(),
            'out_of_stock' => (clone $query)->where('current_stock', 0)->count(),
            'low_stock' => (clone $query)->whereColumn('current_stock', '<=', 'min_stock')->count(),
            'expiring_soon' => (clone $query)->whereDate('expiration_date', '<=', now()->addDays(30))->count(),
            'controlled_substances' => (clone $query)->where('controlled', true)->count(),
            'prescription_required' => (clone $query)->where('requires_prescription', true)->count(),
        ];

        // Add financial data only for admin
        if ($user->role === 'admin') {
            $stats['total_inventory_value'] = $query->sum(\DB::raw('current_stock * unit_cost'));
            $stats['potential_revenue'] = $query->sum(\DB::raw('current_stock * sale_price'));
        } else {
            $stats['total_inventory_value'] = 0; // Hidden
            $stats['potential_revenue'] = 0; // Hidden
        }

        // Add role-specific information
        $stats['user_role'] = $user->role;
        $stats['is_admin'] = $user->role === 'admin';
        $stats['can_see_financial_data'] = $user->role === 'admin';

        return response()->json($stats);
    }

    /**
     * Check if current user can access specific medication
     */
    private function canUserAccessMedication(Medication $medication): bool
    {
        $user = auth()->user();
        
        // Admin can access all medications
        if ($user->role === 'admin') {
            return true;
        }
        
        // Apply role-specific access checks
        switch ($user->role) {
            case 'doctor':
            case 'nurse':
                // Medical staff can access all medications for medical purposes
                return true;
                
            default:
                // Other roles have limited access
                return $medication->current_stock > 0; // Only available medications
        }
    }

    /**
     * Hide sensitive data based on user role
     */
    private function hideSensitiveData(array $medications): void
    {
        $user = auth()->user();
        
        // Admin sees everything
        if ($user->role === 'admin') {
            return;
        }
        
        foreach ($medications as $medication) {
            // Hide financial data for non-admin users
            $medication->makeHidden(['unit_cost', 'sale_price']);
            
            // Additional restrictions by role
            switch ($user->role) {
                case 'doctor':
                case 'nurse':
                    // Medical staff can see medical information but not financial
                    break;
                    
                default:
                    // Other roles see very limited information
                    $medication->makeHidden([
                        'unit_cost', 'sale_price', 'current_stock', 
                        'min_stock', 'max_stock', 'storage_notes'
                    ]);
                    break;
            }
        }
    }
}
