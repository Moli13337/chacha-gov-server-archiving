<?php

namespace App\Listeners;

use App\Events\BatchPublish;
use App\Repositories\ActivityLogRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\Activitylog\Contracts\Activity;

class BatchPublishListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(BatchPublish $event)
    {

        $subject_type_id = array_get($event->params, 'subject_type_id',0);
        if (empty($subject_type_id)) {
            return;
        }

        switch ($subject_type_id) {
            case ACTIVITY_SUBJECT_TYPE['agent']:
                $this->saveByIds($event->params['ids']??[], $subject_type_id, $event->params['publish_status']??0);
                break;
        }

    }

    public function saveByIds($ids, $subject_type_id, $status)
    {
        if (empty($ids)) {
            return;
        }

        $data = [];
        $time = date('Y-m-d', time());
        $ip = ip(1,true);
        foreach ($ids as $k => $v) {
            $tmp = [];
            $tmp['type'] = ACTIVITY_TYPE['updated'];
            $tmp['subject_type_id'] = $subject_type_id;
            $tmp['subject_id'] = $v;
            $tmp['properties'] = json_encode(['attribute' => [ 'publish_status'=>$status], 'old' => []]);
            $tmp['title'] = array_get(trans('constant.activity_type'), ACTIVITY_TYPE['updated'], '');
            $tmp['description'] = trans('mysqlColumn.agent.publish_status');
            $tmp['causer_id'] = (int)getLoginStaff('id');
            $tmp['causer_name'] = empty(getLoginStaff('name')) ? '' : getLoginStaff('name');
            $tmp['created_at'] = $time;
            $tmp['updated_at'] = $time;
            $tmp['ip'] = $ip;
            $data[] = $tmp;
        }

        app(ActivityLogRepository::class)->storeBatchRepository($data);

    }
}
