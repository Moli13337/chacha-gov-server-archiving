<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Apply\ApplyCheckRepository;

class CheckApproval extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:checkapproval';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '检查主审部门、协同部门、园区管委会的审批时间';

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
        app(ApplyCheckRepository::class)->checkApproval();
    }
}
