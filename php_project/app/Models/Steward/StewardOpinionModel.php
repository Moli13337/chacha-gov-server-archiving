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
use App\Models\Log\StewardOpinionLog;
use App\Models\Traits\IsNewPublishTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class StewardOpinionModel extends BaseModel
{
    use SoftDeletes;

    use BelongPublishStaff;
    use StewardOpinionLog;


    // 表名
    protected $table = 'steward_opinion';

    const TABLE_NAME = 'steward_opinion';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    protected $appends = ['type_name', 'publish_status_name', 'is_new'];

    use IsNewPublishTrait;


    public function getTypeNameAttribute()
    {
        return !isset($this->attributes['type']) ? '' : array_get(trans('constant.steward_opinion_type'), $this->attributes['type'], '');

    }

    public function getPublishStatusNameAttribute()
    {
        return !isset($this->attributes['publish_status']) ? '' : array_get(trans('constant.publish_status'), $this->attributes['publish_status'], '');

    }


    public function file()
    {
        return $this->hasMany(StewardOpinionFileModel::class,'steward_opinion_id');
    }

    public function userOpinion()
    {
        return $this->hasMany(StewardUserOpinionModel::class,'steward_opinion_id');
    }

}