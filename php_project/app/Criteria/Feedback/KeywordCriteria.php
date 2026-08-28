<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/5/24
 * Time: 0:07
 */

namespace App\Criteria\Feedback;


use App\Criteria\BaseCriteria;
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

                // 关联表的模糊搜索
                $q = $q->orWhere(function ($q) use ($value) {
                    $q->whereHas('user', function ($query) use ($value){
                        $query->where(function ($query) use ($value) {
                            $query->Where('name', 'like', '%'.$value.'%');
                            $query->orWhere('mobile', 'like', '%'.$value.'%');
                        });

                    });
                });

                return $q;

            });

        }
        return $model;
    }
}