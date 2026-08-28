<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/17
 * Time: 19:06
 */

namespace App\Models\Steward;


use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class StewardUserOpinionModel extends BaseModel
{
    use SoftDeletes;



    // 表名
    protected $table = 'steward_user_opinion';

    const TABLE_NAME = 'steward_user_opinion';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    public function file()
    {
        return $this->hasMany(StewardUserOpinionFileModel::class,'steward_user_opinion_id');
    }
}