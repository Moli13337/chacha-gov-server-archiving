<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/11
 * Time: 13:49
 */

namespace App\Models\OriginalPolicy;


use App\Models\BaseModel;

class OriginalBigDataModel extends BaseModel
{

    protected $connection = 'mysql_original_policy';

    // 这张表是被分表的
    // 表名
    protected $table = 'big_data_origin_1';

    const TABLE_NAME = 'big_data_origin_1';

    // 主键
    protected $primaryKey = 'big_data_id';

    // 黑名单
    protected $guarded = ['big_data_id'];

    public $incrementing = false;
}