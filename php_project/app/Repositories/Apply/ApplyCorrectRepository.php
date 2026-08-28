<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/15
 * Time: 1:03
 */

namespace App\Repositories\Apply;


use App\Common\Code;
use App\Criteria\ApplyReplenish\ApplyCriteria;
use App\Criteria\ApplyReplenish\HaveUserCriteria;
use App\Criteria\ApplyReplenish\TimeCriteria;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereCreatedEndCriteria;
use App\Criteria\WhereCreatedStartCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Criteria\WhereInCriteria;
use App\Exceptions\CodeException;
use App\Exceptions\QueryException;
use App\Models\ApplyCorrectModel;
use App\Models\ApplyModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;
use App\Repositories\Staff\StaffDepartmentRepository;
use App\Repositories\User\UserRepository;

class ApplyCorrectRepository extends BaseRepository
{

    use CommonRepository;
    public function model()
    {
        return ApplyCorrectModel::class;
    }

    public function list($search_arr, $column =['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        $applyColumn = 'id,enterprise_id,project_id,user_id,user_name,number,project_name,enterprise_name';

        try {
            $this->pushCriteria(new WhereEqualCriteria($search_arr,['status']));
            $this->pushCriteria(new ApplyCriteria($search_arr));
            $this->pushCriteria(new WhereCreatedStartCriteria($search_arr, 'start_time'));
            $this->pushCriteria(new WhereCreatedEndCriteria($search_arr, 'end_time'));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $this->with(['apply:'.$applyColumn, 'department:department_id,name',
                'operatorStaff:id,name,mobile','user:user_id,name,mobile']);
            $res = $this->paginate($per_page, $column);
        } catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        $data =  page($res,$current_page);
        if (empty($data['data'])) {
            return $data;
        }
        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]['department'] = empty($value['department'][0]) ? [] : $value['department'][0];
            $data['data'][$key]['user'] = empty($value['user'][0]) ? [] : $value['user'][0];
        }

        return $data;
    }

    public function detail($id)
    {
        $applyColumn = 'id,enterprise_id,project_id,user_id,user_name,number,project_name,enterprise_name,contact_phone';
        $res = $this->model->where('id', $id)
            ->with([
                'apply:'.$applyColumn,
                'department:department_id,name',
                'operatorStaff:id,name,mobile',
                'user:user_id,name,mobile',
                'auditDepartment:id,name',
                'auditStaff:id,name,mobile',

            ])
            ->first();

        if (empty($res)) {
            return [];
        }
        $res = $res->toArray();
//        $res['operator_staff'] = empty($res['operator_staff'][0]) ? [] : $res['operator_staff'][0];
        $res['department'] = empty($res['department'][0]) ? [] : $res['department'][0];
        $res['audit_department'] = empty($res['audit_department']) ? [] : $res['audit_department'];
        $res['user'] = empty($res['user'][0]) ? [] : $res['user'][0];
        return $res;
    }

    public function saveCorrect($params)
    {
        $res = $this->storeRepository($params);
        return $res;
    }

    public function hasWait($apply_id)
    {
        $ins = [
            APPLY_CORRECT_STATUS['one'],
            APPLY_CORRECT_STATUS['three'],
            APPLY_CORRECT_STATUS['four'],
            APPLY_CORRECT_STATUS['six'],
        ];
        return $this->model->where('apply_id',$apply_id)->whereIn('status', $ins)->count();
    }

    public function allowSubmit($apply_id)
    {
        $ins = [
            APPLY_CORRECT_STATUS['three'],
            APPLY_CORRECT_STATUS['six'],
        ];
        return $this->model->where('apply_id',$apply_id)->whereIn('status', $ins)->count();
    }

    // 在用户待订正中的详情
    public function simpleDetail($apply_id)
    {
        $ins = [
            APPLY_CORRECT_STATUS['three'],
            APPLY_CORRECT_STATUS['six'],
        ];
        $res = $this->model->where($apply_id)->whereIn('status', $ins)->orderBy('id', 'DESC')->first();
        return empty($res) ?[] : $res->toArray();
    }

    public function detailByApply($apply_id)
    {
        $applyColumn = 'id,enterprise_id,project_id,user_id,user_name,number,project_name,enterprise_name,contact_phone';
        $res = $this->model->where('apply_id', $apply_id)
            ->with([
                'apply:'.$applyColumn,
                'department:department_id,name',
                'operatorStaff:id,name,mobile',
//                'correctContent'
            ])
            ->orderBy('id', 'DESC')
            ->first();

        if (empty($res)) {
            return [];
        }
        $res = $res->toArray();
//        $res['operator_staff'] = empty($res['operator_staff'][0]) ? [] : $res['operator_staff'][0];
        $res['department'] = empty($res['department'][0]) ? [] : $res['department'][0];
        return $res;
    }

    // 指定apply_id  查询里面有需要用户订正资料的申报
    public function getUserWaitByIds($ids)
    {
        if (empty($ids)) {
            return [];
        }
        $ins = [
            APPLY_CORRECT_STATUS['three'],
            APPLY_CORRECT_STATUS['six'],
        ];
        $res  = $this->model->select(['apply_id'])->whereIn('apply_id', $ids)->whereIn('status', $ins)->get()->toArray();
        return array_column($res, 'apply_id');
    }

    public function getCorrectWaitByApply($applyId, $column=['*'])
    {
        $ins = [
            APPLY_CORRECT_STATUS['three'],
            APPLY_CORRECT_STATUS['six'],
        ];
        $res  = $this->model->select($column)->where('apply_id', $applyId)->whereIn('status', $ins)->first();
        return empty($res) ? [] : $res->toArray();
    }

    // 客户端列表
    public function clientList($search_arr, $column =['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        $applyColumn = 'id,enterprise_id,project_id,user_id,user_name,number,project_name,enterprise_name,contact_phone';
        $statusArr = [
            APPLY_CORRECT_STATUS['three'],
            APPLY_CORRECT_STATUS['four'],
            APPLY_CORRECT_STATUS['five'],
            APPLY_CORRECT_STATUS['six'],
            APPLY_CORRECT_STATUS['seven'],
            APPLY_CORRECT_STATUS['eight'],
        ];
        try {
            $this->pushCriteria(new WhereEqualCriteria($search_arr,['status']));
            $this->pushCriteria(new WhereInCriteria(['status' => $statusArr],'status'));
            $this->pushCriteria(new ApplyCriteria($search_arr));
            $this->pushCriteria(new WhereCreatedStartCriteria($search_arr, 'start_time'));
            $this->pushCriteria(new WhereCreatedEndCriteria($search_arr, 'end_time'));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $this->pushCriteria(new HaveUserCriteria($search_arr));
            $this->with(['apply:'.$applyColumn, 'department:department_id,name',
                'operatorStaff:id,name,mobile','user:user_id,name,mobile']);
            $res = $this->paginate($per_page, $column);
        } catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        $data =  page($res,$current_page);
        if (empty($data['data'])) {
            return $data;
        }
        foreach ($data['data'] as $key => $value) {
//            $data['data'][$key]['operator_staff'] = empty($value['operator_staff'][0]) ? [] : $value['operator_staff'][0];
            $data['data'][$key]['department'] = empty($value['department'][0]) ? [] : $value['department'][0];
            $data['data'][$key]['user'] = empty($value['user'][0]) ? [] : $value['user'][0];
        }

        return $data;
    }

    public function updateByWhere($where, $data)
    {
        return $this->model->where($where)->update($data);
    }

    /**
     * FUNCTION_NAME : haveCheck
     * author : jp
     * 查是否有订正完成 且待检查的
     * @param $applyId
     * @return mixed
     */
    public function haveCheck($applyId)
    {
        $where = [
            'apply_id' => $applyId,
            'status' => APPLY_CORRECT_STATUS['seven'],
            'is_check' => APPLY_CORRECT_IS_CHECK['yes'],
        ];
        return $this->model->where($where)->count();
    }

    public function lastDetail($applyId, $column =['*'])
    {
        return $this->model->where('apply_id', $applyId)->orderBy('id', 'DESC')->first();
    }

    /**
     * FUNCTION_NAME : checkApproval
     * author : jp
     * 检查当前审批 是否能向下进行的 判断 订正资料
     * @param $applyId
     * @return int
     */
    public function checkApproval($applyId)
    {
        $have = $this->lastDetail($applyId, ['status', 'is_check']);
        if (!empty($have)) {
            if ($have['is_check'] == APPLY_CORRECT_IS_CHECK['yes']) {
                return Code::APPLY_CORRECT_APPROVAL_ERROR;
            }

            if ($have['status'] == APPLY_CORRECT_STATUS['one']) {
                return Code::APPLY_CORRECT_APPROVAL_ONE_ERROR;

            } elseif ($have['status'] == APPLY_CORRECT_STATUS['three']) {
                return Code::APPLY_CORRECT_APPROVAL_THREE_ERROR;

            } elseif ($have['status'] == APPLY_CORRECT_STATUS['four']) {
                return Code::APPLY_CORRECT_APPROVAL_FOUR_ERROR;

            }
        }
        return 0;
    }



}