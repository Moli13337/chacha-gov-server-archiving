<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories\Enterprise;


use App\Models\CreditDepartmentModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class CreditDepartmentRepository extends BaseRepository
{
    use CommonRepository;

    public function model()
    {
        return CreditDepartmentModel::class;
    }

    public function getList($column=['*'])
    {
        return $this->model->select($column)->get()->toArray();
    }



}