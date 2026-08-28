<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/4
 * Time: 16:49
 */

namespace App\Repositories\Steward;


use App\Common\Code;
use App\Criteria\KeywordCriteria;
use App\Criteria\OrderByCriteria;
use App\Criteria\StewardPush\FollowIndustryCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Exceptions\QueryException;
use App\Models\Steward\StewardPushRecordModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class StewardPushRecordRepository extends BaseRepository
{

    use CommonRepository;

    public function model()
    {
        return StewardPushRecordModel::class;
    }

    public function list($search_arr, $column =['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try {
            $this->pushCriteria(new WhereEqualCriteria($search_arr,['steward_push_id']));
            $this->pushCriteria(new KeywordCriteria($search_arr,['enterprise_name']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $res = $this->paginate($per_page, $column);
        } catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    /**
     * FUNCTION_NAME : trumpet
     * author : jp
     * 小喇叭
     * @param $search_arr
     * @param array $column
     * @return array
     * @throws QueryException
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function trumpet($search_arr, $column=['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try {
            $this->pushCriteria(new WhereEqualCriteria($search_arr,['user_id']));
            $this->pushCriteria(new KeywordCriteria($search_arr,['content']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $this->pushCriteria(new FollowIndustryCriteria($search_arr));

            $this->with('sourcePush:id,obj_type,obj_enc_id');
            $res = $this->paginate($per_page, $column);
        } catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }
}