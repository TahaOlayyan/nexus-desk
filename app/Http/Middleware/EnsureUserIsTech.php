<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsTech
{
    /**
     * Ensure the authenticated user is a technician.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || Auth::user()->role !== 'tech') {
            abort(403, 'Unauthorized. Technician access is required.');
        }

        return $next($request);
    }
}
