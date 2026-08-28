<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/17
 * Time: 19:24
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Events\ComputeStars;
use App\Events\FileChange;
use App\Exceptions\QueryException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\DistrictService;
use App\Http\Requests\Agent\BatchPublishRequest;
use App\Http\Requests\Agent\CleanCreditRequest;
use App\Http\Requests\Agent\DeleteRequest;
use App\Http\Requests\Agent\DetailRequest;
use App\Http\Requests\Agent\ListRequest;
use App\Http\Requests\Agent\SaveRequest;
use App\Http\Requests\Agent\UpdatePublishRequest;
use App\Http\Requests\Agent\UpdateRequest;
use App\Repositories\Agent\AgentCommentRepository;
use App\Repositories\Agent\AgentCreditRepository;
use App\Repositories\Agent\AgentFileRepository;
use App\Repositories\Agent\AgentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentController extends Controller
{

    protected $agentRepository;
    protected $agentFileRepository;

    public function __construct(AgentRepository $agentRepository,
                                AgentFileRepository $agentFileRepository)
    {
        $this->agentRepository = $agentRepository;
        $this->agentFileRepository = $agentFileRepository;
    }

    /**
     *
     * @api POST /api/agent/store 新增
     * @apiVersion 1.0.0
     * @apiName AgentStore
     * @apiGroup 运营端--中介机构服务管理
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {Number} submit_time 提交时间
     * @apiParam {Number} submit_material 是否提交电子资料 0-否 2-是
     *
     * @apiSuccess {Number} code 返回代码
     * @apiSuccess {String} message 返回信息
     * @apiSuccess {Object} data 返回数据块
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "code": 200,
     *       "message": "操作成功",
     *         "data":{
     *              "field-1": "xx",
     *              "field-2": "xx",
     *              "field-3": "xx"
     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function store(SaveRequest $request)
    {
        $fileColumn = [
            'file',
            'file.*.name',
            'file.*.save_url',
        ];

        $white = array_diff(array_keys($request->rules()), $fileColumn);

        $params = $request->only($white);
        $params[CREATED_STAFF_ID] = (int)getLoginStaff('id');

        $params['enc_id'] = $this->getEncId();
        $params['code'] = $this->agentRepository->getMaxCode();
        $params = $this->initValue($params);
        try {
            DB::beginTransaction();
            $res = $this->agentRepository->storeRepository($params);
            $this->storeFile($request, $res['id']);
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

        if ($params['submit_material'] == AGENT_SUBMIT_MATERIAL['yes']) {
            event(new ComputeStars([$res['id']]));
        }
        return codeRender(Code::OK);
    }

    public function storeFile($request, $id)
    {
        $file = $request->input('file', []);

        $column = ['name', 'save_url'];
        $file = array_map(function ($v) use ($column) {
            return array_only($v, $column);
        }, $file);
        if (!empty($file)) {
            foreach ($file as $key => $value) {
                $file[$key]['agent_id'] = $id;
                $file[$key] = array_merge($file[$key], returnCreatedUpdatedAt());
            }
            $this->agentFileRepository->storeBatchRepository($file);
        }
    }

    /**
     *
     * @api POST /api/agent/update 更新
     * @apiVersion 1.0.0
     * @apiName AgentUpdate
     * @apiGroup 运营端--中介机构服务管理
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {Number} submit_time 提交时间
     * @apiParam {Number} submit_material 是否提交电子资料 0-否 2-是
     *
     * @apiSuccess {Number} code 返回代码
     * @apiSuccess {String} message 返回信息
     * @apiSuccess {Object} data 返回数据块
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "code": 200,
     *       "message": "操作成功",
     *         "data":{

     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function update(UpdateRequest $request)
    {
        $fileColumn = [
            'file',
            'file.*.id',
            'file.*.name',
            'file.*.save_url',
            'enterprise_id',
        ];

        $white = array_diff(array_keys($request->rules()), $fileColumn);
        $params = $request->only($white);
        $params = $this->initValue($params);
        try {
            DB::beginTransaction();
            $res = $this->agentRepository->updateRepository($params);
            $this->updateFile($request, $params['id']);
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
        event(new ComputeStars([$request->input('id')]));
        return codeRender(Code::OK);
    }

    public function updateFile($request, $id)
    {
        $file = $request->input('file', []);
        $column = ['id','name', 'save_url'];
        $file = array_map(function ($v) use ($column) {
            return array_only($v, $column);
        }, $file);

        $list = $this->agentFileRepository->getList($id, ['id']);
        $exist = array_column($list, 'id');

        $deletes = array_diff($exist, array_column($file, 'id'));

        if (!empty($file)) {
            foreach ($file as $key => $value) {
                if (!empty($value['id'])) {
                    unset($file[$key]);
                    continue;
                }
                unset($file[$key]['id']);
                $file[$key]['agent_id'] = $id;
                $file[$key] = array_merge($file[$key], returnCreatedUpdatedAt());
            }
            $this->agentFileRepository->storeBatchRepository($file);
            if ($file) {
                $log = [
                    'type' => ACTIVITY_TYPE['created'],
                    'subject_id' => $id,
                    'subject_type_id' => ACTIVITY_SUBJECT_TYPE['agent'],
                    'properties' => json_encode(['attributes' => $file, 'old' => []]),
                ];
                event(new FileChange($log));
            }
        }

        if ($deletes) {
            $this->agentFileRepository->deleteRepository($deletes);
            $log = [
                'type' => ACTIVITY_TYPE['deleted'],
                'subject_id' => $id,
                'subject_type_id' => ACTIVITY_SUBJECT_TYPE['agent'],
                'properties' => json_encode(['attributes' => [], 'old' => $deletes]),
            ];
            event(new FileChange($log));
        }
    }

    public function detail(DetailRequest $request)
    {
        $where = [
            'id' => $request->input('id')
        ];
        $data = $this->agentRepository->detail($where);
        if (empty($data)) {
            return codeRender(Code::OK, $data);
        }
        $data = array_merge($data, app(DistrictService::class)->getDistrictName($data));
        $data['agent_name'] = array_get($data['enterprise'], 'name', '');
        $data['agent_type_name'] = array_get($data['agent_type'], 'name', '');
        // 这里要统计星
        $data['enterprise_stars_arr'] = app(AgentCommentRepository::class)->getNumGroupType($data['id'],MESSAGE_USER_TYPE['user']);
        $data['department_stars_arr'] = app(AgentCommentRepository::class)->getNumGroupType($data['id'],MESSAGE_USER_TYPE['staff']);
        $data = array_merge($data, app(DistrictService::class)->getDistrictName($data));
        $data['composite_stars'] = (float)$data['composite_stars'];
        $data['department_stars'] = (float)$data['department_stars'];
        $data['enterprise_stars'] = (float)$data['enterprise_stars'];
        return codeRender(Code::OK, $data);
    }

    public function delete(DeleteRequest $request)
    {
        $this->agentRepository->deleteRepository($request->input('ids'));
        return codeRender(Code::OK);
    }

    /**
     *
     * @api GET /api/agent/list  中介服务列表
     * @apiVersion 1.0.0
     * @apiName 中介服务列表
     * @apiGroup 运营端--中介服务
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {String} keyword
     * @apiParam {Number} type_id
     * @apiParam {Number} credit_type
     * @apiParam {Number} enterprise_id
     * @apiParam {Number} page
     * @apiParam {Number} per_page
     *
     * @apiSuccess {Number} code 返回代码
     * @apiSuccess {String} message 返回信息
     * @apiSuccess {Object} data 返回数据块
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "code": 200,
     *       "message": "操作成功",
     *       "data":{
                "total": 1,
                "total_page": 1,
                "current_page": 1,
                "per_page_num": 1,
                "data": [
                    {
                    "id": 5,
                    "enc_id": "c721cdafd62d7703e84a",
                    "code": 3,
                    "enterprise_id": 1,
                    "type_id": 2,
                    "service_item": "服务事项222444444444",
                    "file_name": "【1904密训资料】中国古代文学史（一）（全国）.pdf",
                    "file_url": "https://xkd-saas-base.oss-cn-beijing.aliyuncs.com/dev-wenjiang/20190807/0/eCjwQs2dA1vLhbCOwEkbI8jDvLZp3Y77MYwMhYxl.pdf",
                    "service_detail": "服务详情",
                    "province_code": 510000000000,
                    "city_code": 510100000000,
                    "district_code": 510115000000,
                    "address": "地址",
                    "contact_name": "18808054854",
                    "contact_phone": "蒋鹏",
                    "publish_status": 1,
                    "remark": "6666",
                    "composite_stars": 0,
                    "department_stars": 0,
                    "enterprise_stars": 0,
                    "credit_type": 0,
                    "submit_time": 0,
                    "submit_material": 0,
                    "created_staff_id": 2,
                    "created_at": "1571214796",
                    "updated_at": "1575885783",
                    "deleted_at": null,
                    "credit_type_name": "信用正常",
                    "agent_name": "成都海科康商贸有限公司同人店",
                    "agent_type_name": "科技创新"
                    }
                ]
     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        $params['order_by'] = ['id' => 'DESC'];
        $data = $this->agentRepository->list($params);

        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }

        foreach ($data['data'] as $key => &$value) {
            $value['agent_name'] = array_get($value['enterprise'], 'name', '');
            unset($value['enterprise']);
            $value['agent_type_name'] = array_get($value['agent_type'], 'name', '');
            unset($value['agent_type']);
            $value['composite_stars'] = (float)$value['composite_stars'];
            $value['department_stars'] = (float)$value['department_stars'];
            $value['enterprise_stars'] = (float)$value['enterprise_stars'];

        }
        return codeRender(Code::OK, $data);
    }

    private function getEncId(){
        $enc_id = substr(md5(time().rand()), 0, 20);

        $data = $this->agentRepository->getByEncId($enc_id, ['id']);

        if (!empty($data)) {
            return $this->getEncId();
        }
        return $enc_id;
    }

    public function updatePublish(UpdatePublishRequest $request)
    {
        $param = $this->filter($request);
        $detail = $this->agentRepository->findRepository($param['id'])->toArray();

        if ($detail['credit_type'] == AGENT_CREDIT_TYPE['serious']) {
            return codeRender(Code::AGENT_PUBLISH_CREDIT_SERIOUS_ERROR);
        }

        $this->agentRepository->updateRepository($param);
        return codeRender(Code::OK);
    }

    public function batchPublish(BatchPublishRequest $request)
    {
        $status = $request->input('publish_status');
        $arr = $this->agentRepository->getByIds($request->input('ids'), ['id', 'credit_type', 'publish_status']);
        $ids = [];
        foreach ($arr as $k => $v) {
            if ($v['publish_status'] == $status) {
                continue;
            } elseif ($status == PUBLISH_STATUS['yes'] && $v['credit_type'] == AGENT_CREDIT_TYPE['serious']) {
                continue;
            }
            $ids[] = $v['id'];
        }
        $this->agentRepository->batchPublish($ids, $request->input('publish_status'));
        return codeRender(Code::OK);
    }

    public function clean(CleanCreditRequest $request)
    {
        $param = $request->input('ids');
        try {
            DB::beginTransaction();
            app(AgentCreditRepository::class)->cleanByAgentId($param);
            $this->agentRepository->cleanCreditByIds($param);
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
        event(new ComputeStars($request->input('ids', [])));
        return codeRender(Code::OK);
    }

    public function initValue($data)
    {
        $keys = [
            'remark' => '',
            'service_detail' => '',
        ];
        foreach ($keys as $key => $value) {
            $data[$key] = empty($data[$key]) ? $value : $data[$key];
        }
        return $data;
    }



}