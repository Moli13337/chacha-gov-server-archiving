<?php

namespace App\Http\Middleware;

use App\Common\Code;
use App\Exceptions\ValidatorException;
use Closure;

class FilterWordCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $key)
    {
        $filter = app('FilterWord');
        $content = $request->input($key);
        if (!blank($content) && $filter->islegal($content)) {
            throw new ValidatorException(Code::TEXT_CHECK_CONTENT_ERROR);
        }

        return $next($request);
    }
}
