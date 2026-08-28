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
use App\Criteria\WhereInCriteria;
use App\Events\BatchPublish;
use App\Exceptions\QueryException;
use App\Http\Controllers\Service\DistrictService;
use App\Models\AgentModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class AgentRepository extends BaseRepository
{

    use CommonRepository;
    
    public function model()
    {
        return AgentModel::class;
    }

    public function detail($where, $column = ['*'])
    {
        $res = $this->model->select($column)->where($where)->with(['file:agent_id,name,save_url','enterprise:id,name', 'agentType:id,name']);
        $user_id = (int)getLoginHome('id');
        if (!empty($user_id)) {
            $res = $res->withCount(['collections' => function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('obj_type', OBJ_TYPE['agent']);
            }]);
        }
        $data = $res->first();
        return empty($data) ? [] : $data->toArray();
    }

    public function list($search_arr, $column =['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try {
            $this->pushCriteria(new WhereEqualCriteria($search_arr,['enterprise_id','type_id', 'credit_type']));
            $this->pushCriteria(new \App\Criteria\Agent\KeywordCriteria($search_arr,['contact_name']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $this->with(['enterprise:id,name', 'agentType:id,name']);
            $res = $this->paginate($per_page, $column);
        } catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    public function clientList($search_arr, $column =['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try {
            $this->pushCriteria(new WhereEqualCriteria($search_arr,['province_code','city_code','district_code','publish_status', 'credit_type']));
            $this->pushCriteria(new WhereInCriteria($search_arr,['type_id']));
            $this->pushCriteria(new \App\Criteria\Agent\KeywordCriteria($search_arr,['service_item']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $this->with(['enterprise:id,name', 'agentType:id,name']);
            $res = $this->paginate($per_page, $column);
        } catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    public function getByEncId($enc_id, $column = ['*'])
    {
        $data = $this->model->select($column)->where('enc_id', $enc_id)->first();

        return empty($data) ? [] : $data->toArray();
    }

    public function getMaxCode()
    {
        $code = $this->model->withTrashed()->max('code');
        $code = $code ?? 0;
        return ++$code;
    }

    public function cleanCreditByIds($ids)
    {
        $update = [
            'credit_type' => 0
        ];
        return $this->model->whereIn('id', $ids)->update($update);
    }

    public function creditList($search_arr, $column =['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try {
            $this->pushCriteria(new WhereEqualCriteria($search_arr,['type_id', 'publish_status', 'credit_type']));
            $this->pushCriteria(new \App\Criteria\Agent\KeywordCriteria($search_arr,['service_item']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $this->with(['enterprise:id,name', 'agentType:id,name', 'credit' => function ($query) use ($search_arr) {
                $query->select(['id','agent_id','type','created_at'])
                    ->where('type', $search_arr['credit_type'])
                    ->where('is_audit', IS_AUDIT['yes'])
                    ->first();
            }]);
            $res = $this->paginate($per_page, $column);
        } catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    public function collectionByiIds($ids)
    {
        $column = ['id', 'enc_id','enterprise_id', 'type_id', 'service_item', 'file_name', 'file_url', 'service_detail',
            'province_code','city_code', 'district_code',
            'address',
            'contact_name',
            'contact_phone',
            'publish_status',
            'remark',
            'composite_stars',
            'department_stars',
            'enterprise_stars',
            'created_at',
            'credit_type',

        ];
        if (empty($ids)) {
            return [];
        }
        $res = $this->model->select($column)->whereIn('id', $ids)->with(['enterprise', 'agentType'])->get()->toArray();
        if (empty($res)) {
            return [];
        }
        $code_arr = app(DistrictService::class)->getDistrictCode($res);

        foreach ($res as $key => &$value) {
            $value['agent_name'] = array_get($value['enterprise']??[], 'name', '');
            unset($value['enterprise']);
            $value['agent_type_name'] = array_get($value['agent_type']??[], 'name', '');
            $value['composite_stars'] = (float)$value['composite_stars'];
            $value['department_stars'] = (float)$value['department_stars'];
            $value['province_name'] = array_get($code_arr, $value['province_code'], '');
            $value['city_name'] = array_get($code_arr, $value['city_code'],'');
            $value['district_name'] = array_get($code_arr, $value['district_code'],'');
            unset($value['agent_type']);
            unset($value['enterprise_id']);

        }

        return $res;
    }

    public function getByIds($ids, $column=['*'])
    {
        return $this->model->select($column)->whereIn('id', $ids)->get()->toArray();
    }

    public function batchPublish($ids, $status)
    {
        if (empty($ids)) {
            return 0;
        }

        $where = [];
        if ($status == PUBLISH_STATUS['yes']) {
            $where[] = ['credit_type', '!=', AGENT_CREDIT_TYPE['serious']];
        }
        $res = $this->model->whereIn('id', $ids)->where($where)->update(['publish_status' => $status]);
        $tmp = ['ids' => $ids, 'subject_type_id' => ACTIVITY_SUBJECT_TYPE['agent'], 'publish_status' => $status];
        event(new BatchPublish($tmp));
        return $res;
    }

    /**
     * FUNCTION_NAME : updatePublishByEnterprise
     * author : jp
     * 指定企业 更新 上下架
     * @param $enterprise_id
     * @param $status
     * @return int
     */
    public function updatePublishByEnterprise($enterprise_id, $status)
    {
        if (empty($enterprise_id)) {
            return 0;
        }
        $where = [];
        if ($status == PUBLISH_STATUS['yes']) {
            $where[] = ['credit_type', '!=', AGENT_CREDIT_TYPE['serious']];
        }

        $res = $this->model->where('enterprise_id', $enterprise_id)->where($where)->update(['publish_status' => $status]);
        return $res;
    }

    /**
     * FUNCTION_NAME : deleteByEnterprise
     * author : jp
     * 通过企业删除
     * @param $enterprise_id
     * @return int
     */
    public function deleteByEnterprise($enterprise_id)
    {
        if (empty($enterprise_id)) {
            return 0;
        }

        $res = $this->model->where('enterprise_id', $enterprise_id)->delete();
        return $res;
    }

    public function hasByType($type_id)
    {
        return $this->model->where('type_id',$type_id)->count();
    }

}