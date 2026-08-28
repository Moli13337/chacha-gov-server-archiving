<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/12/9
 * Time: 15:50
 */

namespace App\Criteria\AgentEnterprise;


use App\Criteria\BaseCriteria;
use App\Models\AgentModel;
use App\Models\UserModel;
use Prettus\Repository\Contracts\RepositoryInterface;

class HasAgentCriteria extends BaseCriteria
{

    public function apply($model, RepositoryInterface $repository)
    {
        $column = 'type_id';
        $value = trim(array_get($this->params, $column));

        if (!blank($value)) {
           $model = $model->whereHas('agent', function ($query) use ($value) {
               $query->where(AgentModel::TABLE_NAME.'.type_id', $value);
           } );
        } else {
            $model = $model->has('agent');
        }

        return $model;
    }
}