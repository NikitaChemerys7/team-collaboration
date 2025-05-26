<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized. Please login first.'
            ], 401);
        }


        if ($role === 'admin' && !$user->isAdmin()) {
            return response()->json([
                'message' => 'Access denied. Admin privileges required.'
            ], 403);
        }

        if ($role === 'editor' && !($user->isEditor() || $user->isAdmin())) {
            return response()->json([
                'message' => 'Access denied. Editor privileges required.'
            ], 403);
        }

        return $next($request);
    }
}
