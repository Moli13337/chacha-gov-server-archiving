<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Apply\ApplyCheckRepository;

class CheckMaterial extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:checkmaterial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '补充资料24小时提醒';

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
        app(ApplyCheckRepository::class)->checkMaterial();
    }
}
