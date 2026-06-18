<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $peran): Response
    {
        if (!$request->user() || $request->user()->peran !== $peran) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied. You do not have the required role.',
            ], 403);
        }

        return $next($request);
    }
}
