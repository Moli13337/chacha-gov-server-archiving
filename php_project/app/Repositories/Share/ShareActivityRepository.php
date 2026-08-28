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
use App\Criteria\SelectCriteria;
use App\Criteria\ShareActivity\ApplyStatusCriteria;
use App\Criteria\ShareActivity\StatusCriteria;
use App\Criteria\ShareActivity\TimeCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Exceptions\QueryException;
use App\Models\Share\ShareActivityModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;
use Illuminate\Support\Facades\DB;

class ShareActivityRepository extends BaseRepository
{

    use CommonRepository;

    public function model()
    {
        return ShareActivityModel::class;
    }

    public function detail($where, $column = ['*'])
    {
        $data = $this->model->select($column)->where($where)->first();
        return empty($data) ? [] : $data->toArray();
    }


    public function simpleDetail($where, $column = ['*'])
    {
        $data = $this->model->select($column)->where($where)->first();
        return empty($data) ? [] : $data->toArray();
    }

    public function list($search_arr, $column =['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try {
            $this->pushCriteria(new WhereEqualCriteria($search_arr,['type', 'publish_status']));
            $this->pushCriteria(new KeywordCriteria($search_arr,['title', 'sponsor']));
            $this->pushCriteria(new StatusCriteria($search_arr, 'status'));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $this->withCount(['apply']);
            $res = $this->paginate($per_page, $column);
        } catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }


    public function getByEncId($enc_id, $column = ['*'])
    {
        $data = $this->model->select($column)->where('enc_id', $enc_id)->first();

        return empty($data) ? [] : $data->toArray();
    }

    /**
     * FUNCTION_NAME : computeStatus
     * author : jp
     *  计算状态
     * @param $start
     * @param $end
     * @return int
     */
    public function computeStatus($start, $end)
    {
        if ( $end < time() ) {
            return SHARE_ACTIVITY_STATUS['over'];
        } elseif ($start < time()) {
            return SHARE_ACTIVITY_STATUS['off'];
        } elseif ($start > time()) {
            return SHARE_ACTIVITY_STATUS['on'];
        } else {
            return 0;
        }
    }

    public function clientList($search_arr, $column =['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
//        $column = $this->resetSelectColumn($column, ShareActivityModel::TABLE_NAME);
        try {
            $this->pushCriteria(new WhereEqualCriteria($search_arr,['publish_status', 'province_code','city_code','district_code']));
            $this->pushCriteria(new KeywordCriteria($search_arr,['title', 'sponsor']));
            $this->pushCriteria(new TimeCriteria($search_arr));
            $this->pushCriteria(new StatusCriteria($search_arr, 'status'));
            $this->pushCriteria(new ApplyStatusCriteria($search_arr, 'apply_status'));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $user_id = (int)getLoginHome('id');
            $this->withCount(['apply' => function($query) use ($user_id) {
                if (!empty($user_id)) {
                    $query->where('user_id', $user_id);
                } else {
                    $query->where(DB::raw('1 != 1'));
                }
            }]);
            $res = $this->paginate($per_page, $column);
        } catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    public function clientDetail($where, $column = ['*'])
    {
        $user_id = (int)getLoginHome('id');
        $data = $this->model->select($column)->withCount(['apply' => function($query) use ($user_id) {
            if (!empty($user_id)) {
                $query->where('user_id', $user_id);
            } else {
                $query->where(DB::raw('1 != 1'));
            }
        }])->where($where)->first();
        return empty($data) ? [] : $data->toArray();
    }
}