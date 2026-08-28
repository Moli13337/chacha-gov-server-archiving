<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/14
 * Time: 19:37
 */

namespace App\Criteria\ApplyReplenish;


use App\Criteria\BaseCriteria;
use Prettus\Repository\Contracts\RepositoryInterface;

class ApplyCriteria extends BaseCriteria
{

    public function apply($model, RepositoryInterface $repository)
    {
        $column = 'keyword';
        $value = trim(array_get($this->params, $column));
        if (!blank($value)) {
            $value = "%$value%";

            $model = $model->where(function ($query) use ($value) {
                $query = $query->whereHas('apply', function ($q) use ($value) {
                    $q->where('enterprise_name', 'like', $value);
                    $q->orWhere('project_name', 'like', $value);
                });

                $query->orWhere(function ($q) use ($value) {
                    $q->whereHas('department', function ($qq) use ($value) {
                        $qq->where('name', 'like', $value);
                    });
                });
            });


        }
//        $model = $model->whereHas('apply', function ($query) use ($value) {
//            if (!empty($value)) {
//                $value = "%$value%";
//
//                $query->where('enterprise_name', 'like', $value);
//                $query->orWhere('project_name', 'like', $value);
//            }
//        });
        return $model;
    }
}