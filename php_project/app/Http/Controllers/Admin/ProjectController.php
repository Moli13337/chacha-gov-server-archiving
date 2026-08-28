<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/19
 * Time: 0:44
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Events\FileChange;
use App\Events\IndustryChange;
use App\Events\MaterialsChange;
use App\Events\MaterialsOtherChange;
use App\Events\ProjectFileChange;
use App\Events\ProjectPlateChange;
use App\Events\Recommend;
use App\Exceptions\QueryException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\DistrictService;
use App\Http\Controllers\Service\IndustryService;
use App\Http\Requests\Enterprise\ConditionRequest;
use App\Http\Requests\Policy\LogRequest;
use App\Http\Requests\Project\DeleteBatchRequest;
use App\Http\Requests\Project\DeleteRequest;
use App\Http\Requests\Project\DetailRequest;
use App\Http\Requests\Project\ListRequest;
use App\Http\Requests\Project\SaveRequest;
use App\Http\Requests\Project\UpdatePublishRequest;
use App\Http\Requests\Project\UpdateRequest;
use App\Repositories\ActivityLogRepository;
use App\Repositories\Policy\ProjectFileRepository;
use App\Repositories\Policy\ProjectIndustryRepository;
use App\Repositories\Policy\ProjectMaterialsOtherRepository;
use App\Repositories\Policy\ProjectMaterialsRepository;
use App\Repositories\Policy\ProjectPlateRepository;
use App\Repositories\Policy\ProjectRepository;
use App\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{

    protected $repository;
    protected $projectFileRepository;
    protected $projectMaterialsRepository;
    protected $projectMaterialsOtherRepository;
    protected $projectPlateRepository;

    public function __construct(ProjectRepository $repository,
                                ProjectFileRepository $projectFileRepository,
                                ProjectMaterialsRepository $projectMaterialsRepository,
                                ProjectMaterialsOtherRepository $projectMaterialsOtherRepository,
                                ProjectPlateRepository $projectPlateRepository)
    {
        $this->repository = $repository;
        $this->projectFileRepository = $projectFileRepository;
        $this->projectMaterialsRepository = $projectMaterialsRepository;
        $this->projectMaterialsOtherRepository = $projectMaterialsOtherRepository;
        $this->projectPlateRepository = $projectPlateRepository;
    }


    public function store(SaveRequest $request)
    {
        $white = [
            'policy_id',
            'name',
            'content',
            'mold_id',
            'province_code',
            'city_code',
            'district_code',
            'validity_sdate',
            'validity_edate',
            'publish_status',
            'policy_basis',
            'sup_object',
            'sup_content',
            'apply_condition',
            'policy_advisory',
        ];

        $data = Collection::filter($white, $request->all());
        $data['code'] = $this->createCode();
        $data['enc_id'] = $this->getEncId();
        $data['created_staff_id'] = getLoginStaff('id');
        $data['policy_id'] = empty($data['policy_id']) ? 0 : $data['policy_id'];
        try {
            DB::beginTransaction();
            $res = $this->repository->storeRepository($data);
            $this->relationInsert($request->all(), $res['id']);
            DB::commit();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            throw new QueryException(Code::FAIL, $e->getMessage());
        }

        event(new Recommend(['obj_id' => $res['id'], 'obj_type' => OBJ_TYPE['project']]));
        return codeRender(Code::OK, $res);

    }

    public function relationInsert($data, $id)
    {
        if (!empty($data['materials'])) {
            $this->relationMaterialsInsert($data['materials'], $id);
        }

        if (!empty($data['materials_other'])) {
            $this->relationOtherInsert($data['materials_other'], $id);
        }

        if (!empty($data['plate'])) {
            $this->relationPlateInsert($data['plate'], $id);
        }

        if (!empty($data['file'])) {
            $this->relationFileInsert($data['file'], $id);
        }

        if (!empty($data['industry'])) {
            $this->relationIndustryInsert($data['industry']??[],$id);
        }
    }

    public function relationMaterialsInsert($data, $project_id)
    {
        $white = [
            'name',
            'is_need',
            'type',
        ];
        // 新增一个补充材料的 该条不能被删除
        $data[] = [
            'name' => '请上传补充材料',
            'is_need' => MATERIALS_NEED['or'],
            'type' => MATERIALS_TYPE['default'],
        ];        foreach ($data as $key => &$v) {
            $v = Collection::filter($white, $v);
            $v['project_id'] = $project_id;
            $v = array_merge($v, returnCreatedUpdatedAt());
        }



        $this->projectMaterialsRepository->storeBatch($data);
    }

    public function relationOtherInsert($data, $project_id)
    {
        $white = [
            'content',
        ];
        $data = Collection::filter($white, $data);
        $data['project_id'] = $project_id;

        $this->projectMaterialsOtherRepository->storeRepository($data);
    }

    public function relationPlateInsert($data, $project_id)
    {
        $white = [
            'title',
            'content',
        ];
        foreach ($data as $key => &$v) {
            $v = Collection::filter($white, $v);
            $v['project_id'] = $project_id;
            $v = array_merge($v, returnCreatedUpdatedAt());
        }

        $this->projectPlateRepository->storeBatch($data);
    }

    public function relationFileInsert($data, $project_id)
    {
        $white = [
            'name',
            'save_url'
        ];
        foreach ($data as $key => &$v) {
            $v = Collection::filter($white, $v);
            $v['project_id'] = $project_id;
            $v = array_merge($v, returnCreatedUpdatedAt());
        }

        $this->projectFileRepository->storeBatch($data);
    }

    public function relationIndustryInsert($data, $project_id)
    {
        $white = app(IndustryService::class)->industryItem;
        foreach ($data as $key => &$v) {
            $v = Collection::filter($white, $v);
            $v = app(IndustryService::class)->initIndustry($v);
            $v['project_id'] = $project_id;
            $v = array_merge($v, returnCreatedUpdatedAt());
        }

       app(ProjectIndustryRepository::class)->storeBatch($data);

    }

    public function update(UpdateRequest $request)
    {
        $white = [
            'id',
            'policy_id',
            'name',
            'content',
            'mold_id',
            'validity_sdate',
            'validity_edate',
            'publish_status',
            'policy_basis',
            'sup_object',
            'sup_content',
            'apply_condition',
            'policy_advisory',
        ];
        $data = Collection::filter($white, $request->all());

        if (isset($data['policy_id'])) {
            $data['policy_id'] = (int)array_get($data, 'policy_id', 0);
        }

        try {
            DB::beginTransaction();
            $res = $this->repository->updateRepository($data);
            $this->relationUpdate($request->all(), $res['id']);
            DB::commit();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            throw new QueryException(Code::FAIL, $e->getMessage());
        }
        event(new Recommend(['obj_id' => $res['id'], 'obj_type' => OBJ_TYPE['project']]));

        return codeRender(Code::OK, $res);

    }

    public function relationUpdate($data, $id)
    {
        $this->relationMaterialsUpdate($data['materials']??[], $id);

        $this->relationOtherUpdate($data['materials_other']??[], $id);
        $this->relationPlateUpdate($data['plate']??[], $id);
        $this->relationFileUpdate($data['file']??[], $id);
        $this->relationIndustryUpdate($data['industry']??[], $id);
    }

    public function relationMaterialsUpdate($data, $project_id)
    {
        $all = $this->projectMaterialsRepository->getAllByProject($project_id);

        // 排除补充材料的
        foreach ($all as $ka => $va) {
            if ($va['type'] == MATERIALS_TYPE['default']) {
                unset($all[$ka]);
            }
        }

        $ids = array_column($all, 'id');
        $white = [
            'name',
            'is_need',
            'type',
        ];

        $update = [];
        $insert = [];
        $exist = [];

        $all = array_column($all, null, 'id');

        foreach ($data as $key => $v) {
            if (!empty($v['id']) && in_array($v['id'], $ids)) {
                $exist[] = $v['id'];
                // 处理更新
                $tmp = [];
                foreach ($white as $kw => $vw) {
                    if ($all[$v['id']][$vw] != $v[$vw]) {
                        $tmp[$vw] = $v[$vw];
                    }
                }

                if (!empty($tmp)) {
                    $tmp['id'] = $v['id'];
                    $update[] = $tmp;
                }
            } else {
                $v = Collection::filter($white, $v);
                $v['project_id'] = $project_id;
                $insert[] = array_merge($v, returnCreatedUpdatedAt());
            }
        }

        $flag = false;
        // 新增
        if (!empty($insert)) {
            $flag = true;
            $this->projectMaterialsRepository->storeBatch($insert);
        }

        // 删除
        if ($diff = array_diff($ids, $exist)) {
            $flag = true;
            $this->projectMaterialsRepository->deleteByIds($diff);
        }

        // 更新
        if (!empty($update)) {
            $flag = true;
            foreach ($update as $ku => $vu) {
                $this->projectMaterialsRepository->updateRepository($vu);
            }
        }

        if ($flag) {
            $params = [
                'type' => ACTIVITY_TYPE['updated'],
                'subject_id' => $project_id,
                'subject_type_id' => OBJ_TYPE['project'],
                'properties' => json_encode(['attributes' => array_merge($insert, $update), 'old' => $all]),
            ];
            event(new MaterialsChange($params));
        }
    }

    public function relationOtherUpdate($data, $project_id)
    {
        if (empty($data)) {
            return;
//            $this->projectMaterialsOtherRepository->deleteByProjectId($project_id);
        } else {
            $res = $this->projectMaterialsOtherRepository->getByProject($project_id);
            $params = [
                'subject_id' => $project_id,
                'type' => ACTIVITY_TYPE['updated'],
                'subject_type_id' => ACTIVITY_SUBJECT_TYPE['project'],
            ];
            if (empty($res)) {
                $insert = [
                    'project_id' => $project_id,
                    'content' => $data['content']
                ];
                $this->projectMaterialsOtherRepository->storeRepository($insert);
                $params['description'] = json_encode(['attributes' => $insert, 'old' => []]);
                event(new MaterialsOtherChange($params));
            } elseif ($data['content'] != $res['content']) {
                $this->projectMaterialsOtherRepository->updateRepository([
                    'id' => $res['id'],
                    'content' => $data['content']
                ]);
                $params['description'] = json_encode(['attributes' => ['content' => $data['content']], 'old' => $res]);
                event(new MaterialsOtherChange($params));
            }

//            $this->projectMaterialsOtherRepository->selfUpdateOrCreate(
//                ['project_id' => $project_id],
//                ['content' => $data['content']]
//            );
        }

    }

    public function relationPlateUpdate($data, $project_id)
    {
        $params = [
            'data' => $data,
            'type' => ACTIVITY_TYPE['updated'],
            'subject_type_id' => ACTIVITY_SUBJECT_TYPE['project'],
            'id' => $project_id
        ];
        event(new ProjectPlateChange($params));

        $res = $this->projectPlateRepository->deleteByProjectId($project_id);
        if (!empty($data)) {
            $res = $this->relationPlateInsert($data, $project_id);
        }
        return $res;
    }

    public function relationFileUpdate($data, $project_id)
    {
        $params = [
            'data' => $data,
            'id' => $project_id,
            'type' => ACTIVITY_TYPE['updated'],
            'subject_type_id' => ACTIVITY_SUBJECT_TYPE['project'],
        ];
        event(new ProjectFileChange($params));

        $res = $this->projectFileRepository->deleteByProjectId($project_id);
        if (!empty($data)) {
            $res = $this->relationFileInsert($data, $project_id);
        }
        return $res;
    }

    public function relationIndustryUpdate($data, $project_Id)
    {
        // 这里需要对行业进行更细致的判断， 便于区分是否编辑
        $have = app(ProjectIndustryRepository::class)->list($project_Id);
        $industryItem = app(IndustryService::class)->industryItem;
        $haveArr = array_map(function ($item) use ($industryItem) {
            return implode('-', array_only($item, $industryItem));
        }, $have);
        $res = app(ProjectIndustryRepository::class)->deleteByProjectId($project_Id);
        $flag = false;
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                if (!in_array(implode('-', array_only($v, $industryItem)), $haveArr)) {
                    $flag = true;
                    break;
                }
            }

            $res = $this->relationIndustryInsert($data, $project_Id);
        }

        if (count($have) != count($data) || $flag) {
            $params = [
                'type' => ACTIVITY_TYPE['updated'],
                'subject_id' => $project_Id,
                'subject_type_id' => OBJ_TYPE['project'],
                'properties' => json_encode(['attributes' => $data, 'old' => $have]),
            ];
            event(new IndustryChange($params));
        }
        return $res;
    }

    public function detail(DetailRequest $request)
    {
        $data = $this->repository->detail($request->input('id'));
        if (empty($data)) {
            return codeRender(Code::OK, $data);
        }

        $data['policy_name'] = array_get($data['policy']??[],'name', '');
        $data = array_except($data, 'policy');

        foreach ($data['materials'] as $key => $value) {
            if ($value['type'] == MATERIALS_TYPE['default']) {
                unset($data['materials'][$key]);
            }
        }
        $data['materials'] = array_values($data['materials']);
        $data['industry'] = app(IndustryService::class)->getIndustryNameList($data['industry']??[]);
        return codeRender(Code::OK, $data);
    }

    public function delete(DeleteRequest $request)
    {
        $this->repository->deleteRepository($request->input('id'));

        return codeRender(Code::OK);
    }

    public function updatePublish(UpdatePublishRequest $request)
    {
        $this->repository->updateRepository($this->filter($request));
        event(new Recommend(['obj_id' => $request->input('id'), 'obj_type' => OBJ_TYPE['project']]));

        return codeRender(Code::OK);
    }

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        // 处理过期
        if (isset($params['expired']) && !blank($params['expired']) && $params['expired'] == EXPIRED['no']) {
            $params['edate_gt'] = time();
            $params['sdate_elt'] = time();
        } elseif (isset($params['expired']) && $params['expired'] == EXPIRED['yes']) {
            $params['edate_elt'] = time();
        } elseif (isset($params['expired']) && $params['expired'] == EXPIRED['init']) {
            $params['sdate_gt'] = time();
        }

        if (!empty($params['sort']) && $params['sort'] == SORT['asc']) {
            $params['order_by']['id'] = 'ASC';
        } else {
            $params['order_by']['id'] = 'DESC';
        }

        $data = $this->repository->list($params);

        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }
        $code_arr = app(DistrictService::class)->getDistrictCode($data['data']);

        foreach ($data['data'] as $key => &$val) {
            $val['district']['province_name'] = array_get($code_arr, $val['province_code'], '');
            $val['district']['city_name'] = array_get($code_arr, $val['city_code'],'');
            $val['district']['district_name'] = array_get($code_arr, $val['district_code'],'');

        }
        return codeRender(Code::OK, $data);
    }

    private function createCode(){
        $prefix = date('Ymd');
        $code_data = $this->repository->getByLikeCode($prefix, ['code']);
        $code = empty($code_data) ? 1 : substr($code_data['code'], 8) + 1;
        return $prefix.str_pad($code, 6, 0, STR_PAD_LEFT);
    }

    private function getEncId(){
        $enc_id = substr(md5(time().rand()), 0, 20);

        $data = $this->repository->getByEncId($enc_id, ['id']);

        if (!empty($data)) {
            return $this->getEncId();
        }
        return $enc_id;
    }

    public function deleteBatch(DeleteBatchRequest $request)
    {
        $this->repository->deleteBatch($request->input('ids'));

        return codeRender(Code::OK);
    }

    public function log(LogRequest $request, ActivityLogRepository $activityLogRepository)
    {
        $tmp = [
            'subject_id' => $request->input('id'),
            'subject_type_id' => ACTIVITY_SUBJECT_TYPE['project'],
        ];
        $params = $this->filter($request);
        $params = array_merge($params, $tmp);
        $data = $activityLogRepository->getList($params);
        return codeRender(Code::OK, $data);
    }

    public function condition(ConditionRequest $request)
    {
        $params = $this->filter($request);
        $params['order_by'] = ['id' => 'DESC'];
        $data =  $this->repository->conditionList($params, ['id', 'name']);
        if (empty($data['data'])) {
            return codeRender(Code::OK, []);
        }
        $new = [];
        foreach ($data['data'] as $key => $value) {
            $new[] = $value;
        }
        return codeRender(Code::OK, $new);
    }

}