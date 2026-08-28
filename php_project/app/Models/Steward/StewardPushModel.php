<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/4
 * Time: 16:46
 */

namespace App\Models\Steward;


use App\Models\BaseModel;
use App\Models\BelongStaff;
use Illuminate\Database\Eloquent\SoftDeletes;

class StewardPushModel extends BaseModel
{

    use SoftDeletes;
    use BelongStaff;

    // 表名
    protected $table = 'steward_push';

    const TABLE_NAME = 'steward_push';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    public $appends = ['obj_type_name', 'type_name'];

    public function getTypeNameAttribute()
    {
        return !isset($this->attributes['type']) ? '' : array_get(trans('constant.steward_push_type'), $this->attributes['type'], '');

    }

    public function getObjTypeNameAttribute()
    {
        return !isset($this->attributes['obj_type']) ? '' : array_get(trans('constant.steward_push_obj_type'), $this->attributes['obj_type'], '');

    }

}