<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/22
 * Time: 11:04
 */

namespace App\Models;


use App\Repositories\Apply\ApprovalCoordinateRelationRepository;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApprovalCoordinateLogModel extends BaseModel
{

    use SoftDeletes;
    use BelongStaff;

    // 表名
    protected $table = 'flo_approval_coordinate_log';

    const TABLE_NAME = 'flo_approval_coordinate_log';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    public function approval()
    {
        return $this->hasManyThrough(ApprovalModel::class,
            ApprovalCoordinateRelationModel::class,
            'log_id',
            'id', 'id', 'approval_id');
    }

}