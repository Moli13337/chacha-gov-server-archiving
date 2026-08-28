<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/24
 * Time: 17:14
 */

namespace App\Models\Log;


use App\Models\EnterpriseModel;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

trait AgentLog
{
    use LogsActivity;

    // 变更的字段
    protected static $logAttributes = [
        'service_item',
        'file_name',
        'file_url',
        'province_code',
        'city_code',
        'district_code',
        'address',
        'contact_name',
        'contact_phone',
        'publish_status',
        'remark',
        'submit_time',
        'submit_material',
    ];
    // 忽略的字段
    protected static $logAttributesToIgnore = [
        'created_at','updated_at', 'composite_stars', 'department_stars','credit_type',
        ];

//     只记录变更的值
    protected static $logOnlyDirty = true;

    // 禁止空提交
    protected static $submitEmptyLogs = false;

    public function getDescriptionForEvent(string $eventName): string
    {
        $change = $this->attributeValuesToBeLogged($eventName);
        $type = array_get(ACTIVITY_TYPE, $eventName, 0);
        $content = '';
//        $base = array_get($change['attributes']??[], 'name', '');
        $enterprise_id = $this->attributes['enterprise_id'];
        $base = EnterpriseModel::select(['name'])->withTrashed()->where('id', $enterprise_id)->first();
        $base = $base['name'];

        if ($type == ACTIVITY_TYPE['created']) {
            $content = $base;
        } elseif ($type == ACTIVITY_TYPE['deleted']) {
            $content = $base;
        } elseif ($type == ACTIVITY_TYPE['updated']) {
            $columnName = trans('mysqlColumn.agent');
            $temp_content = [];
            if (!empty($change['attributes'])) {
                foreach ($change['attributes'] as $key => $value) {
                    $temp_content[] = array_get($columnName, $key, '');
                }
            }
            // 特殊处理服务详情
            if (isset($this->attributes['service_detail']) && $this->attributes['service_detail'] != $this->getOriginal('service_detail')) {
                $temp_content[] = array_get($columnName, 'service_detail', '');
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
        $activity->subject_type_id = ACTIVITY_SUBJECT_TYPE['agent'];
        $activity->title = array_get(trans('constant.activity_type'), $activity->type, $eventName);
        $activity->ip = ip(1,true);
    }
}