<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:05
 */

namespace App\Models;


use App\Models\Log\UnscrambleLog;
use Illuminate\Database\Eloquent\SoftDeletes;

class PolicyUnscrambleModel extends BaseModel
{
    // 软删除
    use SoftDeletes;

    use BelongStaff;

    // 表名
    protected $table = 'policy_unscramble';

    const TABLE_NAME = 'policy_unscramble';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    protected $appends = ['publish_status_desc'];

    use UnscrambleLog;

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

    public function policy()
    {
        return $this->belongsToMany(
            PolicyModel::class,
            PolicyUnscrambleRelationModel::TABLE_NAME,
            'unscramble_id',
            'policy_id'
        )->withPivot('id','obj_type')
            ->whereNull(PolicyUnscrambleRelationModel::TABLE_NAME . '.deleted_at');
    }
}