<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/12/12
 * Time: 9:49
 */

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class UserPushModel extends BaseModel
{

    // 软删除
    use SoftDeletes;


    // 表名
    protected $table = 'user_push';

    const TABLE_NAME = 'user_push';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];
}