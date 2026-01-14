<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    // Verifica si el usuario autenticado tiene alguno de los roles requeridos
    public function handle(Request $request, Closure $next, ...$roles): Response{
        
      $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        if (!$user->roles()->whereIn('name', $roles)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Forbidden: You do not have the required permissions'
            ], 403);
        }
        return $next($request);
    }
}