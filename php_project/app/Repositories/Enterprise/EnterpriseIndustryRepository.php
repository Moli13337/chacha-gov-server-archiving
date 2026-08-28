<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories\Enterprise;


use App\Models\EnterpriseApplyModel;
use App\Models\EnterpriseIndustryModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class EnterpriseIndustryRepository extends BaseRepository
{
    use CommonRepository;
    public function model()
    {
        return EnterpriseIndustryModel::class;
    }

    public function selfUpdateOrCreate($where, $data)
    {
        $res = $this->model->updateOrCreate($where, $data);
        return $res;
    }

    public function getByEnterprise($enterprise_id, $column = ['*'])
    {
        $res = $this->model->select($column)->where('enterprise_id', $enterprise_id)->first();

        return empty($res) ? [] : $res->toArray();
    }
    public function storeBatch($data)
    {
        return $this->model->insert($data);
    }
}