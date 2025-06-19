<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FilterUserData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Agregar información del usuario actual a la request
            $request->merge([
                'current_user_id' => $user->id,
                'current_user_role' => $user->role,
                'current_doctor_id' => $user->doctor ? $user->doctor->id : null,
                'is_admin' => $user->role === 'admin',
            ]);
        }

        return $next($request);
    }
} 