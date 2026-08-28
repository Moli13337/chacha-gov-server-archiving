<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories\Policy;


use App\Common\Code;
use App\Exceptions\QueryException;
use App\Models\ProjectIndustryModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class ProjectIndustryRepository extends BaseRepository
{
    use CommonRepository;

    public function model()
    {
        return ProjectIndustryModel::class;
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

    public function deleteByProjectId($policy_id)
    {
        return $this->model->where('project_id', $policy_id)->delete();
    }

    public function list($policy_id, $column =['*'])
    {
        return $this->model->select($column)->where('project_id', $policy_id)->get()->toArray();
    }
}