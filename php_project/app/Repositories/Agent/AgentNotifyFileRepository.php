<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/17
 * Time: 19:10
 */

namespace App\Repositories\Agent;


use App\Models\AgentNotifyFileModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class AgentNotifyFileRepository extends BaseRepository
{

    use CommonRepository;
    
    public function model()
    {
        return AgentNotifyFileModel::class;
    }

    public function getList($agent_notify_id, $column = ['*'])
    {
        return $this->model->select($column)->where('agent_notify_id', $agent_notify_id)->get()->toArray();
    }
}