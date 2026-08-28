<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories;


use App\Common\Code;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Criteria\WhereLikeCriteria;
use App\Exceptions\QueryException;
use App\Models\InformationModel;

class InformationRepository extends BaseRepository
{
    use CommonRepository;

    public function model()
    {
        return InformationModel::class;
    }

    public function search($search_arr)
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new WhereLikeCriteria($search_arr, ['title']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $res = $this->paginate($per_page);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);

    }
}