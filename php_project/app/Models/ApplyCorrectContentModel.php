<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/14
 * Time: 18:08
 */

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class ApplyCorrectContentModel extends BaseModel
{
// 软删除
    use SoftDeletes;

    use BelongStaff;

    // 表名
    protected $table = 'flo_apply_correct_content';

    const TABLE_NAME = 'flo_apply_correct_content';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    protected $appends = ['content'];

    public function getContentAttribute()
    {
        return !isset($this->attributes['content']) ?[] :
            (empty($this->attributes['content']) ? [] : json_decode($this->attributes['content'], true));
    }


}