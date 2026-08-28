<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 部门
 * @author ASUS
 *
 */
class StaffDepartmentModel extends BaseModel
{
	// 软删除
	use SoftDeletes;
	
	// 表名
	protected $table = 'staff_department';
	
	const TABLE_NAME = 'staff_department';

	// 主键
	protected $primaryKey = 'id';
	
	// 黑名单
	protected $guarded = ['id'];

	public function operatorStaff() {
	    return $this->hasManyThrough(StaffModel::class,
            StaffBindDepartmentModel::class,
            'department_id',
            'id',
            'id',
            'staff_id'
        )->where(StaffBindDepartmentModel::TABLE_NAME.'.opertor_type', STAFF_OPERTOR_TYPE['one']);
    }
}
