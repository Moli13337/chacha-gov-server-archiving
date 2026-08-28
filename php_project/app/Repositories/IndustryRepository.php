<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories;


use App\Common\Code;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Exceptions\QueryException;
use App\Models\IndustryModel;

class IndustryRepository extends BaseRepository
{
    use CommonRepository;

    public function model()
    {
        return IndustryModel::class;
    }

    public function search($search_arr)
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new WhereEqualCriteria($search_arr, ['title']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $res = $this->paginate($per_page);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);

    }

    public function getTree($column = ['*'], $where = [])
    {
        $data = $this->model->select($column)->where($where)->get()->toArray();
        $level = ['category', 'b_type', 'm_type', 's_type'];
        $data = array_map(function ($item) use ($level) {
            $tmp = [];
            foreach ($level as $kr => $v) {
                if ($v == 's_type' && !empty($item[$v])) {
                    $item['pid'] = implode('-', $tmp);
                    $tmp[$kr] = $item[$v];
                    $item['cid'] = implode('-', $tmp);
                    break;
                } elseif (empty($item[$v])){
                    $item['cid'] = implode('-', $tmp);
                    unset($tmp[$kr-1]);
                    $item['pid'] = implode('-', $tmp);
                    break;
                }
                $tmp[$kr] = $item[$v]??0;
            }
            return $item;
        }, $data);
        return empty($data) ? [] : getTree($data,'cid', 'pid', 'children', '' );
    }

    public function getByIds($ids, $column=['*'])
    {
        return $this->model->select($column)->whereIn('id', $ids)->get()->toArray();
    }

    /**
     * FUNCTION_NAME : firstIndustry
     * author : jp
     * client 行业
     * @return mixed
     */
    public function firstIndustry()
    {
        $data = $this->model
            ->whereNull('b_type')
            ->whereNull('m_type')
            ->whereNull('s_type')
            ->has('firstIndustry')
            ->get(['id', 'type_name'])
            ->toArray();
        return $data;
    }

    public function categoryIndustry($column=['*'])
    {
        return $this->model->select($column)
            ->whereNull('b_type')
            ->whereNull('m_type')
            ->whereNull('s_type')->get()->toArray();
    }
}