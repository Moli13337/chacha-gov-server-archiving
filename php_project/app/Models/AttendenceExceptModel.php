<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * 记录工作日放假和周末上班
 * @author lxh
 *
 */
class AttendenceExceptModel extends BaseModel
{
	// 表名
	protected $table = 'attendence_except';
	
	const TABLE_NAME = 'attendence_except';

	// 主键
	protected $primaryKey = 'id';
	
	// 黑名单
	protected $guarded = ['id'];
}
