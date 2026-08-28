<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * 主审部门和协同部门补充资料24小时定时消息通知
 * @author lxh
 *
 */
class ApprovalMaterialModel extends BaseModel
{
    use BelongStaff;

	public $timestamps = FALSE;
	
	// 表名
	protected $table = 'flo_approval_material';
	
	const TABLE_NAME = 'flo_approval_material';

	// 主键
	protected $primaryKey = 'id';
	
	// 黑名单
	protected $guarded = ['id'];

	protected $appends = ['status_name'];

	public function getStatusNameAttribute()
    {
        if (!isset($this->attributes['status'])) {
            return '';
        }

        $status = $this->attributes['status'];
        return array_get(trans('constant.material_send_status'), $status, '');

    }

	public function getMaterialAttribute()
    {
        return !isset($this->attributes['material']) ? [] :
            ( empty($this->attributes['material'])?[]:json_decode($this->attributes['material'], true));
    }

    public function apply()
    {
        return $this->belongsTo(ApplyModel::class, 'apply_id');
    }

    public function department()
    {
        return $this->belongsToMany(
            StaffDepartmentModel::class,
            ApprovalModel::TABLE_NAME,
            'id',
            'department_id',
                    'approval_id','id'
            );
    }

    public function approval()
    {
        return $this->belongsTo(ApprovalModel::class, 'approval_id');
    }


    public function user()
    {
        return $this->belongsToMany(
            UserModel::class,
            ApplyModel::TABLE_NAME,
            'id',
            'user_id',
            'apply_id','id'
        );
    }
}
