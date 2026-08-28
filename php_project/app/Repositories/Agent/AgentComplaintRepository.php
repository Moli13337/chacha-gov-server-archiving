<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/17
 * Time: 19:10
 */

namespace App\Repositories\Agent;


use App\Common\Code;
use App\Criteria\AgentComplaint\KeywordCriteria;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Exceptions\QueryException;
use App\Models\AgentComplaintModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class AgentComplaintRepository extends BaseRepository
{

    use CommonRepository;
    
    public function model()
    {
        return AgentComplaintModel::class;
    }

    public function list($search_arr, $column = ['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try {
            $this->pushCriteria(new WhereEqualCriteria($search_arr,['agent_id','status', 'type', 'is_dispose']));
            $this->pushCriteria(new KeywordCriteria($search_arr));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $this->has('agent');
            $this->with(['agent:name', 'agentType:name', 'user:id,name,mobile', 'enterprise:id,name']);
            $res = $this->paginate($per_page);
        } catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);

    }

    public function detail($id,$column = ['*'])
    {
        $res = $this->model->select($column)->where('id', $id)->with(['agent:name', 'agentType:name', 'user:id,name,mobile', 'enterprise:id,name'])->first();

        if (empty($res)) {
            return [];
        }
        $res = $res->toArray();
        $res['agent_name'] = empty($res['agent'][0]) ? '' : array_get($res['agent'][0], 'name', '');
        unset($res['agent']);
        $res['agent_type_name'] = empty($res['agent_type'][0]) ? '' : array_get($res['agent_type'][0], 'name', '');
        unset($res['agent_type']);
        $res['user_name'] = array_get($res['user'], 'name', '');
        $res['user_mobile'] = array_get($res['user'], 'mobile', '');
        $res['enterprise_name'] = array_get($res['enterprise'], 'name', '');
        unset($res['user']);
        unset($res['enterprise']);
        $res['dispose'] = [];
        if ($res['status'] == AGENT_COMPLAINT_STATUS['success']) {
            $res['dispose'] = $this->disposeDetail($res['id'], ['user_id','content', 'type','created_at']);
        }

        return $res;
    }

    public function disposeDetail($source_id, $column =['*'])
    {
        $res = $this->model->select($column)->where('source_id', $source_id)->with('staff:id,name')->first();
        if (empty($res)) {
            return [];
        }
        $res = $res->toArray();
        $res['user_name'] = array_get($res['staff'], 'name', '');
        unset($res['staff']);
        return $res;
    }

    public function getMaxCode()
    {
        $code = $this->model->max('code');
        $code = $code ?? 0;
        return ++$code;
    }

}