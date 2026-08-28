<?php
namespace App\Repositories\Staff;

use App\Models\RoleTypeModel;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\RoleModel;
use App\Models\RoleBindStaffModel;
use App\Models\RoleBindResourceModel;
use App\Repositories\BaseRepository;
use App\Models\RoleBindApiModel;

class RoleTypeRepository  extends BaseRepository
{
	public function model()
	{
		return RoleTypeModel::class;
	}

	/**
	 * name 唯一性检查
	 * $isUpdate: true 更新  false 新增
	 */
	public function checkUnique($arr, $isUpdate = false)
	{
		$where = [];
		$where[] = ['name', '=', $arr['name']];
		if ($isUpdate) {
			$where[] = ['id', '<>', $arr['id']];
		}
		$staff = RoleTypeModel::where($where)->limit(1)->get()->toArray();
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
		$list = RoleTypeModel::where($where)
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
		$where[] = ['reserved', '=', RESERVED_NO];

		$list = RoleTypeModel::where($where)
			->orderBy('id', 'asc')
			->get(['id', 'name'])
			->toArray();

		return returnPage($list, 0);
	}
	
	/**
	 * 新增
	 */
	public function storeRoleType($arr)
	{
		DB::beginTransaction();
	
		try{
	
			$result = $this->create($arr);
	
			// log
			storeActivityLog(ACTIVITY_TYPE['created'],
				ACTIVITY_SUBJECT_TYPE['role_type'], $result['id'], $arr);
	
			DB::commit();
			return $result['id'];
	
		}catch (Exception $e){
			DB::rollBack();
			return false;
		}
	
	}
	
	/**
	 * 修改
	 */
	public function updateRoleType($arr, $old = [])
	{
		DB::beginTransaction();
	
		$id = $arr['id'];
		try{
	
			// log
			storeActivityLog(ACTIVITY_TYPE['updated'],
				ACTIVITY_SUBJECT_TYPE['role_type'], $arr['id'], $arr, $old);
	
			$this->update(array_except($arr,['id']), $arr['id']);
	
			DB::commit();
			return true;
	
		}catch (Exception $e){
			DB::rollBack();
			return false;
		}
	}
	
	/**
	 * 删除角色组
	 * 除角色组以及角色组下属的所有角色，同时删除角色与资源、角色与员工的关联关系。（默认角色组不可删除）
	 */
	public function deleteRoleType($arr)
	{
		// 查询角色
		$roleList = RoleModel::where([
				'role_type_id' => $arr['id']
			])
			->get(['id'])
			->toArray();

		// 查询角色绑定的所有员工
		$roleIds = [];
		$staffList = [];
		if (!empty($roleList)) {
			$roleIds = array_unique(array_column($roleList, 'id'));
			$staffList = RoleBindStaffModel::whereIn('role_id', $roleIds)->get(['staff_id'])->toArray();
		}

		DB::beginTransaction();
	
		try{
			// 删除自己
			RoleTypeModel::where(['id' => $arr['id']])->delete();
			
			if (!empty($roleIds)) {
				// 删除角色
				RoleModel::where(['role_type_id' => $arr['id']])->delete();
				
				// 删除绑定员工
				RoleBindStaffModel::whereIn('role_id', $roleIds)->delete();
				
				// 删除绑定资源
				RoleBindResourceModel::whereIn('role_id', $roleIds)->delete();
				
				// 删除绑定接口
				RoleBindApiModel::whereIn('role_id', $roleIds)->delete();
			}
			
			// 改变员工的token
			if (!empty($staffList)) {
				$staffIds = array_unique(array_column($staffList, 'staff_id'));
				app(StaffTokenRepository::class)->changeToken($staffIds);
			}
			
			// log
			storeActivityLog(ACTIVITY_TYPE['deleted'],
				ACTIVITY_SUBJECT_TYPE['role_type'], $arr['id'], $arr);

		}catch (Exception $e){
			DB::rollBack();
			return false;
		}
	
		DB::commit();
		return true;
	}
	
}
