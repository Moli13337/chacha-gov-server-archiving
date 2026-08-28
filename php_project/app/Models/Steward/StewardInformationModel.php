<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/17
 * Time: 19:06
 */

namespace App\Models\Steward;


use App\Models\BaseModel;
use App\Models\BelongPublishStaff;
use App\Models\Log\StewardInformationLog;
use Illuminate\Database\Eloquent\SoftDeletes;

class StewardInformationModel extends BaseModel
{
    use SoftDeletes;

    use BelongPublishStaff;
    use StewardInformationLog;


    // 表名
    protected $table = 'steward_information';

    const TABLE_NAME = 'steward_information';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    protected $appends = ['type_name', 'publish_status_name'];


    public function getTypeNameAttribute()
    {
        return !isset($this->attributes['type']) ? '' : array_get(trans('constant.steward_information_type'), $this->attributes['type'], '');

    }

    public function getPublishStatusNameAttribute()
    {
        return !isset($this->attributes['publish_status']) ? '' : array_get(trans('constant.publish_status'), $this->attributes['publish_status'], '');

    }


    public function file()
    {
        return $this->hasMany(StewardInformationFileModel::class,'steward_info_id');
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

}