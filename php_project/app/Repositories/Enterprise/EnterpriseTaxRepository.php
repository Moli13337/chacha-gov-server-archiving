<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories\Enterprise;


use App\Common\Code;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Events\LogCommon;
use App\Exceptions\QueryException;
use App\Models\EnterpriseTaxModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class EnterpriseTaxRepository extends BaseRepository
{
    use CommonRepository;
    public function model()
    {
        return EnterpriseTaxModel::class;
    }

    public function search($search_arr)
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new WhereEqualCriteria($search_arr,['enterprise_id', 'type']));
            $this->pushCriteria(new OrderByCriteria($search_arr));

            $res = $this->paginate($per_page);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    public function deleteBatch($ids)
    {
        event(new LogCommon([
            'type' => ACTIVITY_TYPE['deleted'],
            'description' => trans('mysqlColumn.enterprise.tax_import'),
            'attribute' => $ids,
            'old' => [],
        ], ACTIVITY_SUBJECT_TYPE['enterprise']));
        return $this->model->whereIn('id', $ids)->delete();
    }

    /**
     * FUNCTION_NAME : getByEYT
     * author : jp
     * 通过 企业 年 类型 查找数据
     * @param $where
     * @param array $column
     * @return array
     */
    public function getByEYT($where, $column=['*'])
    {
        $res = $this->model->select($column)->where($where)->first();

        return empty($res) ? [] : $res->toArray();
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


}