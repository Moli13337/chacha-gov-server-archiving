<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/13
 * Time: 18:17
 */

namespace App\Criteria\StewardPush;


use App\Criteria\BaseCriteria;
use App\Models\Steward\StewardPushModel;
use App\Models\Steward\StewardPushRecordModel;
use App\Models\UserPushModel;
use Prettus\Repository\Contracts\RepositoryInterface;

class UserPushProjectCriteria extends BaseCriteria
{
    public function apply($model, RepositoryInterface $repository)
    {
        // 是否关注行业
        $isFollow = getLoginHome(IS_FOLLOW_INDUSTRY);
        $user_id = getLoginHome('id');
        $steward_push = array_get($this->params, 'steward_push');

        if (!blank($steward_push) && !empty($user_id)) {
            $model = $model->whereHas('userPush', function ($query) use ($user_id, $isFollow) {
                $query = $query->where(UserPushModel::TABLE_NAME.'.user_id', $user_id);
            });
        }
        return $model;
    }
}