<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/5
 * Time: 11:08
 */

namespace App\Repositories\User;


use App\Models\UserFollowIndustryModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class UserFollowIndustryRepository extends BaseRepository
{

    use CommonRepository;

    public function model()
    {
        return UserFollowIndustryModel::class;
    }

    public function selfUpdateOrCreate($where, $data)
    {
        $res = $this->model->updateOrCreate($where, $data);
        return $res;
    }

    public function updateOne($where, $data)
    {
        $res = $this->model->where($where)->update($data);
        return $res;
    }

    public function deleteVice($where)
    {
        $res = $this->model->where($where)->delete();
        return $res;
    }

    public function deleteFollow($where, $ids)
    {
        $res = $this->model->where($where);
        if (!empty($ids)) {
            $res = $res->whereNotIn('id', $ids);
        }
        $res = $res->delete();
        return $res;
    }

    public function getAll($user_id, $column =['*'])
    {
        return $this->model->select($column)->where('user_id', $user_id)->get()->toArray();
    }

    public function haveFollow($user_id)
    {
        return $this->model->where('user_id', $user_id)->count();
    }



}