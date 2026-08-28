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
use App\Models\ProjectMaterialsModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class ProjectMaterialsRepository extends BaseRepository
{
    use CommonRepository;

    public function model()
    {
        return ProjectMaterialsModel::class;
    }

    public function getAllByProject($project_id, $column=['*'])
    {
        return $this->model->select($column)->where('project_id', $project_id)->get()->toArray();
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

    public function deleteByIds($ids)
    {
        return $this->model->whereIn('id', $ids)->delete();
    }
}