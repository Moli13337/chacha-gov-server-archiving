<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/13
 * Time: 18:27
 */

namespace App\Http\Controllers\Service;


use App\Events\FileChange;
use App\Events\GovAgenChange;
use App\Events\IndustryChange;
use App\Events\PolicyRelation;
use App\Models\PolicyModel;
use App\Repositories\IndustryRepository;
use App\Repositories\Policy\BigDataRepository;
use App\Repositories\Policy\GovAgenRepository;
use App\Repositories\Policy\MoldRepository;
use App\Repositories\Policy\PolicyConclusionRepository;
use App\Repositories\Policy\PolicyFileRepository;
use App\Repositories\Policy\PolicyGovAgenRepository;
use App\Repositories\Policy\PolicyIndustryRepository;
use App\Repositories\Policy\PolicyItemRepository;
use App\Repositories\Policy\PolicyMoldRepository;
use App\Repositories\Policy\PolicyRelationRepository;
use App\Repositories\Policy\PolicyRepository;
use App\Repositories\Policy\PolicySummarizeRepository;
use App\Support\Collection;
use Illuminate\Support\Arr;
use Xkd\Location\Location;

class PolicyService extends BaseService
{

    protected $policyRepository;
    protected $policyIndustryRepository;
    protected $policySummarizeRepository;
    protected $policyFileRepository;
    protected $policyRelationRepository;
    protected $govAgenRepository;
    protected $policyGovAgenRepository;
    protected $policyConclusionRepository;
    protected $policyItemRepository;
    protected $policyMoldRepository;
    protected $moldRepository;
    protected $districtService;
    protected $industryService;
    protected $summarizeService;

    public function __construct(PolicyRepository $policyRepository,
                                PolicyIndustryRepository $policyIndustryRepository,
                                PolicySummarizeRepository $policySummarizeRepository,
                                PolicyFileRepository $policyFileRepository,
                                PolicyRelationRepository $policyRelationRepository,
                                PolicyGovAgenRepository $policyGovAgenRepository,
                                GovAgenRepository $govAgenRepository,
                                PolicyConclusionRepository $policyConclusionRepository,
                                PolicyItemRepository $policyItemRepository,
                                PolicyMoldRepository $policyMoldRepository,
                                MoldRepository $moldRepository,
                                DistrictService $districtService,
                                IndustryService $industryService, SummarizeService $summarizeService)
    {
        $this->policyRepository = $policyRepository;
        $this->policyIndustryRepository = $policyIndustryRepository;
        $this->policySummarizeRepository = $policySummarizeRepository;
        $this->policyFileRepository = $policyFileRepository;
        $this->policyRelationRepository = $policyRelationRepository;
        $this->govAgenRepository = $govAgenRepository;
        $this->policyGovAgenRepository = $policyGovAgenRepository;
        $this->policyConclusionRepository = $policyConclusionRepository;
        $this->policyItemRepository = $policyItemRepository;
        $this->policyMoldRepository = $policyMoldRepository;
        $this->moldRepository = $moldRepository;
        $this->districtService = $districtService;
        $this->industryService = $industryService;
        $this->summarizeService = $summarizeService;
    }

    public function store($data)
    {
        $white = [
            'obj_type',
            'name',
            'doc_num',
            'content',
            'content_name',
            'content_url',
            'pub_time',
            'province_code',
            'city_code',
            'district_code',
            'validity_sdate',
            'validity_edate',
            'publish_status',
            'source',
            'source_web',
            'source_url',
            'is_handel',
            'big_data_id',
        	'target_id'
        ];
        $data = Collection::filter($white, $data);
        $data = $this->districtService->formatDistrict($data);
        $data['enc_id'] = $this->getEncId();
        $data['code'] = $this->createCode();
        $data['created_staff_id'] = getLoginStaff('id');

        // 处理big_data_id
        if(isset($data['big_data_id']) && empty($data['big_data_id'])) {
            $data['big_data_id'] = 0;
        }

        $res = $this->policyRepository->storeRepository($data);

        if (!empty($data['big_data_id'])) {
            $big_data_update = [
                'id' => $data['big_data_id'],
                'is_handle' => BIG_DATA_HANDLE['yes'],
            ];
            app(BigDataRepository::class)->updateRepository($big_data_update);
        }
        return $res;
    }

    public function update($data)
    {
        $white = [
            'id',
            'obj_type',
            'name',
            'doc_num',
            'content',
            'content_name',
            'content_url',
            'pub_time',
            'province_code',
            'city_code',
            'district_code',
            'validity_sdate',
            'validity_edate',
            'publish_status',
            'source',
            'source_web',
            'source_url',
            'is_handel',
            'big_data_id'
        ];

        $data = Collection::filter($white, $data);
        $data = $this->districtService->formatDistrict($data);
        $initTime = [
            'pub_time' => 0,
            'validity_sdate' => 0,
            'validity_edate' => 0,
        ];

        foreach ($initTime as $k => $v) {
            if (empty($data[$k])) {
                $data[$k] = $v;
            }
        }


        $res = $this->policyRepository->updateRepository($data);
        return $res;
    }

    public function delete($id)
    {
        $res = $this->policyRepository->deleteRepository($id);
        return $res;
    }

    public function deleteBatch($ids)
    {
        $res = $this->policyRepository->deleteBatch($ids);
        return $res;
    }

    public function updatePublish($data)
    {
        $res = $this->policyRepository->updateRepository($data);
        return $res;
    }

    public function list($params)
    {
        // 处理发文体系
        $gov_where = [];
        if (!empty($params['gov_agen_first'])) {
            $gov_where['gov_agen_first'] = $params['gov_agen_first'];
        }

        if (!empty($params['gov_agen_second'])) {
            $gov_where['gov_agen_second'] = $params['gov_agen_second'];
        }

        if (!empty($gov_where)) {
            $policy_ids = $this->policyGovAgenRepository->getPolicy($gov_where, ['policy_id']);

            if (empty($policy_ids)) {
                return buildEmptyPage();
            }
            $params['id'] = array_column($policy_ids, 'policy_id');
        }

//        // 处理过期
//        if (isset($params['expired']) && !blank($params['expired']) && $params['expired'] == EXPIRED['no']) {
//            $params['edate_gt'] = time();
//        } elseif (isset($params['expired']) && $params['expired'] == EXPIRED['yes']) {
//            $params['edate_elt'] = time();
//        }

        // 处理排序
        if (!empty($params['sort_pub']) && $params['sort_pub'] == SORT_PUB['desc']) {
            $params['order_by'] = [
                'pub_time' => 'DESC'
            ];
        } elseif (!empty($params['sort_pub']) && $params['sort_pub'] == SORT_PUB['asc']) {
            $params['order_by'] = [
                'pub_time' => 'ASC'
            ];
        }
        if (!empty($params['sort']) && $params['sort'] == SORT['asc']) {
            $params['order_by']['id'] = 'ASC';
        } else {
            $params['order_by']['id'] = 'DESC';
        }

        $data = $this->policyRepository->list($params);
        if (empty($data['data'])) {
            return $data;
        }

        // 发文体系
        $gov_name = $this->getGovAgenName($data['data']);

        // 地区
        $code_arr = $this->districtService->getDistrictCode($data['data']);

        foreach ($data['data'] as $key => &$val) {
            $val['province_name'] = array_get($code_arr, $val['province_code'], '');
            $val['city_name'] = array_get($code_arr, $val['city_code'],'');
            $val['district_name'] = array_get($code_arr, $val['district_code'],'');

            if (!empty($gov_name) && !empty($val['gov_agen'])) {
                foreach ($val['gov_agen'] as $kg => &$vg) {
                    $vg['gov_agen_first_name'] = array_get($gov_name, $vg['gov_agen_first'], '');
                    $vg['gov_agen_second_name'] = array_get($gov_name, $vg['gov_agen_second'], '');
                }
            }
        }

        return $data;
    }

    public function getGovAgenName($data)
    {
        // 发文体系
        $gov_ids = [];
        foreach ($data as $key => $value) {
            $tmp = array_column($value['gov_agen'],'gov_agen_first');
            $tmp = array_merge($tmp,array_column($value['gov_agen'],'gov_agen_second'));
            $gov_ids = array_merge($gov_ids, $tmp);
        }
        $gov_ids = array_unique(array_filter($gov_ids));
        $gov_name = $this->govAgenRepository->getByIds($gov_ids,['id', 'gov_agen_name']);
        $gov_name = array_column($gov_name, 'gov_agen_name', 'id');
        return $gov_name;
    }

    public function macroDetail($id)
    {
        $data = $this->policyRepository->macroDetail($id);

        if (empty($data)) {
            return $data;
        }

        $data = array_merge($data, $this->districtService->getDistrictName($data));
        $data['industry'] = $this->industryService->getIndustryNameList($data['industry']??[]);

        $relation = $this->relationDetail($data, $id, $data['obj_type']);

        unset($data['relation_policy']);
        unset($data['relation_policy_reverse']);

        $data['summarize'] = $this->summarizeService->getSummarize($data['id'], $data['summarize']??[]);
        $data = array_merge($data, $relation);
        $data['gov_agen'] = $this->govAgenDetail($data['gov_agen']);

        return $data;
    }


    public function supPolicyDetail($id)
    {
        $data = $this->policyRepository->supPolicyDetail($id);

        if (empty($data)) {
            return $data;
        }
        $data = array_merge($data, $this->districtService->getDistrictName($data));
        $data['industry'] = $this->industryService->getIndustryNameList($data['industry']??[]);

        $relation = $this->relationDetail($data, $id, $data['obj_type']);

        unset($data['relation_policy']);
        unset($data['relation_policy_reverse']);
        $data['summarize'] = $this->summarizeService->getSummarize($data['id'], $data['summarize']??[]);
        $data = array_merge($data, $relation);
        $data['gov_agen'] = $this->govAgenDetail($data['gov_agen']);

        return $data;
    }

    public function impleReguDetail($id)
    {
        $data = $this->policyRepository->impleReguDetail($id);

        if (empty($data)) {
            return $data;
        }

        $data = array_merge($data, $this->districtService->getDistrictName($data));
        $data['industry'] = $this->industryService->getIndustryNameList($data['industry']??[]);

        $relation = $this->relationDetail($data, $id, $data['obj_type']);

        unset($data['relation_policy']);
        unset($data['relation_policy_reverse']);
        $data['summarize'] = $this->summarizeService->getSummarize($data['id'], $data['summarize']??[]);
        $data = array_merge($data, $relation);
        $data['gov_agen'] = $this->govAgenDetail($data['gov_agen']);

        return $data;
    }

    public function announceDetail($id)
    {
        $data = $this->policyRepository->announceDetail($id);

        if (empty($data)) {
            return $data;
        }

        $data = array_merge($data, $this->districtService->getDistrictName($data));

        if (empty($data['unscramble'])) {

        }

        $data['unscramble'] = (object) ($data['unscramble'][0] ??[]);

        $relation = $this->relationDetail($data, $id, $data['obj_type']);

        unset($data['relation_policy']);
        unset($data['relation_policy_reverse']);

        $data = array_merge($data, $relation);
        $data['gov_agen'] = $this->govAgenDetail($data['gov_agen']);

        $data['mold'] = $this->moldDetail($data['mold']);
        return $data;
    }


    public function publicityDetail($id)
    {
        $data = $this->policyRepository->publicityDetail($id);
        $data = array_merge($data, $this->districtService->getDistrictName($data));
        $data['gov_agen'] = $this->govAgenDetail($data['gov_agen']);

        return $data;
    }

    public function relationIndustryInsert($data, $policy_id)
    {
        $white = $this->industryService->industryItem;
        foreach ($data as $key => &$v) {
            $v = Collection::filter($white, $v);
            $v = $this->industryService->initIndustry($v);
            $v['policy_id'] = $policy_id;
            $v = array_merge($v, returnCreatedUpdatedAt());
        }
        $this->policyIndustryRepository->storeBatch($data);

    }

    public function conclusionInsert($data, $policy_id)
    {
        $white = [
            'conclusion',
        ];
        $data = Collection::filter($white, $data);
        $data['policy_id'] = $policy_id;

        $this->policyConclusionRepository->storeRepository($data);
    }

    public function itemInsert($data, $policy_id)
    {
        // TODO 下一版需要增加 两个条款到 policy

        $white = [
            'content',
        ];
        foreach ($data as $key => &$v) {
            $v = Collection::filter($white, $v);
            $v['policy_id'] = $policy_id;
            $v = array_merge($v, returnCreatedUpdatedAt());
        }
        $this->policyItemRepository->storeBatch($data);
    }

    public function moldInsert($data, $policy_id)
    {
        $white = [
            'mold_id',
        ];
        $data = Collection::filter($white, $data);
        $data['policy_id'] = $policy_id;

        $this->policyMoldRepository->storeRepository($data);
    }

    public function govAgenInsert($data, $policy_id)
    {
        $white = [
            'gov_agen_first',
            'gov_agen_second',
        ];
        foreach ($data as $key => &$v) {
            $v = Collection::filter($white, $v);
            $v['policy_id'] = $policy_id;
            $v = array_merge($v, returnCreatedUpdatedAt());
        }
        $this->policyGovAgenRepository->storeBatch($data);
    }


    public function relationFileInsert($data, $policy_id)
    {
        $white = [
            'name',
            'save_url'
        ];
        foreach ($data as $key => &$v) {
            $v = Collection::filter($white, $v);
            $v['policy_id'] = $policy_id;
            $v = array_merge($v, returnCreatedUpdatedAt());
        }

        $this->policyFileRepository->storeBatch($data);
    }

    public function relationRelationInsert($data, $obj_id, $obj_type)
    {
        $white = [
            'obj_type_relation_id',
            'type'
        ];
        foreach ($data as $key => &$v) {
            $v = Collection::filter($white, $v);
            $v['obj_id'] = $obj_id;
            $v['obj_type'] = $obj_type;
            $v = array_merge($v, returnCreatedUpdatedAt());
        }

        $this->policyRelationRepository->storeBatch($data);
    }

    private function getEncId(){
        $enc_id = substr(md5(time().rand()), 0, 20);

        $data = $this->policyRepository->getByEncId($enc_id, ['id']);

        if (!empty($data)) {
            return $this->getEncId();
        }
        return $enc_id;
    }

    private function createCode(){
        $prefix = date('Ymd');
        $code_data = $this->policyRepository->getByLikeCode($prefix);
        $code = empty($code_data) ? 1 : substr($code_data['code'], 8) + 1;
        return $prefix.str_pad($code, 6, 0, STR_PAD_LEFT);
    }

    /**
     * FUNCTION_NAME : relationDetail
     * author : jp
     * 政策关联详情
     * @param $data
     * @param $obj_id
     * @param $obj_type
     * @return array
     */
    public function relationDetail($data, $obj_id, $obj_type)
    {
        $tmp1  = empty($data['relation_policy']) ? [] : $data['relation_policy'];
        $tmp2 = empty($data['relation_policy_reverse']) ? [] : $data['relation_policy_reverse'];
        $out_data = array_merge($tmp1, $tmp2);

        $re_type = $this->typeHasRelation($obj_type);

        if (empty($re_type)) {
            return [];
        }

        $relation = [];

        foreach ($re_type as $kr => $vr) {
            $relation[$vr] = [];
        }

        if (empty($out_data)) {
            return $relation;
        }

        $base_relation_key = [
            OBJ_TYPE['macro_policy'] => 'macro_policy_relation',
            OBJ_TYPE['sup_policy'] => 'sup_policy_relation',
            OBJ_TYPE['imple_regu'] => 'imple_regu_relation',
            OBJ_TYPE['announce'] => 'announce_relation',
            OBJ_TYPE['publicity'] => 'publicity_relation',
        ];

        $policy_ids = array_column($out_data, 'obj_type_relation_id');
        $policy_ids = array_unique(array_merge($policy_ids, array_column($out_data, 'obj_id')));

        $name_arr = $this->policyRepository->getByIds($policy_ids);

        $names = array_column($name_arr, 'name', 'id');

        // 关联关系变成双向的时候要注意 交换自己和关联关系的位置
        foreach ($out_data AS $key=>$value) {
            if ($out_data[$key]['obj_type_relation_id'] == $obj_id) {
                $tmp = $out_data[$key]['obj_type_relation_id'];
                $out_data[$key]['obj_type_relation_id'] = $out_data[$key]['obj_id'];
                $out_data[$key]['obj_id'] = $tmp;

                $tmp = $out_data[$key]['obj_type'];
                $out_data[$key]['obj_type'] = $out_data[$key]['type'];
                $out_data[$key]['type'] = $tmp;
            }

            $target_key = array_get($base_relation_key, $out_data[$key]['type'], '');

            if ($target_key && !empty($names[$out_data[$key]['obj_type_relation_id']])) {
                $out_data[$key]['name'] = $names[$out_data[$key]['obj_type_relation_id']];
                $relation[$target_key][] = $out_data[$key];
            }
        }

        return $relation;

    }

    /**
     * FUNCTION_NAME : typeHasRelation
     * author : jp
     * 当前政策对象的 能关联的政策类型
     * @param $obj_type
     * @return array
     */
    public function typeHasRelation($obj_type)
    {
        $arr = [];
        switch ($obj_type) {
            case OBJ_TYPE['macro_policy']:
                $arr = ['sup_policy_relation', 'announce_relation','macro_policy_relation'];
                break;
            case OBJ_TYPE['sup_policy']:
                $arr = ['macro_policy_relation', 'imple_regu_relation','announce_relation', 'publicity_relation'];
                break;
            case OBJ_TYPE['imple_regu']:
                $arr = ['sup_policy_relation', 'announce_relation','publicity_relation'];
                break;
            case OBJ_TYPE['announce']:
                $arr = ['macro_policy_relation','sup_policy_relation', 'imple_regu_relation','publicity_relation'];
                break;
            default:
                break;
        }

        return $arr;
    }

    /**
     * FUNCTION_NAME : govAgenDetail
     * author : jp
     * 发文体系
     * @param $data
     * @return array
     */
    public function govAgenDetail($data)
    {
        if (empty($data)) {
            return [];
        }

        $first = array_column($data, 'gov_agen_first');
        $ids = array_unique(array_merge($first, array_column($data, 'gov_agen_second')));
        $names = $this->govAgenRepository->getByIds($ids, ['id','gov_agen_name']);
        $names = array_column($names, 'gov_agen_name', 'id');

        foreach ($data as $key => &$value) {
            $value['gov_agen_first_name'] = $names[$value['gov_agen_first']];
            $value['gov_agen_second_name'] = $names[$value['gov_agen_second']];
        }

        return $data;
    }

    public function moldDetail($data)
    {
        if (empty($data)) {
            return [];
        }

        $mold = $this->moldRepository->findRepository($data['mold_id']);
        $data['name'] = $mold['name'];
        return $data;
    }

    public function relationIndustryUpdate($data, $policy_id, $obj_type)
    {
        // 这里需要对行业进行更细致的判断， 便于区分是否编辑
        $have = $this->policyIndustryRepository->list($policy_id);
        $industryItem = $this->industryService->industryItem;
        $haveArr = array_map(function ($item) use ($industryItem) {
             return implode('-', array_only($item, $industryItem));
        }, $have);
        $res = $this->policyIndustryRepository->deleteByPolicyId($policy_id);
        $flag = false;
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                if (!in_array(implode('-', array_only($v, $industryItem)), $haveArr)) {
                    $flag = true;
                    break;
                }
            }

            $res = $this->relationIndustryInsert($data, $policy_id);
        }

        if (count($have) != count($data) || $flag) {
            $params = [
                'type' => ACTIVITY_TYPE['updated'],
                'subject_id' => $policy_id,
                'subject_type_id' => $obj_type,
                'properties' => json_encode(['attributes' => $data, 'old' => $have]),
            ];
            event(new IndustryChange($params));
        }
        return $res;
    }


    public function conclusionUpdate($data, $policy_id)
    {
        $white = [
            'conclusion'
        ];

        $update = Collection::filter($white, $data);
        $this->policyConclusionRepository->updateByPolicyId($update,$policy_id);
    }

    public function moldUpdate($data, $policy_id)
    {
        $white = [
            'mold_id'
        ];

        $update = Collection::filter($white, $data);
        if (empty($update)) {
            $update = ['mold_id' => 0];
            $this->policyMoldRepository->deleteByPolicy($policy_id);
        }

        $where = ['policy_id' =>  $policy_id];
        $this->policyMoldRepository->customUpdateOrCreate($where,$update);
//        $this->policyMoldRepository->updateByPolicyId($update, $policy_id);
    }

    public function govAgenUpdate($data, $policy_id, $obj_type)
    {
        $gov_key = ['gov_agen_first','gov_agen_second'];
        $have = $this->policyGovAgenRepository->getPolicy(['policy_id' => $policy_id], $gov_key);
        $haveArr = array_map(function ($item) use ($gov_key) {
            return implode('-', array_only($item, $gov_key));
        }, $have);

        $res = $this->policyGovAgenRepository->deleteByPolicyId($policy_id);
        $flag = false;
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                if (!in_array(implode('-', array_only($v, $gov_key)), $haveArr)) {
                    $flag = true;
                    break;
                }
            }
            $res = $this->govAgenInsert($data, $policy_id);
        }
        if (count($have) != count($data) || $flag) {
            $params = [
                'type' => ACTIVITY_TYPE['updated'],
                'subject_id' => $policy_id,
                'subject_type_id' => $obj_type,
                'properties' => json_encode(['attributes' => $data, 'old' => $have]),
            ];
            event(new GovAgenChange($params));
        }
        return $res;
    }

    public function relationFileUpdate($data, $policy_id, $obj_type)
    {
        $have = $this->policyFileRepository->getByPolicy(['policy_id' => $policy_id], ['save_url']);
        $haveArr = array_column($have, 'save_url');
        $res = $this->policyFileRepository->deleteByPolicyId($policy_id);
        $flag = false;
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                if (!in_array($v['save_url'], $haveArr)) {
                    $flag = true;
                    break;
                }
            }
            $res = $this->relationFileInsert($data, $policy_id);
        }
        if (count($have) != count($data) || $flag) {
            $params = [
                'type' => ACTIVITY_TYPE['updated'],
                'subject_id' => $policy_id,
                'subject_type_id' => $obj_type,
                'properties' => json_encode(['attributes' => $data, 'old' => $have]),
            ];
            event(new FileChange($params));
        }
        return $res;
    }

    public function relationRelationUpdate($data, $obj_id, $obj_type)
    {
        // 写日志
        $have = PolicyModel::select(['id'])->where('id', $obj_id)->with(['relationPolicy','relationPolicyReverse'])->get()->toArray();
        $have = $this->relationDetail($have[0]??[],$obj_id, $obj_type);
        $haveArr = [];
        $white = [
            'obj_type_relation_id',
            'type'
        ];
        $tmpHave = [];
        foreach ($have as $key => $value) {
            foreach ($value as $kv => $vv) {
                $tmpHave[] = $tmp = array_only($vv, $white);
                $haveArr[] = implode('-', $tmp);
            }
        }

        $flag = false;
        $res = $this->policyRelationRepository->deleteObjID($obj_id);
        if (!empty($data)) {
            foreach ($data as $kd => $vd) {
                if (!in_array(implode('-', array_only($vd, $white)), $haveArr)) {
                    $flag = true;
                    break;
                }
            }

            $res = $this->relationRelationInsert($data, $obj_id, $obj_type);
        }

        if (count($haveArr) != count($data) || $flag) {
            $params = [
                'type' => empty($data) ? ACTIVITY_TYPE['deleted'] : ACTIVITY_TYPE['updated'],
                'subject_id' => $obj_id,
                'subject_type_id' => $obj_type,
                'properties' => json_encode(['attributes' => $data, 'old' => $tmpHave]),
            ];
            event(new PolicyRelation($params));
        }
        return $res;
    }

    public function itemUpdate($data, $policy_id)
    {
        // TODO 下一版需要增加 两个条款到 policy

        $all = $this->policyItemRepository->getByPolicy($policy_id);

        $ids = array_column($all, 'id');
        $all = array_column($all, null, 'id');

        $update = [];
        $insert = [];
        $exist = [];

        $white = [
            'content',
        ];
        foreach ($data as $key => $v) {

            if (!empty($v['id']) && in_array($v['id'], $ids)) {
                $exist[] = $v['id'];
                foreach ($white as $kw => $vw) {
                    if ($all[$v['id']][$vw] != $v[$vw]) {
                        $tmp = Collection::filter($white, $v);
                        $tmp['id'] = $v['id'];
                        $update[] = $tmp;
                    }
                }

            } else {
                $v = Collection::filter($white, $v);
                $v['policy_id'] = $policy_id;
                $insert[] = array_merge($v, returnCreatedUpdatedAt());
            }
        }

        // 新增
        if (!empty($insert)) {
            $this->policyItemRepository->storeBatch($insert);
        }

        // 删除
        if ($diff = array_diff($ids, $exist)) {
            $this->policyItemRepository->deleteByIds($diff);
        }

        // 更新
        if (!empty($update)) {
            foreach ($update as $ku => $vu) {
                $this->policyItemRepository->updateRepository($vu);
            }
        }

    }

    /**
     * FUNCTION_NAME : detailByEncId
     * author : jp
     * 客户端政策详情
     * @param $enc_id
     * @return array
     */
    public function detailByEncId($enc_id)
    {
        $column = [
            'id',
            'enc_id',
            'obj_type',
            'name',
            'content',
            'content_name',
            'content_url',
            'province_code',
            'city_code',
            'district_code',
            'validity_sdate',
            'validity_edate',
            'publish_status',
            'created_at',
            'pub_time'
        ];
        $detail = $this->policyRepository->detailByEncId($enc_id, $column);

        if (empty($detail)) {
            return $detail;
        }

        $relation = $this->cRelationDetail($detail, $detail['id'], $detail['obj_type']);
        unset($detail['relation_policy']);
        unset($detail['relation_policy_reverse']);

//        $detail['relation_policy'] = $relation;

        $detail = array_merge($detail, $relation);

        $detail['summarize'] = $this->summarizeService->getClientSummarize($detail['id'], $detail['summarize']??[]);

        $detail['id'] = $detail['enc_id'];

        $code_arr_key = ['province_code', 'city_code', 'district_code'];

        $code_arr = array_unique(array_filter(Collection::filter($code_arr_key, $detail)));
        $code_arr = $this->districtService->getCode($code_arr);
        $detail['province_name'] = array_get($code_arr, $detail['province_code'], '');
        $detail['city_name'] = array_get($code_arr, $detail['city_code'],'');
        $detail['district_name'] = array_get($code_arr, $detail['district_code'],'');

        $detail['gov_agen'] = $this->govAgenDetail($detail['gov_agen']);
        $gov_key = [
            'gov_agen_first',
            'gov_agen_second',
            'gov_agen_first_name',
            'gov_agen_second_name',
        ];
        foreach ($detail['gov_agen'] as $key => $value) {
            $detail['gov_agen'][$key] = Collection::filter($gov_key, $value);
        }

        $unscramble = [];
        $unscramble_key = [
            'id',
            'enc_id',
            'name',
            'content_url'
        ];
        if (!empty($detail['unscramble'][0])) {
            $unscramble = Collection::filter($unscramble_key, $detail['unscramble'][0]);
            $unscramble['id'] = $unscramble['enc_id'];
        }

        $detail['unscramble'] = (object)$unscramble;

        $industry = $this->industryService->getIndustryNameList($detail['industry']??[]);
        $industry_key = [
            'first_industry_name',
            'second_industry_name',
            'third_industry_name',
            'fourth_industry_name',
        ];

        foreach ($industry as $ki => $vi) {
            $industry[$ki] = Collection::filter($industry_key, $vi);
        }
        $detail['industry'] = $industry;

        $file = $detail['file'] ?? [];

        $file_key = [
            'name',
            'save_url',
        ];
        foreach ($file as $kf => $vf) {
            $file[$kf] = Collection::filter($file_key, $vf);
        }
        $detail['file'] = $file;

        $detail['conclusion'] = empty($detail['conclusion']['conclusion']) ? '' : $detail['conclusion']['conclusion'];

        $item = [];
        $item_key = ['content'];
        if (!empty($detail['item'])) {
            foreach ($detail['item'] as $kd => $vd) {
                $item[] = Collection::filter($item_key, $vd);
                $detail['content'] .= $vd['content'];
            }
        }

        $detail['content'] .= $detail['conclusion'];

        $detail['item'] = $item;


        if ($detail['obj_type'] == OBJ_TYPE['macro_policy']) {
            unset($detail['conclusion']);
            unset($detail['item']);
        }
        return $detail;
    }

    /**
     * FUNCTION_NAME : cList
     * author : jp
     * 客户端列表
     * @param $params
     * @return array
     * @throws \App\Exceptions\QueryException
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Xkd\Location\Exceptions\ClientException
     */
    public function cList($params)
    {
        $column = [
            'id',
            'enc_id',
            'doc_num',
            'pub_time',
            'name',
            'content',
            'content_name',
            'content_url',
            'province_code',
            'city_code',
            'district_code',
            'validity_sdate',
            'validity_edate',
            'publish_status',
            'created_at'
        ];
        $data = $this->policyRepository->search($params, $column);

        if (empty($data['data'])) {
            return $data;
        }

        // 发文体系
        $gov_name = $this->getGovAgenName($data['data']);

        // 地区
        $code_arr = $this->districtService->getDistrictCode($data['data']);

        // 行业
        $industry = $this->getIndustryName($data['data']);

        $gov_key = [
            'gov_agen_first_name',
            'gov_agen_second_name',
        ];
        foreach ($data['data'] as $key => &$val) {
            $val['id'] = $val['enc_id'];
            $val['province_name'] = array_get($code_arr, $val['province_code'], '');
            $val['city_name'] = array_get($code_arr, $val['city_code'],'');
            $val['district_name'] = array_get($code_arr, $val['district_code'],'');

            $tmpIndustry = [];
            // 行业
            if (!empty($val['industry'])) {
                foreach ($val['industry'] as $ki => $vi) {
                    if (!empty($industry[$vi['first_industry_id']])) {
                        $tmpIndustry[] = $industry[$vi['first_industry_id']];
                    }
                }
            }

            $val['industry'] = array_unique($tmpIndustry);

            // 发文体系
            if (!empty($gov_name) && !empty($val['gov_agen'])) {
                foreach ($val['gov_agen'] as $kg => &$vg) {
                    $vg['gov_agen_first_name'] = array_get($gov_name, $vg['gov_agen_first'], '');
                    $vg['gov_agen_second_name'] = array_get($gov_name, $vg['gov_agen_second'], '');
                    $vg = Collection::filter($gov_key,$vg);
                }
            }
        }

        return $data;
    }

    public function getIndustryName($data)
    {
        // 行业
        $ids = [];
        foreach ($data as $key => $value) {
            $tmp = array_column($value['industry'],'first_industry_id');
            $tmp = array_merge($tmp,array_column($value['industry'],'second_industry_id'));
            $tmp = array_merge($tmp,array_column($value['industry'],'third_industry_id'));
            $tmp = array_merge($tmp,array_column($value['industry'],'fourth_industry_id'));
            $ids = array_merge($ids, $tmp);
        }
        $ids = array_unique(array_filter($ids));
        $name = $this->industryService->getIndustry($ids,['id', 'type_name']);
        return $name;
    }

    public function publicityList($params)
    {
        $column = [
            'id',
            'enc_id',
            'doc_num',
            'obj_type',
            'pub_time',
            'name',
            'content',
            'province_code',
            'city_code',
            'district_code',
            'validity_sdate',
            'validity_edate',
            'publish_status',
            'created_at'
        ];
        $data = $this->policyRepository->publicityList($params, $column);

        if (empty($data['data'])) {
            return $data;
        }

        // 地区
        $code_arr = $this->districtService->getDistrictCode($data['data']);

        foreach ($data['data'] as $key => &$val) {
            $val['id'] = $val['enc_id'];
            $val['obj_type_name'] = array_get(trans('constant.publicity_set_short'), $val['obj_type'], '');
            $val['province_name'] = array_get($code_arr, $val['province_code'], '');
            $val['city_name'] = array_get($code_arr, $val['city_code'],'');
            $val['district_name'] = array_get($code_arr, $val['district_code'],'');

            // 7天
            $val['is_new'] = $val['created_at'] < (time() - NEW_DAY * 86400) ? NEW_STATUS['not'] : NEW_STATUS['is'];

        }

        return $data;
    }

    public function announceDetailByEncId($enc_id)
    {
        $column = [
            'id',
            'enc_id',
            'obj_type',
            'name',
            'content',
            'province_code',
            'city_code',
            'district_code',
            'validity_sdate',
            'validity_edate',
            'publish_status',
            'created_at',
            'pub_time',
        ];

        $detail = $this->policyRepository->announceDetailByEncId($enc_id, $column);

        if (empty($detail)) {
            return $detail;
        }

        $detail['id'] = $detail['enc_id'];
        $detail['obj_type_name'] = array_get(trans('constant.publicity_set_short'), $detail['obj_type'], '');


        $code_arr_key = ['province_code', 'city_code', 'district_code'];

        $code_arr = array_unique(array_filter(Collection::filter($code_arr_key, $detail)));
        $code_arr = $this->districtService->getCode($code_arr);
        $detail['province_name'] = array_get($code_arr, $detail['province_code'], '');
        $detail['city_name'] = array_get($code_arr, $detail['city_code'],'');
        $detail['district_name'] = array_get($code_arr, $detail['district_code'],'');

        $detail['gov_agen'] = $this->govAgenDetail($detail['gov_agen']);
        $gov_key = [
            'gov_agen_first',
            'gov_agen_second',
            'gov_agen_first_name',
            'gov_agen_second_name',
        ];
        foreach ($detail['gov_agen'] as $key => $value) {
            $detail['gov_agen'][$key] = Collection::filter($gov_key, $value);
        }

        $relation = $this->cRelationDetail($detail, $detail['id'], $detail['obj_type']);

        unset($detail['relation_policy']);
        unset($detail['relation_policy_reverse']);

        $detail = array_merge($detail, $relation);
        $unscramble = [];
        $unscramble_key = [
            'id',
            'enc_id',
            'name',
            'content_url'
        ];
        if (!empty($detail['unscramble'][0])) {
            $unscramble = Collection::filter($unscramble_key, $detail['unscramble'][0]);
            $unscramble['id'] = $unscramble['enc_id'];
        }

        $detail['unscramble'] = (object)$unscramble;

        $industry = $this->industryService->getIndustryNameList($detail['industry']??[]);

        $industry_key = [
            'first_industry_name',
            'second_industry_name',
            'third_industry_name',
            'fourth_industry_name',
        ];

        foreach ($industry as $ki => $vi) {
            $industry[$ki] = Collection::filter($industry_key, $vi);
        }
        $detail['industry'] = $industry;

        $file = $detail['file'] ?? [];

        $file_key = [
            'name',
            'save_url',
        ];
        foreach ($file as $kf => $vf) {
            $file[$kf] = Collection::filter($file_key, $vf);
        }
        $detail['file'] = $file;

        $project_key = [
            'id',
            'enc_id',
            'name',
            'announce_status',
            'announce_status_desc',
        ];
        $project = [];

        if (!empty($detail['project'])) {
            foreach ($detail['project'] as $kp => $vp) {
                $tmp = Collection::filter($project_key, $vp);
                $tmp['id'] = $tmp['enc_id'];
                $project[] = $tmp;
            }
        }

        $detail['project'] = $project;
        return $detail;
    }

    public function publicityDetailByEncId($enc_id)
    {
        $column = [
            'id',
            'enc_id',
            'obj_type',
            'name',
            'content',
            'province_code',
            'city_code',
            'district_code',
            'validity_sdate',
            'validity_edate',
            'publish_status',
            'created_at',
            'pub_time',
        ];

        $detail = $this->policyRepository->publicityDetailByEncId($enc_id, $column);

        if (empty($detail)) {
            return $detail;
        }

        $detail['id'] = $detail['enc_id'];
        $detail['obj_type_name'] = array_get(trans('constant.publicity_set_short'), $detail['obj_type'], '');

        $code_arr_key = ['province_code', 'city_code', 'district_code'];

        $code_arr = array_unique(array_filter(Collection::filter($code_arr_key, $detail)));
        $code_arr = $this->districtService->getCode($code_arr);
        $detail['province_name'] = array_get($code_arr, $detail['province_code'], '');
        $detail['city_name'] = array_get($code_arr, $detail['city_code'],'');
        $detail['district_name'] = array_get($code_arr, $detail['district_code'],'');

        $detail['gov_agen'] = $this->govAgenDetail($detail['gov_agen']);
        $gov_key = [
            'gov_agen_first',
            'gov_agen_second',
            'gov_agen_first_name',
            'gov_agen_second_name',
        ];
        foreach ($detail['gov_agen'] as $key => $value) {
            $detail['gov_agen'][$key] = Collection::filter($gov_key, $value);
        }

        $file = $detail['file'] ?? [];

        $file_key = [
            'name',
            'save_url',
        ];
        foreach ($file as $kf => $vf) {
            $file[$kf] = Collection::filter($file_key, $vf);
        }
        $detail['file'] = $file;

        return $detail;
    }

    public function approvalDetailByEncId($enc_id)
    {
        $column = [
            'id',
            'enc_id',
            'obj_type',
            'name',
            'content',
            'created_at'
        ];
        $detail = $this->policyRepository->approvalDetailByEncId($enc_id, $column);

        if (empty($detail)) {
            return $detail;
        }

        $detail['id'] = $detail['enc_id'];
        $detail['obj_type_name'] = array_get(trans('constant.publicity_set_short'), $detail['obj_type'], '');

        return $detail;
    }

    /**
     * FUNCTION_NAME : cRelationDetail
     * author : jp
     * c 端 关联政策的详情
     * @param $data
     * @param $obj_id
     * @param $obj_type
     * @return array
     */
    public function cRelationDetail($data, $obj_id, $obj_type)
    {
        $tmp1  = empty($data['relation_policy']) ? [] : $data['relation_policy'];
        $tmp2 = empty($data['relation_policy_reverse']) ? [] : $data['relation_policy_reverse'];
        $out_data = array_merge($tmp1, $tmp2);

        $re_type = $this->typeHasRelation($obj_type);

        if (empty($re_type)) {
            return [];
        }

        $relation = [];

        foreach ($re_type as $kr => $vr) {
            $relation[$vr] = [];
        }

        if (empty($out_data)) {
            return $relation;
        }

        $base_relation_key = [
            OBJ_TYPE['macro_policy'] => 'macro_policy_relation',
            OBJ_TYPE['sup_policy'] => 'sup_policy_relation',
            OBJ_TYPE['imple_regu'] => 'imple_regu_relation',
            OBJ_TYPE['announce'] => 'announce_relation',
            OBJ_TYPE['publicity'] => 'publicity_relation',
        ];

        $policy_ids = array_column($out_data, 'obj_type_relation_id');
        $policy_ids = array_unique(array_merge($policy_ids, array_column($out_data, 'obj_id')));

        $name_arr = $this->policyRepository->getByIds($policy_ids);

        $names = array_column($name_arr, 'name', 'id');
        $enc_ids = array_column($name_arr, 'enc_id', 'id');
        $publish_arr = array_column($name_arr, 'publish_status', 'id');
        $exist_ids = array_column($name_arr, 'id');

        // 关联关系变成双向的时候要注意 交换自己和关联关系的位置
        foreach ($out_data AS $key=>$value) {
            if ($out_data[$key]['obj_type_relation_id'] == $obj_id) {
                $tmp = $out_data[$key]['obj_type_relation_id'];
                $out_data[$key]['obj_type_relation_id'] = $out_data[$key]['obj_id'];
                $out_data[$key]['obj_id'] = $tmp;

                $tmp = $out_data[$key]['obj_type'];
                $out_data[$key]['obj_type'] = $out_data[$key]['type'];
                $out_data[$key]['type'] = $tmp;
            }

            $target_key = array_get($base_relation_key, $out_data[$key]['type'], '');

            // 只看发布状态下的政策
            if (array_get($publish_arr, $out_data[$key]['obj_type_relation_id'], 0) != PUBLISH_STATUS['yes']
            || !in_array($out_data[$key]['obj_type_relation_id'], $exist_ids)) {
                continue;
            }

            if ($target_key && !empty($names[$out_data[$key]['obj_type_relation_id']])) {
                $out_data[$key]['name'] = $names[$out_data[$key]['obj_type_relation_id']];
                $out_data[$key]['obj_type'] = $out_data[$key]['type'];
                $out_data[$key]['id'] = $enc_ids[$out_data[$key]['obj_type_relation_id']];

                $relation[$target_key][] = Collection::filter(['id','name','obj_type'], $out_data[$key]);
            }
        }

        return $relation;

    }

    /**
     * FUNCTION_NAME : storeApproval
     * author : jp
     * 保存拨款公告  特列 默认地区
     * @param $data
     * @return mixed
     * @throws \App\Exceptions\QueryException
     */
    public function storeApproval($data)
    {
        $white = [
            'obj_type',
            'name',
            'doc_num',
            'content',
            'pub_time',
            'province_code',
            'city_code',
            'district_code',
            'validity_sdate',
            'validity_edate',
            'publish_status',
            'source',
            'source_web',
            'source_url',
            'is_handel',
            'target_id'
        ];
        $data = Collection::filter($white, $data);
        $data = $this->districtService->defaultDistrictFilter($data);
        $data['enc_id'] = $this->getEncId();
        $data['code'] = $this->createCode();
        $data['created_staff_id'] = getLoginStaff('id');
        $data['pub_time'] = time();

        $res = $this->policyRepository->storeRepository($data);

        return $res;
    }

    public function getCollectionByIds($ids)
    {
        $arr = $this->policyRepository->getByIds($ids);

        if (empty($arr)) {
            return [];
        }
        // 地区
        $code_arr = $this->districtService->getDistrictCode($arr);
        foreach ($arr as $key => &$val) {
            $val['obj_type_name'] = array_get(trans('constant.publicity_set_short'), $val['obj_type'], '');
            $val['province_name'] = array_get($code_arr, $val['province_code'], '');
            $val['city_name'] = array_get($code_arr, $val['city_code'],'');
            $val['district_name'] = array_get($code_arr, $val['district_code'],'');
        }

        return $arr;


    }
}