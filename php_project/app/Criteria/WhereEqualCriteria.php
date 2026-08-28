<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/5
 * Time: 1:05
 */

namespace App\Criteria;


use Prettus\Repository\Contracts\RepositoryInterface;

class WhereEqualCriteria extends BaseCriteria
{
    public function apply($model, RepositoryInterface $repository)
    {
        if (is_array($this->key)) {
            foreach ($this->key as $item) {
                $model  = $this->getModel($item,$model);
            }
        }
        if (is_string($this->key)) {
            $model = $this->getModel($this->key,$model);
        }
        return $model;
    }

    public function getModel($key,$model)
    {
        $value = trim(array_get($this->params, $key));
        if (!blank($value)) {
            $model = $model->where($key, $value);
        }
        return $model;
    }
}