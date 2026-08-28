<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/14
 * Time: 18:08
 */

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class ApplyCorrectModel extends BaseModel
{
// 软删除
    use SoftDeletes;

    use BelongStaff;

    // 表名
    protected $table = 'flo_apply_correct';

    const TABLE_NAME = 'flo_apply_correct';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    protected $appends = ['status_name'];

    public function getStatusNameAttribute()
    {
        return !isset($this->attributes['status']) ? '' : array_get(trans('constant.apply_correct_status'), $this->attributes['status'], '');
    }

    public function getOriginContentAttribute()
    {
        return !isset($this->attributes['origin_content']) ? [] :
            (empty($this->attributes['origin_content']) ? [] : json_decode($this->attributes['origin_content'], true));

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

//    public function operatorStaff()
//    {
//        return $this->belongsToMany(StaffModel::class,
//            StaffBindDepartmentModel::TABLE_NAME,
//            'department_id','staff_id','department_id','id')
//            ->where(StaffBindDepartmentModel::TABLE_NAME.'.opertor_type', STAFF_OPERTOR_TYPE['one'])->limit(1);
//    }

    public function operatorStaff()
    {
        return $this->staff();
    }

    public function approval()
    {
        return $this->belongsTo(ApprovalModel::class, 'approval_id');
    }
    public function correctContent()
    {
        return $this->hasMany(ApplyCorrectContentModel::class, 'correct_id');
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

    public function auditDepartment()
    {
        return $this->belongsTo(StaffDepartmentModel::class, 'audit_department_id');
    }

    public function auditStaff(){
        return $this->belongsTo(StaffModel::class, 'audit_staff_id')
            ->select(['id','name', 'mobile'])
            ->withDefault([
                'id' => 0,
                'name' => '',
                'mobile' => '',
            ]);
    }
}