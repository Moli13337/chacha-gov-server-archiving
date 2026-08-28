<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * 审批理由和补充资料表
 * @author lxh
 *
 */
class ApprovalMarkModel extends BaseModel
{
	// 表名
	protected $table = 'flo_approval_mark';
	
	const TABLE_NAME = 'flo_approval_mark';

	// 主键
	protected $primaryKey = 'id';
	
	// 黑名单
	protected $guarded = ['id'];
}
