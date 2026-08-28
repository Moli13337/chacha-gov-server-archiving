<?php

namespace App\Listeners;

use App\Events\ApplyFormPdfCreate;
use App\Events\ApplyPdfCreate;
use App\Events\PolicyRelation;
use App\Events\ZipCreate;
use App\Repositories\ActivityLogRepository;
use App\Repositories\PdfRepository;
use App\Repositories\Policy\PolicyRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class ApplyFormPdfCreateListener
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
     * @param  ApplyFormPdfCreate  $event
     * @return void
     */
    public function handle(ApplyFormPdfCreate $event)
    {
        //
        try {
            app(PdfRepository::class)->approvalCreate($event->params);
        } catch (\Exception $e) {
            Log::error('ApplyFormPdfCreateListener error: '.$e->getMessage());
        }
    }
}
