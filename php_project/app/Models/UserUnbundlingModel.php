<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * 接口
 * @author ASUS
 *
 */
class UserUnbundlingModel extends BaseModel
{
	// 表名
	protected $table = 'user_unbundling';
	
	const TABLE_NAME = 'user_unbundling';

	// 主键
	protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];
}
