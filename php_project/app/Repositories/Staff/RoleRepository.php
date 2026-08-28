<?php
namespace App\Repositories\Staff;

use App\Common\Code;
use App\Exceptions\CodeException;
use App\Models\RoleModel;
use App\Models\RoleTypeModel;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\StaffModel;
use App\Models\RoleBindResourceModel;
use App\Models\RoleBindStaffModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Log;
use App\Models\RoleBindApiModel;

class RoleRepository  extends BaseRepository
{
	public function model()
	{
		return RoleModel::class;
	}

	/**
	 * name 唯一性检查
	 * $isUpdate: true 更新  false 新增
	 */
	public function checkUnique($arr, $isUpdate = false)
	{
		$where = [];
		$where[] = ['name', '=', $arr['name']];
		$where[] = ['role_type_id', '=', $arr['role_type_id']];
		
		if ($isUpdate) {
			$where[] = ['id', '<>', $arr['id']];
		}
		$staff = RoleModel::where($where)->limit(1)->get()->toArray();
		if (empty($staff)) {
			// 不存在
			return false;
		}
		
		return true;
	}
	
	/**
	 * 查找一条
	 */
	public function findDetail($where, $columns = ['*'])
	{
		$list = RoleModel::where($where)
			->limit(1)
			->get($columns)
			->toArray();

		$result = empty($list) ? [] : $list[0];
		return $result;
	}
	
	/**
	 * 列表
	 */
	public function list($arr)
	{
		$where = [];

		// 角色组
		$list = RoleTypeModel::where($where)
			->select('id', 'name AS label', 'reserved')
			->orderBy('id', 'asc')
			->get()
			->toArray();
		
		// 角色
		$listRole = RoleModel::where($where)
			->select('id', 'role_type_id', 'name AS label', 'reserved', 'description')
			->orderBy('id', 'asc')
			->get()
			->toArray();

		// 组装数据
		foreach ($list as $key => $value) {
			$tmpArr = [];
			foreach ($listRole as $key2 => $value2) {
				if ($value['id'] === $value2['role_type_id']) {
					$value2['role_type_name'] = $value['label'];
					$value2['compose_id'] = $value['id'] . '-' . $value2['id'];
					$tmpArr[] = $value2;
					unset($listRole[$key2]);
				}
			}
			$value['compose_id'] = $value['id'];
			$value['children'] = $tmpArr;
			$list[$key] = $value;
		}

		return returnPage($list, count($list));
	}
	
	/**
	 * 新增
	 */
	public function storeRole($arr)
	{
		DB::beginTransaction();
	
		try{
	
			$result = $this->create($arr);
	
			// log
			storeActivityLog(ACTIVITY_TYPE['created'],
				ACTIVITY_SUBJECT_TYPE['role'], $result['id'], $arr);
	
			DB::commit();
			return $result['id'];
	
		}catch (Exception $e){
			Log::error('role storeRole' . $e->getMessage());
			DB::rollBack();
			return false;
		}
	
	}
	
	/**
	 * 修改
	 */
	public function updateRole($arr, $old = [])
	{
		DB::beginTransaction();
	
		$id = $arr['id'];
		try{
	
			// log
			storeActivityLog(ACTIVITY_TYPE['updated'],
				ACTIVITY_SUBJECT_TYPE['role'], $arr['id'], $arr, $old);
	
			$this->update(array_except($arr,['id']), $arr['id']);
	
			DB::commit();
			return true;
	
		}catch (Exception $e){
			Log::error('role updateRole' . $e->getMessage());
			DB::rollBack();
			return false;
		}
	}
	
	/**
	 * 删除角色
	 * 删除角色，同时删除角色与资源、角色与员工的关联关系。（超级管理员角色不可删除）
	 */
	public function deleteRole($arr)
	{
		// 查询角色绑定的所有员工
		$staffList = RoleBindStaffModel::where([
				'role_id' => $arr['id']
			])->get(['staff_id'])->toArray();
	
		DB::beginTransaction();
	
		try{
			// 删除自己
			RoleModel::where(['id' => $arr['id']])->delete();

			// 删除绑定员工
			RoleBindStaffModel::where(['role_id' => $arr['id']])->delete();
			
			// 删除绑定资源
			RoleBindResourceModel::where(['role_id' => $arr['id']])->delete();
			
			// 删除绑定接口
			RoleBindApiModel::where(['role_id' => $arr['id']])->delete();
			
			// 改变员工的token
			if (!empty($staffList)) {
				$staffIds = array_unique(array_column($staffList, 'staff_id'));
				app(StaffTokenRepository::class)->changeToken($staffIds);
			}
			
			// log
			storeActivityLog(ACTIVITY_TYPE['deleted'],
				ACTIVITY_SUBJECT_TYPE['role'], $arr['id'], $arr);
			
			DB::commit();
			return true;
		}catch (Exception $e){
			Log::error('role deleteRole' . $e->getMessage());
			DB::rollBack();
			return false;
		}
	}
	
	/**
	 * 绑定员工--多个
	 */
	public function bindStaff($arr)
	{
		// 查询之前已有的员工
		$staffList = RoleBindStaffModel::where([
				'role_id' => $arr['id']
			])->get(['staff_id'])->toArray();

		$staffIdTmp = [];
		if (!empty($staffList)) {
			$staffIdTmp = array_unique(array_column($staffList, 'staff_id'));
		}
		
		$staffList = empty($arr['staff_list']) ? [] : array_unique($arr['staff_list']);
		$staffIds = array_merge(array_diff($staffList, $staffIdTmp),array_diff($staffIdTmp, $staffList));
		
		// 数据处理
		$bindData = [];
		if (!empty($staffList)) {
			$currentTime = time();
			foreach ($staffList as $key => $value) {
				$bindData[] = [
					'staff_id' => $value,
					'role_id' => $arr['id'],
					'created_at' => $currentTime
				];
			}
			
			
		}

		DB::beginTransaction();
		
		try{
			// 删除已有 - 兼容新增
			if (!empty($arr['id'])) {
				RoleBindStaffModel::where(['role_id' => $arr['id']])->delete();
			}

			// 新增
			if (!empty($bindData)) {
				RoleBindStaffModel::insert($bindData);
			}
			
			// 改变员工的token
			if (!empty($staffIds)) {
				app(StaffTokenRepository::class)->changeToken($staffIds);
			}
			
			DB::commit();
			return true;
		}catch (Exception $e){
			Log::error('role bindStaff' . $e->getMessage());
			DB::rollBack();
			return false;
		}
	}
	
	/**
	 * 员工列表
	 */
	public function getStaffList($arr)
	{
		$where = [];
		$where[] = ['f1.role_id', '=', $arr['id']];
		
		$tmpModel = (new StaffModel())
			->setTable('f2')
			->from(StaffModel::TABLE_NAME . ' AS f2')
			->leftJoin(RoleBindStaffModel::TABLE_NAME . ' AS f1','f1.staff_id','=','f2.id')
			->where($where);
		
		$staffCount = $tmpModel->get(['f2.id'])->count();
		
		$page = commonPage($arr);
		
		$staffList = $tmpModel
			->orderBy('f2.id', 'asc')
			->offset($page['offset'])
			->limit($page['page_size'])
			->get([
				'f2.id',
				'f2.name',
				'f2.mobile',
				'f2.sex',
				'f2.email',
				'f2.photo_url',
				'f2.created_at'
			])
			->toArray();
		
		return returnPage($staffList, $staffCount);
	}
	
	/**
	 * 删除员工
	 */
	public function deleteStaff($arr)
	{
		if (empty($arr['staff_list'])) {
			return true;
		}

		DB::beginTransaction();
		try{
			$staffList = $arr['staff_list'];
			foreach ($staffList as $key => $value) {
				$tmpWhere = [
					['role_id', '=', $arr['id']],
					['staff_id', '=', $value]
				];
				RoleBindStaffModel::where($tmpWhere)->delete();
			}
			
			// 改变员工的token
			app(StaffTokenRepository::class)->changeToken($staffList);
		
			DB::commit();
			return true;
		}catch (Exception $e){
			Log::error('role deleteStaff' . $e->getMessage());
			DB::rollBack();
			return false;
		}

	}
	
	/**
	 * 绑定资源
	 */
	public function bindResource($arr)
	{
		// 查询的权限
		$resourceBind = RoleBindResourceModel::where([
				'role_id' => $arr['id']
			])->get(['resource_id'])->toArray();
			
		$resourceIdTmp = [];
		if (!empty($resourceBind)) {
			$resourceIdTmp = array_unique(array_column($resourceBind, 'resource_id'));
		}
		
		$resourceList = empty($arr['resource_list']) ? [] : array_unique($arr['resource_list']);

		// 数据处理
		$bindData = [];
		if (!empty($resourceList)) {
			$currentTime = time();
			foreach ($resourceList as $key => $value) {
				$bindData[] = [
					'resource_id' => $value,
					'role_id' => $arr['id'],
					'created_at' => $currentTime
				];
			}
		}
		
		$staffIds = [];
		if (count($resourceIdTmp) != count($resourceList)) {
			// 查询该角色下的所有人
			$staffList = RoleBindStaffModel::where([
				'role_id' => $arr['id']
			])->get(['staff_id'])->toArray();
			
			if (!empty($staffList)) {
				$staffIds = array_unique(array_column($staffList, 'staff_id'));
			}
		}

		DB::beginTransaction();
		// 删除已有
		try{
			RoleBindResourceModel::where(['role_id' => $arr['id']])->delete();
	
			// 新增
			if (!empty($bindData)) {
				RoleBindResourceModel::insert($bindData);
			}
			
			// 改变员工的token
			if (!empty($staffIds)) {
				app(StaffTokenRepository::class)->changeToken($staffIds);
			}
			
			DB::commit();
			return true;
		}catch (Exception $e){
			Log::error('role bindResource' . $e->getMessage());
			DB::rollBack();
			return false;
		}
	}
	
	/**
	 * 绑定API
	 */
	public function bindApi($arr)
	{
// 		// 查询的权限
// 		$resourceBind = RoleBindResourceModel::where([
// 			'role_id' => $arr['id']
// 		])->get(['resource_id'])->toArray();
			
// 		$resourceIdTmp = [];
// 		if (!empty($resourceBind)) {
// 			$resourceIdTmp = array_unique(array_column($resourceBind, 'resource_id'));
// 		}
	
		$apiList = empty($arr['api_list']) ? [] : array_unique($arr['api_list']);
	
		// 数据处理
		$bindData = [];
		if (!empty($apiList)) {
			foreach ($apiList as $key => $value) {
				$bindData[] = [
					'api_id' => $value,
					'role_id' => $arr['id']
				];
			}
		}
	
		$staffIds = [];
// 		if (count($resourceIdTmp) != count($resourceList)) {
// 			// 查询该角色下的所有人
// 			$staffList = RoleBindStaffModel::where([
// 				'role_id' => $arr['id']
// 			])->get(['staff_id'])->toArray();
				
// 			if (!empty($staffList)) {
// 				$staffIds = array_unique(array_column($staffList, 'staff_id'));
// 			}
// 		}
	
		DB::beginTransaction();
		// 删除已有
		try{
			RoleBindApiModel::where(['role_id' => $arr['id']])->delete();
	
			// 新增
			if (!empty($bindData)) {
				RoleBindApiModel::insert($bindData);
			}
				
// 			// 改变员工的token
// 			if (!empty($staffIds)) {
// 				app(StaffTokenRepository::class)->changeToken($staffIds);
// 			}
				
			DB::commit();
			return true;
		}catch (Exception $e){
			Log::error('role bindApi' . $e->getMessage());
			DB::rollBack();
			return false;
		}
	}

	/**
	 * 更换超级管理员
	 * 不删除变更之前的权限，在角色查询权限的时候优先查询超管，在查询权限树
	 */
	public function changeAdmin($arr)
	{
		DB::beginTransaction();

		try{
			$result = RoleBindStaffModel::where([
				'role_id' => $arr['role_id'],
				'staff_id' => $arr['current_id']
			])
			->update([
				'staff_id' => $arr['staff_id']
			]);
			
			// 改变员工的token
			app(StaffTokenRepository::class)->changeToken([
				$arr['staff_id'], $arr['current_id']
			]);
				
			DB::commit();
			return true;
		}catch (Exception $e){
			Log::error('role changeAdmin' . $e->getMessage());
			DB::rollBack();
			return false;
		}
	}

	public function getStaffAdmin()
    {
        // 这里默认的是 RESERVED_YES 是超级管理员角色
        // 查询是否存在
        $whereExist = [
            'reserved' => RESERVED_YES
        ];
        $resultExist = $this->findDetail($whereExist, ['id']);
        if (empty($resultExist)) {
            throw new CodeException(Code::CHECK_OPERATE_ERROR);
        }

        $role_id = $resultExist['id'];

        $arr = [
            'id' => $role_id
        ];
        $data = $this->getStaffList($arr);
        $staff = array_get($data['list']??[], 0, []);
        return $staff;
    }

    public function bindStaffOne($arr)
    {
        $arr['created_at'] = time();
        return RoleBindStaffModel::insert($arr);
    }
}
