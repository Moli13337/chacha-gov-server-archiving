<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/14
 * Time: 18:08
 */

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class ApplyReplenishModel extends BaseModel
{
// 软删除
    use SoftDeletes;

    use BelongStaff;

    // 表名
    protected $table = 'flo_apply_replenish';

    const TABLE_NAME = 'flo_apply_replenish';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    protected $appends = ['status_name'];

    public function getStatusNameAttribute()
    {
        return !isset($this->attributes['status']) ? '' : array_get(trans('constant.apply_replenish_status'), $this->attributes['status'], '');

    }

    public function apply()
    {
        return $this->belongsTo(ApplyModel::class, 'apply_id');
    }

    public function department()
    {
        return $this->belongsTo(StaffDepartmentModel::class, 'department_id');
    }
}