<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories\Enterprise;


use App\Common\Code;
use App\Criteria\Enterprise\HaveIndustryCriteria;
use App\Criteria\Enterprise\InnerRelationCriteria;
use App\Criteria\KeywordCriteria;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereInCriteria;
use App\Exceptions\QueryException;
use App\Models\EnterpriseModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class EnterpriseRepository extends BaseRepository
{
    use CommonRepository;
    public function model()
    {
        return EnterpriseModel::class;
    }

    public function exist_name($name) {
        $res = $this->model->where(['name' => $name])->first();
        return empty($res) ? [] : $res->toArray();
    }

    public function exist_credit($credit) {
        $res = $this->model->where(['unified_credit_code' => $credit])->first();
        return empty($res) ? [] : $res->toArray();
    }

    public function detail($id)
    {
        $res = $this->model->find($id);

        return empty($res) ? [] : $res->toArray();
    }

    public function search($search_arr)
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new WhereInCriteria($search_arr,['id']));
            $this->pushCriteria(new KeywordCriteria($search_arr, ['name','legal_represent']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $this->pushCriteria(new HaveIndustryCriteria($search_arr));
            $this->with(['user' => function($query) {
                $query->select(['name', 'mobile']);
            }]);



            if (isset($search_arr['relation_status']) && $search_arr['relation_status'] == USER_ENTERPRISE_RELATION_STATUS['yes']) {
                $this->has('user');
            } elseif (isset($search_arr['relation_status']) && !blank($search_arr['relation_status']) && $search_arr['relation_status'] == USER_ENTERPRISE_RELATION_STATUS['no']) {
                $this->model->doesntHave('user');
            }

            $res = $this->paginate($per_page);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    public function getByTaxNum($number, $column = ['*'])
    {
        $res = $this->model->select($column)->whereIn('tax_number', $number)->get()->toArray();
        return $res;
    }

    /**/
    public function getByUnified($code, $column=['*'])
    {
        return $this->model->select($column)->whereIn('unified_credit_code', $code)->get()->toArray();
    }

    public function storeBatch($data)
    {
        return $this->model->insert($data);
    }

    public function conditionList($search_arr, $column=['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new KeywordCriteria($search_arr, ['name']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $res = $this->paginate($per_page, $column);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    public function getByIds($ids, $column =['*'])
    {
        return $this->model->select($column)->whereIn('id', $ids)->get()->toArray();
    }

}