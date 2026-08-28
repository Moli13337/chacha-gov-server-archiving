<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/29
 * Time: 15:49
 */

namespace App\Models\Log;

use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

trait PolicyLog
{

    use LogsActivity;

    // 变更的字段
    protected static $logAttributes = [
        'name',
        'doc_num',
        'pub_time',
        'province_code',
        'city_code',
        'district_code',
        'validity_sdate',
        'validity_edate',
        'publish_status',
        'source_web',
        'source_url',
        'deleted'
        ];
    // 忽略的字段
    protected static $logAttributesToIgnore = ['end_id', 'code','content', 'created_at','updated_at'];

//     只记录变更的值
    protected static $logOnlyDirty = true;

    // 禁止空提交
    protected static $submitEmptyLogs = false;

    public function getDescriptionForEvent(string $eventName): string
    {
        $change = $this->attributeValuesToBeLogged($eventName);

        $type = array_get(ACTIVITY_TYPE, $eventName, 0);
        $content = '';
        if ($type == ACTIVITY_TYPE['created']) {
            $content = $this->attributes['name'];
        } elseif ($type == ACTIVITY_TYPE['deleted']) {
            $content = $this->attributes['name'];
        } elseif ($type == ACTIVITY_TYPE['updated']) {
            $columnName =  trans('mysqlColumn.policy');
            if (!empty($this->attributes['obj_type']) && $this->attributes['obj_type'] == OBJ_TYPE['announce']) {
                $columnName =  trans('mysqlColumn.announce');
            } elseif (!empty($this->attributes['obj_type']) && $this->attributes['obj_type'] == OBJ_TYPE['publicity']) {
                $columnName =  trans('mysqlColumn.publicity');
            }
            $temp_content = [];
            if (!empty($change['attributes'])) {
                foreach ($change['attributes'] as $key => $value) {
                    $temp_content[] = array_get($columnName, $key, '');
                }
            }
            // 特殊处理正文
            if (isset($this->attributes['content']) && $this->attributes['content'] != $this->getOriginal('content')) {
                $temp_content[] = array_get($columnName, 'content', '');
            }

            $temp_content = array_filter($temp_content);
            $content = implode('，', $temp_content);
        }
        return empty($content) ? ''  : $content;
    }

    public function tapActivity(Activity $activity, $eventName)
    {
        $activity->type = array_get(ACTIVITY_TYPE, $eventName, 0);
        $activity->causer_id = (int)getLoginStaff('id');
        $activity->causer_name = empty(getLoginStaff('name')) ? '' : getLoginStaff('name');
        $activity->subject_type_id = $this->getAttribute('obj_type');
        $activity->title = array_get(trans('constant.activity_type'), $activity->type, $eventName);
        $activity->ip = ip(1,true);
    }

}