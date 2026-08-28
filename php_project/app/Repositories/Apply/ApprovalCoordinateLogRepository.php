<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/22
 * Time: 11:07
 */

namespace App\Repositories\Apply;


use App\Common\Code;
use App\Criteria\KeywordCriteria;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Criteria\WhereInCriteria;
use App\Exceptions\QueryException;
use App\Models\ApprovalCoordinateLogModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;
use App\Repositories\Staff\StaffDepartmentRepository;

class ApprovalCoordinateLogRepository extends BaseRepository
{

    use CommonRepository;

    public function model()
    {
        return ApprovalCoordinateLogModel::class;
    }

    public function search($search_arr)
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new WhereEqualCriteria($search_arr,['apply_id']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $this->with(['staff']);
            $res = $this->paginate($per_page);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    public function approval($where)
    {
        $res = $this->model->find($where['id'])->approval()->get()->toArray();
        if (empty($res)) {
            return [];
        }

        $department_id = array_column($res, 'department_id');
        $department = app(StaffDepartmentRepository::class)->getByIds($department_id, ['id','name']);
        $department = array_column($department, 'name', 'id');

        foreach ($res as $key => $value) {
            $res[$key]['department_name'] = array_get($department, $value['department_id'], '');
        }
        return $res;
    }

}