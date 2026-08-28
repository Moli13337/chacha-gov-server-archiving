<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * 审批意见
 * @author lxh
 *
 */
class ApprovalOpinionModel extends BaseModel
{
	public $timestamps = FALSE;
	
	// 表名
	protected $table = 'flo_approval_opinion';
	
	const TABLE_NAME = 'flo_approval_opinion';

	// 主键
	protected $primaryKey = 'id';
	
	// 黑名单
	protected $guarded = ['id'];
}
