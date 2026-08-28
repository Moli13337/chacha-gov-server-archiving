<?php

namespace App\Listeners;

use App\Events\PolicyBatchDelete;
use App\Models\ActivityLogModel;
use App\Repositories\ActivityLogRepository;
use App\Repositories\Policy\PolicyRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PolicyBatchDeleteListener
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
     * @param  PolicyBatchDelete  $event
     * @return void
     */
    public function handle(PolicyBatchDelete $event)
    {
        $exist = app(PolicyRepository::class)->detailById($event->params[0]??0, 'obj_type');
        if (empty($exist)) {
            return;
        }
        $data['type'] = ACTIVITY_TYPE['deleted'];
        $data['subject_type_id'] = $exist['obj_type'];
        $data['properties'] = json_encode(['attribute' => $event->params, 'old' => []]);
        $data['title'] = array_get(trans('constant.activity_type'), ACTIVITY_TYPE['deleted'], '');
        $data['description'] = array_get(trans('constant.activity_subject_type'), $exist['obj_type'], '');
        $data['causer_id'] = (int)getLoginStaff('id');
        $data['causer_name'] = empty(getLoginStaff('name')) ? '' : getLoginStaff('name');
        app(ActivityLogRepository::class)->store($data);
    }
}
