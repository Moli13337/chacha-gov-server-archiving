<?php
namespace App\Repositories\Staff;

use App\Criteria\KeywordCriteria;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Exceptions\QueryException;
use App\Models\StaffModel;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\StaffBindDepartmentModel;
use App\Models\RoleBindStaffModel;
use App\Models\RoleModel;
use App\Common\Code;
use App\Repositories\BaseRepository;
use App\Models\StaffDepartmentModel;
use App\Models\RoleBindApiModel;
use App\Models\ApiModel;
use Illuminate\Support\Facades\Log;

class StaffRepository  extends BaseRepository
{
	// 1 姓名  2电话  3 邮箱
	public $staffListFilter = [
		1 => 'name',
		2 => 'mobile',
		3 => 'email'
	];
	
	public function model()
	{
		return StaffModel::class;
	}
	
	/**
	 * mobile 唯一性检查
	 * $isUpdate: true 更新  false 新增
	 */
	public function checkUnique($arr, $isUpdate = false)
	{
		$where = [];
		$where[] = ['mobile', '=', $arr['mobile']];
		
		if ($isUpdate) {
			$where[] = ['id', '<>', $arr['id']];
		}
		$staff = StaffModel::withTrashed()->where($where)->limit(1)->get()->toArray();
		if (empty($staff)) {
			// 不存在
			return false;
		}
		
		if ($isUpdate) {
			// 编辑
			return true;
		}

		// 新增
		$unique = $staff[0];
		if (!empty($unique['deleted_at'])) {
			// 恢复软删除
			$result = StaffModel::where('id', $unique['id'])->restore();
			return ['id' => $unique['id']];
		}
		
		return true;
	}

	/**
	 * 列表
	 */
	public function staffListPage($arr, $columns = ['*'])
	{
		$where = [];

		// 搜索
		if (!empty($arr['keyword'])) {
			// 去除空格
			$keyword = trim($arr['keyword']);
			$keyword = '%'.addslashes($keyword).'%';

			// filter_type 1 姓名  2电话  3 邮箱
			$filter_type = empty($arr['filter_type']) ? 1 : $arr['filter_type'];
			$where[] = [$this->staffListFilter[$filter_type], 'like', $keyword];
		}

		$count = StaffModel::where($where)->count();

		$page = commonPage($arr);
		$list = StaffModel::where($where)
			->select($columns)
			->orderBy('number', 'desc')
			->offset($page['offset'])
			->limit($page['page_size'])
			->get()
			->toArray();
		
		
		if (!empty($list)) {
			// 判断超管
			$whereRole = [];
			$whereRole[] = ['f2.reserved', '=', RESERVED_YES];
			$roleList = (new RoleBindStaffModel())
				->setTable('f1')
				->from(RoleBindStaffModel::TABLE_NAME . ' AS f1')
				->join(RoleModel::TABLE_NAME . ' AS f2','f1.role_id','=','f2.id')
				->where($whereRole)
				->orderBy('f1.staff_id', 'asc')
				->limit(1)
				->get(['f1.staff_id'])
				->toArray();
				
				
			if (!empty($roleList)) {
				foreach ($list as $key => $value) {
					$value['reserved'] = RESERVED_NO;
					if ($value['id'] == $roleList[0]['staff_id']) {
						$value['reserved'] = RESERVED_YES;
					}
					$list[$key] = $value;
				}
			}
		}

		return returnPage($list, $count);
	}
	
	/**
	 * 列表
	 */
	public function staffListAll($arr, $columns = ['*'])
	{
		$list = [];
		if (empty($arr['filter_type']) || empty($arr['filter_id'])) {
			$list = $this->staffListAll2($arr, $columns);
		} else {
			// filter_type: 1部门  2角色
			$filterType = (int)$arr['filter_type'];
			// filter_id 部门ID 或者角色ID
			$filterId = $arr['filter_id'];

			if ($filterType === 1) {
				$list = $this->staffListAll3($arr, $columns);
				
				// 查询部门信息
				$resultTmp = StaffBindDepartmentModel::where(['department_id' => $filterId])
					->orderBy('staff_id', 'asc')
					->get(['staff_id', 'opertor_type'])
					->toArray();

				if (!empty($resultTmp)) {
					foreach ($resultTmp as $key => $value) {
						
						foreach ($list as $key2 => $value2) {
							if ($value['staff_id'] == $value2['id']) {
								$value2['checked'] = true;
								$value2['opertor_type'] = $value['opertor_type'];
								$list[$key2] = $value2;
								break;
							}
						}
						unset($resultTmp[$key]);
					}
				}
			} else if ($filterType === 2) {
				$list = $this->staffListAll2($arr, $columns);
				
				// 查询角色信息
				$resultTmp = RoleBindStaffModel::where(['role_id' => $filterId])
					->select('staff_id')
					->orderBy('staff_id', 'asc')
					->get()
					->toArray();
				
				if (!empty($resultTmp)) {
					foreach ($resultTmp as $key => $value) {
				
						foreach ($list as $key2 => $value2) {
							if ($value['staff_id'] == $value2['id']) {
								$value2['checked'] = true;
								$list[$key2] = $value2;
								break;
							}
						}
						unset($resultTmp[$key]);
					}
				}
			}
	
		}

		return returnPage($list, count($list));
	}
	
	/**
	 * 列表
	 */
	public function staffListAll2($arr, $columns = ['*'])
	{
		$where = [];
	
		$limit = $arr['limit'] ?? LIMIT_PAGE_SIZE;
	
		$list = [];
	
		// 搜索
		if (!empty($arr['keyword'])) {
			$filterArr = ['name', 'mobile'];
				
			// 去除空格
			$keyword = trim($arr['keyword']);
			$keyword = "%$keyword%";
				
			$list = StaffModel::where(function ($q) use ($filterArr, $keyword) {
					$q = $q->where($filterArr[0], 'like', $keyword);
	
					foreach ($filterArr as $k => $v) {
						if ($k ==0) {
							continue;
						}
						$q = $q->orWhere($v, 'like', $keyword);
					}
					return $q;
	
				})
				->where($where)
				->select($columns)
				->orderBy('number', 'asc')
				->limit($limit)
				->get()
				->toArray();
				
		} else {
			// 全部
			$list = StaffModel::where($where)
				->select($columns)
				->orderBy('number', 'asc')
				->limit($limit)
				->get()
				->toArray();
		}
		
		return $list;
	}
	
	
	/**
	 * 列表
	 */
	public function staffListAll3($arr, $columns = ['*'])
	{
		$where = [];
	
		$limit = $arr['limit'] ?? LIMIT_PAGE_SIZE;
	
		$list = [];
		
		// 搜索
		if (!empty($arr['keyword'])) {
			$filterArr = ['name', 'mobile'];
				
			// 去除空格
			$keyword = trim($arr['keyword']);
			$keyword = "%$keyword%";
				
			$list = (new StaffModel())
				->setTable('f1')
				->from(StaffModel::TABLE_NAME . ' AS f1')
				->whereNotExists(function($query) {
					$query->select(DB::raw(1))
					->from(StaffBindDepartmentModel::TABLE_NAME . ' AS f2')
					->whereRaw('f1.id = f2.staff_id');
					
				})->where(function ($q) use ($filterArr, $keyword) {
				$q = $q->where($filterArr[0], 'like', $keyword);
					
				foreach ($filterArr as $k => $v) {
					if ($k ==0) {
						continue;
					}
					$q = $q->orWhere($v, 'like', $keyword);
				}
				return $q;
					
			})
			->where($where)
			->orderBy('f1.number', 'asc')
			->limit($limit)
			->get($columns)
			->toArray();
				
		} else {
			$list = (new StaffModel())
				->setTable('f1')
				->from(StaffModel::TABLE_NAME . ' AS f1')
				->whereNotExists(function($query) {
					$query->select(DB::raw(1))
					->from(StaffBindDepartmentModel::TABLE_NAME . ' AS f2')
					->whereRaw('f1.id = f2.staff_id');
				})
				->where($where)
				->select($columns)
				->orderBy('f1.number', 'asc')
				->limit($limit)
				->get()
				->toArray();
		}

		return $list;
	}
	
	
	/**
	 * 详情
	 */
	public function staffDetail($where, $columns = ['*'])
	{
		$list = StaffModel::where($where)
			->limit(1)
			->get($columns)
			->toArray();

		if (empty($list)) {
			return [];
		}

		$staff = $list[0];
		$staff_id = $where['id']??$staff['id'];
		// 查询部门信息
		$resultDep = (new StaffBindDepartmentModel())
			->setTable('f1')
			->from(StaffBindDepartmentModel::TABLE_NAME . ' AS f1')
			->join(StaffDepartmentModel::TABLE_NAME . ' AS f2','f1.department_id','=','f2.id')
			->where([
				'f1.staff_id' => $staff_id
			])
			->limit(1)
			->get(['f2.name', 'f2.id'])
			->toArray();

		$staff['department_id'] = empty($resultDep[0]['id']) ? '' : $resultDep[0]['id'];
		$staff['department_name'] = empty($resultDep[0]['name']) ? '' : $resultDep[0]['name'];

		// 查询角色信息
		$resultRole = RoleBindStaffModel::where(['staff_id' => $staff_id])
			->orderBy('role_id', 'asc')
			->get(['role_id'])
			->toArray();

		$staff['role_list'] = empty($resultRole) ? [] : array_unique(array_column($resultRole, 'role_id'));
		
		return $staff;
	}
	
	/**
	 * 查找最新的一条
	 */
	public function findLast($arr)
	{
		$where = [];
		$list = StaffModel::withTrashed()
			->where($where)
			->select('number')
			->orderBy('number', 'desc')
			->limit(1)
			->get()
			->toArray();

		$result = empty($list) ? [] : $list[0];
		return $result;
	}
	
	/**
	 * 查找一条
	 */
	public function findDetail($where, $columns = ['*'])
	{
		$list = StaffModel::withTrashed()
			->where($where)
			->limit(1)
			->get($columns)
			->toArray();
	
		$result = empty($list) ? [] : $list[0];
		return $result;
	}
	
	/**
	 * 新增
	 */
	public function storeStaff($arr)
	{
		DB::beginTransaction();
	
		try{
				
			$result = $this->create($arr);
	
			// log
			storeActivityLog(ACTIVITY_TYPE['created'],
				ACTIVITY_SUBJECT_TYPE['staff'], $result['id'], $arr);
				
			DB::commit();
			return $result['id'];
	
		}catch (Exception $e){
			Log::error('staff storeStaff' . $e->getMessage());
			DB::rollBack();
			return false;
		}
	
	}
	
	/**
	 * 修改
	 */
	public function updateStaff($arr, $old = [])
	{
		DB::beginTransaction();
	
		$id = $arr['id'];
		try{
				
			// log
			storeActivityLog(ACTIVITY_TYPE['updated'],
				ACTIVITY_SUBJECT_TYPE['staff'], $arr['id'], $arr, $old);
				
			$this->update(array_except($arr,['id']), $arr['id']);
	
			DB::commit();
			return true;
	
		}catch (Exception $e){
			Log::error('staff updateStaff' . $e->getMessage());
			DB::rollBack();
			return false;
		}
	}
	
	/**
	 * 删除-批量
	 */
	public function deleteStaff($arr)
	{
		// 查询操作人员
		$tmpStaff = StaffBindDepartmentModel::where([
				'opertor_type' => STAFF_OPERTOR_TYPE['one']
			])
			->whereIn('staff_id', $arr['staff_list'])
			->limit(1)
			->get(['staff_id'])
			->toArray();
		
		if (!empty($tmpStaff)) {
			return Code::RBAC_STAFF_DELETE_ERROR;
		}
		
		// 判断是否为超管
		$roleList = (new RoleBindStaffModel())
			->setTable('f1')
			->from(RoleBindStaffModel::TABLE_NAME . ' AS f1')
			->join(RoleModel::TABLE_NAME . ' AS f2','f1.role_id','=','f2.id')
			->where([
				'f2.reserved' => RESERVED_YES
			])
			->whereIn('f1.staff_id', $arr['staff_list'])
			->orderBy('f1.staff_id', 'asc')
			->limit(1)
			->get(['f1.staff_id'])
			->toArray();

		if (!empty($roleList)) {
			return Code::RBAC_SUPER_ADMIN_DELETE_ERROR;
		}
		
		DB::beginTransaction();
		
		try{
			StaffModel::whereIn('id', $arr['staff_list'])->delete();
		
			// log
			storeActivityLog(ACTIVITY_TYPE['deleted'],
				ACTIVITY_SUBJECT_TYPE['staff'], $arr['staff_list'][0], $arr);
				
			DB::commit();
			return true;
				
		}catch (Exception $e){
			Log::error('staff deleteStaff' . $e->getMessage());
			DB::rollBack();
			return false;
		}
	}
	
	/**
	 * 查找-多条-未删除的
	 */
	public function staffListMany($where, $columns = ['*'])
	{
		$list = StaffModel::where($where)
			->limit(1)
			->get($columns)
			->toArray();
	
		return empty($list) ? [] : $list[0];
	}
	
	/**
	 * 查找-员工权限--接口
	 */
	public function checkPermission($arr)
	{
		$staffId = $arr['staff_id'];

		// 员工信息
		$whereStaff = [
			'id' => $staffId
		];
		$staffList = StaffModel::where($whereStaff)
			->limit(1)
			->get(['id'])
			->toArray();

		if (empty($staffList)) {
			// 用户已被删
			return Code::LOGIN_PERMISSION_ONE;
		}

		// 查询角色信息
		$whereRole = [];
		$whereRole[] = ['f1.staff_id', '=', $staffId];
		$roleList = (new RoleBindStaffModel())
			->setTable('f1')
			->from(RoleBindStaffModel::TABLE_NAME . ' AS f1')
			->join(RoleModel::TABLE_NAME . ' AS f2','f1.role_id','=','f2.id')
			->where($whereRole)
			->orderBy('f1.role_id', 'asc')
			->get(['f1.role_id', 'f2.reserved'])
			->toArray();
		
		if (empty($roleList)) {
			return Code::LOGIN_PERMISSION_TWO;
		}

		// 判断是否为超管
		$reserved = array_column($roleList, 'reserved');
		if (in_array(RESERVED_YES, $reserved)) {
			$staff = $staffList[0];
			return $staff;
		}

		// 查询API - 后期优化加缓存
		// TODO
		$roleIds = array_unique(array_column($roleList, 'role_id'));
		$apiList = (new RoleBindApiModel())
			->setTable('f1')
			->from(RoleBindApiModel::TABLE_NAME . ' AS f1')
			->join(ApiModel::TABLE_NAME . ' AS f2','f1.api_id','=','f2.id')
			->whereIn('role_id', $roleIds)
			->orderBy('f2.number', 'asc')
			->get(['f2.number'])
			->toArray();
		
		if (empty($apiList)) {
			return Code::LOGIN_PERMISSION_THREE;
		}

		$numberArr = array_unique(array_column($apiList, 'number'));
		if (!in_array($arr['number'], $numberArr)) {
			// 无权限
			return Code::LOGIN_PERMISSION_FOUR;
		}

		$staff = $staffList[0];
		return $staff;
	}

    /**
     * FUNCTION_NAME : getDepartment
     *
     * 获取用户的部门
     * @param $id
     * @return array
     */
	public function getDepartment($id)
    {
        $res = $this->model->select(['id'])->where('id', $id)->with('department:id,name')->first();
        return empty($res) ? [] : $res->toArray()['department'][0]??[];
    }

    /**
     * FUNCTION_NAME : getByIds
     *
     * 获取staff
     * @param $ids
     * @param array $column
     * @param null $trashed
     * @return mixed
     */
    public function getByIds($ids, $column=['*'], $trashed = null)
    {
        $model = $this->model;
        if ($trashed == QUERY_TRASHED) {
            $model = $model->withTrashed();
        }

        return $model->select($column)->whereIn('id', $ids)->get()->toArray();
    }

    public function getDepartmentByIds($ids)
    {
        return $this->model->select(['id'])->whereIn('id', $ids)->with('department:id,name')->get()->toArray();
    }

    public function list($search_arr, $column= ['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try {
            $this->pushCriteria(new KeywordCriteria($search_arr,['name', 'mobile']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            if ($per_page == NOT_PER_PAGE) {
                return $this->get($column)->toArray();
            }
            $res = $this->paginate($per_page, $column);
        } catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    public function getDepartmentInfo($id, $column = ['*'])
    {
        $res = $this->model->select($column)->where('id', $id)->with('department:id,name')->first();
        if (empty($res)) {
            return [];
        }
        $res = $res->toArray();
        $res['department'] = $res['department'][0]??[];
        return $res;
    }

    /**
     * FUNCTION_NAME : getDepartmentInfoList
     *
     * 获取指定用户集合 的部门
     * @param $ids
     * @param array $column
     * @return array
     */
    public function getDepartmentInfoList($ids, $column = ['*'])
    {
        $res = $this->model->select($column)->whereIn('id', $ids)->with('department:id,name')->get()->toArray();
        if (empty($res)) {
            return [];
        }
        foreach ($res as $key => &$value) {
            $value['department'] = $value['department'][0]??[];
        }
        return $res;
    }

    public function getByMobile($mobile, $column = ['*'])
    {
        $res = $this->model->select($column)->where('mobile', $mobile)->first();

        return empty($res) ? [] : $res->toArray();
    }

}
