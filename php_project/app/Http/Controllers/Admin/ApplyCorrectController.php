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
use App\Http\Controllers\Service\ApplyCorrectService;
use App\Http\Requests\ApplyCorrect\AgainRequest;
use App\Http\Requests\ApplyCorrect\AgreeRequest;
use App\Http\Requests\ApplyCorrect\DetailRequest;
use App\Http\Requests\ApplyCorrect\InvalidRequest;
use App\Http\Requests\ApplyCorrect\ListRequest;
use App\Http\Requests\ApplyCorrect\SaveRequest;
use App\Repositories\Apply\ApplyCorrectRepository;
use App\Repositories\Staff\StaffDepartmentRepository;

class ApplyCorrectController extends Controller
{

    protected $repository;
    protected $correctService;
    public function __construct(ApplyCorrectRepository $repository, ApplyCorrectService $correctService)
    {
        $this->repository = $repository;
        $this->correctService = $correctService;
    }

    /**
     *
     * @api get /api/approval/correct/list 订正资料列表
     * @apiVersion 1.0.0
     * @apiName CorrectList
     * @apiGroup 运营端--申请申报管理--订正资料
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {String} keyword
     * @apiParam {Number} status 状态 1-待批准 2-不批准 3-待订正 4-待审核 5-订正无效 6-重新订正（这里需要再次生成新的记录） 7-订正完成 8-订正作废
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

     * {
        "code": 200,
        "message": "操作成功",
        "data": {
            "total": 4,
            "total_page": 1,
            "current_page": 1,
            "per_page_num": 4,
            "data": [{
                "id": 19,
                "apply_id": 169,
                "approval_id": 182,
                "department_id": 4,
                "mark": "",
                "status": 3,
                "source_id": 0,
                "audit_time": 1574220682,
                "submit_time": 0,
                "created_at": "1574217218",
                "status_name": "待订正",
                "apply": {
                    "id": 169,
                    "enterprise_id": 1,
                    "project_id": 24,
                    "user_id": 1,
                    "user_name": "蒋",
                    "number": 160,
                    "project_name": "额鹅鹅鹅",
                    "enterprise_name": "深圳市腾讯计算机系统有限公司"
                },
                "department": {
                    "department_id": 4,
                    "name": "园区管委会企服中心1",
                    "pivot": {
                        "id": 182,
                        "department_id": 4
                    }
                },
                "operator_staff": {
                    "id": 9,
                    "name": "张三2",
                    "mobile": "18583253111",
                    "pivot": {
                        "department_id": 4,
                        "staff_id": 9
                    }
                },
                "user": {
                    "user_id": 1,
                    "name": "蒋",
                    "mobile": "18808054854",
                    "pivot": {
                        "id": 169,
                        "user_id": 1
                    }
                }
            },
            {
            "id": 17,
            "apply_id": 179,
            "approval_id": 182,
            "department_id": 4,
            "mark": "66",
            "status": 7,
            "source_id": 15,
            "audit_time": 1574151168,
            "submit_time": 0,
            "created_at": "1573887673",
            "status_name": "订正完成",
            "apply": {
                "id": 179,
                "enterprise_id": 1,
                "project_id": 24,
                "user_id": 1,
                "user_name": "蒋",
                "number": 170,
                "project_name": "额鹅鹅鹅",
                "enterprise_name": "成都海科康商贸有限公司同人店"
            },
            "department": {
                "department_id": 4,
                "name": "园区管委会企服中心1",
                "pivot": {
                    "id": 182,
                    "department_id": 4
                }
            },
            "operator_staff": {
                "id": 9,
                "name": "张三2",
                "mobile": "18583253111",
                "pivot": {
                    "department_id": 4,
                    "staff_id": 9
                }
            },
            "user": {
                "user_id": 1,
                "name": "蒋",
                "mobile": "18808054854",
                "pivot": {
                    "id": 179,
                    "user_id": 1
                }
            }
        },
        {
        "id": 16,
        "apply_id": 169,
        "approval_id": 182,
        "department_id": 4,
        "mark": "66",
        "status": 5,
        "source_id": 15,
        "audit_time": 0,
        "submit_time": 0,
        "created_at": "1573887612",
        "status_name": "订正无效",
        "apply": {
        "id": 169,
        "enterprise_id": 1,
        "project_id": 24,
        "user_id": 1,
        "user_name": "蒋",
        "number": 160,
        "project_name": "额鹅鹅鹅",
        "enterprise_name": "深圳市腾讯计算机系统有限公司"
        },
        "department": {
        "department_id": 4,
        "name": "园区管委会企服中心1",
        "pivot": {
        "id": 182,
        "department_id": 4
        }
        },
        "operator_staff": {
        "id": 9,
        "name": "张三2",
        "mobile": "18583253111",
        "pivot": {
        "department_id": 4,
        "staff_id": 9
        }
        },
        "user": {
        "user_id": 1,
        "name": "蒋",
        "mobile": "18808054854",
        "pivot": {
        "id": 169,
        "user_id": 1
        }
        }
        },
        {
        "id": 15,
        "apply_id": 169,
        "approval_id": 182,
        "department_id": 4,
        "mark": "ss",
        "status": 5,
        "source_id": 0,
        "audit_time": 1573888811,
        "submit_time": 0,
        "created_at": "1573812924",
        "status_name": "订正无效",
        "apply": {
        "id": 169,
        "enterprise_id": 1,
        "project_id": 24,
        "user_id": 1,
        "user_name": "蒋",
        "number": 160,
        "project_name": "额鹅鹅鹅",
        "enterprise_name": "深圳市腾讯计算机系统有限公司"
        },
        "department": {
        "department_id": 4,
        "name": "园区管委会企服中心1",
        "pivot": {
        "id": 182,
        "department_id": 4
        }
        },
        "operator_staff": {
        "id": 9,
        "name": "张三2",
        "mobile": "18583253111",
        "pivot": {
        "department_id": 4,
        "staff_id": 9
        }
        },
        "user": {
        "user_id": 1,
        "name": "蒋",
        "mobile": "18808054854",
        "pivot": {
        "id": 169,
        "user_id": 1
        }
        }
        }
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

        $column = [
            'id',
            'apply_id',
            'approval_id',
            'department_id',
            'mark',
            'invalid_mark',
            'status',
            'source_id',
            'audit_time',
            'submit_time',
            'created_at',
            CREATED_STAFF_ID,
        ];
        $data = $this->repository->list($params, $column);

        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }

        return codeRender(Code::OK, $data);

    }

    /**
     *
     * @api GET /api/approval/correct/detail 订正资料详情
     * @apiVersion 1.0.0
     * @apiName CorrectDetail
     * @apiGroup 运营端--申请申报管理--订正资料
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
     * {
            "code": 200,
            "message": "操作成功",
            "data": {
                "id": 17,
                "apply_id": 179,
                "approval_id": 182,
                "department_id": 4,
                "mark": "66",
                "status": 7,
                "source_id": 15,
                "has_material": 0,
                "audit_time": 1574151168,
                "submit_time": 0,
                "created_staff_id": 9,
                "created_at": "1573887673",
                "updated_at": "1574151169",
                "deleted_at": null,
                "status_name": "订正完成",
                "apply": {
                    "id": 179,
                    "enterprise_id": 1,
                    "project_id": 24,
                    "user_id": 1,
                    "user_name": "蒋",
                    "number": 170,
                    "project_name": "额鹅鹅鹅",
                    "enterprise_name": "成都海科康商贸有限公司同人店",
                    "contact_phone": "11"
                },
                "department": {
                    "department_id": 4,
                    "name": "园区管委会企服中心1",
                    "pivot": {
                        "id": 182,
                        "department_id": 4
                    }
                },
                "operator_staff": {
                    "id": 9,
                    "name": "张三2",
                    "mobile": "18583253111",
                    "pivot": {
                        "department_id": 4,
                        "staff_id": 9
                    }
                },
                "user": {
                    "user_id": 1,
                    "name": "蒋",
                    "mobile": "18808054854",
                    "pivot": {
                        "id": 179,
                        "user_id": 1
                    }
                },
                "audit_department": {
                    "id": 1,
                    "name": "区企业服务中心"
                },
                "audit_staff": {
                    "id": 34,
                    "name": "2345",
                    "mobile": "17708112010"
                },
                "change_file": [
                    {
                    "id": 4,
                    "apply_id": 179,
                    "correct_id": 17,
                    "file_id": 0,
                    "correct_type": 1,
                    "file_name": "banner.png",
                    "file_url": "https://xkd-saas-base.oss-cn-beijing.aliyuncs.com/dev-wenjiang/20191116/3/j9ao79ai4TktUpH0C0bwmqOFYd9U8f9ysI0l3xmn.png",
                    "file_type": 3,
                    "check_status": 1,
                    "project_materials_id": 56,
                    "created_at": "1574067106",
                    "updated_at": "1574067106",
                    "deleted_at": null,
                    "correct_type_name": "新增"
                    },
                ],
                "change_content": [
                    "经营（办公）地址由[ss]变更为[s555s]; ",
                    "企业主营业务介绍由[222]变更为[222555566555]; ",
                    "2018年纳税额由[797]变更为[1]; "
                ]
            }
      }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function detail(DetailRequest $request)
    {
        $data = $this->correctService->correctDetail($request->input('id'));
        if (!empty($data)) {
            unset($data['change']);
        }
        unset($data['origin_content']);
        unset($data['changes']);
        return codeRender(Code::OK, $data);
    }

    /**
     *
     * @api POST /api/approval/correct/save 订正申请
     * @apiVersion 1.0.0
     * @apiName CorrectSave
     * @apiGroup 运营端--申请申报管理--订正资料
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {Number} approval_id 审批id
     * @apiParam {String} mark 订正原因
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
     *         "data":{
     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function save(SaveRequest $request)
    {
        $params = $request->all();
        $department_id = $params['approval_department_id'];
        $auth = $this->correctService->authDepartmentSubmit($department_id);
        if (!$auth) {
            return codeRender(Code::APPLY_CORRECT_DEPARTMENT_SAVE_ERROR);
        }
        $has = $this->repository->hasWait($params['apply_id']);
        if (!empty($has)) {
            return codeRender(Code::APPLY_CORRECT_SAVE_ERROR);
        }
        $res = $this->correctService->saveCorrect($params);
        return codeRender(Code::OK, $res);
    }

    /**
     *
     * @api POST /api/approval/correct/agree 订正批准
     * @apiVersion 1.0.0
     * @apiName CorrectAgree
     * @apiGroup 运营端--申请申报管理--订正资料
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {Number} ID
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
     *         "data":{
     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function agree(AgreeRequest $request)
    {
        $params = $this->filter($request);
        $res = $this->correctService->agree($params);

        return codeRender(Code::OK, $res);
    }


    /**
     *
     * @api POST /api/approval/correct/disagree 订正不批准
     * @apiVersion 1.0.0
     * @apiName CorrectDisagree
     * @apiGroup 运营端--申请申报管理--订正资料
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
     *     {
     *       "code": 200,
     *       "message": "操作成功",
     *         "data":{
     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function disagree(AgreeRequest $request)
    {
        $params = $this->filter($request);
        $res = $this->correctService->disagree($params);

        return codeRender(Code::OK, $res);
    }

    /**
     *
     * @api POST /api/approval/correct/pass 订正通过
     * @apiVersion 1.0.0
     * @apiName CorrectPass
     * @apiGroup 运营端--申请申报管理--订正资料
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
     *     {
     *       "code": 200,
     *       "message": "操作成功",
     *         "data":{
     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function pass(AgreeRequest $request)
    {
        $params = $this->filter($request);
        $res = $this->correctService->pass($params);

        return codeRender(Code::OK, $res);
    }

    /**
     *
     * @api POST /api/approval/correct/again 重订正
     * @apiVersion 1.0.0
     * @apiName CorrectAgain
     * @apiGroup 运营端--申请申报管理--订正资料
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {String} mark
     * @apiParam {Number} id
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
     *         "data":{

     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function again(AgainRequest $request)
    {
        $params = $this->filter($request);
        $res = $this->correctService->again($params);

        return codeRender(Code::OK, $res);
    }

    /**
     *
     * @api POST /api/approval/correct/invalid 作废
     * @apiVersion 1.0.0
     * @apiName CorrectInvalid
     * @apiGroup 运营端--申请申报管理--订正资料
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {String} id
     * @apiParam {Number} mark
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
     *         "data":{
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
    public function invalid(InvalidRequest $request)
    {
        $params = $this->filter($request);
        $res = $this->correctService->invalid($params);

        return codeRender(Code::OK, $res);
    }
}