<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/17
 * Time: 11:03
 */

namespace App\Http\Controllers\Home;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\DistrictService;
use App\Http\Requests\Project\DetailRequest;
use App\Http\Requests\Project\HomeDetailRequest;
use App\Http\Requests\Project\HomeListRequest;
use App\Repositories\Policy\ProjectRepository;
use App\Support\Collection;

class ProjectController extends Controller
{

    protected $repository;
    protected $districtService;

    public function __construct(ProjectRepository $repository,
                                DistrictService $districtService)
    {
        $this->repository = $repository;
        $this->districtService = $districtService;
    }

    public function index(DetailRequest $request)
    {
        $data = $this->repository->detail($request->input('id'));


        return codeRender(Code::OK, $data);
    }


    /**
     * FUNCTION_NAME : applyDetail
     * author : jp
     * 申请项目用到的详情
     * @param DetailRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function applyDetail(DetailRequest $request)
    {
        $data = $this->repository->applyDetail($request->input('id'));

        if (empty($data)) {
            return codeRender(Code::OK, $data);
        }

        $expect = [
            'id',
            'code',
            'name',
            'mold_id',
            'validity_sdate',
            'validity_edate',
            'materials'
        ];

        $newData = Collection::filter($expect, $data);

        $newData['mold_name'] = empty($data['mold']['name']) ? '' : $data['mold']['name'];

        return codeRender(Code::OK, $newData);
    }

    public function list(HomeListRequest $request)
    {
        $params = $this->filter($request);
        $params = app(DistrictService::class)->defaultDistrictFilter($params);
        $params['order_by'] = [
            'id' => 'DESC'
        ];
        $params['publish_status'] =PUBLISH_STATUS['yes'];
        // 处理申报状态
        if (isset($params['announce_status']) && $params['announce_status'] == ANNOUNCE_STATUS['enter']) {
            $params['edate_gt'] = time();
            $params['sdate_elt'] = time();
        } elseif (isset($params['announce_status']) && $params['announce_status'] == ANNOUNCE_STATUS['over']) {
            $params['edate_elt'] = time();
        } elseif (isset($params['announce_status']) && $params['announce_status'] == ANNOUNCE_STATUS['wait']) {
            $params['sdate_gt'] = time();
        }

        $column = [
            'id',
            'enc_id',
            'name',
            'sup_content',
            'mold_id',
            'validity_sdate',
            'validity_edate',
            'province_code',
            'city_code',
            'district_code',
            'created_at'
        ];
        $data = $this->repository->search($params, $column);

        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }

        // 地区
        $code_arr = $this->districtService->getDistrictCode($data['data']);

        foreach ($data['data'] as $key => &$value) {
            $value['id'] = $value['enc_id'];

            // 这里做个直接写上地址

            $value['province_name'] = array_get($code_arr, $value['province_code'], '');
            $value['city_name'] = array_get($code_arr, $value['city_code'],'');
            $value['district_name'] = array_get($code_arr, $value['district_code'],'');

        }
        return codeRender(Code::OK, $data);
    }

    public function detail(HomeDetailRequest $request)
    {
        $column = [
            'id',
            'enc_id',
            'policy_id',
            'code',
            'name',
            'mold_id',
            'policy_basis',
            'sup_object',
            'sup_content',
            'apply_condition',
            'policy_advisory',
            'province_code',
            'city_code',
            'district_code',
            'validity_sdate',
            'validity_edate',
            'publish_status',
            'created_at',
        ];
        $data = $this->repository->getByEncId($request->input('id'), $column);

        if (empty($data)) {
            return codeRender(Code::OK, $data);
        }

        $data = array_merge($data, $this->districtService->getDistrictName($data));


        $data['mold_name'] = array_get($data['mold']??[], 'name', '');
        unset($data['mold']);

        $plate = [];
        $plate_key = [
            'title', 'content'
        ];
        if (!empty($data['plate'])) {
            foreach ($data['plate'] as $key => $value) {
                $plate[] = Collection::filter($plate_key, $value);
            }
        }
        $data['plate'] = $plate;

        $materials = [];
        $materials_key = [
            'id','project_id','name', 'is_need','type'
        ];

        $materials_supplement = [];
        if (!empty($data['materials'])) {
            foreach ($data['materials'] as $key => $value) {
                $tmp = Collection::filter($materials_key, $value);
                $tmp['is_need_name'] = array_get(trans('constant.materials_need'), $tmp['is_need']);
                if ($value['type'] == MATERIALS_TYPE['default']) {
                    $materials_supplement = $tmp;
                    continue;
                }
                $materials[] = $tmp;
            }
        }
        if (!empty($materials_supplement)) {
            array_push($materials, $materials_supplement);
        }

        $data['materials'] = $materials;

        $other_key = [
            'content'
        ];
        $data['materials_other'] = Collection::filter($other_key, $data['materials_other']??[]);
        $file = [];
        $file_key = ['name', 'save_url'];

        if (!empty($data['file'])) {
            foreach ($data['file'] as $key => $value) {
                $file[] = Collection::filter($file_key, $value);
            }
        }
        $data['file'] = $file;

        $policy = [];
        if (!empty($data['policy'])) {
            $policy_key = ['id', 'enc_id', 'name', 'obj_type'];
            $policy = Collection::filter($policy_key, $data['policy']);
            $policy['id'] = $policy['enc_id'];
        }
        $data['policy'] = $policy;
        $data['project_id'] = $data['id'];
        $data['id'] = $data['enc_id'];

        unset($data['policy_id']);
        return codeRender(Code::OK, $data);
    }

    /**
     * FUNCTION_NAME : pushList
     * author : jp
     * 推送列表
     * @param HomeListRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \App\Exceptions\QueryException
     * @throws \Xkd\Location\Exceptions\ClientException
     */
    public function pushList(HomeListRequest $request)
    {
        $params = $this->filter($request);
//        $params = app(DistrictService::class)->defaultDistrictFilter($params);
        $params['order_by'] = [
            'id' => 'DESC'
        ];
        $params['publish_status'] =PUBLISH_STATUS['yes'];
        // 处理申报状态
        if (isset($params['announce_status']) && $params['announce_status'] == ANNOUNCE_STATUS['enter']) {
            $params['edate_gt'] = time();
            $params['sdate_elt'] = time();
        } elseif (isset($params['announce_status']) && $params['announce_status'] == ANNOUNCE_STATUS['over']) {
            $params['edate_elt'] = time();
        } elseif (isset($params['announce_status']) && $params['announce_status'] == ANNOUNCE_STATUS['wait']) {
            $params['sdate_gt'] = time();
        }

        $column = [
            'id',
            'enc_id',
            'name',
            'sup_content',
            'mold_id',
            'validity_sdate',
            'validity_edate',
            'province_code',
            'city_code',
            'district_code',
            'created_at'
        ];
        $data = $this->repository->search($params, $column);

        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }

        // 地区
        $code_arr = $this->districtService->getDistrictCode($data['data']);

        foreach ($data['data'] as $key => &$value) {
            $value['id'] = $value['enc_id'];

            // 这里做个直接写上地址

            $value['province_name'] = array_get($code_arr, $value['province_code'], '');
            $value['city_name'] = array_get($code_arr, $value['city_code'],'');
            $value['district_name'] = array_get($code_arr, $value['district_code'],'');

        }
        return codeRender(Code::OK, $data);
    }

}