<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/5
 * Time: 1:05
 */

namespace App\Criteria;


use Prettus\Repository\Contracts\RepositoryInterface;

class OrderByCriteria extends BaseCriteria
{
    /**

     * 应用过滤条件:按自定义规则排序
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $items = array_get($this->params, 'order_by',[]);
        if (is_array($items) && !empty($items)) {
            foreach ($items as $field => $type) {
                $model = $model->orderBy($field, $type);

            }
        }
        return $model;
    }

}