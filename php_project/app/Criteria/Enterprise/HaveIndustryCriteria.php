<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/12/10
 * Time: 10:16
 */

namespace App\Criteria\Enterprise;


use App\Criteria\BaseCriteria;
use App\Models\EnterpriseIndustryModel;
use Prettus\Repository\Contracts\RepositoryInterface;

class HaveIndustryCriteria extends BaseCriteria
{

    public function apply($model, RepositoryInterface $repository)
    {
        $columns = [
            "first_industry_id",
            "second_industry_id",
            "third_industry_id",
            "fourth_industry_id",
        ];
        $data = [];
        foreach ($columns as $k => $v) {
            $tmp = trim(array_get($this->params, $v, ''));
            if (!blank($tmp)) {
                $data[] = [$v,'=',$tmp];
            }
        }

        if (!empty($data)) {
            $en = EnterpriseIndustryModel::select(['enterprise_id'])->where($data)->get()->toArray();
            $en =array_column($en, 'enterprise_id');
            if (empty($en)) {
                $en[] = [0];
            }
            $model = $model->whereIn('id', $en);
//            $model = $model->whereHas('industry', function ($query) use ($data) {
//                $query->where($data);
//            });
        }

        return $model;
    }
}