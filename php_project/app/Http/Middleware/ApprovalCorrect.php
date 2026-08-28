<?php

namespace App\Http\Middleware;

use App\Repositories\Apply\ApplyCorrectRepository;
use Closure;

class ApprovalCorrect
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
        $applyId = $request->input('apply_id', 0);
        // 判断是否有待预检的订正资料
        $have = app(ApplyCorrectRepository::class)->checkApproval($applyId);
        if (!empty($have)) {
            return codeRender($have);
        }
        return $next($request);
    }
}
