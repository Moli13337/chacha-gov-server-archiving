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
use App\Models\ProjectFileModel;
use App\Models\ProjectModel;
use App\Models\ProjectPlateModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class ProjectFileRepository extends BaseRepository
{
    use CommonRepository;

    public function model()
    {
        return ProjectFileModel::class;
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

    public function deleteByProjectId($project_id)
    {
        return $this->model->where('project_id', $project_id)->delete();
    }

    public function getByProject($where, $column=['*'])
    {
        return $this->model->select($column)->where($where)->get()->toArray();
    }


}