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
use App\Models\PolicyModel;
use App\Models\PolicyMoldModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class PolicyMoldRepository extends BaseRepository
{
    use CommonRepository;

    public function model()
    {
        return PolicyMoldModel::class;
    }

    public function search($search_arr)
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new WhereEqualCriteria($search_arr, ['title']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $res = $this->paginate($per_page);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);

    }

    public function updateByPolicyId($data, $policy_id)
    {
        return $this->model->where('policy_id', $policy_id)->update($data);
    }

    public function deleteByPolicy($policy_id)
    {
        return $this->model->where('policy_id', $policy_id)->delete();
    }

    public function customUpdateOrCreate($where, $attributes)
    {
        return $this->model->updateOrCreate($where,$attributes);
    }
}