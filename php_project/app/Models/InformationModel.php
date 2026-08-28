<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:07
 */

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class InformationModel extends BaseModel
{
    // 软删除
    use SoftDeletes;

    // 表名
    protected $table = 'information';

    const TABLE_NAME = 'information';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];
}