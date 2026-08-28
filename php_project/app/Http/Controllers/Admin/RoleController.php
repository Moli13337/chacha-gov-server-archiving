<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Common\Code;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Staff\RoleRepository;

class RoleController extends Controller
{
	protected $roleRepository;
	
	public function __construct(RoleRepository $roleRepository){
		$this->roleRepository = $roleRepository;
	}

	/**
	 * 列表
	 */
	public function list(Request $request)
	{
		$data = $request->all();
		$result = $this->roleRepository->list($data);
		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 新增
	 */
	public function store(Request $request)
	{
		$data = $request->all();
		
		$rules = [
			'name' => ['required', 'max:20'],
			'description'=>['required', 'max:100'],
			'role_type_id' => ['required', 'integer']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}

		// 检查唯一性
		$unique = $this->roleRepository->checkUnique($data);
		if ($unique) {
			return codeRender(Code::RBAC_ROLE_NAME_UNIQUE_ERROR);
		}
		
		$result = $this->roleRepository->storeRole($data);
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
			'name' => ['required', 'max:20'],
			'description'=>['required', 'max:100'],
			'role_type_id' => ['required', 'integer']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}
		
		// 查询是否存在
		$whereExist = [
			'id' => $data['id']
		];
		$resultExist = $this->roleRepository->findDetail($whereExist);
		if (empty($resultExist)) {
			return codeRender(Code::CHECK_OPERATE_ERROR);
		}

		// 检查唯一性
		$unique = $this->roleRepository->checkUnique($data, true);
		if ($unique) {
			return codeRender(Code::RBAC_ROLE_NAME_UNIQUE_ERROR);
		}

		$result = $this->roleRepository->updateRole($data);
		if (!$result) {
			return codeRender(Code::DB_ERROR, $resultExist);
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
		$resultExist = $this->roleRepository->findDetail($whereExist, ['id']);
		if (empty($resultExist)) {
			return codeRender(Code::CHECK_OPERATE_ERROR);
		}
		
		$result = $this->roleRepository->deleteRole($data);
		if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 绑定员工
	 */
	public function bindStaff(Request $request)
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
			'id' => $data['id']
		];
		$resultExist = $this->roleRepository->findDetail($whereExist, ['id']);
		if (empty($resultExist)) {
			return codeRender(Code::CHECK_OPERATE_ERROR);
		}

		// 绑定资源
		$result = $this->roleRepository->bindStaff($data);
		if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 列表
	 */
	public function getStaffList(Request $request)
	{
		$data = $request->all();
		
		$rules = [
			'id' => ['required', 'integer']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}
		
		$result = $this->roleRepository->getStaffList($data);
		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 删除员工
	 */
	public function deleteStaff(Request $request)
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
			'id' => $data['id']
		];
		$resultExist = $this->roleRepository->findDetail($whereExist, ['id']);
		if (empty($resultExist)) {
			return codeRender(Code::CHECK_OPERATE_ERROR);
		}
	
		$result = $this->roleRepository->deleteStaff($data);
		if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 绑定资源
	 */
	public function bindResource(Request $request)
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
			'id' => $data['id']
		];
		$resultExist = $this->roleRepository->findDetail($whereExist, ['id']);
		if (empty($resultExist)) {
			return codeRender(Code::CHECK_OPERATE_ERROR);
		}

		// 绑定资源
		$result = $this->roleRepository->bindResource($data);
		if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 绑定API
	 */
	public function bindApi(Request $request)
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
			'id' => $data['id']
		];
		$resultExist = $this->roleRepository->findDetail($whereExist, ['id']);
		if (empty($resultExist)) {
			return codeRender(Code::CHECK_OPERATE_ERROR);
		}
	
		// 绑定
		$result = $this->roleRepository->bindApi($data);
		if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 更换超级管理员
	 */
	public function changeAdmin(Request $request)
	{
		$data = $request->all();
	
		$rules = [
			'staff_id' => ['required', 'integer']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}
		
		$currentId = getLoginStaff('id');
		if ($currentId == $data['staff_id']) {
			return codeRender(Code::OK, true);
		}

		// 查询是否存在
		$whereExist = [
			'reserved' => RESERVED_YES
		];
		$resultExist = $this->roleRepository->findDetail($whereExist, ['id']);
		if (empty($resultExist)) {
			return codeRender(Code::CHECK_OPERATE_ERROR);
		}

		$data['current_id'] = $currentId;
		$data['role_id'] = $resultExist['id'];
		$result = $this->roleRepository->changeAdmin($data);
		if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
		return codeRender(Code::OK, $result);
	}
}
