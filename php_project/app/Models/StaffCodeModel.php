<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * 验证码
 */
class StaffCodeModel extends BaseModel
{
	// 表名
	protected $table = 'staff_code';

	// 主键
	protected $primaryKey = 'id';
	
	// 黑名单
	protected $guarded = ['id'];
}
