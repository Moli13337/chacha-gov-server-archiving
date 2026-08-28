<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 申请表附件内容检查-异常
 * @author lxh
 *
 */
class ApplyFileExceptionModel extends BaseModel
{

    use SoftDeletes;

	// 表名
	protected $table = 'flo_apply_file_exception';
	
	const TABLE_NAME = 'flo_apply_file_exception';

	// 主键
	protected $primaryKey = 'id';
	
	// 黑名单
	protected $guarded = ['id'];
}
