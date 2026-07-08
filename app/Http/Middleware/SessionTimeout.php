<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    /**
     * Session timeout in minutes
     */
    protected int $timeout = 120; // 2 hours

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (Auth::check()) {
            $lastActivity = session('last_activity');

            // If last activity is older than timeout, logout
            if ($lastActivity && (time() - $lastActivity) > ($this->timeout * 60)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')
                    ->with('error', 'Session Anda telah berakhir. Silakan login kembali.');
            }
        }

        // Update last activity
        session(['last_activity' => time()]);

        return $next($request);
    }
}
