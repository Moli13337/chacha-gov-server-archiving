<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * 员工-部门-关联
 * @author ASUS
 *
 */
class StaffBindDepartmentModel extends BaseModel
{
	// 表名
	protected $table = 'staff_bind_department';
	
	const TABLE_NAME = 'staff_bind_department';

	// 联合主键
	protected $primaryKey = ['staff_id', 'department_id'];
	
	// 非自增
	public $incrementing = false;
	
	// 时间手动维护
	public $timestamps = false;
	
	// 白名单
	protected $fillable = ['*'];
}
