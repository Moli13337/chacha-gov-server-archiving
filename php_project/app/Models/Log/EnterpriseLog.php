<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/24
 * Time: 15:53
 */

namespace App\Models\Log;


use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

trait EnterpriseLog
{
    use LogsActivity;


    // 变更的字段
    protected static $logAttributes = [
        'name',
        'unified_credit_code',
        'organization_code',
        'tax_number',
        'legal_represent',
        'business_license_url',
        'regist_capital',
        'regist_address',
        'regist_time',
        'business_term',
        'business_address',
        'business_area',
    ];
    // 忽略的字段
    protected static $logAttributesToIgnore = ['created_at','updated_at'];

//     只记录变更的值
    protected static $logOnlyDirty = true;

    // 禁止空提交
    protected static $submitEmptyLogs = false;

    public function getDescriptionForEvent(string $eventName): string
    {
        $change = $this->attributeValuesToBeLogged($eventName);
        $type = array_get(ACTIVITY_TYPE, $eventName, 0);
        $content = '';
        $base = array_get($change['attributes']??[], 'name', '');
        if ($type == ACTIVITY_TYPE['created']) {
            $content = $base;
        } elseif ($type == ACTIVITY_TYPE['deleted']) {
            $content = $base;
        } elseif ($type == ACTIVITY_TYPE['updated']) {
            $columnName = trans('mysqlColumn.enterprise');
            $temp_content = [];
            if (!empty($change['attributes'])) {
                foreach ($change['attributes'] as $key => $value) {
                    $temp_content[] = array_get($columnName, $key, '');
                }
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
        $activity->subject_type_id = ACTIVITY_SUBJECT_TYPE['enterprise'];
        $activity->title = array_get(trans('constant.activity_type'), $activity->type, $eventName);
        $activity->ip = ip(1,true);
    }
}