<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 角色
 * @author ASUS
 *
 */
class RoleModel extends BaseModel
{
	// 软删除
	use SoftDeletes;
	
	// 表名
	protected $table = 'role';
	
	const TABLE_NAME = 'role';

	// 主键
	protected $primaryKey = 'id';
	
	// 黑名单
	protected $guarded = ['id'];
}
