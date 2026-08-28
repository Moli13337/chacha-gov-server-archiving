<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Common\Code;
use Xkd\Location\Location;

class ConfigController extends Controller
{
	/**
	 * 数据字典配置
	 */
	public function config(Request $request)
	{
		$constant = trans('constant');
		// 返回到前端：需要哪些自行添加
		$result = [
			'publicity_set' => $constant['publicity_set'], //  公示集合
			'announce_status' => $constant['announce_status'], //  申报状态
            'user_message_source' => $constant['user_message_source_home'], // 消息来源类型
            'user_message_read' => $constant['user_message_read'], // 消息状态
            'apply_correct_status' => $constant['apply_correct_status_client'], // 订正状态筛选

			'others' => []
		];
		
		// 转换数据
		foreach ($result as $key => $value) {
			$tmpList = [];
			if (is_array($value)) {
				foreach ($value as $key2 => $value2) {
					$tmpList[] = [
						'id' => $key2,
						'name' => $value2
					];
				}
			}
			$result[$key] = $tmpList;
		}

		return codeRender(Code::OK, $result);
	}

    /**
     * FUNCTION_NAME : getDistricts
     * author : jp
     * 获取行政区划
     * @param Request $request
     * @return mixed
     * @throws \Xkd\Location\Exceptions\ClientException
     */
	public function getDistricts(Request $request)
    {
        return Location::getInfo('district')->getDistricts($request->all());
    }

}
