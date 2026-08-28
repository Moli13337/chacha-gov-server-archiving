<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:05
 */

namespace App\Models;


use App\Models\Log\PolicyLog;
use Illuminate\Database\Eloquent\SoftDeletes;

class PolicyModel extends BaseModel
{
    // 软删除
    use SoftDeletes;

    use BelongStaff;

    // 日志记录
    use PolicyLog;

    // 表名
    protected $table = 'policy';

    const TABLE_NAME = 'policy';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    protected $appends = ['publish_status_desc', 'expired','expired_desc'];

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

    public function getExpiredAttribute()
    {
        if (!isset($this->attributes['validity_edate']) || empty($this->attributes['validity_sdate'])) {
            return 0;
        }
        $tmp = $this->attributes['validity_edate'] < time() ? EXPIRED['yes'] : EXPIRED['no'];
        return $tmp;
    }

    /**
     * FUNCTION_NAME : getExpiredDescAttribute
     * author : jp
     * 过期描述
     * @return mixed
     */
    public function getExpiredDescAttribute()
    {
        if (!isset($this->attributes['validity_edate']) || empty($this->attributes['validity_sdate'])) {
            return array_get(trans('constant.expired'), EXPIRED['no'], '');
        }
        $tmp = $this->attributes['validity_edate'] < time() ? EXPIRED['yes'] : EXPIRED['no'];
        return array_get(trans('constant.expired'), $tmp, '');
    }

    public function govAgen()
    {
        return $this->hasMany(PolicyGovAgenModel::class,'policy_id');
    }

    public function industry()
    {
        return $this->hasMany(PolicyIndustryModel::class,'policy_id');
    }

    public function summarize()
    {
        return $this->hasMany(PolicySummarizeDirectionModel::class,'policy_id');
//        return $this->hasManyThrough(
//            PolicySummarizeModel::class,
//            PolicySummarizeDirectionModel::class,
//            'policy_id',
//            'direction_id'
//        );
    }

    public function file()
    {
        return $this->hasMany(PolicyFileModel::class,'policy_id');
    }

    public function relationPolicy()
    {
        // TODO 过滤删除的政策
        return $this->hasMany(PolicyRelationModel::class,'obj_id');
    }

    public function relationPolicyReverse()
    {
        // TODO 过滤删除的政策
        return $this->hasMany(PolicyRelationModel::class,'obj_type_relation_id');
    }

    public function conclusion()
    {
        return $this->hasOne(PolicyConclusionModel::class,'policy_id');
    }

    public function item()
    {
        return $this->hasMany(PolicyItemModel::class, 'policy_id');
    }

    /**
     * FUNCTION_NAME : mold
     * author : jp
     * 政策类型
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mold()
    {
        return $this->hasOne(PolicyMoldModel::class, 'policy_id');
    }

    /**
     * FUNCTION_NAME : unscramble
     * author : jp
     * 政策解读
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function unscramble()
    {
        return $this->belongsToMany(
            PolicyUnscrambleModel::class,
            PolicyUnscrambleRelationModel::TABLE_NAME,
            'policy_id',
            'unscramble_id')->whereNull(PolicyUnscrambleRelationModel::TABLE_NAME . '.deleted_at');
    }

    public function project()
    {
        return $this->hasMany(ProjectModel::class,'policy_id');
    }

    public function collections()
    {
        return $this->hasMany(UserCollectionModel::class,'obj_id');
    }


}