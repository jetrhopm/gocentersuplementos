<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStoreMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('services.store.maintenance_mode')) {
            return $next($request);
        }

        if ($request->is('admin*') || $request->is('webhooks/*') || $request->is('build/*') || $request->is('storage/*')) {
            return $next($request);
        }

        return response()->view('store.maintenance', status: 503);
    }
}
