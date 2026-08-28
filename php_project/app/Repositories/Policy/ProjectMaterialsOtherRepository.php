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
use App\Models\ProjectMaterialsOtherModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class ProjectMaterialsOtherRepository extends BaseRepository
{
    use CommonRepository;

    public function model()
    {
        return ProjectMaterialsOtherModel::class;
    }

    public function deleteByProjectId($project_id)
    {
        return $this->model->where('project_id', $project_id)->delete();
    }

    public function selfUpdateOrCreate($where, $data)
    {
        $res = $this->model->updateOrCreate($where, $data);
        return $res;
    }

    public function getByProject($project_id)
    {
        $res = $this->model->where('project_id', $project_id)->first();
        return empty($res) ? [] : $res->toArray();
    }

}