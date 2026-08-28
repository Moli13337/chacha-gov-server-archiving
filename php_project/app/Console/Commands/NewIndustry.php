<?php

namespace App\Console\Commands;

use App\Support\ChunkExcelFilter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class NewIndustry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'new:industry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '指定行业';

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
        $file = 'D:\work_space\温江\温江管家服务的行业.xlsx';
        $this->importData($file);
    }

    public function importData($file, $row = 0)
    {
        // 自动识别文件类型
        $fileType = IOFactory::identify($file);

        // 创建一个reader
        $reader = IOFactory::createReader($fileType);

        // 创建一个分块的过滤器
        $filter = new ChunkExcelFilter();

        $reader->setReadDataOnly(true);

        $reader->setReadFilter($filter);

        // 获取 sheetinfo
        $sheetArr = $reader->listWorksheetInfo($file);
        $currentSheet = $sheetArr[0];

        $startRow = 1;
        $chunkSize = 3;
        // 设置 按chunk读取 读取 第一行  包含的信息 年份 类型
        $filter->setRows($startRow,$chunkSize);
        // 加载文件
        $spreadsheet = $reader->load($file);
        // 获取活动sheet
        $sheet = $spreadsheet->getActiveSheet();
        $cells = $sheet->toArray();

        $info = $sheet->getCellByColumnAndRow(1,2)->getValue();


        $startRow = empty($row) ? 2 : $row + 1;
        $chunkSize = 5;
//        $currentSheet['totalRows'] = 30;
        $filename = "E:\work\saas_server\backend\app\Console\Commands\ss.sql";
        $sqlRe = fopen($filename, 'w');
        $str = "UPDATE `industry` SET `is_bank` = 1 WHERE `id` = %s;";
        // 分块读取内容
        for ($i = $startRow;$startRow<=$currentSheet['totalRows'];$startRow+=$chunkSize) {
            $data = [];
//            Log::info('import excel', ['start' => $startRow, 'end' => $startRow+$chunkSize]);
            $filter->setRows($startRow, $chunkSize);
            $spreadsheet = $reader->load($file);
            $sheet = $spreadsheet->getActiveSheet();

            $endRow = $startRow + $chunkSize;

            // 将列映射成key
            $indexToColumn = [
                1 => 'id',
                9 => 'deleted_at',
            ];

            $all = [];
            for ($i;$i<$endRow;$i++) {
//                echo $i.PHP_EOL;
                $item = [];
                foreach ($indexToColumn as $key => $value) {
                    $tmpValue = $sheet->getCellByColumnAndRow($key, $i)->getValue();
                    $item[$value] = intval($tmpValue);
                }

                if (!empty($item['deleted_at'])) {
                    $sql =  sprintf($str, $item['id']) . PHP_EOL;
                    echo $sql;
                    fwrite($sqlRe, $sql);
                }
            }

        }

        fclose($sqlRe);


    }
}
