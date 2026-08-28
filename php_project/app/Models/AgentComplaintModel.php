<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/17
 * Time: 19:06
 */

namespace App\Models;


use App\Models\Log\AgentComplaintLog;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentComplaintModel extends BaseModel
{
    use SoftDeletes;
    use AgentComplaintLog;

    // 表名
    protected $table = 'agent_complaint';

    const TABLE_NAME = 'agent_complaint';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    protected $appends = ['status_name', 'type_name'];

    public function getStatusNameAttribute()
    {
        return !isset($this->attributes['status']) ? '' : array_get(trans('constant.agent_complaint_status'), $this->attributes['status'], '');
    }

    public function getTypeNameAttribute()
    {
        return !isset($this->attributes['type']) ? '' : array_get(trans('constant.agent_complaint_type'), $this->attributes['type'], '');
    }

    public function agent()
    {
        return $this->belongsToMany(
            EnterpriseModel::class,
            AgentModel::TABLE_NAME,
            'id',
            'enterprise_id',
            'agent_id'
        )->whereNull(AgentModel::TABLE_NAME.'.deleted_at')->withPivot(['id', 'enterprise_id', 'type_id']);
    }

    public function agentType()
    {
        return $this->belongsToMany(
            AgentTypeModel::class,
            AgentModel::TABLE_NAME,
            'id',
            'type_id',
            'agent_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function enterprise()
    {
        return $this->belongsTo(EnterpriseModel::class, 'enterprise_id');

    }

    public function staff()
    {
        return $this->belongsTo(StaffModel::class, 'user_id');
    }

}