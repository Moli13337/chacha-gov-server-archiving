<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2020/1/3
 * Time: 9:42
 */

namespace App\Http\Controllers\Home;


use App\Common\Code;
use App\Exceptions\CodeException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\UserService;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterUserRequest;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserTokenRepository;
use Illuminate\Auth\Events\Login;

class LoginController extends Controller
{

    protected $userTokenRepository;

    protected $repository;

    public function __construct(UserTokenRepository $userTokenRepository, UserRepository $repository)
    {
        $this->userTokenRepository = $userTokenRepository;
        $this->repository = $repository;
    }

    /**
     *
     * @api POST /home/common/register 注册
     * @apiVersion 1.0.0
     * @apiName 注册
     * @apiGroup PC--注册
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {String} name 用户名
     * @apiParam {Number} mobile 电话
     * @apiParam {Number} code 验证码
     * @apiParam {Number} integer 腾讯uid
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
     *       "data":{
                    "mobile": "18808054854",
                    "name": "liang",
                    "uid": "646274869",
                    "updated_at": "1578016827",
                    "created_at": "1578016827",
                    "id": 33,
                    "token": "xxx",
                    "token_pre": "bearer",
     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function register(RegisterUserRequest $request)
    {
        $params = $this->filter($request);
//        $params['password'] = encryption($params['password']);
        $params = array_except($params,'code');

        $data = $this->repository->storeRepository($params);

        return $this->selfLogin($data, $request->input('type'));
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
     *
     * @api POST /home/common/login 登录
     * @apiVersion 1.0.0
     * @apiName 登录
     * @apiGroup PC--登录
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {Number} uid 腾讯uid
     * @apiParam {Number} type 登录方式 默认1（可不填） 1-PC 2-小程序
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
     *       "data":{
                    "mobile": "18808054854",
                    "name": "liang",
                    "uid": "646274869",
                    "updated_at": "1578016827",
                    "created_at": "1578016827",
                    "id": 33,
                    "token": "xxx",
                    "token_pre": "bearer",
     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function login(LoginRequest $request)
    {
        $params = $this->filter($request);
        $user = $this->repository->getByUid($params['uid']);
        if (empty($user)) {
            throw new CodeException(Code::LOGIN_MOBILE_EMPTY_ERROR);
        }
        return $this->selfLogin($user);
    }


    public function loginV2(LoginRequest $request)
    {
        $params = $this->filter($request);
        $user = $this->repository->getByUid($params['uid']);
        if (empty($user)) {
            $data = [
                'uid' => $params['uid']
            ];
            $user = app(UserRepository::class)->storeRepository($data);
            $user = $this->repository->getByUid($user['uid']);
        }
        return $this->selfLogin($user);
    }


}