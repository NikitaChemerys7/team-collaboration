<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Conference;
use App\Models\Subpage;

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
        $conferenceId = $request->route('conference');

        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized access.'
            ], 401);
        }

        if ($user->isAdmin()) {
            return $next($request);
        }

        if ($user->isEditor()) {
            $conference = Conference::find($conferenceId);
            if (!$conference) {
                return response()->json([
                    'error' => 'Conference not found.'
                ], 404);
            }

            if (!$user->canManageYear($conference->year)) {
                return response()->json([
                    'error' => 'You do not have permission to manage this conference.'
                ], 403);
            }

            $subpageId = $request->route('subpage');
            if ($subpageId) {
                $subpage = Subpage::find($subpageId);
                if (!$subpage) {
                    return response()->json([
                        'error' => 'Subpage not found.'
                    ], 404);
                }

                if ($subpage->conference_id !== $conference->id) {
                    return response()->json([
                        'error' => 'Subpage does not belong to this conference.'
                    ], 403);
                }
            }

            return $next($request);
        }

        return response()->json([
            'error' => 'Access denied to this subpage.'
        ], 403);
    }
} 