<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/13
 * Time: 18:17
 */

namespace App\Criteria\StewardPush;


use App\Criteria\BaseCriteria;
use Prettus\Repository\Contracts\RepositoryInterface;

class FollowIndustryCriteria extends BaseCriteria
{
    public function apply($model, RepositoryInterface $repository)
    {
        $obj_type = array_get($this->params, 'obj_type');
        $isFollow = getLoginHome(IS_FOLLOW_INDUSTRY);

        if (!blank($obj_type) || empty($isFollow)) {
            $model = $model->whereHas('sourcePush', function ($query) use ($obj_type, $isFollow) {
                if (!blank($obj_type)) {
                    $query = $query->where('obj_type', $obj_type);
                }
                if (empty($isFollow)) {
                    $query->whereIn('type', [STEWARD_PUSH_TYPE['authentication'], STEWARD_PUSH_TYPE['register']]);
                }
            } );
        }

        return $model;
    }
}