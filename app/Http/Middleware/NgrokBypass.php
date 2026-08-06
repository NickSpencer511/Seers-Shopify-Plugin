<?php

namespace App\Http\Middleware;

use Closure;

class NgrokBypass
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('ngrok-skip-browser-warning', 'true');
        $response->headers->remove('X-Frame-Options');
        $response->headers->set('Content-Security-Policy', "frame-ancestors https://*.myshopify.com https://admin.shopify.com;");
        return $response;
    }
}
