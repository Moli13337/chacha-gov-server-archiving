<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/5
 * Time: 13:57
 */

namespace App\Repositories\Share;


use App\Common\Code;
use App\Criteria\KeywordCriteria;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Exceptions\QueryException;
use App\Models\Share\ShareActivityApplyModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class ShareActivityApplyRepository extends BaseRepository
{

    use CommonRepository;

    public function model()
    {
        return ShareActivityApplyModel::class;
    }

    public function list($search_arr, $column =['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try {
            $this->pushCriteria(new WhereEqualCriteria($search_arr,['activity_id']));
            $this->pushCriteria(new KeywordCriteria($search_arr,['user_name', 'enterprise_name']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $res = $this->paginate($per_page, $column);
        } catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    /**
     * FUNCTION_NAME : hasCount
     * author : jp
     * author : jp
     * @param $where
     * @return mixed
     */
    public function hasCount($where)
    {
        return $this->model->where($where)->count();
    }

}