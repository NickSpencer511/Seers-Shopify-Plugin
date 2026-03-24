<?php

namespace App\Http\Middleware;

use Closure;

class NgrokBypass
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('ngrok-skip-browser-warning', 'true');
        return $response;
    }
}
