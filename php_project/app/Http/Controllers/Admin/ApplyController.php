<?php
/**
 * Created by PhpStorm.
 * User: Lxh
 * Date: 2019/6/10
 * Time: 10:54
 */

namespace App\Http\Controllers\Admin;

use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApprovalCoordinate\ListRequest;
use App\Http\Requests\ApprovalCoordinate\LogRequest;
use App\Repositories\Apply\ApprovalCoordinateLogRepository;
use App\Repositories\PdfRepository;
use Illuminate\Http\Request;
use App\Repositories\Apply\ApplyRepository;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Apply\ApprovalRepository;

class ApplyController extends Controller
{
	protected $applyRepository;
	protected $approvalRepository;

	public function __construct(ApplyRepository $applyRepository, ApprovalRepository $approvalRepository)
	{
		$this->applyRepository = $applyRepository;
		$this->approvalRepository = $approvalRepository;
	}

    /**
     *
     * @api GET /api/apply/list 申请记录列表
     * @apiVersion 1.0.0
     * @apiName list
     * @apiGroup 运营端--申报记录
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {String} keyword
     * @apiParam {Number} apply_status
     * @apiParam {Number} mold_id
     * @apiParam {Number} main_department_id 主审部门id
     * @apiParam {Number} start_time
     * @apiParam {Number} end_time
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
                "total": 7,
                "total_page": 1,
                "current_page": 1,
                "per_page_num": 10,
                "data": [
                    {
                    "id": 161,
                    "number": 152,
                    "policy_name": "温江区关于促进民营经济健康发展实施意见",
                    "project_name": "ff34411",
                    "enterprise_name": "深圳市腾讯计算机系统有限公司",
                    "contact_name": "11",
                    "contact_phone": "11",
                    "apply_status": 9,
                    "audit_time": 1568857181,
                    "created_at": "1568857105",
                    "submit_time": 1568822400,
                    "main_department": {
                        "department_id": 5,
                        "name": "园区管委会企服中心2",
                        "apply_id": 161
                    },
                    "has_material": false,
                    "has_correct": false
                    },
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
		
		// 草稿不查询
		if (!empty($data['apply_status']) && $data['apply_status'] == APPLY_STATUS['one']) {
			return codeRender(Code::OK, [
				'total' => 0,
				'data' => []
			]);
		}

		$columns = [
			'id', 
			'number', 
			'policy_name', 
			'project_name', 
			'enterprise_name', 
			'contact_name', 
			'contact_phone', 
			'apply_status', 
			'audit_time', 
			'created_at',
			'submit_time'
		];
		$result = $this->applyRepository->list($data, $columns);
		return codeRender(Code::OK, $result);
	}

	/**
	 *
	 * @api GET /api/apply/detail 申报详情
	 * @apiVersion 1.0.0
	 * @apiName ApiName
	 * @apiGroup 运营端--申报记录
	 *
	 * @apiHeader {String} Authorization 用户授权token
	 * @apiHeaderExample {json} Header-Example:
	 *     {
	 *       "Authorization": "xxx",
	 *     }
	 *
	 * @apiParam {String} Parm1 参数1
	 * @apiParam {Number} Parm2 参数2
	 *
	 * @apiSuccess {Number} code 返回代码
	 * @apiSuccess {String} message 返回信息
	 * @apiSuccess {Object} data 返回数据块
	 * @apiSuccess {Object} flow_status 流程状态 one two three four
     *                                 分别表示：提交申报 区企业服务中心 主审部门 园区管委会审核 管委会办公室拨款
     * @apiSuccess {Object} flow_status.status flow_status下的status 1待进行 2-进行中 3-已完成 4-已结束
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

		$result = $this->approvalRepository->detail($data);
        if (!empty($result) && isset($result['able_revocation']) && $result['able_revocation'] == APPLY_ABLE_REVOCATION['yes']) {
            $this->applyRepository->setUnAbleRevocation($result['id']);
        }
		return codeRender(Code::OK, $result);
	}
	
	
	/**
	 * 列表-发票异常列表
	 */
	public function fileExceptionList(Request $request)
	{
		$data = $request->all();
	
		$rules = [
			'apply_id' => ['required', 'integer']
		];
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return codeRender(Code::PARAM_ERROR , '', $validator->errors()->first());
		}
	
		$result = $this->applyRepository->fileExceptionList($data);
		return codeRender(Code::OK, $result);
	}

    /**
     *
     * @api GET /api/apply/selectCoordinateLog 选择协同部门日志
     * @apiVersion 1.0.0
     * @apiName selectCoordinateLog
     * @apiGroup 运营端--申报申请管理
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {Number} apply_id 申报id
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
                "total": 1,
                "total_page": 1,
                "current_page": 1,
                "per_page_num": 1,
                "data": [
                    {
                        "id": 1,
                        "apply_id": 171,
                        "approval_id": 176,
                        "created_staff_id": 9,
                        "created_at": "1573713851",
                        "updated_at": "1573713851",
                        "deleted_at": null,
                        "staff": {
                            "id": 9,
                            "name": "张三2",
                            "mobile": "18583253111"
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
    public function selectCoordinateLog(LogRequest $request)
    {
        $params = $this->filter($request);
        $params['order_by'] = ['id' => 'DESC'];

        $data = app(ApprovalCoordinateLogRepository::class)->search($params);

        return codeRender(Code::OK, $data);
    }

    /**
     *
     * @api GET /api/apply/selectCoordinateList 已选择的列表
     * @apiVersion 1.0.0
     * @apiName selectCoordinateList
     * @apiGroup 运营端--申报申请管理
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {Number} log_id
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
            "data": [
                {
                "id": 179,
                "apply_id": 171,
                "department_id": 5,
                "type": 3,
                "start_time": 1573401600,
                "end_time": 1577289599,
                "audit_time": 0,
                "status": 1,
                "audit_type": 0,
                "created_at": "1573713851",
                "updated_at": "1573713851",
                "deleted_at": null,
                "remark": "sss",
                "log_id": 1,
                "department_name": "园区管委会企服中心2"
                }
            ]
        }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function selectCoordinateList(ListRequest $request)
    {

        $params = [
            'apply_id' => $request->input('apply_id', 0),
            'id' => $request->input('log_id', 0)
        ];

        $data = app(ApprovalCoordinateLogRepository::class)->approval($params);

        return codeRender(Code::OK, $data);
    }


}