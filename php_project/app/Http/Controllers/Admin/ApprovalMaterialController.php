<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/14
 * Time: 18:47
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApplyReplenish\DetailRequest;
use App\Http\Requests\ApplyReplenish\ListRequest;
use App\Repositories\Apply\ApprovalMaterialRepository;

class ApprovalMaterialController extends Controller
{

    public $repository;
    public function __construct(ApprovalMaterialRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     *
     * @api get /api/approval/material/list 补充资料列表
     * @apiVersion 1.0.0
     * @apiName MaterialList
     * @apiGroup 运营端--申报申请管理--补充资料
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {String} keyword
     * @apiParam {Number} status 补充状态 1待提交补充材料发送一次提醒2发送二次提醒3已提交补充材料
     * @apiParam {Number} start_time 开始时间
     * @apiParam {Number} end_time 结束时间
     * @apiParam {Number} page
     * @apiParam {Number} per_page
     *
     * @apiSuccess {Number} code 返回代码
     * @apiSuccess {String} message 返回信息
     * @apiSuccess {Object} data 返回数据块
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
    {
        "code": 200,
        "message": "操作成功",
        "data": {
            "total": 9,
            "total_page": 1,
            "current_page": 1,
            "per_page_num": 9,
            "data": [
                {
                    "id": 36,
                    "apply_id": 17,
                    "approval_id": 23,
                    "enterprise_id": 1,
                    "user_id": 1,
                    "mark": "tttttttttttttttttttttttttttt",
                    "status": 1,
                    "material": [],
                    "submit_time": 0,
                    "created_at": 1564127689,
                    "updated_at": 0,
                    "start_time": 1564070400,
                    "end_time": 1563984000,
                    "apply": {
                        "id": 17,
                        "enterprise_id": 1,
                        "project_id": 17,
                        "user_id": 1,
                        "user_name": "tets",
                        "number": 16,
                        "project_name": "ff34411",
                        "enterprise_name": "深圳市腾讯计算机系统有限公司",
                        "contact_phone": "111"
                    },
                    "department": {
                        "department_id": 4,
                        "name": "园区管委会企服中心1",
                        "pivot": {
                            "id": 23,
                            "department_id": 4
                        }
                    },
                    "staff": {
                        "id": 1,
                        "name": "张三",
                        "mobile": "17708112019"
                    },
                    "user": {
                        "user_id": 1,
                        "name": "蒋",
                        "mobile": "18808054854",
                    }
                },
            ]
        }
    }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        $params['order_by'] = ['id' => 'DESC'];

        $data = $this->repository->list($params);

        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }

        return codeRender(Code::OK, $data);

    }

    /**
     *
     * @api get /api/approval/material/detail 补充资料详情
     * @apiVersion 1.0.0
     * @apiName MaterialDetail
     * @apiGroup 运营端--申报申请管理--补充资料
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {Number} id
     *
     * @apiSuccess {Number} code 返回代码
     * @apiSuccess {String} message 返回信息
     * @apiSuccess {Object} data 返回数据块
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
    {
        "code": 200,
        "message": "操作成功",
        "data": {
            "id": 28,
            "apply_id": 3,
            "approval_id": 6,
            "enterprise_id": 1,
            "user_id": 1,
            "mark": "尊敬的用户，在关于促进民营经济健康发展资金政策政策类型的托尔斯泰项目的申报中，你提交的申报资料不齐全，请在2019-07-09~2019-07-08时间段内把以下资料补充完整，如果逾期未补充相关资料将会影响本次的项目申报。具体内容：ssss。同时企业在个人中心-我的申报中可以进行补充资料操作。",
            "status": 3,
            "material": [],
            "submit_time": 0,
            "created_at": 1562641590,
            "updated_at": 1562655216,
            "start_time": 0,
            "end_time": 1562675462,
            "apply": {
                "id": 3,
                "enterprise_id": 1,
                "project_id": 17,
                "user_id": 3,
                "user_name": "王五",
                "number": 2,
                "project_name": "ff34411",
                "enterprise_name": "深圳市腾讯计算机系统有限公司",
                "contact_phone": "18200490944"
            },
            "department": {
                "department_id": 4,
                "name": "园区管委会企服中心1",
            },
            "staff": {
                "id": 1,
                "name": "张三",
                "mobile": "17708112019"
            },
            "user": {
                "user_id": 3,
                "name": "黄婷",
                "mobile": "18200490943",
            }
        }
    }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function detail(DetailRequest $request)
    {

        $data = $this->repository->detail($request->input('id'));
        if (empty($data)) {
            return codeRender(Code::OK, $data);
        }

        $tmp = [
            'policy_name' => array_get($data['apply']??[], 'policy_name', ''),
            'project_name' => array_get($data['apply']??[], 'project_name', ''),
            'startTime' => empty($data['start_time']) ? '' : date('Y-m-d', $data['start_time']),
            'endTime' => empty($data['end_time']) ? '' : date('Y-m-d', $data['end_time']),
            'mark' => array_get($data, 'mark', ''),
        ];
        $data['sms_content'] = trans('message.sms.six', $tmp);
        return codeRender(Code::OK, $data);
    }
}