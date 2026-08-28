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
use App\Models\PolicyConclusionModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class PolicyConclusionRepository extends BaseRepository
{
    use CommonRepository;

    public function model()
    {
        return PolicyConclusionModel::class;
    }

    public function updateByPolicyId($data, $policy_id)
    {
        return $this->model->where('policy_id', $policy_id)->update($data);
    }
}