<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TrustProxies
{
    protected $proxies = '*';

    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO;

    public function handle(Request $request, Closure $next)
    {
        $request->setTrustedProxies(
            $this->proxies === '*' ? [$request->getClientIp()] : $this->proxies,
            $this->headers
        );

        return $next($request);
    }
}