<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/4
 * Time: 14:49
 */

namespace App\Http\Controllers\Service;


use App\Repositories\Enterprise\EnterpriseBackupRepository;
use App\Support\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class QiChaChaService
{

    protected $key;
    protected $secretKey;
    protected $baseUrl;
    // 列表接口
    protected $listUrl='/ECIV4/SearchWide';
    // 详情 basic
    protected $detailUrl='/ECIV4/GetBasicDetailsByName';
    // 详情 full
    protected $fullUrl='/ECIV4/GetFullDetailsByName';
    protected $http;
    // 7天
    protected $keepTime = 7*24*60*60;

    public function __construct()
    {
        $this->key = env('QICHACHA_KEY');
        $this->secretKey = env('QICHACHA_SECRET_KEY');
        $this->baseUrl = env('QICHACHA_URL');

        $this->http = new Http();
    }

    public function token()
    {
        $time = time();
        $token = strtoupper(md5($this->key.$time.$this->secretKey));
        $header = [
            'Token' => $token,
            'Timespan' => $time,
        ];

        $this->http->setHeaders($header);
    }

    /**
     * FUNCTION_NAME : getOrgList
     * author : jp
     * 列表
     * @param $name
     * @return array
     * @throws \App\Exceptions\CodeException
     */
    public function getOrgList($name)
    {
        $params = [
            'key' => $this->key,
            'keyword' => $name,
        ];
        $key = REDIS_QCC.$name;
        $data = Cache::get($key, []);
        if (empty($data)) {
            $this->token();
            $data = $this->http->httpRequest($this->baseUrl.$this->listUrl, $params, 'GET');
            if (isset($data['Status']) && $data['Status'] == 200) {
                Cache::put($key, $data, $this->keepTime);
            } else {
                Log::error('qichacha get lit. ', $data);
                return [];
            }
        }

        if (empty($data['Result'])) {
            return [];
        } else {
            // 这里转换 Name->name
            $tmp = $data['Result'];
            foreach ($tmp as $key => $value) {
                $value['name'] = $value['Name'];
                unset($value['Name']);
                $tmp[$key] = $value;
            }

            return $tmp;
        }
    }

    /**
     * FUNCTION_NAME : getOrgDetail
     * author : jp
     * 基本详情
     * @param $name
     * @return array
     * @throws \App\Exceptions\CodeException
     */
    public function getOrgDetail($name)
    {
        $params = [
            'key' => $this->key,
            'keyword' => $name,
        ];
        $key = REDIS_QCC_DETAIL.$name;
        $data = $this->getCacheData($key, $name);

        if (empty($data)) {
            $this->token();
            $data = $this->http->httpRequest($this->baseUrl.$this->detailUrl, $params, 'GET');
            if (isset($data['Status']) && $data['Status'] == 200) {
                $this->backup($data);
                Cache::put($key, $data, $this->keepTime);
            } else {
                Log::error('qichacha get detail. ', $data);
                return [];
            }
        }
        $column = [
            'name' => 'Name',
            'legal_represent' => 'OperName',
            'organization_code' => 'OrgNo',
//            'industry' => 'industry',
            'unified_credit_code' => 'CreditCode',
            'regist_address' => 'Address',
//            'team_num' => 'socialStaffNum',
            'business_scope' => 'Scope',
            'com_type' => 'EntType',
            'regist_capital' => 'RegistCapi',
            // 注册时间
            'regist_time' => 'StartDate',
            'key_no' => 'KeyNo'
        ];


        if (empty($data['Result'])) {
            return [];
        } else {
            $rs = [];
            foreach ($column as $key => $value) {
                if (isset($data['Result'][$value])) {
                    $rs[$key] = $data['Result'][$value];
                } else {
                    $rs[$key] = '';
                }
            }
            // 只取出注册资本的数字
            $tmp = [];
            preg_match_all('/(\d+(\.\d)?)/', $rs['regist_capital'], $tmp);
            $tmp = empty($tmp[1][0]) ? 0 : $tmp[1][0];

            // 组织机构代码, 原字符串中有 -
            $rs['organization_code'] = str_replace('-', '', $rs['organization_code']);
            if (empty($rs['organization_code'])) {
                $rs['organization_code'] = substr($rs['unified_credit_code'], -2, 9);
            }
            $rs['regist_capital'] = (float)$tmp;
            // 转时间
            $rs['regist_time'] = strtotime($rs['regist_time']);

            // 纳税人识别号
            $rs['tax_number'] = $rs['unified_credit_code'];

            // 获取税收
            return $rs;
        }
    }

    /**
     * FUNCTION_NAME : getOrgDetailFull
     * author : jp
     * full 详情
     * @param $name
     * @return array
     * @throws \App\Exceptions\CodeException
     */
    public function getOrgDetailFull($name)
    {
        $params = [
            'key' => $this->key,
            'keyWord' => $name,
        ];
        $key = REDIS_QCC_DETAIL_FULL.$name;

        $data = $this->getData($key, $name);

        if (empty($data)) {
            $this->token();
            $data = $this->http->httpRequest($this->baseUrl.$this->fullUrl, $params, 'GET');
            if (isset($data['Status']) && $data['Status'] == 200) {
                $this->backup($data);
                Cache::put($key, $data, $this->keepTime);
            } else {
                Log::error('qichacha get full detail. ', $data);
                return [];
            }
        }

        $column = [
            'name' => 'Name',
            'legal_represent' => 'OperName',
            'organization_code' => 'OrgNo',
//            'industry' => 'industry',
            'unified_credit_code' => 'CreditCode',
            'regist_address' => 'Address',
            'team_num' => 'PersonScope',
            'business_scope' => 'Scope',
            'com_type' => 'EntType',
            'regist_capital' => 'RegistCapi',
            // 注册时间
            'regist_time' => 'StartDate',
            'key_no' => 'KeyNo'
        ];

        if (empty($data['Result'])) {
            return [];
        } else {
            $rs = [];
            foreach ($column as $key => $value) {
                if (isset($data['Result'][$value])) {
                    $rs[$key] = $data['Result'][$value];
                } else {
                    $rs[$key] = '';
                }
            }
            // 只取出注册资本的数字
            $tmp = [];
            preg_match_all('/(\d+(\.\d)?)/', $rs['regist_capital'], $tmp);
            $tmp = empty($tmp[1][0]) ? 0 : $tmp[1][0];

            // 组织机构代码, 原字符串中有 -
            $rs['organization_code'] = str_replace('-', '', $rs['organization_code']);
            $rs['regist_capital'] = (float)$tmp;
            // 转时间
            $rs['regist_time'] = strtotime($rs['regist_time']);

            // 纳税人识别号
            $rs['tax_number'] = empty($data['Result']['CompanyTaxCreditItems'][0]['No']) ? '' : $data['Result']['CompanyTaxCreditItems'][0]['No'];

            // 获取税收
            return $rs;
        }
    }

    public function getCacheData($key, $name)
    {
        $data = Cache::get($key, []);
        if (!empty($data)) {
            return $data;
        }
        return app(EnterpriseBackupRepository::class)->getByName($name);
    }

    public function backup($data)
    {
        app(EnterpriseBackupRepository::class)->customUpdateOrCreate(
            $data['Result']['KeyNo'],
            $data['Result']['Name'],
            json_encode($data)
        );
    }
}