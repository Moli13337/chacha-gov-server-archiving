<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Apply\ApplyCheckRepository;

class CheckApply extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:checkapply';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check apply enterprise credit and tax and invoice';

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
        app(ApplyCheckRepository::class)->checkApply();
    }
}
