<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;

class CheckFeature
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $featureKey)
    {
        if (!Setting::getValue($featureKey, true)) {
            abort(403, 'Akses ke modul/fitur ini dinonaktifkan oleh administrator.');
        }

        return $next($request);
    }
}
