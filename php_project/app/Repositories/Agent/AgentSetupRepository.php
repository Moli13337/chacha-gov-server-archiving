<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/17
 * Time: 19:10
 */

namespace App\Repositories\Agent;


use App\Models\AgentSetupModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class AgentSetupRepository extends BaseRepository
{

    use CommonRepository;
    
    public function model()
    {
        return AgentSetupModel::class;
    }

    public function detail($where, $column = ['*'])
    {
        $data = $this->model->select($column)->where($where)->with('file')->first();
        return empty($data) ? [] : $data->toArray();
    }

    public function getAll($where, $column=['*'])
    {
        return $this->model->select($column)->where($where)->with('file:agent_setup_id,name,save_url')->get()->toArray();
    }
}