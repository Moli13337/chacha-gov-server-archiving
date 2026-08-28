<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/10
 * Time: 10:54
 */

namespace App\Http\Controllers\Home;


use App\Common\Code;
use App\Exceptions\CodeException;
use App\Exceptions\QueryException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\UserService;
use App\Http\Requests\Unbundling\FirstRequest;
use App\Http\Requests\Unbundling\SecondRequest;
use App\Http\Requests\User\ChangeEmailRequest;
use App\Http\Requests\User\ChangePwdRequest;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserUnbundlingRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * FUNCTION_NAME : detail
     * author : jp
     * 用户详情
     * @param Request $request
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function detail(Request $request)
    {
        $id = getLoginHome('id');
        $data = $this->repository->detail($id);
        unset($data['password']);
        return codeRender(Code::OK, $data);
    }

    /**
     * FUNCTION_NAME : changePassword
     * author : jp
     * 变更密码
     * @param ChangePwdRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \App\Exceptions\QueryException
     */
    public function changePassword(ChangePwdRequest $request)
    {
        $data = $this->filter($request);
        $data['id'] = (int)getLoginHome('id');

        if ($data['old_password'] == $data['password']) {
            return codeRender(Code::LOGIN_OLD_PASSWORD_REPEAT_ERROR);
        }

        $data['old_password'] = encryption($data['old_password']);
        $data['password'] = encryption($data['password']);

        // 查询旧密码是否匹配
        $resultStaff = $this->repository->findRepository($data['id']);
        if (empty($resultStaff) || $resultStaff['password'] != $data['old_password']) {
            return codeRender(Code::LOGIN_OLD_PASSWORD_ERROR);
        }
        // 密码处理
        $data2 = [
            'id' =>  $data['id'],
            'password' => $data['password']
        ];
        $result = $this->repository->updateRepository($data2);
        $user = app(UserService::class)->login($result);
        return codeRender(Code::OK, $user);
    }

    /**
     * FUNCTION_NAME : changeEmail
     * author : jp
     * 绑定邮箱
     * @param ChangeEmailRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \App\Exceptions\QueryException
     */
    public function changeEmail(ChangeEmailRequest $request)
    {
        $params = $this->filter($request);
        $params['id'] = getLoginHome('id');
        $this->repository->updateRepository($params);
        return codeRender(Code::OK);
    }

    /**
     * FUNCTION_NAME : unbundlingFirst
     * author : jp
     * 解绑第一步
     * @param FirstRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws QueryException
     */
    public function unbundlingFirst(FirstRequest $request)
    {
        $data = [
            'user_id' => getLoginHome('id'),
            'step' => UNBUNDLING_STEP_FIRST,
        ];
        $res =  app(UserUnbundlingRepository::class)->storeRepository($data);
        return codeRender(Code::OK);
    }

    /**
     * FUNCTION_NAME : unbundlingSecond
     * author : jp
     * 解绑第二步
     * @param SecondRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws CodeException
     * @throws QueryException
     */
    public function unbundlingSecond(SecondRequest $request)
    {
        $user_id = getLoginHome('id');

        if ($request->input('mobile') == getLoginHome('mobile')) {
            throw new CodeException(Code::PARAM_ERROR, trans('validation.custom.mobile.change_repeat'));
        }

        $has = app(UserUnbundlingRepository::class)->lastFirst($user_id);
        if (empty($has)) {
            throw new CodeException(Code::USER_UNBUNDLING_ERROR);
        }

        $update = [
            'id' => $has['id'],
            'step' => 2,
        ];
        $data = [
            'id' => $user_id,
            'mobile' => $request->input('mobile'),
        ];
        try {
            \DB::beginTransaction();
            app(UserUnbundlingRepository::class)->updateRepository($update);
            $this->repository->updateRepository($data);
            \DB::commit();
        } catch (QueryException $e) {
            \DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            \DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }

        return codeRender(Code::OK);
    }
}