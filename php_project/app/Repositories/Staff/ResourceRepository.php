<?php
namespace App\Repositories\Staff;

use App\Models\RoleModel;
use App\Models\RoleTypeModel;
use App\Models\RoleBindResourceModel;
use App\Models\ResourceModel;
use App\Models\ResourceTypeModel;
use App\Models\RoleBindStaffModel;
use App\Repositories\BaseRepository;
use App\Common\Code;
use App\Models\RoleBindApiModel;
use App\Models\ApiModel;

class ResourceRepository  extends BaseRepository
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
		// 资源类型
		$list = ResourceTypeModel::where($where)
			->select(['*', 'name AS label'])
			->orderBy('id', 'asc')
			->get()
			->toArray();
		
		// 资源
		$listRole = ResourceModel::where($where)
			->select(['*', 'name AS label'])
			->orderBy('id', 'asc')
			->get()
			->toArray();
		
		// 查询角色已有的权限
		$bindList = [];
		if (!empty($arr['role_id'])) {
			// 查询部门信息
			$resultBind = RoleBindResourceModel::where(['role_id' => $arr['role_id']])
				->orderBy('resource_id', 'asc')
				->get(['resource_id'])
				->toArray();
			
			$bindList = empty($resultBind) ? [] : array_unique(array_column($resultBind, 'resource_id'));
		}

		// 组装数据
		foreach ($list as $key => $value) {
			unset($value['name']);
			
			$tmpArr = [];
			foreach ($listRole as $key2 => $value2) {
				if ($value['id'] === $value2['resource_type_id']) {
					$value2['resource_type_name'] = $value['label'];
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
	 * 列表
	 */
	public function getRoleList($arr)
	{
		$where = [];
		$where[] = ['f1.resource_id', '=', $arr['id']];
	
		$tmpModel = (new RoleBindResourceModel())
			->setTable('f1')
			->from(RoleBindResourceModel::TABLE_NAME . ' AS f1')
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
	
	/**
	 * 权限列表
	 */
	public function permissionList($arr)
	{
		$staffId = $arr['staff_id'];
		// 查询角色信息
		$whereRole = [];
		$whereRole[] = ['f2.staff_id', '=', $staffId];
		
		$roleList = (new RoleModel())
			->setTable('f1')
			->from(RoleModel::TABLE_NAME . ' AS f1')
			->join(RoleBindStaffModel::TABLE_NAME . ' AS f2','f2.role_id','=','f1.id')
			->where([
				'f2.staff_id' => $staffId
			])
			->orderBy('f1.id', 'asc')
			->get(['f1.id', 'f1.reserved'])
			->toArray();

		if (empty($roleList)) {
			return Code::LOGIN_PERMISSION_TWO;
		}
		
		$roleIds = array_unique(array_column($roleList, 'id'));
		// 判断是否为超管
		$reserved = array_column($roleList, 'reserved');
		if (!in_array(RESERVED_YES, $reserved)) {
			// 不等于超管查询接口权限
			$apiList = (new RoleBindApiModel())
				->setTable('f1')
				->from(RoleBindApiModel::TABLE_NAME . ' AS f1')
				->join(ApiModel::TABLE_NAME . ' AS f2','f1.api_id','=','f2.id')
				->whereIn('role_id', $roleIds)
				->orderBy('f2.number', 'asc')
				->get(['f2.number'])
				->toArray();
			
			if (empty($apiList)) {
				return Code::LOGIN_PERMISSION_SIX;
			}
		}
		
		// 查询权限菜单
		$resourceTypeList = ResourceTypeModel::where([])
			->orderBy('id', 'asc')
			->get([
				'id',
				'name',
				'alias'
			])
			->toArray();
		
		if (empty($resourceTypeList)) {
			return Code::LOGIN_PERMISSION_FIVE;
		}
		
		// 资源
		$resourceList = ResourceModel::where([])
			->orderBy('id', 'asc')
			->get([
				'id',
				'resource_type_id',
				'name',
				'alias'
			])
			->toArray();
		
		if (empty($resourceList)) {
			return Code::LOGIN_PERMISSION_FIVE;
		}

		// 判断是否为超管,超管查询全部
		if (in_array(RESERVED_YES, $reserved)) {
			// 组装数据
			foreach ($resourceTypeList as $key => $value) {
				$tmpArr = [];
				foreach ($resourceList as $key2 => $value2) {
					if ($value['id'] === $value2['resource_type_id']) {
						$tmpArr[] = $value2;
						unset($resourceList[$key2]);
					}
				}
				$value['children'] = $tmpArr;
				$resourceTypeList[$key] = $value;
			}
			
			return $resourceTypeList;
		}
		
		// 查询资源
		$resourceBindList = (new RoleBindResourceModel())
			->setTable('f1')
			->from(RoleBindResourceModel::TABLE_NAME . ' AS f1')
			->join(ResourceModel::TABLE_NAME . ' AS f2','f2.id','=','f1.resource_id')
			->whereIn('f1.role_id', $roleIds)
			->orderBy('f2.id', 'asc')
			->get(['f2.id'])
			->toArray();

		if (empty($resourceBindList)) {
			return Code::LOGIN_PERMISSION_FIVE;
		}
		
		$resourceIds = array_unique(array_column($resourceBindList, 'id'));
		// 排除没有配置的
		foreach ($resourceList as $key => $value) {
			if (!in_array($value['id'], $resourceIds)) {
				unset($resourceList[$key]);
			}
		}

		$return = [];
		foreach ($resourceTypeList as $key => $value) {
			$tmpArr = [];
			foreach ($resourceList as $key2 => $value2) {
				if ($value['id'] === $value2['resource_type_id']) {
					$tmpArr[] = $value2;
					unset($resourceList[$key2]);
				}
			}
			// 没有删除大类
			if (empty($tmpArr)) {
				unset($resourceTypeList[$key]);
				continue;
			}
			$value['children'] = $tmpArr;
			$return[] = $value;
		}

		return $return;
	}
	
}
