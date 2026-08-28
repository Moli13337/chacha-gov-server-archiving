<?php

namespace App\Listeners;

use App\Events\PolicyRelation;
use App\Events\ZipCreate;
use App\Http\Controllers\Service\ZipService;
use App\Repositories\ActivityLogRepository;
use App\Repositories\Policy\PolicyRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Xkd\Upload\Upload;

class ZipCreateListener
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
     * @param  ZipCreate  $event
     * @return void
     */
    public function handle(ZipCreate $event)
    {
        //
        app(ZipService::class)->createApplyZip($event->params);
    }
}
