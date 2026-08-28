<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2020/1/3
 * Time: 11:40
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Common\Util;
use App\Exceptions\CodeException;
use App\Exceptions\QueryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\LoginRequest;
use App\Http\Requests\Staff\RegisterRequest;
use App\Repositories\SmsRepository;
use App\Repositories\Staff\ResourceRepository;
use App\Repositories\Staff\RoleRepository;
use App\Repositories\Staff\StaffRepository;
use App\Repositories\Staff\StaffTokenRepository;
use App\Repositories\User\UserMessageRepository;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{


    protected $repository;
    protected $staffTokenRepository;
    public function __construct(StaffRepository $repository, StaffTokenRepository $staffTokenRepository)
    {
        $this->repository = $repository;
        $this->staffTokenRepository = $staffTokenRepository;
    }

    /**
     *
     * @api POST /api/common/register 注册
     * @apiVersion 1.0.0
     * @apiName ApiName
     * @apiGroup DefaultGroup
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {String} Parm1 参数1
     * @apiParam {Number} Parm2 参数2
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
     *              "field-1": "xx",
     *              "field-2": "xx",
     *              "field-3": "xx"
     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->only(['mobile', 'name', 'uid']);
        // 判断 登录用户 绑定uid
        $unique = $this->repository->checkUnique($data);

        if (is_array($unique)) {
            // 修改
            $data['id'] = $unique['id'];
            // 编号处理
            unset($data['number']);
            $result = $this->repository->updateStaff($data);
            $staff = $this->bindUid($data);
            $this->sendMessage($data);
        } elseif ($unique) {
            // 重复返回
            $staff = $this->bindUid($data);
            $this->sendMessage($data);
        } else {
            $staffId = $this->storeStaff($data);
            $where = [
                ['id', '=', $staffId]
            ];
            $staff = $this->repository->staffDetail($where);
        }

        $staff = $this->selfLogin($staff);

        return codeRender(Code::OK, $staff);
    }

    /**
     * FUNCTION_NAME : storeStaff
     * author : jp
     * 保存用户
     * @param $data
     * @return bool
     */
    private function storeStaff($data)
    {
        // 新增
        // 编号处理: 从1开始连续增加
        $number = STAFF_NUMBER;
        $resultLast = $this->repository->findLast($data);
        if (!empty($resultLast)) {
            $number = ++$resultLast['number'];
        }
        $data['number'] = $number;

        $result = $this->repository->storeStaff($data);
        if (!empty($result)) {

            $this->sendMessage($data);
        }
        return $result;
    }

    public function sendMessage($data)
    {
        // 查找超级管理员
        $staff = app(RoleRepository::class)->getStaffAdmin();
        if (empty($staff)) {
            return false;
        }

        $send = [
            'template' => SMS_TEMPLATE['staff_register'],
            'telephone' => $staff['mobile'],
            'param' => [
                'name' => $data['name']
            ],
        ];
        app(SmsRepository::class)->send($send);

        // 发站内信

        $message = [
            'content' => trans('message.staff_register', ['name' => $data['name']]),
            'user_id' => $staff['id'],
            'user_type' => MESSAGE_USER_TYPE['staff'],
            'type' => USER_MESSAGE_READ['not'],
            'source_type_id' => USER_MESSAGE_SOURCE['staff_register'],
        ];
        app(UserMessageRepository::class)->storeRepository($message);


    }

    /**
     * FUNCTION_NAME : bindUid
     * author : jp
     * 绑定uid
     * @param $data
     * @return array
     * @throws CodeException
     */
    private function bindUid($data)
    {
        $where = [
            ['mobile', '=', $data['mobile']],
        ];
        $staff = $this->repository->staffDetail($where);
        if (empty($staff)) {
            throw new CodeException(Code::LOGIN_MOBILE_EMPTY_ERROR);
        }
        $uid = array_get($staff,'uid', '');

        if (empty($uid)) {
            $update = [
                'uid' => $data['uid'],
                'id' => $staff['id'],
                'name' => $data['name'],
            ];
            $result = $this->repository->updateStaff($update);
            if (!$result) {
                throw new CodeException(Code::DB_ERROR);
            }
        } elseif (!empty($uid) && $uid != $data['uid']) {
            throw new CodeException(Code::PARAM_ERROR);
        }
        return $staff;
    }

    /**
     * FUNCTION_NAME : selfLogin
     * author : jp
     * 登录
     * @param $staff
     * @return array
     * @throws CodeException
     */
    public function selfLogin($staff)
    {
        if (empty($staff)) {
            throw new CodeException(Code::LOGIN_MOBILE_EMPTY_ERROR);
        }
        // 查询菜单、接口权限
        $permission = app(ResourceRepository::class)->permissionList([
            'staff_id' => $staff['id']
        ]);
        if (is_numeric($permission)) {
            throw new CodeException($permission);
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
            throw new CodeException(Code::FAIL);
        }

        // token data
        $dataToken = [
            'staff_id' => $staffToken['staff_id'],
            'sign' => $staffToken['sign']
        ];
        $token = Util::tokenEncode(['data' => $dataToken]);
        $staff['token'] = $token;
        $staff['token_pre'] = BASE_BEARER;

        $staff = array_except($staff, ['uid','password', 'deleted_at']);
        return $staff;
    }

    /**
     *
     * @api POST /api/common/login 登录
     * @apiVersion 1.0.0
     * @apiName 登录
     * @apiGroup 运营端--登录
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {String} uid
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
        $where = [
            ['uid', '=', $params['uid']]
        ];
        $staff = $this->repository->staffDetail($where);
        $staff = $this->selfLogin($staff);

        return codeRender(Code::OK, $staff);


    }


    public function loginV2(LoginRequest $request)
    {
        $params = $this->filter($request);
        $where = [
            ['uid', '=', $params['uid']]
        ];
        $staff = $this->repository->staffDetail($where);

        if (empty($staff)) {
            $data = [
                'uid' => $params['uid'],
            ];
            try {
                $staff = $this->repository->newStoreRepository($data);
                $staff = $staff->toArray();
                $newRole = [
                    'staff_id' => $staff['id'],
                    'role_id' => 1,
                ];
                app(RoleRepository::class)->bindStaffOne($newRole);

            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                throw new QueryException(Code::DB_ERROR, $e->getMessage());
            } catch (QueryException $e) {
                DB::rollBack();
                throw new QueryException(Code::DB_ERROR, $e->getMessage());
            } catch (\Exception $e) {
                DB::rollBack();
                throw new QueryException(Code::FAIL, $e->getMessage());
            }
            $where = [
                ['id', '=', $staff['id']]
            ];
            $staff = $this->repository->staffDetail($where);
        }
        $staff = $this->selfLogin($staff);

        return codeRender(Code::OK, $staff);
    }
}