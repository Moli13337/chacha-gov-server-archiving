<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/11
 * Time: 13:49
 */

namespace App\Models\OriginalPolicy;


use App\Models\BaseModel;

class OriginalPolicyGovModel extends BaseModel
{
    protected $connection = 'mysql_original_policy';

    // 表名
    protected $table = 'gov_agen_policy';

    const TABLE_NAME = 'gov_agen_policy';

    // 主键
    protected $primaryKey = 'gov_agen_policy_id';

    // 黑名单
    protected $guarded = ['gov_agen_policy_id'];
}