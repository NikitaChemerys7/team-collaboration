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
        if (!auth()->check()) {
            return redirect('/')->with('error', 'Please login first');
        }

        $user = auth()->user();
        
        if ($role === 'admin' && !$user->isAdmin()) {
            return redirect('/')->with('error', 'Access denied. Admin privileges required.');
        }
        
        if ($role === 'editor' && !($user->isEditor() || $user->isAdmin())) {
            return redirect('/')->with('error', 'Access denied. Editor privileges required.');
        }

        return $next($request);
    }
}
