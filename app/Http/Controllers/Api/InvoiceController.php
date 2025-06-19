<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['patient']);
        
        // Non-admin users see limited invoice data
        $user = auth()->user();
        if ($user->role !== 'admin') {
            if ($user->role === 'accountant') {
                // Accountants can see all invoices but with limited access
            } else {
                // Other roles should not see invoices - redirect or show empty
                $query->where('id', -1); // This will return empty result
            }
        }

        // Filter by payment status
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('issue_date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('issue_date', '<=', $request->to_date);
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

        $invoices = $query->orderBy('issue_date', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($invoices);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after:issue_date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Calculate totals
        $subtotal = 0;
        foreach ($request->items as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }

        $taxAmount = $subtotal * 0.19; // 19% tax rate
        $total = $subtotal + $taxAmount;

        // Generate invoice number
        $lastInvoice = Invoice::latest('id')->first();
        $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad(($lastInvoice ? $lastInvoice->id + 1 : 1), 4, '0', STR_PAD_LEFT);

        $invoice = Invoice::create([
            'patient_id' => $request->patient_id,
            'invoice_number' => $invoiceNumber,
            'issue_date' => $request->issue_date,
            'due_date' => $request->due_date,
            'items' => json_encode($request->items),
            'subtotal' => $subtotal,
            'tax' => $taxAmount,
            'total' => $total,
            'payment_status' => 'pending',
            'payment_method' => null,
            'notes' => $request->notes,
        ]);

        return response()->json($invoice->load(['patient']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['patient']);

        return response()->json($invoice);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        // Don't allow updates to paid invoices
        if ($invoice->payment_status === 'paid') {
            return response()->json([
                'message' => 'Cannot update a paid invoice'
            ], 400);
        }

        $request->validate([
            'patient_id' => 'sometimes|exists:patients,id',
            'issue_date' => 'sometimes|date',
            'due_date' => 'sometimes|date',
            'items' => 'sometimes|array|min:1',
            'items.*.description' => 'required_with:items|string',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $updateData = $request->except(['items']);

        // Recalculate totals if items are updated
        if ($request->has('items')) {
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $taxAmount = $subtotal * 0.19; // 19% tax rate
            $total = $subtotal + $taxAmount;

            $updateData = array_merge($updateData, [
                'items' => json_encode($request->items),
                'subtotal' => $subtotal,
                'tax' => $taxAmount,
                'total' => $total,
            ]);
        }

        $invoice->update($updateData);

        return response()->json($invoice->load(['patient']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        // Only allow deletion of unpaid invoices
        if ($invoice->payment_status === 'paid') {
            return response()->json([
                'message' => 'Cannot delete a paid invoice'
            ], 400);
        }

        $invoice->delete();

        return response()->json(['message' => 'Invoice deleted successfully']);
    }

    /**
     * Update payment status.
     */
    public function updatePaymentStatus(Request $request, Invoice $invoice)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,cancelled,overdue',
            'payment_method' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $updateData = [
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
        ];

        $invoice->update($updateData);

        return response()->json($invoice->load(['patient']));
    }

    /**
     * Get overdue invoices.
     */
    public function overdue(Request $request)
    {
        $query = Invoice::with(['patient'])
            ->where('payment_status', 'pending')
            ->where('due_date', '<', now());

        $overdueInvoices = $query->orderBy('due_date')
            ->paginate($request->get('per_page', 15));

        return response()->json($overdueInvoices);
    }

    /**
     * Get invoice statistics.
     */
    public function stats(Request $request)
    {
        $user = auth()->user();
        
        if ($user->role === 'admin' || $user->role === 'accountant') {
            // Admin and accountants see all invoice stats
            $stats = [
                'total_invoices' => Invoice::count(),
                'pending_invoices' => Invoice::where('payment_status', 'pending')->count(),
                'paid_invoices' => Invoice::where('payment_status', 'paid')->count(),
                'overdue_invoices' => Invoice::where('payment_status', 'pending')
                    ->where('due_date', '<', now())->count(),
                'total_revenue' => Invoice::where('payment_status', 'paid')->sum('total'),
                'pending_revenue' => Invoice::where('payment_status', 'pending')->sum('total'),
                'monthly_revenue' => Invoice::where('payment_status', 'paid')
                    ->whereMonth('issue_date', now()->month)->sum('total'),
                'average_invoice' => Invoice::where('payment_status', 'paid')->avg('total') ?? 0,
            ];
        } else {
            // Other roles see limited or no financial data
            $stats = [
                'total_invoices' => 0,
                'pending_invoices' => 0,
                'paid_invoices' => 0,
                'overdue_invoices' => 0,
                'total_revenue' => 0,
                'pending_revenue' => 0,
                'monthly_revenue' => 0,
                'average_invoice' => 0,
            ];
        }

        return response()->json($stats);
    }
}
