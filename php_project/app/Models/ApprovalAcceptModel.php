<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * 受理申报记录
 * @author lxh
 *
 */
class ApprovalAcceptModel extends BaseModel
{
	public $timestamps = FALSE;
	
	// 表名
	protected $table = 'flo_approval_accept';
	
	const TABLE_NAME = 'flo_approval_accept';

	// 主键
	protected $primaryKey = 'id';
	
	// 黑名单
	protected $guarded = ['id'];
}
