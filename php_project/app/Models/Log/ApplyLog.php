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

trait ApplyLog
{
    use LogsActivity;

    // 变更的字段
    protected static $logAttributes = [
        'enterprise_id',
        'project_id',
        'apply_money',
        'support_content',
        'allocation_time',
        'submit_time',
    ];
    // 忽略的字段
    protected static $logAttributesToIgnore = [
        'created_at','updated_at',
        ];

//     只记录变更的值
    protected static $logOnlyDirty = true;

    // 禁止空提交
    protected static $submitEmptyLogs = false;

    protected $subject_type_id;

    public function getDescriptionForEvent(string $eventName): string
    {
        $change = $this->attributeValuesToBeLogged($eventName);
        $type = array_get(ACTIVITY_TYPE, $eventName, 0);
        $content = '';
//        $base = array_get($change['attributes']??[], 'name', '');
        $baseSupplement = array_get(trans('constant.activity_subject_type'), ACTIVITY_SUBJECT_TYPE['apply_supplement'], '');
        $base = array_get(trans('constant.activity_subject_type'), ACTIVITY_SUBJECT_TYPE['apply'], '');

        $this->subject_type_id = ACTIVITY_SUBJECT_TYPE['apply'];
        if ($type == ACTIVITY_TYPE['created']) {
            if (isset($this->attributes['is_supplement']) && $this->attributes['is_supplement'] == APPLY_SUPPLEMENT['yes']) {
                $content = $baseSupplement;
                $this->subject_type_id = ACTIVITY_SUBJECT_TYPE['apply_supplement'];
            } else {
                $content = '';
            }
        } elseif ($type == ACTIVITY_TYPE['deleted']) {
            if (isset($this->attributes['is_supplement']) && $this->attributes['is_supplement'] == APPLY_SUPPLEMENT['yes']) {
                $this->subject_type_id = ACTIVITY_SUBJECT_TYPE['apply_supplement'];
                $content = $baseSupplement;
            } else {
                $content = '';
            }
        } elseif ($type == ACTIVITY_TYPE['updated']) {
            $columnName = trans('mysqlColumn.apply');
            $temp_content = [];
            if (!empty($change['attributes'])) {
                foreach ($change['attributes'] as $key => $value) {
                    $temp_content[] = array_get($columnName, $key, '');
                }
            }
            $temp_content = array_filter($temp_content);
            $content = implode('，', $temp_content);

            if ($this->getOriginal('is_supplement') == APPLY_SUPPLEMENT['yes']) {
                $this->subject_type_id = ACTIVITY_SUBJECT_TYPE['apply_supplement'];
            } else {
                $content = '';
            }
        }

        return empty($content) ? ''  : $content;
    }

    public function tapActivity(Activity $activity, $eventName)
    {
        $activity->type = array_get(ACTIVITY_TYPE, $eventName, 0);
        $activity->causer_id = (int)getLoginStaff('id');
        $activity->causer_name = empty(getLoginStaff('name')) ? '' : getLoginStaff('name');
//        $activity->subject_type_id = ACTIVITY_SUBJECT_TYPE['agent'];
        $activity->subject_type_id = $this->subject_type_id;
        $activity->title = array_get(trans('constant.activity_type'), $activity->type, $eventName);
        $activity->ip = ip(1,true);
    }
}