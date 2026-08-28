<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Department\GetDepartmentRequest;
use App\Rules\Mobile;
use App\Rules\Sphone;
use App\Rules\Telephone;
use Illuminate\Http\Request;
use App\Common\Code;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Staff\StaffDepartmentRepository;
use Illuminate\Validation\Rule;


class StaffDepartmentController extends Controller
{
	protected $repository;
	
	public function __construct(StaffDepartmentRepository $repository){
		$this->repository = $repository;
	}

	/**
	 * 列表
	 */
	public function list(Request $request)
	{
		$data = $request->all();
		$result = $this->repository->list($data);
		if (!empty(['list'])) {
			$list = getTree($result['list']);
			$result['list'] = $list;
		}
		
		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 列表 - 审批使用
	 */
	public function listAll(Request $request)
	{
		$data = $request->all();
		$result = $this->repository->listAll($data);
		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 详情
	 */
	public function detail(Request $request)
	{
		$data = $request->all();
		
		$rules = [
			'id' => ['required', 'integer']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}

		$where = [
			'id' => $data['id']
		];
		$result = $this->repository->findDetail($where, ['*'], true);
		$result['parent_list'] = empty($result['parent_list']) ? [] : explode('-', $result['parent_list']);
		
		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 新增
	 */
    /**
     *
     * @api {post} /api/department/store 新增部门
     * @apiVersion 1.0.0
     * @apiName DepartmentStore
     * @apiGroup 运营端--部门
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {String} name 必填
     * @apiParam {String} description  描述 必填
     * @apiParam {String} phone  固定电话
     * @apiParam {Number} type  部门类型1区企业服务中心2普通部门3园区管委会企服中心4指挥部5园区管委会办公室
     *
     * @apiSuccess {Number} code 返回代码
     * @apiSuccess {String} message 返回信息
     * @apiSuccess {Object} data 返回数据块
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "code": 200,
     *       "message": "操作成功",
     *         "data":{
     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
	public function store(Request $request)
	{
		$data = $request->all();

		$rules = [
			'name' => ['required', 'max:20'],
			'description'=>['required', 'max:50'],
			"type" => ['required', 'integer', Rule::in(array_values(DEPARTMENT_TYPE))],
            'phone' => ['nullable', 'string',  new  Telephone()]
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}
		$data = $this->initValue($data);
		// parent_id处理
		$data['parent_id'] = 0;//empty($data['parent_id']) ? 0 : $data['parent_id']; // 默认第一级部门
		// manager_id 处理
		$data['manager_id'] = 0;//empty($data['manager_id']) ? 0 : $data['manager_id'];

		// 检查唯一性
		$unique = $this->repository->checkUnique($data);
		if (is_numeric($unique)) {
			return codeRender($unique);
		}
		
		// 递归层级处理
		if (!empty($data['parent_list'])) {
			$data['parent_list'] = implode('-', $data['parent_list']);
		}

		$result = $this->repository->storeDepartment($data);
		if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 修改
	 */
    /**
     *
     * @api {post} /api/department/Update 编辑部门
     * @apiVersion 1.0.0
     * @apiName DepartmentUpdate
     * @apiGroup 运营端--部门
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {Number} id 必填
     * @apiParam {String} name 必填
     * @apiParam {String} description  描述 必填
     * @apiParam {String} phone  固定电话
     * @apiParam {Number} type  部门类型 1区企业服务中心2普通部门3园区管委会企服中心4指挥部5园区管委会办公室
     *
     * @apiSuccess {Number} code 返回代码
     * @apiSuccess {String} message 返回信息
     * @apiSuccess {Object} data 返回数据块
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "code": 200,
     *       "message": "操作成功",
     *         "data":{
     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
	public function update(Request $request)
	{
		$data = $request->all();
	
		$rules = [
			'id' => ['required', 'integer'],
			'name' => ['required', 'max:20'],
			'description'=>['required', 'max:50'],
            "type" => ['required', 'integer', Rule::in(array_values(DEPARTMENT_TYPE))],
            'phone' => ['nullable', 'string',  new  Telephone()]
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
		
		// parent_id处理
		$data['parent_id'] = 0;//empty($data['parent_id']) ? 0 : $data['parent_id']; // 默认第一级部门
		// manager_id 处理
		$data['manager_id'] = 0;//empty($data['manager_id']) ? 0 : $data['manager_id'];

		// 检查唯一性
		$unique = $this->repository->checkUnique($data, true);
		if (is_numeric($unique)) {
			return codeRender($unique);
		}

		// 递归层级处理
		if (!empty($data['parent_list'])) {
			$data['parent_list'] = implode('-', $data['parent_list']);
		}
		$result = $this->repository->updateDepartment($data, $resultExist);
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
			'id' => $data['id']
		];
		$resultExist = $this->repository->findDetail($whereExist, ['id', 'deleted_at']);
		if (empty($resultExist)) {
			return codeRender(Code::CHECK_OPERATE_ERROR);
		}
		// 如果已被删除-返回成功
		if (!is_null($resultExist['deleted_at'])) {
			return codeRender(Code::OK, true);
		}

		$result = $this->repository->deleteDepartment($data);
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
			'id' => ['required', 'integer'],
			'staff_list' => ['required']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}
		
		// 操作人员唯一校验 - 操作类型1操作人员2监督人员3普通人员
		$operatorNum = 0;
		$staffList = $data['staff_list'];
		foreach ($staffList as $key => $value) {
			if (empty($value['staff_id'])) {
				return codeRender(Code::PARAM_ERROR, '', '', 'staff_id');
			}
			if (empty($value['opertor_type'])) {
				return codeRender(Code::PARAM_ERROR, '', '', '人员类型');
			}
			
			if ($value['opertor_type'] == STAFF_OPERTOR_TYPE['one']) {
				$operatorNum++;
			}
		}
		
		if ($operatorNum > 1) {
			return codeRender(Code::RBAC_OPERATOR_ONE_UNIQUE_ERROR);
		}

		// 查询是否存在
		$whereExist = [
			'id' => $data['id']
		];
		$resultExist = $this->repository->findDetail($whereExist, ['id']);
		if (empty($resultExist)) {
			return codeRender(Code::CHECK_OPERATE_ERROR);
		}
		
		$result = $this->repository->bindStaff($data);
		if (is_numeric($result)) {
			return codeRender($result);
		} else if (!$result) {
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
		
		$result = $this->repository->getStaffList($data);
		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 删除员工
	 */
	public function deleteStaff(Request $request)
	{
		$data = $request->all();
	
		$rules = [
			'id' => ['required', 'integer'],
			'staff_list' => ['required']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}
	
		// 查询是否存在
		$whereExist = [
			'id' => $data['id']
		];
		$resultExist = $this->repository->findDetail($whereExist, ['id']);
		if (empty($resultExist)) {
			return codeRender(Code::CHECK_OPERATE_ERROR);
		}
	
		$result = $this->repository->deleteStaff($data);
		if (is_numeric($result)) {
			return codeRender($result);
		} else if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
		return codeRender(Code::OK, true);
	}
	
	/**
	 * 修改审核权限
	 */
	public function updateOperator(Request $request)
	{
		$data = $request->all();
	
		$rules = [
			'id' => ['required', 'integer'],
			'staff_id' => ['required', 'integer'],
			'opertor_type' => ['required', 'integer']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}
	
		// 查询是否存在
		$whereExist = [
			'id' => $data['id']
		];
		$resultExist = $this->repository->findDetail($whereExist, ['id']);
		if (empty($resultExist)) {
			return codeRender(Code::CHECK_OPERATE_ERROR);
		}
	
		$result = $this->repository->updateOperator($data);
		if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
		return codeRender(Code::OK, true);
	}
	
	
	/**
	 * 获取没有绑定部门列表
	 */
	public function getNotBindDepartmentList(Request $request)
	{
		$data = $request->all();
		$result = $this->repository->getNotBindDepartmentList($data);
		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 获取绑定部门列表
	 */
	public function getBindDepartmentList(Request $request)
	{
		$data = $request->all();
		$result = $this->repository->getBindDepartmentList($data);
		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 新增绑定部门
	 */
	public function bindDepartment(Request $request)
	{
		$data = $request->all();
	
		$rules = [
			'department_one_id' => ['required', 'integer'],
			'department_two_id' => ['required', 'integer']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}

		$result = $this->repository->bindDepartment($data);
		if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 删除绑定部门
	 */
	public function deleteBindDepartment(Request $request)
	{
		$data = $request->all();
	
		$rules = [
			'department_one_id' => ['required', 'integer'],
			'department_two_id' => ['required', 'integer'],
			'department_three_id' => ['required', 'integer'],
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}
	
		$result = $this->repository->deleteBindDepartment($data);
		if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
		return codeRender(Code::OK, $result);
	}

	public function initValue($data)
    {
        $keys = [
            'phone' => '',
        ];
        foreach ($keys as $key => $value) {
            $data[$key] = empty($data[$key]) ? $value : $data[$key];
        }
        return $data;
    }

    public function getDepartment(GetDepartmentRequest $request)
    {
        $params = $this->filter($request);
        $data = $this->repository->getDepartment($params, ['id', 'name']);
        return codeRender(Code::OK, $data);
    }
}
