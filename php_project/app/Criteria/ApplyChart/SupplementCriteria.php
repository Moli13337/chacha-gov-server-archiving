<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/25
 * Time: 18:00
 */

namespace App\Criteria\ApplyChart;


use App\Criteria\BaseCriteria;
use App\Models\Scope\SupplementApplyScope;
use Prettus\Repository\Contracts\RepositoryInterface;

class SupplementCriteria extends BaseCriteria
{
    public function apply($model, RepositoryInterface $repository)
    {
        return $model->withoutGlobalScopes([ SupplementApplyScope::class])->supplement();
    }
}