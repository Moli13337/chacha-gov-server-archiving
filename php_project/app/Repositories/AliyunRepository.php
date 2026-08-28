<?php
namespace App\Repositories\Apply;

use App\Repositories\BaseRepository;
use App\Models\ApplyFileModel;
use TencentYoutuyun\Http;
use Illuminate\Support\Facades\Log;

class AliyunRepository  extends BaseRepository
{
	public function model()
	{
		return ApplyFileModel::class;
	}
	
	/**
	 * 发票识别
	 * https://market.aliyun.com/products/57000002/cmapi028399.html?spm=5176.730005.productlist.d_cmapi028399.27e23524vOobJO&innerSource=search_%E5%8F%91%E7%A5%A8%E7%9C%9F%E4%BC%AA#sku=yuncode2239900000
	 * 返回成功为真   不成功为假发票
	 */
	public function checkInvoice($arr)
	{
		if (empty($arr['invoice_code']) || empty($arr['invoice_number'] 
			|| empty($arr['invoice_billing_date'])) ) {
			return false;
		}
		
		$checkcode = '';
		if (!empty($arr['invoice_checkcode'])) {
			$checkcode = substr($arr['invoice_checkcode'], -6);
		}
		$totalAmount = 0;
		if (!empty($arr['invoice_money'])) {
			$totalAmount = $arr['invoice_money'];
		}
		
		$bodys = '';
		$bodys .= 'billingDate=' . $arr['invoice_billing_date'];
		$bodys .= '&checkCode=' . $checkcode;
		$bodys .= '&invoiceCode=' . $arr['invoice_code'];
		$bodys .= '&invoiceNumber=' . $arr['invoice_number'];
		$bodys .= '&totalAmount=' . $totalAmount;

		$host = "http://verinvoice.sinosecu.com.cn";
		$path = "/verapi/verInvoice.do";
		$method = "POST";
		$appcode = env('ALIYUN_APP_CODE');
		$headers = array();
		array_push($headers, "Authorization:APPCODE " . $appcode);
		//根据API的要求，定义相对应的Content-Type
		array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded; charset=UTF-8");
		$querys = "";
		$url = $host . $path;
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($curl, CURLOPT_HEADER, true); // 不要头部信息
		if (1 == strpos("$".$host, "https://"))
		{
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		}
		curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
		
		$rsp = curl_exec($curl);
		curl_close($curl);
// 		var_dump(curl_exec($curl));

		$ret  = json_decode($rsp, true);
		
		if (!empty($ret['message']['status']) && $ret['message']['status'] == 2) {
			// 识别成功
			return true;
		}
		
		Log::error('aliyun invoice error ' . $rsp);
		return false;
	}
	
}