<?php

namespace App\Console\Commands;

use App\Http\Controllers\Service\RecommendedService;
use Illuminate\Console\Command;

class CronRecommend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:recommend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        app(RecommendedService::class)->recommend();

    }
}
