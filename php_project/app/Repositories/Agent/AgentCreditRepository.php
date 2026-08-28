<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/17
 * Time: 19:10
 */

namespace App\Repositories\Agent;


use App\Common\Code;
use App\Criteria\KeywordCriteria;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Exceptions\QueryException;
use App\Models\AgentCreditModel;
use App\Models\AgentModel;
use App\Models\EnterpriseModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class AgentCreditRepository extends BaseRepository
{

    use CommonRepository;
    
    public function model()
    {
        return AgentCreditModel::class;
    }

    public function list($search_arr, $column = ['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try {
            $this->pushCriteria(new WhereEqualCriteria($search_arr,['agent_id','type', 'is_audit']));
            $this->pushCriteria(new \App\Criteria\AgentCredit\KeywordCriteria($search_arr, ['project_name']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $this->has('agent');
            $this->with(['agent:name', 'agentType:name', 'staff']);
            $res = $this->paginate($per_page);
        } catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);

    }

    public function clientList($search_arr, $column = ['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try {
            $this->pushCriteria(new WhereEqualCriteria($search_arr,['agent_id','is_audit', 'is_show']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $res = $this->paginate($per_page, $column);
        } catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    public function cleanByAgentId($agent_id)
    {
        $update = [
            'is_show' => IS_SHOW['no']
        ];
        return  $this->model->whereIn('agent_id', $agent_id)->update($update);
    }

    public function creditList($search_arr, $column = ['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try {
            $this->pushCriteria(new WhereEqualCriteria($search_arr,['type','is_audit', 'is_show']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $this->has('agent');
            $this->with(['agent' => function ($query) {
                $col = [
                    EnterpriseModel::TABLE_NAME.'.name',
                    AgentModel::TABLE_NAME.'.id',
                    AgentModel::TABLE_NAME.'.enc_id',
                    AgentModel::TABLE_NAME.'.type_id',
                    AgentModel::TABLE_NAME.'.publish_status',
                ];
                $query->select($col);
            }]);
            $res = $this->paginate($per_page, $column);
        } catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

}