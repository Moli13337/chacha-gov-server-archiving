<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/30
 * Time: 16:31
 */

namespace App\Criteria\Material;


use App\Criteria\BaseCriteria;
use Prettus\Repository\Contracts\RepositoryInterface;

class StatusCriteria extends BaseCriteria
{
    public function apply($model, RepositoryInterface $repository)
    {
        $column = 'status';
        $columns = $this->key;
        $value = trim(array_get($this->params, $column));
        if (!blank($value)) {
            if ($value == 1) {
                $model = $model->where('status', MATERIAL_SEND_STATUS['three']);
            } else {
                $model = $model->where('status', '<', MATERIAL_SEND_STATUS['three']);
            }
        }

        return $model;
    }
}