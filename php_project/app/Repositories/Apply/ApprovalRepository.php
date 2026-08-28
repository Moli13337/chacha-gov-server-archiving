<?php
namespace App\Repositories\Apply;

use App\Events\ApplyFormPdfCreate;
use App\Events\ZipCreate;
use App\Exceptions\CodeException;
use App\Http\Controllers\Service\ZipService;
use App\Models\ApplyFileModel;
use App\Repositories\Staff\StaffDepartmentRepository;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Common\Code;
use App\Repositories\BaseRepository;
use App\Models\ApplyModel;
use App\Models\ApplyFileExceptionModel;
use Illuminate\Support\Facades\Log;
use App\Models\ApprovalModel;
use App\Models\ApprovalMarkModel;
use App\Repositories\User\UserMessageRepository;
use App\Models\UserEnterpriseRelationModel;
use App\Models\ApprovalOpinionModel;
use App\Models\ApprovalPushModel;
use App\Models\StaffBindDepartmentModel;
use App\Models\StaffModel;
use App\Models\StaffDepartmentModel;
use App\Models\EnterpriseTaxModel;
use App\Models\EnterpriseCreditModel;
use App\Models\CreditDepartmentModel;
use App\Http\Controllers\Service\PolicyService;
use App\Models\ApprovalMaterialModel;
use App\Models\UserModel;
use App\Repositories\SmsRepository;
use App\Repositories\PdfRepository;
use App\Models\ApprovalFileModel;

class ApprovalRepository  extends BaseRepository
{

	public function model()
	{
		return ApprovalModel::class;
	}
	
	/**
	 *  唯一性检查
	 * $isUpdate: true 更新  false 新增
	 */
	public function checkUnique($arr)
	{
		$where = [];
		$where[] = ['apply_id', '=', $arr['apply_id']];
		$where[] = ['type', '=', $arr['approval_type']];

		$staff = ApprovalModel::where($where)->limit(1)->get(['id'])->toArray();
		if (empty($staff)) {
			return false;
		}
		return true;
	}

	/**
	 * 列表
	 */
	public function list($arr)
	{
		$columns = [
			'f1.id AS approval_id',
			'f1.status AS approval_status',
			'f2.number',
			'f2.policy_name',
			'f2.project_name',
			'f2.enterprise_name',
			'f2.contact_name',
			'f2.contact_phone',
			'f2.apply_status',
			'f2.audit_time',
			'f1.created_at'
		];

		$where = [];
		$where[] = ['f1.department_id', '=', $arr['department_id']];
		
		// status
		if (!empty($arr['apply_status'])) {
			$where[] = ['f2.apply_status', '=', $arr['apply_status']];
		}

		$applyModel = (new ApprovalModel())
			->setTable('f1')
			->from(ApprovalModel::TABLE_NAME . ' AS f1')
			->join(ApplyModel::TABLE_NAME . ' AS f2','f1.apply_id','=','f2.id')
			->where($where);

		// 搜索
		if (!empty($arr['keyword'])) {
			$filterArr = ['f2.policy_name', 'f2.project_name', 'f2.enterprise_name'];
		
			// 去除空格
			$keyword = trim($arr['keyword']);
			$keyword = "%$keyword%";
			
			$applyModel = $applyModel->where(function ($q) use ($filterArr, $keyword) {
				$q = $q->where($filterArr[0], 'like', $keyword);
			
				foreach ($filterArr as $k => $v) {
					if ($k ==0) {
						continue;
					}
					$q = $q->orWhere($v, 'like', $keyword);
				}
				return $q;
			});
		}
		
		$count = $applyModel->count();

		$page = commonPage($arr);
		$list = $applyModel
			->orderBy('f1.id', 'desc')
			->offset($page['offset'])
			->limit($page['page_size'])
			->get($columns)
			->toArray();

		return returnPage($list, $count);
	}

	/**
	 * 详情
	 */
	public function detail($arr)
	{
		$applyId = 0;
		if (!empty($arr['approval_id'])) {
			// 运营端： 审批进入
			$applyId = $arr['apply_id'];
		} else {
			$applyId = $arr['id'];
		}
		
		$apply = app(ApplyRepository::class)->detailApply(['id' => $applyId]);
		if (empty($apply)) {
			return [];
		}

		//
        try {
            if (empty($apply['zip_url']) && empty($apply['zip_business_id'])) {

                $tmpFile = [];
                $tmpConfig = array_get($apply, 'config', []);
                foreach ($tmpConfig as $kt =>$v) {
                    $fileTmp = array_get($v, 'file_list', []);
                    foreach ($fileTmp as $ktf => $vtf) {
                        $tmpFile[] = $vtf;
                    }
                }
                $tmpBusiness_id = businessId();
                event(new ZipCreate([
                    'id' => $apply['id'],
                    'urls' => array_column($tmpFile, 'file_url'),
                    'name' => $apply['project_name'].'-'.$apply['enterprise_name'],
                    'business_id' => $tmpBusiness_id
                ]));

            }
        } catch (\Exception $e) {
            Log::error('create zip fail. '. $e->getMessage());
        }

		
		$apply['apply_id'] = $applyId;
		
		// 审批信息
		if (!empty($arr['approval_id'])) {
			$apply['approval_id'] = $arr['approval_id'];
			$apply['approval_type'] = $arr['approval_type'];
			$apply['approval_status'] = $arr['approval_status'];
			$apply['approval_audit_type'] = $arr['approval_audit_type'];
			$apply['approval_start_time'] = $arr['approval_start_time'];
			$apply['approval_end_time'] = $arr['approval_end_time'];
			$apply['approval_department_id'] = $arr['approval_department_id'];
			$apply['approval_department_name'] = $arr['approval_department_name'];
			
			// 协同部门查询备注
			if ($apply['approval_type'] == APPROVAL_TYPE['three']) {
				$approvalCoo = ApprovalModel::where([
						'id' => $arr['approval_id']
					])
					->limit(1)
					->get(['remark'])
					->toArray();
				
				$apply['approval_remark'] = empty($approvalCoo) ? '' : $approvalCoo[0]['remark'];
			}
			
			if (in_array($apply['approval_type'], 
				[APPROVAL_TYPE['two'], APPROVAL_TYPE['three'], APPROVAL_TYPE['five']])) {
				
				$appStartTime = $arr['approval_start_time'];
				$appEndTime = $arr['approval_end_time'];
				// 计算需要工作日
				$workDays = 0;
				$days = intval(($appEndTime - $appStartTime)/86400) + 1;
				if ($days > 0) {
					$currentDay = strtotime(date('Y-m-d', $appStartTime));
					while($days > 0) {
						// 累加天数
						if (getAttendenceExcept($currentDay)) {
							$workDays++;
						}
						$currentDay += 86400;
						$days--;
					}
				}
				$apply['approval_need_day'] = $workDays;
				
				
				// 计算截止的工作日
				$timeSign = false;
				$startTimeTmp = strtotime(date('Y-m-d', $appStartTime));
				$endTimeTmp = strtotime(date('Y-m-d', $appEndTime));
				$todayTime = strtotime(date('Y-m-d'));

				if ($startTimeTmp < $todayTime ) {
					if ($endTimeTmp >= $todayTime) {
						// 没到
						$appStartTime = $todayTime;
						
					} else {
						// 超时
						$timeSign = true;
						$appStartTime = $endTimeTmp + 86400; // 不算最后一天，加一天
						$appEndTime = $todayTime;
					}
				}

				$workDays = 0;
				$days = intval(($appEndTime - $appStartTime)/86400) + 1;
				if ($days > 0) {
					$currentDay = $appStartTime;
					while($days > 0) {
						// 累加天数
						if (getAttendenceExcept($currentDay)) {
							$workDays++;
						}$currentDay += 86400;
						$days--;
					}
				}
				
				$apply['approval_time_sign'] = $timeSign; // 是否超时 true超时  false没有
				$apply['approval_end_day'] = $workDays;
			}
			
			// 主审部门判断是否已经选过协同部门
			$approvalCoordinateList = [];
			if ($arr['approval_type'] == APPROVAL_TYPE['two']) {
				$approvalCoordinateList = $this->getCoordinate($applyId);
			}
			$apply['approval_coordinate_list'] = $approvalCoordinateList;
			
			/**预审核信息**/
			$resultEco = $apply['result_economy'];
			unset($apply['result_economy']);
			$year = date('Y', strtotime('-1 year'));
			
			// 1、纳税额：比对本地即可
			$whereTax = [
				'enterprise_id' => $apply['enterprise_id'],
				'year' => $year
			];
			$taxList = EnterpriseTaxModel::where($whereTax)
				->get([
					'type',
					'annual_tax'
				])
				->toArray();
			
			// 企业纳税信息预审
			$taxCountry = 0;
			$taxLocation = 0;
			foreach ($taxList as $key => $value) {
				if ($value['type'] == TAX_TYPE['all']) {
					$taxCountry = $value['annual_tax'];
				} else if ($value['type'] == TAX_TYPE['local']) {
					$taxLocation = $value['annual_tax'];
				}
			}
			
			// 企业自填纳税额
			$taxEconomy = 0;
			foreach ($resultEco as $key => $value) {
				if ($value['year'] === $year && $value['type'] === APPLY_ECONOMY_TYPE['seven']) {
					$taxEconomy = $value['content'];
				}
			}
			
			$apply['tax_list'] = [
				'year' => $year,
				'tax_country' => $taxCountry,
				'tax_location' => $taxLocation,
				'tax_economy' => $taxEconomy
			];
			
			// 2、征信信息预审：只查前两年的处罚信息
			$yearOne = date('Y', strtotime('-2 year'));
			$yearTwo = date('Y', strtotime('-1 year'));
			$startTime = strtotime($yearOne. '-01-01');
			$endTime = strtotime($yearTwo . '-12-31');
			
			$whereCredit = [];
			$whereCredit[] = ['f1.enterprise_id', '=', $apply['enterprise_id']];
			$whereCredit[] = ['f1.decision_date', '>=', $startTime];
			$whereCredit[] = ['f1.decision_date', '<=', $endTime];
			$creditListTmp =  (new EnterpriseCreditModel())
				->setTable('f1')
				->from(EnterpriseCreditModel::TABLE_NAME . ' AS f1')
				->leftJoin(CreditDepartmentModel::TABLE_NAME . ' AS f2','f2.id','=','f1.department_id')
				->where($whereCredit)
				->orderBy('f1.decision_date', 'desc')
				->get([
					'f1.decision_date',
					'f2.name AS department_name'
				])
				->toArray();
			
			// 组装数据
			$creditOne = [];
			$creditTwo = [];
			foreach ($creditListTmp as $key => $value) {
				if (date('Y', $value['decision_date']) == $yearOne) {
					$creditOne[] = $apply['enterprise_name'] . $yearOne . '年在温江受到了'.$value['department_name'].'的处罚';
				} else if (date('Y', $value['decision_date']) == $yearTwo) {
					$creditTwo[] = $apply['enterprise_name'] . $yearTwo . '年在温江受到了'.$value['department_name'].'的处罚';
				}
			}
				
			// 交换顺序返回
			$apply['credit_list_one'] = [
				'year' => $yearTwo,
				'list' => $creditTwo
			];
			$apply['credit_list_two'] = [
				'year' => $yearOne,
				'list' => $creditOne
			];
			
			// 企业发票信息预审 - 异常信息
			$where = [];
			$where[] = ['f2.apply_id', '>=', $arr['apply_id']];
			if (!empty($arr['type'])) {
				$where[] = ['f1.type', '=', $arr['type']];
			}

//			$applyFile = ApplyFileExceptionModel::where([
//					'apply_id' => $applyId
//				])
//				->groupBy('type')
//				->get([
//					'type',
//					DB::raw("sum(1) as count")
//				])
//				->toArray();;
            $applyFileException = ApplyFileExceptionModel::where([
                'apply_id' => $applyId,
                'status' => APPLY_EXCEPTION_STATUS['fail']
                ])->count();
			
//			$apply['file_exception_list'] = $applyFile;
			$apply['invoice_exception_num'] = $applyFileException;

            $invoiceFileNum = ApplyFileModel::where([
                'apply_id' => $applyId,
                'file_type' => MATERIALS_TYPE['invoice']
            ])->count();
			$apply['invoice_file_num'] = $invoiceFileNum;

			// 审批配置时间
			$apply['approval_config'] = [
				'config_audit' => $configEndDay = getApprovalConfig(APPROVAL_CONFIG_TYPE['three'], 'config_value'),
				'config_timeout' => $configEndDay = getApprovalConfig(APPROVAL_CONFIG_TYPE['four'], 'config_value'),
			];
			
			// 处理打印申请表, 更新
			if (empty($apply['pdf_url']) && !empty($apply['business_id'])) {
				$pdfUrl = app(PdfRepository::class)->getPdf(['business_id' => $apply['business_id']]);
				
				if (!empty($pdfUrl['data'])) {
					$result = ApplyModel::where([
						'id' => $apply['apply_id']
					])->update([
						'pdf_url' => $pdfUrl['data']
					]);
				
					$apply['pdf_url'] = $pdfUrl['data'];
				}
			}

            if (empty($apply['pdf_url']) && !empty($apply['business_id'])) {
                $pdfUrl = app(PdfRepository::class)->getPdf(['business_id' => $apply['business_id']]);

                if (!empty($pdfUrl['data'])) {
                    $result = ApplyModel::where([
                        'id' => $apply['apply_id']
                    ])->update([
                        'pdf_url' => $pdfUrl['data']
                    ]);

                    $apply['pdf_url'] = $pdfUrl['data'];
                }
            }

			// 处理打包附件
            if (empty($apply['zip_url']) && !empty($apply['zip_business_id'])) {

                $zipUrl = app(ZipService::class)->get($apply['zip_business_id']);

                if (!empty($zipUrl['data']['url'])) {
                    $result = ApplyModel::where([
                        'id' => $apply['apply_id']
                    ])->update([
                        'zip_url' => $zipUrl['data']['url']
                    ]);

                    $apply['zip_url'] = $zipUrl['data']['url'];
                }
            }
		}
	
		// 审批流程
		$approvalList = (new ApprovalModel())
			->setTable('f1')
			->from(ApprovalModel::TABLE_NAME . ' AS f1')
			->leftJoin(ApprovalOpinionModel::TABLE_NAME . ' AS f2','f2.approval_id','=','f1.id')
			->leftJoin(StaffDepartmentModel::TABLE_NAME . ' AS f3','f1.department_id','=','f3.id')
			->where([
				'f1.apply_id' => $applyId
			])
			->orderBy('f1.type', 'asc')
			->get([
				'f1.id AS approval_id', 
				'f1.department_id', 
				'f1.type AS approval_type', 
				'f1.start_time', 
				'f1.audit_time',
				'f2.expert_mark',
				'f2.department_mark',
				'f2.business_id',
				'f2.pdf_url',
				'f3.name AS department_name',
				'f2.created_at AS submit_time',
				'f1.created_at',
				'f1.remark',
			])
			->toArray();
		
		// 查询附件信息
		$approvalIdArr = array_column($approvalList, 'approval_id');
		$approvalFileList = ApprovalFileModel::whereIn('approval_id', $approvalIdArr)
			->get([
				'id AS approval_file_id',
				'approval_id', 
				'file_name', 
				'file_url', 
				'created_at'
			])
			->toArray();

		$typeTwo = []; // 用于数组排序
		$typeTwoIndex = 0; // 记录主审部门的位置
		$typeFourIndex = 0; // 记录指挥部审批的位置
		$approvalId = 0;
        $selectCoordinateTime = 0; //选择协同部门的时间
		foreach ($approvalList as $key => $value) {
			// 计算耗时
			$takeTime = 0;
			if ($value['approval_type'] != APPROVAL_TYPE['one']){
				 $takeTime = getTakeUpTime($value['start_time'], $value['audit_time']);
			}
			$value['take_time'] = $takeTime;
			
			if ($apply['apply_status'] == APPLY_STATUS['four'] && $value['approval_type'] == APPROVAL_TYPE['one']) {
				// 查询不受理原因
				$markList = ApprovalMarkModel::where([
						'approval_id' => $value['approval_id'],
						'type' => APPROVAL_MARK_TYPE['one']
					])
					->limit(1)
					->get([
						'mark'
					])
					->toArray();
				
				$value['approval_mark'] = empty($markList) ? '' : $markList[0]['mark'];
			}

			if ($value['approval_type'] == APPROVAL_TYPE['two']) {
				$typeTwo = $value;
				$typeTwoIndex = $key;
			}
			if ($value['approval_type'] == APPROVAL_TYPE['four']) {
				$typeFourIndex = $key;
			}

            if ($value['approval_type'] == APPROVAL_TYPE['three'] && !$selectCoordinateTime) {
                $approvalList[$typeTwoIndex]['selectCoordinateTime'] = $value['created_at'];
                $selectCoordinateTime = $value['created_at'];
            }
			
			// 处理打印申请表, 更新
			if (empty($value['pdf_url']) && !empty($value['business_id'])) {
				$pdfUrl = app(PdfRepository::class)->getPdf(['business_id' => $value['business_id']]);
			
				if (!empty($pdfUrl['data'])) {
					$result = ApprovalOpinionModel::where([
						'approval_id' => $value['approval_id']
					])->update([
						'pdf_url' => $pdfUrl['data']
					]);
			
					$value['pdf_url'] = $pdfUrl['data'];
				}
			}
			
			// 找出园区办公室的审批
			if ($value['approval_type'] == APPROVAL_TYPE['five']) {
				$approvalId = $value['approval_id'];
				//unset($approvalList[$key]);
			} 
// 			else {
// 				$approvalList[$key] = $value;
// 			}
			
			$fileList = [];
			if (!empty($approvalFileList)) {
				foreach ($approvalFileList as $key2 => $value2) {
					if ($value2['approval_id'] == $value['approval_id']) {
						unset($value2['approval_id']);
						$fileList[] = $value2;
						unset($approvalFileList[$key2]);
					}
				}
			}
			
			$value['file_list'] = $fileList;
			
			$approvalList[$key] = $value;
            // 这里应因为主审部门的value有可能变化，所以要再次赋值
            if ($value['approval_type'] == APPROVAL_TYPE['two']) {
                $typeTwo = $value;
            }
		}
		
		// 改变主审部门的顺序
        $mainDepartment = [];
		if ($typeTwoIndex != 0) {
			// 1、先删除主审部门
            $typeTwo['selectCoordinateTime'] = $selectCoordinateTime;
            $main = app(StaffDepartmentRepository::class)->getOperatorStaff($typeTwo['department_id'], ['id','name']);
            $mainDepartment = $main;
			array_splice($approvalList, $typeTwoIndex, 1);
			// 2、主审部门插入
			if ($typeFourIndex != 0) {
                // 有指挥部插入指挥部之前
				array_splice($approvalList, $typeFourIndex-1, 0, [$typeTwo]);
			} else {
				// 没有指挥部插入末尾即可
				array_push($approvalList, $typeTwo);
			}
		}

		$apply['approval_list'] = $approvalList;
		$apply['main_department'] = (object)$mainDepartment;

		$mark = '';
		if (!empty($approvalId)) {
			// 延时拨款原因
			$markList = ApprovalMarkModel::where([
					'approval_id' => $approvalId,
					'type' => APPROVAL_MARK_TYPE['two']
				])
				->limit(1)
				->get(['mark'])
				->toArray();
			
			$mark = empty($markList) ? '' : $markList[0]['mark'];
		}
		$apply['defer_mark'] = $mark;

		// 处理申报详情附件
        $apply['detail_pdf_url'] = app(ApplyPdfRepository::class)->getPdf($apply);


        return $apply;
	}

	
	/***公共信息***/
	
	
	/**
	 * 操作人员验权
	 */
	public function operatorAuth($arr)
	{
		// 查询操作人员
		$staff = (new StaffDepartmentModel())
			->setTable('f1')
			->from(StaffDepartmentModel::TABLE_NAME . ' AS f1')
			->join(StaffBindDepartmentModel::TABLE_NAME . ' AS f2','f2.department_id','=','f1.id')
			->where([
				'f2.staff_id' => $arr['staff_id'],
				'f2.opertor_type' => STAFF_OPERTOR_TYPE['one']
			])
			->limit(1)
			->get(['f2.department_id', 'f1.name AS approval_department_name'])
			->toArray();

		return empty($staff) ? [] : $staff[0];
	}
	
	/**
	 * 审批验证权限
	 * @param unknown $arr
	 */
	public function approvalAuth($arr) {
		// 审批
		$approval = ApprovalModel::where(['id' => $arr['approval_id']])
			->limit(1)
			->get([
				'apply_id', 
				'department_id', 
				'type AS approval_type', 
				'status AS approval_status',
				'start_time AS approval_start_time', 
				'end_time AS approval_end_time',
				'audit_type AS approval_audit_type'
			])
			->toArray();
		
		if (empty($approval)) {
			return [];
		}
		
		$approval = $approval[0];

		// 申请表信息
		$where = [
			'id' => $approval['apply_id']
		];
		
		$columns = [
			'enterprise_id', 
			'project_id', 
			'policy_name', 
			'project_name', 
			'enterprise_name',
			'apply_status',
			'apply_money'
		];
		
		$apply = ApplyModel::where($where)
			->limit(1)
			->get($columns)
			->toArray();
		
		if (empty($apply)) {
			return [];
		}
		
		$apply = $apply[0];

		return array_merge($approval, $apply);
	}
	
	/**
	 * 新增审批-单个
	 */
	public function storeApproval($arr)
	{
		$currentTime = time();
		$apply = [
			'apply_id' => $arr['apply_id'],
			'department_id' => $arr['department_id'],
			'type' => $arr['approval_type'],
			'start_time' => empty($arr['start_time']) ? $currentTime : $arr['start_time'],
			'end_time' => empty($arr['end_time']) ? $currentTime : $arr['end_time'],
			'status' => APPROVAL_STATUS['one'],
			'created_at' => time(),
			'remark' => empty($arr['remark']) ? '' : $arr['remark']
		];
		
		$result = ApprovalModel::create($apply);
		return $result;
	}
	
	/**
	 * 更新申请表状态
	 */
	public function updateApplyStatus($arr)
	{
		$where = [
			'id' => $arr['apply_id']
		];
		$data = [
			'apply_status' => $arr['apply_status'],
			'audit_time' => time()
		];
		
		$result = ApplyModel::where($where)->update($data);
		return $result;
	}
	
	/*******审批流程操作***********/


	/**
	 * 企业服务部受理
	 */
	public function accept($arr)
	{
		// 查询主审部门操作员
		$staff = (new StaffModel())
			->setTable('f1')
			->from(StaffModel::TABLE_NAME . ' AS f1')
			->join(StaffBindDepartmentModel::TABLE_NAME . ' AS f2','f2.staff_id','=','f1.id')
			->where([
				'f2.department_id' => $arr['department_id'],
				'f2.opertor_type' => STAFF_OPERTOR_TYPE['one']
			])
			->limit(1)
			->orderBy('f1.number', 'asc')
			->get(['f2.staff_id', 'f2.department_id', 'f1.mobile'])
			->toArray();

		if (empty($staff)) {
			return Code::APPROVAL_STAFF_EXIST_ERROR;
		}
		
		$staff = $staff[0];

		// 推送
		$staffList = [];
		$tmpData = [];
		if (!empty($arr['push_list'])) {
			$pushList = $arr['push_list'];
			
			// 查询推送部门人员
			$staffList = (new StaffModel())
				->setTable('f1')
				->from(StaffModel::TABLE_NAME . ' AS f1')
				->join(StaffBindDepartmentModel::TABLE_NAME . ' AS f2','f2.staff_id','=','f1.id')
				->whereIn('f2.department_id', $pushList)
				->orderBy('f1.number', 'asc')
				->get(['f2.staff_id', 'f2.department_id', 'f1.mobile'])
				->toArray();

			// 组织数据
			foreach ($pushList as $key => $value) {
				$tmpData[] = [
					'apply_id' => $arr['apply_id'],
					'department_id' => $value,
					'created_at' => time()
				];
			}
		}

		// 开始和结束时间:计算主审部门的审核时间:主审部门的审核时间计时从接收到企业服务中心受理的申报项目起，两个工作日后系统自动开始计时，
		$configEndDay = getApprovalConfig(APPROVAL_CONFIG_TYPE['two'], 'config_value');
		$arr['config_time'] = $configEndDay;
		$timeArr = getStartEndTimeOne(date('Y-m-d'), APPROVAL_WORK_DAY, $configEndDay);
		$arr['start_time'] = $timeArr['start_time'];
		$arr['end_time'] = $timeArr['end_time'];

		$arr['approval_type'] = APPROVAL_TYPE['two'];

		// 企业信息
		$enterUser = $this->getEnterpriseUser($arr);
		if (empty($enterUser['user_id']) || empty($enterUser['mobile'])) {
			return Code::APPROVAL_ENTER_USER_ERROR;
		}
		
        $opinion = [
            'approval_id' => $arr['approval_id'],
            'expert_mark' => $arr['expert_mark'] ?? '',
            'department_mark' => $arr['department_mark'] ?? '',
            'created_at' => time(),
            'business_id' => businessId()
        ];
		DB::beginTransaction();
	
		try{
			
			// 修改申请表状态
			$this->updateApplyStatus([
				'apply_id' => $arr['apply_id'],
				'apply_status' => APPLY_STATUS['five']
			]);
			
			// 修改审批状态-已处理
			ApprovalModel::where([
				'id' => $arr['approval_id']
			])->update([
				'status' => APPROVAL_STATUS['two'],
				'audit_time' => time()
			]);
			
			// 新增主审部门审批流程
			$resultStore = $this->storeApproval($arr);

			if (!empty($tmpData)) {
				ApprovalPushModel::insert($tmpData);
			}

			// 缓存审批ID
			$approvalId = $arr['approval_id'];
			
			// 站内信
			// 给选择的主审部门发送主审部门审核通知
			$arr['staff_id'] = $staff['staff_id'];
			$arr['approval_id'] = $resultStore['id']; // 更改目标ID
			$userMessage = $this->sendMessage($arr, APPROVAL_MESSAGE_CONTENT['one']);
			
			// 企服中心的受理记录
			$arr['approval_id'] = $approvalId;
			$arr['user_message_id'] = $userMessage['user_message_id'];
			app(ApprovalAcceptRepository::class)->store($arr);

			// 意见
            ApprovalOpinionModel::insert($opinion);

			// 更改目标ID
			$arr['approval_id'] = $arr['apply_id'];
			// 给选中的其他部门发送申报消息通知
			foreach ($staffList as $key => $value) {
				// 排除主审部门的操作人员
				if ($value['staff_id'] == $staff['staff_id']) continue;
				
				$arr['staff_id'] = $value['staff_id'];
				$this->sendMessage($arr, APPROVAL_MESSAGE_CONTENT['two']);
			}
			// 给企业发送申报审核消息通知
			$arr['user_id'] = $enterUser['user_id'];
			$this->sendMessage($arr, APPROVAL_MESSAGE_CONTENT['three']);

			DB::commit();

		}catch (Exception $e){
			Log::error('approval accept' . $e->getMessage());
			DB::rollBack();
			return false;
		}
		
		/** 短信 **/
		// 给选择的主审部门发送主审部门审核通知
		$arr['mobile'] = $staff['mobile'];
		$this->sendSms($arr, SMS_TEMPLATE['one']);
		
		// 给选中的其他部门发送申报消息通知
		foreach ($staffList as $key => $value) {
			// 排除主审部门的操作人员
			if ($value['staff_id'] == $staff['staff_id']) continue;
			
			$arr['mobile'] = $value['mobile'];
			$this->sendSms($arr, SMS_TEMPLATE['two']);
		}
		// 给企业发送申报审核消息通知
		$arr['mobile'] = $enterUser['mobile'];
		$this->sendSms($arr, SMS_TEMPLATE['three']);

        // pdf调用
        $applyObj = app(ApplyRepository::class)->detailApply(['id' => $arr['apply_id']]);
        $applyObj['expert_mark'] = $opinion['expert_mark'];
        $applyObj['department_mark'] = $opinion['department_mark'];
        $applyObj['business_id'] = $opinion['business_id'];
        app(PdfRepository::class)->createApprovalPdf($applyObj, true);

        $detail = $this->detail(['id' => $arr['apply_id']]);
        app(ApplyPdfRepository::class)->pdfCreate($detail);

        return true;
	}
	
	/**
	 * 选择协同审核部门
	 */
	public function coordinate($arr)
	{
		$departmentList = $arr['department_list'];

		$departmentIdArr = array_column($departmentList, 'department_id');
        $staffListTmp = $this->checkDepartmentStaff($departmentIdArr);
		$staffList = [];
		foreach ($staffListTmp as $key => $value) {
			$staffList[$value['department_id']] = $value;
		}

		$log = [
		    'apply_id' => $arr['apply_id'],
		    'approval_id' => $arr['approval_id'],
		    CREATED_STAFF_ID => (int)getLoginStaff('id'),
        ];


		DB::beginTransaction();
	
		try{
            //  将受理审批记录改为已读
            app(ApprovalAcceptRepository::class)->updateRead($arr['approval_id']);

            $logRes = app(ApprovalCoordinateLogRepository::class)->storeRepository($log);

            $coorRelation = [];
            $time = time();
			foreach ($departmentList as $key => $value) {
				$tmpDepartmentId = $value['department_id'];
				// 排除主审部门、排除没有人的部门
				if ($tmpDepartmentId == $arr['approval_department_id'] 
					|| empty($staffList[$tmpDepartmentId])) {
					continue;
				}
				
				$arr['department_id'] = $tmpDepartmentId;
				$arr['remark'] = array_get($value, 'remark', '');
				$resultStore = $this->storeApproval($arr);
                $coorRelation[] = [
                     'approval_id' => $resultStore['id'],
                     'log_id' => $logRes['id'],
                     'created_at' => $time,
                     'updated_at' => $time
                ];
					
				// 需要给选中的协同部门发送协同部门评审通知
				$tmpObj = $staffList[$tmpDepartmentId];
				$arr['staff_id'] = $tmpObj['staff_id'];
				$arr['department_name'] = $tmpObj['department_name'];
				$arr['approval_id'] = $resultStore['id'];
				$this->sendMessage($arr, APPROVAL_MESSAGE_CONTENT['five']);
			}

			if (!empty($coorRelation)) {
                app(ApprovalCoordinateRelationRepository::class)->storeBatchRepository($coorRelation);
            }

			DB::commit();
	
		}catch (Exception $e){
			Log::error('approval coordinate' . $e->getMessage());
			DB::rollBack();
			return false;
		}
		
		/** 短信 **/
		// 需要给选中的协同部门发送协同部门评审通知
		foreach ($departmentList as $key => $value) {
			if (empty($value['department_id']) || 
				empty($staffList[$value['department_id']])) {
				continue;
			}
			$tmpObj = $staffList[$value['department_id']];
			$arr['mobile'] = $tmpObj['mobile'];
            $arr['department_name'] = $tmpObj['department_name'];
			$this->sendSms($arr, SMS_TEMPLATE['five']);
		}

        $detail = $this->detail(['id' => $arr['apply_id']]);
        app(ApplyPdfRepository::class)->pdfCreate($detail);
		
		return true;
	}

	
	/**
	 * 审批理由和补充资料表
	 * type:1企业服务不受理2园区办公室延时拨款3主审部门补充资料4协同部门补充资料
	 */
	public function mark($arr)
	{
		$messageType = 0;
		$smsType = 0;
		$applyStatusNext = 0;
	
		$type = $arr['type'];
		$opinion = [];
		if ($type == APPROVAL_MARK_TYPE['one']) {
			// 企业服务不受理
			$messageType = APPROVAL_MESSAGE_CONTENT['four'];
			$smsType = SMS_TEMPLATE['four'];
			$applyStatusNext = APPLY_STATUS['four'];
            $opinion = [
                'approval_id' => $arr['approval_id'],
                'department_mark' => $arr['mark'] ?? '',
                'created_at' => time(),
                'business_id' => businessId()
            ];
		
		} else if ($type == APPROVAL_MARK_TYPE['two']) {
			// 园区办公室延时拨款-只能填写一次
			$mark = ApprovalMarkModel::where([
					'approval_id' => $arr['approval_id'],
					'type' => APPROVAL_MARK_TYPE['two']
				])
				->limit(1)
				->get(['id'])
				->toArray();
				
			if (!empty($mark)) {
				return Code::APPROVAL_MARK_REPEAT_ERROR;
			}

			$messageType = APPROVAL_MESSAGE_CONTENT['twenty'];
			$smsType = SMS_TEMPLATE['sixteen'];

		} else if ($type == APPROVAL_MARK_TYPE['three']) {
			// 主审部门补充资料 - 系统给企业发送申报审核消息通知
			$arr['start_time'] = strtotime(date('Y-m-d'));
			$arr['end_time'] = $arr['approval_end_time'];
			
			$messageType = APPROVAL_MESSAGE_CONTENT['six'];
			$smsType = SMS_TEMPLATE['six'];
			
		} else if ($type == APPROVAL_MARK_TYPE['four']) {
			// 协同部门补充资料-系统给企业发送申报审核消息通知
			$arr['start_time'] = strtotime(date('Y-m-d'));
			$arr['end_time'] = $arr['approval_end_time'];
			
			$messageType = APPROVAL_MESSAGE_CONTENT['seven'];
			$smsType = SMS_TEMPLATE['six'];
		}
		
		// 企业信息
		$enterUser = $this->getEnterpriseUser($arr);
		if (empty($enterUser['user_id']) || empty($enterUser['mobile'])) {
			return Code::APPROVAL_ENTER_USER_ERROR;
		}
		
		$mark = [
			'approval_id' => $arr['approval_id'],
			'mark' => $arr['mark'],
			'type' => $type,
			'created_at' => time()
		];

		DB::beginTransaction();
		
		try{
			$res = ApprovalMarkModel::insert($mark);

            if ($type == APPROVAL_MARK_TYPE['one'] && array_get($arr, 'refresh', 0) == MARK_REFRESH) {
                // 不受理 且不存档时 需要重新预检
                app(ApplyFileRepository::class)->refreshInvoice($arr['apply_id']);
                app(ApplyFileExceptionRepository::class)->refreshApply($arr['apply_id']);
            }

			// 修改申请表状态
			if ($applyStatusNext != 0) {
				$this->updateApplyStatus([
					'apply_id' => $arr['apply_id'],
					'apply_status' => $applyStatusNext
				]);
			}
			
			// 修改审批状态-已处理
			if ($applyStatusNext == APPLY_STATUS['four']) {
				ApprovalModel::where([
					'id' => $arr['approval_id']
				])->update([
					'status' => APPROVAL_STATUS['two'],
					'audit_time' => time()
				]);
			}

			if (!empty($opinion) && $type == APPROVAL_MARK_TYPE['one']) {
                // 意见 企业服务中心不受理
                ApprovalOpinionModel::insert($opinion);
            }

			// 站内信
			$arr['user_id'] = $enterUser['user_id'];
			$message = $this->sendMessage($arr, $messageType);
			
			// 记录补充资料的发送记录-用于24小时再发一次
			if (!empty($message) && 
				($type == APPROVAL_MARK_TYPE['three'] || $type == APPROVAL_MARK_TYPE['four'])) {
				$materialArr = [
					'apply_id' => $arr['apply_id'],
					'approval_id' => $arr['approval_id'],
					'enterprise_id' => $arr['enterprise_id'],
					'user_id' => $message['user_id'],
					'mark' => $arr['mark'],
					'status' => MATERIAL_SEND_STATUS['one'],
					'created_at' => time(),
					CREATED_STAFF_ID => (int)getLoginStaff('id'),
					'start_time' => $arr['start_time'],
					'end_time' => $arr['end_time']
				];
				ApprovalMaterialModel::insert($materialArr);
			}
	
			DB::commit();

		}catch (Exception $e){
			Log::error('apply mark' . $e->getMessage());
			DB::rollBack();
			return false;
		}

		/** 短信 **/
		// 发给企业
		$arr['mobile'] = $enterUser['mobile'];
		$this->sendSms($arr, $smsType);

		if (!empty($opinion) && $type == APPROVAL_MARK_TYPE['one']) {
		    // 企业服务中心 审批不过需要调用pdf
            // pdf调用
            $applyObj = app(ApplyRepository::class)->detailApply(['id' => $arr['apply_id']]);
            $applyObj['expert_mark'] = $opinion['expert_mark']??'';
            $applyObj['department_mark'] = $opinion['department_mark']??'';
            $applyObj['business_id'] = $opinion['business_id'];
            app(PdfRepository::class)->createApprovalPdf($applyObj, true);
            app(ApplyPdfRepository::class)->pdfCreate($applyObj);
            $detail = $this->detail(['id' => $arr['apply_id']]);
            app(ApplyPdfRepository::class)->pdfCreate($detail);

        }
        return true;
	}
	
	
	/**
	 * 主审部门-审计操作
	 * audit_type: 1需要审计参与  2 延长审核时间
	 */
	public function audit($arr)
	{
		// 审计类主审部门
		$configEndDay = 0;
		if ($arr['audit_type'] == APPROVAL_AUDIT_TYPE['YES']) {
			$configEndDay = getApprovalConfig(APPROVAL_CONFIG_TYPE['three'], 'config_value');
		}

		// 审计类延长时间
		if ($arr['audit_type'] == APPROVAL_AUDIT_TYPE['TIMEOUT']) {
			if ($arr['approval_audit_type'] == APPROVAL_AUDIT_TYPE['NO']) {
				$configEndDay += getApprovalConfig(APPROVAL_CONFIG_TYPE['two'], 'config_value');
			} else if ($arr['approval_audit_type'] == APPROVAL_AUDIT_TYPE['YES']) {
				$configEndDay += getApprovalConfig(APPROVAL_CONFIG_TYPE['three'], 'config_value');
			}

			$configEndDay += getApprovalConfig(APPROVAL_CONFIG_TYPE['four'], 'config_value');
		}

		$timeArr = getStartEndTimeOne(date('Y-m-d', $arr['approval_start_time']), 0, $configEndDay);

		try{

			ApprovalModel::where([
				'id' => $arr['approval_id']
			])->update([
				'end_time' => $timeArr['end_time'],
				'audit_type' => $arr['audit_type']
			]);

			return true;

		}catch (Exception $e){
			Log::error('approval audit' . $e->getMessage());
			return false;
		}
	}
	
	/**
	 * 审批意见
	 * opinion_type 
	 * 1协同部门提交意见 
	 * 2主审部门审核通过意见、线下会审通过意见、指挥部审核通过提交意见
	 * 3主审部门审核不通过意见 、线下会审不通过意见、指挥部审核不通过提交意见
	 * 4主审部门提交指挥部填写意见
	 */
	public function opinion($arr)
	{
		// 审批下一步状态
		$applyStatusNext = 0;
		// 消息类型
		$messageTypArr = [];
		$smsType = [];
		
		$optionType = $arr['opinion_type'];
		if ($optionType == APPROVAL_OPTION_TYPE['one']) {
			// 协同部门提交意见 - 查询主审部门
			$approval = ApprovalModel::where([
					'apply_id' => $arr['apply_id'],
					'type' => APPROVAL_TYPE['two']
				])
				->limit(1)
				->get(['department_id', 'id'])
				->toArray();
			
			if (empty($approval)) {
				return Code::APPROVAL_STAFF_EXIST_ERROR;
			}
			
			// 查询操作人员
			$staff = (new StaffModel())
				->setTable('f1')
				->from(StaffModel::TABLE_NAME . ' AS f1')
				->join(StaffBindDepartmentModel::TABLE_NAME . ' AS f2','f2.staff_id','=','f1.id')
				->where([
					'f2.opertor_type' => STAFF_OPERTOR_TYPE['one'],
					'f2.department_id' => $approval[0]['department_id']
					
				])
				->limit(1)
				->get(['f2.staff_id', 'f2.department_id', 'f1.mobile'])
				->toArray();
			
			if (empty($staff)) {
				return Code::APPROVAL_STAFF_EXIST_ERROR;
			}
			
			$staff = $staff[0];
			$arr['staff_id'] = $staff['staff_id'];
			$arr['department_id'] = $staff['department_id'];
			$arr['mobile'] = $staff['mobile'];
			$arr['department_name'] = $arr['approval_department_name'];
			$arr['approval_id_two'] = $approval[0]['id'];
			
			$messageTypArr = [APPROVAL_MESSAGE_CONTENT['eight']];
			$smsType = [SMS_TEMPLATE['seven']];

		} else if ($optionType == APPROVAL_OPTION_TYPE['two']) {
			// 主审部门审核通过意见、线下会审通过意见、指挥部审核通过提交意见
			
			// 查询园区办公室操作人员
// 			$staff = app(ApprovalDepartmentRepository::class)->getStaff(DEPARTMENT_TYPE['five']);
			$staff = app(ApprovalDepartmentRepository::class)->getStaff2($arr);

			if (empty($staff)) {
				return Code::APPROVAL_STAFF_EXIST_ERROR;
			}
			
			$arr['staff_id'] = $staff['staff_id'];
			$arr['department_id'] = $staff['department_id'];
			$arr['mobile'] = $staff['mobile'];
				
			// 开始和结束时间:计算园区办公室的任务时间
			$configEndDay = getApprovalConfig(APPROVAL_CONFIG_TYPE['one'], 'config_value');
			$arr['config_time'] = $configEndDay;
			$timeArr = getStartEndTimeOne(date('Y-m-d'), 0, $configEndDay);
			$arr['start_time'] = $timeArr['start_time'];
			$arr['end_time'] = $timeArr['end_time'];
			
			// 待拨款
			$arr['approval_type'] = APPROVAL_TYPE['five'];
			$applyStatusNext = APPLY_STATUS['eight'];

			$applyStatus = $arr['apply_status'];
			// 主审部门审核通过意、
			if ($applyStatus == APPLY_STATUS['five']) {
				$messageTypArr = [
					APPROVAL_MESSAGE_CONTENT['nine'], 
					APPROVAL_MESSAGE_CONTENT['ten']
				];
				
				$smsType = [
					SMS_TEMPLATE['eight'],
					SMS_TEMPLATE['nine']
				];
			} else if ($applyStatus == APPLY_STATUS['six']) {
				$messageTypArr = [
					APPROVAL_MESSAGE_CONTENT['eleven'],
					APPROVAL_MESSAGE_CONTENT['twelve']
				];
				
				$smsType = [
					SMS_TEMPLATE['eight'],
					SMS_TEMPLATE['nine']
				];
			} else {
				// 待指挥部审核 可操作
				$messageTypArr = [
					APPROVAL_MESSAGE_CONTENT['thirteen'],
					APPROVAL_MESSAGE_CONTENT['fourteen']
				];
				
				$smsType = [
					SMS_TEMPLATE['ten'],
					SMS_TEMPLATE['eleven']
				];
			}
		} else if ($optionType == APPROVAL_OPTION_TYPE['three']) {
			// 状态值判断
			$applyStatus = $arr['apply_status'];
			if ($applyStatus == APPLY_STATUS['five']) {
				// 主审部门不通过
				$applyStatusNext = APPLY_STATUS['ten'];
				$messageTypArr = [APPROVAL_MESSAGE_CONTENT['seventeen']];
				
				$smsType = [SMS_TEMPLATE['fourteen']];
			} else if ($applyStatus == APPLY_STATUS['six']) {
				// 线下会审不通过
				$applyStatusNext = APPLY_STATUS['eleven'];
				$messageTypArr = [APPROVAL_MESSAGE_CONTENT['eighteen']];
				
				$smsType = [SMS_TEMPLATE['fourteen']];
			} else if ($applyStatus == APPLY_STATUS['seven']) {
				// 指挥部审核不通过
				$applyStatusNext = APPLY_STATUS['twelve'];
				$messageTypArr = [APPROVAL_MESSAGE_CONTENT['nineteen']];
				
				$smsType = [SMS_TEMPLATE['fifteen']];
			}
		} else {// 主审部门提交指挥部填写意见
			// 查询指挥部操作人员
			$staff = app(ApprovalDepartmentRepository::class)->getStaff3($arr);
			if (empty($staff)) {
				return Code::APPROVAL_STAFF_EXIST_ERROR;
			}
			$arr['staff_id'] = $staff['staff_id'];
			$arr['department_id'] = $staff['department_id'];
			$arr['mobile'] = $staff['mobile'];

			// 待指挥部审核
			$arr['approval_type'] = APPROVAL_TYPE['four'];
			$applyStatusNext = APPLY_STATUS['seven'];
			
			$messageTypArr = [
				APPROVAL_MESSAGE_CONTENT['fifteen'],
				APPROVAL_MESSAGE_CONTENT['sixteen']
			];
			
			$smsType = [
				SMS_TEMPLATE['twelve'],
				SMS_TEMPLATE['thirteen']
			];
		}
		
		// 企业信息
		$enterUser = [];
		if ($applyStatusNext != 0) {
			$enterUser = $this->getEnterpriseUser($arr);
			if (empty($enterUser['user_id']) || empty($enterUser['mobile'])) {
				return Code::APPROVAL_ENTER_USER_ERROR;
			}
			$arr['user_id'] = $enterUser['user_id'];
		}
		
		// 发送企业信息的数组
		$enterpriseTypeArr = [
			SMS_TEMPLATE['nine'],
			SMS_TEMPLATE['eleven'],
			SMS_TEMPLATE['fourteen'],
			SMS_TEMPLATE['fifteen'],
			SMS_TEMPLATE['thirteen']
		];

		$opinion = [
			'approval_id' => $arr['approval_id'],
			'expert_mark' => $arr['expert_mark'] ?? '',
			'department_mark' => $arr['department_mark'] ?? '',
// 			'file_url' => '',
// 			'file_name' => $arr['file_name'] ?? '',
			'created_at' => time(),
			'business_id' => businessId()
		];

		DB::beginTransaction();

		try{
			ApprovalOpinionModel::insert($opinion);
			
			// 修改审批状态-已处理
			ApprovalModel::where([
				'id' => $arr['approval_id']
			])->update([
				'status' => APPROVAL_STATUS['two'],
				'audit_time' => time()
			]);
			// 主审部门操作时
			if (APPLY_STATUS['five'] == $arr['apply_status']) {
                //  将受理审批记录改为已读
                app(ApprovalAcceptRepository::class)->updateRead($arr['approval_id']);
            }
			
			// 修改申请表状态
			if ($applyStatusNext != 0) {
				$this->updateApplyStatus([
					'apply_id' => $arr['apply_id'],
					'apply_status' => $applyStatusNext
				]);
			}

			// 新增审批流
			if ($applyStatusNext == APPLY_STATUS['eight'] || $applyStatusNext == APPLY_STATUS['seven']) {
				$resultStore = $this->storeApproval($arr);
				$arr['approval_id'] = $resultStore['id'];
			}

			// 站内信
			foreach ($messageTypArr as $key => $value) {
				// 置为主审部门ID
				if ($value == APPROVAL_MESSAGE_CONTENT['eight']) {
					$arr['approval_id'] = $arr['approval_id_two'];
				}
				$this->sendMessage($arr, $value);
			}

			DB::commit();
			
		}catch (Exception $e){
			Log::error('approval opinion' . $e->getMessage());
			DB::rollBack();
			return false;
		}
		
		
		/** 短信 **/
		$staffMobile = $arr['mobile'] ?? '';
		foreach ($smsType as $key => $value) {
			if (in_array($value, $enterpriseTypeArr)) {
				$arr['mobile'] = $enterUser['mobile'];
			} else {
				$arr['mobile'] = $staffMobile;
			}
			
			$this->sendSms($arr, $value);
		}
		
		// pdf调用
		$applyObj = app(ApplyRepository::class)->detailApply(['id' => $arr['apply_id']]);
		$applyObj['expert_mark'] = $opinion['expert_mark'];
		$applyObj['department_mark'] = $opinion['department_mark'];
		$applyObj['business_id'] = $opinion['business_id'];
		app(PdfRepository::class)->createApprovalPdf($applyObj, true);

        $detail = $this->detail(['id' => $arr['apply_id']]);
        app(ApplyPdfRepository::class)->pdfCreate($detail);
		return true;
	}

	/**
	 * 主审部门-需要线下会审
	 */
	public function offline($arr)
	{
		try{
			
			// 1、修改申请表状态 - 待拨款
			$this->updateApplyStatus([
				'apply_id' => $arr['apply_id'],
				'apply_status' => APPLY_STATUS['six']
			]);
			return true;
	
		}catch (Exception $e){
			Log::error('approval offline' . $e->getMessage());
			return false;
		}
	}
	
	/**
	 * 填写拨款反馈
	 */
	public function feedback($arr)
	{
		// 查询主审部门信息
		$approval = ApprovalModel::where([
				'apply_id' => $arr['apply_id'],
				'type' => APPROVAL_TYPE['two']
			])
			->limit(1)
			->get(['department_id'])
			->toArray();
			
		if (empty($approval)) {
			return Code::APPROVAL_STAFF_EXIST_ERROR;
		}
			
		// 查询操作人员
		$staff = (new StaffModel())
			->setTable('f1')
			->from(StaffModel::TABLE_NAME . ' AS f1')
			->join(StaffBindDepartmentModel::TABLE_NAME . ' AS f2','f2.staff_id','=','f1.id')
			->join(StaffDepartmentModel::TABLE_NAME . ' AS f3','f3.id','=','f2.department_id')
			->where([
				'f2.opertor_type' => STAFF_OPERTOR_TYPE['one'],
				'f2.department_id' => $approval[0]['department_id']
			])
			->limit(1)
			->get(['f2.staff_id', 'f2.department_id', 'f1.mobile', 'f1.name AS staff_name', 'f3.name AS department_name', 'f3.phone'])
			->toArray();
			
		if (empty($staff)) {
			return Code::APPROVAL_STAFF_EXIST_ERROR;
		}
			
		$staff = $staff[0];
		
		$project_name = $arr['project_name'] ?? '';
		$enterprise_name = $arr['enterprise_name'] ?? '';
		// 获得资金支持应该是审核反馈后的金额
		$apply_money = $arr['support_content'] ?? 0;
		$department_name = $staff['department_name'] ?? '';
		$staff_name = $staff['staff_name'] ?? '';
		$mobile = $staff['phone'] ?? '';
		$name = '关于'.$enterprise_name.'企业在'.$project_name.'项目获得资金支持公示';
		$content = $enterprise_name.'在'.$project_name.'项目中，获得资金支持'.$apply_money.'万元。现在予以公示，七天内有意见请致电'.$department_name.'局，
		联系人：'.$staff_name.'，联系电话：' . $mobile . '。';
		
		// 企业信息
		$enterUser = $this->getEnterpriseUser($arr);
		if (empty($enterUser['user_id']) || empty($enterUser['mobile'])) {
			return Code::APPROVAL_ENTER_USER_ERROR;
		}
		
		DB::beginTransaction();
		
		try{
			
			ApplyModel::where([
				'id' => $arr['apply_id']
			])->update([
				'apply_status' => APPLY_STATUS['nine'],
				'audit_time' => time(),
				'support_content' => $arr['support_content'],
				'allocation_time' => $arr['allocation_time']
			]);
				
			// 修改审批状态-已处理
			ApprovalModel::where([
				'id' => $arr['approval_id']
			])->update([
				'status' => APPROVAL_STATUS['two'],
				'audit_time' => time()
			]);

			// 站内信
			$arr['user_id'] = $enterUser['user_id'];
			$this->sendMessage($arr, APPROVAL_MESSAGE_CONTENT['twentyone']);
			
			// 添加公告
			app(PolicyService::class)->storeApproval([
				'obj_type' => OBJ_TYPE['approval'],
				'name' => $name,
				'content' => $content,
				'publish_status' => PUBLISH_STATUS['yes'],
				'target_id' => $arr['approval_id'],
				'publish_status' =>  PUBLISH_STATUS['yes']
			]);
				
			DB::commit();

		}catch (Exception $e){
			Log::error('apply feedback' . $e->getMessage());
			DB::rollBack();
			return false;
		}
		
		/** 短信 **/
		$arr['mobile'] = $enterUser['mobile'];
		$this->sendSms($arr, SMS_TEMPLATE['seventeen']);

        $detail = $this->detail(['id' => $arr['apply_id']]);
        app(ApplyPdfRepository::class)->pdfCreate($detail);
		return true;
		
	}
	
	/**
	 * 站内信和短信内容
	 */
	public function getContent($arr, $type)
	{
		$policy_name = $arr['policy_name'] ?? '';
		$project_name = $arr['project_name'] ?? '';
		$enterprise_name = $arr['enterprise_name'] ?? '';
		$department_name = $arr['department_name'] ?? '';

		$content = '';
		$sourceType = 0;
		switch ($type)
		{
			case APPROVAL_MESSAGE_CONTENT['one']:
				$department_name = $arr['department_name'] ?? '';
				$time = $arr['config_time'];
				$content = '尊敬的用户，在关于'.$policy_name.'政策类型的'.$project_name.'项目的申报中，'.$department_name.'部门为主审部门，部门审核时间为'.$time.'个工作日，请尽快去审核。';
				$sourceType = USER_MESSAGE_SOURCE['three'];
				break;
			case APPROVAL_MESSAGE_CONTENT['two']:
				$content = '尊敬的用户，'.$enterprise_name.'企业在关于'.$policy_name.'政策类型的'.$project_name.'项目的申报，已经被区企业服务中心受理，点击即可查看申报详情。';
				$sourceType = USER_MESSAGE_SOURCE['eight'];
				break;
			case APPROVAL_MESSAGE_CONTENT['three']:
				$content = '尊敬的用户，在关于'.$policy_name.'政策类型的'.$project_name.'项目的申报中，您的申报已经被企业服务中心受理。';
				$sourceType = USER_MESSAGE_SOURCE['eleven'];
				break;
			case APPROVAL_MESSAGE_CONTENT['four']:
				$mark = $arr['mark'] ?? '';
				$content = '尊敬的用户，在关于'.$policy_name.'政策类型的'.$project_name.'项目的申报中，您的申报经区企业服务中心处理后，结果为不受理，按要求修改后可以再次提交申报。具体原因：' . $mark;
				$sourceType = USER_MESSAGE_SOURCE['eleven'];
				break;
			case APPROVAL_MESSAGE_CONTENT['five']:
				$department_name = $arr['department_name'] ?? '';
				$startTime = date('Y-m-d', $arr['start_time']);
				$endTime = date('Y-m-d', $arr['end_time']);
				$content = '尊敬的用户，在关于'.$policy_name.'政策类型的'.$project_name.'项目的申报中，'.$department_name.'部门为协同部门，部门评审时间为'.$startTime.'到'.$endTime.'，请在审核时间内提交部门评审意见。';
				$sourceType = USER_MESSAGE_SOURCE['four'];
				break;
			case APPROVAL_MESSAGE_CONTENT['six']:
				// 内容中的时间段：点击确定的时间为开始时间，部门审核截止时间为终止时间。
				$startTime = date('Y-m-d', $arr['start_time']);
				$endTime = date('Y-m-d', $arr['end_time']);
				$mark = $arr['mark'] ?? '';
				$content = '尊敬的用户，在关于'.$policy_name.'政策类型的'.$project_name.'项目的申报中，你提交的申报资料不齐全，请在'.$startTime.'~'.$endTime.'时间段内把资料补充完整，如果逾期未补充相关资料将会影响本次的项目申报。具体内容：'.$mark.'。同时企业在个人中心-我的申报中可以进行补充资料操作。';
				$sourceType = USER_MESSAGE_SOURCE['eleven'];
				break;
			case APPROVAL_MESSAGE_CONTENT['seven']:
				// 内容中的时间段：点击确定的时间为开始时间，部门审核截止时间为终止时间。
				$startTime = date('Y-m-d', $arr['start_time']);
				$endTime = date('Y-m-d', $arr['end_time']);
				$mark = $arr['mark'] ?? '';
				$content = '尊敬的用户，在关于'.$policy_name.'政策类型的'.$project_name.'项目的申报中，你提交的申报资料不齐全，请在'.$startTime.'~'.$endTime.'时间段内把资料补充完整，如果逾期未补充相关资料将会影响本次的项目申报。具体内容：'.$mark.'。同时企业在个人中心-我的申报中可以进行补充资料操作。';
				$sourceType = USER_MESSAGE_SOURCE['eleven'];
				break;
			case APPROVAL_MESSAGE_CONTENT['eight']:
				$department_name = $arr['department_name'] ?? '';
				$content = '尊敬的用户，在关于'.$policy_name.'政策类型的'.$project_name.'项目的申报中，协同部门'.$department_name.'部门已经提交部门评审意见，请及时查看。';
				$sourceType = USER_MESSAGE_SOURCE['nine'];
				break;
			case APPROVAL_MESSAGE_CONTENT['nine']:
				$time = $arr['config_time'];
				$content = '尊敬的用户，在关于'.$policy_name.'政策类型的'.$project_name.'项目已经通过审核，请管委会办公室在'.$time.'个工作日内进行拨款并在系统上提交拨款反馈。';
				$sourceType = USER_MESSAGE_SOURCE['six'];
				break;
			case APPROVAL_MESSAGE_CONTENT['ten']:
				$content = '尊敬的用户，在关于'.$policy_name.'政策类型的'.$project_name.'项目的申报中已经通过部门审核。请耐心等候工作人员联系你进行项目拨款。';
				$sourceType = USER_MESSAGE_SOURCE['eleven'];
				break;
			case APPROVAL_MESSAGE_CONTENT['eleven']:
				$time = $arr['config_time'];
				$content = '尊敬的用户，在关于'.$policy_name.'政策类型的'.$project_name.'项目已经通过审核，请管委会办公室在'.$time.'个工作日内进行拨款并在系统上提交拨款反馈。';
				$sourceType = USER_MESSAGE_SOURCE['six'];
				break;
			case APPROVAL_MESSAGE_CONTENT['twelve']:
				$time = $arr['config_time'];
				$content = '尊敬的用户，在关于'.$policy_name.'政策类型的'.$project_name.'项目的申报中已经通过部门审核。请耐心等候工作人员联系你进行项目拨款。';
				$sourceType = USER_MESSAGE_SOURCE['eleven'];
				break;
			case APPROVAL_MESSAGE_CONTENT['thirteen']:
				$time = $arr['config_time'];
				$content = '尊敬的用户，在关于'.$policy_name.'政策类型的'.$project_name.'项目已经通过指挥部决策。请管委会办公室在'.$time.'个工作日内进行拨款并在系统上提交拨款反馈。';
				$sourceType = USER_MESSAGE_SOURCE['six'];
				break;
			case APPROVAL_MESSAGE_CONTENT['fourteen']:
				$content = '尊敬的用户，在关于'.$policy_name.'政策类型的'.$project_name.'项目的申报中已经通过指挥部审核。请耐心等候工作人员联系你进行项目拨款。';
				$sourceType = USER_MESSAGE_SOURCE['eleven'];
				break;
			case APPROVAL_MESSAGE_CONTENT['fifteen']:
				$content = '尊敬的用户，在关于'.$policy_name.'政策类型的'.$project_name.'项目的申报中存在异议。现提交到指挥部进行审议，请指挥部尽快登录系统提交决策意见。';
				$sourceType = USER_MESSAGE_SOURCE['five'];
				break;
			case APPROVAL_MESSAGE_CONTENT['sixteen']:
				$content = '尊敬的用户，在关于'.$policy_name.'政策类型的'.$project_name.'项目的申报中已经通过，因在部门审核中有异议，现已提交指挥部审核。';
				$sourceType = USER_MESSAGE_SOURCE['eleven'];
				break;
			case APPROVAL_MESSAGE_CONTENT['seventeen']:
				$content = '尊敬的用户，在关于'.$policy_name.'政策类型的'.$project_name.'项目的申报中未通过部门审核。具体未通过原因请到个人中心我的申报处查看部门审核意见。';
				$sourceType = USER_MESSAGE_SOURCE['eleven'];
				break;
			case APPROVAL_MESSAGE_CONTENT['eighteen']:
				$content = '尊敬的用户，在关于'.$policy_name.'政策类型的'.$project_name.'项目的申报中未通过部门审核。具体未通过原因请到个人中心我的申报处查看部门审核意见。';
				$sourceType = USER_MESSAGE_SOURCE['eleven'];
				break;
			case APPROVAL_MESSAGE_CONTENT['nineteen']:
				$content = '尊敬的用户，在关于'.$policy_name.'政策类型的'.$project_name.'项目的申报中未通过指挥部审核。具体未通过原因请到个人中心我的申报处查看部门审核意见。';
				$sourceType = USER_MESSAGE_SOURCE['eleven'];
				break;
			case APPROVAL_MESSAGE_CONTENT['twenty']:
				$mark = $arr['mark'] ?? '';
				$content = '尊敬的用户，在关于'.$policy_name.'政策类型的'.$project_name.'项目的申报中，因为'.$mark.'原因，所以导致了延时拨款。';
				$sourceType = USER_MESSAGE_SOURCE['eleven'];
				break;
			case APPROVAL_MESSAGE_CONTENT['twentyone']:
				$content = '尊敬的用户，在关于'.$policy_name.'政策类型的'.$project_name.'项目的申报中。已经进行拨款并且在系统公示公告处进行公示。';
				$sourceType = USER_MESSAGE_SOURCE['twelve'];
				break;
			case APPROVAL_MESSAGE_CONTENT['twentytwo']:
				$content = '尊敬的用户，有企业提交了关于'.$policy_name.'政策类型的'.$project_name.'项目的申报，请区企业服务中心尽快受理 。';
				$sourceType = USER_MESSAGE_SOURCE['two'];
				break;
			case APPROVAL_MESSAGE_CONTENT['twentythree']:
				$time = $arr['time'];
				$content = '尊敬的用户，本部门在关于'.$policy_name.'政策类型的'.$project_name.'项目的申报中，距离本部门审核截止日期还剩'.$time.'天了，请尽快去审核。';
				$sourceType = USER_MESSAGE_SOURCE['seven'];
				break;
			case APPROVAL_MESSAGE_CONTENT['twentyfour']:
				$time = $arr['time'];
				$content = '尊敬的用户，本部门在关于'.$policy_name.'政策类型的'.$project_name.'项目的申报中，距离本部门审核截止日期还剩'.$time.'天了，请尽快去审核。';
				$sourceType = USER_MESSAGE_SOURCE['seven'];
				break;
			case APPROVAL_MESSAGE_CONTENT['twentyfive']:
				$content = '尊敬的用户，在'.$enterprise_name.'企业提交的'.$policy_name.'政策类型的'.$project_name.'项目中，企业已经按要求在系统上补充了相关资料，请及时查看。';
				$sourceType = USER_MESSAGE_SOURCE['ten'];
				break;
			case APPROVAL_MESSAGE_CONTENT['twentysix']:
				$time = $arr['time'];
				$content = '尊敬的用户，本部门在关于'.$policy_name.'政策类型的'.$project_name.'项目的申报中，距离本部门审核截止日期还剩'.$time.'天了，请尽快去审核。';
				$sourceType = USER_MESSAGE_SOURCE['seven'];
				break;
            case APPROVAL_MESSAGE_CONTENT['twentyseven']:
                $content = '在'.$enterprise_name.'企业申报的'.$project_name.'项目审核过程中，'.$department_name.'部门发起了资料订正请求，请及时进行查看。';
                $sourceType = USER_MESSAGE_SOURCE['apply_correct'];
                break;
            case APPROVAL_MESSAGE_CONTENT['twentyeight']:
                $audit_name = array_get($arr, 'audit_name', '');
                $content = '企业服务中心'.$audit_name.'您本次发起的关于'.$enterprise_name.'企业在'.$project_name.'申报的资料订正请求，如有疑问请联系企业服务中心。';
                $sourceType = USER_MESSAGE_SOURCE['apply_correct_audit'];
                break;
            case APPROVAL_MESSAGE_CONTENT['twentynine']:
                $content = $enterprise_name.'企业已经按照要求把申报的'.$project_name.'项目的相关资料订正完毕，请及时进行查看。';
                $sourceType = USER_MESSAGE_SOURCE['apply_correct_success'];
                break;
            case APPROVAL_MESSAGE_CONTENT['thirty']:
                $content = $enterprise_name.'企业撤回了关于'.$policy_name.'政策类型的'.$project_name.'项目的申报。';
                $sourceType = USER_MESSAGE_SOURCE['apply_revocation'];
                break;
            case APPROVAL_MESSAGE_CONTENT['thirtyone']:
                $mark = array_get($arr, 'mark', '');
                $content = '尊敬的用户，在关于'.$project_name.'项目的申报中，您的申报资料信息有误，请按照要求对资料进行订正。具体原因：'.$mark;
                $sourceType = USER_MESSAGE_SOURCE['user_apply_correct'];
                break;
            case APPROVAL_MESSAGE_CONTENT['thirtytwo']:
                $mark = array_get($arr, 'mark', '');
                $content = '尊敬的用户，在关于'.$project_name.'项目的申报中，您本次进行的申报资料订正申请未通过审核，请按要求重新订正。具体原因：'.$mark;
                $sourceType = USER_MESSAGE_SOURCE['user_apply_correct_again'];
                break;
            case APPROVAL_MESSAGE_CONTENT['thirtythree']:
                $content = '尊敬的用户，在关于'.$project_name.'项目的申报中，您本次进行的申报资料订正申请通过审核并且生效。';
                $sourceType = USER_MESSAGE_SOURCE['user_apply_correct_success'];
                break;
            case APPROVAL_MESSAGE_CONTENT['thirtyfour']:
                $content = '在'.$enterprise_name.'企业申报的'.$project_name.'项目审核过程中，'.$department_name.'部门发起了资料订正请求，已被企业服务中心作废本次资料订正。';
                $sourceType = USER_MESSAGE_SOURCE['apply_correct_invalid'];
                break;
            case APPROVAL_MESSAGE_CONTENT['thirtyfive']:
                $content = '尊敬的用户，在关于'.$project_name.'项目的申报中，部门要求您进行申报资料订正已经作废。';
                $sourceType = USER_MESSAGE_SOURCE['user_apply_correct_invalid'];
                break;
            case APPROVAL_MESSAGE_CONTENT['thirtysix']:
                $content = $enterprise_name.'企业已经按要求把申报的'.$project_name.'项目资料订正完毕，请及时进行审核。';
                $sourceType = USER_MESSAGE_SOURCE['apply_correct_audit_wait'];
                break;
		}
		
		return [
			'content' => $content,
			'source_type' => $sourceType
		];
	}
	
	/**
	 * 站内信
	 */
	public function sendMessage($arr, $type)
	{
		$user_type = MESSAGE_USER_TYPE['staff'];
		$user_id = empty($arr['staff_id']) ? 0 : $arr['staff_id'];
		$target_id = empty($arr['approval_id']) ? 0 : $arr['approval_id'];

		$userArr = [
			APPROVAL_MESSAGE_CONTENT['three'], 
			APPROVAL_MESSAGE_CONTENT['four'],
			APPROVAL_MESSAGE_CONTENT['six'],
			APPROVAL_MESSAGE_CONTENT['seven'],
			APPROVAL_MESSAGE_CONTENT['ten'],
			APPROVAL_MESSAGE_CONTENT['twelve'],
			APPROVAL_MESSAGE_CONTENT['fourteen'],
			APPROVAL_MESSAGE_CONTENT['sixteen'],
			APPROVAL_MESSAGE_CONTENT['seventeen'],
			APPROVAL_MESSAGE_CONTENT['eighteen'],
			APPROVAL_MESSAGE_CONTENT['nineteen'],
			APPROVAL_MESSAGE_CONTENT['twenty'],
			APPROVAL_MESSAGE_CONTENT['twentyone'],
			APPROVAL_MESSAGE_CONTENT['thirtyone'],
			APPROVAL_MESSAGE_CONTENT['thirtytwo'],
			APPROVAL_MESSAGE_CONTENT['thirtythree'],
			APPROVAL_MESSAGE_CONTENT['thirtyfive'],
		];
		if (in_array($type, $userArr)) {
			$user_type = MESSAGE_USER_TYPE['user'];
			$target_id = $arr['apply_id']; // 企业用申请表ID
			$user_id = $arr['user_id'];
		}

		$content = $this->getContent($arr, $type);
		
		$message = [
			'content' => $content['content'],
			'user_id' => $user_id,
			'user_type' => $user_type,
			'type' => USER_MESSAGE_READ['not'],
			'source_type_id' => $content['source_type'],
			'target_id' => $target_id
		];

		$result = app(UserMessageRepository::class)->storeRepository($message);
		
		$message['user_message_id'] = $result['id'] ?? 0;
		return $message;
	}
	
	/**
	 * 获取企业的人员信息
	 */
	private function getEnterpriseUser($arr) {
		// user id
		$user = (new UserModel())
			->setTable('f1')
			->from(UserModel::TABLE_NAME . ' AS f1')
			->join(UserEnterpriseRelationModel::TABLE_NAME . ' AS f2','f2.user_id','=','f1.id')
			->where(['f2.enterprise_id' => $arr['enterprise_id']])
			->whereRaw('f1.deleted_at is null')
			->whereNull('f2.deleted_at')
			->limit(1)
			->get(['f2.user_id', 'f1.mobile'])
			->toArray();
		
		return [
			'user_id' => empty($user[0]['user_id']) ? '' : $user[0]['user_id'],
			'mobile' => empty($user[0]['mobile']) ? '' : $user[0]['mobile'],
		];
	}
	
	/**
	 * 短信
	 */
	public function sendSms($arr, $type)
	{
		$param = $this->getSmsParam($arr, $type);

 		$resultSms = app(SmsRepository::class)->send([
 			'telephone' => $arr['mobile'],
 			'template' => $type,
 			'param' => $param
 		]);
 		if (!$resultSms) {
 			return codeRender(Code::FAIL);
 		}
		return true;
	}

	/**
	 * 获取短信的参数
	 */
	public function getSmsParam($arr, $type)
	{
		$return = [];
		$return['policy_name'] = $arr['policy_name'] ?? '';
		$return['project_name'] = $arr['project_name'] ?? '';

		switch ($type)
		{
			case SMS_TEMPLATE['one']:
				$return['department_name'] = $arr['department_name'] ?? '';
				$return['time'] = $arr['config_time'] ?? '';
				break;
			case SMS_TEMPLATE['two']:
				$return['enterprise_name'] = $arr['enterprise_name'] ?? '';
				break;
			case SMS_TEMPLATE['three']:
				break;
			case SMS_TEMPLATE['four']:
// 				$return['mark'] = $arr['mark'] ?? '';
				break;
			case SMS_TEMPLATE['five']:
				$return['department_name'] = $arr['department_name'] ?? '';
				$return['startTime'] =  date('Y-m-d', $arr['start_time']);
				$return['endTime'] = date('Y-m-d', $arr['end_time']);
				break;
			case SMS_TEMPLATE['six']:
// 				$return['mark'] = $arr['mark'] ?? '';
				$return['startTime'] =  date('Y-m-d', $arr['start_time']);
				$return['endTime'] = date('Y-m-d', $arr['end_time']);
				break;
			case SMS_TEMPLATE['seven']:
				$return['department_name'] = $arr['department_name'] ?? '';
				break;
			case SMS_TEMPLATE['eight']:
				$return['time'] = $arr['config_time'];
				break;
			case SMS_TEMPLATE['nine']:
				break;
			case SMS_TEMPLATE['ten']:
				$return['time'] = $arr['config_time'];
				break;
			case SMS_TEMPLATE['eleven']:
				break;
			case SMS_TEMPLATE['twelve']:
				break;
			case SMS_TEMPLATE['thirteen']:
				break;
			case SMS_TEMPLATE['fourteen']:
				break;
			case SMS_TEMPLATE['fifteen']:
				break;
			case SMS_TEMPLATE['sixteen']:
// 				$return['mark'] = $arr['mark'] ?? '';
				break;
			case SMS_TEMPLATE['seventeen']:
				break;
			case SMS_TEMPLATE['eighteen']:
				break;
			case SMS_TEMPLATE['nineteen']:
				$return['time'] = $arr['time'];
				break;
			case SMS_TEMPLATE['twenty']:
				$return['enterprise_name'] = $arr['enterprise_name'] ?? '';
				break;
            case SMS_TEMPLATE['twentyseven']:
                $return = array_only($arr, ['enterprise_name', 'project_name', 'department_name']);
                break;
            case SMS_TEMPLATE['twentyeight']:
                $return = array_only($arr, ['enterprise_name', 'project_name', 'audit_name']);
                break;
            case SMS_TEMPLATE['twentynine']:
                $return = array_only($arr, ['enterprise_name', 'project_name']);
                break;
            case SMS_TEMPLATE['thirty']:
                $return = array_only($arr, ['enterprise_name', 'project_name', 'policy_name']);
                break;
            case SMS_TEMPLATE['thirtyone']:
                $return = array_only($arr, [ 'project_name']);
                break;
            case SMS_TEMPLATE['thirtytwo']:
                $return = array_only($arr, [ 'project_name']);
                break;
            case SMS_TEMPLATE['thirtythree']:
                $return = array_only($arr, [ 'project_name']);
                break;
            case SMS_TEMPLATE['thirtyfour']:
                $return = array_only($arr, ['enterprise_name', 'project_name', 'department_name']);
                break;
            case SMS_TEMPLATE['thirtyfive']:
                $return = array_only($arr, [ 'project_name']);
                break;
            case SMS_TEMPLATE['thirtysix']:
                $return = array_only($arr, [ 'project_name', 'enterprise_name']);
                break;
		}
	
		foreach ($return as $key => $value) {
			$return[$key] = getStrLength($value);
		}
		
		return $return;
	}
	
	/**
	 * 保存审批意见附件-多个
	 */
	public function saveOpinionFile($arr)
	{
		$tmpData = [];
		foreach ($arr['file_list'] as $key => $value) {
			$tmpData[] = [
				'approval_id' => $arr['approval_id'],
				'file_name' => $value['file_name'],
				'file_url' => $value['file_url'],
				'created_at' => time()
			];
		}
	
		DB::beginTransaction();
	
		try{
				
			ApprovalFileModel::insert($tmpData);
	
			DB::commit();
	
		}catch (Exception $e){
			Log::error('approval saveOpinionFile' . $e->getMessage());
			DB::rollBack();
			return false;
		}

		return true;
	}
	
	/**
	 * 删除审批意见附件-单个
	 */
	public function deleteOpinionFile($arr)
	{
		try{
	
			ApprovalFileModel::where([
				'approval_id' => $arr['approval_id'],
				'id' => $arr['approval_file_id']
			])->delete();

		}catch (Exception $e){
			Log::error('approval deleteOpinionFile' . $e->getMessage());
			return false;
		}
	
		return true;
	}

	public function getByApply($apply_id)
    {
        $res = $this->model->where('apply_id', $apply_id)->first();
        return empty($res) ? [] : $res->toArray();
    }
    /**
     * FUNCTION_NAME : getCoordinate
     * author : jp
     * 获取协同部门
     * @param $applyId
     * @return array
     */
	public function getCoordinate($applyId)
    {
        $approvalCoordinateList = [];

        $coordinate = ApprovalModel::where([
            'apply_id' => $applyId,
            'type' => APPROVAL_TYPE['three']
        ])
            ->get(['department_id', 'start_time', 'end_time', 'remark'])
            ->toArray();

        if (empty($coordinate)) {
            $approvalCoordinateList = [
                'department_list' => [],
                'start_time' => 0,
                'end_time' => 0
            ];
        } else {
            $approvalCoordinateList = [
                'department_list' => $coordinate,
                'start_time' => $coordinate[0]['start_time'],
                'end_time' => $coordinate[0]['end_time']
            ];
        }
        return $approvalCoordinateList;
    }

    /**
     * FUNCTION_NAME : checkDepartmentStaff
     * author : jp
     * 检查部门的操作人员
     * @param $departmentIdArr
     * @return array
     * @throws CodeException
     */
    public function checkDepartmentStaff($departmentIdArr)
    {
        // 查询协同部门的负责人
        $staffListTmp = (new StaffModel())
            ->setTable('f1')
            ->from(StaffModel::TABLE_NAME . ' AS f1')
            ->join(StaffBindDepartmentModel::TABLE_NAME . ' AS f2','f2.staff_id','=','f1.id')
            ->join(StaffDepartmentModel::TABLE_NAME . ' AS f3','f3.id','=','f2.department_id')
            ->where('f2.opertor_type', STAFF_OPERTOR_TYPE['one'])
            ->whereIn('f2.department_id', $departmentIdArr)
            ->orderBy('f1.number', 'asc')
            ->get(['f2.staff_id', 'f2.department_id', 'f1.mobile', 'f3.name AS department_name'])
            ->toArray();

        // 部门人员校验
        if (empty($staffListTmp)) {
            throw new CodeException(Code::APPROVAL_STAFF_EXIST_ERROR);
        }

        $hasDepartment = array_column($staffListTmp, 'department_id');
        foreach ($departmentIdArr as $kd => $vd) {
            if (!in_array($vd, $hasDepartment)) {
                $departmentErr = StaffDepartmentModel::select(['name'])->find($vd);
                if (empty($departmentErr)) {
                    return Code::APPROVAL_STAFF_EXIST_ERROR;
                }
                throw new CodeException(Code::APPROVAL_DEPARTMENT_SAME_ERROR,
                    trans('approval.department_staff_error', ['name' => $departmentErr['name']]));
            }
        }

        return $staffListTmp;
    }


    /**
     * FUNCTION_NAME : getApprovalByApplyId
     * author : jp
     * 获取拨款的审批id
     * @param $applyId
     * @return mixed
     */
    public function getApprovalByApplyId($applyId)
    {
        return $this->model
            ->whereIn('apply_id', $applyId)->where('type', APPROVAL_TYPE['five'])->get()->toArray();
    }

    
    /**
     * FUNCTION_NAME : EnterpriseCenterOpinion
     * author : jp
     * 企业服务中心审批意见
     * @param $arr
     * @return mixed
     */
    public function EnterpriseCenterOpinion($arr)
    {
        $opinion = [
            'approval_id' => $arr['approval_id'],
            'expert_mark' => $arr['expert_mark'] ?? '',
            'department_mark' => $arr['department_mark'] ?? '',
// 			'file_url' => '',
// 			'file_name' => $arr['file_name'] ?? '',
            'created_at' => time(),
            'business_id' => businessId()
        ];

        return ApprovalOpinionModel::insert($opinion);
    }

    public function enterpriseApprovalByApply($applyId)
    {
        $res = $this->model->where('apply_id', $applyId)->where('type', APPROVAL_TYPE['one'])->first();
        return empty($res) ? [] : $res->toArray();
    }

    public function deleteById($id)
    {
        return $this->model->destroy($id);
    }
}
