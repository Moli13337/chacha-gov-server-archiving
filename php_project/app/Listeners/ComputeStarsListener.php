<?php

namespace App\Listeners;

use App\Events\ComputeStars;
use App\Events\PolicyRelation;
use App\Repositories\Agent\AgentCommentRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ComputeStarsListener
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
     * @param  PolicyRelation  $event
     * @return void
     */
    public function handle(ComputeStars $event)
    {
        if (empty($event->params)) {
            return;
        }
        //
        app(AgentCommentRepository::class)->computeStarsV2($event->params);
    }
}
