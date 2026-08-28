<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * 角色-接口-关联
 * @author ASUS
 *
 */
class RoleBindApiModel extends BaseModel
{
	// 表名
	protected $table = 'role_bind_api';
	
	const TABLE_NAME = 'role_bind_api';
	
	// 联合主键
	protected $primaryKey = ['role_id', 'api_id'];
	
	// 非自增
	public $incrementing = false;
	
	// 时间手动维护
	public $timestamps = false;
	
	// 白名单
	protected $fillable = ['*'];
}
