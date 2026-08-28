<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories\User;


use App\Common\Code;
use App\Exceptions\QueryException;
use App\Models\UserEnterpriseRelationModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class UserEnterpriseRelationRepository extends BaseRepository
{
    use CommonRepository;

    public function model()
    {
        return UserEnterpriseRelationModel::class;
    }

    public function relation($in_data)
    {
        $res = $this->model->where($in_data)->get();

        return $res->isEmpty() ? [] : $res->toArray();
    }

    public function deleteByUserId($user_id)
    {
        $res = $this->model->where('user_id', $user_id)->delete();
        return $res;
    }

    public function deleteByEnterpriseId($enterprise_id)
    {

        $res = $this->model->where('enterprise_id', $enterprise_id)->delete();

        return $res;
    }


    /**
     * FUNCTION_NAME : getByEnterpriseIds
     * author : jp
     * 通过企业ids 查找关联关系
     * @param $ids
     * @param array $column
     * @return mixed
     */
    public function getByEnterpriseIds($ids, $column = ['*'])
    {
        return $this->model->select($column)->whereIn('enterprise_id', $ids)->get()->toArray();
    }

    public function relationByUser($in_data)
    {
        $res = $this->model->where($in_data)->has('enterprise')->get();

        return $res->isEmpty() ? [] : $res->toArray();
    }

    public function relationByEnterprise($in_data)
    {
        $res = $this->model->where($in_data)->has('user')->get();

        return $res->isEmpty() ? [] : $res->toArray();
    }
}