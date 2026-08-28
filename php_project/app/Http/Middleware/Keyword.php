<?php

namespace App\Http\Middleware;

use Closure;

class Keyword
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

        $keyword = $request->input('keyword');
        if (!blank($keyword)) {
            if ($keyword == "\\") {
                $request->offsetSet('keyword', "\\\\");
            }
        }

        return $next($request);
    }
}
