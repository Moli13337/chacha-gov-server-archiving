<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/17
 * Time: 19:06
 */

namespace App\Models;


use App\Models\Log\AgentCreditLog;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentCreditModel extends BaseModel
{
    use SoftDeletes;

    use BelongStaff;
    use AgentCreditLog;

    // 表名
    protected $table = 'agent_credit';

    const TABLE_NAME = 'agent_credit';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    protected $appends = ['credit_type_name', 'audit_name'];

    public function getCreditTypeNameAttribute()
    {
        return !isset($this->attributes['type']) ? '' : array_get(trans('constant.agent_credit_type'), $this->attributes['type'], '');
    }

    public function getAuditNameAttribute()
    {
        return !isset($this->attributes['is_audit']) ? '' : array_get(trans('constant.is_audit'), $this->attributes['is_audit'], '');
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
}