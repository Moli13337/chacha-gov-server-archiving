<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/17
 * Time: 19:10
 */

namespace App\Repositories\Agent;


use App\Models\AgentSetupFileModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class AgentSetupFileRepository extends BaseRepository
{

    use CommonRepository;
    
    public function model()
    {
        return AgentSetupFileModel::class;
    }

    public function getList($agent_setup_id, $column = ['*'])
    {
        return $this->model->select($column)->where('agent_setup_id', $agent_setup_id)->get()->toArray();
    }
}