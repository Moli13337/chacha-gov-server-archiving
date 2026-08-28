<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/6
 * Time: 14:09
 */

namespace App\Models\Steward;


use App\Models\BaseModel;

class StewardUserModel extends BaseModel
{
    // 表名
    protected $table = 'steward_user';

    const TABLE_NAME = 'steward_user';

    // 主键
    protected $primaryKey = 'user_id';

}