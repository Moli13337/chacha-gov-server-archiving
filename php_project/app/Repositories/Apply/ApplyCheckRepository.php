<?php
namespace App\Repositories\Apply;

use App\Http\Controllers\Service\OcrService;
use App\Models\ApplyCorrectModel;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Repositories\BaseRepository;
use App\Models\ApplyModel;
use App\Models\ApplyFileModel;
use App\Models\ApplyFileExceptionModel;
use App\Models\ApprovalModel;
use App\Models\StaffModel;
use App\Models\StaffBindDepartmentModel;
use App\Models\ApprovalMaterialModel;
use Illuminate\Support\Facades\Log;
use App\Models\UserModel;
use App\Models\UserEnterpriseRelationModel;

/**
 * 检查脚本
 * @author ASUS
 *
 */
class ApplyCheckRepository  extends BaseRepository
{

	public function model()
	{
		return ApplyModel::class;
	}

    /**
     * FUNCTION_NAME : checkApply
     * author : jp
     * 预检
     * @throws Exception
     */
    public function checkApply() {

        // 补充申报
        $flag = $this->checkSupplement();
        if (!is_array($flag)) {
            echo 'check supplement end';
            return;
        }
        $where = [
            'apply_status' => APPLY_STATUS['two'],
        ];
        $column = [
            'id',
            'policy_name',
            'project_name',
            'enterprise_name',
        ];

        $list = ApplyModel::applyAll()->where($where)
            ->orderBy('number', 'desc')
            ->limit(1)
            ->get($column)
            ->toArray();
        if (empty($list)) {
            echo 'apply data is empty';
            $this->checkCorrect();
            return;
        }
        $applyInfo = $list[0];
        $applyId = $applyInfo['id'];
        // 附件
        $where = [
            'apply_id' => $applyId,
            'file_type' => MATERIALS_TYPE['invoice'],
            'check_status' => APPLY_CHECK_STATUS['init']
        ];
        $column = [
            'id',
            'apply_id',
            'file_url',
            'invoice_number',
            'invoice_money',
            'invoice_billing_date',
            'invoice_checkcode',
            'invoice_code'
        ];
        $resultFile = ApplyFileModel::where($where)
            ->orderBy('id', 'DESC')
            ->get($column)
            ->toArray();

        // 待检查发票不为空的时候去 检查发票
        if (!empty($resultFile)) {
           $flag = $this->handleInvoice($resultFile, $applyId);

           if (is_string($flag)) {
                return $flag;
           }

        }
        // 查询区业务服务部门操作员
        $staff = app(ApprovalDepartmentRepository::class)->getStaff();
        if (empty($staff)) {
            echo 'department one manager is empty';
//            return;
        }
        // 3、名称重复检查: 只能等全部检验完毕之后二次查询最新状态才能进行统计判定
        $this->handleRepeatInvoice($applyId, $applyInfo, $staff);
        echo 'check over';
    }

    /**
     * FUNCTION_NAME : handleInvoice
     * author : jp
     * 处理发票
     * @param $resultFile
     * @param $applyId
     * @return string
     */
    public function handleInvoice($resultFile, $applyId)
    {
        if (empty($resultFile)) {
            return $resultFile;
        }
        // 待检查发票不为空的时候去 检查发票

        try {
            // 预处理发票
            foreach ($resultFile as $key => $value) {
                if (empty($value['file_url'])) {
                    // 异常
                    $this->updateApplyFile([
                        'id' => $value['id']
                    ], [
                        'check_status' => APPLY_CHECK_STATUS['error']
                    ]);
                    continue;
                }

                // 1、识别发票
                $value = $this->handleOcrInvoice($value, $applyId);
                if (empty($value['invoice_number'])) {
                    continue;
                }

                // 是否一年内
                // 2、假发票-阿里
                $this->handleTruthInvoice($value, $applyId);

                // 4、其他项目使用重复检查
                $repeat = $this->handleRepeatApplyInvoice($value, $applyId);
                if ($repeat) {
                    continue;
                }
                // 正常可用
                $this->updateApplyFile([
                    'id' => $value['id']
                ], [
                    'check_status' => APPLY_CHECK_STATUS['normal']
                ]);
            }

        }catch (Exception $e){
            Log::error('checkapply' . $e->getMessage());
            return 'check one two error';
        }

    }

    /**
     * FUNCTION_NAME : handleOcrInvoice
     * author : jp
     * 处理ocr 发票
     * @param $params
     * @param $applyId
     * @return array
     * @throws \App\Exceptions\QueryException
     */
    public function handleOcrInvoice($params, $applyId)
    {
        try {
            $result = app(YoutuRepository::class)->checkInvoice($params['file_url']);
        } catch (\Exception $e) {
            $result = [];
            $err = $e->getMessage();
        }

        // 发票代码 发票编号 开票日期 金额 全部拿到才算是识别成功
        $keys = [
            'invoice_billing_date',
            'invoice_code',
            'invoice_number',
            'invoice_money',
        ];
        $flag = true;
        foreach ($keys as $v) {
            if (empty($result[$v])) {
                $flag = false;
                break;
            }
        }

        $invoice = array_only($result, $keys);


        if (!$flag) {
            // 更新状态-异常
            $this->updateApplyFile([
                'id' => $params['id']
            ], array_merge($invoice, [
                'check_status' => APPLY_CHECK_STATUS['error']
            ]));

            if (empty($err)) {
                $remark  = '识别失败';
            } else {
                $remark  = $err;
            }

            $data = [
                'apply_id' => $applyId,
                'apply_file_id' => $params['id'],
                'ocr' => APPLY_EXCEPTION_OCR['fail'],
                'status' => APPLY_EXCEPTION_STATUS['fail'],
                'remark' => $remark,
            ];
            // 识别失败
            $this->createOrUpdateException($applyId, $data);
        } else {
            $this->updateApplyFile([
                'id' => $params['id']
            ], $invoice);
            $data = [
                'apply_id' => $applyId,
                'apply_file_id' => $params['id'],
                'ocr' => APPLY_EXCEPTION_OCR['success'],
            ];
            $this->createOrUpdateException($applyId, $data);
        }
        return array_merge($params, $result);
    }

    /**
     * FUNCTION_NAME : handleYearInvoice
     * author : jp
     * 判断一年内发票
     * @param $params
     * @param $applyId
     * @return bool
     */
    public function handleYearInvoice($params, $applyId)
    {
        if (empty($params['invoice_billing_date'])) {
            // 异常
            $this->updateApplyFile([
                'id' => $params['id'],
            ], [
                'check_status' => APPLY_CHECK_STATUS['error']
            ]);
            return false;
        }
        $year = strtotime( '-1 years');
        if ($year > strtotime($params['invoice_billing_date'])) {

            // 异常
            $this->updateApplyFile([
                'id' => $params['id'],
            ], [
                'check_status' => APPLY_CHECK_STATUS['error']
            ]);

            $data = [
                'apply_id' => $applyId,
                'apply_file_id' => $params['id'],
                'is_year' => APPLY_EXCEPTION_YEAR['not'],
                'is_truth' => APPLY_EXCEPTION_TRUTH['or'],
                'status' => APPLY_EXCEPTION_STATUS['fail'],
            ];
            $this->createOrUpdateException($applyId, $data);
            return false;
        } else {
            $data = [
                'apply_id' => $applyId,
                'apply_file_id' => $params['id'],
                'is_year' => APPLY_EXCEPTION_YEAR['yes'],
            ];
            $this->createOrUpdateException($applyId, $data);
        }
        return true;
    }

    /**
     * FUNCTION_NAME : handleTruthInvoice
     * author : jp
     * 发票是真假
     * @param $params
     * @param $applyId
     * @throws \App\Exceptions\QueryException
     */
    public function handleTruthInvoice($params, $applyId)
    {
        $res = $this->handleYearInvoice($params, $applyId);
        if (!$res) {
            return;
        }
        $resultAli = app(AliyunRepository::class)->checkInvoice($params);
//        Log::error('alicheck: ' . json_encode($params) . '    ' . $resultAli);
        if (!$resultAli) {
            // 更新状态-异常
            $this->updateApplyFile([
                'id' => $params['id']
            ], [
                'check_status' => APPLY_CHECK_STATUS['error']
            ]);

            $data = [
                'apply_id' => $applyId,
                'apply_file_id' => $params['id'],
                'is_truth' => APPLY_EXCEPTION_TRUTH['not'],
                'status' => APPLY_EXCEPTION_STATUS['fail'],
            ];
            $this->createOrUpdateException($applyId, $data);
        } else {
            $data = [
                'apply_id' => $applyId,
                'apply_file_id' => $params['id'],
                'is_truth' => APPLY_EXCEPTION_TRUTH['yes'],
            ];
            $this->createOrUpdateException($applyId, $data);
        }
    }

    /**
     * FUNCTION_NAME : handleRepeatApplyInvoice
     * author : jp
     * 重复检查发票（其他项目）
     * @param $params
     * @param $applyId
     * @return bool
     * @throws \App\Exceptions\QueryException
     */
    public function handleRepeatApplyInvoice($params, $applyId)
    {
        $where = [];
        $where[] = ['invoice_number', '=', $params['invoice_number']];
        $where[] = ['apply_id', '<>', $params['apply_id']];
        $result = ApplyFileModel::where($where)
            ->limit(1)
            ->get(['id', 'apply_id'])
            ->toArray();

        if (empty($result)) {
            // 其他项目使用重复检查
            $data = [
                'apply_id' => $applyId,
                'apply_file_id' => $params['id'],
                'repeat_apply' => APPLY_EXCEPTION_REPEAT_APPLY['no'],
            ];
            $this->createOrUpdateException($applyId, $data);
            return false;
        }

        // 查询具体申请表信息
        $applyOne = ApplyModel::applyAll()->where(['id' => $result[0]['apply_id']])
            ->limit(1)
            ->get(['enterprise_name', 'project_name'])
            ->toArray();

        $enterprise_name = empty($applyOne[0]['enterprise_name']) ? '' : $applyOne[0]['enterprise_name'];
        $project_name = empty($applyOne[0]['project_name']) ? '' : $applyOne[0]['project_name'];

        $remark = '';
        if (!empty($enterprise_name) && !empty($project_name)) {
            $remark = '本张发票在'.$enterprise_name.'企业'.$project_name.'项目申报中使用过';

            // 更新状态-异常
            $this->updateApplyFile([
                'id' => $params['id']
            ], [
                'check_status' => APPLY_CHECK_STATUS['error']
            ]);

            // 其他项目使用重复检查
            $data = [
                'apply_id' => $applyId,
                'apply_file_id' => $params['id'],
                'repeat_apply' => APPLY_EXCEPTION_REPEAT_APPLY['yes'],
                'status' => APPLY_EXCEPTION_STATUS['fail'],
                'remark' => $remark,
            ];
            $this->createOrUpdateException($applyId, $data);
        } else {
            // 其他项目使用重复检查
            $data = [
                'apply_id' => $applyId,
                'apply_file_id' => $params['id'],
                'repeat_apply' => APPLY_EXCEPTION_REPEAT_APPLY['no'],
            ];
            $this->createOrUpdateException($applyId, $data);
        }

        return true;
    }

    /**
     * FUNCTION_NAME : handleRepeatInvoice
     * author : jp
     * 重复检查发票（当次提交）
     * @param $applyId
     * @param $applyInfo
     * @param $staff
     * @return string
     * @throws Exception
     */
    public function handleRepeatInvoice($applyId, $applyInfo, $staff)
    {
        $where = [
            'apply_id' => $applyId,
            'file_type' => MATERIALS_TYPE['invoice']
        ];
        $result = ApplyFileModel::where($where)
            ->orderBy('id', 'asc')
            ->get([
                'id',
                'invoice_number',
                'check_status'
            ])
            ->toArray();
        // 查询是否已经创建过流程
        $existApproval = app(ApprovalRepository::class)->getByApply($applyId);
        if (empty($result)) {
            echo 'apply file invoice is empty';
            // 需要将没有发表的变更为下个状态
            DB::beginTransaction();

            try{

                // 更新申请表信息
                ApplyModel::where(['id' => $applyId])
                    ->update(['apply_status' => APPLY_STATUS['three']]);

                // 创建企服部门审批流程
                if (empty($existApproval)) {
                    $existApproval = app(ApprovalRepository::class)->storeApproval([
                        'apply_id' => $applyId,
                        'department_id' => $staff['department_id'],
                        'approval_type' => APPROVAL_TYPE['one']
                    ]);
                }
                // 发送消息通知
                app(ApprovalRepository::class)->sendMessage([
                    'policy_name' => $applyInfo['policy_name'],
                    'project_name' => $applyInfo['project_name'],
                    'enterprise_name' => $applyInfo['enterprise_name'],
                    'staff_id' => $staff['staff_id'],
                    'approval_id' => $existApproval['id']
                ], APPROVAL_MESSAGE_CONTENT['twentytwo']);
                DB::commit();
            }catch (Exception $e){
                DB::rollBack();
                echo 'db error';
                return false;
            }
            /** 短信 **/
            app(ApprovalRepository::class)->sendSms([
                'policy_name' => $applyInfo['policy_name'],
                'project_name' => $applyInfo['project_name'],
                'mobile' => $staff['mobile']
            ], SMS_TEMPLATE['eighteen']);
            return true;
        }

        // 根据正常 + 异常的 总数 = 附件总数 来判断校验完毕
        $tmpCount = 0;
        foreach ($result as $key => $value) {
            if (in_array($value['check_status'], [APPLY_CHECK_STATUS['error'], APPLY_CHECK_STATUS['normal']])) {
                $tmpCount++;
            }
        }
        //没有检查完毕继续循环检查
        if ($tmpCount != count($result)) {
            echo 'not check over,continue check';
            return false;
        }
        // 判断是否全部检查完毕：检查完毕就去检查名称重复
        $numberList = [];
        foreach ($result as $key => $value) {
            if (!empty($value['invoice_number'])) {
                $numberList[] = $value['invoice_number'];
            }
        }
// 		$numberList = array_column($resultFile2, 'invoice_number');
        $countList = array_count_values($numberList);

        // 找出重复信息
        $repeatNumber = [];
        foreach ($countList as $key => $value) {
            if ($value > 1) {
                $repeatNumber[] = $key;
            }
        }

        $repeatIds = [];
        $repeatData = [];
        foreach ($result as $key => $value) {
            if (empty($value['invoice_number'])) {
                continue;
            } elseif (!in_array($value['invoice_number'], $repeatNumber)) {
                $temp = [
                    'apply_id' => $applyId,
                    'apply_file_id' => $value['id'],
                    'repeat' => APPLY_EXCEPTION_REPEAT['no'],
                    'status' => APPLY_EXCEPTION_STATUS['success'],
                ];
                $this->createOrUpdateException($applyId, $temp);
                continue;
            }
            $repeatIds[] = $value['id'];
            $temp = [
                'apply_id' => $applyId,
                'apply_file_id' => $value['id'],
                'repeat' => APPLY_EXCEPTION_REPEAT['yes'],
                'status' => APPLY_EXCEPTION_STATUS['fail'],
            ];

            // 新增异常信息
            $this->createOrUpdateException($applyId, $temp);
            unset($result[$key]);
        }

        // 查询是否已经创建过流程
        $result = app(ApprovalRepository::class)->getByApply($applyId);

        // 事务保持同步，不然无法保证全部检查完毕
        DB::beginTransaction();

        try{
            if (!empty($repeatIds)) {
                // 更新附件信息
                ApplyFileModel::whereIn('id', $repeatIds)
                    ->update(['check_status' => APPLY_CHECK_STATUS['error']]);
            }

            // 更新申请表信息
            ApplyModel::where(['id' => $applyId])
                ->update(['apply_status' => APPLY_STATUS['three']]);

            //

            // 创建企服部门审批流程
            if (empty($result)) {
                $result = app(ApprovalRepository::class)->storeApproval([
                    'apply_id' => $applyId,
                    'department_id' => $staff['department_id'],
                    'approval_type' => APPROVAL_TYPE['one']
                ]);
            }

            // 发送消息通知
            app(ApprovalRepository::class)->sendMessage([
                'policy_name' => $applyInfo['policy_name'],
                'project_name' => $applyInfo['project_name'],
                'enterprise_name' => $applyInfo['enterprise_name'],
                'staff_id' => $staff['staff_id'],
                'approval_id' => $result['id']
            ], APPROVAL_MESSAGE_CONTENT['twentytwo']);

            DB::commit();
        }catch (Exception $e){
            DB::rollBack();
            echo 'db error';
            return false;
        }
        /** 短信 **/
        app(ApprovalRepository::class)->sendSms([
            'policy_name' => $applyInfo['policy_name'],
            'project_name' => $applyInfo['project_name'],
            'mobile' => $staff['mobile']
        ], SMS_TEMPLATE['eighteen']);
        return true;
    }


    /**
	 * 预处理
	 * 企业发票信息预审
	 */
//	public function checkApply() {
//	    $img = 'https://base-policy.oss-cn-beijing.aliyuncs.com/local/20190830/c/2pJerm1ES8liKSwLtqrl4ZpyrhGeoSZD5Z5N1u5m.jpg';
//	    app(OcrService::class)->vatInvoice($img);
//	    exit;
//		$where = [
//			'apply_status' => APPLY_STATUS['two']
//		];
//
//		$list = ApplyModel::where($where)
//			->orderBy('number', 'desc')
//			->limit(1)
//			->get([
//				'id',
//				'policy_name',
//				'project_name',
//				'enterprise_name'
//			])
//			->toArray();
//
//		if (empty($list)) {
//			return 'apply data is empty';
//		}
//
//		$applyInfo = $list[0];
//
//		$applyId = $applyInfo['id'];
//
//		// 附件
//		$where = [
//			'apply_id' => $applyId,
//			'file_type' => MATERIALS_TYPE['invoice'],
//			'check_status' => APPLY_CHECK_STATUS['init']
//		];
//		$resultFile = ApplyFileModel::where($where)
//			->orderBy('id', 'asc')
//			->get([
//				'id',
//				'apply_id',
//				'file_url',
//				'invoice_number',
//				'invoice_money',
//				'invoice_billing_date',
//				'invoice_checkcode',
//				'invoice_code'
//			])
//			->toArray();
//
//		// 待检查发票不为空的时候去 检查发票
//		if (!empty($resultFile)) {
//
//			try {
//
//				// 预处理发票
//				foreach ($resultFile as $key => $value) {
//					if (empty($value['file_url'])) {
//						// 异常
//						$this->updateApplyFile([
//							'id' => $value['id']
//						], [
//							'check_status' => APPLY_CHECK_STATUS['error']
//						]);
//						continue;
//					}
//
//					// 1、识别发票
//					if (empty($value['invoice_number'])) {
//						$result = app(YoutuRepository::class)->checkInvoice($value['file_url']);
//						if (empty($result['invoice_number'])) {
//							// 更新状态-异常
//							$this->updateApplyFile([
//								'id' => $value['id']
//							], [
//								'check_status' => APPLY_CHECK_STATUS['error']
//							]);
//
//							// 识别失败
//							ApplyFileExceptionModel::insert([
//								'apply_id' => $applyId,
//								'apply_file_id' => $value['id'],
//								'type' => APPLY_EXCEPTION_TYPE['one'],
//								'create_at' => time()
//							]);
//							continue;
//						}
//						// 更新发票编号
//						$this->updateApplyFile([
//							'id' => $value['id']
//						], [
//							'invoice_number' => $result['invoice_number'],
//							'invoice_money' => $result['invoice_money'] ?? '',
//							'invoice_billing_date' => $result['invoice_billing_date'] ?? '',
//							'invoice_checkcode' => $result['invoice_checkcode'] ?? '',
//							'invoice_code' => $result['invoice_code'] ?? ''
//						]);
//
//						$value = array_merge($value, $result);
//					}
//
//					// 2、假发票-阿里
//					$resultAli = app(AliyunRepository::class)->checkInvoice($value);
//					Log::error('alicheck: ' . json_encode($value) . '    ' . $resultAli);
//					if (!$resultAli) {
//						// 更新状态-异常
//						$this->updateApplyFile([
//							'id' => $value['id']
//						], [
//							'check_status' => APPLY_CHECK_STATUS['error']
//						]);
//
//						// 识别失败
//						ApplyFileExceptionModel::insert([
//							'apply_id' => $applyId,
//							'apply_file_id' => $value['id'],
//							'type' => APPLY_EXCEPTION_TYPE['two'],
//							'create_at' => time()
//						]);
//						//continue;
//					}
//
//					// 4、其他项目使用重复检查
//					$whereFour = [];
//					$whereFour[] = ['invoice_number', '=', $value['invoice_number']];
//					$whereFour[] = ['apply_id', '<>', $value['apply_id']];
//					$resultFour = ApplyFileModel::where($whereFour)
//						->limit(1)
//						->get(['id', 'apply_id'])
//						->toArray();
//
//					if (!empty($resultFour)) {
//						// 更新状态-异常
//						$this->updateApplyFile([
//							'id' => $value['id']
//						], [
//							'check_status' => APPLY_CHECK_STATUS['error']
//						]);
//
//						// 查询具体申请表信息
//						$applyOne = ApplyModel::where(['id' => $resultFour[0]['apply_id']])
//							->limit(1)
//							->get(['enterprise_name', 'project_name'])
//							->toArray();
//
//						$enterprise_name = empty($applyOne[0]['enterprise_name']) ? '' : $applyOne[0]['enterprise_name'];
//						$project_name = empty($applyOne[0]['project_name']) ? '' : $applyOne[0]['project_name'];
//
//						$remark = '';
//						if (!empty($enterprise_name) && !empty($project_name)) {
//							$remark = '本张发票在'.$enterprise_name.'企业'.$project_name.'项目申报中使用过';
//						}
//						// 其他项目使用重复检查
//						ApplyFileExceptionModel::insert([
//							'apply_id' => $applyId,
//							'apply_file_id' => $value['id'],
//							'type' => APPLY_EXCEPTION_TYPE['four'],
//							'remark' => $remark,
//							'create_at' => time()
//						]);
//						continue;
//					}
//
//					// 正常可用
//					$this->updateApplyFile([
//						'id' => $value['id']
//					], [
//						'check_status' => APPLY_CHECK_STATUS['normal']
//					]);
//				}
//
//			}catch (Exception $e){
//				Log::error('checkapply' . $e->getMessage());
//				return 'check one two error';
//			}
//
//		}
//
//		// 3、名称重复检查: 只能等全部检验完毕之后二次查询最新状态才能进行统计判定
//		$where = [
//			'apply_id' => $applyId,
//			'file_type' => MATERIALS_TYPE['invoice']
//		];
//		$resultFile2 = ApplyFileModel::where($where)
//			->orderBy('id', 'asc')
//			->get([
//				'id',
//				'invoice_number',
//				'check_status'
//			])
//			->toArray();
//
//		if (empty($resultFile2)) {
//			return 'apply file invoice is empty';
//		}
//
//		// 查询区业务服务部门操作员
//		$staff = app(ApprovalDepartmentRepository::class)->getStaff();
//		if (empty($staff)) {
//			return 'department one manager is empty';
//		}
//
//		// 根据正常 + 异常的 总数 = 附件总数 来判断校验完毕
//		$tmpCount = 0;
//		foreach ($resultFile2 as $key => $value) {
//			if (in_array($value['check_status'], [APPLY_CHECK_STATUS['error'], APPLY_CHECK_STATUS['normal']])) {
//				$tmpCount++;
//			}
//		}
//
//		//没有检查完毕继续循环检查
//		if ($tmpCount != count($resultFile2)) {
//			return 'not check over,continue check';
//		}
//
//		// 判断是否全部检查完毕：检查完毕就去检查名称重复
//		$numberList = [];
//		foreach ($resultFile2 as $key => $value) {
//			if (!empty($value['invoice_number'])) {
//				$numberList[] = $value['invoice_number'];
//			}
//		}
//// 		$numberList = array_column($resultFile2, 'invoice_number');
//		$countList = array_count_values($numberList);
//
//		// 找出重复信息
//		$repeatNumber = [];
//		foreach ($countList as $key => $value) {
//			if ($value > 1) {
//				$repeatNumber[] = $key;
//			}
//		}
//
//		$repeatIds = [];
//		$repeatData = [];
//		foreach ($resultFile2 as $key => $value) {
//			if (!in_array($value['invoice_number'], $repeatNumber)) continue;
//
//			$repeatIds[] = $value['id'];
//
//			$repeatData[] = [
//				'apply_id' => $applyId,
//				'apply_file_id' => $value['id'],
//				'type' => APPLY_EXCEPTION_TYPE['three'],
//				'create_at' => time()
//			];
//
//			unset($resultFile2[$key]);
//		}
//
//		// 事务保持同步，不然无法保证全部检查完毕
//		DB::beginTransaction();
//
//		try{
//			if (!empty($repeatIds)) {
//				// 更新附件信息
//				ApplyFileModel::whereIn('id', $repeatIds)
//					->update(['check_status' => APPLY_CHECK_STATUS['error']]);
//
//				// 新增异常信息
//				ApplyFileExceptionModel::insert($repeatData);
//			}
//
//			// 更新申请表信息
//			ApplyModel::where(['id' => $applyId])
//				->update(['apply_status' => APPLY_STATUS['three']]);
//
//			// 创建企服部门审批流程
//			$result = app(ApprovalRepository::class)->storeApproval([
//				'apply_id' => $applyId,
//				'department_id' => $staff['department_id'],
//				'approval_type' => APPROVAL_TYPE['one']
//			]);
//
//			// 发送消息通知
//			app(ApprovalRepository::class)->sendMessage([
//				'policy_name' => $applyInfo['policy_name'],
//				'project_name' => $applyInfo['project_name'],
//				'enterprise_name' => $applyInfo['enterprise_name'],
//				'staff_id' => $staff['staff_id'],
//				'approval_id' => $result['id']
//			], APPROVAL_MESSAGE_CONTENT['twentytwo']);
//
//			DB::commit();
//		}catch (Exception $e){
//			DB::rollBack();
//			return 'db error';
//		}
//
//		/** 短信 **/
//		app(ApprovalRepository::class)->sendSms([
//			'policy_name' => $applyInfo['policy_name'],
//			'project_name' => $applyInfo['project_name'],
//			'mobile' => $staff['mobile']
//		], SMS_TEMPLATE['eighteen']);
//
//		return 'check over';
//	}
	
	/**
	 * 发票文件更改
	 */
	private function updateApplyFile($where = [], $update = []) {
		return ApplyFileModel::where($where)->update($update);
	}

    /**
     * FUNCTION_NAME : createOrUpdateException
     * author : jp
     * 创建or更新异常
     * @param $applyId
     * @param $arr
     * @throws \App\Exceptions\QueryException
     */
	private function createOrUpdateException($applyId,$arr)
    {
        $where = [
            'apply_id' => $applyId,
            'apply_file_id' => $arr['apply_file_id'],
        ];
        $res = app(ApplyFileExceptionRepository::class)->getByFile($where);

        if (!$res) {
            $arr['created_at'] = time();
            app(ApplyFileExceptionRepository::class)->storeRepository($arr);
        } else {
            $remark = explode('；', array_get($res, 'remark', ''));
            $remark[] = array_get($arr, 'remark', '');
            $update = array_merge($res, array_except($arr,['']));
            unset($update['updated_at']);
            unset($update['created_at']);
            if (array_get($res, 'status') == APPLY_EXCEPTION_STATUS['fail']) {
                $update['status'] = APPLY_EXCEPTION_STATUS['fail'];
            }
            $update['remark'] = implode('；',array_unique($remark));
            app(ApplyFileExceptionRepository::class)->updateRepository($update);
        }

    }

	/**
	 * 主审部门的审核
	 * 距离主审部门审核截止时间前三天和前一天，分别去检测主审部门是否已经完成审核（根据当时的申报审核状态判断即可），如果未完成审核（
	 * 即申报状态仍处于待主审部门审核）。需要给主审部门发送审核通知：
	 * 通知内容：本部门在关于xxx政策类型的xxx项目的申报中，距离本部门审核截止日期还剩3/1天了，请尽快去审核。
	 * 
	 * 协调部门的审核
	 * 距离部门评审截止时间前三天和前一天，分别去检测协同部门是否已经提交意见，如果未提交意见。需要给协同部门发送审核通知：
	 * 通知内容：本部门在关于xxx政策类型的xxx项目的申报中，距离本部门审核截止日期还剩3/1天了，请尽快去审核。
	 * 
	 * 系统根据管委会办公室评审截止时间计时，到期前三天和前一天进行提醒。
	 */
	public function checkApproval() 
	{
		$typeArr = [APPROVAL_TYPE['two'], APPROVAL_TYPE['three'], APPROVAL_TYPE['five']];
		$currentTime = strtotime(date('Y-m-d'));

		$where = [];
		$where[] = ['status', '=', APPROVAL_STATUS['one']];
		$where[] = ['end_time', '<', $currentTime + 345600]; // 预计结束时间3天之内的-查到4天后的00:00:00
		$where[] = ['end_time', '>=', $currentTime];
		
		$list = ApprovalModel::where($where)
			->whereIn('type', $typeArr)
			->get([
				'id AS approval_id', 
				'apply_id', 
				'department_id',
				'end_time', 
				'type'
			])
			->toArray();

		if (empty($list)) {
			return 'approval is empty';
		}
		
		// 查询部门操作人员
		$departmentList = array_unique(array_column($list, 'department_id'));
		
		$staffList = (new StaffModel())
			->setTable('f1')
			->from(StaffModel::TABLE_NAME . ' AS f1')
			->join(StaffBindDepartmentModel::TABLE_NAME . ' AS f2','f2.staff_id','=','f1.id')
// 			->join(StaffDepartmentModel::TABLE_NAME . ' AS f3','f3.id','=','f2.department_id')
			->where('f2.opertor_type', STAFF_OPERTOR_TYPE['one'])
			->whereIn('f2.department_id', $departmentList)
			->orderBy('f1.number', 'asc')
			->get(['f2.staff_id', 'f2.department_id', 'f1.mobile'])
			->toArray();
		
		$departmentArr = [];
		foreach ($staffList as $key => $value) {
			$departmentArr[$value['department_id']] = $value;
		}
		
		// 查询申请表信息
		$applyIdList = array_unique(array_column($list, 'apply_id'));
		$applyList = ApplyModel::whereIn('id', $applyIdList)
			->get([
				'id', 
				'policy_name', 
				'project_name',
				'enterprise_name',
				'enterprise_id'
			])
			->toArray();
		
		$applyArr = [];
		foreach ($applyList as $key => $value) {
			$applyArr[$value['id']] = $value;
		}

		foreach ($list as $key => $value) {
			$time = 0;
			// 1天 
			if ($value['end_time'] >= ($currentTime + 86400) 
				&& $value['end_time'] < ($currentTime + 172800)) {
				$time = 1;
			} else if ($value['end_time'] >= ($currentTime + 259200) 
				&& $value['end_time'] < ($currentTime + 345600) ) {
				// 3天
				$time = 3;
			} else {
				continue;
			}
			
			if (empty($departmentArr[$value['department_id']])) {
				continue;
			}
			if (empty($applyArr[$value['apply_id']])) {
				continue;
			}
			$staffInfo = $departmentArr[$value['department_id']];
			$applyInfo = $applyArr[$value['apply_id']];

			$messageType = 0;
			if ($value['type'] == APPROVAL_TYPE['two']) {
				$messageType = APPROVAL_MESSAGE_CONTENT['twentythree'];
			} else if ($value['type'] == APPROVAL_TYPE['three']) {
				$messageType = APPROVAL_MESSAGE_CONTENT['twentyfour'];
			} else {
				$messageType = APPROVAL_MESSAGE_CONTENT['twentysix'];
			}

			// 发通知
			app(ApprovalRepository::class)->sendMessage([
				'policy_name' => $applyInfo['policy_name'],
				'project_name' => $applyInfo['project_name'],
				'enterprise_name' => $applyInfo['enterprise_name'],
				'staff_id' => $staffInfo['staff_id'],
				'approval_id' => $value['approval_id'],
				'time' => $time
			], $messageType);

			/** 短信 **/
			app(ApprovalRepository::class)->sendSms([
				'policy_name' => $applyInfo['policy_name'],
				'project_name' => $applyInfo['project_name'],
				'mobile' => $staffInfo['mobile'],
				'time' => $time
			], SMS_TEMPLATE['nineteen']);
		}
		
		return 'check over';
	}
	
	/**
	 * 主审部门和协同部门的补充资料发送通知
	 * 截止时间前24小时发送一次
	 */
	public function checkMaterial()
	{
		$currentTime = strtotime(date('Y-m-d'));
	
		$where = [];
		$where[] = ['status', '=', MATERIAL_SEND_STATUS['one']];
		$where[] = ['end_time', '<', $currentTime + 172800]; // 1天以内的再发一次
		$where[] = ['end_time', '>=', $currentTime];
		
		$list = ApprovalMaterialModel::where($where)
			->get([
				'id',
				'apply_id', 
				'enterprise_id', 
				'mark',
				'start_time',
				'end_time'
			])
			->toArray();
	
		if (empty($list)) {
			return 'repeat send is empty';
		}
		
		// 查询申请表信息
		$applyIds = array_unique(array_column($list, 'apply_id'));
		
		$applyList = ApplyModel::whereIn('id', $applyIds)
			->get([
				'id',
				'policy_name',
				'project_name',
				'enterprise_name'
			])
			->toArray();
			
		if (empty($applyList)) {
			return 'apply list is empty';
		}

		$enterpriseIds = array_unique(array_column($list, 'enterprise_id'));
		
		// 企业信息
		$userList = (new UserModel())
			->setTable('f1')
			->from(UserModel::TABLE_NAME . ' AS f1')
			->join(UserEnterpriseRelationModel::TABLE_NAME . ' AS f2','f2.user_id','=','f1.id')
			->whereIn('f2.enterprise_id', $enterpriseIds)
			->whereRaw('f1.deleted_at is null')
			->get(['f2.enterprise_id', 'f2.user_id', 'f1.mobile'])
			->toArray();
			
		if (empty($userList)) {
			return 'enterprise user is empty';
		}

		foreach ($userList as $key => $value) {
			$userList[$value['enterprise_id']] = $value;
		}

		foreach ($applyList as $key => $value) {
			$applyList[$value['id']] = $value;
		}

		foreach ($list as $key => $value) {
			// 企业信息
			if (empty($userList[$value['enterprise_id']])) {
				continue;
			}
			
			if (empty($applyList[$value['apply_id']])) {
				continue;
			}
			
			$enterUser = $userList[$value['enterprise_id']];
			$apply = $applyList[$value['apply_id']];
			unset($apply['id']);
			$tmpData = array_merge($value, $apply, $enterUser);

			app(ApprovalRepository::class)->sendMessage($tmpData, APPROVAL_MESSAGE_CONTENT['seven']);

			/** 短信 **/
			// 发企业
			$arr['mobile'] = $enterUser['mobile'];
			app(ApprovalRepository::class)->sendSms($tmpData, SMS_TEMPLATE['six']);
		
			// 更新状态
			ApprovalMaterialModel::where([
				'id' => $value['id']
			])->update([
				'status' => MATERIAL_SEND_STATUS['two'],
			]);
		}
		
		return 'check over';
	}

	public function checkSupplement()
    {
        $where = [
            'audit_status' => PRE_AUDIT_STATUS['wait'],
        ];
        $column = [
            'id',
            'policy_name',
            'project_name',
            'enterprise_name',
            'audit_status'
        ];

        $list = ApplyModel::applyAll()->supplement()->where($where)
            ->orderBy('number', 'desc')
            ->limit(1)
            ->get($column)
            ->toArray();
//        dd($list);
        if (empty($list)) {
            echo 'apply supplement data is empty';
            return [];
        }
        $applyInfo = $list[0];
        $applyId = $applyInfo['id'];
        // 附件
        $where = [
            'apply_id' => $applyId,
            'file_type' => MATERIALS_TYPE['invoice'],
            'check_status' => APPLY_CHECK_STATUS['init']
        ];
        $column = [
            'id',
            'apply_id',
            'file_url',
            'invoice_number',
            'invoice_money',
            'invoice_billing_date',
            'invoice_checkcode',
            'invoice_code'
        ];
        $resultFile = ApplyFileModel::where($where)
            ->orderBy('id', 'DESC')
            ->get($column)
            ->toArray();

        $update = [
            'id' => $applyId,
            'audit_status' => PRE_AUDIT_STATUS['already'],
        ];

        if (empty($resultFile)) {
            app(ApplyRepository::class)->updateSupplement($update);
            return;
        }

        foreach ($resultFile as $key => $value) {
            if (empty($value['file_url'])) {
                // 异常
                $this->updateApplyFile([
                    'id' => $value['id']
                ], [
                    'check_status' => APPLY_CHECK_STATUS['error']
                ]);
                continue;
            }
            $value = $this->supplementHandleOcrInvoice($value, $applyId);
            $resultFile[$key] = $value;
            if (empty($value['invoice_number'])) {
                continue;
            }
        }
        app(ApplyRepository::class)->updateSupplement($update);
        $this->handleSupplementRepeatInvoice($applyId);

    }

    public function supplementHandleOcrInvoice($params, $applyId)
    {
        try {
            $result = app(YoutuRepository::class)->checkInvoice($params['file_url']);
        } catch (\Exception $e) {
            $result = [];
            $err = $e->getMessage();
        }

        // 发票代码 发票编号 开票日期 金额 全部拿到才算是识别成功
        $keys = [
            'invoice_number',
        ];
        $flag = true;
        foreach ($keys as $v) {
            if (empty($result[$v])) {
                $flag = false;
                break;
            }
        }
        $keys = [
            'invoice_billing_date',
            'invoice_code',
            'invoice_number',
            'invoice_money',
        ];
        $invoice = array_only($result, $keys);
        if (!$flag) {
            // 更新状态-异常
            $this->updateApplyFile([
                'id' => $params['id']
            ], array_merge($invoice, [
                'check_status' => APPLY_CHECK_STATUS['error']
            ]));

            if (empty($err)) {
                $remark  = '识别失败';
            } else {
                $remark  = $err;
            }

            $data = [
                'apply_id' => $applyId,
                'apply_file_id' => $params['id'],
                'ocr' => APPLY_EXCEPTION_OCR['fail'],
                'status' => APPLY_EXCEPTION_STATUS['fail'],
                'remark' => $remark,
            ];
            // 识别失败
            $this->createOrUpdateException($applyId, $data);
        } else {
            $this->updateApplyFile([
                'id' => $params['id']
            ], array_merge( $invoice, ['check_status' => APPLY_CHECK_STATUS['normal']]));
            $data = [
                'apply_id' => $applyId,
                'apply_file_id' => $params['id'],
                'ocr' => APPLY_EXCEPTION_OCR['success'],
            ];

            $this->createOrUpdateException($applyId, $data);
        }
        return array_merge($params, $result);
    }

    public function handleSupplementRepeatInvoice($applyId)
    {
        $where = [
            'apply_id' => $applyId,
            'file_type' => MATERIALS_TYPE['invoice']
        ];
        $result = ApplyFileModel::where($where)
            ->orderBy('id', 'asc')
            ->get([
                'id',
                'invoice_number',
                'check_status'
            ])
            ->toArray();

        if (empty($result)) {
            echo 'apply file invoice is empty';
            return false;
        }

        // 根据正常 + 异常的 总数 = 附件总数 来判断校验完毕
        $tmpCount = 0;
        foreach ($result as $key => $value) {
            if (in_array($value['check_status'], [APPLY_CHECK_STATUS['error'], APPLY_CHECK_STATUS['normal']])) {
                $tmpCount++;
            }
        }
        //没有检查完毕继续循环检查
        if ($tmpCount != count($result)) {
            echo 'not check over,continue check';
            return false;
        }
        // 判断是否全部检查完毕：检查完毕就去检查名称重复
        $numberList = [];
        foreach ($result as $key => $value) {
            if (!empty($value['invoice_number'])) {
                $numberList[] = $value['invoice_number'];
            }
        }
// 		$numberList = array_column($resultFile2, 'invoice_number');
        $countList = array_count_values($numberList);

        // 找出重复信息
        $repeatNumber = [];
        foreach ($countList as $key => $value) {
            if ($value > 1) {
                $repeatNumber[] = $key;
            }
        }

        $repeatIds = [];
        $repeatData = [];
        foreach ($result as $key => $value) {
            if (empty($value['invoice_number'])) {
                continue;
            } elseif (!in_array($value['invoice_number'], $repeatNumber)) {
                $temp = [
                    'apply_id' => $applyId,
                    'apply_file_id' => $value['id'],
                    'repeat' => APPLY_EXCEPTION_REPEAT['no'],
                    'status' => APPLY_EXCEPTION_STATUS['success'],
                ];
                $this->createOrUpdateException($applyId, $temp);
                continue;
            }
            $repeatIds[] = $value['id'];
            $temp = [
                'apply_id' => $applyId,
                'apply_file_id' => $value['id'],
                'repeat' => APPLY_EXCEPTION_REPEAT['yes'],
                'status' => APPLY_EXCEPTION_STATUS['fail'],
            ];

            // 新增异常信息
            $this->createOrUpdateException($applyId, $temp);
            unset($result[$key]);
        }
        if (!empty($repeatIds)) {
            // 更新附件信息
            ApplyFileModel::whereIn('id', $repeatIds)
                ->update(['check_status' => APPLY_CHECK_STATUS['error']]);
        }

        return true;
    }

    /**
     * FUNCTION_NAME : checkCorrect
     * author : jp
     * 检查 订正资料的附件
     * @return array|void
     */
    public function checkCorrect()
    {
        $where = [
            'is_check' => APPLY_CORRECT_IS_CHECK['yes'],
            'status' => APPLY_CORRECT_STATUS['seven']
        ];
        echo 'check correct start'.PHP_EOL;
        $correct = ApplyCorrectModel::where($where)->first();
        if (empty($correct)) {
            echo "correct empty".PHP_EOL;
            return [];
        }

        $correct_id = $correct['id'];
        $applyId = $correct['apply_id'];

        $where = [
            'apply_id' => $applyId,
            'file_type' => MATERIALS_TYPE['invoice'],
            'check_status' => APPLY_CHECK_STATUS['init']
        ];
        $column = [
            'id',
            'apply_id',
            'file_url',
            'invoice_number',
            'invoice_money',
            'invoice_billing_date',
            'invoice_checkcode',
            'invoice_code'
        ];
        $resultFile = ApplyFileModel::where($where)
            ->orderBy('id', 'DESC')
            ->get($column)
            ->toArray();

        if (empty($resultFile)) {
            $this->checkSuccess($correct_id);
            echo 'check correct success'.PHP_EOL;
            return;
        }

        $this->handleInvoice($resultFile, $applyId);
        $this->handleAloneCorrectRepeatInvoice($applyId, $correct_id);

        echo 'check correct success'.PHP_EOL;
        return;

    }

    // 检查订正资料的重复信息 （检查的不止订正的发票也包含已经存档的发票）
    public function handleAloneCorrectRepeatInvoice($applyId, $correctId)
    {
        $result = $this->handleAloneRepeatInvoice($applyId);
        if (!empty($result)) {
            DB::beginTransaction();
            try{
                $this->checkCorrectSuccess($correctId);
                DB::commit();
            }catch (Exception $e){
                DB::rollBack();
                echo 'db error';
                return false;
            }
            return true;
        }
        return true;
    }

    // 检查完成
    public function checkCorrectSuccess($correct_id)
    {
        $where = [
            'id' => $correct_id,
        ];
        $data = [
            'is_check' => APPLY_CORRECT_IS_CHECK['success']
        ];
        app(ApplyCorrectRepository::class)->updateByWhere($where, $data);
    }

    // 单独处理发票的信息
    protected function handleAloneRepeatInvoice($applyId)
    {
        $where = [
            'apply_id' => $applyId,
            'file_type' => MATERIALS_TYPE['invoice']
        ];
        $result = ApplyFileModel::where($where)
            ->orderBy('id', 'asc')
            ->get([
                'id',
                'invoice_number',
                'check_status'
            ])
            ->toArray();

        if (empty($result)) {
            echo 'apply file invoice is empty';
            return true;
        }

        // 根据正常 + 异常的 总数 = 附件总数 来判断校验完毕
        $tmpCount = 0;
        foreach ($result as $key => $value) {
            if (in_array($value['check_status'], [APPLY_CHECK_STATUS['error'], APPLY_CHECK_STATUS['normal']])) {
                $tmpCount++;
            }
        }
        //没有检查完毕继续循环检查
        if ($tmpCount != count($result)) {
            echo 'not check over,continue check';
            return false;
        }
        // 判断是否全部检查完毕：检查完毕就去检查名称重复
        $numberList = [];
        foreach ($result as $key => $value) {
            if (!empty($value['invoice_number'])) {
                $numberList[] = $value['invoice_number'];
            }
        }
// 		$numberList = array_column($resultFile2, 'invoice_number');
        $countList = array_count_values($numberList);

        // 找出重复信息
        $repeatNumber = [];
        foreach ($countList as $key => $value) {
            if ($value > 1) {
                $repeatNumber[] = $key;
            }
        }

        $repeatIds = [];
        $repeatData = [];
        foreach ($result as $key => $value) {
            if (empty($value['invoice_number'])) {
                continue;
            } elseif (!in_array($value['invoice_number'], $repeatNumber)) {
                $temp = [
                    'apply_id' => $applyId,
                    'apply_file_id' => $value['id'],
                    'repeat' => APPLY_EXCEPTION_REPEAT['no'],
                    'status' => APPLY_EXCEPTION_STATUS['success'],
                ];
                $this->createOrUpdateException($applyId, $temp);
                continue;
            }
            $repeatIds[] = $value['id'];
            $temp = [
                'apply_id' => $applyId,
                'apply_file_id' => $value['id'],
                'repeat' => APPLY_EXCEPTION_REPEAT['yes'],
                'status' => APPLY_EXCEPTION_STATUS['fail'],
            ];

            // 新增异常信息
            $this->createOrUpdateException($applyId, $temp);
            unset($result[$key]);
        }
        return true;
    }



}
