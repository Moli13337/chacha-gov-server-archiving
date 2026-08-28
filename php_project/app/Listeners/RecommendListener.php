<?php

namespace App\Listeners;

use App\Common\RedisConstant;
use App\Events\Recommend;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class RecommendListener
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
    public function handle(Recommend $event)
    {
        //
        $data = $event->params;
        if (empty($data)) {
            return;
        }
        $data = implode(',', $data);
        try {
            Redis::rpush(RedisConstant::RECOMMEND, $data);
        } catch (\Exception $e) {
            Log::error('recommend event error :'. $e->getMessage());
        }
    }
}
