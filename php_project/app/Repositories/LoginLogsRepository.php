<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories;


use App\Criteria\OrderByCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Models\LoginLogsModel;
use App\Models\OperationLogsModel;

class LoginLogsRepository extends BaseRepository
{
    use CommonRepository;

    public function model()
    {
        return LoginLogsModel::class;
    }

    public function list($search_arr)
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new WhereEqualCriteria($search_arr, ['source_id', 'source_type']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $res = $this->paginate($per_page);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }
}