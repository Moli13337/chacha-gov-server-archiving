<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/1
 * Time: 19:35
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ActivityLogModel extends Model
{
    // 表名
    protected $table = 'activity_log';

    const TABLE_NAME = 'activity_log';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $appends = ['terminal_desc'];

    public function getIpAttribute()
    {
        return !isset($this->attributes['ip']) ? '' : long2ip($this->attributes['ip']);
    }

    public function getTerminalDescAttribute()
    {
        return !isset($this->attributes['terminal']) ? '' : array_get(trans('constant.terminal'),$this->attributes['terminal'], '');
    }



}