<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/12/10
 * Time: 19:13
 */

namespace App\Criteria\User;


use App\Criteria\BaseCriteria;
use App\Models\UserEnterpriseRelationModel;
use App\Models\UserModel;
use Prettus\Repository\Contracts\RepositoryInterface;

class OrderByCriteria extends BaseCriteria
{

    public function apply($model, RepositoryInterface $repository)
    {
        $key = $this->key;
        $value = trim(array_get($this->params, $key, ''));
        if (!blank($value) && $value == USER_LIST_ORDER_TYPE['two']) {
            $table = UserEnterpriseRelationModel::TABLE_NAME;
            $tableSo = UserModel::TABLE_NAME;
//            $model = $model->leftJoin($table, $table.'.user_id', '=', $tableSo.'.id')->whereNull($table.'.deleted_at')->orderBy($table.'.id', 'DESC');
            $model = $model->leftJoin($table, function ($join) use ($table, $tableSo) {
                $join->on( $table.'.user_id', '=', $tableSo.'.id')->whereNull($table.'.deleted_at');
            });
        } else {
            $model = $model->orderBy('id', 'DESC');
        }

        return $model;
    }
}