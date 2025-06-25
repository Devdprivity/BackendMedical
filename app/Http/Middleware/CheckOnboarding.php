<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOnboarding
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Skip check if user is not authenticated
        if (!$user) {
            return $next($request);
        }
        
        // Skip check if user is admin (admins don't need onboarding)
        if ($user->role === 'admin') {
            return $next($request);
        }
        
        // Skip check if already on onboarding routes
        if ($request->routeIs('onboarding.*')) {
            return $next($request);
        }
        
        // Skip check for API routes
        if ($request->is('api/*')) {
            return $next($request);
        }
        
        // Skip check for auth routes
        if ($request->routeIs('login*') || $request->routeIs('register*') || $request->routeIs('auth.*')) {
            return $next($request);
        }
        
        // Skip check for logout
        if ($request->routeIs('logout')) {
            return $next($request);
        }
        
        // Skip check for debug routes
        if ($request->routeIs('debug.*')) {
            return $next($request);
        }
        
        // Only check for doctors and nurses (roles that need onboarding)
        if (!in_array($user->role, ['doctor', 'nurse'])) {
            return $next($request);
        }
        
        // Check if onboarding is required
        $requiresOnboarding = !($user->onboarding_completed ?? false);
        
        if ($requiresOnboarding) {
            // Log for debugging
            \Log::info('Redirecting user to onboarding', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'onboarding_completed' => $user->onboarding_completed,
                'current_route' => $request->route() ? $request->route()->getName() : 'unknown',
                'current_url' => $request->url()
            ]);
            
            return redirect()->route('onboarding.index')->with('info', 
                'Por favor, completa la configuración de tu cuenta para continuar.'
            );
        }
        
        return $next($request);
    }
}
