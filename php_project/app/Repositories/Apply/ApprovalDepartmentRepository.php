<?php
namespace App\Repositories\Apply;

use App\Repositories\BaseRepository;
use App\Models\StaffBindDepartmentModel;
use App\Models\StaffDepartmentModel;
use App\Models\StaffModel;
use App\Models\ApprovalModel;
use App\Models\StaffDepartmentBindDepartmentModel;

/**
 * 用于查询审批中各个部门的专用接口
 * @author ASUS
 *
 */
class ApprovalDepartmentRepository  extends BaseRepository
{

	public function model()
	{
		return StaffDepartmentModel::class;
	}

	/**
	 * 查询操作人员-只有唯一一个
	 * $departmentType 1 区企业服务部门  4 指挥部  5 园区办公室
	 * $opertorType 1 操作人员
	 */
	public function getStaff($departmentType = 0, $opertorType = 0)
	{
		$departmentType = empty($departmentType) ? DEPARTMENT_TYPE['one'] : $departmentType;
		$opertorType = empty($opertorType) ? STAFF_OPERTOR_TYPE['one'] : $opertorType;
		
		$staff = (new StaffModel())
			->setTable('f1')
			->from(StaffModel::TABLE_NAME . ' AS f1')
			->join(StaffBindDepartmentModel::TABLE_NAME . ' AS f2','f2.staff_id','=','f1.id')
			->join(StaffDepartmentModel::TABLE_NAME . ' AS f3','f3.id','=','f2.department_id')
			->where([
				'f2.opertor_type' => $opertorType,
				'f3.type' => $departmentType
			])
			->limit(1)
			->get(['f2.staff_id', 'f2.department_id', 'f1.mobile'])
			->toArray();
		
		return empty($staff) ? [] : $staff[0];
	}
	
	/**
	 * 园区办公室
	 */
	public function getStaff2($arr)
	{
		$list1 = ApprovalModel::where([
				'apply_id' => $arr['apply_id'],
				'type' => APPROVAL_TYPE['two']
			])
			->limit(1)
			->get(['department_id'])
			->toArray();
			
		if (empty($list1)) {
			return [];
		}
		
		$departmentId = $list1[0]['department_id'];

		$list2 = StaffDepartmentBindDepartmentModel::where([
				'department_one_id' => $departmentId
			])
			->limit(1)
			->get(['department_two_id'])
			->toArray();
		
		if (empty($list2)) {
			return [];
		}
		
		$departmentId2 = $list2[0]['department_two_id'];

		$staff = (new StaffModel())
			->setTable('f1')
			->from(StaffModel::TABLE_NAME . ' AS f1')
			->join(StaffBindDepartmentModel::TABLE_NAME . ' AS f2','f2.staff_id','=','f1.id')
			->join(StaffDepartmentModel::TABLE_NAME . ' AS f3','f3.id','=','f2.department_id')
			->where([
				'f2.opertor_type' => STAFF_OPERTOR_TYPE['one'],
				'f2.department_id' => $departmentId2
			])
			->limit(1)
			->get(['f2.staff_id', 'f2.department_id', 'f1.mobile'])
			->toArray();
	
		return empty($staff) ? [] : $staff[0];
	}

    /**
     * 指挥部
     */
    public function getStaff3($arr)
    {
        $list1 = ApprovalModel::where([
            'apply_id' => $arr['apply_id'],
            'type' => APPROVAL_TYPE['two']
        ])
            ->limit(1)
            ->get(['department_id'])
            ->toArray();

        if (empty($list1)) {
            return [];
        }

        $departmentId = $list1[0]['department_id'];

        $list2 = StaffDepartmentBindDepartmentModel::where([
            'department_one_id' => $departmentId
        ])
            ->limit(1)
            ->get(['department_three_id'])
            ->toArray();

        if (empty($list2)) {
            return [];
        }

        $departmentId2 = $list2[0]['department_three_id'];

        $staff = (new StaffModel())
            ->setTable('f1')
            ->from(StaffModel::TABLE_NAME . ' AS f1')
            ->join(StaffBindDepartmentModel::TABLE_NAME . ' AS f2','f2.staff_id','=','f1.id')
            ->join(StaffDepartmentModel::TABLE_NAME . ' AS f3','f3.id','=','f2.department_id')
            ->where([
                'f2.opertor_type' => STAFF_OPERTOR_TYPE['one'],
                'f2.department_id' => $departmentId2
            ])
            ->limit(1)
            ->get(['f2.staff_id', 'f2.department_id', 'f1.mobile'])
            ->toArray();

        return empty($staff) ? [] : $staff[0];
    }

    /**
     * FUNCTION_NAME : getStaffByDepartment
     * author : jp
     * 通过部门id找操作员
     * @param $department_id
     * @return array
     */
    public function getStaffByDepartment($department_id)
    {
        $staff = (new StaffModel())
            ->setTable('f1')
            ->from(StaffModel::TABLE_NAME . ' AS f1')
            ->join(StaffBindDepartmentModel::TABLE_NAME . ' AS f2','f2.staff_id','=','f1.id')
            ->join(StaffDepartmentModel::TABLE_NAME . ' AS f3','f3.id','=','f2.department_id')
            ->where([
                'f2.opertor_type' => STAFF_OPERTOR_TYPE['one'],
                'f2.department_id' => $department_id
            ])
            ->limit(1)
            ->get(['f2.staff_id', 'f2.department_id', 'f1.mobile'])
            ->toArray();

        return empty($staff) ? [] : $staff[0];
    }

}
