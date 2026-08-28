<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Time: 16:05
 */

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectIndustryModel extends BaseModel
{
    // 软删除
    use SoftDeletes;

    // 表名
    protected $table = 'project_industry';

    const TABLE_NAME = 'project_industry';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];
}