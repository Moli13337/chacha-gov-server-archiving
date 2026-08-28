<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/5
 * Time: 13:55
 */

namespace App\Models\Share;


use App\Models\BaseModel;
use App\Models\BelongPublishStaff;
use App\Models\Log\ShareActivityLog;
use App\Models\Traits\IsNewPublishTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShareActivityModel extends BaseModel
{
    use SoftDeletes;

    use BelongPublishStaff;
    use ShareActivityLog;


    // 表名
    protected $table = 'share_activity';

    const TABLE_NAME = 'share_activity';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    protected $appends = ['status','type_name', 'publish_status_name', 'status_name', 'is_new'];

    use IsNewPublishTrait;


    public function getStatusAttribute()
    {
        $start = !isset($this->attributes['validity_sdate']) ? '' : $this->attributes['validity_sdate'];
        $end = !isset($this->attributes['validity_edate']) ? '' : $this->attributes['validity_edate'];
        if (!blank($end) && $end < time() ) {
            return SHARE_ACTIVITY_STATUS['over'];
        } elseif (!blank($start) && $start < time()) {
            return SHARE_ACTIVITY_STATUS['off'];
        } elseif (!blank($start) && $start > time()) {
            return SHARE_ACTIVITY_STATUS['on'];
        } else {
            return 0;
        }
    }

    public function getTypeNameAttribute()
    {
        return !isset($this->attributes['type']) ? '' : array_get(trans('constant.share_activity_type'), $this->attributes['type'], '');

    }

    public function getPublishStatusNameAttribute()
    {
        return !isset($this->attributes['publish_status']) ? '' : array_get(trans('constant.publish_status'), $this->attributes['publish_status'], '');
    }

    /**
     * FUNCTION_NAME : getStatusNameAttribute
     * author : jp
     *
     * @return mixed
     */
    public function getStatusNameAttribute()
    {
        $status = $this->getStatusAttribute();
        return empty($status) ? '' : array_get(trans('constant.share_activity_status'), $status, '');
    }

    /**
     * FUNCTION_NAME : apply
     * author : jp
     * 报名
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function apply()
    {
        return $this->hasMany(ShareActivityApplyModel::class,'activity_id');
    }

}