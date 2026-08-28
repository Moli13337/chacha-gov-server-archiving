<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * 审批配置表
 * @author lxh
 *
 */
class ApprovalConfigModel extends BaseModel
{
	public $timestamps = FALSE;
	
	// 表名
	protected $table = 'flo_approval_config';
	
	const TABLE_NAME = 'flo_approval_config';

	// 主键
	protected $primaryKey = 'id';
	
	// 黑名单
	protected $guarded = ['id'];
}
