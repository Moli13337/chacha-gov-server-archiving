<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/17
 * Time: 19:06
 */

namespace App\Models;


use App\Models\Log\AgentLog;
use App\Models\Traits\IsNewPublishTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentModel extends BaseModel
{
    use SoftDeletes;

    use AgentLog;

    // 表名
    protected $table = 'agent';

    const TABLE_NAME = 'agent';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    protected $appends = ['credit_type_name', 'is_new'];

    use IsNewPublishTrait;

    public function getCreditTypeNameAttribute()
    {
        return !isset($this->attributes['credit_type']) ? '' : array_get(trans('constant.agent_credit_type'), $this->attributes['credit_type'], '');

    }

    public function file()
    {
        return $this->hasMany(AgentFileModel::class,'agent_id');
    }

    public function enterprise()
    {
        return $this->belongsTo(EnterpriseModel::class, 'enterprise_id')->withTrashed();
    }

    public function agentType()
    {
        return $this->belongsTo(AgentTypeModel::class, 'type_id');
    }

    public function credit()
    {
        return $this->hasMany(AgentCreditModel::class, 'agent_id');
    }

    public function collections()
    {
        return $this->hasMany(UserCollectionModel::class,'obj_id');
    }
}