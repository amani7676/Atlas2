<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow access to /info route without authentication
        if ($request->is('info') || $request->is('info/*')) {
            return $next($request);
        }

        // Check if user is not authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
