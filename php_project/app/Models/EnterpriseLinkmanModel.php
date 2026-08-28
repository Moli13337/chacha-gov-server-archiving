<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:05
 */

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class EnterpriseLinkmanModel extends BaseModel
{
    // 软删除
    use SoftDeletes;

    // 表名
    protected $table = 'enterprise_linkman';

    const TABLE_NAME = 'enterprise_linkman';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    protected $appends = ['duty_desc'];

    public function getDutyDescAttribute()
    {
        return !isset($this->attributes['duty']) ? '' : array_get(trans('constant.linkman_duty'),$this->attributes['duty'],'');
    }
}