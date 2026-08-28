<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories\User;


use App\Common\Code;
use App\Criteria\KeywordCriteria;
use App\Criteria\OrderByCriteria;
use App\Criteria\Policy\WhereEqualExpiredCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Criteria\WhereLikeCriteria;
use App\Exceptions\QueryException;
use App\Models\EnterpriseModel;
use App\Models\UserModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;
use function foo\func;

class UserRepository extends BaseRepository
{
    use CommonRepository;
    public function model()
    {
        return UserModel::class;
    }

    public function forgetPwdByMobile($data)
    {
        try {
            $res = $this->model->where('mobile', $data['mobile'])->update(array_except($data, 'mobile'));
        } catch (\Illuminate\Database\QueryException $e) {
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }

        return $res;
    }

    public function forgetPwdById($data)
    {
        try {
            $res = $this->model->where('id', $data['id'])->update(array_except($data, 'id'));
            app(UserTokenRepository::class)->resetToken($data['id']);
        } catch (\Illuminate\Database\QueryException $e) {
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }

        return $res;
    }

    public function login($data, $column = ['*'])
    {
        try {
            $res = $this->model->where($data)->get($column);
        } catch (\Illuminate\Database\QueryException $e) {
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }

        return $res->isEmpty() ? [] : $res->first()->toArray();
    }

    public function detail($id)
    {
        $res = $this->model->with(['enterprise'])->find($id);
        return empty($res) ? [] : $res->toArray();
    }

    public function list($search_arr, $column = ['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new KeywordCriteria($search_arr, ['name', 'mobile']));
            $this->pushCriteria(new WhereEqualCriteria($search_arr, ['is_forbidden']));
//            $this->pushCriteria(new OrderByCriteria($search_arr));
            $this->pushCriteria(new \App\Criteria\User\OrderByCriteria($search_arr, 'order_type'));
            $this->with('enterprise');
            if (isset($search_arr['relation_status']) && $search_arr['relation_status'] == USER_ENTERPRISE_RELATION_STATUS['yes']) {
                $this->has('enterprise');
            } elseif (isset($search_arr['relation_status']) && !blank($search_arr['relation_status']) && $search_arr['relation_status'] == USER_ENTERPRISE_RELATION_STATUS['no']) {
                $this->model->doesntHave('enterprise');
            }
            $order_type = array_get($search_arr, 'order_type');
            if ($order_type == USER_LIST_ORDER_TYPE['two']) {
                $column = [UserModel::TABLE_NAME.'.*'];
            }
            $res = $this->paginate($per_page, $column);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    public function enterpriseDetail($id, $column = ['*'])
    {
        $data = $this->model->select($column)->find($id)->enterprise()->first();

        return empty($data) ? [] : $data->toArray();
    }

    public function existMobile($mobile)
    {
        $res = $this->model->where('mobile', $mobile)->first();

        return !empty($res);
    }

    /**
     * FUNCTION_NAME : authCount
     * author : jp
     * 认证总数
     * @return mixed
     */
    public function authCount()
    {
        return $this->model->has('enterprise')->count();
    }

    /**
     * FUNCTION_NAME : getAccount
     * author : jp
     * 查询用户 手机/邮箱
     * @param $account
     * @return array
     */
    public function getAccount($account)
    {
        if (is_numeric($account)) {
            $user = $this->model->where('mobile', $account)->first();
        } else {
            $user = $this->model->where('email', $account)->first();
        }

        return empty($user) ? [] : $user->toArray();
    }

    public function getByIds($ids, $column=['*'], $trashed = null)
    {
        $model = $this->model;
        if ($trashed == QUERY_TRASHED) {
            $model = $model->withTrashed();
        }

        return $model->select($column)->whereIN('id', $ids)->get()->toArray();
    }

    public function getEnterpriseByIds($ids, $column=['*'])
    {
        return $this->model->select(['id'])->whereIn('id', $ids)->with(['enterprise' => function ($query) {
            $query->select([EnterpriseModel::TABLE_NAME.'.id', EnterpriseModel::TABLE_NAME.'.name']);
        }])->get()->toArray();

    }

    public function getRegister($where, $column = ['*'])
    {
        return $this->model->select($column)->where($where)->where('is_forbidden', USER_FORBIDDEN['no'])->get()->toArray();
    }

    public function getAuthentication($where, $column = ['*'])
    {
        return $this->model->select($column)->where($where)->has('enterprise')->with(['enterprise' => function($query) {
             $query->select([EnterpriseModel::TABLE_NAME.'.name', EnterpriseModel::TABLE_NAME.'.id']);
        }])->get()->toArray();
    }

    public function getUserByFollowIndustry($search_arr, $column=['*'])
    {

        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{

            $this->pushCriteria(new WhereEqualCriteria($search_arr, ['is_forbidden']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $this->with(['enterprise' => function($query) {
                $query->select([EnterpriseModel::TABLE_NAME.'.name', EnterpriseModel::TABLE_NAME.'.id']);
            }]);
            $keyword = '';
            if (!blank($search_arr['keyword'])) {
                $keyword = $search_arr['keyword'];
                $keyword = "%$keyword%";
            }
            $this->whereHas('enterprise', function($query) use ($keyword) {
                if (!empty($keyword)) {
                    $query->where('name', 'like', $keyword);
                }
            });
            $where = [
                "first_industry_id" ,
                "second_industry_id",
                "third_industry_id" ,
                "fourth_industry_id",
            ];
            $where = array_only($search_arr, $where);
            $this->whereHas('followIndustry',function ($query) use ($where) {
                $query->where($where);
            });
            $res = $this->paginate($per_page,$column);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    public function getByUid($uid)
    {
        $user = $this->model->where('uid', $uid)->first();

        return empty($user) ? [] : $user->toArray();
    }

}