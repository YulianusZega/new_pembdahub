<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerformanceMonitor
{
    /**
     * Slow request threshold in milliseconds.
     */
    protected int $slowThreshold = 1000;

    /**
     * High query count threshold.
     */
    protected int $queryThreshold = 50;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        // Only enable detailed query logging in debug mode
        if (config('app.debug')) {
            DB::enableQueryLog();
        }

        $response = $next($request);

        $executionTime = (microtime(true) - $startTime) * 1000; // ms
        $memoryUsed = (memory_get_usage() - $startMemory) / 1024 / 1024; // MB
        $queryCount = config('app.debug') ? count(DB::getQueryLog()) : 0;

        // Always log slow requests regardless of debug mode
        if ($executionTime > $this->slowThreshold) {
            Log::channel('performance')->warning('Slow request detected', [
                'url' => $request->method() . ' ' . $request->path(),
                'time_ms' => round($executionTime, 2),
                'memory_mb' => round($memoryUsed, 2),
                'queries' => $queryCount,
                'user_id' => $request->user()?->id,
                'ip' => $request->ip(),
            ]);
        }

        // Log N+1 candidates (too many queries)
        if ($queryCount > $this->queryThreshold) {
            Log::channel('performance')->warning('High query count', [
                'url' => $request->method() . ' ' . $request->path(),
                'queries' => $queryCount,
                'time_ms' => round($executionTime, 2),
            ]);
        }

        // Add debug headers only in debug mode
        if (config('app.debug')) {
            $response->headers->set('X-Debug-Time', round($executionTime, 2) . 'ms');
            $response->headers->set('X-Debug-Memory', round($memoryUsed, 2) . 'MB');
            $response->headers->set('X-Debug-Queries', $queryCount);
        }

        return $response;
    }
}
