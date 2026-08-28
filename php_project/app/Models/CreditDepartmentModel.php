<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * 申请表
 * @author lxh
 *
 */
class CreditDepartmentModel extends BaseModel
{
	// 表名
	protected $table = 'credit_department';
	
	const TABLE_NAME = 'credit_department';

	// 主键
	protected $primaryKey = 'id';
	
	// 黑名单
	protected $guarded = ['id'];
}
