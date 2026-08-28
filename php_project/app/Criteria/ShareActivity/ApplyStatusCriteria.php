<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/8
 * Time: 11:17
 */

namespace App\Criteria\ShareActivity;


use App\Criteria\BaseCriteria;
use Prettus\Repository\Contracts\RepositoryInterface;

class ApplyStatusCriteria extends BaseCriteria
{
    public function apply($model, RepositoryInterface $repository)
    {
        $value = trim(array_get($this->params, $this->key));
        $user_id = (int)getLoginHome('id');
        if (!blank($value) && $user_id) {
            if ($value == SHARE_ACTIVITY_APPLY_STATUS['yes']) {
                $model = $model->whereHas('apply', function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                });
            }
            if ($value == SHARE_ACTIVITY_APPLY_STATUS['no']) {
                $model = $model->whereDoesntHave('apply', function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                });
            }
        }
        return $model;
    }
}