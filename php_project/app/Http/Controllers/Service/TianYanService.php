<?php

namespace App\Http\Controllers\Service;


use App\Http\Controllers\Controller;
use App\Support\Http;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class TianYanService extends Controller
{

    protected $token;

    // 天眼基础链接
    protected $baseUrl;
    // 列表链接
    protected $listUrl = '/services/v3/newopen/searchV2.json';
    // 详情链接
    protected $detailUrl = '/services/v3/newopen/baseinfo.json';
    // 经营
    protected $abnormalUrl = '/services/v3/newopen/abnormal.json';
    // business
    protected $businessUrl = '/services/v3/newopen/baseinfo.json';

    protected $headers = [];

    protected $client = '';

    // 7天
    protected $keepTime = 7*24*60*60;

    protected $http = [];

    public function __construct()
    {
        $this->token = getenv('TIANYAN_AUTH_TOKEN');
        $this->baseUrl = getenv('TIANYAN_URL');
        $this->headers = [
            'Authorization' => $this->token,
        ];

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => $this->headers,
        ]);

        $this->http = new Http();

        $this->http->setHeaders($this->headers);
    }

    public function getOrgList($input_data)
    {
        $params = [
            'word' => $input_data['org_name']
        ];

        $key = REDIS_TY_LIST.$input_data['org_name'];
        $data = Cache::get($key, []);

        if (empty($data)) {
            $data = $this->http->httpRequest($this->baseUrl.$this->listUrl, $params, 'GET');
            Cache::put($key, $data, $this->keepTime);
        }

        if (empty($data['data'])) {
            return [];
        } else {

            foreach ($data['data'] as &$value) {
                $value['name'] = preg_replace('/<[\/em]{2,3}>/', '', $value['name']);
            }
            return $data['data'];
        }
    }

    /**
     * FUNCTION_NAME : getOrgDetail
     * author : jp
     * 详情
     * @param $input_data
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getOrgDetail($input_data)
    {
        $params = [
            'name' => $input_data['org_name']
        ];

        $key = REDIS_TY_DETAIL.$params['name'];
        $data = Cache::get($key, []);
        if (empty($data)) {
            $data = $this->http->httpRequest($this->baseUrl.$this->detailUrl, $params, 'GET');
            if (isset($data['error_code']) && $data['error_code'] == 0) {
                Cache::put($key, $data, $this->keepTime);
            } else {
                return [];
            }
        }

        $column = [
            'name' => 'name',
            'legal_represent' => 'legalPersonName',
            'organization_code' => 'orgNumber',
            'industry' => 'industry',
            'unified_credit_code' => 'creditCode',
            'regist_address' => 'regLocation',
            'team_num' => 'socialStaffNum',
            'business_scope' => 'businessScope',
            'com_type' => 'companyOrgType',
            'regist_capital' => 'regCapital',
            // 注册时间
            'regist_time' => 'estiblishTime',
            'ty_id' => 'id'
        ];

        if (empty($data['result'])) {
            return [];
        } else {

            $rs = [];
            foreach ($column as $key => $value) {
                if (isset($data['result'][$value])) {
                    $rs[$key] = $data['result'][$value];
                }
            }

            // 只取出注册资本的数字
            $tmp = [];
            preg_match_all('/(\d+(\.\d)?)/', $rs['regist_capital'], $tmp);
            $tmp = empty($tmp[1][0]) ? 0 : $tmp[1][0];

            $rs['regist_capital'] = (float)$tmp;
            // 转时间
            $rs['regist_time'] = floor($rs['regist_time']/1000);
            return $rs;
        }
    }
}
