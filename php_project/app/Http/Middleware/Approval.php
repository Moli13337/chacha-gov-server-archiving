<?php

namespace App\Http\Middleware;

use Closure;
use App\Common\Code;
use App\Repositories\Apply\ApprovalRepository;

class Approval
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
		
		if (empty($data['approval_id'])) {
			return codeRender(Code::CHECK_EMPTY_ERROR, '', '', 'approval_id');
		}
		
		// 查询部门ID
		$data['staff_id'] = getLoginStaff('id');
		$approvalRepository = app(ApprovalRepository::class);
		$result = $approvalRepository->operatorAuth($data);
		if (empty($result)) {
			return codeRender(Code::APPROVAL_DEPARTMENT_ERROR);
		}

		// 审批
		$authData = app(ApprovalRepository::class)->approvalAuth($data);
		if (empty($authData)) {
			return codeRender(Code::APPROVAL_EXIST_ERROR);
		}

		// 判断部门是否一致
		if ($result['department_id'] != $authData['department_id']) {
//			return codeRender(Code::APPROVAL_DEPARTMENT_SAME_ERROR);
		}

		unset($authData['department_id']);

		$authData['approval_department_id'] = $result['department_id'];
		$authData['approval_department_name'] = $result['approval_department_name'];
		foreach ($authData as $key => $value) {
			$request->offsetSet($key, $value);
		}

		return $next($request);
	}
}
