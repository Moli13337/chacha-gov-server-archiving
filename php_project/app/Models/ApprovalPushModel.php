<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * 审批表
 * @author lxh
 *
 */
class ApprovalPushModel extends BaseModel
{
	// 表名
	protected $table = 'flo_approval_push';
	
	const TABLE_NAME = 'flo_approval_push';

	// 主键
	protected $primaryKey = 'id';
	
	// 黑名单
	protected $guarded = ['id'];
}
