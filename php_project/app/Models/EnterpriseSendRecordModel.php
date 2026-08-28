<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:05
 */

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class EnterpriseSendRecordModel extends BaseModel
{
    // 软删除
    use SoftDeletes;

    // 表名
    protected $table = 'enterprise_send_record';

    const TABLE_NAME = 'enterprise_send_record';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];
}