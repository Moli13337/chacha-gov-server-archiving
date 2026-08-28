<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/21
 * Time: 15:57
 */

namespace App\Http\Controllers\Service;


use App\Repositories\IndustryRepository;

class IndustryService extends BaseService
{

    protected $industryRepository;

    public $industryItem = [
        'first_industry_id',
        'second_industry_id',
        'third_industry_id',
        'fourth_industry_id',
    ];

    public function __construct(IndustryRepository $industryRepository)
    {
        $this->industryRepository = $industryRepository;
    }

    public function getIndustryName($data)
    {
        $ids = [];

        $item = [
            'first_industry_id' => 'first_industry_name',
            'second_industry_id' => 'second_industry_name',
            'third_industry_id' => 'third_industry_name',
            'fourth_industry_id' => 'fourth_industry_name',
        ];

        foreach (array_keys($item) as $k => $v) {
            $ids[] = array_get($data, $v, 0);
        }

        $ids = array_unique(array_filter($ids));
        $industry = [];
        $name = $this->getIndustry($ids);
        foreach ($item as  $key => $value) {
            if (isset($data[$key])) {
                $industry[$value] = array_get($name, $data[$key], '');
            }
        }

        return $industry;
    }

    public function getIndustry($ids, $column=['*'])
    {
        $industry = [];

        if (empty($ids)) {
            return $industry;
        }
        $name = $this->industryRepository->getByIds($ids, $column);
        if (empty($name)) {
            return $industry;
        }

        $name = array_column($name, 'type_name', 'id');
        return $name;
    }

    public function getIndustryNameList($data)
    {

        $first = array_filter(array_unique(array_column($data, 'first_industry_id')));
        $second = array_filter(array_unique(array_column($data, 'second_industry_id')));
        $third = array_filter(array_unique(array_column($data, 'third_industry_id')));
        $fourth = array_filter(array_unique(array_column($data, 'fourth_industry_id')));
        $ids = array_merge($first, $second, $third, $fourth);
        $name = $this->getIndustry($ids);
        if (empty($name)) {
            $name = [];
        }
        $item = [
            'first_industry_id' => 'first_industry_name',
            'second_industry_id' => 'second_industry_name',
            'third_industry_id' => 'third_industry_name',
            'fourth_industry_id' => 'fourth_industry_name',
        ];

        foreach ($data as $k => $v) {
            foreach ($item as  $key => $value) {
                if (isset($v[$key])) {
                    $data[$k][$value] = array_get($name, $v[$key], '');
                }
            }
        }

        return $data;
    }

    /**
     * FUNCTION_NAME : initIndustry
     * author : jp
     * 初始化行业 默认4级
     * @param $data
     * @return mixed
     */
    public function initIndustry($data)
    {
        foreach ($this->industryItem as $k => $v) {
            if (empty($data[$v])) {
                $data[$v] = 0;
            }
        }
        return $data;
    }

    /**
     * FUNCTION_NAME : v2
     * author : jp
     * 行业数 （管家服务）
     * @return array
     */
    public function v2()
    {
        $where = [
            'is_bank' => 0
        ];
        $data = $this->industryRepository->getTree(['id', 'category', 'b_type', 'm_type', 's_type', 'type_name'], $where);

        return $data;
    }
}