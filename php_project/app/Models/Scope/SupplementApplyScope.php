<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/25
 * Time: 14:52
 */

namespace App\Models\Scope;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SupplementApplyScope implements Scope
{

    /**
     * FUNCTION_NAME : apply
     *
     * 全局作用域 默认情况下排除补录的数据
     * @param Builder $builder
     * @param Model $model
     * @return Builder|void
     */
    public function apply(Builder $builder, Model $model)
    {
        return $builder->where('is_supplement', APPLY_SUPPLEMENT['no']);
    }
}