<?php
namespace App\Repositories\Staff;

use App\Models\StaffDepartmentModel;
use App\Models\StaffBindDepartmentModel;
use Illuminate\Support\Facades\DB;
use App\Models\StaffModel;
use Exception;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Log;
use App\Common\Code;
use App\Models\StaffDepartmentBindDepartmentModel;

class StaffDepartmentRepository  extends BaseRepository
{
	public function model()
	{
		return StaffDepartmentModel::class;
	}

	/**
	 * name 唯一性检查
	 * $isUpdate: true 更新  false 新增
	 */
	public function checkUnique($arr, $isUpdate = false)
	{
		$where = [];
		if ($isUpdate) {
			$where[] = ['id', '<>', $arr['id']];
		}

		// type 唯一  - 类型1区企业服务中心
		if (in_array($arr['type'], [DEPARTMENT_TYPE['one']])) {
			$where[] = ['type', '=', $arr['type']];
			$staff = StaffDepartmentModel::where($where)->limit(1)->get(['id'])->toArray();
			if (!empty($staff)) {
				// 不存在
				return Code::RBAC_DEPARTMENT_TYPE_UNIQUE_ERROR;
			}

			if ($isUpdate) {
				unset($where[1]);
			} else {
				unset($where[0]);
			}
		}

		// name 唯一
		$where[] = ['name', '=', $arr['name']];
		$staff = StaffDepartmentModel::where($where)->limit(1)->get(['id'])->toArray();
		if (!empty($staff)) {
			return Code::RBAC_DEPARTMENT_NAME_UNIQUE_ERROR;
		}

		return true;
	}

	/**
	 * 列表
	 */
	public function list($arr)
	{
		$where = [];

		// 搜索
		if (!empty($arr['keyword'])) {
			// 去除空格
			$keyword = trim($arr['keyword']);
			$keyword = '%'.addslashes($keyword).'%';
			$where[] = ['name', 'like', $keyword];
		}

		$list = StaffDepartmentModel::where($where)
			->orderBy('id', 'asc')
			->get(['id', 'name AS label', 'parent_id', 'type'])
			->toArray();

		return returnPage($list, count($list));
	}

	/**
	 * 列表
	 * type 1 园区管委会 和全部 2 协同部门
	 */
	public function listAll($arr)
	{
		$where = [];

		// 搜索
		if (!empty($arr['keyword'])) {
			// 去除空格
			$keyword = trim($arr['keyword']);
			$keyword = '%'.addslashes($keyword).'%';
			$where[] = ['name', 'like', $keyword];
		}
		$model = StaffDepartmentModel::where($where);

		// 1 园区管委会 和全部 2 协同部门
		$type = empty($arr['type']) ? 1 : $arr['type'];
		if ($type == 2) {
			$typeArr = [DEPARTMENT_TYPE['two']];
			$model->whereIn('type', $typeArr);
		}

		$list = $model->orderBy('id', 'asc')
			->get(['id', 'name', 'type'])
			->toArray();

		$listThree = [];
		if ($type == 1) {
			foreach ($list as $key => $value) {
				if ($value['type'] == DEPARTMENT_TYPE['three']) {
					$listThree[] = $value;
				}
			}
		}

		$result = [
			'list' => $list ?? [],
			'list_park' => $listThree
		];
		return $result;
	}

	/**
	 * 详情
	 */
	public function departmentDetail($where, $columns = ['*'])
	{
		$list = StaffDepartmentModel::where($where)
			->select($columns)
			->limit(1)
			->get()
			->toArray();

		if (!empty($list)) {
			$detail = $list[0];
			// 查询部门经理
			$manager = StaffModel::find($detail['manager_id']);
			$detail['manager_name'] = empty($manager) ? '' : $manager->name;

			// 上级部门
			$depart = StaffDepartmentModel::find($detail['parent_id']);
			$detail['parent_name'] = empty($depart) ? '' : $depart->name;
		}

		return $detail;
	}

	/**
	 * 查找一条
	 * @param unknown $where
	 * @param array $columns
	 * @param string $isNeedParent 是否需要上级部门信息
	 */
	public function findDetail($where, $columns = ['*'], $isNeedParent = false)
	{
		$list = StaffDepartmentModel::where($where)
			->limit(1)
			->get($columns)
			->toArray();

		$result = [];
		if (!empty($list)) {
			$result = $list[0];

			if ($isNeedParent) {
				// 查询部门经理
				$manager = StaffModel::find($result['manager_id']);
				$result['manager_name'] = empty($manager) ? '' : $manager->name;
			}
		}
		return $result;
	}

	/**
	 * 新增
	 */
	public function storeDepartment($arr)
	{
		DB::beginTransaction();

		try{

			$result = $this->create($arr);

			// log
			storeActivityLog(ACTIVITY_TYPE['created'],
				ACTIVITY_SUBJECT_TYPE['department'], $result['id'], $arr);

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
	public function updateDepartment($arr, $old = [])
	{
		DB::beginTransaction();

		$id = $arr['id'];
		try{

			// log
			storeActivityLog(ACTIVITY_TYPE['updated'],
				ACTIVITY_SUBJECT_TYPE['department'], $arr['id'], $arr, $old);

			$this->update(array_except($arr,['id']), $arr['id']);

			DB::commit();
			return true;

		}catch (Exception $e){
			Log::error('department updateDepartment' . $e->getMessage());
			DB::rollBack();
			return false;
		}
	}

	/**
	 * 删除部门
	 */
	public function deleteDepartment($arr)
	{
		DB::beginTransaction();

		try{
			// 删除当前部门和下属部门
			StaffDepartmentModel::where(['id' => $arr['id']])->delete();

			//$this->delTreeChild($arr['id']);

			// 删除绑定员工
			StaffBindDepartmentModel::where(['department_id' => $arr['id']])->delete();

			// log
			storeActivityLog(ACTIVITY_TYPE['deleted'],
				ACTIVITY_SUBJECT_TYPE['department'], $arr['id'], $arr);

			DB::commit();
			return true;

		}catch (Exception $e){
			Log::error('department deleteDepartment' . $e->getMessage());
			DB::rollBack();
			return false;
		}
	}

	/**
	 * 递归删除
	 */
	public function delTreeChild($id)
	{
		$document = StaffDepartmentModel::find($id);
		if (!$document) {
			return false;
		}
		$documentOne = StaffDepartmentModel::where(['parent_id' => $id])->get();
		if (count($documentOne) != 0) {
			$document->delete();
			foreach ($documentOne as $item){
				self::delTreeChild($item->id);
			}
			return true;
		} else {
			$document->delete();
			return true;
		}
	}

	/**
	 * 绑定员工
	 */
	public function bindStaff($arr)
	{
		$storeData = [];
		$staffList = $arr['staff_list'];
		$currentTime = time();
		$operatorStaffId = '';
		foreach ($staffList as $key => $value) {
			$storeData[] = [
				'staff_id' => $value['staff_id'],
				'department_id' => $arr['id'],
				'created_at' => $currentTime,
				'opertor_type' => $value['opertor_type']
			];

			if ($value['opertor_type'] == STAFF_OPERTOR_TYPE['one']) {
				$operatorStaffId = $value;
			}
		}

		// 判断操作人员唯一
		if (!empty($operatorStaffId)) {
			$where = [];
			$where[] = ['f2.opertor_type', '=', STAFF_OPERTOR_TYPE['one']];
			$where[] = ['f2.department_id', '=', $arr['id']];
			$where[] = ['f2.staff_id', '!=', $operatorStaffId];
			$bindList = (new StaffModel())
				->setTable('f1')
				->from(StaffModel::TABLE_NAME . ' AS f1')
				->join(StaffBindDepartmentModel::TABLE_NAME . ' AS f2','f2.staff_id','=','f1.id')
				->where($where)
				->limit(1)
				->get(['f2.staff_id'])
				->toArray();

			if (!empty($bindList)) {
				return Code::RBAC_OPERATOR_ONE_EXIST_ERROR;
			}
		}

		try{
			StaffBindDepartmentModel::insert($storeData);
		}catch (Exception $e){
			Log::error('staffDepartment bindStaff' . $e->getMessage());
			return false;
		}
		return true;
	}

	/**
	 * 员工列表
	 */
	public function getStaffList($arr)
	{
		$where = [];
		$where[] = ['f1.department_id', '=', $arr['id']];
		$tmpModel = (new StaffModel())
			->setTable('f2')
			->from(StaffModel::TABLE_NAME . ' AS f2')
			->leftJoin(StaffBindDepartmentModel::TABLE_NAME . ' AS f1','f1.staff_id','=','f2.id')
			->where($where);

		$staffCount = $tmpModel->select(['f2.id'])->count();

		$page = commonPage($arr);

		$staffList = $tmpModel
			->select([
				'f2.id',
				'f2.name',
				'f2.mobile',
				'f2.sex',
				'f2.email',
				'f2.photo_url',
				'f2.created_at',
				'f1.opertor_type'
			])
			->orderBy('f1.opertor_type', 'asc')
			->offset($page['offset'])
			->limit($page['page_size'])
			->get()
			->toArray();

		return returnPage($staffList, $staffCount);
	}

	/**
	 * 删除员工
	 */
	public function deleteStaff($arr)
	{
		$staffList = $arr['staff_list'];

		// 操作人员不能删除
		$where = [];
		$where[] = ['f2.department_id', '=', $arr['id']];
		$where[] = ['f2.opertor_type', '=', STAFF_OPERTOR_TYPE['one']];
		$tmpStaff = $bindList = (new StaffModel())
			->setTable('f1')
			->from(StaffModel::TABLE_NAME . ' AS f1')
			->join(StaffBindDepartmentModel::TABLE_NAME . ' AS f2','f2.staff_id','=','f1.id')
			->where($where)
			->limit(1)
			->get(['f2.staff_id'])
			->toArray();

		if (!empty($tmpStaff)) {
			foreach ($staffList as $key => $value) {
				if ($value == $tmpStaff[0]['staff_id']) {
					return Code::RBAC_OPERATOR_ONE_DELETE_ERROR;
				}
			}
		}

		DB::beginTransaction();
		try{

			foreach ($staffList as $key => $value) {
				$tmpWhere = [
					['department_id', '=', $arr['id']],
					['staff_id', '=', $value]
				];
				StaffBindDepartmentModel::where($tmpWhere)->delete();
			}

			DB::commit();
			return true;

		}catch (Exception $e){
			Log::error('department deleteStaff' . $e->getMessage());
			DB::rollBack();
			return false;
		}
	}

	/**
	 * 修改审核权限
	 */
	public function updateOperator($arr)
	{
		$tmpStaff = [];
		if ($arr['opertor_type'] == STAFF_OPERTOR_TYPE['one']) {
			// 判断之前操作人员是否已经存在，存在替换
			$where = [];
			$where[] = ['f1.department_id', '=', $arr['id']];
			$tmpStaff = StaffBindDepartmentModel::where([
					'department_id' => $arr['id'],
					'opertor_type' => STAFF_OPERTOR_TYPE['one']
				])
				->limit(1)
				->get(['staff_id'])
				->toArray();
		}

		DB::beginTransaction();

		try{
			// 将之前操作人改为普通人员
			if (!empty($tmpStaff)) {
				StaffBindDepartmentModel::where([
					'department_id' => $arr['id'],
					'staff_id' => $tmpStaff[0]['staff_id']
				])
				->update([
					'opertor_type' => STAFF_OPERTOR_TYPE['three']
				]);
			}

			StaffBindDepartmentModel::where([
				'department_id' => $arr['id'],
				'staff_id' => $arr['staff_id']
			])
			->update([
				'opertor_type' => $arr['opertor_type']
			]);

			DB::commit();
			return true;

		}catch (Exception $e){
			Log::error('department updateOperator' . $e->getMessage());
			DB::rollBack();
			return false;
		}

	}

	/**
	 * 列表
	 */
	public function getNotBindDepartmentList($arr)
	{
		$where = [];
		$typeArr = [DEPARTMENT_TYPE['three'], DEPARTMENT_TYPE['four'],DEPARTMENT_TYPE['five']];

		$list = StaffDepartmentModel::where($where)
			->whereIn('type', $typeArr)
			->orderBy('id', 'asc')
			->get(['id', 'name', 'type'])
			->toArray();

		$listBind = StaffDepartmentBindDepartmentModel::where($where)
				->get(['department_one_id', 'department_two_id', 'department_three_id'])
				->toArray();

		$oneList = array_column($listBind, 'department_one_id');
		$twoList = array_column($listBind, 'department_two_id');
		$threeList = array_column($listBind, 'department_three_id');

		$resultOne = [];
		$resultTwo = [];
		$resultThree = [];
		foreach ($list as $key => $value) {
			if ($value['type'] == DEPARTMENT_TYPE['three']) {
				if (!in_array($value['id'], $oneList)) {
					$resultOne[] = $value;
				}
			} else if ($value['type'] == DEPARTMENT_TYPE['five']) {
				if (!in_array($value['id'], $twoList)) {
					$resultTwo[] = $value;
				}
			} else if ($value['type'] == DEPARTMENT_TYPE['four']) {
                if (!in_array($value['id'], $threeList)) {
                    $resultThree[] = $value;
                }
            }
		}

		return [
			'one_list' => $resultOne,
			'two_list' => $resultTwo,
			'three_list' => $resultThree,
		];
	}

	/**
	 * 列表
	 */
	public function getBindDepartmentList($arr)
	{
		$where = [];

		$count = StaffDepartmentBindDepartmentModel::where($where)->count();

		$page = commonPage($arr);

		$list = $bindList = (new StaffDepartmentBindDepartmentModel())
			->setTable('f1')
			->from(StaffDepartmentBindDepartmentModel::TABLE_NAME . ' AS f1')
			->join(StaffDepartmentModel::TABLE_NAME . ' AS f2','f2.id','=','f1.department_one_id')
			->join(StaffDepartmentModel::TABLE_NAME . ' AS f3','f3.id','=','f1.department_two_id')
			->leftJoin(StaffDepartmentModel::TABLE_NAME . ' AS f4','f4.id','=','f1.department_three_id')
			->where($where)
			->offset($page['offset'])
			->limit($page['page_size'])
			->get([
				'f1.department_one_id',
				'f1.department_two_id',
				'f1.department_three_id',
				'f2.name AS department_one_name',
				'f3.name AS department_two_name',
				'f4.name AS department_three_name',
			])
			->toArray();

		return returnPage($list, $count);
	}

	/**
	 * 绑定部门
	 */
	public function bindDepartment($arr)
	{
	    $department_three_id = empty($arr['department_three_id']) ? 0 : $arr['department_three_id'];
		DB::beginTransaction();
		try{

			StaffDepartmentBindDepartmentModel::where([
				'department_one_id' => $arr['department_one_id'],
				'department_two_id' => $arr['department_two_id'],
				'department_three_id' => $department_three_id,
			])->delete();

			StaffDepartmentBindDepartmentModel::insert([
				'department_one_id' => $arr['department_one_id'],
				'department_two_id' => $arr['department_two_id'],
				'department_three_id' => $department_three_id,
			]);

			DB::commit();
			return true;

		}catch (Exception $e){
			Log::error('department bindDepartment' . $e->getMessage());
			DB::rollBack();
			return false;
		}
	}

	/**
	 * 绑定部门
	 */
	public function deleteBindDepartment($arr)
	{
		DB::beginTransaction();
		try{

			StaffDepartmentBindDepartmentModel::where([
				'department_one_id' => $arr['department_one_id'],
				'department_two_id' => $arr['department_two_id'],
				'department_three_id' => $arr['department_three_id']
			])->delete();

			DB::commit();
			return true;

		}catch (Exception $e){
			Log::error('department deleteBindDepartment' . $e->getMessage());
			DB::rollBack();
			return false;
		}
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

    public function getDepartment($where, $column = ['*'])
    {
        return $this->model->select($column)->where($where)->get()->toArray();
    }

    public function getOperatorStaff($id, $column = ['*'])
    {
        $res = $this->model->select($column)->where('id', $id)->with(['operatorStaff:staff_id,name,mobile'])->first();

        if (empty($res)) {
            return [];
        }
        $res = $res->toArray();
        $staff = array_get($res['operator_staff'], 0,[]);
        $data = [
            'department_id' => $res['id'],
            'department_name' => $res['name'],
            'staff_id' => array_get($staff, 'staff_id', 0),
            'staff_name' => array_get($staff, 'name', 0),
            'mobile' => array_get($staff, 'mobile', 0),
        ];
        return $data;
    }

    public function getOperatorStaffByIds($ids, $column = ['*'])
    {
        $res = $this->model->select($column)->whereIn('id', $ids)->with(['operatorStaff:staff_id,name,mobile'])->get()->toArray();

        if (empty($res)) {
            return [];
        }
        $data = [];
        foreach ($res as $k => $v) {
            $staff = array_get($v['operator_staff'], 0,[]);
            $data[] = [
                'department_id' => $v['id'],
                'department_name' => $v['name'],
                'staff_id' => array_get($staff, 'staff_id', 0),
                'staff_name' => array_get($staff, 'name', 0),
                'mobile' => array_get($staff, 'mobile', 0),
            ];
        }

        return $data;
    }

    public function getOperatorStaffByAll($column = ['*'])
    {
        $res = $this->model->select($column)->with(['operatorStaff:staff_id,name,mobile'])->get()->toArray();

        if (empty($res)) {
            return [];
        }
        $data = [];
        foreach ($res as $k => $v) {
            $staff = array_get($v['operator_staff'], 0,[]);
            $data[] = [
                'department_id' => $v['id'],
                'department_name' => $v['name'],
                'staff_id' => array_get($staff, 'staff_id', 0),
                'staff_name' => array_get($staff, 'name', 0),
                'mobile' => array_get($staff, 'mobile', 0),
            ];
        }

        return $data;
    }
}
