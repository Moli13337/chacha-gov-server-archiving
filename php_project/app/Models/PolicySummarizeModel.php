<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:05
 */

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class PolicySummarizeModel extends BaseModel
{
    // 软删除
    use SoftDeletes;

    // 表名
    protected $table = 'policy_summarize';

    const TABLE_NAME = 'policy_summarize';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];
}