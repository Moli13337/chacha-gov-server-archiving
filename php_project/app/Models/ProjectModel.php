<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:05
 */

namespace App\Models;


use App\Models\Log\ProjectLog;
use App\Models\Steward\StewardPushModel;
use App\Models\Steward\StewardPushRecordModel;
use App\Models\Traits\IsNewTrait;
use http\Client\Curl\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectModel extends BaseModel
{
    // 软删除
    use SoftDeletes;

    use BelongStaff;

    // 表名
    protected $table = 'project';

    const TABLE_NAME = 'project';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    protected $appends = ['publish_status_desc', 'expired_desc', 'announce_status', 'announce_status_desc', 'is_new'];

    use IsNewTrait;
    use ProjectLog;

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

    /**
     * FUNCTION_NAME : getExpiredDescAttribute
     * author : jp
     * 过期描述
     * @return mixed
     */
    public function getExpiredDescAttribute()
    {
        if (!isset($this->attributes['validity_edate']) || !isset($this->attributes['validity_sdate']) ) {
            return '';
        }
        $tmp = $this->attributes['validity_edate'] < time() ? EXPIRED['yes'] : EXPIRED['no'];
        $tmp = $this->attributes['validity_sdate'] > time() ? EXPIRED['init'] : $tmp;
        return array_get(trans('constant.expired'), $tmp, '');
    }

    public function getAnnounceStatusAttribute()
    {
        if (!isset($this->attributes['validity_edate']) || !isset($this->attributes['validity_sdate']) ) {
            return ANNOUNCE_STATUS['wait'];
        }
        $tmp = $this->attributes['validity_edate'] < time() ? ANNOUNCE_STATUS['over'] : ANNOUNCE_STATUS['enter'];
        $tmp = $this->attributes['validity_sdate'] > time() ? ANNOUNCE_STATUS['wait'] : $tmp;
        return $tmp;
    }

    public function getAnnounceStatusDescAttribute()
    {
        return array_get(trans('constant.announce_status'), $this->getAnnounceStatusAttribute(), '');
    }

    /**
     * FUNCTION_NAME : mold
     * author : jp
     * 政策类型
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mold()
    {
        return $this->belongsTo(MoldModel::class, 'mold_id');
    }

    /**
     * FUNCTION_NAME : plate
     * author : jp
     * 内容板块
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function plate()
    {
        return $this->hasMany(ProjectPlateModel::class, 'project_id');
    }

    public function materials()
    {
        return $this->hasMany(ProjectMaterialsModel::class, 'project_id');
    }

    public function materialsOther()
    {
        return $this->hasOne(ProjectMaterialsOtherModel::class, 'project_id');
    }

    public function file()
    {
        return $this->hasMany(ProjectFileModel::class, 'project_id');
    }

    public function policy()
    {
        return $this->belongsTo(PolicyModel::class, 'policy_id');
    }

    public function stewardPush()
    {
        return $this->hasManyThrough(
            StewardPushRecordModel::class,
            StewardPushModel::class,
            'obj_id',
                        'steward_push_id'

        );
    }

    public function userPush()
    {
        return $this->hasMany(UserPushModel::class, 'obj_id')
            ->where(UserPushModel::TABLE_NAME.'.obj_type', OBJ_TYPE['project']);
    }

    public function collections()
    {
        return $this->hasMany(UserCollectionModel::class,'obj_id');
    }


    public function industry()
    {
        return $this->hasMany(ProjectIndustryModel::class,'project_id');
    }

}