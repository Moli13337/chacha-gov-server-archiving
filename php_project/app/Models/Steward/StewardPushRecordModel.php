<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/4
 * Time: 16:46
 */

namespace App\Models\Steward;


use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class StewardPushRecordModel extends BaseModel
{

    use SoftDeletes;
    
    // 表名
    protected $table = 'steward_push_record';

    const TABLE_NAME = 'steward_push_record';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    protected $appends = ['is_new'];

    public function getIsNewAttribute()
    {
        return ($this->attributes['created_at'] < (time() -  7*24*60*60)) ? 0 : 1;
    }

    public function sourcePush()
    {
        return $this->belongsTo(StewardPushModel::class, 'steward_push_id');
    }


}