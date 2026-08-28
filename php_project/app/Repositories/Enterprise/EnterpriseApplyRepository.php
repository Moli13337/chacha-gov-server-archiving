<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories\Enterprise;


use App\Common\Code;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Exceptions\QueryException;
use App\Models\EnterpriseApplyModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class EnterpriseApplyRepository extends BaseRepository
{
    use CommonRepository;
    public function model()
    {
        return EnterpriseApplyModel::class;
    }

    public function search($search_arr)
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new WhereEqualCriteria($search_arr,['enterprise_id']));
            $this->pushCriteria(new OrderByCriteria($search_arr));

            $res = $this->paginate($per_page);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }
}