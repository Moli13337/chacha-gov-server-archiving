<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/25
 * Time: 18:05
 */

namespace App\Http\Controllers\Home;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\DistrictService;
use App\Http\Controllers\Service\PolicyService;
use App\Http\Requests\Policy\HomeDetailRequest;
use App\Http\Requests\Publicity\HomeListRequest;

class PublicityController extends Controller
{

    protected $policyService;

    public function __construct(PolicyService $policyService)
    {
        $this->policyService = $policyService;
    }

    public function list(HomeListRequest $request)
    {
        $params = $this->filter($request);
        $params['order_by'] = [
            'id' => 'DESC'
        ];

        if (empty($params['obj_type'])) {
            $params['obj_type'] = [
                OBJ_TYPE['announce'],
                OBJ_TYPE['publicity'],
                OBJ_TYPE['approval'],
            ];
        } else {
            $params['obj_type'] = [
                $params['obj_type']
            ];
        }
        $params['publish_status'] =PUBLISH_STATUS['yes'];
        $params = app(DistrictService::class)->clientDistrictFilter($params);
        $data = $this->policyService->publicityList($params);
        return codeRender(Code::OK, $data);
    }

    public function announce(HomeDetailRequest $request)
    {
        $data = $this->policyService->announceDetailByEncId($request->input('id'));
        $data = $this->dealRelation($data);
        return codeRender(Code::OK, $data);
    }

    public function publicity(HomeDetailRequest $request)
    {
        $data = $this->policyService->publicityDetailByEncId($request->input('id'));
        $data = $this->dealRelation($data);
        return codeRender(Code::OK, $data);
    }

    public function approval(HomeDetailRequest $request)
    {
        $data = $this->policyService->approvalDetailByEncId($request->input('id'));
        $data = $this->dealRelation($data);
        return codeRender(Code::OK, $data);
    }

    public function dealRelation($detail)
    {
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
        return $detail;
    }
}