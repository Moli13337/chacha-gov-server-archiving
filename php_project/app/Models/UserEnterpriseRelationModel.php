<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:06
 */

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class UserEnterpriseRelationModel extends BaseModel
{
    // 软删除
    use SoftDeletes;

    // 表名
    protected $table = 'user_enterprise_relation';

    const TABLE_NAME = 'user_enterprise_relation';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function enterprise()
    {
        return $this->belongsTo(EnterpriseModel::class, 'enterprise_id');
    }

}