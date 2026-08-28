<?php

namespace App\Http\Middleware;

use App\Common\Permission;
use Closure;
use App\Common\Util;
use App\Common\Code;
use Illuminate\Support\Str;
use App\Repositories\User\UserTokenRepository;

class Home
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
		$authorization = $request->header('Authorization');
        $request->setTrustedProxies($request->getClientIps());


        $actions = $request->route()->getAction();

        // 允许在未登录下可以访问的接口
        $noLogin = false;
        if (isset($actions['permission']) && $actions['permission']  == Permission::PERMISSION_NO_LOGIN) {
            $noLogin = true;
        }

		// 前缀校验
        $flag = strpos($authorization, BASE_BEARER . ' ') === false;
		if ($flag) {
		    if ($noLogin) {
		        return $next($request);
            }
			return codeRender(Code::AUTH_BEARRE_ERROR);
		}

		// 验证token
		$token = Str::substr($authorization, Str::length(BASE_BEARER) + 1); // 加一位空格
		if (empty($token)) {
			return codeRender(Code::AUTH_TOKEN_EMPTY_ERROR);
		}

		// 解析token
		$userInfo = Util::tokenDecode(['data' => $token]);
		if (empty($userInfo)) {
			return codeRender(Code::AUTH_TOKEN_EMPTY_ERROR);
		}
		
		// sign有效时间 校验
		$dataToken = (array)$userInfo['data'];

		$loginType = empty($dataToken['type']) ? LOGIN_TYPE['pc']:$dataToken['type'];
		$userToken = [
			'user_id' => empty($dataToken['user_id']) ? 0 : $dataToken['user_id'],
			'type' => $loginType,
			'sign' => $dataToken['sign']
		];
		$userTokenRepository = app(UserTokenRepository::class);
		$resultToken = $userTokenRepository->findToken($userToken);
        if (empty($resultToken)) {
			return codeRender(Code::AUTH_TOKEN_EXPIRE_ERROR);
		}

//		// 当前时间大于token过期时间，token失效
//		if (time() > $resultToken['updated_at']) {
//			return codeRender(Code::AUTH_TOKEN_EXPIRE_ERROR);
//		}
        $resultToken['login_type'] = $loginType;
		setLoginHome($resultToken);

		return $next($request);
	}
}
