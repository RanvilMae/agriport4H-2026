<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsManager
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !in_array($user->role, ['Admin', 'President', 'Coordinator'])) {
            abort(403, 'Access Denied: Authorized Managers Only.');
        }

        return $next($request);
    }
}