<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/12/9
 * Time: 15:50
 */

namespace App\Criteria\AgentEnterprise;


use App\Criteria\BaseCriteria;
use App\Models\UserModel;
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
                $q = $q->orWhereHas('user', function ($query) use ($value) {
                    $query->where(UserModel::TABLE_NAME.'.name', 'like', $value);
                });
                return $q;
            });
        }

        return $model;
    }
}