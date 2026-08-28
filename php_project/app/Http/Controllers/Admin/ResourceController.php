<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Common\Code;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Staff\ResourceRepository;


class ResourceController extends Controller
{
	protected $repository;
	
	public function __construct(ResourceRepository $repository){
		$this->repository = $repository;
	}

	/**
	 * 列表-资源
	 */
	public function list(Request $request)
	{
		$data = $request->all();
		$result = $this->repository->list($data);
		return codeRender(Code::OK, $result);
	}

	/**
	 * 列表-角色
	 */
	public function getRoleList(Request $request)
	{
		$data = $request->all();
		
		$rules = [
			'id' => ['required', 'integer']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}
		
		$result = $this->repository->getRoleList($data);
		return codeRender(Code::OK, $result);
	}

// 	/**
// 	 * 登陆人员的权限列表
// 	 */
// 	public function permissionList(Request $request)
// 	{
// 		$data = $request->all();
// 		$result = $this->repository->permissionList($data);
// 		return codeRender(Code::OK, $result);
// 	}
}
