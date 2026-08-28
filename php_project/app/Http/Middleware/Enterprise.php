<?php

namespace App\Http\Middleware;

use Closure;
use App\Common\Code;
use App\Repositories\User\UserRepository;

class Enterprise
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
		$data = $request->all();
		
		// 验证企业权限
		$userId = getLoginHome('id');
		$enterInfo = app(UserRepository::class)->enterpriseDetail($userId);
		if (empty($enterInfo['id'])) {
			return codeRender(Code::ENTERPRISE_AUTH_ERROR);
		}
		
		$request->offsetSet('enterprise_id', $enterInfo['id']);
		$request->offsetSet('enterprise_name', $enterInfo['name']);

		return $next($request);
	}
}
