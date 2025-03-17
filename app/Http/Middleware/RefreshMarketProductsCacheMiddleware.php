<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Jobs\RefreshMarketCacheJob;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RefreshMarketProductsCacheMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        RefreshMarketCacheJob::dispatch();

        return $response;
    }
}
