<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanManageConference
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $conferenceId = $request->route('conference');

        if (!$user || !$user->canManageConference($conferenceId)) {
            return response()->json([
                'error' => 'Access denied to this conference.'
            ], 403);
        }
        
        return $next($request);
    }
}
