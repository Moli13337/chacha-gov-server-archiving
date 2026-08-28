<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Common\Code;
use Xkd\Location\Location;

class ConfigController extends Controller
{
	/**
	 * 数据字典配置
	 */
	public function config(Request $request)
	{
		$constant = trans('constant');
		// 返回到前端：需要哪些自行添加
		$result = [
		    'publish_status' => $constant['publish_status'], // 发布状态
			'apply_status' => $constant['apply_status'], // 申请表状态
            'class_policy_type' => $constant['class_policy_type'],
            'expired' => $constant['expired'], // 是否过期
            'materials_need' => $constant['materials_need'], // 是否过期
            'materials_type' => $constant['materials_type'], // 是否过期
            'tax_type' => $constant['tax_type'], // 本级
            'department_type' => $constant['department_type'], // 部门类型
            'staff_opertor_type' => $constant['staff_opertor_type'], // 员工类型
            'activity_subject_type' => $constant['activity_subject_type'], // 操作模块
            'apply_exception_year' => $constant['apply_exception_year'], // 发票年限
            'apply_exception_truth' => $constant['apply_exception_truth'], // 发票真伪
            'apply_exception_repeat_apply' => $constant['apply_exception_repeat_apply'], // 发票重复申报
            'apply_exception_repeat' => $constant['apply_exception_repeat'], // 发票重复
            'is_show' => $constant['is_show'], // 是否显示
            'is_calculate' => $constant['is_calculate'], // 是否统计分值
            'agent_credit_type' => $constant['agent_credit_type'], // 中介信用行为类型
            'is_audit' => $constant['is_audit'], // 审核
            'agent_complaint_type' => $constant['agent_complaint_type'], // 中介投诉类型
            'agent_complaint_status' => $constant['agent_complaint_status'], // 中介投诉状态
            'agent_guide_type' => $constant['agent_guide_type'], // 中介指南
            'user_message_source' => $constant['user_message_source_admin'], // 消息类型
            'steward_opinion_type' => $constant['steward_opinion_type'], // 调查问卷类型
            'steward_information_type' => $constant['steward_information_type'], // 信息动态类型
            'steward_push_type' => $constant['steward_push_type'], // 推送方式
            'steward_push_obj_type' => $constant['steward_push_obj_type'], // 推送类型
            'share_activity_type' => $constant['share_activity_type'], // 活动类型
            'share_activity_status' => $constant['share_activity_status'], // 活动类型
            'apply_correct_status' => $constant['apply_correct_status'], // 消息类型
            'sms' => trans('sms'), // 短信消息
            'agent_submit_material' => $constant['agent_submit_material'], // 中介服务 提交材料
            'user_list_order_type' => $constant['user_list_order_type'], // 用户列表排序方式
			'others' => []
		];
		
		// 转换数据
		foreach ($result as $key => $value) {
			$tmpList = [];
			if (is_array($value)) {
				foreach ($value as $key2 => $value2) {
					$tmpList[] = [
						'id' => $key2,
						'name' => $value2
					];
				}
			}
			$result[$key] = $tmpList;
		}

		return codeRender(Code::OK, $result);
	}

    /**
     * FUNCTION_NAME : getDistricts
     * author : jp
     * 获取行政区划
     * @param Request $request
     * @return mixed
     * @throws \Xkd\Location\Exceptions\ClientException
     */
	public function getDistricts(Request $request)
    {
        return Location::getInfo('district')->getDistricts($request->all());
    }
	
// 	/**
// 	 * 详情
// 	 * type 1不需要层级关系 2需要层级关系
// 	 */
// 	public function staffDetail(Request $request)
// 	{
// 		$data = $request->all();
		
// 		if (empty($data['staff_id'])) {
// 			return codeRender(Code::CHECK_EMPTY_ERROR, '', 'staff_id');
// 		}
		
// 		$where = [
// 			'id' => $data['staff_id']
// 		];
// 		$columns = !empty($data['columns']) && is_array($data['columns']) 
// 			? $data['columns'] 
// 			: ['id', 'mobile', 'name', 'email'];
		
// 		// 1不需要层级关系 2需要层级关系
// 		$result = [];
// 		$type = empty($data['type']) ? 1 : $data['type'];
// 		if ($type == 2) {
// 			$result = $this->staffRepository->staffDetail($where, $columns);
// 		} else {
// 			$result = $this->staffRepository->findDetail($where, $columns);
// 		}

// 		return codeRender(Code::OK, $result);
// 	}
	
// 	/**
// 	 * 新增
// 	 */
// 	public function staffStore(Request $request)
// 	{
// 		$data = $request->all();
		
// 		if (empty($data['mobile'])) {
// 			return codeRender(Code::CHECK_EMPTY_ERROR, '', 'mobile');
// 		}
// 		if (strlen($data['mobile']) > 11) {
// 			return codeRender(Code::CHECK_LENGTH_ERROR, '', 'mobile');
// 		}

// 		// 检查唯一性
// 		$whereExist = [
// 			'real_operator_id' => $data['real_operator_id']
// 		];
// 		$resultExist = $this->staffRepository->findDetail($whereExist, ['id']);
// 		if (!empty($resultExist)) {
// 			return codeRender(Code::CHECK_UNIQUE_ERROR, '', 'real_operator_id');
// 		}
		
// 		DB::beginTransaction();
		
// 		// 新增员工
// 		$data['number'] = STAFF_NUMBER;
// 		$data['password'] = encryption($data['mobile']);
// 		$resultStaff = $this->staffRepository->storeRepository($data);
// 		if ($resultStaff['code'] !== Code::OK) {
// 			DB::rollBack();
// 			return codeRender($resultStaff['code'], $resultStaff['data']);
// 		}

// 		// 新增默认角色组
// 		$roleType = [
// 			'name' => '默认角色组',
// 			'reserved' => RESERVED_YES,
// 			'real_operator_id' => $data['real_operator_id']
// 		];
// 		$resultRoleType = $this->roleTypeRepository->storeRepository($roleType);
// 		if ($resultRoleType['code'] !== Code::OK) {
// 			DB::rollBack();
// 			return codeRender($resultRoleType['code'], $resultRoleType['data']);
// 		}
		
// 		// 新增默认角色-超级管理员
// 		$role = [
// 			'name' => '超级管理员',
// 			'description' => '超级管理员',
// 			'role_type_id' => $resultRoleType['data']['id'],
// 			'reserved' => RESERVED_YES,
// 			'real_operator_id' => $data['real_operator_id']
// 		];
// 		$resultRole = $this->roleRepository->storeRepository($role);
// 		if ($resultRole['code'] !== Code::OK) {
// 			DB::rollBack();
// 			return codeRender($resultRole['code'], $resultRole['data']);
// 		}
		
// 		// 新增角色-超管
// 		$bind = [
// 			'staff_id' => $resultStaff['data']['id'],
// 			'role_id' => $resultRole['data']['id'],
// 			'created_at' => time()
// 		];
// 		$resultBind = $this->roleRepository->bindStaffMany(['bindData' => $bind]);
// 		if (!$resultBind) {
// 			DB::rollBack();
// 			return codeRender(Code::DB_ERROR, '');
// 		}
		
// 		DB::commit();
		
// 		// 排除密码
// 		unset($resultStaff['data']['password']);
		
// 		return codeRender($resultStaff['code'], $resultStaff['data']);
// 	}
}
