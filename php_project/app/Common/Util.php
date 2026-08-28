<?php

namespace App\Common;

use Exception;
use Firebase\JWT\JWT;

/**
 * 工具类
 */
class Util
{
	/**
	 * 生成token
	 * token包含：
	 * 登录用户id
	 * 姓名
	 * 手机
	 * 最后更新时间
	 * sign（随机字符串）
	 * 运营方信息,如果是侠客岛，取委托的运营商列表（数组）
	 */
	public static function tokenEncode($arr)
	{
		$privateKey = file_get_contents(resource_path('crt/rsa_private_key.pem'));

		$time = time();
		$token = [
// 			"iss" => BASE_URL, //签发者 可选
// 			"aud" => BASE_URL, //接收该JWT的一方，可选
// 			"iat" => $time, //签发时间
// 			"nbf" => $time, //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
// 			'exp' => $time + 7200, //过期时间,这里设置2个小时
			'data' => $arr['data'] //自定义信息，不要定义敏感信息
		];
	
		$jwt = JWT::encode($token, $privateKey, 'RS256');
		return $jwt;
	}
	
	/**
	 * 解密token
	 */
	public static function tokenDecode($arr)
	{
		$publicKey = file_get_contents(resource_path('crt/rsa_public_key.pem'));
		
		try {
			$decoded = JWT::decode($arr['data'], $publicKey, array('RS256'));
			$decoded_array = (array) $decoded;
			return $decoded_array;
		} catch (Exception $e) {
			return '';
		}
	}
}