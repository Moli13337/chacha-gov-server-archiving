<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/8
 * Time: 10:57
 */

namespace App\Criteria\ShareActivity;


use App\Criteria\BaseCriteria;
use Prettus\Repository\Contracts\RepositoryInterface;

class StatusCriteria extends BaseCriteria
{
    public function apply($model, RepositoryInterface $repository)
    {
        $value = trim(array_get($this->params, $this->key));
        if (!blank($value)) {
            $where = $this->selectTime($value);
            $model = $model->where($where);
        }
        return $model;

    }

    public function selectTime($status)
    {
        switch ($status) {
            case SHARE_ACTIVITY_STATUS['on']:
                $where = [
                    ['validity_sdate', '>', time()]
                ];
                break;
            case SHARE_ACTIVITY_STATUS['off']:
                $where = [
                    ['validity_sdate', '<', time()],
                    ['validity_edate', '>', time()],
                ];
                break;
            case SHARE_ACTIVITY_STATUS['over']:
                $where = [
                    ['validity_edate', '<', time()]
                ];
                break;
            default:
                $where = [];
        }

        return $where;
    }
}