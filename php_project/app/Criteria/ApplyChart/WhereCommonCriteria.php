<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/8/22
 * Time: 16:14
 */

namespace App\Criteria\ApplyChart;


use App\Criteria\BaseCriteria;
use Prettus\Repository\Contracts\RepositoryInterface;

class WhereCommonCriteria extends BaseCriteria
{
    public function apply($model, RepositoryInterface $repository)
    {
        if (!empty($this->params)) {
            $model = $model->where($this->params);
        }
        return $model;
    }
}