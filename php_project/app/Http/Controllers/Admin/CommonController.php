<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\CodeException;
use App\Exceptions\ValidatorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Sms\SmsCodeRequest;
use App\Rules\Captcha;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Common\Code;
use App\Common\Util;
use App\Repositories\Staff\StaffRepository;
use App\Repositories\Staff\RoleTypeRepository;
use App\Repositories\Staff\RoleRepository;
use App\Repositories\Staff\StaffTokenRepository;
use App\Repositories\Staff\StaffCodeRepository;
use Illuminate\Support\Facades\Cache;
use Xkd\Location\Location;
use App\Repositories\Staff\ResourceRepository;
use App\Repositories\SmsRepository;

class CommonController extends Controller
{
	protected $staffRepository;
	protected $roleTypeRepository;
	protected $roleRepository;
	protected $staffTokenRepository;
	protected $staffCodeRepository;
	
	public function __construct(StaffRepository $staffRepository, 
		RoleTypeRepository $roleTypeRepository, 
		RoleRepository $roleRepository, 
		StaffTokenRepository $staffTokenRepository,
		StaffCodeRepository $staffCodeRepository){
		
		$this->staffRepository = $staffRepository;
		$this->roleTypeRepository = $roleTypeRepository;
		$this->roleRepository = $roleRepository;
		$this->staffTokenRepository = $staffTokenRepository;
		$this->staffCodeRepository = $staffCodeRepository;
	}

	/**
	 * 登陆
	 */
	public function login(Request $request)
	{
		$data = $request->all();
	
		if (empty($data['mobile'])) {
			return codeRender(Code::LOGIN_MOBILE_ERROR);
		}
		if (strlen($data['mobile']) !== 11) {
			return codeRender(Code::LOGIN_MOBILE_LENGTH_ERROR);
		}
		if (empty($data['password'])) {
			return codeRender(Code::LOGIN_PASSWORD_ERROR);
		}
	
		$data['password'] = encryption($data['password']);

		$where = [
			'mobile' => $data['mobile'],
			'password' => $data['password']
		];
		$staff = $this->staffRepository->staffListMany($where);
		if (empty($staff)) {
			return codeRender(Code::LOGIN_STAFF_EMPTY_ERROR);
		}
		
		// 查询菜单、接口权限
		$permission = app(ResourceRepository::class)->permissionList([
			'staff_id' => $staff['id']
		]);
		if (is_numeric($permission)) {
			return codeRender($permission);
		}
		
		$staff['permission'] = $permission;

		// 模拟单点登录-存入token表
		$staffToken = [
			'staff_id' => $staff['id'],
			'sign' => signRandom(),
			'expire' => TOKEN_EXPIRE
		];
		$resultToken = $this->staffTokenRepository->storeOrUpdateToken($staffToken);
		if ($resultToken['code'] !== Code::OK) {
			return codeRender(Code::FAIL);
		}
	
		// token data
		$dataToken = [
			'staff_id' => $staffToken['staff_id'],
			'sign' => $staffToken['sign']
		];
		$token = Util::tokenEncode(['data' => $dataToken]);
		$staff['token'] = $token;
		$staff['token_pre'] = BASE_BEARER;
	
		$staff = array_except($staff, ['password', 'deleted_at']);

		return codeRender(Code::OK, $staff);
	}
	
	
	/**
	 * 忘记密码1-发送验证码
	 */
	public function forgetPwdOne(Request $request)
	{
		$data = $request->all();
	
		if (empty($data['mobile'])) {
			return codeRender(Code::LOGIN_MOBILE_ERROR);
		}
		if (strlen($data['mobile']) !== 11) {
			return codeRender(Code::LOGIN_MOBILE_LENGTH_ERROR);
		}
	
		// 校验与存入验证码
		// 检查账号是否存在
		$where = ['mobile' => $data['mobile']];
		$columns = ['id'];
		$resultStaff = $this->staffRepository->staffListMany($where, $columns);
		if (empty($resultStaff)) {
			return codeRender(Code::LOGIN_MOBILE_EMPTY_ERROR);
		}

		// 存储验证码
		$codeData = [
			'mobile' => $data['mobile'],
			'code' => smscode()
		];
		$resultStore = $this->staffCodeRepository->storeRepository($codeData);
		if ($resultStore['code'] !== Code::OK) {
			return codeRender(Code::FAIL);
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
			return codeRender(Code::FAIL);
		}

		return codeRender(Code::OK, true);
	}
	
	/**
	 * 忘记密码2-检查验证码
	 */
	public function forgetPwdTwo(Request $request)
	{
		$data = $request->all();
	
		if (empty($data['mobile'])) {
			return codeRender(Code::LOGIN_MOBILE_ERROR);
		}
		if (strlen($data['mobile']) !== 11) {
			return codeRender(Code::LOGIN_MOBILE_LENGTH_ERROR);
		}
		if (empty($data['code']) || strlen($data['code']) !== 6) {
			return codeRender(Code::LOGIN_SEND_CODE_ERROR);
		}
	
		// 验证码校验
		$data['expire'] = CODE_EXPIRES;
		$resultCode = $this->staffCodeRepository->checkCode($data);
		if (empty($resultCode) || $resultCode['code'] !== $data['code']) {
			return codeRender(Code::LOGIN_CODE_INVALID_ERROR);
		}
		
		return codeRender(Code::OK, true);
	}
	
	/**
	 * 忘记密码3-更新密码
	 */
	public function forgetPwdThree(Request $request)
	{
		$data = $request->all();
	
		if (empty($data['mobile'])) {
			return codeRender(Code::LOGIN_MOBILE_ERROR);
		}
		if (strlen($data['mobile']) !== 11) {
			return codeRender(Code::LOGIN_MOBILE_LENGTH_ERROR);
		}
		if (empty($data['password'])) {
			return codeRender(Code::CHECK_EMPTY_ERROR, '', 'password');
		}
		if (empty($data['confirm_password'])) {
			return codeRender(Code::CHECK_EMPTY_ERROR, '', 'confirm_password');
		}
		if ($data['password'] !== $data['confirm_password']) {
			return codeRender(Code::LOGIN_PASSWORD_MATCH_ERROR);
		}
	
		$data['password'] = encryption($data['password']);
		
		// 验证手机号
		$where = [
			'mobile' => $data['mobile']
		];
		$resultStaff = $this->staffRepository->staffListMany($where, ['id']);
		if (empty($resultStaff)) {
			return codeRender(Code::FAIL);
		}

		$staffId =$resultStaff['id'];
		
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
     * FUNCTION_NAME : getDistricts
     * author : jp
     * 获取行政区划
     * @param Request $request
     * @return mixed
     * @throws \Xkd\Location\Exceptions\ClientException
     */
	public function getDistricts(Request $request)
    {
        return Location::getInfo('district')->getDistricts($request->all());
    }

    public function getDistrictsAll(Request $request)
    {
        return Location::getInfo('district')->getAll([]);
    }

    /**
     * FUNCTION_NAME : captcha
     * author : jp
     * 获取图片验证码
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws ValidatorException
     */
    public function captcha(Request $request){

        if ($request->method() == 'POST') {
            $rule = [
                'key' => ['required', 'string'],
                'captcha' => ['required', 'string', new Captcha($request->input('key'))],
            ];

            $validator = \Validator::make($request->all(), $rule);

            if ($validator->fails()) {
                throw new ValidatorException(Code::PARAM_ERROR, $validator->messages()->first());
            }
            return codeRender(Code::OK);
        }

        $data = app('captcha')->create('default', true);
        $key = $data['key'];
        $keyMd5 = encryption(signRandom());
        $expiresAt = Carbon::now()->addMinute(CAPTCHA_EXPIRES);
        Cache::put(REDIS_CAPTCHA.$keyMd5, $key, $expiresAt);
        $data['key'] = $keyMd5;
        return codeRender(Code::OK, $data);
    }

    /**
     * FUNCTION_NAME : smsCode
     * author : jp
     * 发送短信
     * @param SmsCodeRequest $request
     * @param StaffCodeRepository $staffCodeRepository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function smsCode(SmsCodeRequest $request, StaffCodeRepository $staffCodeRepository)
    {
        // 存储验证码
        $codeData = [
            'mobile' => $request->input('mobile'),
            'code' => smscode()
        ];
        $flag = $this->staffRepository->getByMobile($codeData['mobile']);
        if ($request->input('tag') == HOME_SMS_CODE['login']) {
            if (!$flag) {
                throw new CodeException(Code::HOME_SMS_CODE_LOGIN_ERROR);
            }
        } elseif ($request->input('tag') == HOME_SMS_CODE['register']) {
            if (!empty($flag['uid'])) {
                throw new CodeException(Code::HOME_SMS_CODE_REGISTER_ERROR);
            }
        }

        $params = [
            'telephone' => $codeData['mobile'],
            'template' => SMS_TEMPLATE['twentytwo'],
            'param' => [
                'code' => $codeData['code']
            ]
        ];
        app(SmsRepository::class)->send($params);
        $resultStore = $staffCodeRepository->storeRepository($codeData);
        if ($resultStore['code'] !== Code::OK) {
            return codeRender(Code::FAIL);
        }
//        return codeRender(Code::OK, $resultStore['data']);
        return codeRender(Code::OK);
    }
}
