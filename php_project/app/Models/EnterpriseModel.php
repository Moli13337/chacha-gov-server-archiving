<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:05
 */

namespace App\Models;


use App\Models\Log\EnterpriseLog;
use App\Repositories\Agent\AgentRepository;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnterpriseModel extends BaseModel
{
    // 软删除
    use SoftDeletes;

    use EnterpriseLog;

    // 表名
    protected $table = 'enterprise';

    const TABLE_NAME = 'enterprise';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];


    public function user()
    {
        return $this->belongsToMany(
            UserModel::class,
            UserEnterpriseRelationModel::TABLE_NAME,
            'enterprise_id',
            'user_id')->whereNull(UserEnterpriseRelationModel::TABLE_NAME.'.deleted_at');
    }

    public function agent()
    {
        return $this->hasMany(AgentModel::class,'enterprise_id');
    }

    public function industry()
    {
        return $this->hasOne(EnterpriseIndustryModel::class, 'enterprise_id');
    }
}