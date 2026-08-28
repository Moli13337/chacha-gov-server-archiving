<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/17
 * Time: 19:10
 */

namespace App\Repositories\Agent;


use App\Models\AgentTypeModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class AgentTypeRepository extends BaseRepository
{

    use CommonRepository;
    
    public function model()
    {
        return AgentTypeModel::class;
    }

    public function getAll($column = ['*'])
    {
        return $this->model->select($column)->get()->toArray();
    }

    public function firstClass($column = ['*'])
    {
        return $this->model->select($column)->where('parent_id', 0)->orderBy('number', 'DESC')->orderBy('id', 'ASC')->get()->toArray();
    }

    public function getByIds($ids, $column = ['*'])
    {
        return $this->model->select($column)->get()->toArray();
    }

    public function list($column =['*'])
    {
        $data = $this->model->select($column)->orderBy('number', 'DESC')->orderBy('id', 'ASC')->get()->toArray();
        return getTree($data, 'id', 'parent_id');
    }

    public function reserved($column = ['*'])
    {
        return $this->model->select($column)->where('parent_id', 0)->where('reserved', RESERVED_YES)->orderBy('number', 'DESC')->orderBy('id', 'ASC')->get()->toArray();
    }
}