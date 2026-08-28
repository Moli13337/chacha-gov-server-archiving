<?php

namespace App\Http\Middleware;

use App\Common\Code;
use App\Repositories\User\UserFollowIndustryRepository;
use Closure;

class Steward
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
        // 验证 用户是否关注行业
        $userId = getLoginHome('id');

        $has = app(UserFollowIndustryRepository::class)->haveFollow($userId);
        $arr = getLoginHome();
        if (!$has) {
            $arr[IS_FOLLOW_INDUSTRY] = false;
            setLoginHome($arr);
        } else {
            $arr[IS_FOLLOW_INDUSTRY] = true;
            setLoginHome($arr);
        }

        return $next($request);
    }
}
