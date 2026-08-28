<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/17
 * Time: 19:10
 */

namespace App\Repositories\Steward;


use App\Common\Code;
use App\Criteria\KeywordCriteria;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Exceptions\QueryException;
use App\Models\Steward\StewardUserOpinionModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class StewardUserOpinionRepository extends BaseRepository
{

    use CommonRepository;
    
    public function model()
    {
        return StewardUserOpinionModel::class;
    }

    public function detail($where, $column = ['*'])
    {
        $data = $this->model->select($column)->where($where)->with(['file:id,steward_user_opinion_id,name,save_url'])->first();
        return empty($data) ? [] : $data->toArray();
    }

    public function list($search_arr, $column =['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try {
            $this->pushCriteria(new WhereEqualCriteria($search_arr,['steward_opinion_id']));
            $this->pushCriteria(new KeywordCriteria($search_arr,['user_name', 'enterprise_name']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $res = $this->paginate($per_page, $column);
        } catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);    }

}