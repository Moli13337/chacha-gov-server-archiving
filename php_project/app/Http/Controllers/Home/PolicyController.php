<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/25
 * Time: 10:20
 */

namespace App\Http\Controllers\Home;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\DistrictService;
use App\Http\Controllers\Service\PolicyService;
use App\Http\Requests\Policy\HomeDetailRequest;
use App\Http\Requests\Policy\HomeListRequest;
use App\Repositories\Policy\PolicyRepository;

class PolicyController extends Controller
{

    protected $policyRepository;
    protected $policyService;

    public function __construct(PolicyRepository $policyRepository, PolicyService $policyService)
    {
        $this->policyRepository = $policyRepository;
        $this->policyService = $policyService;
    }

    public function list(HomeListRequest $request)
    {
        $params = $this->filter($request);
        $params['order_by'] = [
            'id' => 'DESC'
        ];
        $params['obj_type'] = [
            OBJ_TYPE['macro_policy'],
            OBJ_TYPE['sup_policy'],
            OBJ_TYPE['imple_regu'],
        ];
        $params['publish_status'] =PUBLISH_STATUS['yes'];
        $params = app(DistrictService::class)->clientDistrictFilter($params);
        $data = $this->policyService->cList($params);
        return codeRender(Code::OK, $data);
    }

    public function detail(HomeDetailRequest $request)
    {
        $detail = $this->policyService->detailByEncId($request->input('id'));

        if (empty($detail)) {
            return codeRender(Code::OK, $detail);
        }
        $base_relation_key = [
            'macro_policy_relation',
            'sup_policy_relation',
            'imple_regu_relation',
            'announce_relation',
            'publicity_relation',
        ];

        // 为前端准备的
        foreach ($base_relation_key as $k => $v) {
            if (!isset($detail[$v])) {
                $detail[$v] = [];
            }
        }

        return codeRender(Code::OK, $detail);
    }
}