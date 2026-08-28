<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories\Policy;


use App\Common\Code;
use App\Criteria\BigData\IdCriteria;
use App\Criteria\BigData\WhereEndCriteria;
use App\Criteria\BigData\WhereStartCriteria;
use App\Criteria\KeywordCriteria;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Exceptions\QueryException;
use App\Models\BigDataModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class BigDataRepository extends BaseRepository
{
    use CommonRepository;

    public function model()
    {
        return BigDataModel::class;
    }

    public function search($search_arr)
    {
        $current_page = array_get($search_arr,'page',1);
        $current_page = 1;
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');

        $flag = false;
        try{
            $this->pushCriteria(new WhereEqualCriteria($search_arr, ['obj_type','is_handle']));

            $this->pushCriteria(new IdCriteria($search_arr));
            if (isset($search_arr['next']) && $search_arr['next'] == NEXT_PAGE['no']) {
                $reverse = [
                    'DESC' => 'ASC',
                    'ASC' => 'DESC',
                ];
                $search_arr['order_by']['id'] = $reverse[strtoupper(array_get($search_arr['order_by']??[], 'id', 'ASC'))];
                $flag = true;
            }
            $this->pushCriteria(new WhereStartCriteria($search_arr, 'start_time'));
            $this->pushCriteria(new WhereEndCriteria($search_arr, 'end_time'));
            $this->pushCriteria(new KeywordCriteria($search_arr, ['name','code','doc_num','source_web']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $res = $this->paginate($per_page);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        if ($flag) {
            $data = page($res,$current_page);
            $data['data'] = array_reverse($data['data']);
            return $data;
        }
        return page($res,$current_page);

    }

    public function updatePartition($ids, $obj_type)
    {
        $this->model->whereIn('id', $ids)->update(['obj_type' => $obj_type]);
        return true;
    }

    public function deleteBatch($ids)
    {
        $this->model->whereIn('id', $ids)->delete();
        return true;
    }

    public function storeBatch($data)
    {
        return $this->model->insert($data);
    }

    public function originalLast()
    {
        $res = $this->model->orderBy('original_big_data_id', 'DESC')->first(['original_big_data_id']);
        return empty($res) ? 0 : $res->toArray()['original_big_data_id'];
    }
}