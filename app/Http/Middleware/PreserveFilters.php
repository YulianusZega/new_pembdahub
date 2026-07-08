<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class PreserveFilters
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Jika request GET ke halaman index/dashboard admin, simpan parameternya ke session
        if ($request->isMethod('GET') && $request->route()) {
            $routeName = $request->route()->getName();
            
            if ($routeName && str_starts_with($routeName, 'admin.')) {
                if (
                    str_ends_with($routeName, '.index') || 
                    str_ends_with($routeName, '.monitoring') || 
                    str_ends_with($routeName, '.rekap') || 
                    str_ends_with($routeName, '.logs')
                ) {
                    // Simpan parameter query ke session untuk route ini (termasuk array kosong jika direset)
                    session(['filters.' . $routeName => $request->query()]);
                }
            }
        }

        // Jalankan request ke controller berikutnya
        $response = $next($request);

        // 2. Jika response adalah redirect, periksa apakah tujuannya adalah halaman index admin
        if ($response instanceof RedirectResponse) {
            try {
                $targetUrl = $response->getTargetUrl();
                
                // Buat request sementara untuk mencocokkan route tujuan
                $targetRequest = Request::create($targetUrl, 'GET');
                $route = app('router')->getRoutes()->match($targetRequest);
                $targetRouteName = $route ? $route->getName() : null;

                if ($targetRouteName && str_starts_with($targetRouteName, 'admin.')) {
                    if (
                        str_ends_with($targetRouteName, '.index') || 
                        str_ends_with($targetRouteName, '.monitoring') || 
                        str_ends_with($targetRouteName, '.rekap') || 
                        str_ends_with($targetRouteName, '.logs')
                    ) {
                        $savedFilters = session('filters.' . $targetRouteName);
                        if (is_array($savedFilters) && !empty($savedFilters)) {
                            // Ekstrak parameter query yang ada di URL redirect (jika ada)
                            $parsedUrl = parse_url($targetUrl);
                            $urlParams = [];
                            if (isset($parsedUrl['query'])) {
                                parse_str($parsedUrl['query'], $urlParams);
                            }

                            // Gabungkan filter dari session dengan parameter di URL redirect (URL redirect lebih diprioritaskan)
                            $mergedParams = array_merge($savedFilters, $urlParams);

                            // Rekonstruksi URL redirect baru dengan parameter gabungan
                            $newUrl = route($targetRouteName, $mergedParams);
                            $response->setTargetUrl($newUrl);
                        }
                    }
                }
            } catch (\Exception $e) {
                // Abaikan jika pencocokan route gagal (agar tidak crash)
            }
        }

        return $response;
    }
}
