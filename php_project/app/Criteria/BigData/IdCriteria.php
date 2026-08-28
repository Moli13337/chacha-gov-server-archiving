<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/5
 * Time: 1:05
 */

namespace App\Criteria\BigData;


use App\Criteria\BaseCriteria;
use Prettus\Repository\Contracts\RepositoryInterface;
use function GuzzleHttp\Psr7\str;

class IdCriteria extends BaseCriteria
{
    public function apply($model, RepositoryInterface $repository)
    {
        // 控制上一页下一页的id
        $value = trim(array_get($this->params, 'id'));
        $next = trim(array_get($this->params, 'next'));
        if (blank($value) || blank($next)) {
            return $model;
        }
        // 需要控制 上一页、 下一页 、 id排序 等的影响
        $tmp = array_values(array_get($this->params, 'order_by', []));
        $order = strtoupper(current($tmp));
        $order = empty($order) ? 'ASC' : $order;

        $rule = [
            NEXT_PAGE['yes'] => ['DESC' => '<', 'ASC' => '>'],
            NEXT_PAGE['no'] => ['DESC' => '>', 'ASC' => '<'],
        ];
        $operator = array_get($rule[$next], $order);
        $model->where('id', $operator, $value);
        return $model;
    }
}