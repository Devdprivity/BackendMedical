<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            // For API requests, return JSON error instead of redirecting
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'Unauthenticated',
                    'message' => 'You must be logged in to access this resource.'
                ], 401);
            }
            
            return redirect()->route('login.view');
        }

        $user = auth()->user();
        $userRole = $user->role;

        // Admin has access to everything
        if ($userRole === 'admin') {
            return $next($request);
        }

        // Check if user has any of the required roles
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // For API requests, return JSON error
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'error' => 'No tienes permisos para acceder a este recurso.',
                'required_role' => $roles,
                'your_role' => $userRole
            ], 403);
        }

        // For web requests, redirect with error
        return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
    }
}
