<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * 资源
 * @author ASUS
 *
 */
class ResourceModel extends BaseModel
{
	// 表名
	protected $table = 'resource';
	
	const TABLE_NAME = 'resource';

	// 主键
	protected $primaryKey = 'id';
}
