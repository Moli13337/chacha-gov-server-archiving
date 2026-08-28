<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:02
 */

namespace App\Models;


use App\Models\Log\UserLog;
use App\Models\Steward\StewardUserModel;
use App\Repositories\User\UserEnterpriseRelationRepository;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserModel extends BaseModel
{
    // 软删除
    use SoftDeletes;

    use UserLog;

    // 表名
    protected $table = 'user';

    const TABLE_NAME = 'user';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    public function enterprise()
    {
//        return $this->belongsToMany(EnterpriseModel::class, UserEnterpriseRelationModel::TABLE_NAME,
//             'user_id', 'enterprise_id')->where(function ($query){
//                 $query->whereNull(UserEnterpriseRelationModel::TABLE_NAME . '.deleted_at');
//        });

        return $this->belongsToMany(EnterpriseModel::class, UserEnterpriseRelationModel::TABLE_NAME,
            'user_id', 'enterprise_id')->whereNull(UserEnterpriseRelationModel::TABLE_NAME . '.deleted_at');
    }

    public function followIndustry()
    {
        return $this->hasMany(UserFollowIndustryModel::class,'user_id');
    }
}