<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * 角色-资源-关联
 * @author ASUS
 *
 */
class RoleBindResourceModel extends BaseModel
{
	// 表名
	protected $table = 'role_bind_resource';
	
	const TABLE_NAME = 'role_bind_resource';
	
	// 联合主键
	protected $primaryKey = ['role_id', 'resource_id'];
	
	// 非自增
	public $incrementing = false;
	
	// 时间手动维护
	public $timestamps = false;
	
	// 白名单
	protected $fillable = ['*'];
}
