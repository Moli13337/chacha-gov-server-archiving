<?php
namespace App\Repositories;

use Illuminate\Support\Facades\Log;
use App\Models\StaffCodeModel;
use App\Common\Code;
use function GuzzleHttp\json_encode;
use Exception;

/**
 * 短信
 * @author ASUS
 *
 */
class SmsRepository  extends BaseRepository
{
	public function model()
	{
		return StaffCodeModel::class;
	}
	
	/**
	 * 发送短信
	 * telephone : 电话号码 字符串
	 * template : 模板 字符串
	 * param ： 参数 数组
	 */
	public function send($arr)
	{
		if (empty($arr['telephone']) || empty($arr['template'])) {
			return false;
		}

		$smsArr = [
			'channel' => 1, // 阿里云
			'telephone' => $arr['telephone'],
			'business_id' => businessId(),
			'template_code' => $arr['template'],
			'template_param' => $arr['param'] ?? []
		];
		
		try {
			
			$result = app('sms')->send($smsArr);
			if ($result['code'] != Code::OK) {
				Log::error('sms send prams: ' . json_encode($smsArr));
				Log::error('sms send result: ', $result);
				return false;
			}
			
		}catch (Exception $e){
			Log::error('sms send prams: ' . json_encode($smsArr));
			Log::error('sms send result: ' . $e->getMessage());
			return false;
		}

		return true;
	}
	
}