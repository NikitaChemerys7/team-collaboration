<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        /** @var \Illuminate\Contracts\Auth\Guard $guard */
        $guard = auth();
        if (!$guard->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthorized. Please login first.'
                ], 401);
            }

            return redirect('/')->with('error', 'Please login first');
        }

        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!$user) {
            return $this->accessDenied($request, 'User not authenticated.');
        }

        if (method_exists($user, 'isAdmin') || method_exists($user, 'isEditor')) {
            if ($role === 'admin' && !$user->isAdmin()) {
                return $this->accessDenied($request, 'Admin privileges required.');
            }

            if ($role === 'editor' && !($user->isEditor() || $user->isAdmin())) {
                return $this->accessDenied($request, 'Editor privileges required.');
            }
        } else {
            if ($user->role !== $role) {
                return $this->accessDenied($request, 'Insufficient permissions.');
            }
        }


        return $next($request);
    }

    protected function accessDenied(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthorized. ' . $message], 403);
        }

        return redirect('/')->with('error', 'Access denied. ' . $message);
    }
}
