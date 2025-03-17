<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiRequestJsonMiddleware
{
    private const HEADER_NAME_ACCEPT = 'Accept';
    private const ACCEPT_HEADER_VALUE_JSON = 'application/json';

    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set(self::HEADER_NAME_ACCEPT, self::ACCEPT_HEADER_VALUE_JSON);

        return $next($request);
    }
}
