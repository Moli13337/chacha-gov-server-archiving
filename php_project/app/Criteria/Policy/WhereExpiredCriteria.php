<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/5
 * Time: 1:05
 */

namespace App\Criteria\Policy;


use App\Criteria\BaseCriteria;
use Prettus\Repository\Contracts\RepositoryInterface;

class WhereExpiredCriteria extends BaseCriteria
{
    public function apply($model, RepositoryInterface $repository)
    {
        $value = trim(array_get($this->params, $this->key));
        if (!blank($value)) {
            // 处理过期
            if ($value == EXPIRED['no']) {
                $model->where(function ($query) {
                    $query->where('validity_edate', '>', time());
                    $query->orWhere('validity_edate', 0);
                });
            } elseif ($value == EXPIRED['yes']) {
                $model->where(function ($query) {
                    $query->where('validity_edate', '<=', time());
                    $query->where('validity_edate', '!=', 0);
                });
            }
        }
        return $model;
    }
}