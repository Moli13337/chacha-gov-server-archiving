<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/17
 * Time: 19:06
 */

namespace App\Models;


use App\Models\Log\AgentCommentLog;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentCommentModel extends BaseModel
{
    use SoftDeletes;
    use AgentCommentLog;

    // 表名
    protected $table = 'agent_comment';

    const TABLE_NAME = 'agent_comment';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id')->where(AgentCommentModel::TABLE_NAME.'.user_type', MESSAGE_USER_TYPE['user']);
    }

    public function staff()
    {
        return $this->belongsTo(StaffModel::class, 'user_id')->where(AgentCommentModel::TABLE_NAME.'.user_type', MESSAGE_USER_TYPE['staff']);
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