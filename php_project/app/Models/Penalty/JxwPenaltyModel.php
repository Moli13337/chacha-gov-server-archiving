<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/8
 * Time: 15:53
 */

namespace App\Models\Penalty;


use Illuminate\Database\Eloquent\Model;

class JxwPenaltyModel extends Model
{
    protected $connection = 'mysql_enterprise';

    // 表名
    protected $table = 's_ys_jxw_administrative_penalty';

    const TABLE_NAME = 's_ys_jxw_administrative_penalty';

    // 主键
    protected $primaryKey = 'REGISTER_NO';

    // 黑名单
    protected $guarded = ['REGISTER_NO'];
    // 非递增或者非数字的主键
    public $incrementing = false;
    // 非整数主键，需要加上
    protected $keyType = 'string';
    use MigrateRelation;

}