<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/5
 * Time: 1:05
 */

namespace App\Criteria\Credit;


use App\Criteria\BaseCriteria;
use Prettus\Repository\Contracts\RepositoryInterface;

class WhereEndCriteria extends BaseCriteria
{
    public function apply($model, RepositoryInterface $repository)
    {
        $value = trim(array_get($this->params, $this->key));
        if (!blank($value)) {
            $model = $model->where('decision_date', '<', $value);
        }
        return $model;
    }
}