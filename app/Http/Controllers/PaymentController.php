<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentPayment;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Show payment information for an appointment (public access via QR/link)
     */
    public function show(Request $request, $reference)
    {
        $payment = AppointmentPayment::where('payment_reference', $reference)
                                   ->with(['appointment.doctor', 'appointment.patient', 'paymentMethod'])
                                   ->firstOrFail();

        return view('payments.show', compact('payment'));
    }

    /**
     * Show payment options for an appointment
     */
    public function paymentOptions(Request $request, $appointmentId)
    {
        $appointment = Appointment::with(['doctor', 'patient'])
                                 ->findOrFail($appointmentId);

        // Obtener métodos de pago disponibles del doctor
        $paymentMethods = PaymentMethod::where('user_id', $appointment->doctor->user_id)
                                     ->active()
                                     ->orderBy('order')
                                     ->get();

        if ($paymentMethods->isEmpty()) {
            return view('payments.no-methods', compact('appointment'));
        }

        return view('payments.options', compact('appointment', 'paymentMethods'));
    }

    /**
     * Create a payment for an appointment
     */
    public function createPayment(Request $request, $appointmentId)
    {
        $appointment = Appointment::with(['doctor', 'patient'])
                                 ->findOrFail($appointmentId);

        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);

        // Verificar que el método de pago pertenece al doctor de la cita
        if ($paymentMethod->user_id !== $appointment->doctor->user_id) {
            return back()->withErrors(['payment_method_id' => 'Método de pago no válido para este doctor.']);
        }

        // Crear el pago
        $payment = AppointmentPayment::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $appointment->doctor_id,
            'payment_method_id' => $paymentMethod->id,
            'payment_type' => $paymentMethod->type,
            'amount' => $paymentMethod->consultation_fee,
            'currency' => $paymentMethod->currency,
            'status' => AppointmentPayment::STATUS_PENDING,
            'payment_data' => [],
        ]);

        // Actualizar estado de la cita
        $appointment->update(['payment_status' => 'pending']);

        // Redirigir según el tipo de pago
        if ($paymentMethod->isAutomaticPayment()) {
            return $this->handleAutomaticPayment($payment, $paymentMethod);
        } else {
            return $this->handleManualPayment($payment, $paymentMethod);
        }
    }

    /**
     * Handle automatic payment (PayPal, Stripe)
     */
    private function handleAutomaticPayment($payment, $paymentMethod)
    {
        $paymentUrl = $paymentMethod->generatePaymentUrl($payment->appointment, $payment->amount);
        
        if ($paymentUrl) {
            return redirect($paymentUrl);
        }

        return redirect()->route('payments.show', ['reference' => $payment->payment_reference])
                        ->with('error', 'No se pudo generar la URL de pago automático.');
    }

    /**
     * Handle manual payment (Pago Móvil, Binance Pay)
     */
    private function handleManualPayment($payment, $paymentMethod)
    {
        return redirect()->route('payments.manual', ['reference' => $payment->payment_reference]);
    }

    /**
     * Show manual payment form
     */
    public function showManualPaymentForm($reference)
    {
        $payment = AppointmentPayment::where('payment_reference', $reference)
                                   ->with(['appointment.doctor', 'appointment.patient', 'paymentMethod'])
                                   ->firstOrFail();

        if (!$payment->paymentMethod->isManualPayment()) {
            return redirect()->route('payments.show', ['reference' => $reference]);
        }

        return view('payments.manual', compact('payment'));
    }

    /**
     * Process manual payment submission
     */
    public function processManualPayment(Request $request, $reference)
    {
        $payment = AppointmentPayment::where('payment_reference', $reference)
                                   ->with(['paymentMethod'])
                                   ->firstOrFail();

        if ($payment->status !== AppointmentPayment::STATUS_PENDING) {
            return redirect()->route('payments.show', ['reference' => $reference])
                           ->with('error', 'Este pago ya ha sido procesado.');
        }

        // Validar según el tipo de pago
        $this->validateManualPaymentData($request, $payment->payment_type);

        // Guardar los datos del pago
        $paymentData = $this->buildPaymentData($request, $payment->payment_type);
        $payment->update([
            'payment_data' => $paymentData,
            'paid_at' => now(),
        ]);

        return redirect()->route('payments.show', ['reference' => $reference])
                        ->with('success', 'Información de pago enviada. Será verificada por el doctor.');
    }

    /**
     * Payment success callback
     */
    public function success(Request $request, $paymentId)
    {
        // Handle successful automatic payments (PayPal, Stripe callbacks)
        $payment = AppointmentPayment::where('payment_reference', $paymentId)->firstOrFail();
        
        // Verify payment with the payment provider
        // This would include API calls to PayPal, Stripe, etc.
        
        $payment->markAsCompleted(null, 'Pago automático completado');

        return view('payments.success', compact('payment'));
    }

    /**
     * Payment cancellation callback
     */
    public function cancel(Request $request, $appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        
        return view('payments.cancelled', compact('appointment'));
    }

    /**
     * Generate QR code for payment
     */
    public function generateQR($reference)
    {
        $payment = AppointmentPayment::where('payment_reference', $reference)->firstOrFail();
        
        $qrCode = $payment->generateQRCode();
        
        return response($qrCode)
               ->header('Content-Type', 'image/png');
    }

    /**
     * Generate payment link for sharing
     */
    public function generateLink($appointmentId)
    {
        $appointment = Appointment::with(['doctor'])->findOrFail($appointmentId);
        
        // Crear un token temporal para acceso sin autenticación
        $token = Str::random(32);
        
        // Guardar el token en cache por 24 horas
        cache()->put("payment_token_{$token}", $appointmentId, now()->addHours(24));
        
        $paymentUrl = route('payments.options-token', ['token' => $token]);
        
        return response()->json([
            'success' => true,
            'payment_url' => $paymentUrl,
            'qr_url' => route('payments.qr-link', ['token' => $token]),
        ]);
    }

    /**
     * Show payment options via token (for sharing)
     */
    public function showPaymentOptionsWithToken($token)
    {
        $appointmentId = cache()->get("payment_token_{$token}");
        
        if (!$appointmentId) {
            abort(404, 'Enlace de pago expirado o inválido');
        }
        
        return $this->paymentOptions(request(), $appointmentId);
    }

    /**
     * Validate manual payment data based on type
     */
    private function validateManualPaymentData(Request $request, $paymentType)
    {
        switch ($paymentType) {
            case 'paypal':
                $request->validate([
                    'paypal_email' => 'required|email',
                    'transaction_id' => 'required|string',
                ]);
                break;

            case 'binance_pay':
                $request->validate([
                    'binance_id' => 'required|string',
                    'transaction_hash' => 'required|string',
                    'network' => 'required|string',
                ]);
                break;

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
        }
    }

    /**
     * Build payment data array based on type
     */
    private function buildPaymentData(Request $request, $paymentType)
    {
        switch ($paymentType) {
            case 'paypal':
                return [
                    'paypal_email' => $request->paypal_email,
                    'transaction_id' => $request->transaction_id,
                    'submitted_at' => now()->toISOString(),
                ];

            case 'binance_pay':
                return [
                    'binance_id' => $request->binance_id,
                    'transaction_hash' => $request->transaction_hash,
                    'network' => $request->network,
                    'submitted_at' => now()->toISOString(),
                ];

            case 'pago_movil':
                return [
                    'sender_phone' => $request->sender_phone,
                    'sender_cedula' => $request->sender_cedula,
                    'sender_bank' => $request->sender_bank,
                    'reference' => $request->reference,
                    'transaction_date' => $request->transaction_date,
                    'transaction_time' => $request->transaction_time,
                    'submitted_at' => now()->toISOString(),
                ];

            default:
                return [];
        }
    }
}
