<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * token
 */
class StaffTokenModel extends BaseModel
{
	
	// 表名
	protected $table = 'staff_token';

	// 主键
	protected $primaryKey = 'id';
	
	// 时间手动维护
	public $timestamps = false;
	
	// 黑名单
	protected $guarded = ['id'];
}
