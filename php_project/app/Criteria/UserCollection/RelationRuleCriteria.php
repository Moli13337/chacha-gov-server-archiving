<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/12
 * Time: 14:55
 */

namespace App\Criteria\UserCollection;


use App\Criteria\BaseCriteria;
use Prettus\Repository\Contracts\RepositoryInterface;
use function foo\func;

class RelationRuleCriteria extends BaseCriteria
{

    public function apply($model, RepositoryInterface $repository)
    {
        $column = $this->key;
        $value = trim(array_get($this->params, $column));

        if (!blank($value)) {
            switch ($value) {
                case OBJ_TYPE['project']:
                    $model = $model->whereHas('policy', function ($query) {
                        $query->where('publish_status', PUBLISH_STATUS['yes']);
                    });
                    break;
                case OBJ_TYPE['agent']:
                    $model = $model->whereHas('agent', function ($query) {
                        $query->where('publish_status', '=',PUBLISH_STATUS['yes']);
                    });
                    break;
                default:
                    $model = $model->whereHas('policy', function ($query) use ($value) {
                        $query->where('obj_type', $value)->where('publish_status', PUBLISH_STATUS['yes']);
                    });
                    break;
            }
        } else {
            $model = $model->where(function ($query) {
                $query = $query->orWhere(function ($q) {
                    $q->whereHas('policy', function ($query) {
                        $query->where('publish_status', PUBLISH_STATUS['yes']);
                    });
                    return $q;
                });
                $query = $query->orWhere(function ($q) {
                    $q->whereHas('agent', function ($query) {
                        $query->where('publish_status', PUBLISH_STATUS['yes']);
                    });
                    return $q;
                });
                $query = $query->orWhere(function ($q) {
                    $q->whereHas('project', function ($query) {
                        $query->where('publish_status', PUBLISH_STATUS['yes']);
                    });
                    return $q;
                });
                return $query;
            });
        }

        return $model;
    }
}