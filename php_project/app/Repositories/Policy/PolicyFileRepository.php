<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories\Policy;


use App\Common\Code;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Exceptions\QueryException;
use App\Models\PolicyFileModel;
use App\Models\PolicyModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class PolicyFileRepository extends BaseRepository
{
    use CommonRepository;

    public function model()
    {
        return PolicyFileModel::class;
    }

    public function storeBatch($data)
    {
        try {
            $this->model->insert($data);
        } catch (\Illuminate\Database\QueryException $e) {
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }

        return true;
    }

    public function deleteByPolicyId($policy_id)
    {
        return $this->model->where('policy_id', $policy_id)->delete();
    }

    public function getByPolicy($where, $column=['*'])
    {
        return $this->model->select($column)->where($where)->get()->toArray();
    }
}