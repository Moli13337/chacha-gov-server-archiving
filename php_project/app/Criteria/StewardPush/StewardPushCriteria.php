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
use Prettus\Repository\Contracts\RepositoryInterface;

class StewardPushCriteria extends BaseCriteria
{
    public function apply($model, RepositoryInterface $repository)
    {
        $isFollow = getLoginHome(IS_FOLLOW_INDUSTRY);
        $user_id = getLoginHome('id');
        if (empty($user_id)) {
           return $model;
        }
        $type = array_get($this->params, 'type', '');
        $model = $model->whereHas('stewardPush', function ($query) use ($isFollow, $user_id, $type) {
            $query = $query->where(StewardPushRecordModel::TABLE_NAME.'.user_id', $user_id);
            if (!blank($type)) {
                $query = $query->where(StewardPushModel::TABLE_NAME.'.obj_type', $type);
            }
            if (empty($isFollow)) {
                $query->whereIn(StewardPushModel::TABLE_NAME.'.type', [STEWARD_PUSH_TYPE['authentication'], STEWARD_PUSH_TYPE['register']]);
            }
        } );

        return $model;
    }
}