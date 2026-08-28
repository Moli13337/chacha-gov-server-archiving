<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/12
 * Time: 17:20
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\PolicyService;
use App\Http\Requests\Policy\DeleteBatchRequest;
use App\Http\Requests\Policy\DeleteRequest;
use App\Http\Requests\Policy\DetailRequest;
use App\Http\Requests\Policy\GetPolicyForUnscrambleRequest;
use App\Http\Requests\Policy\GetPolicyRelationRequest;
use App\Http\Requests\Policy\LogRequest;
use App\Http\Requests\Policy\UpdatePublishRequest;
use App\Models\PolicyModel;
use App\Models\PolicyMoldModel;
use App\Repositories\ActivityLogRepository;
use App\Repositories\Policy\PolicyRepository;

class PolicyController extends Controller
{

    protected $repository;
    protected $policyService;

    public function __construct(PolicyRepository $repository,
                                PolicyService $policyService)
    {
        $this->repository = $repository;
        $this->policyService = $policyService;
    }

    public function delete(DeleteRequest $request)
    {
        $this->policyService->delete($request->input('id'));

        return codeRender(Code::OK);
    }

    public function updatePublish(UpdatePublishRequest $request)
    {
        if ($request->input('publish_status')  == PUBLISH_STATUS['yes']) {
            $detail = $this->repository->detailById($request->input('id'), ['content_url', 'obj_type']);
            if (empty($detail['content_url']) && in_array($detail['obj_type'],[OBJ_TYPE['macro_policy'],
                    OBJ_TYPE['sup_policy'], OBJ_TYPE['imple_regu']])) {
                return codeRender(Code::PARAM_ERROR, '',trans('validation.custom.publish_status.publish'));
            }
        }

        $this->policyService->updatePublish($this->filter($request));
        return codeRender(Code::OK);
    }

    public function getPolicyByName(GetPolicyRelationRequest $request)
    {
        $params = $this->filter($request);
        $params['order_by']['id'] = 'DESC';

        $data = $this->repository->list($params);

        return codeRender(Code::OK, $data);
    }

    public function deleteBatch(DeleteBatchRequest $request)
    {
        $this->policyService->deleteBatch($request->input('ids'));

        return codeRender(Code::OK);
    }

    public function simpleDetail(DetailRequest $request)
    {
        $column = [
            'id',
            'enc_id',
            'code',
            'name',
            'obj_type'
        ];
        $data = $this->repository->detailById($request->input('id'), $column);

        return codeRender(Code::OK, $data);
    }

    public function getPolicyForUnscramble(GetPolicyForUnscrambleRequest $request)
    {
        $params = $this->filter($request);
        $params['obj_type'] = [
            OBJ_TYPE['macro_policy'],
            OBJ_TYPE['sup_policy'],
            OBJ_TYPE['imple_regu'],
            OBJ_TYPE['announce'],
        ];
        $params['order_by']['id'] = 'DESC';


        $column = [
            'id',
            'enc_id',
            'code',
            'name',
            'obj_type'
        ];
        $data = $this->repository->getPolicyForUnscramble($params, $column);

        return codeRender(Code::OK, $data);
    }

    public function log(LogRequest $request, ActivityLogRepository $activityLogRepository)
    {
        $params = $this->filter($request);
        $params['subject_id'] = $request->input('id');
        $data = $activityLogRepository->getPolicyList($params);
        return codeRender(Code::OK, $data);
    }



}