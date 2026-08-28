<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:05
 */

namespace App\Models;



class EnterpriseBackupModel extends BaseModel
{
    // 软删除
    // 表名
    protected $table = 'enterprise_backup';

    const TABLE_NAME = 'enterprise_backup';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

}