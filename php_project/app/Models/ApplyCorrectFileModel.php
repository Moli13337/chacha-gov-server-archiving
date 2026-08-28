<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/14
 * Time: 18:08
 */

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class ApplyCorrectFileModel extends BaseModel
{
// 软删除
    use SoftDeletes;

    use BelongStaff;

    // 表名
    protected $table = 'flo_apply_correct_file';

    const TABLE_NAME = 'flo_apply_correct_file';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    protected $appends = ['content', 'correct_type_name'];

    public function getContentAttribute()
    {
        return !isset($this->attributes['content']) ?[] :
            (empty($this->attributes['content']) ? [] : json_decode($this->attributes['content'], true));
    }

    public function getCorrectTypeNameAttribute()
    {
        return !isset($this->attributes['correct_type']) ? '' : array_get(trans('constant.apply_correct_file_type'), $this->attributes['correct_type'], '');
    }
    

}