<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/5/24
 * Time: 0:07
 */

namespace App\Criteria;


use Prettus\Repository\Contracts\RepositoryInterface;

class KeywordCriteria extends BaseCriteria
{
    public function apply($model, RepositoryInterface $repository)
    {
        $column = 'keyword';
        $columns = $this->key;
        $value = trim(array_get($this->params, $column));

        if (!blank($value)) {
            $value = "%$value%";
            $model = $model->where(function ($q) use ($columns,$value) {
                $q = $q->where($columns[0], 'like', $value);

                foreach ($columns as $k => $v) {
                    if ($k == 0) {
                        continue;
                    }
                    $q = $q->orWhere($v, 'like', $value);
                }
                return $q;

            });

        }
        return $model;
    }
}