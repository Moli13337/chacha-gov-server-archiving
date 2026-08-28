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
use App\Models\Log\StewardOpinionLog;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShareActivityApplyModel extends BaseModel
{
    use SoftDeletes;

    use BelongPublishStaff;
    use StewardOpinionLog;


    // 表名
    protected $table = 'share_activity_apply';

    const TABLE_NAME = 'share_activity_apply';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

}