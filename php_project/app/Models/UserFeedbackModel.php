<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:08
 */

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;

class UserFeedbackModel extends BaseModel
{
    // 软删除
    use SoftDeletes;

    // 表名
    protected $table = 'user_feedback';

    const TABLE_NAME = 'user_feedback';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    protected $appends = ['status_desc', 'type_name'];

    /**
     * FUNCTION_NAME : user
     * author : jp
     * 关联 用户
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(UserModel::class)
            ->select(['id','name', 'mobile'])
            ->withDefault();
    }

    /**
     * FUNCTION_NAME : reply
     * author : jp
     * 关联 回复
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function reply()
    {
        return $this->hasOne(UserFeedbackModel::class, 'source_id')
            ->where(['is_reply' => FEEDBACK_REPLY['staff']])
            ->withDefault();
    }


    public function getStatusDescAttribute()
    {
//        return array_get(trans('constant.feedback_status'), $this->attributes['status'], '');
        return !isset($this->attributes['status']) ? '' : array_get(trans('constant.feedback_status'), $this->attributes['status'], '');
    }

    public function getTypeNameAttribute()
    {
        return !isset($this->attributes['type']) ? '' : array_get(trans('constant.feedback_type'), $this->attributes['type'], '');
    }
}