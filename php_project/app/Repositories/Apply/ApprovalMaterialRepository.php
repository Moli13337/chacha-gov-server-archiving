<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/15
 * Time: 0:16
 */

namespace App\Repositories\Apply;


use App\Common\Code;
use App\Criteria\ApplyReplenish\ApplyCriteria;
use App\Criteria\ApplyReplenish\TimeCriteria;
use App\Criteria\Material\StatusCriteria;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Exceptions\QueryException;
use App\Models\ApprovalMaterialModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class ApprovalMaterialRepository extends BaseRepository
{

    use CommonRepository;

    public function model()
    {
        return ApprovalMaterialModel::class;
    }

    public function list($search_arr, $column =['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        $applyColumn = 'id,enterprise_id,project_id,user_id,user_name,number,project_name,enterprise_name,contact_phone';
        try {
//            $this->pushCriteria(new WhereEqualCriteria($search_arr,['status']));
            $this->pushCriteria(new StatusCriteria($search_arr,['status']));
            $this->pushCriteria(new ApplyCriteria($search_arr));
            $this->pushCriteria(new TimeCriteria($search_arr, ['start_time', 'end_time']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $this->with(['apply:'.$applyColumn, 'department:department_id,name', 'staff:id,name,mobile', 'user:user_id,name,mobile']);
            $res = $this->paginate($per_page, $column);
        } catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        $data =  page($res,$current_page);
        if (empty($data['data'])) {
            return $data;
        }

        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]['department'] = array_get($value['department'], 0,[]);
            $data['data'][$key]['user'] = array_get($value['user'], 0,[]);
        }

        return $data;
    }

    public function detail($id)
    {
        $applyColumn = 'id,enterprise_id,project_id,user_id,user_name,number,policy_name,project_name,enterprise_name,contact_phone';

        $res = $this->model->where('id', $id)
            ->with(['apply:'.$applyColumn, 'department:department_id,name', 'staff:id,name,mobile', 'user:user_id,name,mobile'])
            ->first();

        if (empty($res)) {
            return [];
        }
        $res = $res->toArray();

        $res['department'] = array_get($res['department'], 0,[]);
        $res['user'] = array_get($res['user'], 0,[]);

        return $res;
    }
}