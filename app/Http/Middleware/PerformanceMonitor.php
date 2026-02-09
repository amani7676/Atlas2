<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PerformanceMonitor
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
        // Only monitor in local environment
        if (!app()->environment('local')) {
            return $next($request);
        }

        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        // Enable query logging
        DB::enableQueryLog();

        $response = $next($request);

        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);

        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $memoryUsage = $endMemory - $startMemory;
        $queries = DB::getQueryLog();

        // Log performance metrics
        if ($executionTime > 1000) { // Log if takes more than 1 second
            Log::warning('Slow Request Detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'execution_time' => round($executionTime, 2) . 'ms',
                'memory_usage' => round($memoryUsage / 1024 / 1024, 2) . 'MB',
                'query_count' => count($queries),
                'slow_queries' => $this->getSlowQueries($queries),
            ]);
        }

        // Log query details for debugging
        if (count($queries) > 50) { // Log if more than 50 queries
            Log::warning('Too Many Queries Detected', [
                'url' => $request->fullUrl(),
                'query_count' => count($queries),
                'total_time' => array_sum(array_column($queries, 'time')) . 'ms',
            ]);
        }

        // Add performance headers for debugging
        $response->headers->set('X-Execution-Time', round($executionTime, 2) . 'ms');
        $response->headers->set('X-Memory-Usage', round($memoryUsage / 1024 / 1024, 2) . 'MB');
        $response->headers->set('X-Query-Count', count($queries));

        return $response;
    }

    private function getSlowQueries(array $queries): array
    {
        $slowQueries = [];
        
        foreach ($queries as $query) {
            if ($query['time'] > 100) { // Queries taking more than 100ms
                $slowQueries[] = [
                    'sql' => $query['query'],
                    'time' => $query['time'] . 'ms',
                    'bindings' => $query['bindings'],
                ];
            }
        }

        return $slowQueries;
    }
}
