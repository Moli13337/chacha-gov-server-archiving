<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/12
 * Time: 10:09
 */

namespace App\Repositories\User;


use App\Common\Code;
use App\Criteria\KeywordCriteria;
use App\Criteria\OrderByCriteria;
use App\Criteria\UserCollection\RelationRuleCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Criteria\WhereInCriteria;
use App\Exceptions\QueryException;
use App\Models\UserCollectionModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class UserCollectionRepository extends BaseRepository
{

    use CommonRepository;
    public function model()
    {
        return UserCollectionModel::class;
    }

    public function cancelCollection($where)
    {
        return $this->model->where($where)->delete();
    }

    public function clientList($search_arr, $column=['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new WhereEqualCriteria($search_arr, ['user_id','obj_type']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $this->pushCriteria(new RelationRuleCriteria($search_arr, 'obj_type'));
            $obj_type = array_get($search_arr, 'obj_type');
//            $policyWith = function ($query) {
//                $query->where('publish_status', PUBLISH_STATUS['yes']);
//            };
//            if (!blank($obj_type)) {
//
//            } else {
//                $this->with(['policy' => $policyWith , 'agent' => $policyWith, 'project' => $policyWith]);
//
//            }
            $res = $this->paginate($per_page, $column);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    public function hasCollection($where)
    {
        return $this->model->where($where)->count();
    }
}