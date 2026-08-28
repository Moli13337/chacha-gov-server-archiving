<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/27
 * Time: 15:59
 */

namespace App\Http\Controllers\Service;


use App\Support\Http;
use Illuminate\Support\Facades\Log;

class TextCheckService
{

    protected $secret_id;
    protected $secret_key;
    protected $business_id;
    protected $baseUrl;
    protected $http;
    protected $textUrl = '/v3/text/check';
    protected $version = 'v3.1';

    public function __construct()
    {
        $this->secret_id = env('DUN_SECRET_ID');
        $this->secret_key = env('DUN_SECRET_KEY');
        $this->business_id = env('DUN_TEXT_BUSINESS_ID');
        $this->baseUrl = env('DUN_URL');
        $this->http = new Http();
    }

    public function check($content)
    {
        $params = [
            'secretId' => $this->secret_id,
            'businessId' => $this->business_id,
            'timestamp' => time()*1000,
            'nonce' => randomInteger(11),
            'dataId' => str_random(11),
            'content' => $content,
            'version' => $this->version
        ];

        $params['signature'] = $this->genSignature($this->secret_key, $params);
        $data = $this->http->httpRequest($this->baseUrl.$this->textUrl, $params, 'POST');

        // TODO 需要监控服务
        if (empty($data['code'])  || empty($data['result'])
            || $data['code'] != 200 || $data['result']['action'] != 0) {
            Log::error('text check content' . $content);
            Log::error('text check result', $data);
            return false;
        }
        return true;

    }

    public function genSignature($secretKey, $params)
    {
        ksort($params);
        $buff="";
        foreach($params as $key=>$value){
            $buff .=$key;
            $buff .=$value;
        }
        $buff .= $secretKey;
        return md5(mb_convert_encoding($buff, "utf8", "auto"));
    }

    public function toUtf8($params){
        $utf8s = array();
        foreach ($params as $key => $value) {
            $utf8s[$key] = is_string($value) ? mb_convert_encoding($value, "utf8", 'auto') : $value;
        }
        return $utf8s;
    }
}