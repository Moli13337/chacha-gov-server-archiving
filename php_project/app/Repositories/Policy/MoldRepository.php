<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories\Policy;


use App\Common\Code;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Exceptions\QueryException;
use App\Models\MoldModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class MoldRepository extends BaseRepository
{
    use CommonRepository;

    public function model()
    {
        return MoldModel::class;
    }

    public function allList($column)
    {
        return $this->model->all($column);
    }

    public function whereList($where, $column)
    {
        return $this->model->where($where)->select($column)->get()->toArray();
    }

    public function getByIds($ids, $column)
    {
        return $this->model->whereIn('id', $ids)->select($column)->get()->toArray();
    }
}