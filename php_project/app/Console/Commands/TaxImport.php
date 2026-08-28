<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\EnterpriseTaxController;
use App\Repositories\Enterprise\EnterpriseTaxImportRepository;
use Illuminate\Console\Command;

class TaxImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tax:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'tax importing';

    protected $enterpriseTaxController;
    protected $enterpriseTaxImportRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(EnterpriseTaxImportRepository $enterpriseTaxImportRepository,
                                EnterpriseTaxController $enterpriseTaxController)
    {
        parent::__construct();
        $this->enterpriseTaxImportRepository = $enterpriseTaxImportRepository;
        $this->enterpriseTaxController = $enterpriseTaxController;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 这里只取一条来运行

        $data = $this->enterpriseTaxImportRepository->getOne();

        if (!empty($data)) {
            echo date('Y-m-d H:i:s').PHP_EOL;
            $arr = explode('/', $data['file_url']);
            $downPath = storage_path('app/public'.DIRECTORY_SEPARATOR.end($arr));
            downFile($data['file_url'],$downPath);
            $this->enterpriseTaxController->importData($downPath, $data['id'], $data['current_row']);
            unlink($downPath);
            echo date('Y-m-d H:i:s').PHP_EOL;
            $this->enterpriseTaxImportRepository->deleteRepository($data['id']);
        }
    }
}
