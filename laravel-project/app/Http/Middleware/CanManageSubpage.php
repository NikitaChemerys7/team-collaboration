<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanManageSubpage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $subpageId = $request->route('subpage');

        if (!$user || !$user->canManageSubpage((int)$subpageId)) {
            return response()->json([
                'error' => 'Access denied to this subpage.'
            ], 403);
        }

        return $next($request);
    }
}
