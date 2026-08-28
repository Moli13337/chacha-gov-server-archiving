<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories\User;


use App\Common\Code;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Exceptions\QueryException;
use App\Models\UserMessageModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class UserMessageRepository extends BaseRepository
{
    use CommonRepository;

    public function model()
    {
        return UserMessageModel::class;
    }

    public function list($search_arr)
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new WhereEqualCriteria($search_arr, ['user_id', 'user_type', 'source_type_id', 'is_read']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $res = $this->paginate($per_page);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }


    public function detail($where, $column = ['*'])
    {
        $res = $this->model->select($column)->where($where)->first();
        return empty($res) ?[] : $res->toArray();
    }

    /**
     * FUNCTION_NAME : updateRead
     * author : jp
     * 更新已读
     * @param $id
     * @return bool
     */
    public function updateRead($id)
    {
        $update = [
            'is_read' => USER_MESSAGE_READ['is']
        ];
        $this->model->where('id', $id)->update($update);
        return true;
    }

    public function storeBatch($data)
    {
        try {
            $this->model->insert($data);
        } catch (\Illuminate\Database\QueryException $e) {
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }

        return true;
    }

    /**
     * FUNCTION_NAME : unReadNum
     * author : jp
     * 消息未读数
     * @param $where
     * @return mixed
     */
    public function unReadNum($where)
    {

        return $this->model->where($where)->where('is_read', USER_MESSAGE_READ['not'])->count();
    }

    /**
     * FUNCTION_NAME : read
     * author : jp
     * 读消息
     * @param $where
     * @return mixed
     */
    public function read($where)
    {
        return $this->model->where($where)->update(['is_read' => USER_MESSAGE_READ['is']]);
    }

    public function readApproval(int $target_id)
    {
        $source = [
            USER_MESSAGE_SOURCE['two'],
            USER_MESSAGE_SOURCE['three'],
            USER_MESSAGE_SOURCE['four'],
            USER_MESSAGE_SOURCE['five'],
            USER_MESSAGE_SOURCE['six'],
        ];
        // 先看已读的情况
        $data = $this->model->where('target_id', $target_id)->where('source_type_id', $source)->first();
        if (empty($data) || $data['is_read'] == USER_MESSAGE_READ['is']) {
            return;
        }

        $where = [
            'id' => $data['id']
        ];
        $this->model->where($where)->update(['is_read' => USER_MESSAGE_READ['is']]);
        return;
    }


}