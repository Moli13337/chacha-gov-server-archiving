<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\PenaltyController;
use Illuminate\Console\Command;

class MigrateEnterprise extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:enterprise';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '迁移企业信息';

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
        app(PenaltyController::class)->migrate();
    }
}
