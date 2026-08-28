<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * 申请经济指标表
 * @author lxh
 *
 */
class ApplyEconomyModel extends BaseModel
{
	// 表名
	protected $table = 'flo_apply_economy';
	
	const TABLE_NAME = 'flo_apply_economy';

	// 主键
	protected $primaryKey = 'id';
	
	// 黑名单
	protected $guarded = ['id'];
}
