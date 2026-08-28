<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:05
 */

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class PolicyUnscrambleRelationModel extends BaseModel
{
    // 软删除
    use SoftDeletes;

    // 表名
    protected $table = 'policy_unscramble_relation';

    const TABLE_NAME = 'policy_unscramble_relation';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];


    public function unscramble()
    {
        return $this->belongsTo(PolicyUnscrambleModel::class,'unscramble_id');
    }

    public function policy()
    {
        return $this->belongsTo(PolicyModel::class,'policy_id');
    }
}