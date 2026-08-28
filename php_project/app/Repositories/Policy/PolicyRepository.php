<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories\Policy;


use App\Common\Code;
use App\Criteria\KeywordCriteria;
use App\Criteria\OrderByCriteria;
use App\Criteria\Policy\DistrictAllCriteria;
use App\Criteria\Policy\WhereCreatedEndCriteria;
use App\Criteria\Policy\WhereCreatedStartCriteria;
use App\Criteria\Policy\WhereEdateEltCriteria;
use App\Criteria\Policy\WhereEdateGtCriteria;
use App\Criteria\Policy\WhereEqualExpiredCriteria;
use App\Criteria\Policy\WhereExpiredCriteria;
use App\Criteria\Policy\WhereSdateEltCriteria;
use App\Criteria\Policy\WhereSdateGtCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Criteria\WhereInCriteria;
use App\Criteria\WhereLikeCriteria;
use App\Events\PolicyBatchDelete;
use App\Exceptions\QueryException;
use App\Models\PolicyModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;
use App\Support\Collection;

class PolicyRepository extends BaseRepository
{
    use CommonRepository;

    /**** 迁移政策***/
    // 这里知己取出不用做处理的部分
    protected  $policyOnly = [
        'enc_id',
        'code',
        'obj_type',
        'name',
        'doc_num',
        'pub_time',
        'validity_sdate',
        'validity_edate',
        'publish_status',
    ];
    // 详情的字段
    protected $detailColumn = [
        'source' => 'src',
        'source_web' => 'src_web',
        'source_url' => 'src_site',
    ];

    // 处理直辖市的
    protected $municipality = [110000,120000,310000,500000];
    protected $city = [110000=>110100,120000=>120100,310000=>310100,500000=>50100];

    /**** 迁移政策***/


    public function model()
    {
        return PolicyModel::class;
    }

    public function list($search_arr)
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new WhereEqualCriteria($search_arr, ['obj_type','province_code','city_code','district_code','publish_status']));
            $this->pushCriteria(new WhereInCriteria($search_arr,['id']));
            $this->pushCriteria(new WhereCreatedStartCriteria($search_arr, 'start_time'));
            $this->pushCriteria(new WhereCreatedEndCriteria($search_arr, 'end_time'));

            $this->pushCriteria(new WhereExpiredCriteria($search_arr, 'expired'));

            $this->pushCriteria(new WhereEdateEltCriteria($search_arr, 'edate_elt'));
            $this->pushCriteria(new WhereEdateGtCriteria($search_arr, 'edate_gt'));

            $this->pushCriteria(new WhereSdateGtCriteria($search_arr, 'sdate_gt'));
            $this->pushCriteria(new WhereSdateEltCriteria($search_arr, 'sdate_elt'));

            $this->pushCriteria(new WhereLikeCriteria($search_arr, ['name']));
            $this->pushCriteria(new KeywordCriteria($search_arr, ['name']));
            $this->pushCriteria(new OrderByCriteria($search_arr));

            // 特殊处理下过期过期不能 包含为设置结束时间的数据
//            $this->pushCriteria(new WhereEqualExpiredCriteria($search_arr, 'expired'));

            $this->with(['govAgen','unscramble']);
            if (isset($search_arr['obj_type']) && $search_arr['obj_type'] == OBJ_TYPE['announce']) {
                $this->with(['project']);

            }
            $res = $this->paginate($per_page);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);

    }

    public function getByEncId($enc_id, $column = ['*'])
    {
        $data = $this->model->select($column)->where('enc_id', $enc_id)->first();

        return empty($data) ? [] : $data->toArray();
    }

    public function getByLikeCode($data, $column = ['*'])
    {
        $data = $this->model->select($column)->where('code', 'like', $data.'%')->orderBy('id', 'desc')->first();
        return empty($data) ? [] : $data->toArray();
    }

    public function getByIds($ids, $column = ['*'])
    {
        return $this->model->select($column)->whereIn('id', $ids)->get()->toArray();
    }

    public function macroDetail($id)
    {
        $data = $this->model
            ->with(['govAgen','industry', 'summarize', 'relationPolicy','relationPolicyReverse', 'file','staff', 'unscramble'])
            ->find($id);

        return empty($data) ? [] : $data->toArray();
    }

    public function supPolicyDetail($id)
    {
        $data = $this->model->with(['govAgen','industry', 'summarize', 'conclusion','item','relationPolicy','relationPolicyReverse', 'file','staff','unscramble'])->find($id);

        return empty($data) ? [] : $data->toArray();
    }

    public function impleReguDetail($id)
    {
        $data = $this->model->with(['govAgen','industry', 'summarize', 'conclusion','item','relationPolicy','relationPolicyReverse', 'file','staff','unscramble'])->find($id);

        return empty($data) ? [] : $data->toArray();
    }

    public function announceDetail($id)
    {
        $data = $this->model->with(['govAgen', 'mold','relationPolicy','relationPolicyReverse', 'file','staff','unscramble'])->find($id);

        return empty($data) ? [] : $data->toArray();
    }

    public function publicityDetail($id)
    {
        $data = $this->model->with(['govAgen','staff'])->find($id);

        return empty($data) ? [] : $data->toArray();
    }

    public function deleteBatch($ids)
    {
        // 批量删除手动触发事件
//        event(new PolicyBatchDelete($ids));
        $this->model->destroy($ids);
    }

    /**
     * FUNCTION_NAME : getIndexNewPolicy
     * author : jp
     * 首页最新政策列表
     * @param $search_arr
     * @param array $column
     * @return array
     * @throws QueryException
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getIndexNewPolicy($search_arr, $column = ['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new WhereEqualCriteria($search_arr, ['publish_status']));
            $this->pushCriteria(new WhereInCriteria($search_arr,['obj_type']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $res = $this->paginate($per_page, $column);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    /**
     * FUNCTION_NAME : search
     * author : jp
     * 客户端
     * @param $search_arr
     * @param array $column
     * @return array
     * @throws QueryException
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function search($search_arr, $column = ['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new WhereEqualCriteria($search_arr, ['province_code','city_code','district_code','publish_status']));
            $this->pushCriteria(new WhereInCriteria($search_arr,['obj_type']));
            $this->pushCriteria(new KeywordCriteria($search_arr,['name','content']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $this->pushCriteria(new DistrictAllCriteria($search_arr, 'district_all'));
            $this->with(['industry' => function ($query) {
                $query->select(['policy_id','first_industry_id']);
            }, 'govAgen' => function ($query) {
                $query->select(['policy_id', 'gov_agen_first', 'gov_agen_second']);
            }]);

            // 行业 查询
            if (isset($search_arr['industry']) && !empty($search_arr['industry'])) {
                $industryWhere = explode(',', $search_arr['industry']);
                $this->whereHas('industry', function ($query) use ($industryWhere) {
                    $query->whereIn('first_industry_id', $industryWhere);
                });
            }
            $res = $this->paginate($per_page, $column);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }


    public function detailByEncId($enc_id, $column = ['*'])
    {
        $res = $this->model
            ->select($column)
            ->where('enc_id', $enc_id)
            ->where('publish_status', PUBLISH_STATUS['yes'])
            ->with(['govAgen','industry', 'summarize', 'relationPolicy','relationPolicyReverse', 'file','item','conclusion'])
            ->with(['unscramble' => function($query) {
                $query->where('publish_status', PUBLISH_STATUS['yes']);
            }]);

        $user_id = (int)getLoginHome('id');
        if (!empty($user_id)) {
            // 这里只有宏观政策
            $res = $res->withCount(['collections' => function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->whereRaw('obj_type = '. PolicyModel::TABLE_NAME.'.obj_type');

            }]);
        }

        $data = $res->first();
        return empty($data) ? [] : $data->toArray();
    }

    public function publicityList($search_arr,$column = ['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new WhereEqualCriteria($search_arr, ['province_code','city_code','district_code','publish_status']));
            $this->pushCriteria(new WhereInCriteria($search_arr,['obj_type']));
            $this->pushCriteria(new KeywordCriteria($search_arr,['name','content']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $res = $this->paginate($per_page, $column);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    public function announceDetailByEncId($enc_id, $column=['*'])
    {
        $res = $this->model
            ->select($column)
            ->where('enc_id', $enc_id)
            ->where('publish_status', PUBLISH_STATUS['yes'])
            ->with(['govAgen','industry', 'relationPolicy','relationPolicyReverse', 'file'])
            ->with(['unscramble' => function($query) {
                $query->where('publish_status', PUBLISH_STATUS['yes']);
            }])
            ->with(['project' => function ($query) {
                $query->where('publish_status', PUBLISH_STATUS['yes']);
            }]);

        $user_id = (int)getLoginHome('id');
        if (!empty($user_id)) {
            $res = $res->withCount(['collections' => function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('obj_type', OBJ_TYPE['announce']);
            }]);
        }

        $data = $res->first();
        return empty($data) ? [] : $data->toArray();
    }

    public function publicityDetailByEncId($enc_id, $column=['*'])
    {
        $res = $this->model
            ->select($column)
            ->where('enc_id', $enc_id)
            ->where('publish_status', PUBLISH_STATUS['yes'])
            ->with(['govAgen', 'file']);
        $user_id = (int)getLoginHome('id');
        if (!empty($user_id)) {
            $res = $res->withCount(['collections' => function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('obj_type', OBJ_TYPE['publicity']);
            }]);
        }
        $data = $res->first();
        return empty($data) ? [] : $data->toArray();
    }

    public function approvalDetailByEncId($enc_id, $column=['*'])
    {
        $res = $this->model
            ->select($column)
            ->where('enc_id', $enc_id)
            ->where('publish_status', PUBLISH_STATUS['yes']);
        $user_id = (int)getLoginHome('id');
        if (!empty($user_id)) {
            $res = $res->withCount(['collections' => function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('obj_type', OBJ_TYPE['approval']);
            }]);
        }
        $data = $res->first();
        return empty($data) ? [] : $data->toArray();
    }

    public function detailById($id, $column=['*'])
    {
        $res = $this->model->select($column)->find($id);
        return empty($res) ? [] : $res->toArray();
    }

    public function getPolicyForUnscramble($search_arr, $column = ['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new WhereInCriteria($search_arr,['obj_type']));
            $this->pushCriteria(new WhereLikeCriteria($search_arr, ['name']));
            $this->pushCriteria(new OrderByCriteria($search_arr));

            // TODO 这里加上with 后才能过滤掉 关联了解读的政策
            $this->with(['unscramble']);
            $this->model->doesntHave('unscramble');
            $res = $this->paginate($per_page, $column);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }


    /**
     * FUNCTION_NAME : migrate
     * author : jp
     * 迁移数据
     * @param $data
     * @throws QueryException
     */
    public function migrate(&$data)
    {
        $policy = Collection::filter($this->policyOnly, $data);
        $policy['original_policy_id'] = $data['policy_id'];
        $policy['content'] = array_get($data,'text', '');
        $detail = array_get($data, 'detail', []);
        if (!empty($detail)) {
            foreach ($this->detailColumn as $kd => $vd) {
                $policy[$kd] = array_get($detail, $vd, '');
            }
        }

        // 处理地区 110000000000
        //         110000
        if (in_array($data['province_code'],$this->municipality)) {
            $policy['city_code'] = $this->city[$data['province_code']] * 1000000;
            $policy['district_code'] = $data['city_code'] * 1000000;
        } else {
            $policy['city_code'] = $data['city_code'] * 1000000;
            $policy['district_code'] = $data['district_code'] * 1000000;
        }
        $policy['province_code'] = $data['province_code'] * 1000000;

        $govKey = [
            'gov_agen_first',
            'gov_agen_second',
        ];
        $gov = [];
        if (!empty($data['gov_agen'])) {
            $gov = array_map(function ($item) use ($govKey) {
                return Collection::filter($govKey, $item);
            }, array_get($data,'gov_agen', []));
        }
        $conclusion = [];
        if (!empty($data['conclusion'])) {
            $conclusion['conclusion'] = $data['conclusion']['conclusion'];
        }

        $item = [];
        if (!empty($data['item'])) {
            foreach (array_get($data,'item', []) as $ki => $vi) {
                $item[] = [
                    'content' => $vi['text']
                ];
            }
//            $item = array_map(function ($item) {
//                return Collection::filter(['content'], $item);
//            }, array_get($data,'item', []));
        }
        $newModel = $this->storeRepository($policy);
        if (!empty($gov)) {
            $newModel->govAgen()->createMany($gov);
        }
        if (!empty($item)) {
            $newModel->item()->createMany($item);
        }
        if (!empty($conclusion)) {
            $newModel->conclusion()->create($conclusion);
        }

        $this->resetModel();
        // 删除大变量
        unset($data);
        unset($policy);
        unset($item);
        unset($gov);
        unset($conclusion);
    }

    /**
     * FUNCTION_NAME : originalLast
     * author : jp
     * 取出最后一个policy_id
     * @return int
     */
    public function originalLast()
    {
        $res = $this->model->orderBy('original_policy_id', 'DESC')->first(['original_policy_id']);
        return empty($res) ? 0 : $res->toArray()['original_policy_id'];
    }

    /**
     * FUNCTION_NAME : getPolicyByTarget
     * author : jp
     * 获取拨款的
     * @param $target
     * @param array $column
     * @return mixed
     */
    public function getPolicyByTarget($target, $column = ['*'])
    {
        return $this->model->select($column)->whereIn('target_id', $target)->get()->toArray();
    }

}