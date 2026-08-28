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

trait StewardOpinionLog
{
    use LogsActivity;

    // 变更的字段
    protected static $logAttributes = [
        'title',
        'content',
        'source_name',
        'link',
        'type',
        'publish_status',
        'publish_time',
        PUBLISH_STAFF_ID,
    ];
    // 忽略的字段
    protected static $logAttributesToIgnore = [
        'created_at','updated_at',
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
        $base = array_get($this->attributes, 'title', '');

        if ($type == ACTIVITY_TYPE['created']) {
            $content = $base;
        } elseif ($type == ACTIVITY_TYPE['deleted']) {
            $content = $base;
        } elseif ($type == ACTIVITY_TYPE['updated']) {
            $columnName = trans('mysqlColumn.stewardOpinion');
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
        $activity->subject_type_id = ACTIVITY_SUBJECT_TYPE['steward_opinion'];
        $activity->title = array_get(trans('constant.activity_type'), $activity->type, $eventName);
        $activity->ip = ip(1,true);
    }
}