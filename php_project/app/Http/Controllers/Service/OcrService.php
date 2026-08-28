<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/2
 * Time: 10:37
 */

namespace App\Http\Controllers\Service;


use App\Common\Code;
use App\Exceptions\CodeException;
use Illuminate\Support\Facades\Log;
use TencentCloud\Common\Credential;
use TencentCloud\Ocr\V20181119\Models\BizLicenseOCRRequest;
use TencentCloud\Ocr\V20181119\Models\VatInvoiceOCRRequest;
use TencentCloud\Ocr\V20181119\OcrClient;

class OcrService extends BaseService
{

    protected $secret;
    protected $secretKey;
    protected $region;

    public function __construct()
    {
        $this->secret = getenv('TENCENT_SECRET_ID');
        $this->secretKey = getenv('TENCENT_SECRET_KEY');
        $this->region = 'ap-shanghai';
    }

    public function vatInvoice($image)
    {
        $cred = new Credential($this->secret, $this->secretKey);
        $client = new OcrClient($cred, $this->region);
        $req = new VatInvoiceOCRRequest();

        if (!isBase64($image)) {
            $req->ImageUrl =  $image;
        } else {
            $req->ImageBase64 =  $image;
        }

        try {
            $resp = $client->VatInvoiceOCR($req);
        } catch (\Exception $e) {
            Log::error('tencent VatInvoice error.' . $e->getMessage(). '. img:'. $image);
//            dd($e->getMessage());
            throw new CodeException(Code::OCR_VAT_INVOICE_ERROR, $e->getMessage());
        }

        $rule = [
            '开票日期' => 'invoice_billing_date',
            '发票代码' => 'invoice_code',
            '发票号码' => 'invoice_number',
            '校验码' => 'invoice_checkcode',
            '合计金额' => 'invoice_money',
        ];
        $result = [];

        foreach ($resp->VatInvoiceInfos as $key => $value) {
            $temp = array_get($rule, $value->Name, '');
            if (empty($temp)) {
                continue;
            }
            $result[$temp] = $value->Value;
        }

        $result = $this->handleInvoice($result);

        return $result;
    }

    public function handleInvoice($params)
    {
        foreach ($params as $key => $value) {
            if ($key == 'invoice_number') {
                // 处理格式，提取数字
                preg_match('/\d+/', $value, $numArr);
                $params['invoice_number'] = empty($numArr[0]) ? '' : $numArr[0];
            } elseif ($key == 'invoice_money') {
                $params['invoice_money'] = preg_replace("/[¥]/",'',$value);
            } elseif ($key == 'invoice_billing_date') {
                $dateArr = date_parse_from_format('Y年m月d日', $value);
                $dateTime = mktime(0,0,0,$dateArr['month'],$dateArr['day'],$dateArr['year']);
                $params['invoice_billing_date'] = date('Y-m-d', $dateTime);
            }
        }

        return $params;
    }
}