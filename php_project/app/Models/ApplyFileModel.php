<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 申请表附件
 * @author lxh
 *
 */
class ApplyFileModel extends BaseModel
{

    use SoftDeletes;

	// 表名
	protected $table = 'flo_apply_file';
	
	const TABLE_NAME = 'flo_apply_file';

	// 主键
	protected $primaryKey = 'id';
	
	// 黑名单
	protected $guarded = ['id'];
}
