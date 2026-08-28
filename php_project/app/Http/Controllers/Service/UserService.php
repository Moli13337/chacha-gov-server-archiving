<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/8/26
 * Time: 18:19
 */

namespace App\Http\Controllers\Service;


use App\Common\Code;
use App\Common\Util;
use App\Repositories\LoginLogsRepository;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserTokenRepository;
use App\Support\Collection;
use Zhuzhichao\IpLocationZh\Ip;

class UserService
{

    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function login($user, $type = '')
    {
        // 模拟单点登录-存入token表

        $white = [
            'id',
            'uid',
            'name',
        ];
        $user = Collection::filter($white, $user);

        $type = empty($type) ? LOGIN_TYPE['pc'] : $type;
        $userToken = [
            'user_id' => $user['id'],
            'type' => $type,
            'name' => $user['name'],
            'sign' => signRandom(),
            'expire' => TOKEN_EXPIRE_HOME
        ];
        $resultToken = app(UserTokenRepository::class)->storeOrUpdateToken($userToken);
        if ($resultToken['code'] !== Code::OK) {
            return codeRender(Code::FAIL);
        }

        // token data
        $dataToken = [
            'user_id' => $userToken['user_id'],
            'type' => $type,
            'sign' => $userToken['sign']
        ];
        $token = Util::tokenEncode(['data' => $dataToken]);
        $user['token'] = $token;
        $user['token_pre'] = BASE_BEARER;


        $ip = \Illuminate\Support\Facades\Request::ip();
        $address = Ip::find($ip);
        $address = array_unique(Collection::filter([0,1,2], $address));

        try {
            $log_data = [
                'ip' => ip2long($ip),
                'address' => implode('',$address),
                'source_id' => $user['id'],
                'source_type' => LOGIN_LOG_TYPE['user'],
                'type' => LOGIN_TYPE['pc'],
            ];
            $res = app(LoginLogsRepository::class)->storeRepository($log_data);
        } catch (\Exception $e) {
            // TODO
//            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }

        app()->instance(LOGIN_HOME_KEY, $user);

        unset($user['password']);
        return $user;
    }
}