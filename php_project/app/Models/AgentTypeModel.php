<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/17
 * Time: 19:06
 */

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class AgentTypeModel extends BaseModel
{
    use SoftDeletes;

    // 表名
    protected $table = 'agent_type';

    const TABLE_NAME = 'agent_type';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];
}