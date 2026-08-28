<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/11
 * Time: 13:49
 */

namespace App\Models\OriginalPolicy;


use App\Models\BaseModel;

class OriginalPolicyItemModel extends BaseModel
{
    protected $connection = 'mysql_original_policy';

    // 表名
    protected $table = 'policy_item';

    const TABLE_NAME = 'policy_item';

    // 主键
    protected $primaryKey = 'policy_item_id';

    // 黑名单
    protected $guarded = ['policy_item_id'];
}