<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories;


use App\Common\Code;
use App\Criteria\KeywordCriteria;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Criteria\WhereInCriteria;
use App\Criteria\WhereNotEqualCriteria;
use App\Exceptions\QueryException;
use App\Models\ActivityLogModel;

class ActivityLogRepository extends BaseRepository
{

    use CommonRepository;

    public function model()
    {
        return ActivityLogModel::class;
    }

    public function getList($search_arr, $column=['*'])
    {

        $search_arr['order_by'] =  ['id' => 'DESC'];
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new WhereEqualCriteria($search_arr, ['subject_type_id', 'subject_id']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $res = $this->paginate($per_page);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);

    }


    public function getPolicyList($search_arr, $column=['*'])
    {
        $in = [
            ACTIVITY_SUBJECT_TYPE['macro_policy'],
            ACTIVITY_SUBJECT_TYPE['sup_policy'],
            ACTIVITY_SUBJECT_TYPE['imple_regu'],
            ACTIVITY_SUBJECT_TYPE['announce'],
            ACTIVITY_SUBJECT_TYPE['publicity'],
        ];

        $search_arr['order_by'] =  ['id' => 'DESC'];
        $search_arr['subject_type_id'] = $in;

        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new WhereEqualCriteria($search_arr, ['subject_id']));
            $this->pushCriteria(new WhereInCriteria($search_arr, ['subject_type_id']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $res = $this->paginate($per_page);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    public function store($params)
    {
        $params['ip'] = ip(1,true);
        $this->model->create($params);
    }

    public function list($search_arr)
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new WhereEqualCriteria($search_arr, ['subject_type_id']));
            $this->pushCriteria(new WhereNotEqualCriteria($search_arr, ['causer_id']));
            $this->pushCriteria(new KeywordCriteria($search_arr, ['causer_name']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $res = $this->paginate($per_page);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }
}