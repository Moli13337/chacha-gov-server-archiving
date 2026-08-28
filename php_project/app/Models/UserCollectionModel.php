<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/12
 * Time: 10:08
 */

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class UserCollectionModel extends BaseModel
{

    use SoftDeletes;

    // 表名
    protected $table = 'user_collection';

    const TABLE_NAME = 'user_collection';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];


    public function collectionable()
    {
        return $this->morphTo();
    }

    public function policy()
    {
        return $this->belongsTo(PolicyModel::class, 'obj_id')
            ->whereIn(self::TABLE_NAME.'.obj_type', [
                OBJ_TYPE['macro_policy'],
                OBJ_TYPE['sup_policy'],
                OBJ_TYPE['imple_regu'],
                OBJ_TYPE['announce'],
                OBJ_TYPE['publicity'],
                OBJ_TYPE['approval'],
            ]);
    }

    public function project()
    {
        return $this->belongsTo(ProjectModel::class, 'obj_id')
            ->whereIn(self::TABLE_NAME.'.obj_type', [
                OBJ_TYPE['project'],
            ]);
    }

    public function agent()
    {
        return $this->belongsTo(AgentModel::class, 'obj_id')
            ->whereIn(self::TABLE_NAME.'.obj_type', [
                OBJ_TYPE['agent'],
            ]);
    }
}