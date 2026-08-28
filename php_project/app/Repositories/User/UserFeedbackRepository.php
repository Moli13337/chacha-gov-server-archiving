<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories\User;


use App\Common\Code;
use App\Criteria\Feedback\UserWhereInCriteria;
use App\Criteria\KeywordCriteria;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Criteria\WhereLikeCriteria;
use App\Exceptions\QueryException;
use App\Models\UserFeedbackModel;
use App\Models\UserModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;
use Illuminate\Support\Facades\DB;

class UserFeedbackRepository extends BaseRepository
{

    use CommonRepository;

    public function model()
    {
        return UserFeedbackModel::class;
    }

    public function search($search_arr, $column= ['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new WhereEqualCriteria($search_arr, ['user_id','type','status','is_reply']));
//            $this->pushCriteria(new KeywordCriteria($search_arr, ['content']));
            $this->pushCriteria(new OrderByCriteria($search_arr));

            $this->pushCriteria(new \App\Criteria\Feedback\KeywordCriteria($search_arr, ['content']));
            $this->with(['user', 'reply']);
            $res = $this->paginate($per_page);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    public function detail($id)
    {
        try {
            $data = $this->model->find($id);
        } catch (\Illuminate\Database\QueryException $e) {
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }

        return empty($data) ? [] : $data->toArray();
    }


    /**
     * FUNCTION_NAME : todoNum
     * author : jp
     *  待处理数
     * @return array
     */
    public function todoNum()
    {
        $where = [
            'is_reply' => FEEDBACK_REPLY['user'],
            'status' => FEEDBACK_STATUS['not'],
        ];
        $col = DB::raw('type, count(*) as count');
        $res =  $this->model->select($col)->where($where)->groupBy('type')->get()->toArray();
        $keys = FEEDBACK_TYPE;
        $data = [];
        foreach ($keys as $k => $v) {
            $data[$k] = 0;
        }
        if (empty($res)) {
            return $data;
        }
        $res = array_column($res, 'count', 'type');
        foreach ($keys as $k => $v) {
            $data[$k] = array_get($res, $v,0);
        }
        return $data;
    }


}