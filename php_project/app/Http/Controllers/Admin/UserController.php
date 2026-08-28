<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/10
 * Time: 12:42
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Events\LogCommon;
use App\Exceptions\CodeException;
use App\Exceptions\QueryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\AdminChangeMobileRequest;
use App\Http\Requests\User\DetailRequest;
use App\Http\Requests\User\ForbiddenRequest;
use App\Http\Requests\User\ListRequest;
use App\Http\Requests\User\StewardRequest;
use App\Http\Requests\UserEnterpriseRelation\UpdateRelationRequest;
use App\Repositories\ActivityLogRepository;
use App\Repositories\LoginLogsRepository;
use App\Repositories\Steward\StewardUserRepository;
use App\Repositories\User\UserEnterpriseRelationRepository;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserTokenRepository;
use App\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use tests\Mockery\Adapter\Phpunit\EmptyTestCase;

class UserController extends Controller
{

    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * FUNCTION_NAME : list
     * author : jp
     * 列表
     * @param Request $request
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \App\Exceptions\QueryException
     */
    public function list(ListRequest $request)
    {
        $search = $this->filter($request);

        $search['order_by'] = [
            'id' => 'DESC'
        ];

        $data = $this->repository->list($search);
        // 读认证总数
        $data['auth_count'] = $this->repository->authCount();
        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }

        $user_ids = array_column($data['data'], 'id');
        $steward = app(StewardUserRepository::class)->getStewardList($user_ids);

        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]['enterprise'] = (object)array_get($value['enterprise'],0,[]);
            $data['data'][$key]['steward'] = (object)array_get($steward,$value['id'],[]);
        }
        
        return codeRender(Code::OK, $data);
    }

    /**
     * FUNCTION_NAME : detail
     * author : jp
     * 详情
     * @param DetailRequest $request
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function detail(DetailRequest $request)
    {
        $data = $this->repository->detail($request->input('id'));
        if (empty($data)) {
            return codeRender(Code::OK, $data);
        }
        $data['enterprise'] = (object)array_get($data['enterprise'],0,[]);
        $data['steward'] = (object)app(StewardUserRepository::class)->getSteward($data['id']);
//        $data['enterprise'] = (object)$data['enterprise'];
        return codeRender(Code::OK, $data);
    }

    /**
     * FUNCTION_NAME : delete
     * author : jp
     * 删除
     * @param DetailRequest $request
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \App\Exceptions\QueryException
     */
    public function delete(DetailRequest $request)
    {
        // 删除关联关系
        try {
            DB::beginTransaction();
            $data = $this->repository->deleteRepository($request->input('id'));
            app(UserEnterpriseRelationRepository::class)->deleteByUserId($request->input('id'));
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }

        return codeRender(Code::OK);
    }

    /**
     * FUNCTION_NAME : resetPwd
     * author : jp
     * 重置密码
     * @param DetailRequest $request
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \App\Exceptions\QueryException
     */
    public function resetPwd(DetailRequest $request)
    {
        $params['password'] = encryption(USER_INIT_PWD);
        $params['id'] = $request->input('id');
        $data = $this->repository->forgetPwdById($params);

        if (empty($data)) {
            return codeRender(Code::FAIL);
        }

        return codeRender(Code::OK);

    }

    /**
     * FUNCTION_NAME : updateRelation
     * author : jp
     * 更新认证关系
     * @param UpdateRelationRequest $request
     * @param UserEnterpriseRelationRepository $userEnterpriseRelationRepository
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws QueryException
     */
    public function updateRelation(UpdateRelationRequest $request, UserEnterpriseRelationRepository $userEnterpriseRelationRepository)
    {
        $where = Collection::filter(['user_id', 'enterprise_id'], $request->all());
        $data = $userEnterpriseRelationRepository->relation($where);

        if (!empty($data)) {
            return codeRender(Code::ENTERPRISE_USER_RELATION_NO_CHANGE_ERROR);
        }

        try {

            DB::beginTransaction();
            event(new LogCommon([
                'type' => ACTIVITY_TYPE['updated'],
                'description' => trans('mysqlColumn.user.relation'),
                'subject_id' => $where['user_id'],
                'attribute' => $where,
                'old' => [],
            ], ACTIVITY_SUBJECT_TYPE['user']));
            $userEnterpriseRelationRepository->deleteByUserId($request->input('user_id'));
            $userEnterpriseRelationRepository->deleteByEnterpriseId($request->input('enterprise_id'));
            $userEnterpriseRelationRepository->storeRepository($where);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }

        return codeRender(Code::OK);
    }

    /**
     * FUNCTION_NAME : forbidden
     * author : jp
     * 用户禁止
     * @param ForbiddenRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws QueryException
     */
    public function forbidden(ForbiddenRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->repository->updateRepository($this->filter($request));
            app(UserTokenRepository::class)->resetToken($request->input('id'));
            app(UserEnterpriseRelationRepository::class)->deleteByUserId($request->input('id'));
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }
        return codeRender(Code::OK);
    }

    /**
     * FUNCTION_NAME : changeMobile
     * author : jp
     * 变更手机号
     * @param AdminChangeMobileRequest $request
     * @throws QueryException
     */
    public function changeMobile(AdminChangeMobileRequest $request)
    {
        $params = $this->filter($request);

        $detail = $this->repository->detail($request->input('id'));

        if ($detail['mobile'] == $request->input('mobile')) {
            throw new CodeException(Code::PARAM_ERROR, trans('validation.custom.mobile.change_repeat'));
        }

        try {
            DB::beginTransaction();
            $this->repository->updateRepository($params);
            app(UserTokenRepository::class)->resetToken($request->input('id'));
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }
        return codeRender(Code::OK);
    }

    public function stewardSave(StewardRequest $request)
    {
        $data = [];
        $user_ids  =  array_unique($request->input('user_ids'));
        $staff_id  =  $request->input('staff_id');
        foreach ($user_ids as $key => $value) {
            $data[] = [
                'user_id' => $value,
                'staff_id' => $staff_id
            ];
        }

        try {
            DB::beginTransaction();
            app(StewardUserRepository::class)->deleteByUserId($user_ids);
            app(StewardUserRepository::class)->storeBatchRepository($data);
            DB::commit();

        } catch (QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }
        return codeRender(Code::OK);
    }

}