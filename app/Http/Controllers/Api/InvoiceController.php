<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Invoice::with(['patient.user', 'clinic']);

            // Filter by payment status
            if ($request->has('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }

            // Filter by date range
            if ($request->has('start_date')) {
                $query->whereDate('invoice_date', '>=', $request->start_date);
            }
            if ($request->has('end_date')) {
                $query->whereDate('invoice_date', '<=', $request->end_date);
            }

            // Filter by clinic
            if ($request->has('clinic_id')) {
                $query->where('clinic_id', $request->clinic_id);
            }

            // Search by patient name or invoice number
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', '%' . $search . '%')
                      ->orWhereHas('patient', function ($patientQuery) use ($search) {
                          $patientQuery->where('name', 'like', '%' . $search . '%');
                      });
                });
            }

            $invoices = $query->orderBy('invoice_date', 'desc')
                ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $invoices->items(),
                'pagination' => [
                    'current_page' => $invoices->currentPage(),
                    'per_page' => $invoices->perPage(),
                    'total' => $invoices->total(),
                    'last_page' => $invoices->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching invoices: ' . $e->getMessage()
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
            'clinic_id' => 'required|exists:clinics,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after:invoice_date',
            'services' => 'required|array|min:1',
            'services.*.description' => 'required|string',
            'services.*.quantity' => 'required|integer|min:1',
            'services.*.unit_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Calculate totals
            $subtotal = 0;
            foreach ($request->services as $service) {
                $subtotal += $service['quantity'] * $service['unit_price'];
            }

            $discount = $request->discount_amount ?? 0;
            $taxRate = $request->tax_rate ?? 0;
            $taxAmount = ($subtotal - $discount) * ($taxRate / 100);
            $total = $subtotal - $discount + $taxAmount;

            // Generate invoice number
            $lastInvoice = Invoice::latest('id')->first();
            $invoiceNumber = 'INV-' . str_pad(($lastInvoice ? $lastInvoice->id + 1 : 1), 6, '0', STR_PAD_LEFT);

            $invoice = Invoice::create([
                'patient_id' => $request->patient_id,
                'clinic_id' => $request->clinic_id,
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'services' => $request->services,
                'subtotal' => $subtotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discount,
                'total_amount' => $total,
                'payment_status' => 'pending',
                'notes' => $request->notes,
            ]);

            $invoice->load(['patient.user', 'clinic']);

            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully',
                'data' => $invoice
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $invoice = Invoice::with(['patient.user', 'clinic'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $invoice
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $invoice = Invoice::findOrFail($id);

            // Don't allow updates to paid invoices
            if ($invoice->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot update a paid invoice'
                ], 400);
            }

            $validator = Validator::make($request->all(), [
                'patient_id' => 'sometimes|exists:patients,id',
                'clinic_id' => 'sometimes|exists:clinics,id',
                'invoice_date' => 'sometimes|date',
                'due_date' => 'sometimes|date',
                'services' => 'sometimes|array|min:1',
                'services.*.description' => 'required_with:services|string',
                'services.*.quantity' => 'required_with:services|integer|min:1',
                'services.*.unit_price' => 'required_with:services|numeric|min:0',
                'tax_rate' => 'nullable|numeric|min:0|max:100',
                'discount_amount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = $request->except(['services']);

            // Recalculate totals if services are updated
            if ($request->has('services')) {
                $subtotal = 0;
                foreach ($request->services as $service) {
                    $subtotal += $service['quantity'] * $service['unit_price'];
                }

                $discount = $request->discount_amount ?? $invoice->discount_amount;
                $taxRate = $request->tax_rate ?? $invoice->tax_rate;
                $taxAmount = ($subtotal - $discount) * ($taxRate / 100);
                $total = $subtotal - $discount + $taxAmount;

                $updateData['services'] = $request->services;
                $updateData['subtotal'] = $subtotal;
                $updateData['tax_amount'] = $taxAmount;
                $updateData['total_amount'] = $total;
            }

            $invoice->update($updateData);
            $invoice->load(['patient.user', 'clinic']);

            return response()->json([
                'success' => true,
                'message' => 'Invoice updated successfully',
                'data' => $invoice
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $invoice = Invoice::findOrFail($id);
            
            // Don't allow deletion of paid invoices
            if ($invoice->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete a paid invoice'
                ], 400);
            }

            $invoice->delete();

            return response()->json([
                'success' => true,
                'message' => 'Invoice deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, string $id): JsonResponse
    {
        try {
            $invoice = Invoice::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'payment_status' => 'required|in:pending,paid,overdue,cancelled',
                'payment_date' => 'nullable|date',
                'payment_method' => 'nullable|string',
                'payment_notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = ['payment_status' => $request->payment_status];
            
            if ($request->payment_status === 'paid') {
                $updateData['payment_date'] = $request->payment_date ?? now();
                $updateData['payment_method'] = $request->payment_method;
            }

            if ($request->has('payment_notes')) {
                $updateData['payment_notes'] = $request->payment_notes;
            }

            $invoice->update($updateData);
            $invoice->load(['patient.user', 'clinic']);

            return response()->json([
                'success' => true,
                'message' => 'Payment status updated successfully',
                'data' => $invoice
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating payment status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get overdue invoices
     */
    public function overdue(Request $request): JsonResponse
    {
        try {
            $query = Invoice::with(['patient.user', 'clinic'])
                ->where('payment_status', '!=', 'paid')
                ->where('due_date', '<', now());

            // Filter by clinic if provided
            if ($request->has('clinic_id')) {
                $query->where('clinic_id', $request->clinic_id);
            }

            $invoices = $query->orderBy('due_date', 'asc')
                ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $invoices->items(),
                'pagination' => [
                    'current_page' => $invoices->currentPage(),
                    'per_page' => $invoices->perPage(),
                    'total' => $invoices->total(),
                    'last_page' => $invoices->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching overdue invoices: ' . $e->getMessage()
            ], 500);
        }
    }
}
