<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/21
 * Time: 17:34
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Events\LogCommon;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tax\DeleteRequest;
use App\Http\Requests\Tax\ImportRequest;
use App\Http\Requests\Tax\ListRequest;
use App\Repositories\Enterprise\EnterpriseRepository;
use App\Repositories\Enterprise\EnterpriseTaxImportRepository;
use App\Repositories\Enterprise\EnterpriseTaxRepository;
use App\Support\ChunkExcelFilter;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EnterpriseTaxController extends Controller
{

    protected $enterpriseTaxRepository;
    protected $enterpriseRepository;
    protected $enterpriseTaxImportRepository;

    public function __construct(EnterpriseTaxRepository $enterpriseTaxRepository,
                                EnterpriseRepository $enterpriseRepository,
                                EnterpriseTaxImportRepository $enterpriseTaxImportRepository)
    {
        $this->enterpriseTaxRepository = $enterpriseTaxRepository;
        $this->enterpriseRepository = $enterpriseRepository;
        $this->enterpriseTaxImportRepository = $enterpriseTaxImportRepository;
    }

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        $params['enterprise_id'] = $request['id'];
        $params['order_by'] = [
                'id' => 'DESC'
        ];

        $data = $this->enterpriseTaxRepository->search($params);

        return codeRender(Code::OK, $data);
    }

    public function deleteBatch(DeleteRequest $request)
    {
        $this->enterpriseTaxRepository->deleteBatch($request->input('ids'));
        return codeRender(Code::OK);
    }

    public function import(ImportRequest $request)
    {
        $data = $this->filter($request);
        $data['created_staff_id'] = (int)getLoginStaff('id');
        $res = $this->enterpriseTaxImportRepository->storeRepository($data);
        event(new LogCommon([
            'type' => ACTIVITY_TYPE['created'],
            'description' => trans('mysqlColumn.enterprise.tax_import'),
            'attribute' => $res,
            'old' => [],
        ], ACTIVITY_SUBJECT_TYPE['enterprise']));
        return codeRender(Code::OK);

    }

    public function importData($file, $id, $row = 0)
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


        // [worksheetName, lastColumnLetter,lastColumnIndex,totalRows,totalColumns]
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
        // 获取第一行
        $info = $sheet->getCellByColumnAndRow(1,$startRow)->getValue();

//        $info = "2018年企业税收分税种（本级）";
        $taxType = trans('constant.tax_type');
        $revers = array_flip($taxType);

        // 匹配 年限
        $yearArr = [];
        preg_match_all('/(\d{4})/', $info, $yearArr);
        if (empty($yearArr[1][0])) {
            return false;
        }
        // 匹配类型
        $tmpPattern = '/(' . implode('|', array_values($taxType)) . ')/u';
        $typeArr = [];
        preg_match_all($tmpPattern, $info, $typeArr);
        if (empty($typeArr[1][0]) || empty($revers[$typeArr[1][0]])) {
            return false;
        }

        $year = $yearArr[1][0];
        $type = $revers[$typeArr[1][0]];

        // 将列映射成key
        $indexToColumn = [
             2 => 'tax_number',
             3 => 'name',
             4 => 'annual_tax',
             5 => 'add_value_tax',
             6 => 'enterprise_income_tax',
             7 => 'business_tax',
             8 => 'individual_income_tax',
             9 => 'consumption_tax',
             10 => 'city_planning_tax',
             11 => 'house_tax',
             12 => 'stamp_tax',
             13 => 'urban_land_use_tax',
             14 => 'land_increment_tax',
             15 => 'vehicle_and_vessel_tax',
             16 => 'vehicle_purchase_tax',
             17 => 'farmland_conversion_tax',
             18 => 'deed_tax',
             19 => 'other_tax',
        ];

        // 设置 按chunk读取 chunk 100

        $startRow = empty($row) ? 5 : $row + 1;
        $chunkSize = 5000;
//        $currentSheet['totalRows'] = 30;
        // 分块读取内容
        for ($i = $startRow;$startRow<=$currentSheet['totalRows'];$startRow+=$chunkSize) {
            $data = [];
            Log::info('import excel', ['start' => $startRow, 'end' => $startRow+$chunkSize]);
            $filter->setRows($startRow, $chunkSize);
            $spreadsheet = $reader->load($file);
            $sheet = $spreadsheet->getActiveSheet();

            $endRow = $startRow + $chunkSize;
            for ($i;$i<$endRow;$i++) {
//                echo $i.PHP_EOL;
                $item = [];
                foreach ($indexToColumn as $key => $value) {
                    // 进一步处理格式
                    // 税号 转成string  防止 科学计数
                    $tmpValue = $sheet->getCellByColumnAndRow($key, $i)->getValue();
                    if ($key == 2) {
//                        $item[$value] = empty($tmpValue) ? '' : $this->sctonum($tmpValue);
                        $item[$value] = empty($tmpValue) ? '' : $tmpValue;
                    } elseif ($key == 3) {
                        $item[$value] = empty($tmpValue) ? '' : $tmpValue;
                    } else {
                        $item[$value] = empty($tmpValue) ? 0.00 : (float)$tmpValue;
                    }
                }

                $item['year'] = $year;
                $item['type'] = $type;
                $data[] = $item;
            }
            $this->dealData($data);
            try {
                $update = [
                    'id' => $id,
                    'current_row' => $i
                ];
                $this->enterpriseTaxImportRepository->updateRepository($update);
            } catch (\Exception $e) {

            }
        }

        return true;

    }

    /**
     * FUNCTION_NAME : dealData
     * author : jp
     * 处理数据写入sql
     * @param $data
     * @return bool
     * @throws \App\Exceptions\QueryException
     */
    public function dealData($data)
    {

        // 这里避免不了循环读表
        // 1 检验企业可以读一次表
        // 2 每个企业 每年 口径 只能是循环读表
        // 校验字符串

        $taxs = array_filter(array_unique(array_column($data, 'tax_number')));

        $taxArr = $this->enterpriseRepository->getByTaxNum($taxs, ['id','tax_number']);

        if (empty($taxs)) {
            return true;
        }

        $taxArr = array_column($taxArr, 'id', 'tax_number');

        $insert_data = [];
        foreach ($data as $key => $value) {
            if (empty($taxArr[$value['tax_number']])) {
                continue;
            }

            $where = [
                'enterprise_id' => $taxArr[$value['tax_number']],
                'year' => $value['year'],
                'type' => $value['type']
            ];
            $detail = $this->enterpriseTaxRepository->getByEYT($where);
            if ($detail) {
                continue;
            }

            $value['enterprise_id'] = $taxArr[$value['tax_number']];
            $tmp = array_except($value, ['tax_number','name']);
            $tmp = array_merge($tmp, returnCreatedUpdatedAt());
            $insert_data[] = $tmp;
        }

        if (!empty($insert_data)) {
            $this->enterpriseTaxRepository->storeBatch($insert_data);
        }
        return true;
    }


    /**
     * FUNCTION_NAME : sctonum
     * author : jp
     * 将 科学计数转成 string  数字规律法
     * @param $num
     * @return string
     */
    public function sctonum($num){
        if(false !== stripos($num, "e+")){
            $a = explode("e",strtolower($num));
            return bcmul($a[0], bcpow(10, $a[1]));
        }

        return $num;
    }

    /**
     * FUNCTION_NAME : NumToStr
     * author : jp
     * 将 科学计数转成 string  取尾数法 这里有个bug 可能转成 ***000-**
     * @param $num
     * @return float|string
     */
    public function NumToStr($num) {
        if (stripos($num, 'e') === false)
            return $num;
        $num = trim(preg_replace('/[=\'"]/', '', $num, 1), '"'); //出现科学计数法，还原成字符串
        $result = "";
        while ($num > 0) {
            $v = $num - floor($num / 10) * 10;
            $num = floor($num / 10);
            $result = $v . $result;
        }
        return $result;
    }

}