<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/8
 * Time: 14:11
 */

namespace App\Criteria;



use Prettus\Repository\Contracts\RepositoryInterface;

class SelectCriteria extends BaseCriteria
{
    public function apply($model, RepositoryInterface $repository)
    {
        $column = empty($this->key) ? ['*'] : $this->key;
        $model = $model->select($column);
        return $model;
    }
}