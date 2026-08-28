<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:05
 */

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class LoginLogsModel extends BaseModel
{
    // 软删除
    use SoftDeletes;

    // 表名
    protected $table = 'login_logs';

    const TABLE_NAME = 'login_logs';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    public function getIpAttribute()
    {
        return !isset($this->attributes['ip']) ? '' : long2ip($this->attributes['ip']);
    }
}