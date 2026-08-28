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
use App\Models\PolicyUnscrambleModel;
use App\Models\PolicyUnscrambleRelationModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class PolicyUnscrambleRelationRepository extends BaseRepository
{
    use CommonRepository;

    public function model()
    {
        return PolicyUnscrambleRelationModel::class;
    }

    public function has($ids)
    {
        $res = $this->model->whereIn('policy_id', $ids)->has('unscramble')->get()->toArray();

        return !empty($res);
    }

    public function hasIgnore($ids, $unscramble_id)
    {
        $res = $this->model->whereIn('policy_id', $ids)
            ->where('unscramble_id', '!=', $unscramble_id)
            ->has('unscramble')
            ->get()
            ->toArray();
        return !empty($res);
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

    public function deleteByUnscrambleId($unscramble_id)
    {
        return $this->model->where('unscramble_id', $unscramble_id)->delete();
    }

    public function policyById($id)
    {
        $res = $this->model->where('unscramble_id', $id)->has('policy')->get()->toArray();
        return $res;

    }

}