<?php

namespace App\Listeners;

use App\Events\ApplyPdfCreate;
use App\Events\PolicyRelation;
use App\Events\ZipCreate;
use App\Repositories\ActivityLogRepository;
use App\Repositories\PdfRepository;
use App\Repositories\Policy\PolicyRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Xkd\Upload\Upload;

class ApplyPdfCreateListener
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
    public function handle(ApplyPdfCreate $event)
    {
        //
        try {
            app(PdfRepository::class)->applyCreate($event->params);
        } catch (\Exception $e) {
            Log::error($e);
        }
    }
}
