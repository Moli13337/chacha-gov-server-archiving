<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:10
 */

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class OperationLogsModel extends BaseModel
{
    // 软删除
    use SoftDeletes;

    // 表名
    protected $table = 'operation_logs';

    const TABLE_NAME = 'operation_logs';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];
}