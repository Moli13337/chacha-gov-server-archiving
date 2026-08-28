<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/5
 * Time: 1:05
 */

namespace App\Criteria\Policy;


use App\Criteria\BaseCriteria;
use Prettus\Repository\Contracts\RepositoryInterface;

class DistrictAllCriteria extends BaseCriteria
{
    public function apply($model, RepositoryInterface $repository)
    {
        $value = trim(array_get($this->params, $this->key));
        if (!blank($value)) {
            $model = $model->where(function ($query) {
                $query->orWhere(function ($query) {
                    $query->where('province_code', 0);
                    $query->where('city_code', 0);
                    $query->where('district_code', 0);
                });
                $query->orWhere(function ($query) {
                    $query->where('province_code', env('DEFAULT_PROVINCE_CODE'));
                    $query->where('city_code', 0);
                    $query->where('district_code', 0);
                });
                $query->orWhere(function ($query) {
                    $query->where('province_code', env('DEFAULT_PROVINCE_CODE'));
                    $query->where('city_code', env('DEFAULT_CITY_CODE'));
                    $query->where('district_code', 0);
                });
                $query->orWhere(function ($query) {
                    $query->where('province_code', env('DEFAULT_PROVINCE_CODE'));
                    $query->where('city_code', env('DEFAULT_CITY_CODE'));
                    $query->where('district_code', env('DEFAULT_DISTRICT_CODE'));
                });
            });
        }
        return $model;
    }
}