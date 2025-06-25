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

    /**
     * Generate payment link with QR code
     */
    public function generateLink(Request $request): JsonResponse
    {
        $user = auth()->user();

        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'patient_id' => 'nullable|exists:patients,id',
            'amount' => 'required|numeric|min:0.01',
            'concept' => 'required|string|max:255',
        ]);

        $paymentMethod = PaymentMethod::find($request->payment_method_id);

        // Verificar permisos
        if ($user->role !== 'admin' && $paymentMethod->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para usar este método de pago',
            ], 403);
        }

        // Verificar que el método esté activo
        if (!$paymentMethod->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Este método de pago no está activo',
            ], 422);
        }

        // Generar token único para el link
        $token = bin2hex(random_bytes(32));
        
        // Crear registro de link de pago
        $linkData = [
            'token' => $token,
            'payment_method_id' => $paymentMethod->id,
            'doctor_id' => $user->id,
            'patient_id' => $request->patient_id,
            'amount' => $request->amount,
            'currency' => $paymentMethod->currency,
            'concept' => $request->concept,
            'expires_at' => now()->addDays(7), // Link válido por 7 días
            'created_at' => now(),
        ];

        // Guardar en caché (o podrías crear una tabla específica)
        cache()->put("payment_link_{$token}", $linkData, now()->addDays(7));

        // Generar URL del link de pago
        $paymentUrl = $this->generatePaymentUrl($paymentMethod, $token, $linkData);

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'payment_url' => $paymentUrl,
                'amount' => $request->amount,
                'currency' => $paymentMethod->currency,
                'concept' => $request->concept,
                'method_type' => $paymentMethod->type,
                'expires_at' => $linkData['expires_at']->toISOString(),
            ],
        ]);
    }

    /**
     * Generate specific payment URL based on payment method type
     */
    private function generatePaymentUrl(PaymentMethod $paymentMethod, string $token, array $linkData): string
    {
        $baseUrl = config('app.url');
        
        switch ($paymentMethod->type) {
            case 'paypal':
                return $this->generatePayPalUrl($paymentMethod, $linkData);
                
            case 'binance_pay':
                return $this->generateBinancePayUrl($paymentMethod, $linkData);
                
            case 'pago_movil':
                return $this->generatePagoMovilUrl($paymentMethod, $linkData, $token);
                
            case 'stripe':
                return $this->generateStripeUrl($paymentMethod, $linkData);
                
            default:
                // Link genérico que muestra la información del pago
                return "{$baseUrl}/pay/{$token}";
        }
    }

    /**
     * Generate PayPal payment URL
     */
    private function generatePayPalUrl(PaymentMethod $paymentMethod, array $linkData): string
    {
        $config = $paymentMethod->config;
        $email = $config['paypal_email'] ?? '';
        
        $params = http_build_query([
            'cmd' => '_xclick',
            'business' => $email,
            'item_name' => $linkData['concept'],
            'amount' => $linkData['amount'],
            'currency_code' => $linkData['currency'],
            'return' => config('app.url') . '/payment/success',
            'cancel_return' => config('app.url') . '/payment/cancel',
        ]);
        
        return "https://www.paypal.com/cgi-bin/webscr?{$params}";
    }

    /**
     * Generate Binance Pay URL (placeholder - requires Binance Pay API integration)
     */
    private function generateBinancePayUrl(PaymentMethod $paymentMethod, array $linkData): string
    {
        // Para Binance Pay necesitarías integrar con su API
        // Por ahora devolvemos un link genérico
        $baseUrl = config('app.url');
        return "{$baseUrl}/pay/binance/{$linkData['token']}";
    }

    /**
     * Generate Pago Móvil information URL
     */
    private function generatePagoMovilUrl(PaymentMethod $paymentMethod, array $linkData, string $token): string
    {
        $baseUrl = config('app.url');
        return "{$baseUrl}/pay/pago-movil/{$token}";
    }

    /**
     * Generate Stripe payment URL
     */
    private function generateStripeUrl(PaymentMethod $paymentMethod, array $linkData): string
    {
        // Para Stripe necesitarías crear una sesión de checkout
        // Por ahora devolvemos un link genérico
        $baseUrl = config('app.url');
        return "{$baseUrl}/pay/stripe/{$linkData['token']}";
    }

    /**
     * Get payment link information (public endpoint)
     */
    public function getPaymentLink(string $token): JsonResponse
    {
        $linkData = cache()->get("payment_link_{$token}");
        
        if (!$linkData) {
            return response()->json([
                'success' => false,
                'message' => 'Link de pago no encontrado o expirado',
            ], 404);
        }

        $paymentMethod = PaymentMethod::with(['user'])->find($linkData['payment_method_id']);
        
        if (!$paymentMethod || !$paymentMethod->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Método de pago no disponible',
            ], 422);
        }

        // Obtener información del paciente si existe
        $patient = null;
        if ($linkData['patient_id']) {
            $patient = \App\Models\Patient::find($linkData['patient_id']);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'amount' => $linkData['amount'],
                'currency' => $linkData['currency'],
                'concept' => $linkData['concept'],
                'doctor' => [
                    'name' => $paymentMethod->user->name,
                    'specialty' => $paymentMethod->user->specialty,
                ],
                'patient' => $patient ? [
                    'name' => $patient->first_name . ' ' . $patient->last_name,
                ] : null,
                'payment_method' => [
                    'type' => $paymentMethod->type,
                    'type_name' => $paymentMethod->type_name,
                    'instructions' => $paymentMethod->instructions,
                    'config' => $this->getPublicConfig($paymentMethod),
                ],
                'expires_at' => $linkData['expires_at'],
            ],
        ]);
    }

    /**
     * Get public configuration for payment method (without sensitive data)
     */
    private function getPublicConfig(PaymentMethod $paymentMethod): array
    {
        $config = $paymentMethod->config;
        
        switch ($paymentMethod->type) {
            case 'paypal':
                return [
                    'email' => $config['paypal_email'] ?? '',
                ];
                
            case 'pago_movil':
                return [
                    'phone' => $config['receiver_phone'] ?? '',
                    'bank' => $config['receiver_bank'] ?? '',
                    'name' => $config['receiver_name'] ?? '',
                    'ci' => $config['receiver_cedula'] ?? '',
                ];
                
            case 'binance_pay':
                return [
                    'binance_id' => $config['binance_id'] ?? '',
                ];
                
            default:
                return [];
        }
    }

    /**
     * Get patients and consultation fee for payment link generation
     */
    public function getPaymentLinkData(Request $request): JsonResponse
    {
        $user = auth()->user();

        try {
            // Verificar que el usuario tenga permisos
            if (!in_array($user->role, ['admin', 'doctor'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para acceder a esta funcionalidad'
                ], 403);
            }

            // Obtener pacientes del usuario
            $patientsQuery = \App\Models\Patient::query();
            
            // Filtrar por usuario actual (doctores solo ven sus pacientes)
            if ($user->role !== 'admin') {
                $patientsQuery->where('created_by', $user->id);
            }

            $patients = $patientsQuery->select('id', 'name', 'phone', 'email')
                                    ->orderBy('name')
                                    ->get()
                                    ->map(function ($patient) {
                                        return [
                                            'id' => $patient->id,
                                            'name' => $patient->name,
                                            'contact' => $patient->phone || $patient->email || 'Sin contacto',
                                            'phone' => $patient->phone,
                                            'email' => $patient->email
                                        ];
                                    });

            // Obtener tarifa de consulta del usuario
            $consultationFee = $user->consultation_fee ?? 0;
            $currency = $user->currency ?? 'USD';

            // Obtener métodos de pago activos del usuario
            $paymentMethods = PaymentMethod::where('user_id', $user->id)
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
                                                 'is_manual' => $method->isManualPayment(),
                                                 'is_automatic' => $method->isAutomaticPayment(),
                                                 'config' => $method->config
                                             ];
                                         });

            // Verificar si el usuario tiene métodos de pago configurados
            $hasPaymentMethods = $paymentMethods->count() > 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'patients' => $patients,
                    'consultation_fee' => $consultationFee,
                    'currency' => $currency,
                    'payment_methods' => $paymentMethods,
                    'has_payment_methods' => $hasPaymentMethods,
                    'user_info' => [
                        'name' => $user->name,
                        'specialty' => $user->specialty,
                        'role' => $user->role,
                        'onboarding_completed' => $user->onboarding_completed
                    ],
                    'stats' => [
                        'total_patients' => $patients->count(),
                        'total_payment_methods' => $paymentMethods->count(),
                        'active_payment_methods' => $paymentMethods->where('is_active', true)->count()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getPaymentLinkData: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar datos: ' . $e->getMessage()
            ], 500);
        }
    }
}
