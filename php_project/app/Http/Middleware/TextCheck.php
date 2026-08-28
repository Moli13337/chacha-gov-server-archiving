<?php

namespace App\Http\Middleware;

use App\Common\Code;
use App\Exceptions\ValidatorException;
use App\Http\Controllers\Service\TextCheckService;
use Closure;

class TextCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    /**
     * FUNCTION_NAME : handle
     * author : jp
     *
     * @param $request
     * @param Closure $next
     * @param $key 指定检查的键
     * @return mixed
     * @throws ValidatorException
     */
    public function handle($request, Closure $next, $key)
    {
        $content = $request->input($key);
        if (!blank($content) && !app(TextCheckService::class)->check($content)) {
            throw new ValidatorException(Code::TEXT_CHECK_CONTENT_ERROR);
        }
        return $next($request);
    }
}
