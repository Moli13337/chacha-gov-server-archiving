<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/14
 * Time: 19:37
 */

namespace App\Criteria\ApplyReplenish;


use App\Criteria\BaseCriteria;
use App\Repositories\User\UserRepository;
use Prettus\Repository\Contracts\RepositoryInterface;

class HaveUserCriteria extends BaseCriteria
{

    public function apply($model, RepositoryInterface $repository)
    {
        $enterprise = app(UserRepository::class)->enterpriseDetail(getLoginHome('id'));
        if (empty($enterprise)) {
            $enterprise_id = -1;
        } else {
            $enterprise_id = $enterprise['id'];
        }

        $model = $model->whereHas('apply', function ($query) use ($enterprise_id) {
            $query->where('enterprise_id', $enterprise_id);
        });
        return $model;
    }
}