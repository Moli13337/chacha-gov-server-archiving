<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Common\Code;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Staff\RoleTypeRepository;

class RoleTypeController extends Controller
{
	protected $repository;
	
	public function __construct(RoleTypeRepository $repository){
		$this->repository = $repository;
	}

	/**
	 * 列表
	 */
	public function list(Request $request)
	{
		$data = $request->all();
		$result = $this->repository->list($data);
		return codeRender(Code::OK, $result);
	}

	/**
	 * 新增
	 */
	public function store(Request $request)
	{
		$data = $request->all();
		
		$rules = [
			'name' => ['required', 'max:20']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}

		// 检查唯一性
		$unique = $this->repository->checkUnique($data);
		if ($unique) {
			return codeRender(Code::RBAC_ROLE_TYPE_NAME_UNIQUE_ERROR);
		}

		$result = $this->repository->storeRoleType($data);
		if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 修改
	 */
	public function update(Request $request)
	{
		$data = $request->all();
	
		$rules = [
			'id' => ['required', 'integer'],
			'name' => ['required', 'max:20']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}
		
		// 查询是否存在
		$whereExist = [
			'id' => $data['id']
		];
		$resultExist = $this->repository->findDetail($whereExist);
		if (empty($resultExist)) {
			return codeRender(Code::CHECK_OPERATE_ERROR);
		}
		
		// 检查唯一性
		$unique = $this->repository->checkUnique($data, true);
		if ($unique) {
			return codeRender(Code::RBAC_ROLE_TYPE_NAME_UNIQUE_ERROR);
		}

		$result = $this->repository->updateRoleType($data, $resultExist);
		if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 删除
	 */
	public function delete(Request $request)
	{
		$data = $request->all();
		
		$rules = [
			'id' => ['required', 'integer']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}
		
		// 查询是否存在
		$whereExist = [
			'id' => $data['id'],
			'reserved' => RESERVED_NO
		];
		$resultExist = $this->repository->findDetail($whereExist, ['id']);
		if (empty($resultExist)) {
			return codeRender(Code::CHECK_OPERATE_ERROR);
		}
		
		$result = $this->repository->deleteRoleType($data);
		if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
		return codeRender(Code::OK, $result);
	}
}
