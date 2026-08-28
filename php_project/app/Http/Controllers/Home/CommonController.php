<?php

namespace App\Http\Controllers\Home;

use App\Exceptions\CodeException;
use App\Exceptions\QueryException;
use App\Exceptions\ValidatorException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\SmsService;
use App\Http\Controllers\Service\UserService;
use App\Http\Requests\Sms\CheckSmsCodeRequest;
use App\Http\Requests\Sms\SmsCodeRequest;
use App\Http\Requests\User\ForgetPwdRequest;
use App\Http\Requests\User\LoginMobileUserRequest;
use App\Http\Requests\User\LoginUserRequest;
use App\Http\Requests\User\SaveUserRequest;
use App\Repositories\LoginLogsRepository;
use App\Repositories\SmsRepository;
use App\Repositories\User\UserCodeRepository;
use App\Repositories\User\UserRepository;
use App\Rules\Captcha;
use App\Support\Collection;
use Carbon\Carbon;
use Egulias\EmailValidator\Exception\ConsecutiveAt;
use Illuminate\Http\Request;
use App\Common\Code;
use App\Common\Util;
use App\Repositories\User\UserTokenRepository;
use Illuminate\Support\Facades\Cache;
use Xkd\Location\Location;
use Zhuzhichao\IpLocationZh\Ip;

class CommonController extends Controller
{

	protected $userTokenRepository;

	protected $repository;
	
	public function __construct(UserTokenRepository $userTokenRepository, UserRepository $repository){

		$this->userTokenRepository = $userTokenRepository;
		$this->repository = $repository;
	}


    /**
     * FUNCTION_NAME : register
     * author : jp
     * 用户注册
     * @param SaveUserRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \App\Exceptions\QueryException
     */
	public function register(SaveUserRequest $request)
    {
        $params = $this->filter($request);

        $params['password'] = encryption($params['password']);
        $params = array_except($params,'code');

        $data = $this->repository->storeRepository($params);

        return $this->selfLogin($data, $request->input('type'));

//        return codeRender(Code::OK, $data);
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
	 * 登陆
	 */
	public function login(LoginUserRequest $request)
	{
		$data = $request->all();

 		$data['password'] = encryption($data['password']);

 		// 查用户是否存在
        $user = $this->repository->getAccount($data['account']);
        if (empty($user)) {
            throw new CodeException(Code::LOGIN_MOBILE_EMPTY_ERROR);
        } elseif ($user['password'] != $data['password']) {
            throw new CodeException(Code::LOGIN_PASSWORD_CONFIRM_ERROR);
        }

        $type = array_get($data, 'type', LOGIN_TYPE['pc']);
        $type = empty($type) ? LOGIN_TYPE['pc'] : $type;
        return $this->selfLogin($user, $type);
	}

    /**
     * FUNCTION_NAME : MobileLogin
     * author : jp
     * 手机号登录
     * @param LoginMobileUserRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \App\Exceptions\QueryException
     */
	public function MobileLogin(LoginMobileUserRequest $request)
    {
        // 登录查询
        $where = [
            'mobile' => $request->input('mobile'),
        ];
        $user = $this->repository->login($where, ['id', 'mobile', 'name', 'is_forbidden']);

        return $this->selfLogin($user, $request->input('type'));
    }

	private function selfLogin($user, $type = '')
    {
        if (empty($user)) {
            return codeRender(Code::LOGIN_STAFF_EMPTY_ERROR);
        } elseif ($user['is_forbidden'] == USER_FORBIDDEN['yes']) {
            return codeRender(Code::LOGIN_FORBIDDEN_ERROR);
        }

        $user = app(UserService::class)->login($user, $type);
        return codeRender(Code::OK, $user);
    }


    /**
     * FUNCTION_NAME : smsCode
     * author : jp
     * 发送短信
     * @param SmsCodeRequest $request
     * @param UserCodeRepository $userCodeRepository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
	public function smsCode(SmsCodeRequest $request, UserCodeRepository $userCodeRepository)
    {
        // 存储验证码
        $codeData = [
            'mobile' => $request->input('mobile'),
            'code' => smscode()
        ];
        $flag = $this->repository->existMobile($codeData['mobile']);
        if ($request->input('tag') == HOME_SMS_CODE['login']) {
            if (!$flag) {
                throw new CodeException(Code::HOME_SMS_CODE_LOGIN_ERROR);
            }
        } elseif ($request->input('tag') == HOME_SMS_CODE['register']) {
            if ($flag) {
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
        $resultStore = $userCodeRepository->storeRepository($codeData);
        if ($resultStore['code'] !== Code::OK) {
            return codeRender(Code::FAIL);
        }
//        return codeRender(Code::OK, $resultStore['data']);
        return codeRender(Code::OK);
    }

    /**
     * FUNCTION_NAME : checkSmsCode
     * author : jp
     * 校验手机验证码
     * @param CheckSmsCodeRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function checkSmsCode(CheckSmsCodeRequest $request)
    {
        return codeRender(Code::OK);
    }

    /**
     * FUNCTION_NAME : forgetPwd
     * author : jp
     * 忘记密码
     * @param ForgetPwdRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \App\Exceptions\QueryException
     */
    public function forgetPwd(ForgetPwdRequest $request)
    {
        $white = ['mobile', 'password'];

        $params = Collection::filter($white, $request->all());

        $params['password'] = encryption($params['password']);

        $data = $this->repository->forgetPwdByMobile($params);

        if (empty($data)) {
            return codeRender(Code::FAIL);
        }

        $where = ['mobile' => $params['mobile']];
        $user = $this->repository->login($where);
        return $this->selfLogin($user);
//        return codeRender(Code::OK);
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



}
