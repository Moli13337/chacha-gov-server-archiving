<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/16
 * Time: 15:24
 */

namespace App\Http\Controllers\Home;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\ApplyCorrectService;
use App\Http\Requests\ApplyCorrect\DetailRequest;
use App\Http\Requests\ApplyCorrect\ListRequest;
use App\Repositories\Apply\ApplyCorrectContentRepository;
use App\Repositories\Apply\ApplyCorrectRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApplyCorrectController extends Controller
{

    protected $repository;
    protected $correctService;
    protected $correctContentRepository;
    public function __construct(ApplyCorrectRepository $repository,
                                ApplyCorrectService $correctService)
    {
        $this->repository = $repository;
        $this->correctService = $correctService;
    }

    /**
     * FUNCTION_NAME : save
     * author : jp
     * 保存， 所填内容字段和申报一样
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \App\Exceptions\CodeException
     */
    public function save(Request $request)
    {
        $data = $request->all();
        $rules = [
            'id' => ['required', 'integer'],
            'save_type' => ['required', 'integer', Rule::in([2,3,4])]
        ];
        $validator = \Validator::make($data, $rules);
        if($validator->fails()){
            return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
        }

        // 敏感词检测
        // 排除url
// 		$tmpArr = $data;
// 		if (!empty($tmpArr['file_list'])) {
// 			foreach ($tmpArr['file_list'] as $key => $value) {
// 				unset($value['file_url']);
// 				$tmpArr['file_list'][$key] = $value;
// 			}
// 		}
// 		$content = arrayToString($tmpArr);
// 		$content = getEnglishAndChinese($content);
// 		$contentArr = mbStrSplit($content);
// 		if (!empty($contentArr)) {
// 			foreach ($contentArr as $key => $value) {
// 				if (!app(TextCheckService::class)->check($value)) {
// 					return codeRender(Code::TEXT_CHECK_CONTENT_ERROR);
// 				}
// 			}
// 		}

        // 操作类型  2 企业基本信息  3 项目申报   4 上传附件 5-补充资料
        $btnType = $data['save_type'];
        if ($btnType == 2) {
            $rules = $this->getRule($btnType);
            $validator = \Validator::make($data, $rules);
            if($validator->fails()){
                return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
            }

            if (!is_array($data['industry_id'])) {
                return codeRender(Code::PARAM_ERROR , '', '', 'industry_id');
            }
            if (!is_array($data['economy_list'])) {
                return codeRender(Code::PARAM_ERROR , '', '', 'economy_list');
            }

            $result = $this->correctService->correctContent($data, 2);
            if (is_numeric($result)) {
                return codeRender($result);
            } else if (!$result) {
                return codeRender(Code::DB_ERROR);
            }

            return codeRender(Code::OK, $result);

        } else if ($btnType == 3) {
            $rules = $this->getRule($btnType);
            $validator = \Validator::make($data, $rules);
            if($validator->fails()){
                return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
            }

            $result = $this->correctService->correctContent($data, 3);
            if (is_numeric($result)) {
                return codeRender($result);
            } else if (!$result) {
                return codeRender(Code::DB_ERROR);
            }

            return codeRender(Code::OK, $result);

        } else if ($btnType == 4) {
            if (!empty($data['file_list'])) {
                $fileList = $data['file_list'];
                foreach ($fileList as $key => $value) {
                    if (empty($value['file_url']) || blank($value['file_type']) || empty($value['project_materials_id'])) {
                        return codeRender(Code::PARAM_ERROR , '', '', 'file_list');
                    }
                }
            }

            $result = $this->correctService->correctContent($data, 4);
            if (is_numeric($result)) {
                return codeRender($result);
            } else if (!$result) {
                return codeRender(Code::DB_ERROR);
            }

            return codeRender(Code::OK, $result);

        } else if ($btnType == 5) {
            if (empty($data['file_list'])) {
                return codeRender(Code::CHECK_EMPTY_ERROR , '', '', 'file_list');
            }

            $count = 0;
            foreach ($data['file_list'] as $key => $value) {
                if ($value['file_type'] == MATERIALS_TYPE['default']) {
                    $count++;
                    break;
                }
            }

            if ($count == 0) {
                return codeRender(Code::CHECK_EMPTY_ERROR , '', '', '补充材料');
            }

            $result = $this->correctService->correctContent($data, 5);
            if (is_numeric($result)) {
                return codeRender($result);
            } else if (!$result) {
                return codeRender(Code::DB_ERROR);
            }

            return codeRender(Code::OK, $result);

        } else {
            return codeRender(Code::PARAM_ERROR);
        }
    }

    protected function getRule($type)
    {
        $rules = [];
        switch ($type) {
            case 2:
                $rules = [
                    'enterprise_id' => ['required', 'integer'],
                    'policy_name' => ['required', 'string'],
                    'project_name' => ['required', 'string'],
                    'enterprise_name' => ['required', 'string'],
                    'regist_address'=> ['required', 'string'],
                    'regist_time'=> ['required','string', 'min:1', 'max:10'],
                    'regist_capital'=> ['required', 'string'],
                    'business_address'=> ['required', 'string'],
                    'business_area'=> ['required', 'string'],
                    'unified_credit_code'=>['required', 'string'],
                    'organization_code'=>['required', 'string'],
                    'industry_id'=>['required'],
                    'employee_number' => ['required', 'string'],
                    'employee_degree'=>['required', 'string'],
                    'employee_junior'=>['required', 'string'],
                    'employee_other'=> ['required', 'string'],
                    'legal_name'=> ['required', 'string'],
                    'legal_phone'=> ['required', 'string'],
                    'legal_wechat'=> ['required', 'string'],
                    'charge_name'=> ['required', 'string'],
                    'charge_phone'=> ['required', 'string'],
                    'charge_wechat'=> ['required', 'string'],
                    'contact_name'=> ['required', 'string'],
                    'contact_phone'=> ['required', 'string'],
                    'contact_wechat'=> ['required', 'string'],
                    'economy_list' => ['required']
                ];
                break;
            case 3:
                $rules = [
                    'business_content'=> ['required', 'string', 'max:1000'],
                    'plan_content'=> ['required', 'string', 'max:1000'],
                    'approval_organ' => ['required', 'string', 'max:100'],
                    'approval_number'=> ['required', 'string', 'max:100'],
                    'qualifications'=> ['required', 'string', 'max:1000'],
                    'provisions'=> ['required', 'string', 'max:1000'],
                    'apply_criteria'=> ['required', 'string', 'max:500'],
                    'apply_money'=> ['required', 'string', 'max:10'],
                    'other_notes'=> ['required', 'string', 'max:1000'],
                ];
                break;
        }

        return $rules;
    }

    /**
     *
     * @api get /home/apply/correct/detail 申报详情
     * @apiVersion 1.0.0
     * @apiName ApplyDetail
     * @apiGroup PC--我的申报
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {Number} id 参数2
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
    "id": 179,
    "children_id": 0,
    "enterprise_id": 1,
    "project_id": 24,
    "mold_id": 1,
    "user_id": 0,
    "user_name": "",
    "number": 170,
    "title": "温江区产业扶持专项资金申请表",
    "policy_name": "温江区关于促进民营经济健康发展实施意见",
    "project_name": "额鹅鹅鹅",
    "enterprise_name": "成都海科康商贸有限公司同人店",
    "regist_address": "成都市温江区柳城镇同人街112号19栋1楼17号",
    "regist_time": 1544716800,
    "regist_capital": "0.00",
    "business_address": "ss",
    "business_area": "0.00",
    "unified_credit_code": "91510115MA69BE0N2W",
    "organization_code": "MA69BE0N2",
    "industry_text": [
    "批发和零售业"
    ],
    "industry_id": [
    "1002"
    ],
    "employee_number": "11",
    "employee_degree": "11",
    "employee_junior": "11",
    "employee_other": "11",
    "legal_name": "11",
    "legal_phone": "11",
    "legal_wechat": "11",
    "charge_name": "11",
    "charge_phone": "11",
    "charge_wechat": "11",
    "contact_name": "11",
    "contact_phone": "11",
    "contact_wechat": "11",
    "business_content": "222",
    "plan_content": "222",
    "approval_organ": "222",
    "approval_number": "222",
    "qualifications": "22",
    "provisions": "22",
    "apply_criteria": "22",
    "apply_money": "11",
    "other_notes": "22222",
    "apply_status": 1,
    "audit_status": 1,
    "audit_time": 1573896559,
    "config": [
    {
    "id": 56,
    "project_id": 24,
    "name": "额鹅鹅鹅",
    "is_need": 2,
    "type": 3,
    "is_need_name": "据实提供",
    "file_list": [
    {
    "id": 270,
    "file_name": "banner.png",
    "file_url": "https://xkd-saas-base.oss-cn-beijing.aliyuncs.com/dev-wenjiang/20191116/3/j9ao79ai4TktUpH0C0bwmqOFYd9U8f9ysI0l3xmn.png",
    "file_type": 3,
    "project_materials_id": 56,
    "created_at": "1573896705"
    },
    {
    "id": 271,
    "file_name": "图片.png",
    "file_url": "https://xkd-saas-base.oss-cn-beijing.aliyuncs.com/dev-wenjiang/20191116/9/JYTLsMBlGFaVWsUSVAc86EMfarzEGh9p5MyDNOov.png",
    "file_type": 3,
    "project_materials_id": 56,
    "created_at": "1573896705"
    }
    ]
    },
    {
    "id": 57,
    "project_id": 24,
    "name": "请上传补充材料",
    "is_need": 2,
    "type": 0,
    "is_need_name": "据实提供",
    "file_list": []
    }
    ],
    "support_content": "",
    "allocation_time": 0,
    "submit_time": 0,
    "business_id": "",
    "pdf_url": "",
    "zip_business_id": "",
    "zip_url": "",
    "is_supplement": 0,
    "able_revocation": 0,
    "created_staff_id": 0,
    "created_at": "1573896559",
    "updated_at": "1573896561",
    "deleted_at": null,
    "economy_list": [
    {
    "year": "2016",
    "content_list": [
    {
    "id": 4684,
    "apply_id": 179,
    "year": "2016",
    "content": "1",
    "type": 1
    },
    {
    "id": 4687,
    "apply_id": 179,
    "year": "2016",
    "content": "1",
    "type": 2
    },
    {
    "id": 4702,
    "apply_id": 179,
    "year": "2016",
    "content": "1",
    "type": 7
    },
    {
    "id": 4690,
    "apply_id": 179,
    "year": "2016",
    "content": "1",
    "type": 3
    },
    {
    "id": 4699,
    "apply_id": 179,
    "year": "2016",
    "content": "1",
    "type": 6
    },
    {
    "id": 4693,
    "apply_id": 179,
    "year": "2016",
    "content": "1",
    "type": 4
    },
    {
    "id": 4696,
    "apply_id": 179,
    "year": "2016",
    "content": "1",
    "type": 5
    }
    ]
    },
    {
    "year": "2017",
    "content_list": [
    {
    "id": 4685,
    "apply_id": 179,
    "year": "2017",
    "content": "1",
    "type": 1
    },
    {
    "id": 4703,
    "apply_id": 179,
    "year": "2017",
    "content": "3",
    "type": 7
    },
    {
    "id": 4700,
    "apply_id": 179,
    "year": "2017",
    "content": "1",
    "type": 6
    },
    {
    "id": 4697,
    "apply_id": 179,
    "year": "2017",
    "content": "1",
    "type": 5
    },
    {
    "id": 4694,
    "apply_id": 179,
    "year": "2017",
    "content": "3",
    "type": 4
    },
    {
    "id": 4691,
    "apply_id": 179,
    "year": "2017",
    "content": "1",
    "type": 3
    },
    {
    "id": 4688,
    "apply_id": 179,
    "year": "2017",
    "content": "1",
    "type": 2
    }
    ]
    },
    {
    "year": "2018",
    "content_list": [
    {
    "id": 4695,
    "apply_id": 179,
    "year": "2018",
    "content": "1",
    "type": 4
    },
    {
    "id": 4692,
    "apply_id": 179,
    "year": "2018",
    "content": "1",
    "type": 3
    },
    {
    "id": 4698,
    "apply_id": 179,
    "year": "2018",
    "content": "1",
    "type": 5
    },
    {
    "id": 4689,
    "apply_id": 179,
    "year": "2018",
    "content": "1",
    "type": 2
    },
    {
    "id": 4701,
    "apply_id": 179,
    "year": "2018",
    "content": "1",
    "type": 6
    },
    {
    "id": 4686,
    "apply_id": 179,
    "year": "2018",
    "content": "1",
    "type": 1
    },
    {
    "id": 4704,
    "apply_id": 179,
    "year": "2018",
    "content": "1",
    "type": 7
    }
    ]
    }
    ],
    "has_material": false,
    "has_correct": true
    }
    }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function detail(Request $request)
    {
        $data = $request->all();
        $rules = [
            'id' => ['required', 'integer'],
        ];
        $validator = \Validator::make($data, $rules);
        if($validator->fails()){
            return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
        }

        $detail = $this->correctService->clientDetail($request->only(['id']));
        if (empty($detail)) {
            return codeRender(Code::APPLY_CORRECT_EXIST_ERROR);
        }
        return codeRender(Code::OK, $detail);
    }

    /**
     *
     * @api GET /home/apply/correct/detail 订正记录详情
     * @apiVersion 1.0.0
     * @apiName CorrectDetail
     * @apiGroup 官网--订正记录
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
        {
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
                    {
                    "id": 5,
                    "apply_id": 179,
                    "correct_id": 17,
                    "file_id": 0,
                    "correct_type": 1,
                    "file_name": "图片.png",
                    "file_url": "https://xkd-saas-base.oss-cn-beijing.aliyuncs.com/dev-wenjiang/20191116/9/JYTLsMBlGFaVWsUSVAc86EMfarzEGh9p5MyDNOov.png",
                    "file_type": 3,
                    "check_status": 1,
                    "project_materials_id": 56,
                    "created_at": 1574067106,
                    "updated_at": 1574067106
                    "created_at": "1574067106",
                    "updated_at": "1574067106",
                    "deleted_at": null,
                    "correct_type_name": "新增"
                    },
                    {
                    "id": 6,
                    "apply_id": 179,
                    "correct_id": 17,
                    "file_id": 0,
                    "correct_type": 1,
                    "file_name": "1574044250(1).png",
                    "file_url": "https://xkd-saas-base.oss-cn-beijing.aliyuncs.com/dev-wenjiang/20191118/f/wFW0OcVjQCx7tMximfb235f4P34CLMgW2YaJ8FP6.png",
                    "file_type": 3,
                    "check_status": 1,
                    "project_materials_id": 56,
                    "created_at": "1574067106",
                    "updated_at": "1574067106",
                    "deleted_at": null,
                    "correct_type_name": "新增"
                    }
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
    public function correctDetail(DetailRequest $request)
    {
        $detail = $this->correctService->correctDetail($request['id']);
        if (empty($detail)) {
            return codeRender(Code::OK, $detail);
        }
        unset($detail['origin_content']);
        unset($detail['changes']);
        unset($detail['change']);
        return codeRender(Code::OK, $detail);

    }

    /**
     *
     * @api GET /home/apply/correct/list 订正记录列表
     * @apiVersion 1.0.0
     * @apiName CorrectList
     * @apiGroup PC--订正记录
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {String} keyword
     * @apiParam {Number} status 状态 3-待订正 4-待审核 5-订正无效 6-重新订正 7-订正完成 8-订正作废
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
                "total": 2,
                "total_page": 1,
                "current_page": 1,
                "per_page_num": 2,
                "data": [
                    {
                        "id": 16,
                        "apply_id": 169,
                        "approval_id": 182,
                        "department_id": 4,
                        "mark": "66",
                        "changes": null,
                        "status": 5,
                        "source_id": 15,
                        "has_material": 0,
                        "audit_time": 0,
                        "submit_time": 0,
                        "created_staff_id": 9,
                        "created_at": "1573887612",
                        "updated_at": "1573887612",
                        "deleted_at": null,
                        "status_name": "订正无效",
                        "apply": {
                            "id": 169,
                            "enterprise_id": 1,
                            "project_id": 24,
                            "user_id": 1,
                            "user_name": "蒋",
                            "number": 160,
                            "project_name": "额鹅鹅鹅",
                            "enterprise_name": "深圳市腾讯计算机系统有限公司",
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
                                "id": 169,
                                "user_id": 1
                            }
                        }
                    },
                ]
            }
     *  }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function correctList(ListRequest $request)
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
        $data = $this->repository->clientList($params, $column);
        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }
        return codeRender(Code::OK, $data);
    }


}