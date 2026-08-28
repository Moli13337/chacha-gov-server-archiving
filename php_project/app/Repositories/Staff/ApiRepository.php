<?php
namespace App\Repositories\Staff;

use App\Models\RoleModel;
use App\Models\RoleTypeModel;
use App\Models\RoleBindResourceModel;
use App\Models\ResourceModel;
use App\Models\ResourceTypeModel;
use App\Models\RoleBindStaffModel;
use App\Repositories\BaseRepository;
use App\Models\ApiTypeModel;
use App\Models\ApiModel;
use App\Models\RoleBindApiModel;

class ApiRepository  extends BaseRepository
{
	public function model()
	{
		return ResourceModel::class;
	}
	
	/**
	 * 列表
	 */
	public function list($arr)
	{
		$where = [];
		$list = ApiTypeModel::where($where)
			->orderBy('sort', 'asc')
			->get(['*', 'name AS label'])
			->toArray();
		
		// 资源
		$listRole = ApiModel::where($where)
			->orderBy('sort', 'asc')
			->get(['*', 'name AS label'])
			->toArray();
		
		// 查询角色已有的权限
		$bindList = [];
		if (!empty($arr['role_id'])) {
			// 查询部门信息
			$resultBind = RoleBindApiModel::where(['role_id' => $arr['role_id']])
				->orderBy('api_id', 'asc')
				->get(['api_id'])
				->toArray();
			
			$bindList = empty($resultBind) ? [] : array_unique(array_column($resultBind, 'api_id'));
		}

		// 组装数据
		foreach ($list as $key => $value) {
			unset($value['name']);
			
			$tmpArr = [];
			foreach ($listRole as $key2 => $value2) {
				if ($value['id'] === $value2['api_type_id']) {
					$value2['api_type_name'] = $value['label'];
					unset($value2['name']);
					$value2['checked'] = in_array($value2['id'], $bindList) ? true : false;
					
					$tmpArr[] = $value2;
					unset($listRole[$key2]);
				}
			}
			$value['children'] = $tmpArr;
			$list[$key] = $value;
		}

		return returnPage($list, count($list));
	}
	
	/**
	 * 部门列表
	 */
	public function getRoleList($arr)
	{
		$where = [];
		$where[] = ['f1.api_id', '=', $arr['id']];
	
		$tmpModel = (new RoleBindApiModel())
			->setTable('f1')
			->from(RoleBindApiModel::TABLE_NAME . ' AS f1')
			->join(RoleModel::TABLE_NAME . ' AS f2','f2.id','=','f1.role_id')
			->where($where);
		
		$roleCount = $tmpModel
			->get(['f2.id'])
			->count();
	
		$page = commonPage($arr);
	
		$roleList = $tmpModel
			->orderBy('f2.id', 'asc')
			->offset($page['offset'])
			->limit($page['page_size'])
			->get([
				'f2.id', 
				'f2.role_type_id', 
				'f2.name', 
				'f2.reserved', 
				'f2.description', 
				'f2.created_at'
			])
			->toArray();

		// 查询角色组
		if (!empty($roleList)) {
			$roleTypeId = array_unique(array_column($roleList, 'role_type_id'));

			$roleTypeList = RoleTypeModel::whereIn('id', $roleTypeId)
				->select(['id', 'name'])
				->get()
				->toArray();

			if (!empty($roleTypeList)) {
				foreach ($roleList as $key => $value) {
					$value = (array)$value;
					$role_type_name = '';
					foreach ($roleTypeList as $key2 => $value2) {
						if ($value['role_type_id'] === $value2['id']) {
							$role_type_name = $value2['name'];
							break;
						}
					}
					$value['role_type_name'] = $role_type_name;
					$roleList[$key] = $value;
				}
			}
		}
	
		return returnPage($roleList, $roleCount);;
	}
}
