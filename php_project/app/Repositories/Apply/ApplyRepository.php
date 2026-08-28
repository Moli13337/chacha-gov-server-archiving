<?php
namespace App\Repositories\Apply;

use App\Criteria\ApplyChart\SupplementCriteria;
use App\Criteria\Credit\WhereEndCriteria;
use App\Criteria\KeywordCriteria;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereCreatedEndCriteria;
use App\Criteria\WhereCreatedStartCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Criteria\WhereInCriteria;
use App\Events\ApplyFormPdfCreate;
use App\Events\ApplyPdfCreate;
use App\Events\ZipCreate;
use App\Exceptions\QueryException;
use App\Models\Scope\SupplementApplyScope;
use App\Repositories\Staff\StaffDepartmentRepository;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Repositories\BaseRepository;
use App\Models\ApplyModel;
use App\Models\ApplyEconomyModel;
use App\Models\ApplyFileModel;
use Illuminate\Support\Str;
use function GuzzleHttp\json_decode;
use App\Models\ApplyFileExceptionModel;
use Illuminate\Support\Facades\Log;
use App\Models\ApprovalModel;
use App\Models\ApprovalMarkModel;
use App\Models\ApprovalOpinionModel;
use App\Models\StaffDepartmentModel;
use App\Http\Controllers\Service\IndustryService;
use App\Common\Code;
use App\Repositories\Enterprise\EnterpriseBusinessRepository;
use App\Repositories\Enterprise\EnterpriseEmployeeOverviewRepository;
use App\Repositories\Enterprise\EnterpriseLinkmanRepository;
use App\Models\StaffModel;
use App\Models\StaffBindDepartmentModel;
use App\Models\ApprovalMaterialModel;
use App\Repositories\PdfRepository;

class ApplyRepository  extends BaseRepository
{
	public $industry_segmenter = '|';

	public function model()
	{
		return ApplyModel::class;
	}

	/**
	 * 列表
	 */
	public function list($arr, $columns = ['*'])
	{
		$where = [];
		if (!empty($arr['enterprise_id'])) {
			$where[] = ['enterprise_id', '=', $arr['enterprise_id']];
		}
		
		// status
		if (!empty($arr['apply_status'])) {
			$where[] = ['apply_status', '=', $arr['apply_status']];
		}
		
		// 运营端
		if (empty($arr['apply_status']) && empty($arr['enterprise_id'])) {
			$where[] = ['apply_status', '!=', APPLY_STATUS['one']];
		}

        if (!empty($arr['mold_id'])) {
            $where[] = ['mold_id', '=', $arr['mold_id']];
        }

        if (!empty($arr['start_time'])) {
            $where[] = ['submit_time', '>=', $arr['start_time']];
        }

        if (!empty($arr['end_time'])) {
            $where[] = ['submit_time', '<=', $arr['end_time']];
        }

		$applyModel = ApplyModel::where($where)->whereNull('deleted_at');
		
		// 搜索
		if (!empty($arr['keyword'])) {
			$filterArr = ['policy_name', 'project_name', 'enterprise_name'];
		
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

        if (!empty($arr['main_department_id'])) {
            $department_id = $arr['main_department_id'];
            $applyModel = $applyModel->whereHas('haveDepartment', function ($query) use ($department_id){
                $query->where('department_id', $department_id);
            });
        }

		$count = $applyModel->count();

		$page = commonPage($arr);
		$list = $applyModel
            ->with(['mainDepartment:department_id,name'])
			->orderBy('created_at', 'desc')
			->offset($page['offset'])
			->limit($page['page_size'])
			->get($columns)
			->toArray();

		// getOperatorStaffByIds
        $mainArr = app(StaffDepartmentRepository::class)->getOperatorStaffByAll();
        $mainArr = array_column($mainArr, null,'department_id');

		// 查询是否需要补充资料
		if (!empty($list)) {
			$applyIds = [];
			foreach ($list as $key => $value) {
				if (in_array($value['apply_status'], [
					APPLY_STATUS['five'],
					APPLY_STATUS['six']
				])) {
					$applyIds[] = $value['id'];
				}
			}
			$markIds = [];
			if (!empty($applyIds)) {
				$markList = (new ApprovalMarkModel())
					->setTable('f1')
					->from(ApprovalMarkModel::TABLE_NAME . ' AS f1')
					->join(ApprovalModel::TABLE_NAME . ' AS f2','f2.id','=','f1.approval_id')
					->whereIn('f1.type', [APPROVAL_MARK_TYPE['three'], APPROVAL_MARK_TYPE['four']])
					->whereIn('f2.apply_id', $applyIds)
					->groupBy('f2.apply_id')
					->get(['f2.apply_id'])
					->toArray();
				
				if (!empty($markList)) {
					$markIds = array_column($markList, 'apply_id');
				}
			}

			// 查询是否有 订正资料
            $tmpApplyIds = array_column($list, 'id');
            $hasCorrectIds = app(ApplyCorrectRepository::class)->getUserWaitByIds($tmpApplyIds);

			foreach ($list as $key => $value) {
				$hasMaterial = false;
				if (!empty($markIds) && in_array($value['id'], $markIds)) {
					$hasMaterial = true;
				}
				$value['has_material'] = $hasMaterial;

				if (in_array($value['id'], $hasCorrectIds)) {
                    $value['has_correct'] = true;

                } else {
                    $value['has_correct'] = false;
                }
				$tmp_department = array_get($value['main_department'], 0, []);
				$tmp_department_id = array_get($tmp_department, 'department_id', 0);
				$value['main_department'] = (object)array_get($mainArr,$tmp_department_id, []);
				
				$list[$key] = $value;
			}
		}

		return returnPage2($list, $count, $page);
	}
	
	/**
	 * 详情-申请表
	 */
	public function detailApply($arr)
	{
		$applyId = $arr['id'];
	
		// 申请表
		$where = ['id' => $applyId];
		$apply = ApplyModel::where($where)
			->limit(1)
			->get()
			->toArray();
	
		if (empty($apply)) {
			return [];
		}
	
		$apply = $apply[0];
		
		// 行业类别处理
		$apply['industry_id'] = empty($apply['industry_id']) ? 
			[] : explode($this->industry_segmenter, $apply['industry_id']);
		$apply['industry_text'] = empty($apply['industry_text']) ? 
			[] : explode($this->industry_segmenter, $apply['industry_text']);

		// 经济指标
		$resultEco = ApplyEconomyModel::where(['apply_id' => $applyId])
			->orderBy('year', 'asc')
			->get()
			->toArray();
	
		// 按照日期组装
		$yearList = [];
		foreach ($resultEco as $key => $value) {
			if (!in_array($value['year'], $yearList)) {
				$yearList[] = $value['year'];
			}
		}
	
		// 组装数据
		$economyList = [];
		foreach ($yearList as $key => $value) {
			$tmpList = [];
			foreach ($resultEco as $key2 => $value2) {
				if ($value === $value2['year']) {
					$tmpList[] = $value2;
				}
			}
				
			$economyList[] = [
				'year' => $value,
				'content_list' => $tmpList
			];
		}
	
		$apply['result_economy'] = $resultEco;
		$apply['economy_list'] = $economyList;
	
		// 附件快照
		$fileConfig = empty($apply['config']) ? [] : json_decode($apply['config'], true);

		// TODO 可以先修复数据库的数据就可以不用在这里做修复了
		foreach ($fileConfig as $kf => $vf) {
		    if (!empty($vf['file_list'])) {
		        foreach ($vf['file_list'] as $kfl => $vfl) {
                    if (isset($vfl['create_at'])) {
                        $fileConfig[$kf]['file_list'][$kfl]['created_at'] = $vfl['create_at'];
                        unset( $fileConfig[$kf]['file_list'][$kfl]['create_at']);
                    }
                }
            }
        }

		$resultFile = ApplyFileModel::where(['apply_id' => $applyId])
			->orderBy('id', 'asc')
			->get([
				'id',
				'file_name',
				'file_url',
				'file_type',
				'project_materials_id',
				'created_at',
			])
			->toArray();

		if (!empty($resultFile)) {
			foreach ($fileConfig as $key => $value) {
				$tmpList = [];
				foreach ($resultFile as $key2 => $value2) {
					if ($value['id'] === $value2['project_materials_id']) {
						$tmpList[] = $value2;
						unset($resultFile[$key2]);
					}
				}
	
				$value['file_list'] = $tmpList;
				$fileConfig[$key] = $value;
			}
		}

		$apply['config'] = $fileConfig;
        $apply['flow_status'] = $this->approvalStatusFlow($apply['apply_status']);

		return $apply;
	}
	
	/**
	 * 详情
	 * has_approval : 1默认不需要审批信息  2需要审批信息 
	 */
	public function detail($arr)
	{
		$applyId = $arr['id'];

		$apply = $this->detailApply(['id' => $applyId]);
		if (empty($apply)) {
			return [];
		}
		
		unset($apply['result_economy']);
		// 1默认不需要审批信息  2需要审批信息
		$hasApproval = $arr['has_approval'] ?? 1;

		if ($hasApproval == 1) {
			// 查询是否有补充材料-并判断截止日期大于等于当前时间
			$material = ApprovalMaterialModel::where([
					'apply_id' => $applyId
				])
				->max('end_time');

			$hasMaterial = false;
			if (!empty($material) && $material >= time()) {
				$hasMaterial = true;
			}
				
			$apply['has_material'] = $hasMaterial;

			// 是否有订正资料
            $correct = app(ApplyCorrectRepository::class)->getUserWaitByIds([$apply['id']]);
            $hasCorrect = empty($correct) ? false : true;
            $apply['has_correct'] = $hasCorrect;
			return $apply;
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
				'f2.expert_mark',
				'f2.department_mark',
				'f3.name AS department_name'
			])
			->toArray();

		if (!empty($approvalList)) {
			$typeTwo = []; // 用于数组排序
			$typeTwoIndex = 0; // 记录主审部门的位置
			$typeFourIndex = 0; // 记录指挥部审批的位置
			$approvalId = 0; // 找出园区办公室的审批
			foreach ($approvalList as $key => $value) {
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
				}
				if ($value['approval_type'] == APPROVAL_TYPE['four']) {
					$typeFourIndex = $key;
				}
					
				if ($value['approval_type'] == APPROVAL_TYPE['five']) {
					$approvalId = $value['approval_id'];
					unset($approvalList[$key]);
				} else {
					$approvalList[$key] = $value;
				}
			}
			
			// 改变主审部门的顺序
			if ($typeTwoIndex != 0) {
				// 1、先删除主审部门
				array_splice($approvalList, $typeTwoIndex, 1);
				// 2、主审部门插入
				if ($typeFourIndex != 0) {
					// 有指挥部插入指挥部之前
					array_splice($approvalList, $typeFourIndex, 0, $typeTwo);
				} else {
					// 没有指挥部插入末尾即可
					array_pull($approvalList, $typeTwo);
				}
			}
		}
		
		$apply['approval_list'] = empty($approvalList) ? [] : $approvalList;
		
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
		return $apply;
	}

	/**
	 * store
	 * $isSecond: 第二次新增
	 */
	public function store($arr)
	{
		// 查询数据最大的编号
		$number = $this->getMaxCode();
		
		$economyList = empty($arr['economy_list']) ? [] : $arr['economy_list'];
		$configList = empty($arr['config']) ? [] : $arr['config'];
		
		// config处理
		$fileList = [];
		if (!empty($arr['children_id']) && !empty($configList)) {
			// children_id 查询之前的申请表信息
			$tmpApply = ApplyModel::where([
					'id' => $arr['children_id']
				])->limit(1)->get()->toArray();
		
			if (!empty($tmpApply)) {
				$tmpApply = $tmpApply[0];
				$arr['business_content'] = $tmpApply['business_content'];
				$arr['plan_content'] = $tmpApply['plan_content'];
				$arr['approval_organ'] = $tmpApply['approval_organ'];
				$arr['approval_number'] = $tmpApply['approval_number'];
				$arr['qualifications'] = $tmpApply['qualifications'];
				$arr['provisions'] = $tmpApply['provisions'];
				$arr['apply_criteria'] = $tmpApply['apply_criteria'];
				$arr['apply_money'] = $tmpApply['apply_money'];
				$arr['other_notes'] = $tmpApply['other_notes'];
			}

			foreach ($configList as $key => $value) {
				if (!empty($value['file_list'])) {
					foreach ($value['file_list'] as $key2 => $value2) {
						$fileList[] = [
							'file_name' => $value2['file_name'] ?? '',
							'file_url' => $value2['file_url'] ?? '',
							'file_type' => $value2['file_type'] ?? 0,
							'check_status' => APPLY_CHECK_STATUS['init'],
							'project_materials_id' => $value2['project_materials_id'] ?? 0,
							'created_at' => time()
						];
					}
				}
				
				unset($value['file_list']);
				$configList[$key] = $value;
			}
		}

		$apply = array_except($arr, ['save_type', 'economy_list', 'file_list', 'id', 'children_id']);
		$apply['number'] = $number;
		$apply['audit_time'] = time();
		$apply['apply_status'] = APPLY_STATUS['one'];
		$apply['config'] = empty($configList) ? '' : json_encode($configList);
		$apply['regist_time'] = empty($apply['regist_time']) ? 0 : $apply['regist_time'];

		// 行业类别处理
		$industry_id = '';
		$industry_text = '';
		if (!empty($arr['industry_id'])) {
			$industryId = $arr['industry_id'];
			
			$industryList = app(IndustryService::class)->getIndustry([
				'first_industry_id' => empty($industryId[0]) ? '' : $industryId[0],
				'second_industry_id' => empty($industryId[1]) ? '' : $industryId[1],
				'third_industry_id' => empty($industryId[2]) ? '' : $industryId[2],
				'fourth_industry_id' => empty($industryId[3]) ? '' : $industryId[3],
			]);
			
			$industry_id = implode($this->industry_segmenter, $industryId);
			$industry_text = implode($this->industry_segmenter, $industryList);
		}
		$apply['industry_id'] = $industry_id;
		$apply['industry_text'] = $industry_text;
		
		DB::beginTransaction();

		try{
			$result = $this->create($apply);
			$apply_id = $result['id'];
			
			foreach ($economyList as $key => $value) {
				$value['apply_id'] = $apply_id;
				$economyList[$key] = $value;
			}

			if (!empty($economyList)) {
				ApplyEconomyModel::insert($economyList);
			}
			
			// 更新之前的申请表信息
			if (!empty($arr['children_id'])) {
				$this->update([
					'children_id' => $apply_id
				], $arr['children_id']);
				
				if (!empty($fileList)) {
					foreach ($fileList as $key => $value) {
						$value['apply_id'] = $apply_id;
						$fileList[$key] = $value;
					}
					// 新增
					ApplyFileModel::insert($fileList);
				}
			}

			DB::commit();
			return $apply_id;
			
		}catch (Exception $e){
			Log::error('apply store' . $e->getMessage());
			DB::rollBack();
			return false;
		}
	}
	
	/**
	 * update
	 * type 操作类型  1 草稿 2 企业基本信息  3 项目申报   4 上传附件 5补充材料
	 */
	public function updateApply($arr, $type = 1)
	{
		// 根据ID查询是否存在
		$applyObj = [];
		if ($type == 4) {
			$applyObj = $this->detailApply(['id' => $arr['id']]);
			
			if (empty($applyObj)) {
				return Code::CHECK_OPERATE_ERROR;
			}
		} else {
			$applyObj = ApplyModel::where(['id' => $arr['id']])
				->limit(1)
				->get([
					'id',
					'policy_name',
					'project_name',
					'enterprise_name',
					'apply_status'
				])
				->toArray();
			
			if (empty($applyObj)) {
				return Code::CHECK_OPERATE_ERROR;
			}
			
			$applyObj = $applyObj[0];
			
			// 判断补充材料
			if ($type == 5) {
				if (!in_array($applyObj['apply_status'], [
					APPLY_STATUS['five'],
					APPLY_STATUS['six']
				])) {
					return Code::APPROVAL_TYPE_ERROR;
				}
			}
		}

		$economyList = empty($arr['economy_list']) ? [] : $arr['economy_list'];
		$fileList = empty($arr['file_list']) ? [] : $arr['file_list'];
		$fileTypeArr = [];
		$apply = [];
		
		if (in_array($type, [1, 2, 3])) {
			// 行业类别处理
			if (isset($arr['industry_id'])) {
				// 行业类别处理
				$industry_id = '';
				$industry_text = '';
				if (!empty($arr['industry_id'])) {
					$industryId = $arr['industry_id'];
					
					$industryList = app(IndustryService::class)->getIndustry([
						'first_industry_id' => empty($industryId[0]) ? '' : $industryId[0],
						'second_industry_id' => empty($industryId[1]) ? '' : $industryId[1],
						'third_industry_id' => empty($industryId[2]) ? '' : $industryId[2],
						'fourth_industry_id' => empty($industryId[3]) ? '' : $industryId[3],
					]);
					
					$industry_id = implode($this->industry_segmenter, $industryId);
					$industry_text = implode($this->industry_segmenter, $industryList);
				}

				$arr['industry_id'] = $industry_id;
				$arr['industry_text'] = $industry_text;
			}

			$apply = array_except($arr, ['save_type', 'economy_list', 'file_list', 'config']);
			
			if (isset($apply['regist_time'])) {
				$apply['regist_time'] = empty($apply['regist_time']) ? 0 : $apply['regist_time'];
			}

            list($tmpArr, $hasInvoice) = $this->dealFile($fileList, $arr);
            $fileList = $tmpArr;
            $fileTypeArr = [
                MATERIALS_TYPE['other'],
                MATERIALS_TYPE['invoice'],
                MATERIALS_TYPE['identity'],
                MATERIALS_TYPE['business']
            ];
        } else if ($type == 4) {
			// 附件提交只更新状态
			list($tmpArr, $hasInvoice) = $this->dealFile($fileList, $arr);
			$fileList = $tmpArr;
			$fileTypeArr = [
				MATERIALS_TYPE['other'], 
				MATERIALS_TYPE['invoice'], 
				MATERIALS_TYPE['identity'], 
				MATERIALS_TYPE['business']
			];

            $apply['id'] = $arr['id'];
            $apply['submit_time'] = time();
            // 申报人信息
            $apply['user_id'] = getLoginHome('id');
            $apply['user_name'] = getLoginHome('name');
            $apply['apply_status'] = $hasInvoice ? APPLY_STATUS['two'] : APPLY_STATUS['three'];
            // 业务ID
            $apply['business_id'] = businessId();
            $apply['zip_business_id'] = businessId();
            $apply['zip_url'] = '';
			$arr['has_invoice'] = $hasInvoice;
			// 可撤销
            $apply['able_revocation'] = APPLY_ABLE_REVOCATION['yes'];

			// 处理是否有无发票，没有发票的时候直接创建审批，无需预处理
			if (!$hasInvoice) {
				// 查询区业务服务部门操作员
				$staff = app(ApprovalDepartmentRepository::class)->getStaff();
				if (empty($staff)) {
					return Code::APPROVAL_STAFF_EXIST_ERROR;
				}
				$arr = array_merge($arr, $staff);
			}

		} else if ($type == 5) {
			// 组装数据
			$tmpArr = [];
			foreach ($fileList as $key => $value) {
				$tmpArr[] = [
					'apply_id' => $arr['id'],
					'file_name' => $value['file_name'] ?? '',
					'file_url' => $value['file_url'] ?? '',
					'file_type' => $value['file_type'] ?? 0,
					'check_status' => APPLY_CHECK_STATUS['init'],
					'project_materials_id' => $value['project_materials_id'] ?? 0,
					'created_at' => time()
				];
			}
			
			$fileList = $tmpArr;
			$fileTypeArr = [
				MATERIALS_TYPE['default']
			];
			
			// 查询主审部门和协同部门的操作人员
			$materialList = ApprovalMaterialModel::where([
					'apply_id' => $arr['id']
				])
				->get([
					'approval_id'
				])
				->toArray();

			if (!empty($materialList)) {
				$approvalIds = array_unique(array_column($materialList, 'approval_id'));
				
				// 查询部门
				$departList = ApprovalModel::whereIn('id', $approvalIds)
					->get(['id AS approval_id', 'department_id'])
					->toArray();
				
				$departmentIds = array_column($departList, 'department_id');
				
				// 查询操作人员
				$staffList = (new StaffModel())
					->setTable('f1')
					->from(StaffModel::TABLE_NAME . ' AS f1')
					->join(StaffBindDepartmentModel::TABLE_NAME . ' AS f2','f2.staff_id','=','f1.id')
					->where([
						'f2.opertor_type' => STAFF_OPERTOR_TYPE['one'],
					])
					->whereIn('f2.department_id', $departmentIds)
					->get(['f2.staff_id', 'f2.department_id', 'f1.mobile'])
					->toArray();
					
				
				if (!empty($staffList)) {
					foreach ($departList as $key => $value) {
						foreach ($staffList as $key2 => $value2) {
							if ($value['department_id'] == $value2['department_id']) {
								$value['staff_id'] = $value2['staff_id'];
								$value['mobile'] = $value2['mobile'];
								unset($staffList[$key2]);
								break;
							}
						}
						
						$departList[$key] = $value;
					}
					
					$arr['material_list'] = $departList;
				}
				
			}

            $apply['zip_business_id'] = businessId();
            $apply['zip_url'] = '';
            $apply['id'] = $arr['id'];
        }

		DB::beginTransaction();
		
		try{
            // 基本信息
			if (!empty($apply)) {
				$this->update(array_except($apply,['id']), $apply['id']);
			}
			
			// 经济指标
			if (!empty($economyList)) {
				// 删除已有
				ApplyEconomyModel::where(['apply_id' => $arr['id']])->delete();

				// 新增
				foreach ($economyList as $key => $value) {
					$value['apply_id'] = $arr['id'];
					$economyList[$key] = $value;
				}
				
				ApplyEconomyModel::insert($economyList);
			}

			// 附件处理
			if ($type == 4 || $type == 5 || $type == 1) {
				// 删除已有
				ApplyFileModel::where(['apply_id' => $arr['id']])
					->whereIn('file_type', $fileTypeArr)
					->delete();
				// 新增
				if (!empty($fileList)) {
					ApplyFileModel::insert($fileList);
				}
			}
			
			// 提交操作
			if ($type == 4) {
				$arr = $this->submitApply($applyObj, $arr);
			}
			
			// 提交补充材料
			if ($type == 5) {
			    //
                $fileList = empty($fileList) ?[] : $fileList;
				ApprovalMaterialModel::where([
						'apply_id' => $arr['id']
					])->update([
						'status' => MATERIAL_SEND_STATUS['three'],
                        'submit_time' => time(),
                        'material' => json_encode($fileList, JSON_UNESCAPED_UNICODE)
					]);
					
				// 推送给主审部门和协同部门
				$materialList = $arr['material_list'] ?? [];
				foreach ($materialList as $key => $value) {
					app(ApprovalRepository::class)->sendMessage([
						'staff_id' => $value['staff_id'],
						'approval_id' => $value['approval_id'],
						'policy_name' => $applyObj['policy_name'],
						'project_name' => $applyObj['project_name'],
						'enterprise_name' => $applyObj['enterprise_name']
					], APPROVAL_MESSAGE_CONTENT['twentyfive']);
				}
			}
			
			DB::commit();
			
		}catch (Exception $e){
			Log::error('apply update' . $e->getMessage());
			DB::rollBack();
			return false;
		}
		
		DB::commit();

		// 附件/pdf创建
		try {
		    if ($type == 4 || $type == 5) {
		        $tmpApply = array_merge($arr,$applyObj, $apply??[]);
                $this->zipCreate($tmpApply);
                $detail = app(ApprovalRepository::class)->detail(['id' => $applyObj['id']]);
                app(ApplyPdfRepository::class)->pdfCreate($detail);
            }

        } catch (Exception $e) {
		    Log::error('request zip create: '.$e->getMessage());
        }
		
		/** 短信 **/
		if ($type == 4) {
			// 无发票直接：新增一条审批
			if (!$arr['has_invoice']) {
				app(ApprovalRepository::class)->sendSms([
					'mobile' => $arr['mobile'],
					'policy_name' => $applyObj['policy_name'],
					'project_name' => $applyObj['project_name'],
				], SMS_TEMPLATE['eighteen']);
			}
			
			// pdf调用
			$applyObj['business_id'] = $apply['business_id'];
			app(PdfRepository::class)->createApprovalPdf($applyObj);

        } else if ($type == 5) {
			// 推送给主审部门和协同部门
			$materialList = $arr['material_list'] ?? [];
			foreach ($materialList as $key => $value) {
				app(ApprovalRepository::class)->sendSms([
					'mobile' => $value['mobile'],
					'policy_name' => $applyObj['policy_name'],
					'project_name' => $applyObj['project_name'],
					'enterprise_name' => $applyObj['enterprise_name']
				], SMS_TEMPLATE['twenty']);
				
			}
		}
		return true;
	}

	public function dealFile($fileList, $arr)
    {
        $tmpArr = [];
        $hasInvoice = false;
        if (!empty($fileList)) {
            foreach ($fileList as $key => $value) {
                // 只需要判断一次即可
                if (!$hasInvoice && $value['file_type'] == MATERIALS_TYPE['invoice']) {
                    $hasInvoice = true;
                }
                $tmpArr[] = [
                    'apply_id' => $arr['id'],
                    'file_name' => $value['file_name'] ?? '',
                    'file_url' => $value['file_url'] ?? '',
                    'file_type' => $value['file_type'] ?? 0,
                    'check_status' => APPLY_CHECK_STATUS['init'],
                    'project_materials_id' => $value['project_materials_id'] ?? 0,
                    'created_at' => time(),
                    'updated_at' => time()
                ];
            }
        }
        return [$tmpArr, $hasInvoice];
    }

    /**
     * FUNCTION_NAME : submitApply
     *
     *
     * @param $applyObj 申报信息
     * @throws QueryException
     */
	public function submitApply($applyObj, $arr)
    {
        // 企业经营信息
        app(EnterpriseBusinessRepository::class)->storeApply([
            "enterprise_id" => $applyObj['enterprise_id'],
            "business_address" => $applyObj['business_address'],
            "business_area" => $applyObj['business_area']
        ]);

        // 人员概况
        app(EnterpriseEmployeeOverviewRepository::class)->storeApply([
            "enterprise_id" => $applyObj['enterprise_id'],
            "employee_number" => $applyObj['employee_number'],
            "employee_degree" => $applyObj['employee_degree'],
            "employee_junior" => $applyObj['employee_junior'],
            "employee_other" => $applyObj['employee_other'],
        ]);

        // 联系人---duty 1 法人 2 单位负责人姓名 3 联系人姓名
        app(EnterpriseLinkmanRepository::class)->storeBatch([
            [
                "enterprise_id" => $applyObj['enterprise_id'],
                "duty" => 1,
                "name" => $applyObj['legal_name'],
                'mobile' => $applyObj['legal_phone'],
                'wechat_number' => $applyObj['legal_wechat']
            ],
            [
                "enterprise_id" => $applyObj['enterprise_id'],
                "duty" => 2,
                "name" => $applyObj['charge_name'],
                'mobile' => $applyObj['charge_phone'],
                'wechat_number' => $applyObj['charge_wechat']
            ],
            [
                "enterprise_id" => $applyObj['enterprise_id'],
                "duty" => 3,
                "name" => $applyObj['contact_name'],
                'mobile' => $applyObj['contact_phone'],
                'wechat_number' => $applyObj['contact_wechat']
            ]
        ]);

        // 无发票直接：新增一条审批
        if (!$arr['has_invoice']) {
            $result = app(ApprovalRepository::class)->storeApproval([
                'apply_id' => $arr['id'],
                'department_id' => $arr['department_id'],
                'approval_type' => APPROVAL_TYPE['one']
            ]);

            $arr['approval_id'] = empty($result['id']) ? 0 : $result['id'];

            // 加上 政策类型 和项目
            $arr = array_merge($arr, array_only($applyObj, ['policy_name', 'project_name']));
            //$arr['staff_id'] = $arr['staff_id'];
            app(ApprovalRepository::class)->sendMessage($arr, APPROVAL_MESSAGE_CONTENT['twentytwo']);
        }

        return $arr;
    }
	
	/**
	 * 发票异常信息列表
	 */
	public function fileExceptionList($search_arr = [])
	{
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        $where = [];
        $where[] = ['f2.apply_id', '=', $search_arr['apply_id']];
//        if (!empty($arr['type'])) {
//            $where[] = ['f1.type', '=', $search_arr['type']];
//        }

        $where[] = ['f1.status', '=', APPLY_EXCEPTION_STATUS['fail']];

        $whereColumn = [
            'ocr',
            'is_truth',
            'repeat_apply',
            'repeat'
        ];

        foreach ($whereColumn as $value) {
            if (!empty($search_arr[$value])) {
                $where[] = ['f1.'.$value, '=', $search_arr[$value]];
            }
        }

        $keyword = trim(array_get($search_arr, 'keyword'));
        $func = [];
        if (!blank($keyword)) {
            $keyword = "%$keyword%";
            $func = function ($query) use ($keyword){
                $query->where('file_name', 'like', $keyword);
                $query->orWhere('invoice_number', 'like', $keyword);
            };
        }



        $column = [
            'f2.id AS file_id',
            'f2.file_name',
            'f2.file_url',
            'f2.invoice_number',
            'f2.invoice_money',
            'f2.invoice_billing_date',
            'f2.invoice_checkcode',
            'f2.invoice_code',
            'f1.remark',
            'f1.ocr',
            'f1.is_year',
            'f1.is_truth',
            'f1.repeat_apply',
            'f1.repeat',
            'f1.created_at'
        ];
        $res = (new ApplyFileExceptionModel())
            ->setTable('f1')
            ->from(ApplyFileExceptionModel::TABLE_NAME . ' AS f1')
            ->join(ApplyFileModel::TABLE_NAME . ' AS f2','f1.apply_file_id','=','f2.id')
            ->where($where)
            ->where($func)
            ->paginate($per_page, $column);
        $data = page($res, $current_page);
        // 应前端要求多加一个invoice
        if (empty($data['data'])) {
            return $data;
        }
        foreach ($data['data'] as $k => &$v) {
            $v['invoice'] = [
                'invoice_number' => $v['invoice_number'],
                'invoice_money' => $v['invoice_money'],
                'invoice_billing_date' => $v['invoice_billing_date'],
                'invoice_checkcode' => $v['invoice_checkcode'],
                'invoice_code' => $v['invoice_code'],
            ];
        }

        return $data;
	}
	
	/**
	 * 详情
	 */
	public function detailByEnterpriseId($arr)
	{
		// 申请表
		$where = ['enterprise_id' => $arr['enterprise_id']];
		$apply = ApplyModel::where($where)
			->orderBy('id', 'desc')
			->limit(1)
			->get([
				'id',
				'employee_number',
				'employee_degree',
				'employee_junior',
				'employee_other',
				'legal_name',
				'legal_phone',
				'legal_wechat',
				'charge_name',
				'charge_phone',
				'charge_wechat',
				'contact_name',
				'contact_phone',
				'contact_wechat',
				'business_content',
				'plan_content',
				'approval_organ',
				'approval_number',        
				'qualifications',
				'provisions',
				'apply_criteria',
				'apply_money',
				'other_notes',
				'config'
			])
			->toArray();
	
		if (empty($apply)) {
			return [];
		}
	
		$apply = $apply[0];
		
		$applyId = $apply['id'];
		unset($apply['id']);
		
		// 经济指标
		$resultEco = ApplyEconomyModel::where(['apply_id' => $applyId])
			->orderBy('year', 'asc')
			->get()
			->toArray();
		
		// 按照日期组装
		$yearList = [];
		foreach ($resultEco as $key => $value) {
			if (!in_array($value['year'], $yearList)) {
				$yearList[] = $value['year'];
			}
		}
		
		// 组装数据
		$economyList = [];
		foreach ($yearList as $key => $value) {
			$tmpList = [];
			foreach ($resultEco as $key2 => $value2) {
				if ($value === $value2['year']) {
					$tmpList[] = $value2;
				}
			}
		
			$economyList[] = [
				'year' => $value,
				'content_list' => $tmpList
			];
		}
		
		//$apply['result_economy'] = $resultEco;
		$apply['economy_list'] = $economyList;
		
		// 附件快照
		$fileConfig = empty($apply['config']) ? [] : json_decode($apply['config'], true);
		
		$resultFile = ApplyFileModel::where(['apply_id' => $applyId])
			->orderBy('id', 'asc')
			->get([
				'id',
				'file_name',
				'file_url',
				'file_type',
				'project_materials_id',
				'created_at',
			])
			->toArray();
		
		if (!empty($resultFile)) {
			foreach ($fileConfig as $key => $value) {
				$tmpList = [];
				foreach ($resultFile as $key2 => $value2) {
					if ($value['id'] === $value2['project_materials_id']) {
						$tmpList[] = $value2;
						unset($resultFile[$key2]);
					}
				}
		
				$value['file_list'] = $tmpList;
				$fileConfig[$key] = $value;
			}
		}
		
		$apply['config'] = $fileConfig;
		
		return $apply;
	}
	
	
	/**
	 * 删除
	 */
	public function deleteApply($arr)
	{
		$applyObj = ApplyModel::where(['id' => $arr['id']])
			->limit(1)
			->get([
				'apply_status'
			])
			->toArray();
			
		if (empty($applyObj)) {
			return Code::CHECK_OPERATE_ERROR;
		}
			
		$applyObj = $applyObj[0];
		
		if ($applyObj['apply_status'] != APPLY_STATUS['one']) {
			return Code::APPLY_DELETE_STATUS_ERROR;
		}

		try{
			ApplyModel::where([
				'id' => $arr['id']
			])->delete();

			return true;
	
		}catch (Exception $e){
			Log::error('apply deleteApply' . $e->getMessage());
			return false;
		}
	}

    /**
     * FUNCTION_NAME : refreshPrecheck
     * author : jp
     * 将 状态 更新到带系统预处理
     * @param $id
     * @return mixed
     */
	public function refreshPrecheck($id)
    {
        $update = ['apply_status' => APPLY_STATUS['two']];
        return $this->model->where('id', $id)->update($update);
    }

    public function getMaxCode()
    {
        // 查询数据最大的编号
        $number = $this->model->withoutGlobalScopes([ SupplementApplyScope::class])->withTrashed()->max('number');
        $number = $number ?? 0;
        return $number+1;
    }

    public function getSupplementById($id, $column = ['*'])
    {
        $res =  $this->model->withoutGlobalScopes([ SupplementApplyScope::class])->supplement()->find($id);
        return empty($res) ? [] : $res->toArray();
    }

    public function updateSupplement($data)
    {
//        $res =  $this->model->withoutGlobalScopes([ SupplementApplyScope::class])->supplement()->where('id', $data['id'])->update(array_except($data, ['id']));
        $res =  $this->model->withoutGlobalScopes([ SupplementApplyScope::class])->supplement()->where('id', $data['id'])->first();
        if (empty($res)) {
            return false;
        }
        foreach (array_except($data, ['id']) as $k => $v) {
            $res->$k = $v;
        }
        $res->save();
        return $res;
    }

    public function deleteSupplement($data)
    {
        $res =  $this->model->withoutGlobalScopes([ SupplementApplyScope::class])->find($data);
        if (empty($res)) {
            return false;
        }
        $e = $res->delete();
        return $e;
    }

    public function supplementList($search_arr, $column= ['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new KeywordCriteria($search_arr, ['enterprise_name','project_name']));
            $this->pushCriteria(new WhereCreatedStartCriteria($search_arr, 'start_time'));
            $this->pushCriteria(new WhereCreatedEndCriteria($search_arr, 'end_time'));
            $this->pushCriteria(new SupplementCriteria([]));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $this->with('staff');
            $res = $this->paginate($per_page, $column);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    /**
     * FUNCTION_NAME : zipCreate
     * 发起zip 事件
     *
     * @param $apply
     */
    public function zipCreate($apply)
    {
        try {
            $fileList = ApplyFileModel::select(['file_url'])->where('apply_id',$apply['id'])->get()->toArray();
            event(new ZipCreate([
                'id' => $apply['id'],
                'urls' => array_column($fileList, 'file_url'),
                'name' => $apply['project_name'].'-'.$apply['enterprise_name'],
                'business_id' => array_get($apply, 'zip_business_id', '')
            ]));
        } catch (Exception $e) {
            Log::error('request zip create: '.$e->getMessage());
        }

    }

    public function simpleDetail($where,$column=['*'])
    {
        $res = $this->model->where($where)->first();
        return empty($res) ?[] : $res->toArray();
    }


    /**
     * FUNCTION_NAME : revocation
     * author : jp
     * 撤销申报
     * @param $id
     * @return bool
     */
    public function revocation($id)
    {
        // 用户撤销 将数据置于空
        $data = [
            'apply_status' => APPLY_STATUS['one'],
            'submit_time' => 0,
            'user_id' => 0,
            'user_name' => '',
            'able_revocation' => APPLY_ABLE_REVOCATION['no'],
        ];
        $res = $this->model->where(['id' => $id])->update($data);
        // 撤销掉发票异常的数据
        app(ApplyFileRepository::class)->refreshInvoice($id);
        app(ApplyFileExceptionRepository::class)->refreshApply($id);
        return $res;
    }

    /**
     * FUNCTION_NAME : setUnAbleRevocation
     * author : jp
     * 设置申报不可撤销
     * @param $id
     * @return bool
     */
    public function setUnAbleRevocation($id)
    {
        return $this->model->where(['id' => $id])->update(['able_revocation' => APPLY_ABLE_REVOCATION['no']]);
    }

    /**
     * FUNCTION_NAME : hasMaterial
     * author : jp
     * 判断是否有补充材料
     * @param $applyId
     * @return int
     */
    public function hasMaterial($applyId)
    {
        $material = ApprovalMaterialModel::where([
            'apply_id' => $applyId
        ])
            ->max('end_time');

        $hasMaterial = false;
        if (!empty($material) && $material >= time()) {
            $hasMaterial = true;
        }

        return $hasMaterial;
    }

    public function clientList($arr, $columns = ['*'])
    {
        $where = [];
        if (!empty($arr['enterprise_id'])) {
            $where[] = ['enterprise_id', '=', $arr['enterprise_id']];
        }

        // status
        if (!empty($arr['apply_status'])) {
            $where[] = ['apply_status', '=', $arr['apply_status']];
        }

        // 运营端
        if (empty($arr['apply_status']) && empty($arr['enterprise_id'])) {
            $where[] = ['apply_status', '!=', APPLY_STATUS['one']];
        }

        if (!empty($arr['mold_id'])) {
            $where[] = ['mold_id', '=', $arr['mold_id']];
        }

        $applyModel = ApplyModel::where($where)->whereNull('deleted_at');
        $count = $applyModel->count();
        $page = commonPage($arr);
        $list = $applyModel
            ->orderBy('created_at', 'desc')
            ->offset($page['offset'])
            ->limit($page['page_size'])
            ->get($columns)
            ->toArray();

        // 查询是否需要补充资料
        if (!empty($list)) {
            $applyIds = [];
            foreach ($list as $key => $value) {
                if (in_array($value['apply_status'], [
                    APPLY_STATUS['five'],
                    APPLY_STATUS['six']
                ])) {
                    $applyIds[] = $value['id'];
                }
            }
            $markIds = [];
            if (!empty($applyIds)) {
                $markList = (new ApprovalMarkModel())
                    ->setTable('f1')
                    ->from(ApprovalMarkModel::TABLE_NAME . ' AS f1')
                    ->join(ApprovalModel::TABLE_NAME . ' AS f2','f2.id','=','f1.approval_id')
                    ->whereIn('f1.type', [APPROVAL_MARK_TYPE['three'], APPROVAL_MARK_TYPE['four']])
                    ->whereIn('f2.apply_id', $applyIds)
                    ->groupBy('f2.apply_id')
                    ->get(['f2.apply_id'])
                    ->toArray();

                if (!empty($markList)) {
                    $markIds = array_column($markList, 'apply_id');
                }
            }

            // 查询是否有 订正资料
            $tmpApplyIds = array_column($list, 'id');
            $hasCorrectIds = app(ApplyCorrectRepository::class)->getUserWaitByIds($tmpApplyIds);

            foreach ($list as $key => $value) {
                $hasMaterial = false;
                if (!empty($markIds) && in_array($value['id'], $markIds)) {
                    $hasMaterial = true;
                }
                $value['has_material'] = $hasMaterial;

                if (in_array($value['id'], $hasCorrectIds)) {
                    $value['has_correct'] = true;

                } else {
                    $value['has_correct'] = false;
                }

                $list[$key] = $value;
            }
        }

        return returnPage2($list, $count, $page);
    }

    public function updateApplyById($id, $arr)
    {
        return $this->model->where('id', $id)->update($arr);
    }

    /**
     * FUNCTION_NAME : approvalStatusFlow
     * author : jp
     * 审批流程状态
     * @param $status
     * @return array|object
     */
    public function approvalStatusFlow($status)
    {
        if ($status == APPLY_STATUS['one']) {
            return (object)[];
        }
        $baseStatus = [
            'wait' => 1,
            'on' => 2,
            'success' => 3,
            'off' => 4,
        ];
        $base = [
            'one' => [
                'status' => $baseStatus['wait']
            ],
            'two' => [
                'status' => $baseStatus['wait']
            ],
            'three' => [
                'status' => $baseStatus['wait']
            ],
            'four' => [
                'status' => $baseStatus['wait']
            ],
        ];
        $base['one']['status'] = $baseStatus['success'];
        if ($status == APPLY_STATUS['two']) {
            // 待系统预处理
            return $base;
        } elseif ($status == APPLY_STATUS['three']) {
            // 待受理
            $base['two']['status'] = $baseStatus['on'];
            return $base;
        } elseif ($status == APPLY_STATUS['four']) {
            // 不受理
            $base['two']['status'] = $baseStatus['off'];
            $base = array_except($base, ['three', 'four']);
            return $base;
        }
        $base['two']['status'] = $baseStatus['success'];

        if (in_array($status,
            [APPLY_STATUS['five'], APPLY_STATUS['six'],APPLY_STATUS['seven']])) {
            // 5待主审部门审核6线下会审中7待指挥部审核
            $base['three']['status'] = $baseStatus['on'];
            return $base;
        } elseif (in_array($status,
            [APPLY_STATUS['ten'], APPLY_STATUS['eleven'],APPLY_STATUS['twelve']])) {
            // 10主审部门不通过11线下会审不通过12指挥部不通过
            $base['three']['status'] = $baseStatus['off'];
            $base = array_except($base, ['four']);
            return $base;
        }

        $base['three']['status'] = $baseStatus['success'];
        $base['four']['status'] = $baseStatus['on'];

        if ($status == APPLY_STATUS['eight']) {
            // 待拨款
            $base['four']['status'] = $baseStatus['on'];
            return $base;
        } elseif ($status == APPLY_STATUS['nine']) {
            // 已拨款
            $base['four']['status'] = $baseStatus['success'];
            return $base;
        }

        return $base;

    }


}
