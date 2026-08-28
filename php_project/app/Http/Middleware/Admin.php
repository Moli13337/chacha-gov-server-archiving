<?php

namespace App\Http\Middleware;

use App\Repositories\Apply\ApprovalRepository;
use Closure;
use App\Common\Util;
use App\Common\Code;
use Illuminate\Support\Str;
use App\Repositories\Staff\StaffTokenRepository;
use App\Common\Permission;
use App\Repositories\Staff\StaffRepository;

class Admin
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
		// 前缀校验
		if (strpos($authorization, BASE_BEARER . ' ') === false) {
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

		$staffToken = [
			'staff_id' => empty($dataToken['staff_id']) ? 0 : $dataToken['staff_id'],
			'sign' => $dataToken['sign']
		];
		$staffTokenRepository = app(StaffTokenRepository::class);
		$resultToken = $staffTokenRepository->findToken($staffToken);
		if (empty($resultToken)) {
			return codeRender(Code::AUTH_TOKEN_EXPIRE_ERROR);
		}

 		// 当前时间大于token过期时间，token失效
 		if (time() > $resultToken['updated_at']) {
 			return codeRender(Code::AUTH_TOKEN_EXPIRE_ERROR);
 		}

		// 验权
		$actions = $request->route()->getAction();
		if (empty($actions['permission'])) {
			return codeRender(Code::AUTH_PERMISSION_EMPTY_ERROR);
		}

		// 做个权限白名单
		if ($actions['permission'] != Permission::PERMISSION_ALL) {
			$pmsData = [
				'staff_id' => $dataToken['staff_id'],
				'number' => $actions['permission']
			];
			$resultPms = app(StaffRepository::class)->checkPermission($pmsData);
			if (is_numeric($resultPms)) {
				return codeRender($resultPms);
			}
		}

		// 注入容器
		setLoginStaff($resultToken);

		return $next($request);
	}
}
