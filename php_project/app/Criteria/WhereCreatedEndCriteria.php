<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/5
 * Time: 1:05
 */

namespace App\Criteria;


use Prettus\Repository\Contracts\RepositoryInterface;

class WhereCreatedEndCriteria extends BaseCriteria
{
    public function apply($model, RepositoryInterface $repository)
    {
        $value = trim(array_get($this->params, $this->key));
        if (!blank($value)) {
            $model = $model->where('created_at', '<', $value);
        }
        return $model;
    }
}