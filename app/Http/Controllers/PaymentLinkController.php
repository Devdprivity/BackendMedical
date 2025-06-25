<?php

namespace App\Http\Controllers;

use App\Models\PaymentLink;
use App\Models\PaymentMethod;
use App\Models\Patient;
use App\Models\User;
use App\Traits\FiltersUserData;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentLinkController extends Controller
{
    use FiltersUserData;

    /**
     * Mostrar la vista principal de gestión de links de pago
     */
    public function index()
    {
        $user = Auth::user();
        
        // Verificar que el usuario tenga métodos de pago configurados
        $paymentMethods = PaymentMethod::where('user_id', $user->id)
                                     ->where('is_active', true)
                                     ->get();
        
        if ($paymentMethods->isEmpty()) {
            return redirect()->route('payment-methods.index')
                           ->with('warning', 'Debes configurar al menos un método de pago antes de crear links de pago.');
        }
        
        return view('payment-links.index', compact('paymentMethods'));
    }

    /**
     * Obtener lista de links de pago (API)
     */
    public function getLinks(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $query = PaymentLink::with(['patient', 'paymentMethod'])
                           ->where('doctor_id', $user->id);
        
        // Filtros
        if ($request->has('status') && $request->status !== '') {
            $query->where('payment_status', $request->status);
        }
        
        if ($request->has('patient_id') && $request->patient_id !== '') {
            $query->where('patient_id', $request->patient_id);
        }
        
        if ($request->has('method_id') && $request->method_id !== '') {
            $query->where('payment_method_id', $request->method_id);
        }
        
        // Búsqueda
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('concept', 'like', "%{$search}%")
                  ->orWhere('token', 'like', "%{$search}%")
                  ->orWhereHas('patient', function ($pq) use ($search) {
                      $pq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }
        
        $links = $query->orderBy('created_at', 'desc')
                      ->paginate($request->get('per_page', 10));
        
        return response()->json([
            'success' => true,
            'data' => $links,
        ]);
    }

    /**
     * Obtener datos para crear un link de pago
     */
    public function getCreateData(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Obtener métodos de pago del usuario
        $paymentMethods = PaymentMethod::where('user_id', $user->id)
                                     ->where('is_active', true)
                                     ->orderBy('order')
                                     ->get(['id', 'type', 'consultation_fee', 'currency']);
        
        // Obtener pacientes del usuario
        $patients = Patient::where('created_by', $user->id)
                          ->orderBy('name')
                          ->get(['id', 'name']);
        
        return response()->json([
            'success' => true,
            'data' => [
                'payment_methods' => $paymentMethods->map(function ($method) {
                    return [
                        'id' => $method->id,
                        'type' => $method->type,
                        'type_name' => PaymentMethod::TYPES[$method->type] ?? $method->type,
                        'consultation_fee' => $method->consultation_fee,
                        'currency' => $method->currency,
                    ];
                }),
                'patients' => $patients->map(function ($patient) {
                    return [
                        'id' => $patient->id,
                        'name' => $patient->name,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Crear un nuevo link de pago
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Validar los datos
        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'patient_id' => 'nullable|exists:patients,id',
            'amount' => 'required|numeric|min:0.01',
            'expires_in_hours' => 'required|integer|min:1|max:8760', // máximo 1 año
            'concept' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);
        
        // Verificar que el método de pago pertenece al usuario
        $paymentMethod = PaymentMethod::where('id', $request->payment_method_id)
                                    ->where('user_id', $user->id)
                                    ->where('is_active', true)
                                    ->firstOrFail();
        
        // Verificar que el paciente pertenece al usuario (si se especifica)
        if ($request->patient_id) {
            Patient::where('id', $request->patient_id)
                   ->where('created_by', $user->id)
                   ->firstOrFail();
        }
        
        // Calcular fecha de expiración
        $expiresAt = now()->addHours((int) $request->expires_in_hours);
        
        // Crear el link de pago
        $paymentLink = PaymentLink::create([
            'token' => PaymentLink::generateUniqueToken(),
            'doctor_id' => $user->id,
            'patient_id' => $request->patient_id,
            'payment_method_id' => $request->payment_method_id,
            'amount' => $request->amount,
            'currency' => $paymentMethod->currency,
            'concept' => $request->concept,
            'description' => $request->description,
            'expires_at' => $expiresAt,
            'is_active' => true,
            'payment_status' => PaymentLink::STATUS_PENDING,
            'view_count' => 0,
            'is_used' => false,
            'metadata' => [
                'created_from' => 'web_interface',
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
            ],
        ]);
        
        // Cargar relaciones para la respuesta
        $paymentLink->load(['patient', 'paymentMethod']);
        
        // Generar URLs
        $paymentUrl = url("/pay/{$paymentLink->token}");
        $qrUrl = url("/api/payment-links/{$paymentLink->token}/qr");
        
        return response()->json([
            'success' => true,
            'message' => 'Link de pago creado exitosamente',
            'data' => [
                'id' => $paymentLink->id,
                'token' => $paymentLink->token,
                'payment_url' => $paymentUrl,
                'qr_url' => $qrUrl,
                'amount' => $paymentLink->amount,
                'currency' => $paymentLink->currency,
                'concept' => $paymentLink->concept,
                'description' => $paymentLink->description,
                'expires_at' => $paymentLink->expires_at->toISOString(),
                'patient' => $paymentLink->patient ? [
                    'id' => $paymentLink->patient->id,
                    'name' => $paymentLink->patient->name,
                ] : null,
                'payment_method' => [
                    'id' => $paymentLink->paymentMethod->id,
                    'type' => $paymentLink->paymentMethod->type,
                    'type_name' => PaymentMethod::TYPES[$paymentLink->paymentMethod->type] ?? $paymentLink->paymentMethod->type,
                ],
            ],
        ], 201);
    }

    /**
     * Mostrar un link de pago específico (vista del doctor)
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $paymentLink = PaymentLink::with(['patient', 'paymentMethod', 'appointmentPayment'])
                                 ->where('doctor_id', $user->id)
                                 ->findOrFail($id);
        
        return view('payment-links.show', compact('paymentLink'));
    }

    /**
     * Desactivar un link de pago
     */
    public function deactivate($id): JsonResponse
    {
        $user = Auth::user();
        
        $paymentLink = PaymentLink::where('doctor_id', $user->id)
                                 ->findOrFail($id);
        
        $paymentLink->update([
            'is_active' => false,
            'payment_status' => PaymentLink::STATUS_EXPIRED,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Link de pago desactivado exitosamente',
        ]);
    }

    /**
     * Eliminar un link de pago
     */
    public function destroy($id): JsonResponse
    {
        $user = Auth::user();
        
        $paymentLink = PaymentLink::where('doctor_id', $user->id)
                                 ->findOrFail($id);
        
        // Solo permitir eliminar si no está usado
        if ($paymentLink->is_used) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar un link que ya ha sido usado para un pago',
            ], 422);
        }
        
        $paymentLink->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Link de pago eliminado exitosamente',
        ]);
    }

    /**
     * Vista pública del link de pago (para el cliente)
     */
    public function showPublic($token)
    {
        $paymentLink = PaymentLink::with(['doctor', 'patient', 'paymentMethod'])
                                 ->where('token', $token)
                                 ->firstOrFail();
        
        // Marcar como visto
        $paymentLink->markAsViewed([
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'viewed_at' => now()->toISOString(),
        ]);
        
        // Verificar si está expirado
        if ($paymentLink->isExpired()) {
            return view('payment-links.expired', compact('paymentLink'));
        }
        
        // Verificar si ya fue usado
        if ($paymentLink->is_used) {
            return view('payment-links.completed', compact('paymentLink'));
        }
        
        // Verificar si no está activo
        if (!$paymentLink->is_active) {
            return view('payment-links.inactive', compact('paymentLink'));
        }
        
        return view('payment-links.public', compact('paymentLink'));
    }

    /**
     * Generar código QR para un link de pago
     */
    public function generateQr($token)
    {
        $paymentLink = PaymentLink::where('token', $token)->firstOrFail();
        
        $qrCode = $paymentLink->generateQrCode(300);
        
        return response($qrCode)
               ->header('Content-Type', 'image/png')
               ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Obtener información del link de pago (API pública)
     */
    public function getPublicInfo($token): JsonResponse
    {
        $paymentLink = PaymentLink::with(['doctor', 'patient', 'paymentMethod'])
                                 ->where('token', $token)
                                 ->firstOrFail();
        
        // Marcar como visto
        $paymentLink->markAsViewed([
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'api_access' => true,
        ]);
        
        return response()->json([
            'success' => true,
            'data' => array_merge(
                $paymentLink->getPublicInfo(),
                ['payment_config' => $paymentLink->getPaymentConfig()]
            ),
        ]);
    }

    /**
     * Procesar el pago del link (para métodos automáticos)
     */
    public function processPayment(Request $request, $token): JsonResponse
    {
        $paymentLink = PaymentLink::with(['paymentMethod'])
                                 ->where('token', $token)
                                 ->firstOrFail();
        
        // Verificar que el link esté disponible
        if (!$paymentLink->isAvailable()) {
            return response()->json([
                'success' => false,
                'message' => 'Este link de pago no está disponible',
            ], 422);
        }
        
        // Procesar según el tipo de pago
        switch ($paymentLink->paymentMethod->type) {
            case 'stripe':
                return $this->processStripePayment($request, $paymentLink);
            case 'paypal':
                return $this->processPayPalPayment($request, $paymentLink);
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Método de pago no soportado para procesamiento automático',
                ], 422);
        }
    }

    /**
     * Confirmar pago manual
     */
    public function confirmManualPayment(Request $request, $token): JsonResponse
    {
        $paymentLink = PaymentLink::with(['paymentMethod'])
                                 ->where('token', $token)
                                 ->firstOrFail();
        
        // Verificar que el link esté disponible
        if (!$paymentLink->isAvailable()) {
            return response()->json([
                'success' => false,
                'message' => 'Este link de pago no está disponible',
            ], 422);
        }
        
        // Verificar que sea un método manual
        if (!$paymentLink->paymentMethod->isManualPayment()) {
            return response()->json([
                'success' => false,
                'message' => 'Este método de pago no requiere confirmación manual',
            ], 422);
        }
        
        // Validar datos según el tipo
        $this->validateManualPaymentData($request, $paymentLink->paymentMethod->type);
        
        // Crear el registro de pago pendiente de verificación
        DB::beginTransaction();
        
        try {
            $appointmentPayment = $paymentLink->appointmentPayment()->create([
                'patient_id' => $paymentLink->patient_id,
                'doctor_id' => $paymentLink->doctor_id,
                'payment_method_id' => $paymentLink->payment_method_id,
                'status' => 'pending',
                'payment_type' => $paymentLink->paymentMethod->type,
                'amount' => $paymentLink->amount,
                'currency' => $paymentLink->currency,
                'payment_data' => $this->buildManualPaymentData($request, $paymentLink->paymentMethod->type),
            ]);
            
            // Marcar el link como usado
            $paymentLink->markAsUsed($appointmentPayment->id);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Pago registrado exitosamente. Está pendiente de verificación por el doctor.',
                'data' => [
                    'payment_id' => $appointmentPayment->id,
                    'reference' => $appointmentPayment->payment_reference,
                ],
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago',
            ], 500);
        }
    }

    /**
     * Validar datos de pago manual
     */
    private function validateManualPaymentData(Request $request, $paymentType)
    {
        switch ($paymentType) {
            case 'pago_movil':
                $request->validate([
                    'sender_phone' => 'required|string|size:11',
                    'sender_cedula' => 'required|string',
                    'sender_bank' => 'required|string',
                    'reference' => 'required|string|size:6',
                    'transaction_date' => 'required|date',
                    'transaction_time' => 'required|string',
                ]);
                break;
                
            case 'binance_pay':
                $request->validate([
                    'transaction_hash' => 'required|string',
                    'network' => 'required|string',
                    'sender_wallet' => 'required|string',
                ]);
                break;
        }
    }

    /**
     * Construir datos de pago manual
     */
    private function buildManualPaymentData(Request $request, $paymentType)
    {
        switch ($paymentType) {
            case 'pago_movil':
                return [
                    'sender_phone' => $request->sender_phone,
                    'sender_cedula' => $request->sender_cedula,
                    'sender_bank' => $request->sender_bank,
                    'reference' => $request->reference,
                    'transaction_date' => $request->transaction_date,
                    'transaction_time' => $request->transaction_time,
                ];
                
            case 'binance_pay':
                return [
                    'transaction_hash' => $request->transaction_hash,
                    'network' => $request->network,
                    'sender_wallet' => $request->sender_wallet,
                ];
                
            default:
                return [];
        }
    }

    /**
     * Procesar pago con Stripe (placeholder)
     */
    private function processStripePayment(Request $request, PaymentLink $paymentLink)
    {
        // Aquí iría la integración con Stripe
        return response()->json([
            'success' => false,
            'message' => 'Integración con Stripe pendiente de implementación',
        ], 501);
    }

    /**
     * Procesar pago con PayPal (placeholder)
     */
    private function processPayPalPayment(Request $request, PaymentLink $paymentLink)
    {
        // Aquí iría la integración con PayPal
        return response()->json([
            'success' => false,
            'message' => 'Integración con PayPal pendiente de implementación',
        ], 501);
    }

    /**
     * Obtener estadísticas de links de pago
     */
    public function getStats(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $stats = [
            'total' => PaymentLink::where('doctor_id', $user->id)->count(),
            'active' => PaymentLink::where('doctor_id', $user->id)
                                  ->where('is_active', true)
                                  ->where('expires_at', '>', now())
                                  ->count(),
            'used' => PaymentLink::where('doctor_id', $user->id)
                                ->where('is_used', true)
                                ->count(),
            'total_amount' => PaymentLink::where('doctor_id', $user->id)
                                        ->where('payment_status', 'completed')
                                        ->sum('amount'),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
} 