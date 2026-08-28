<?php
namespace App\Repositories\Apply;

use App\Http\Controllers\Service\OcrService;
use App\Repositories\BaseRepository;
use App\Models\ApplyFileModel;
use TencentYoutuyun\Conf;
use TencentYoutuyun\Auth;
use TencentYoutuyun\Http;
use TencentYoutuyun\YouTu;
use Illuminate\Support\Facades\Log;

class YoutuRepository  extends BaseRepository
{
	public function model()
	{
		return ApplyFileModel::class;
	}
	
	/**
	 * 发票识别
	 * type 1 ocr  2 优图
	 */
	public function checkInvoice($url, $type = 1)
	{
		$result = [];
		if ($type == 1) {
//			$result = $this->ocrInvoice($url);
            $result = app(OcrService::class)->vatInvoice($url);
		} else {
			$result = $this->youtuInvoice($url);
		}
		return $result;
	}
	
	/**
	 * 发起HTTP请求，并返回JSON
	 * 参考文档：https://open.youtu.qq.com/#/open/developer/invoice
	 */
	public function youtuInvoice($url)
	{
		$appid = env('YOUTU_APP_ID');
		$secretId = env('YOUTU_SECRET_ID');
		$secretKey = env('YOUTU_SECRET_KEY');
		$userid = env('YOUTU_USER_ID');
		Conf::setAppInfo($appid, $secretId, $secretKey, $userid);

		$expired = time() + YouTu::EXPIRED_SECONDS;
		$postUrl = Conf::$END_POINT . 'youtu/ocrapi/invoiceocr';
		$sign = Auth::appSign($expired, Conf::$USER_ID);
		
		$post_data = array(
			'app_id' =>  Conf::$APPID,
			'url' => $url,
			'session_id' => session()->getId(),
			'ocr_template' => 'VAT' //VAT：增值税发票（常用字段） VAT_ALL：增值税发票（全字段）
		);
		$data = json_encode($post_data);
		
		$req = array(
			'host' => 'api.youtu.qq.com',
			'url' => $postUrl,
			'method' => 'post',
			'timeout' => 10,
			'data' => $data,
			'header' => array(
// 				'Host:api.youtu.qq.com',
				'Authorization:'.$sign,
				'Content-Type:text/json',
				'Content-Length:' . strlen($data),
				'Expect: ',
			),
		);
		
		$rsp  = Http::send($req);
		$ret  = json_decode($rsp, true);
// 		dd($ret);
		if(!$ret){
			Log::error('youtu invoice error ' . $rsp);
			return [];
		}
		
		return $ret;
	}
	
	/**
	 * 发票识别
	 * https://cloud.tencent.com/document/product/866/17606
	 */
	public function ocrInvoice($url)
	{
		$appid = env('OCR_APP_ID');
		$secret_id = env('OCR_SECRET_ID');
		$secret_key = env('OCR_SECRET_KEY');
		$sign = $this->ocrAppSign($appid, $secret_id, $secret_key);
	
		$post_data = array(
			'app_id' =>  $appid,
			'url' => $url
		);
		$data = json_encode($post_data);
	
		$req = array(
			'host' => 'recognition.image.myqcloud.com',
			'url' => 'https://recognition.image.myqcloud.com/ocr/invoice',
			'method' => 'post',
			'timeout' => 10,
			'data' => $data,
			'header' => array(
				'Authorization:'.$sign,
				'Content-Type:text/json',
				'Content-Length:' . strlen($data),
				'Expect: ',
			),
		);
	
		$rsp  = Http::send($req);
		$ret  = json_decode($rsp, true);
		if(!$ret){
			Log::error('youtu invoice error ' . $rsp);
			return [];
		}
	
		return $ret;
	}
	
	/**
	 * 签名
	 * https://cloud.tencent.com/document/product/866/17734#scene
	 */
	public function ocrAppSign($appid, $secret_id, $secret_key)
	{
		$bucket = "tencentyun";
		$expired = time() + 2592000;
		$onceExpired = 0;
		$current = time();
		$rdm = rand();
		$userid = "0";
		$fileid = "tencentyunSignTest";
	
		$srcStr = 'a='.$appid.'&b='.$bucket.'&k='.$secret_id.'&e='.$expired.'&t='.$current.'&r='.$rdm.'&f=';
		$signStr = base64_encode(hash_hmac('SHA1', $srcStr, $secret_key, true).$srcStr);
		return $signStr;
	}
}