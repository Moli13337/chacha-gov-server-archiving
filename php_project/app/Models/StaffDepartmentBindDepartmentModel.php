<?php

namespace App\Models;

use App\Models\BaseModel;

class StaffDepartmentBindDepartmentModel extends BaseModel
{
	// 表名
	protected $table = 'staff_department_bind_department';
	
	const TABLE_NAME = 'staff_department_bind_department';

	// 联合主键
	protected $primaryKey = ['department_one_id', 'department_two_id'];
	
	// 非自增
	public $incrementing = false;
	
	// 时间手动维护
	public $timestamps = false;
	
	// 白名单
	protected $fillable = ['*'];
}
