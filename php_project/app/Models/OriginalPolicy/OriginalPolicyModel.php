<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/11
 * Time: 13:48
 */

namespace App\Models\OriginalPolicy;


use App\Models\BaseModel;

class OriginalPolicyModel extends BaseModel
{
    protected $connection = 'mysql_original_policy';

    // 表名
    protected $table = 'policy';

    const TABLE_NAME = 'policy';

    // 主键
    protected $primaryKey = 'policy_id';

    // 黑名单
    protected $guarded = ['policy_id'];

    public function govAgen()
    {
        return $this->hasMany(OriginalPolicyGovModel::class, 'obj_id');
    }

    public function item()
    {
        return $this->hasMany(OriginalPolicyItemModel::class, 'policy_id');
    }

    public function conclusion()
    {
        return $this->hasOne(OriginalPolicyConclusionModel::class, 'policy_id');
    }

    public function detail()
    {
        return $this->hasOne(OriginalPolicyDetailModel::class, 'policy_id');
    }
}