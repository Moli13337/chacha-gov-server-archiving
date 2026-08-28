<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 角色组
 * @author ASUS
 *
 */
class RoleTypeModel extends BaseModel
{
	// 软删除
	use SoftDeletes;
	
	// 表名
	protected $table = 'role_type';

	// 主键
	protected $primaryKey = 'id';
	
	// 黑名单
	protected $guarded = ['id'];
}
