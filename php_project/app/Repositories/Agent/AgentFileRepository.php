<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/17
 * Time: 19:10
 */

namespace App\Repositories\Agent;


use App\Models\AgentFileModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class AgentFileRepository extends BaseRepository
{

    use CommonRepository;
    
    public function model()
    {
        return AgentFileModel::class;
    }

    public function getList($agent_id, $column = ['*'])
    {
        return $this->model->select($column)->where('agent_id', $agent_id)->get()->toArray();
    }

}