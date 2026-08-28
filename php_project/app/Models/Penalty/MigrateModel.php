<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/8
 * Time: 19:02
 */

namespace App\Models\Penalty;


use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class MigrateModel extends BaseModel
{
// 软删除
    use SoftDeletes;
    protected $connection = 'mysql_enterprise';

    // 表名
    protected $table = 'migrate';

    const TABLE_NAME = 'migrate';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];
}