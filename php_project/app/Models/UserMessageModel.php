<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:15
 */

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class UserMessageModel extends BaseModel
{
    // 软删除
    use SoftDeletes;

    // 表名
    protected $table = 'user_message';

    const TABLE_NAME = 'user_message';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];
}