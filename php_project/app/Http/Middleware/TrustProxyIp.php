<?php

namespace App\Http\Middleware;

use Closure;

class TrustProxyIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 获取原始ip
        $request->setTrustedProxies($request->getClientIps());
        return $next($request);
    }
}
