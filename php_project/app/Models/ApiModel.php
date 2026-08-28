<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * 接口
 * @author ASUS
 *
 */
class ApiModel extends BaseModel
{
	// 表名
	protected $table = 'api';
	
	const TABLE_NAME = 'api';

	// 主键
	protected $primaryKey = 'id';
}
