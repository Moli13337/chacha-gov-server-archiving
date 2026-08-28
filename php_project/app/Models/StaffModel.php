<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 员工
 */
class StaffModel extends BaseModel
{
	// 软删除
	use SoftDeletes;
	
	// 表名
	protected $table = 'staff';
	
	const TABLE_NAME = 'staff';

	// 主键
	protected $primaryKey = 'id';
	
	// 黑名单
	protected $guarded = ['id'];

	public function department()
    {
        return $this->belongsToMany(
            StaffDepartmentModel::class,
            StaffBindDepartmentModel::TABLE_NAME,
            'staff_id',
            'department_id'
        );
    }

}
