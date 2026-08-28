<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories\Enterprise;


use App\Common\Code;
use App\Criteria\Credit\WhereEndCriteria;
use App\Criteria\Credit\WhereStartCriteria;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Exceptions\QueryException;
use App\Models\EnterpriseCreditModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class EnterpriseCreditRepository extends BaseRepository
{
    use CommonRepository;
    public function model()
    {
        return EnterpriseCreditModel::class;
    }

    public function list($search_arr)
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new WhereEqualCriteria($search_arr,['enterprise_id','department_id']));
            $this->pushCriteria(new WhereStartCriteria($search_arr,'start_time'));
            $this->pushCriteria(new WhereEndCriteria($search_arr,'end_time'));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $this->with(['department','classFirst','classSecond']);

            $res = $this->paginate($per_page);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    public function storeBatch($data)
    {
        return $this->model->insert($data);
    }
}