<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 审批表
 * @author lxh
 *
 */
class ApprovalModel extends BaseModel
{

    use SoftDeletes;

	// 表名
	protected $table = 'flo_approval';
	
	const TABLE_NAME = 'flo_approval';

	// 主键
	protected $primaryKey = 'id';
	
	// 黑名单
	protected $guarded = ['id'];
}
