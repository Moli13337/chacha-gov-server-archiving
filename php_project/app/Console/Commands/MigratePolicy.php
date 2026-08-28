<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\MigratePolicyController;
use Illuminate\Console\Command;

class MigratePolicy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:policy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '迁移政策';

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

        app(MigratePolicyController::class)->migrate();
    }
}
