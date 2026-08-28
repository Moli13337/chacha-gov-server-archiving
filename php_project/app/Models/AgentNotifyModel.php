<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/17
 * Time: 19:06
 */

namespace App\Models;


use App\Models\Log\AgentNotifyLog;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentNotifyModel extends BaseModel
{
    use SoftDeletes;
    use BelongStaff;
    use AgentNotifyLog;

    // 表名
    protected $table = 'agent_notify';

    const TABLE_NAME = 'agent_notify';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    protected $appends = ['publish_status_desc'];

    /**
     * FUNCTION_NAME : getPublishStatusDescAttribute
     * author : jp
     * 发布状态描述
     * @return mixed
     */
    public function getPublishStatusDescAttribute()
    {
        return !isset($this->attributes['publish_status']) ? '' : array_get(trans('constant.publish_status'), $this->attributes['publish_status'], '');
    }

    public function file()
    {
        return $this->hasMany(AgentNotifyFileModel::class,'agent_notify_id');
    }
}