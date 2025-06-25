<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPaymentSetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado'
            ], 401);
        }

        // Verificar que el usuario tenga el rol correcto
        if (!in_array($user->role, ['admin', 'doctor'])) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para acceder a esta funcionalidad'
            ], 403);
        }

        // Verificar que el usuario tenga tarifa de consulta configurada
        if (!$user->consultation_fee || $user->consultation_fee <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Debes configurar una tarifa de consulta antes de generar links de pago',
                'redirect_to' => route('profile.edit')
            ], 422);
        }

        // Verificar que el usuario tenga al menos un método de pago activo
        $paymentMethods = \App\Models\PaymentMethod::where('user_id', $user->id)
                                                   ->active()
                                                   ->count();

        if ($paymentMethods === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Debes configurar al menos un método de pago antes de generar links',
                'redirect_to' => route('payment-methods.create')
            ], 422);
        }

        return $next($request);
    }
} 