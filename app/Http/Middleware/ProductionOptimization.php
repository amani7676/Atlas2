<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ProductionOptimization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Only apply optimizations in production
        if (!app()->environment('production')) {
            return $next($request);
        }

        // Set performance headers
        $response = $next($request);

        // Enable compression
        if (Config::get('performance.optimization.compression.enabled', true)) {
            $response->header('Content-Encoding', 'gzip');
        }

        // Set cache headers for static assets
        if (Config::get('performance.optimization.http_cache.enabled', true)) {
            $maxAge = Config::get('performance.optimization.http_cache.max_age', 3600);
            $response->header('Cache-Control', "public, max-age={$maxAge}");
        }

        // Set security headers
        $response->header('X-Content-Type-Options', 'nosniff');
        $response->header('X-Frame-Options', 'SAMEORIGIN');
        $response->header('X-XSS-Protection', '1; mode=block');

        // Performance hints
        $response->header('X-DNS-Prefetch-Control', 'on');
        $response->header('X-Preload-Cache', 'true');

        return $response;
    }
}
