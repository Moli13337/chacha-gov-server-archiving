<?php
/**
 * Created by PhpStorm.
 * User: Lxh
 * Date: 2019/6/10
 * Time: 10:54
 */

namespace App\Http\Controllers\Home;

use App\Common\Code;
use App\Exceptions\QueryException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\ApplyService;
use Illuminate\Http\Request;
use App\Repositories\Apply\ApplyRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Apply\YoutuRepository;
use App\Http\Controllers\Service\TextCheckService;

class ApplyController extends Controller
{
	protected $applyRepository;
	protected $youtuRepository;

	public function __construct(ApplyRepository $applyRepository, YoutuRepository $youtuRepository)
	{
		$this->applyRepository = $applyRepository;
		$this->youtuRepository = $youtuRepository;
	}

    /**
     *
     * @api post /home/apply/list 申报列表
     * @apiVersion 1.0.0
     * @apiName ApplyList
     * @apiGroup PC--我的申报
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {Number} page_no
     * @apiParam {Number} page_size
     *
     * @apiSuccess {Number} code 返回代码
     * @apiSuccess {String} message 返回信息
     * @apiSuccess {Object} data 返回数据块
     * @apiSuccess {Object} data.data.has_correct 是否有订正资料 false/true
     * @apiSuccess {Object} data.data.has_material 是否有补充资料 false/true
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
    {
    "code": 200,
    "message": "操作成功",
    "data": {
    "total": 162,
    "total_page": 17,
    "current_page": 1,
    "per_page_num": 10,
    "data": [
    {
    "id": 169,
    "policy_name": "温江区关于促进民营经济健康发展实施意见",
    "project_name": "额鹅鹅鹅",
    "apply_status": 5,
    "audit_time": 1573798130,
    "created_at": "1571052066",
    "children_id": 0,
    "has_material": false,
    "has_correct": true
    }
    ]
    }
    }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
	/**
	 * 列表-分页
	 */
	public function list(Request $request)
	{
		$data = $request->all();

		$columns = [
			'id', 
			'policy_name', 
			'project_name', 
			'apply_status', 
			'audit_time', 
			'created_at',
			'children_id',
			'able_revocation'
		];
		$result = $this->applyRepository->clientList($data, $columns);
		return codeRender(Code::OK, $result);
	}

    /**
     *
     * @api get /home/apply/detail 申报详情
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
	/**
	 * 详情
	 */
	public function detail(Request $request)
	{
		$data = $request->all();
		
		$rules = [
			'id' => ['required', 'integer']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}

		$result = $this->applyRepository->detail($data);
		
		return codeRender(Code::OK, $result);
	}
	
	/**
	 * 新增
	 */
	public function store(Request $request)
	{
		$data = $request->all();
		
		$rules = [
			'save_type' => ['required', 'integer']
		];
		$validator = Validator::make($data, $rules);
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
		
		// 操作类型  1 草稿  2 企业基本信息  3 项目申报   4 上传附件
		$btnType = $data['save_type'];
		if ($btnType === 1) {
			$rules = [
                'policy_name' => ['string', 'max:100'],
                'mold_id' => ['required', 'integer'],
                'project_name' => ['string', 'max:100'],
				'enterprise_name' => ['string', 'max:100'],
				'regist_address'=> ['string', 'max:100'],
				'regist_time'=> ['string', 'min:1', 'max:10'],
				'regist_capital'=> ['string', 'max:20'],
				'business_address'=> ['string', 'max:255'],
				'business_area'=> ['string', 'max:20'],
				'unified_credit_code'=> ['string', 'max:20'],
				'organization_code'=> ['string', 'max:80'],
				'employee_number' => ['string', 'max:10'],
				'employee_degree'=> ['string', 'max:10'],
				'employee_junior'=> ['string', 'max:10'],
				'employee_other'=> ['string', 'max:10'],
				'legal_name'=> ['string', 'max:30'],
				'legal_phone'=> ['string', 'max:30'],
				'legal_wechat'=> ['string', 'max:30'],
				'charge_name'=> ['string', 'max:30'],
				'charge_phone'=> ['string', 'max:30'],
				'charge_wechat'=> ['string', 'max:30'],
				'contact_name'=> ['string', 'max:30'],
				'contact_phone'=> ['string', 'max:30'],
				'contact_wechat'=> ['string', 'max:30']
			];
			$validator = Validator::make($data, $rules);
			if($validator->fails()){
				return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
			}
			
			$result = $this->applyRepository->store($data);
			if (!$result) {
				return codeRender(Code::DB_ERROR);
			}
			return codeRender(Code::OK, $result);
			
		} else if ($btnType == 2) {
			$rules = [
				'enterprise_id' => ['required', 'integer'],
				'project_id' => ['required', 'integer'],
				'mold_id' => ['required', 'integer'],
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
			$validator = Validator::make($data, $rules);
			if($validator->fails()){
				return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
			}
			
			if (!is_array($data['industry_id'])) {
				return codeRender(Code::PARAM_ERROR , '', '', 'industry_id');
			}
			if (!is_array($data['economy_list'])) {
				return codeRender(Code::PARAM_ERROR , '', '', 'economy_list');
			}
			
			$result = $this->applyRepository->store($data);
			if (!$result) {
				return codeRender(Code::DB_ERROR);
			}
			
			return codeRender(Code::OK, $result);
			
		} else {
			return codeRender(Code::PARAM_ERROR);
		}
	}
	
	/**
	 * 修改
	 * save_type: 操作类型  1 草稿  2 企业基本信息  3 项目申报   4 上传附件  5补充材料
	 */
	public function update(Request $request)
	{
		$data = $request->all();
		$rules = [
			'id' => ['required', 'integer'],
			'save_type' => ['required', 'integer']
		];
		$validator = Validator::make($data, $rules);
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

		// 操作类型  1 草稿  2 企业基本信息  3 项目申报   4 上传附件 5补充材料
		$btnType = $data['save_type'];
		if ($btnType === 1) {
			$rules = [
                'policy_name' => ['string', 'max:100'],
                'mold_id' => ['required_with:policy_name', 'integer'],
                'project_name' => ['string', 'max:100'],
				'enterprise_name' => ['string', 'max:100'],
				'regist_address'=> ['string', 'max:100'],
                'regist_time'=> ['string', 'min:1', 'max:10'],
				'regist_capital'=> ['string', 'max:20'],
				'business_address'=> ['string', 'max:255'],
				'business_area'=> ['string', 'max:20'],
				'unified_credit_code'=> ['string', 'max:20'],
				'organization_code'=> ['string', 'max:80'],
				'employee_number' => ['string', 'max:10'],
				'employee_degree'=> ['string', 'max:10'],
				'employee_junior'=> ['string', 'max:10'],
				'employee_other'=> ['string', 'max:10'],
				'legal_name'=> ['string', 'max:30'],
				'legal_phone'=> ['string', 'max:30'],
				'legal_wechat'=> ['string', 'max:30'],
				'charge_name'=> ['string', 'max:30'],
				'charge_phone'=> ['string', 'max:30'],
				'charge_wechat'=> ['string', 'max:30'],
				'contact_name'=> ['string', 'max:30'],
				'contact_phone'=> ['string', 'max:30'],
				'contact_wechat'=> ['string', 'max:30'],
				'business_content'=> ['string', 'max:1000'],
				'plan_content'=> ['string', 'max:1000'],
				'approval_organ' => ['string', 'max:100'],
				'approval_number'=> ['string', 'max:100'],
				'qualifications'=> ['string', 'max:1000'],
				'provisions'=> ['string', 'max:1000'],
				'apply_criteria'=> ['string', 'max:500'],
				'apply_money'=> ['string', 'max:10'],
				'other_notes'=> ['string', 'max:1000'],
				'file_list'=> ['nullable','array'],
				'file_list.*.file_url'=> ['string'],
				'file_list.*.file_type'=> ['integer'],
				'file_list.*.project_materials_id'=> ['integer'],
			];
			
			$validator = Validator::make($data, $rules);
			if($validator->fails()){
				return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
			}
			
			$result = $this->applyRepository->updateApply($data, 1);
			if (is_numeric($result)) {
				return codeRender($result);
			} else if (!$result) {
				return codeRender(Code::DB_ERROR);
			}
			return codeRender(Code::OK, true);
				
		} else if ($btnType == 2) {
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
			$validator = Validator::make($data, $rules);
			if($validator->fails()){
				return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
			}
			
			if (!is_array($data['industry_id'])) {
				return codeRender(Code::PARAM_ERROR , '', '', 'industry_id');
			}
			if (!is_array($data['economy_list'])) {
				return codeRender(Code::PARAM_ERROR , '', '', 'economy_list');
			}

			// 对经济指标限制
            $economy_list = $data['economy_list'];
			$years = array_column($economy_list, 'year');
			$years = array_unique(array_filter($years));
			if (count($years) != 3 || count($economy_list) != 7*3) {
                return codeRender(Code::PARAM_ERROR , '', '', 'economy_list');
            }
				
			$result = $this->applyRepository->updateApply($data, 2);
			if (is_numeric($result)) {
				return codeRender($result);
			} else if (!$result) {
				return codeRender(Code::DB_ERROR);
			}
				
			return codeRender(Code::OK, $result);
			
		} else if ($btnType == 3) {
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
			$validator = Validator::make($data, $rules);
			if($validator->fails()){
				return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
			}

			$result = $this->applyRepository->updateApply($data, 3);
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
					if (empty($value['file_url']) || empty($value['file_type']) || empty($value['project_materials_id'])) {
						return codeRender(Code::PARAM_ERROR , '', '', 'file_list');
					}
				}
			}
				
			$result = $this->applyRepository->updateApply($data, 4);
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
				
			$result = $this->applyRepository->updateApply($data, 5);
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
	
	/**
	 * 删除
	 */
	public function delete(Request $request)
	{
		$data = $request->all();
	
		$rules = [
			'id' => ['required', 'integer']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}

		$result = $this->applyRepository->deleteApply($data);
		if (is_numeric($result)) {
			return codeRender($result);
		} else if (!$result) {
			return codeRender(Code::DB_ERROR);
		}
		return codeRender(Code::OK, $result);
	}

    /**
     *
     * @api post /home/apply/revocation 撤销申报
     * @apiVersion 1.0.0
     * @apiName revocation
     * @apiGroup 客户端--项目申报
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
	public function revocation(Request $request)
    {
        $data = $request->all();

        $rules = [
            'id' => ['required', 'integer']
        ];
        $validator = Validator::make($data, $rules);
        if($validator->fails()){
            return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
        }

        $where = [
            'id' => $request->input('id')
        ];
        $data = $this->applyRepository->simpleDetail($where);
        if (empty($data) || $data['enterprise_id'] != $request->input('enterprise_id')) {
            return codeRender(Code::PARAM_ERROR);
        }
        if ($data['able_revocation'] != APPLY_ABLE_REVOCATION['yes']  ||
            !in_array($data['apply_status'], [APPLY_STATUS['two'], APPLY_STATUS['three']])) {
            return codeRender(Code::APPLY_REVOCATION_CANCEL);
        }

        $res = app(ApplyService::class)->revocation($request->input('id'), $data);

        return codeRender(Code::OK, $res);
    }
}