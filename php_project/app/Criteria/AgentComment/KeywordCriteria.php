<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/5/24
 * Time: 0:07
 */

namespace App\Criteria\AgentComment;


use App\Criteria\BaseCriteria;
use App\Models\EnterpriseModel;
use Prettus\Repository\Contracts\RepositoryInterface;

class KeywordCriteria extends BaseCriteria
{
    public function apply($model,  RepositoryInterface $repository)
    {
        $column = 'keyword';
        $columns = $this->key;
        $value = trim(array_get($this->params, $column));

        if (!blank($value)) {
            $value = "%$value%";
            $model = $model->where(function ($q) use ($columns, $value) {
                $q = $q->orWhereHas('user', function ($query) use ($value) {
                    $query->where('name', 'like', $value);
                });

                $q = $q->orWhereHas('staff', function ($query) use ($value) {
                    $query->where('name', 'like', $value);
                });

                $q = $q->orWhereHas('agent', function ($query) use ($value) {
                    $query->where(EnterpriseModel::TABLE_NAME.'.name', 'like', $value);
                });
                return $q;
            });

        }
        return $model;
    }
}