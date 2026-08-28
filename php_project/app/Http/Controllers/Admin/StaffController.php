<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\SendSmsRoleRequest;
use App\Models\StaffModel;
use Illuminate\Http\Request;
use App\Common\Code;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Staff\StaffRepository;
use App\Repositories\Staff\StaffDepartmentRepository;
use App\Repositories\Staff\RoleRepository;
use App\Repositories\Staff\StaffCodeRepository;
use App\Repositories\SmsRepository;

class StaffController extends Controller
{
	protected $staffRepository;
	protected $staffDepartmentRepository;
	protected $roleRepository;
	protected $staffCodeRepository;
	
	public function __construct(StaffRepository $staffRepository, 
		StaffDepartmentRepository $staffDepartmentRepository,
		RoleRepository $roleRepository,
		StaffCodeRepository $staffCodeRepository){
		$this->staffRepository = $staffRepository;
		$this->staffDepartmentRepository = $staffDepartmentRepository;
		$this->roleRepository = $roleRepository;
		$this->staffCodeRepository = $staffCodeRepository;
	}
	
	/**
	 * 列表-分页
	 */
	public function listPage(Request $request)
	{
		$data = $request->all();
		$result = $this->staffRepository->staffListPage($data);
		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 列表-不分页
	 */
	public function listAll(Request $request)
	{
		$data = $request->all();
		$columns = ['id', 'name', 'mobile'];
		
		$result = $this->staffRepository->staffListAll($data, $columns);
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
		$result = $this->staffRepository->staffDetail($where);

		// 角色
		if (!empty($result['role_list'])) {
			$resultRole = $this->roleRepository->list($data);
			if (!empty($resultRole['list'])) {
				$roleTmp = $resultRole['list'];

				$tmpArr = [];
				$roleList = $result['role_list'];
				foreach ($roleList as $key => $value) {
					$tmpRes = findParents($value, $roleTmp);
					if (!empty($tmpRes)) {
						$tmpArr[] = $tmpRes;
					}
				}
				$result['role_list'] = $tmpArr;
			}
		}

		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 新增
	 */
	public function store(Request $request)
	{
		$data = $request->all();

		$rules = [
			'name' => ['required'],
			'mobile'=>['required', 'string', 'max:11'],
			'sex' => ['required'],
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}

		$result = [];
		// 检查唯一性
		$unique = $this->staffRepository->checkUnique($data);
		if (is_array($unique)) {
			// 修改
			$data['id'] = $unique['id'];
			// 密码处理
			$data['password'] = encryption($data['mobile']);
			// 编号处理
			unset($data['number']);
			$result = $this->staffRepository->updateStaff($data);
		} else if ($unique) {
			// 重复返回
			return codeRender(Code::RBAC_STAFF_MOBILE_UNIQUE_ERROR);
		} else {
			// 新增
			$data['password'] = encryption($data['mobile']);
			// 编号处理: 从1开始连续增加
			$number = STAFF_NUMBER;
			$resultLast = $this->staffRepository->findLast($data);
			if (!empty($resultLast)) {
				$number = ++$resultLast['number'];
			}
			$data['number'] = $number;

			$result = $this->staffRepository->storeStaff($data);
		}
		
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
			'name' => ['required'],
			'mobile'=>['required', 'string', 'max:11'],
			'sex' => ['required'],
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}
		// 查询是否存在
		$whereExist = [
			'id' => $data['id']
		];
		$resultExist = $this->staffRepository->findDetail($whereExist);
		if (empty($resultExist)) {
			return codeRender(Code::CHECK_OPERATE_ERROR);
		}
		
		// 检查唯一性
		$unique = $this->staffRepository->checkUnique($data, true);
		if ($unique) {
			return codeRender(Code::RBAC_STAFF_MOBILE_UNIQUE_ERROR);
		}

		// 密码处理
		unset($data['password']);
		$result = $this->staffRepository->updateStaff($data, $resultExist);
		if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
		return codeRender(Code::OK, $result);
	}

	/**
	 * 删除-批量
	 */
	public function deleteBatch(Request $request)
	{
		$data = $request->all();

		$rules = [
			'staff_list' => ['required']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}

		// 查询是否存在
		$whereExist = [
			'id' => getLoginStaff('id')
		];
		$resultExist = $this->staffRepository->findDetail($whereExist, ['id']);
		if (empty($resultExist)) {
			return codeRender(Code::CHECK_OPERATE_ERROR);
		}

		// 删除员工
		$result = $this->staffRepository->deleteStaff($data);
		if (is_numeric($result)) {
			return codeRender($result);
		} else if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
		return codeRender(Code::OK, $result);
	}

	/**
	 * 修改手机号-发送验证码
	 */
	public function changeMobileOne(Request $request)
	{
		$data = $request->all();

		$rules = [
			'mobile'=>['required', 'string', 'max:11']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}

		// 校验与存入验证码
		// 检查账号是否存在
		$where = ['mobile' => $data['mobile']];
		$columns = ['id'];
		$resultStaff = $this->staffRepository->findDetail($where, $columns);
		if (!empty($resultStaff)) {
			return codeRender(Code::LOGIN_MOBILE_EXIST_ERROR);
		}
		
		// 存储验证码
		$codeData = [
			'mobile' => $data['mobile'],
			'code' => smscode()
		];
		$resultStore = $this->staffCodeRepository->storeRepository($codeData);
		if ($resultStore['code'] !== Code::OK) {
			return codeRender(Code::FAIL, '', '验证码存储失败');
		}

		// 发送验证码
		$resultSms = app(SmsRepository::class)->send([
			'telephone' => $data['mobile'],
			'template' => SMS_TEMPLATE['twentytwo'],
			'param' => [
				'code' => $codeData['code']
			]
		]);
		if (!$resultSms) {
			return codeRender(Code::FAIL, '', '验证码发送失败');
		}

		return codeRender(Code::OK, true);
	}
	
	/**
	 * 修改手机号-检查验证码
	 */
	public function changeMobileTwo(Request $request)
	{
		$data = $request->all();
	
		$rules = [
			'id' => ['required', 'integer'],
			'mobile'=>['required', 'string', 'max:11'],
			'code' => ['required', 'string', 'max:6'],
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}
	
		// 验证码校验
		$data['expire'] = CODE_EXPIRES;
		$resultCode = $this->staffCodeRepository->checkCode($data);
		if (empty($resultCode) || $resultCode['code'] !== $data['code']) {
			return codeRender(Code::LOGIN_CODE_INVALID_ERROR);
		}
		
		// 修改手机号码 - 修改手机号码
		$data2 = [
			'id' => $data['id'],
			'mobile' => $data['mobile']
		];
		$result = $this->staffRepository->updateRepository($data2);
		if ($result['code'] !== Code::OK) {
			return codeRender(Code::FAIL);
		}
		
		return codeRender(Code::OK, true);
	}
	
	/**
	 * 修改-更新密码
	 */
	public function changePassword(Request $request)
	{
		$data = $request->all();
	
		if (empty($data['id'])) {
			return codeRender(Code::CHECK_EMPTY_ERROR, '', 'id');
		}
		if (empty($data['old_password'])) {
			return codeRender(Code::CHECK_EMPTY_ERROR, '', '旧密码');
		}
		if (empty($data['password'])) {
			return codeRender(Code::CHECK_EMPTY_ERROR, '', '新密码');
		}
		if (empty($data['confirm_password'])) {
			return codeRender(Code::CHECK_EMPTY_ERROR, '', '确认密码');
		}
		if ($data['password'] !== $data['confirm_password']) {
			return codeRender(Code::LOGIN_PASSWORD_MATCH_ERROR);
		}
	
		$data['old_password'] = encryption($data['old_password']);
		$data['password'] = encryption($data['password']);
		
		// 查询旧密码是否匹配
		$where = [
			'id' => $data['id'],
			'password' => $data['old_password']
		];
		$resultStaff = $this->staffRepository->findDetail($where, ['id']);
		if (empty($resultStaff)) {
			return codeRender(Code::LOGIN_OLD_PASSWORD_ERROR);
		}
	
		$staffId = $data['id'];
		
		// 密码处理
		$data2 = [
			'id' => $staffId,
			'password' => $data['password']
		];
		$result = $this->staffRepository->updateRepository($data2);
		if ($result['code'] !== Code::OK) {
			return codeRender(Code::FAIL);
		}
		
		return codeRender(Code::OK, true);
	}
	
	/**
	 * 修改头像
	 */
	public function changePhoto(Request $request)
	{
		$data = $request->all();
	
		$rules = [
			'id' => ['required', 'integer'],
			'photo_url' => ['required', 'string']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}
		
		$data2 = [
			'id' => $data['id'],
			'photo_url' => $data['photo_url']
		];
		$result = $this->staffRepository->updateRepository($data2);
		return codeRender($result['code'], $result['data']);
	}

	public function list(Request $request)
    {
        $params = $request->all();

        $rules = [
            'keyword' => ['nullable', 'string'],
            'page' => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer']
        ];
        $validator = Validator::make($params, $rules);
        if($validator->fails()){
            return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
        }

        $data = $this->staffRepository->list($params, ['id', 'name', 'mobile']);
        return codeRender(Code::OK, $data);

    }


    public function sendSmsRole(SendSmsRoleRequest $request)
    {
        $ids = $request->input('ids');
        $staffList = StaffModel::select(['id', 'name', 'mobile'])->whereIn('id',$ids)->get()->toArray();
        if (empty($staffList)) {
            return codeRender(Code::OK);
        }

        foreach ($staffList as $k => $v) {
            $send = [
                'template' => SMS_TEMPLATE['staff_send_role'],
                'telephone' => $v['mobile'],
                'params' => [],
            ];
            $e = app(SmsRepository::class)->send($send);
        }

        return codeRender(Code::OK);
    }
}