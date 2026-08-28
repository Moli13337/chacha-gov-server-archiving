<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/14
 * Time: 18:10
 */

namespace App\Repositories\Apply;


use App\Common\Code;
use App\Criteria\ApplyReplenish\ApplyCriteria;
use App\Criteria\ApplyReplenish\TimeCriteria;
use App\Criteria\KeywordCriteria;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Exceptions\QueryException;
use App\Models\ApplyReplenishModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class ApplyReplenishRepository extends BaseRepository
{

    use CommonRepository;

    public function model()
    {
        return ApplyReplenishModel::class;
    }

    /**
     * FUNCTION_NAME : list
     * author : jp
     * 列表
     * @param $search_arr
     * @param array $column
     * @return array
     * @throws QueryException
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function list($search_arr, $column =['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        $applyColumn = 'id,enterprise_id,project_id,user_id,user_name,number,project_name,enterprise_name,contact_phone';
        try {
            $this->pushCriteria(new WhereEqualCriteria($search_arr,['status']));
            $this->pushCriteria(new ApplyCriteria($search_arr));
            $this->pushCriteria(new TimeCriteria($search_arr, ['start_time', 'end_time']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $this->with(['apply:'.$applyColumn, 'department:id,name']);
            $res = $this->paginate($per_page, $column);
        } catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    /**
     * FUNCTION_NAME : detail
     * author : jp
     * 详情
     * @param $id
     * @return array
     */
    public function detail($id)
    {
        $applyColumn = 'id,enterprise_id,project_id,user_id,user_name,number,project_name,enterprise_name,contact_phone';

        $data = $this->model->where('id', $id)
            ->with(['apply:' . $applyColumn, 'department:id,name', 'staff:id,name,mobile'])
            ->first();

        return empty($data) ? [] : $data->toArray();
    }

}