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

class TimeCriteria extends BaseCriteria
{

    public function apply($model, RepositoryInterface $repository)
    {
        $start = trim(array_get($this->params, $this->key[0]));
        $end = trim(array_get($this->params, $this->key[1]));

        if (!blank($start) && !blank($end)) {
            $model = $model->where('created_at', '>=', $start)->where('created_at', '<', $end);
        }
        return $model;
    }
}