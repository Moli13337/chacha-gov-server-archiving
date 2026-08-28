<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/6
 * Time: 14:11
 */

namespace App\Repositories\Steward;


use App\Models\Steward\StewardUserModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;
use App\Repositories\Staff\StaffRepository;

class StewardUserRepository extends BaseRepository
{
    use CommonRepository;

    public function model()
    {
        return StewardUserModel::class;
    }

    public function deleteByUserId($user_id)
    {
        return $this->model->whereIn('user_id', $user_id)->delete();
    }

    public function getSteward($user_id)
    {
        $res = $this->model->where('user_id', $user_id)->first();
        if (empty($res)) {
            return [];
        }

        $steward = app(StaffRepository::class)->getDepartmentInfo($res['staff_id'], ['id','name', 'mobile']);
        if (empty($steward)) {
            return [];
        }
        $steward['department_name'] = array_get($steward['department'], 'name', '');
        $steward['department_id'] = array_get($steward['department'], 'id', '');
        unset($steward['department']);
        return $steward;
    }

    /**
     * FUNCTION_NAME : getStewardList
     * author : jp
     * 获取指定用户的管家
     * @param $user_ids
     * @return array
     */
    public function getStewardList($user_ids)
    {
        $res = $this->model->whereIn('user_id', $user_ids)->get()->toArray();
        if (empty($res)) {
            return [];
        }
        $staff_id = array_column($res, 'staff_id');
        $steward = $this->getDepartment($staff_id);

        $steward = array_column($steward, null, 'id');
        $data = [];
        foreach ($res as $key => $val) {
            $data[$val['user_id']] = array_get($steward, $val['staff_id'], []);
        }
        return  $data;
    }

    public function getDepartment($staff_id)
    {
        $steward = app(StaffRepository::class)->getDepartmentInfoList($staff_id, ['id','name', 'mobile']);
        if (empty($steward)) {
            return [];
        }
        foreach ($steward as $key => &$value) {
            $value['department_name'] = array_get($value['department'], 'name', '');
            $value['department_id'] = array_get($value['department'], 'id', '');
            unset($value['department']);
        }

        return $steward;
    }
}