<?php
/**
 * Created by PhpStorm.
 * User: Lxh
 * Date: 2019/6/10
 * Time: 10:54
 */

namespace App\Http\Controllers\Admin;

use App\Common\Code;
use App\Exceptions\QueryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Apply\RefreshPrecheckRequest;
use App\Repositories\Apply\ApplyCorrectRepository;
use App\Repositories\Apply\ApplyFileExceptionRepository;
use App\Repositories\Apply\ApplyFileRepository;
use App\Http\Requests\Approval\CoordinateRequest;
use App\Repositories\User\UserMessageRepository;
use App\Rules\Decimal;
use Illuminate\Http\Request;
use App\Repositories\Apply\ApplyRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Apply\ApprovalRepository;
use App\Repositories\Apply\ApprovalConfigRepository;

class ApprovalController extends Controller
{
	protected $applyRepository;
	protected $approvalRepository;
	protected $approvalConfigRepository;

	public function __construct(
		ApplyRepository $applyRepository,
			ApprovalRepository $approvalRepository, 
			ApprovalConfigRepository $approvalConfigRepository){
		
		$this->applyRepository = $applyRepository;
		$this->approvalRepository = $approvalRepository;
		$this->approvalConfigRepository = $approvalConfigRepository;
	}
	
	/**
	 * 列表-配置
	 */
	public function configList(Request $request)
	{
		$data = $request->all();
		$result = $this->approvalConfigRepository->configList($data);
		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 保存-配置
	 */
	public function configUpdate(Request $request)
	{
		$data = $request->all();
	
		$rules = [
			'config_list' => ['required']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}
		
		$result = $this->approvalConfigRepository->configUpdate($data);
		if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 列表-分页
	 */
	public function list(Request $request)
	{
		$data = $request->all();
		$data['staff_id'] = getLoginStaff('id');
		$result = $this->approvalRepository->operatorAuth($data);
		if (!empty($result)) {
			// 有权限
			$data['department_id'] = $result['department_id'];
			$result = $this->approvalRepository->list($data);
		}
		return codeRender(Code::OK, $result);
	}

    /**
     *
     * @api GET /api/approval/detail 详情
     * @apiVersion 1.0.0
     * @apiName ApprovalDetail
     * @apiGroup 运营端--申请申报管理
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {Number} approval_id
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
    "id": 161,
    "children_id": 0,
    "enterprise_id": 1,
    "project_id": 17,
    "mold_id": 1,
    "user_id": 25,
    "user_name": "liang",
    "number": 152,
    "title": "温江区产业扶持专项资金申请表",
    "policy_name": "温江区关于促进民营经济健康发展实施意见",
    "project_name": "ff34411",
    "enterprise_name": "深圳市腾讯计算机系统有限公司",
    "regist_address": "四川省成都市温江区海峡两岸科技产业开发园",
    "regist_time": 1104163200,
    "regist_capital": "2000.00",
    "business_address": "222",
    "business_area": "0.00",
    "unified_credit_code": "91510100765384552K",
    "organization_code": "765384552",
    "industry_text": [
    "农、林、牧、渔业",
    "农业",
    "谷物种植"
    ],
    "industry_id": [
    "1",
    "2",
    "3"
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
    "apply_status": 9,
    "audit_status": 1,
    "audit_time": 1568857181,
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
    "id": 221,
    "file_name": "7257120_904_thumb (3).jpg",
    "file_url": "https://xkd-saas-base.oss-cn-beijing.aliyuncs.com/dev-wenjiang/20190919/1/12hJxEKkmiDbNuKOw8xLJzIiu7jUzSDpx0sQTCum.jpg",
    "file_type": 3,
    "project_materials_id": 56,
    "created_at": "1568857135"
    },
    {
    "id": 222,
    "file_name": "7257120_904_thumb (4).jpg",
    "file_url": "https://xkd-saas-base.oss-cn-beijing.aliyuncs.com/dev-wenjiang/20190919/1/EwsbFMiOsvVdKUSoWtSLjP5nllYBzm2rtMwpNVE3.jpg",
    "file_type": 3,
    "project_materials_id": 56,
    "created_at": "1568857135"
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
    "support_content": "222",
    "allocation_time": 1567526400,
    "submit_time": 1568822400,
    "business_id": "wenjiang-1569744921-181118",
    "pdf_url": "",
    "zip_business_id": "wenjiang-1573608565-889518",
    "zip_url": "http://upload.service.dev-wenjiang.heroera.com/storage/",
    "is_supplement": 0,
    "able_revocation": 0,
    "created_staff_id": 0,
    "created_at": "1568857105",
    "updated_at": "1574478402",
    "deleted_at": null,
    "economy_list": [
    {
    "year": "2016",
    "content_list": [
    {
    "id": 4453,
    "apply_id": 161,
    "year": "2016",
    "content": "1",
    "type": 1
    },
    {
    "id": 4456,
    "apply_id": 161,
    "year": "2016",
    "content": "1",
    "type": 2
    },
    {
    "id": 4471,
    "apply_id": 161,
    "year": "2016",
    "content": "1",
    "type": 7
    },
    {
    "id": 4459,
    "apply_id": 161,
    "year": "2016",
    "content": "1",
    "type": 3
    },
    {
    "id": 4468,
    "apply_id": 161,
    "year": "2016",
    "content": "1",
    "type": 6
    },
    {
    "id": 4462,
    "apply_id": 161,
    "year": "2016",
    "content": "1",
    "type": 4
    },
    {
    "id": 4465,
    "apply_id": 161,
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
    "id": 4454,
    "apply_id": 161,
    "year": "2017",
    "content": "1",
    "type": 1
    },
    {
    "id": 4472,
    "apply_id": 161,
    "year": "2017",
    "content": "1",
    "type": 7
    },
    {
    "id": 4469,
    "apply_id": 161,
    "year": "2017",
    "content": "1",
    "type": 6
    },
    {
    "id": 4466,
    "apply_id": 161,
    "year": "2017",
    "content": "1",
    "type": 5
    },
    {
    "id": 4463,
    "apply_id": 161,
    "year": "2017",
    "content": "1",
    "type": 4
    },
    {
    "id": 4460,
    "apply_id": 161,
    "year": "2017",
    "content": "1",
    "type": 3
    },
    {
    "id": 4457,
    "apply_id": 161,
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
    "id": 4464,
    "apply_id": 161,
    "year": "2018",
    "content": "1",
    "type": 4
    },
    {
    "id": 4461,
    "apply_id": 161,
    "year": "2018",
    "content": "1",
    "type": 3
    },
    {
    "id": 4467,
    "apply_id": 161,
    "year": "2018",
    "content": "1",
    "type": 5
    },
    {
    "id": 4458,
    "apply_id": 161,
    "year": "2018",
    "content": "1",
    "type": 2
    },
    {
    "id": 4470,
    "apply_id": 161,
    "year": "2018",
    "content": "1",
    "type": 6
    },
    {
    "id": 4455,
    "apply_id": 161,
    "year": "2018",
    "content": "1",
    "type": 1
    },
    {
    "id": 4473,
    "apply_id": 161,
    "year": "2018",
    "content": "1",
    "type": 7
    }
    ]
    }
    ],
    "apply_id": 161,
    "approval_id": "161",
    "approval_type": 3,
    "approval_status": 1,
    "approval_audit_type": 0,
    "approval_start_time": 1567267200,
    "approval_end_time": 1572537599,
    "approval_department_id": 4,
    "approval_department_name": "园区管委会企服中心1",
    "approval_remark": "ccccccc",
    "approval_need_day": 40,
    "approval_time_sign": true,
    "approval_end_day": 16,
    "approval_coordinate_list": [],
    "tax_list": {
    "year": "2018",
    "tax_country": 0,
    "tax_location": 0,
    "tax_economy": "1"
    },
    "credit_list_one": {
    "year": "2018",
    "list": []
    },
    "credit_list_two": {
    "year": "2017",
    "list": []
    },
    "invoice_exception_num": 0,
    "invoice_file_num": 0,
    "approval_config": {
    "config_audit": "25",
    "config_timeout": "5"
    },
    "approval_list": [
    {
    "approval_id": 157,
    "department_id": 1,
    "approval_type": 1,
    "start_time": 1568857136,
    "audit_time": 1568857181,
    "expert_mark": null,
    "department_mark": null,
    "business_id": null,
    "pdf_url": null,
    "department_name": "区企业服务中心",
    "submit_time": null,
    "created_at": "1568857136",
    "take_time": 0,
    "file_list": []
    },
    {
    "approval_id": 161,
    "department_id": 29,
    "approval_type": 3,
    "start_time": 1567267200,
    "audit_time": 0,
    "expert_mark": null,
    "department_mark": null,
    "business_id": null,
    "pdf_url": null,
    "department_name": "测试部门",
    "submit_time": null,
    "created_at": "1568857238",
    "take_time": "0个工作日0小时0分0秒",
    "file_list": []
    },
    {
    "approval_id": 164,
    "department_id": 17,
    "approval_type": 3,
    "start_time": 1567267200,
    "audit_time": 0,
    "expert_mark": null,
    "department_mark": null,
    "business_id": null,
    "pdf_url": null,
    "department_name": "前端开发部",
    "submit_time": null,
    "created_at": "1568857285",
    "take_time": "0个工作日0小时0分0秒",
    "file_list": []
    },
    {
    "approval_id": 160,
    "department_id": 10,
    "approval_type": 3,
    "start_time": 1567267200,
    "audit_time": 0,
    "expert_mark": null,
    "department_mark": null,
    "business_id": null,
    "pdf_url": null,
    "department_name": "普通部门2",
    "submit_time": null,
    "created_at": "1568857238",
    "take_time": "0个工作日0小时0分0秒",
    "file_list": []
    },
    {
    "approval_id": 163,
    "department_id": 9,
    "approval_type": 3,
    "start_time": 1567267200,
    "audit_time": 0,
    "expert_mark": null,
    "department_mark": null,
    "business_id": null,
    "pdf_url": null,
    "department_name": "普通部门1普通部门1普通部门1普通部门1",
    "submit_time": null,
    "created_at": "1568857275",
    "take_time": "0个工作日0小时0分0秒",
    "file_list": []
    },
    {
    "approval_id": 159,
    "department_id": 4,
    "approval_type": 3,
    "start_time": 1567267200,
    "audit_time": 0,
    "expert_mark": null,
    "department_mark": null,
    "business_id": null,
    "pdf_url": null,
    "department_name": "园区管委会企服中心1",
    "submit_time": null,
    "created_at": "1568857238",
    "take_time": "0个工作日0小时0分0秒",
    "file_list": []
    },
    {
    "approval_id": 162,
    "department_id": 7,
    "approval_type": 3,
    "start_time": 1567267200,
    "audit_time": 0,
    "expert_mark": "【【主审专家意见：123",
    "department_mark": "【【主审部门意见：323",
    "business_id": "wenjiang-1564562750-455377",
    "pdf_url": "https://xkd-saas-base.oss-cn-beijing.aliyuncs.com/qa-wenjiang/20190731/b/2sl2aP6iaHhLvrpSI3ixoZ8OWoDY3YzIDUIyYjIG.pdf",
    "department_name": "园区管委会企服中心4",
    "submit_time": 1564562750,
    "created_at": "1568857275",
    "take_time": "0个工作日0小时0分0秒",
    "file_list": []
    },
    {
    "approval_id": 158,
    "department_id": 5,
    "approval_type": 2,
    "start_time": 1568995200,
    "audit_time": 0,
    "expert_mark": null,
    "department_mark": null,
    "business_id": null,
    "pdf_url": null,
    "department_name": "园区管委会企服中心2",
    "submit_time": null,
    "created_at": "1568857181",
    "take_time": "0个工作日0小时0分0秒",
    "selectCoordinateTime": "1568857238"
    }
    ],
    "main_department": {
    "department_id": 5,
    "department_name": "园区管委会企服中心2",
    "staff_id": 24,
    "staff_name": "王迪",
    "mobile": "13458655834"
    },
    "defer_mark": ""
    }
    }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
	/**
	 * 详情
	 */
	public function detail(Request $request)
	{
		$data = $request->all();

		$rules = [
			'approval_id' => ['required', 'integer']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}
		
		// 申请表
		$result = $this->approvalRepository->detail($data);

		if (!empty($result) && isset($result['able_revocation']) && $result['able_revocation'] == APPLY_ABLE_REVOCATION['yes']) {
            $this->applyRepository->setUnAbleRevocation($result['apply_id']);
        }

		if (!empty($result)) {
            app(UserMessageRepository::class)->readApproval($result['approval_id']);
        }

		return codeRender(Code::OK, $result);
	}

    /**
     *
     * @api post /api/approval/accept 企业服务中心受理
     * @apiVersion 1.0.0
     * @apiName ApprovalAccept
     * @apiGroup 运营端--申报申请管理
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {Number} approval_id 审批id
     * @apiParam {Number} department_id 主审部门id
     * @apiParam {String} department_name 主审部门
     * @apiParam {Array} push_list 推送部门
     * @apiParam {Number} push_list.1 部门id
     * @apiParam {String} expert_mark 专家意见
     * @apiParam {String} department_mark 部门意见
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
	/**
	 * 企业服务部受理
	 */
	public function accept(Request $request)
	{
		$data = $request->all();
	
		$rules = [
			'approval_id' => ['required', 'integer'],
			'department_id' => ['required', 'integer'],
			'department_name' => ['required'],
            'expert_mark' => ['required', 'string', 'max:500'],
            'department_mark' => ['required', 'string', 'max:500'],
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}

		if ($data['apply_status'] == APPLY_STATUS['two']) {
		    // 预处理
            return codeRender(Code::APPROVAL_PREPARE_CHECKING_ERROR);
        } elseif ($data['apply_status'] != APPLY_STATUS['three']) {
            // 待受理
            return codeRender(Code::APPROVAL_TYPE_ERROR);
		}
		
		// 当前审批类型判断
		if ($data['approval_type'] != APPROVAL_TYPE['one']) {
			return codeRender(Code::APPROVAL_TYPE_ERROR);
		}

		$result = $this->approvalRepository->accept($data);
		if (is_numeric($result)) {
			return codeRender($result);
		} else if (!$result) {
			return codeRender(Code::DB_ERROR);
		}

		return codeRender(Code::OK, $result);
	}

    /**
     * FUNCTION_NAME : refreshPrecheck
     * author : jp
     * 重新预检
     * @param RefreshPrecheckRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws QueryException
     */
	public function refreshPrecheck(RefreshPrecheckRequest $request)
    {
        $apply_id = $request->input('apply_id');
        $data = app(ApplyRepository::class)->findRepository($apply_id);
        DB::beginTransaction();
        try {
            app(ApplyFileRepository::class)->refreshInvoice($apply_id);
            app(ApplyRepository::class)->refreshPrecheck($apply_id);
            app(ApplyFileExceptionRepository::class)->refreshApply($apply_id);
            DB::commit();;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR);
        }

        return codeRender(Code::OK);
    }
	
	/**
	 * 选择协同审核部门
	 */
	public function coordinate(CoordinateRequest $request)
	{
		$data = $request->all();

		if ($data['start_time'] > $data['end_time']) {
			return codeRender(Code::APPROVAL_TIME_ERROR);
		}
		
		// 待主审部门审核
        if (!in_array($data['apply_status'],  [APPLY_STATUS['five'], APPLY_STATUS['six']])) {
            return codeRender(Code::APPROVAL_TYPE_ERROR);
        }
		
		// 当前审批类型判断
		if ($data['approval_type'] != APPROVAL_TYPE['two']) {
			return codeRender(Code::APPROVAL_TYPE_ERROR);
		}

        $data['approval_type'] = APPROVAL_TYPE['three'];

        // 这里只需要过滤掉已经添加的协同部门 这里不再限制是否已经添加
//        $coordinateExist = $this->approvalRepository->getCoordinate($data['apply_id']);
//        $exist = array_column($coordinateExist['department_list'], 'department_id');
//        $list = $data['department_list'];
//
//        foreach ($list as $key => $value) {
//            if (in_array($value['department_id'], $exist)) {
//                unset($list[$key]);
//            }
//        }
//        $data['department_list'] = $list;
        
		$result = $this->approvalRepository->coordinate($data);
		if (is_numeric($result)) {
			return codeRender(Code::APPROVAL_STAFF_EXIST_ERROR);
		} else if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
	
		return codeRender(Code::OK, $result);
	}

    /**
     *
     * @api post /api/approval/mark 审批理由和补充资料
     * @apiVersion 1.0.0
     * @apiName ApprovalMark
     * @apiGroup 运营端--申报申请管理
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {Number} approval_id 审批id
     * @apiParam {String} mark 备注
     * @apiParam {Number} type  1企业服务不受理2园区办公室延时拨款3主审部门补充资料4协同部门补充资料
     * @apiParam {Number} refresh  是否发票存档
     * @apiParam {String} expert_mark 专家意见
     * @apiParam {String} department_mark 部门意见
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
	/**
	 * 审批理由和补充资料表
	 * type:1企业服务不受理2园区办公室延时拨款3主审部门补充资料4协同部门补充资料
	 */
	public function mark(Request $request)
	{
		$data = $request->all();

		$rules = [
			'approval_id' => ['required', 'integer'],
			'type' => ['required', 'integer'],
			'mark' => ['required', 'string', 'max:500'],
            'refresh' =>  ['nullable', 'integer'],
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}
        $applyId = $request->input('apply_id', 0);
		switch ($data['type']){
			case APPROVAL_MARK_TYPE['one']:
				if ($data['apply_status'] != APPLY_STATUS['three']) {
                    // 企业服务不受理: 待受理 状态可操作
                    return codeRender(Code::APPROVAL_TYPE_ERROR);
				} elseif ($data['apply_status'] == APPLY_STATUS['two']) {
				    // 待系统预处理
                    return codeRender(Code::APPROVAL_PREPARE_CHECKING_ERROR);
                }
		
				// 审批类型判断
				if ($data['approval_type'] != APPROVAL_TYPE['one']) {
					return codeRender(Code::APPROVAL_TYPE_ERROR);
				}
		
				break;
			case APPROVAL_MARK_TYPE['two']:
				// 园区办公室延时拨款: 待拨款 状态可操作
				if ($data['apply_status'] != APPLY_STATUS['eight']) {
					return codeRender(Code::APPROVAL_TYPE_ERROR);
				}
		
				// 审批类型判断
				if ($data['approval_type'] != APPROVAL_TYPE['five']) {
					return codeRender(Code::APPROVAL_TYPE_ERROR);
				}
		
				break;
			case APPROVAL_MARK_TYPE['three']:
				// 主审部门补充资料： 待主审部门审核-线下会审中
				if (!in_array($data['apply_status'], [APPLY_STATUS['five'], APPLY_STATUS['six']])) {
					return codeRender(Code::APPROVAL_TYPE_ERROR);
				}
		
				// 审批类型判断
				if ($data['approval_type'] != APPROVAL_TYPE['two']) {
					return codeRender(Code::APPROVAL_TYPE_ERROR);
				}
		
				break;
			case APPROVAL_MARK_TYPE['four']:
				// 协同部门补充资料： 待主审部门审核
				if ($data['apply_status'] != APPLY_STATUS['five']) {
					return codeRender(Code::APPROVAL_TYPE_ERROR);
				}
		
				// 审批类型判断
				if ($data['approval_type'] != APPROVAL_TYPE['three']) {
					return codeRender(Code::APPROVAL_TYPE_ERROR);
				}
		
				break;
		
			default:
				return codeRender(Code::PARAM_ERROR, '', '', 'type');
		}
	
		$result = $this->approvalRepository->mark($data);
		if (is_numeric($result)) {
			return codeRender($result);
		} else if (!$result) {
			return codeRender(Code::DB_ERROR);
		}

		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 主审部门-审计操作
	 * audit_type: 1需要审计参与  2 延长审核时间
	 */
	public function audit(Request $request)
	{
		$data = $request->all();
	
		$rules = [
			'approval_id' => ['required', 'integer'],
			'audit_type' => ['required', 'integer'],
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}
		
		// 协同部门补充资料： 待主审部门审核
		$statusArr = [APPLY_STATUS['five'], APPLY_STATUS['six']];
		if (!in_array($data['apply_status'], $statusArr)) {
			return codeRender(Code::APPROVAL_TYPE_ERROR);
		}
		
		// 审批类型判断
		if ($data['approval_type'] != APPROVAL_TYPE['two']) {
			return codeRender(Code::APPROVAL_TYPE_ERROR);
		}

		$result = $this->approvalRepository->audit($data);
		if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
	
		return codeRender(Code::OK, $result);
	}

	/**
	 * 审批意见
	 * opinion_type 
	 * 1协同部门提交意见 
	 * 2主审部门审核通过意见、线下会审通过意见、指挥部审核通过提交意见
	 * 3主审部门审核不通过意见 、线下会审不通过意见、指挥部审核不通过提交意见
	 * 4主审部门提交指挥部填写意见
	 */
	public function opinion(Request $request)
	{
		$data = $request->all();
	
		$rules = [
			'approval_id' => ['required', 'integer'],
			'opinion_type' => ['required', 'integer']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}
        $applyId = $request->input('apply_id', 0);

		switch ($data['opinion_type']){
			case APPROVAL_OPTION_TYPE['one']:
				// 协同部门提交意见 
				// 申请表状态是待主审部门审核、线下会审中可操作(只能在主审部门审核环节中可以)
				$statusArr = [APPLY_STATUS['five'], APPLY_STATUS['six']];
				if (!in_array($data['apply_status'], $statusArr)) {
					return codeRender(Code::APPROVAL_TYPE_ERROR);
				}
				
				// 审批类型判断
				if ($data['approval_type'] != APPROVAL_TYPE['three']) {
					return codeRender(Code::APPROVAL_TYPE_ERROR);
				}

				break;
			case APPROVAL_OPTION_TYPE['two']:
                // 检查是否有订正
                $have = app(ApplyCorrectRepository::class)->checkApproval($applyId);
                if (!empty($have)) {
                    return codeRender($have);
                }

				// 主审部门审核通过意见、线下会审通过意见、指挥部审核通过提交意见
				// 当前状态是待主审部门审核、线下会审中、待指挥部审核 可操作
				$statusArr = [APPLY_STATUS['five'], APPLY_STATUS['six'], APPLY_STATUS['seven']];
				if (!in_array($data['apply_status'], $statusArr)) {
					return codeRender(Code::APPROVAL_TYPE_ERROR);
				}
				
				// 分类处理-审批类型判断
				$applyStatus = $data['apply_status'];
				// 主审部门审核通过意、线下会审通过意见
				if ($applyStatus == APPLY_STATUS['five'] || $applyStatus == APPLY_STATUS['six']) {
					// 审批类型判断
					if ($data['approval_type'] != APPROVAL_TYPE['two']) {
						return codeRender(Code::APPROVAL_TYPE_ERROR);
					}
				} else {
					// 待指挥部审核 可操作:审批类型判断
					if ($data['approval_type'] != APPROVAL_TYPE['four']) {
						return codeRender(Code::APPROVAL_TYPE_ERROR);
					}
				}
				
				break;
			case APPROVAL_OPTION_TYPE['three']:
				// 主审部门审核不通过意见 、线下会审不通过意见、指挥部审核不通过提交意见
				$statusArr = [APPLY_STATUS['five'], APPLY_STATUS['six'], APPLY_STATUS['seven']];
				if (!in_array($data['apply_status'], $statusArr)) {
					return codeRender(Code::APPROVAL_TYPE_ERROR);
				}
				
				// 分类处理-审批类型判断
				$applyStatus = $data['apply_status'];
				// 主审部门审核不通过意见 、线下会审不通过意见
				if ($applyStatus == APPLY_STATUS['five'] || $applyStatus == APPLY_STATUS['six']) {
					// 审批类型判断
					if ($data['approval_type'] != APPROVAL_TYPE['two']) {
						return codeRender(Code::APPROVAL_TYPE_ERROR);
					}
				} else {
					// 待指挥部审核 可操作:审批类型判断
					if ($data['approval_type'] != APPROVAL_TYPE['four']) {
						return codeRender(Code::APPROVAL_TYPE_ERROR);
					}
				}
	
				break;
			case APPROVAL_OPTION_TYPE['four']:
				// 主审部门提交指挥部填写意见
				// 申请表状态是线下会审中可操作
                // 检查是否有订正
                $have = app(ApplyCorrectRepository::class)->checkApproval($applyId);
                if (!empty($have)) {
                    return codeRender($have);
                }
				$statusArr = [APPLY_STATUS['six']];
				if (!in_array($data['apply_status'], $statusArr)) {
					return codeRender(Code::APPROVAL_TYPE_ERROR);
				}
				
				// 审批类型判断
				if ($data['approval_type'] != APPROVAL_TYPE['two']) {
					return codeRender(Code::APPROVAL_TYPE_ERROR);
				}
				
				break;
				
			default:
				return codeRender(Code::PARAM_ERROR, '', '', 'opinion_type');
		}

		$result = $this->approvalRepository->opinion($data);
		if (is_numeric($result)) {
			return codeRender($result);
		} else if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
	
		return codeRender(Code::OK, $result);
	}

	/**
	 * 主审部门-需要线下会审
	 */
	public function offline(Request $request)
	{
		$data = $request->all();
	
		$rules = [
			'approval_id' => ['required', 'integer']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}
		
		// 当前状态是待主审部门审核才能进行线下会审
		if ($data['apply_status'] != APPLY_STATUS['five']) {
			return codeRender(Code::APPROVAL_TYPE_ERROR);
		}
		// 审批类型判断
		if ($data['approval_type'] != APPROVAL_TYPE['two']) {
			return codeRender(Code::APPROVAL_TYPE_ERROR);
		}
		
		$result = $this->approvalRepository->offline($data);
		if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
	
		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 填写拨款反馈
	 */
	public function feedback(Request $request)
	{
		$data = $request->all();
	
		$rules = [
			'approval_id' => ['required', 'integer'],
			'support_content' => ['required', 'numeric', new Decimal()],
			'allocation_time' => ['required']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}
	
		// 当前状态是待拨款才能操作
		if ($data['apply_status'] != APPLY_STATUS['eight']) {
			return codeRender(Code::APPROVAL_TYPE_ERROR);
		}
		// 审批类型判断
		if ($data['approval_type'] != APPROVAL_TYPE['five']) {
			return codeRender(Code::APPROVAL_TYPE_ERROR);
		}
	
		$result = $this->approvalRepository->feedback($data);
		if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
	
		return codeRender(Code::OK, $result);
	}
	
	
	/**
	 * 保存审批意见附件-多个
	 */
	public function saveOpinionFile(Request $request)
	{
		$data = $request->all();
	
		$rules = [
			'approval_id' => ['required', 'integer'],
			'file_list' => ['required']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}
	
		$result = $this->approvalRepository->saveOpinionFile($data);
		if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
	
		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 删除审批意见附件-单个
	 */
	public function deleteOpinionFile(Request $request)
	{
		$data = $request->all();
	
		$rules = [
			'approval_id' => ['required', 'integer'],
			'approval_file_id' => ['required', 'integer']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}
	
		$result = $this->approvalRepository->deleteOpinionFile($data);
		if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
	
		return codeRender(Code::OK, $result);
	}
}