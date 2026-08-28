<?php

namespace App\Listeners;

use App\Events\IndustryChange;
use App\Repositories\ActivityLogRepository;
use App\Repositories\Policy\PolicyRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class IndustryChangeListener
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
     * @param  IndustryChange  $event
     * @return void
     */
    public function handle(IndustryChange $event)
    {
        //
        $data = array_only($event->params, ['type', 'subject_id', 'subject_type_id','properties']);
        // 这里是政策的行业
        $data['title'] = array_get(trans('constant.activity_type'), $data['type'], '');
        $data['description'] = trans('validation.attributes.industry');
        $data['causer_id'] = (int)getLoginStaff('id');
        $data['causer_name'] = empty(getLoginStaff('name')) ? '' : getLoginStaff('name');
        $data['ip'] = ip(1,true);
        app(ActivityLogRepository::class)->storeRepository($data);
    }
}
