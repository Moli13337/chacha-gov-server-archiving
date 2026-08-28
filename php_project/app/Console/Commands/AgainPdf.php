<?php

namespace App\Console\Commands;

use App\Models\ApplyModel;
use App\Repositories\Apply\ApplyRepository;
use App\Repositories\PdfRepository;
use Illuminate\Console\Command;

class AgainPdf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'again:pdf {--id=*}';

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
        $this->again();
    }

    public function again()
    {
        $idArr = $this->option('id');
        $model  = ApplyModel::select(['id'])
            ->where('business_id', '!=', '');
        if (!empty($idArr)) {
            $model = $model->whereIn('id', $idArr);
        }
        $data = $model->get()->toArray();

        foreach ($data as $k => $v) {
            $tmp = [
                'id' => $v['id'],
                'business_id' => businessId(),
                'pdf_url' => ''
            ];
            $apply = app(ApplyRepository::class)->detail($v);
            echo $v['id'].PHP_EOL;
            app(ApplyRepository::class)->updateRepository($tmp);
            $apply['business_id'] = $tmp['business_id'];
            app(PdfRepository::class)->createApprovalPdf($apply);
        }
    }

    public function pdf($data)
    {

    }
}
