<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/17
 * Time: 19:06
 */

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class AgentNotifyFileModel extends BaseModel
{
    use SoftDeletes;

    // 表名
    protected $table = 'agent_notify_file';

    const TABLE_NAME = 'agent_notify_file';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];
}