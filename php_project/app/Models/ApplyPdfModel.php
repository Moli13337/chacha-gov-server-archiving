<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/23
 * Time: 17:09
 */

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class ApplyPdfModel extends BaseModel
{
    use SoftDeletes;

    use BelongStaff;

    // 表名
    protected $table = 'flo_apply_pdf';

    const TABLE_NAME = 'flo_apply_pdf';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

}