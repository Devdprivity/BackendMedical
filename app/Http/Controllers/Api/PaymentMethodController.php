<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Traits\FiltersUserData;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class PaymentMethodController extends Controller
{
    use FiltersUserData;

    /**
     * Display a listing of payment methods.
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        $query = PaymentMethod::query();

        // Filtrar por usuario actual (doctores solo ven sus métodos)
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        // Filtros adicionales
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $paymentMethods = $query->with(['user', 'clinic'])
                               ->orderBy('order')
                               ->orderBy('created_at', 'desc')
                               ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $paymentMethods,
            'available_types' => PaymentMethod::TYPES,
            'available_currencies' => PaymentMethod::CURRENCIES,
        ]);
    }

    /**
     * Store a newly created payment method.
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        // Validación básica
        $request->validate([
            'type' => ['required', Rule::in(array_keys(PaymentMethod::TYPES))],
            'consultation_fee' => 'required|numeric|min:0',
            'currency' => ['required', Rule::in(array_keys(PaymentMethod::CURRENCIES))],
            'instructions' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        // Validación específica por tipo
        $this->validatePaymentMethodConfig($request);

        $paymentMethod = PaymentMethod::create([
            'user_id' => $user->id,
            'clinic_id' => $user->clinic_id,
            'type' => $request->type,
            'config' => $this->buildPaymentConfig($request),
            'consultation_fee' => $request->consultation_fee,
            'currency' => $request->currency,
            'instructions' => $request->instructions,
            'is_active' => $request->boolean('is_active', true),
            'order' => PaymentMethod::where('user_id', $user->id)->count(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Método de pago creado exitosamente',
            'data' => $paymentMethod->load(['user', 'clinic']),
        ], 201);
    }

    /**
     * Display the specified payment method.
     */
    public function show(PaymentMethod $paymentMethod): JsonResponse
    {
        $user = auth()->user();

        // Verificar permisos
        if ($user->role !== 'admin' && $paymentMethod->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver este método de pago',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $paymentMethod->load(['user', 'clinic']),
        ]);
    }

    /**
     * Update the specified payment method.
     */
    public function update(Request $request, PaymentMethod $paymentMethod): JsonResponse
    {
        $user = auth()->user();

        // Verificar permisos
        if ($user->role !== 'admin' && $paymentMethod->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para editar este método de pago',
            ], 403);
        }

        // Validación
        $request->validate([
            'type' => ['sometimes', Rule::in(array_keys(PaymentMethod::TYPES))],
            'consultation_fee' => 'sometimes|numeric|min:0',
            'currency' => ['sometimes', Rule::in(array_keys(PaymentMethod::CURRENCIES))],
            'instructions' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        // Validación específica por tipo si se está cambiando
        if ($request->has('type') && $request->type !== $paymentMethod->type) {
            $this->validatePaymentMethodConfig($request);
        }

        $updateData = $request->only(['consultation_fee', 'currency', 'instructions', 'is_active']);

        if ($request->has('type')) {
            $updateData['type'] = $request->type;
            $updateData['config'] = $this->buildPaymentConfig($request);
        }

        $paymentMethod->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Método de pago actualizado exitosamente',
            'data' => $paymentMethod->fresh(['user', 'clinic']),
        ]);
    }

    /**
     * Remove the specified payment method.
     */
    public function destroy(PaymentMethod $paymentMethod): JsonResponse
    {
        $user = auth()->user();

        // Verificar permisos
        if ($user->role !== 'admin' && $paymentMethod->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para eliminar este método de pago',
            ], 403);
        }

        // Verificar si tiene pagos asociados
        if ($paymentMethod->payments()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar un método de pago que tiene transacciones asociadas',
            ], 422);
        }

        $paymentMethod->delete();

        return response()->json([
            'success' => true,
            'message' => 'Método de pago eliminado exitosamente',
        ]);
    }

    /**
     * Get payment methods for a specific doctor (public endpoint for patients)
     */
    public function getForDoctor(Request $request, $doctorId): JsonResponse
    {
        $paymentMethods = PaymentMethod::where('user_id', $doctorId)
                                     ->active()
                                     ->orderBy('order')
                                     ->get()
                                     ->map(function ($method) {
                                         return [
                                             'id' => $method->id,
                                             'type' => $method->type,
                                             'type_name' => $method->type_name,
                                             'consultation_fee' => $method->consultation_fee,
                                             'currency' => $method->currency,
                                             'currency_name' => $method->currency_name,
                                             'instructions' => $method->instructions,
                                             'is_manual' => $method->isManualPayment(),
                                             'is_automatic' => $method->isAutomaticPayment(),
                                         ];
                                     });

        return response()->json([
            'success' => true,
            'data' => $paymentMethods,
        ]);
    }

    /**
     * Update payment methods order
     */
    public function updateOrder(Request $request): JsonResponse
    {
        $user = auth()->user();

        $request->validate([
            'payment_methods' => 'required|array',
            'payment_methods.*.id' => 'required|exists:payment_methods,id',
            'payment_methods.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->payment_methods as $methodData) {
            $paymentMethod = PaymentMethod::find($methodData['id']);
            
            // Verificar permisos
            if ($user->role !== 'admin' && $paymentMethod->user_id !== $user->id) {
                continue;
            }

            $paymentMethod->update(['order' => $methodData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Orden de métodos de pago actualizado exitosamente',
        ]);
    }

    /**
     * Validate payment method configuration based on type
     */
    private function validatePaymentMethodConfig(Request $request): void
    {
        switch ($request->type) {
            case 'paypal':
                $request->validate([
                    'paypal_email' => 'required|email',
                ]);
                break;

            case 'binance_pay':
                $request->validate([
                    'binance_id' => 'required|string',
                    'supported_networks' => 'required|array',
                    'supported_currencies' => 'required|array',
                ]);
                break;

            case 'pago_movil':
                $request->validate([
                    'receiver_phone' => 'required|string|size:11',
                    'receiver_cedula' => 'required|string',
                    'receiver_bank' => 'required|string',
                    'receiver_name' => 'required|string',
                ]);
                break;

            case 'stripe':
                $request->validate([
                    'stripe_account_id' => 'required|string',
                    'webhook_secret' => 'required|string',
                ]);
                break;
        }
    }

    /**
     * Build payment configuration array based on type
     */
    private function buildPaymentConfig(Request $request): array
    {
        switch ($request->type) {
            case 'paypal':
                return [
                    'paypal_email' => $request->paypal_email,
                ];

            case 'binance_pay':
                return [
                    'binance_id' => $request->binance_id,
                    'supported_networks' => $request->supported_networks ?? ['BSC', 'ETH'],
                    'supported_currencies' => $request->supported_currencies ?? ['USDT', 'BTC', 'BNB'],
                ];

            case 'pago_movil':
                return [
                    'receiver_phone' => $request->receiver_phone,
                    'receiver_cedula' => $request->receiver_cedula,
                    'receiver_bank' => $request->receiver_bank,
                    'receiver_name' => $request->receiver_name,
                ];

            case 'stripe':
                return [
                    'stripe_account_id' => $request->stripe_account_id,
                    'webhook_secret' => $request->webhook_secret,
                ];

            default:
                return [];
        }
    }
}
