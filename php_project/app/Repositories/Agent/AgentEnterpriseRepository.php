<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/12/9
 * Time: 15:09
 */

namespace App\Repositories\Agent;


use App\Common\Code;
use App\Criteria\AgentEnterprise\HasAgentCriteria;
use App\Criteria\AgentEnterprise\KeywordCriteria;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Exceptions\QueryException;
use App\Models\EnterpriseModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class AgentEnterpriseRepository extends BaseRepository
{
    use CommonRepository;

    public function model()
    {
        return EnterpriseModel::class;
    }

    public function list($search_arr, $column = ['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try {
            $this->pushCriteria(new KeywordCriteria($search_arr,['name']));
            $this->pushCriteria(new HasAgentCriteria($search_arr));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $this->with(['agent:id,enterprise_id,type_id,credit_type,publish_status', 'user:name,mobile']);
            $res = $this->paginate($per_page, $column);
        } catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        $data = page($res,$current_page);
        if (empty($data['data'])) {
            return $data;
        }

        $type_ids = [];
        foreach ($data['data'] as $k => $v) {
            $type_ids = array_merge($type_ids, array_column($v['agent']??[], 'type_id'));
        }
        $type_ids = array_filter(array_unique($type_ids));

        // 查询出类型
        $types = app(AgentTypeRepository::class)->getByIds($type_ids, ['id','name']);
        $types = array_column($types, 'name', 'id');
        foreach ($data['data'] as $k => $v) {
            $tmp = [];
            $type_ids = array_merge($type_ids, array_column($v['agent']??[], 'type_id'));
            $tmpStatus = PUBLISH_STATUS['no'];
            foreach ($v['agent']??[] as $ka => $va) {
                $tmpName = array_get($types, $va['type_id']);
                if (!empty($tmpName)) {
                    $tmp[] = $tmpName;
                }
                if (array_get($va, 'publish_status', '') == PUBLISH_STATUS['yes']) {
                    $tmpStatus = PUBLISH_STATUS['yes'];
                }
            }
            $v['type_name'] = implode('、', $tmp);
            $v['user_name'] = array_get($v['user'][0]??[], 'name', '');
            $v['user_mobile'] = array_get($v['user'][0]??[], 'mobile', '');
            $v['publish_status'] = $tmpStatus;
            unset($v['agent']);
            unset($v['user']);
            $data['data'][$k] = $v;
        }

        return $data;
    }
}