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
use App\Models\AgentCommentModel;
use App\Models\AgentModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AgentCommentRepository extends BaseRepository
{

    use CommonRepository;
    
    public function model()
    {
        return AgentCommentModel::class;
    }

    public function list($search_arr, $column = ['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try {
            $this->pushCriteria(new WhereEqualCriteria($search_arr,['agent_id','stars', 'is_show', 'user_type']));
            $this->pushCriteria(new \App\Criteria\AgentComment\KeywordCriteria($search_arr));
            $this->has('agent');
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $this->with(['agent:name', 'agentType:name']);
            $res = $this->paginate($per_page, $column);
        } catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    public function clientList($search_arr, $column=['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try {
            $this->pushCriteria(new WhereEqualCriteria($search_arr,['agent_id','stars', 'is_show', 'user_type']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $res = $this->paginate($per_page, $column);
        } catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    public function getByIds($ids, $column= ['*'])
    {
        return $this->model->select($column)->whereIn('id', $ids)->get()->toArray();
    }

    public function computeStars($agent_ids)
    {
        $column = DB::raw('agent_id, user_type, sum(stars) as sum, count(*) as count');
        $res = $this->model->select($column)->whereIn('agent_id', $agent_ids)->where('is_calculate', IS_CALCULATE['yes'])->groupBy(['agent_id','user_type'])->get()->toArray();

        $data = [];
        foreach ($res as $key => $value) {
            $data[$value['agent_id']][$value['user_type']] = $value;
        }

        $exist = array_keys($data);

        foreach ($agent_ids as $ka => $va) {
            if (!in_array($va, $exist)) {
                $data[$va] =[];
            }
        }

        unset($res);
        $new = [];
        foreach ($data as $k => $v) {
            $department = 0;
            $enterprise = 0;
            $sum = 0;
            $count = 0;
            if (!empty($v[MESSAGE_USER_TYPE['staff']])) {
                $department =  empty( $v[MESSAGE_USER_TYPE['staff']]['count']) ? 0 :
                    number_format( $v[MESSAGE_USER_TYPE['staff']]['sum']/ $v[MESSAGE_USER_TYPE['staff']]['count'], 1);
                $sum += $v[MESSAGE_USER_TYPE['staff']]['sum'] * 60;
                $count += $v[MESSAGE_USER_TYPE['staff']]['count'];
            }
            if (!empty($v[MESSAGE_USER_TYPE['user']])) {
                $enterprise =  empty( $v[MESSAGE_USER_TYPE['user']]['count']) ? 0 :
                    number_format( $v[MESSAGE_USER_TYPE['user']]['sum']/ $v[MESSAGE_USER_TYPE['user']]['count'], 1);
                $sum += $v[MESSAGE_USER_TYPE['user']]['sum'] * 40;
                $count += $v[MESSAGE_USER_TYPE['user']]['count'];
            }
            $composite = empty($count) ? 0 : number_format(($sum/$count)/100,1);

            $new[] = [
                'id' => $k,
                'department_stars' => $department,
                'composite_stars' => $composite,
                'enterprise_stars' => $enterprise,
            ];

        }
        foreach ($new as $vn) {
            try {
                app(AgentRepository::class)->updateRepository($vn);
            } catch (\Exception $e) {
                Log::error('compute stars. '. $e->getMessage() );
            }
        }
//        return $new;
    }

    /**
     * FUNCTION_NAME : computeStarsV2
     * author : jp
     * 第二版计算评分
     * @param $agent_ids
     */
    public function computeStarsV2($agent_ids)
    {
        $column = DB::raw('agent_id, user_type, sum(stars) as sum, count(*) as count');

        // 查出机构
        $agentArr = AgentModel::whereIn('id', $agent_ids)->select(['id', 'submit_material', 'credit_type'])->get()->toArray();
        $agentArr = array_column($agentArr, null, 'id');

        // 这里先取部门的
        $res = $this->model->select($column)
            ->whereIn('agent_id', $agent_ids)
            ->where('is_calculate', IS_CALCULATE['yes'])
            ->where('user_type', MESSAGE_USER_TYPE['staff'])
            ->groupBy(['agent_id','user_type'])
            ->get()
            ->toArray();
        $data = [];
        foreach ($res as $key => $value) {
            $data[$value['agent_id']][$value['user_type']] = $value;
        }

        $column = DB::raw('agent_id, user_id, user_type, sum(stars) as sum, count(*) as count');
        $user = $this->model->select($column)
            ->whereIn('agent_id', $agent_ids)
            ->where('is_calculate', IS_CALCULATE['yes'])
            ->where('user_type', MESSAGE_USER_TYPE['user'])
            ->groupBy(['agent_id','user_id','user_type'])
            ->get()
            ->toArray();

        $enterpriseArr = [];
        foreach ($agentArr as $ke => $ve) {
            $tmpMain = [
                'sum' => 0,
                'count' => 0,
            ];
            if ($ve['submit_material'] == AGENT_SUBMIT_MATERIAL['yes']) {
                $tmpMain['sum'] +=5*10;
                $tmpMain['count'] +=10;
            }
            if ($ve['credit_type'] != AGENT_CREDIT_TYPE['normal']) {
                $tmpMain['sum'] +=1*3;
                $tmpMain['count'] +=3;
            }
            $enterpriseArr[$ke] = $tmpMain;
        }

        foreach ($user as $ku => $vu) {
            $tmp = array_get($enterpriseArr, $vu['agent_id'], []);
            $tmpSum = empty($vu['count']) ? 0 : ($vu['sum']/$vu['count']);
            $tmp['sum'] = array_get($tmp, 'sum', 0) + $tmpSum;
            $tmp['count'] = array_get($tmp, 'count', 0) + 1;
            $enterpriseArr[$vu['agent_id']] = $tmp;
        }

        $exist = array_keys($data);

        foreach ($agent_ids as $ka => $va) {
            if (!in_array($va, $exist)) {
                $data[$va] =[];
            }
        }

        unset($res);
        $new = [];
        foreach ($data as $k => $v) {
            $department = 0;
            $sum = 0;
            $count = 0;
            if (!empty($v[MESSAGE_USER_TYPE['staff']])) {
                $department =  empty( $v[MESSAGE_USER_TYPE['staff']]['count']) ? 0 :
                    number_format( $v[MESSAGE_USER_TYPE['staff']]['sum']/ $v[MESSAGE_USER_TYPE['staff']]['count'], 1);
                $sum += $v[MESSAGE_USER_TYPE['staff']]['sum'];
                $count += $v[MESSAGE_USER_TYPE['staff']]['count'];
            }

            // 这里取用户的评价
            $enterpriseTmp = array_get($enterpriseArr, $k, []);
            $sumEnterprise = array_get($enterpriseTmp, 'sum', 0);
            $countEnterprise = array_get($enterpriseTmp, 'count', 0);

            $enterprise =  empty( $countEnterprise) ? 0 :
                number_format( $sumEnterprise/ $countEnterprise, 1);

            $sum += $sumEnterprise;
            $count += $countEnterprise;

            $composite = empty($count) ? 0 : number_format(($sum/$count),1);

            $new[] = [
                'id' => $k,
                'department_stars' => $department,
                'composite_stars' => $composite,
                'enterprise_stars' => $enterprise,
            ];

        }
        foreach ($new as $vn) {
            try {
                app(AgentRepository::class)->updateRepository($vn);
            } catch (\Exception $e) {
                Log::error('compute stars. '. $e->getMessage() );
            }
        }
    }

    /**
     * FUNCTION_NAME : getNumGroupType
     * 统计企业 类型的 按stars分组的数量
     *
     * @param $agent_id
     * @param $type 用户类型
     * @return array
     */
    public function getNumGroupType($agent_id,$type =0, $all = null)
    {
        $column = DB::raw('stars,count(*) as count');
        $where = [];
        if (!empty($type)) {
            $where[] = ['user_type', '=', $type];
        }

        if (!blank($all)) {
            $where[] = ['is_show', '=', $all];
        }
        $res = $this->model->selectRaw($column)
            ->where('agent_id', $agent_id)
            ->where($where)
            ->groupBy('stars')
            ->get()
            ->toArray();
        $res = array_column($res, 'count', 'stars');
        $data  = [];
        foreach (STARS as $k => $va) {
            $data[$k] = (float)array_get($res, $va, 0);
        }

        $total = array_sum($data);
        $data['total'] = (float)$total;
        return $data;
    }


}