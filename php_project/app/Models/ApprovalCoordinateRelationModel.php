<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/22
 * Time: 11:04
 */

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class ApprovalCoordinateRelationModel extends BaseModel
{

    use SoftDeletes;

    // 表名
    protected $table = 'flo_approval_coordinate_relation';

    const TABLE_NAME = 'flo_approval_coordinate_relation';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

}