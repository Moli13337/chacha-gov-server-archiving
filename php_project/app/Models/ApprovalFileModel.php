<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * 审批附件
 * @author ASUS
 *
 */
class ApprovalFileModel extends BaseModel
{
	public $timestamps = FALSE;
	
	// 表名
	protected $table = 'flo_approval_file';
	
	const TABLE_NAME = 'flo_approval_file';

	// 主键
	protected $primaryKey = 'id';
	
	// 黑名单
	protected $guarded = ['id'];
}
