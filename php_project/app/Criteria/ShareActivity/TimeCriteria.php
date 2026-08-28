<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/7
 * Time: 18:19
 */

namespace App\Criteria\ShareActivity;


use App\Criteria\BaseCriteria;
use Prettus\Repository\Contracts\RepositoryInterface;

class TimeCriteria extends BaseCriteria
{
    public function apply($model, RepositoryInterface $repository)
    {

        $start = trim(array_get($this->params, 'start_time'));
        $end = trim(array_get($this->params, 'end_time'));
        if ((empty($start) && empty($end)) || empty($this->params)) {
            return $model;
        }
        return $model->where('validity_edate', '>', $start)->where('validity_sdate', '<', $end);
    }
}