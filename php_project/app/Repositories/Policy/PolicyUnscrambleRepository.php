<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories\Policy;


use App\Common\Code;
use App\Criteria\KeywordCriteria;
use App\Criteria\OrderByCriteria;
use App\Criteria\Unscramble\WhereCreatedEndCriteria;
use App\Criteria\Unscramble\WhereCreatedStartCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Criteria\WhereLikeCriteria;
use App\Events\BatchDelete;
use App\Exceptions\QueryException;
use App\Models\PolicyUnscrambleModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class PolicyUnscrambleRepository extends BaseRepository
{
    use CommonRepository;

    public function model()
    {
        return PolicyUnscrambleModel::class;
    }

    public function search($search_arr)
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']): env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new WhereEqualCriteria($search_arr, ['publish_status']));
            $this->pushCriteria(new WhereCreatedStartCriteria($search_arr, 'start_time'));
            $this->pushCriteria(new WhereCreatedEndCriteria($search_arr, 'end_time'));

            $this->pushCriteria(new  KeywordCriteria($search_arr,['name']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $this->with(['policy']);
            $res = $this->paginate($per_page);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);

    }

    public function detail($id)
    {
        $res = $this->model->with(['policy','staff'])->find($id);

        return empty($res) ? [] : $res->toArray();
    }

    public function getByLikeCode($data, $column = ['*'])
    {
        $data = $this->model->select($column)->where('code', 'like', $data.'%')->orderBy('id', 'desc')->first();
        return empty($data) ? [] : $data->toArray();
    }

    public function cDetail($id, $column= ['*'])
    {
        $res = $this->model
            ->select($column)
            ->where('publish_status', PUBLISH_STATUS['yes'])
            ->with(['policy' => function ($query) {
                $query->where('publish_status', PUBLISH_STATUS['yes']);
            }])
            ->where('enc_id', $id)
            ->first();

        return empty($res) ? [] : $res->toArray();
    }

    public function getByEncId($enc_id, $column = ['*'])
    {
        $data = $this->model->select($column)->where('enc_id', $enc_id)->first();

        return empty($data) ? [] : $data->toArray();
    }

    public function deleteBatch($ids)
    {
        event(new BatchDelete($ids, ACTIVITY_SUBJECT_TYPE['unscramble']));
        $this->model->whereIn('id', $ids)->delete();
    }

}