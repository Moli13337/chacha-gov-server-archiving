<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/19
 * Time: 18:12
 */

namespace App\Http\Controllers\Service;


use App\Support\Collection;
use Xkd\Location\Location;

class DistrictService extends BaseService
{

    /**
     * FUNCTION_NAME : getDistrictCode
     * author : jp
     *
     * @param $data 一个二维数组列表
     * @return array
     * @throws \Xkd\Location\Exceptions\ClientException
     */
    public function getDistrictCode($data)
    {
        $province_code = array_filter(array_unique(array_column($data, 'province_code')));
        $city_code = array_filter(array_unique(array_column($data, 'city_code')));
        $district_code = array_filter(array_unique(array_column($data, 'district_code')));

        $code = array_merge($province_code, $city_code, $district_code);

        return $this->getCode($code);

    }

    public function getCode($code)
    {

        if (empty($code)) {
            return [];
        }
        $code_where = [
            'codes' => $code,
            // 每页数量 大一点满足所有code数据出现
            'per_page' => 1000,
        ];
        $code_arr = Location::getInfo('district')->getDistricts($code_where);

        if (empty($code_arr['data']['data'])) {
            $code_arr = [];
        } else {
            $code_arr = array_column($code_arr['data']['data'], 'district_name', 'district_code');
        }
        return $code_arr;
    }


    /**
     * FUNCTION_NAME : getDistrictName
     * author : jp
     * 地区名
     * @param $detail
     * @return array
     */
    public function getDistrictName($detail)
    {
        $code_arr_key = ['province_code', 'city_code', 'district_code'];
        $district = [];

        $code_arr = array_unique(array_filter(Collection::filter($code_arr_key, $detail)));
        $code_arr = $this->getCode($code_arr);
        $district['province_name'] = array_get($code_arr, $detail['province_code'], '');
        $district['city_name'] = array_get($code_arr, $detail['city_code'],'');
        $district['district_name'] = array_get($code_arr, $detail['district_code'],'');
        return $district;
    }


    /**
     * FUNCTION_NAME : clientDistrictFilter
     * author : jp
     * 用户端使用地区筛选
     * @param $params
     * @return mixed
     */
    public function clientDistrictFilter($params){

        $province = array_get($params, 'province_code', '');
        $city = array_get($params, 'city_code', '');
        $district = array_get($params, 'district_code', '');
        if (blank($province) && blank($city) && blank($district)) {
            $params['district_all'] = true;
            return $params;
        }
        if (!isset($params['district_code']) || blank($params['district_code'])) {
            $params['district_code'] = 0;
        }

        if (!isset($params['city_code']) || blank($params['city_code'])) {
            $params['city_code'] = 0;
        }

        if (!isset($params['province_code']) || blank($params['province_code'])) {
            $params['province_code'] = 0;
        }
        return $params;
    }

    /**
     * FUNCTION_NAME : formatDistrict
     * author : jp
     * 格式化地址
     * @param $data
     * @return mixed
     */
    public function formatDistrict($data)
    {
        $column = [
            'province_code',
            'city_code',
            'district_code',
        ];

        foreach ($column as $key => $value) {
            if (empty($data[$value])) {
                $data[$value] = 0;
            }
        }
        return $data;
    }


    /**
     * FUNCTION_NAME : defaultDistrictFilter
     * author : jp
     * 默认地区
     * @param $params
     * @return mixed
     */
    public function defaultDistrictFilter($params)
    {
        $params['province_code'] = env('DEFAULT_PROVINCE_CODE');
        $params['city_code'] = env('DEFAULT_CITY_CODE');
        $params['district_code'] = env('DEFAULT_DISTRICT_CODE');
        return $params;
    }

    /**
     * FUNCTION_NAME : clientDistrictFilterV2
     * 客户端地去筛选 （包含下一级区划的情况）
     *
     * @param $params
     * @return mixed
     */
    public function clientDistrictFilterV2($params){
        if (isset($params['district_code']) && empty($params['district_code'])) {
            unset($params['district_code']);
        }
        if (isset($params['city_code']) && empty($params['city_code'])) {
            unset($params['city_code']);
        }
        if (isset($params['province_code']) && empty($params['province_code'])) {
            unset($params['province_code']);
        }
        return $params;
    }

    public function getDistrictNameList($data)
    {
        $province_code = array_filter(array_unique(array_column($data, 'province_code')));
        $city_code = array_filter(array_unique(array_column($data, 'city_code')));
        $district_code = array_filter(array_unique(array_column($data, 'district_code')));

        $code = array_merge($province_code, $city_code, $district_code);

        $codeArr = $this->getCode($code);

        if (empty($codeArr)) {
            return $data;
        }
        $item = [
            'province_code' => 'province_code_name',
            'city_code' => 'city_code_name',
            'district_code' => 'district_code_name',
        ];

        foreach ($data as $k => $v) {
            foreach ($item as  $key => $value) {
                if (isset($v[$key])) {
                    $data[$k][$value] = array_get($codeArr, $v[$key], '');
                }
            }
        }

        return $data;

    }
}