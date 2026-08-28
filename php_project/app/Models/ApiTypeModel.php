<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * 接口分类
 * @author ASUS
 *
 */
class ApiTypeModel extends BaseModel
{
	// 表名
	protected $table = 'api_type';
	
	const TABLE_NAME = 'api_type';

	// 主键
	protected $primaryKey = 'id';
}
