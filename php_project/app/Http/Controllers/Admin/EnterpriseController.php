<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/10
 * Time: 17:33
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Exceptions\QueryException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\IndustryService;
use App\Http\Controllers\Service\TianYanService;
use App\Http\Requests\Enterprise\AdminSaveEnterpriseRequest;
use App\Http\Requests\Enterprise\AdminUpdateEnterpriseRequest;
use App\Http\Requests\Enterprise\ConditionRequest;
use App\Http\Requests\Enterprise\DetailRequest;
use App\Http\Requests\Enterprise\ListEnterpriseRequest;
use App\Http\Requests\Enterprise\SaveLicenseRequest;
use App\Http\Requests\Enterprise\SendMessageRequest;
use App\Repositories\Enterprise\EnterpriseBusinessRepository;
use App\Repositories\Enterprise\EnterpriseEmployeeOverviewRepository;
use App\Repositories\Enterprise\EnterpriseIndustryRepository;
use App\Repositories\Enterprise\EnterpriseLinkmanRepository;
use App\Repositories\Enterprise\EnterpriseRepository;
use App\Repositories\Enterprise\EnterpriseSendRecordRepository;
use App\Repositories\User\UserEnterpriseRelationRepository;
use App\Repositories\User\UserMessageRepository;
use App\Support\Collection;
use Illuminate\Support\Facades\DB;

class EnterpriseController extends Controller
{

    protected $repository;
    protected $enterpriseBusinessRepository;
    protected $enterpriseIndustryRepository;
    protected $industryService;
    protected $tianYanService;

    public function __construct(EnterpriseRepository $repository,
                                EnterpriseBusinessRepository $enterpriseBusinessRepository,
                                EnterpriseIndustryRepository $enterpriseIndustryRepository,
                                IndustryService $industryService, TianYanService $tianYanService)
    {
        $this->repository = $repository;
        $this->enterpriseBusinessRepository = $enterpriseBusinessRepository;
        $this->enterpriseIndustryRepository = $enterpriseIndustryRepository;
        $this->industryService = $industryService;
        $this->tianYanService = $tianYanService;
    }

    public function store(AdminSaveEnterpriseRequest $request)
    {
        $white = [
            'name',
            'unified_credit_code',
            'organization_code',
            'legal_represent',
            'regist_capital',
            'regist_address',
            'regist_time',
            'business_address',
            'business_area',
            'tax_number',
        ];
        $data = Collection::filter($white, $request->all());

//        $industry = Collection::filter($this->industryService->industryItem, $request->all());
//        $industry = $this->industryService->initIndustry($industry);
        $data['created_staff_id'] = getLoginStaff('id');
        $data = $this->initValue($data);

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
            throw new QueryException(Code::DB_ERROR, $e->message());
        } catch (\Exception $e) {
            DB::rollBack();
            throw new QueryException(Code::FAIL, $e->getMessage());
        }

        return codeRender(Code::OK, $res);
    }

    public function relationInsert($data, $id)
    {
        $this->industryUpdate($data, $id);
    }


    public function update(AdminUpdateEnterpriseRequest $request)
    {
        $white = [
            'id',
            'name',
            'unified_credit_code',
            'organization_code',
            'legal_represent',
            'regist_capital',
            'regist_address',
            'regist_time',
            'business_address',
            'business_area',
            'tax_number',
        ];

        $data = Collection::filter($white, $request->all());
        $data = $this->initValue($data);
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
            throw new QueryException(Code::DB_ERROR, $e->message());
        } catch (\Exception $e) {
            DB::rollBack();
            throw new QueryException(Code::FAIL, $e->getMessage());
        }

        return codeRender(Code::OK, $res);
    }

    public function relationUpdate($data, $id)
    {
        $this->industryUpdate($data, $id);
    }

    public function industryUpdate($data, $id)
    {
        $white = [
            'first_industry_id',
            'second_industry_id',
            'third_industry_id',
            'fourth_industry_id',
        ];

        $data = Collection::filter($white, $data);
        $data = $this->industryService->initIndustry($data);
        $where = [
            'enterprise_id' => $id
        ];
        $this->enterpriseIndustryRepository->selfUpdateOrCreate($where, $data);
    }

    public function detail(DetailRequest $request)
    {
        $data =$this->repository->detail($request->input('id'));

        $industry = $this->enterpriseIndustryRepository->getByEnterprise($request->input('id'));

        $industry_name = $this->industryService->getIndustryName($industry);

        $data['industry'] = (object)array_merge($industry, $industry_name);
        return codeRender(Code::OK, $data);
    }

    /**
     *
     * @api GET /api/enterprise/list 企业列表
     * @apiVersion 1.0.0
     * @apiName 企业列表
     * @apiGroup 运营端--企业信息
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {String} keyword
     * @apiParam {Number} relation_status 关联状态 0-未关联 1-关联
     * @apiParam {Number} first_industry_id
     * @apiParam {Number} second_industry_id
     * @apiParam {Number} third_industry_id
     * @apiParam {Number} fourth_industry_id
     * @apiParam {Number} per_page
     * @apiParam {Number} page
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
                    "id": 1,
                    "name": "成都海科康商贸有限公司同人店",
                    "unified_credit_code": "91510115MA69BE0N2W",
                    "organization_code": "MA69BE0N2",
                    "tax_number": "91510115MA69BE0N2W",
                    "legal_represent": "赵兵",
                    "business_license_url": "",
                    "regist_capital": "0.00",
                    "regist_address": "成都市温江区柳城镇同人街112号19栋1楼17号",
                    "regist_time": 1544716800,
                    "business_term": "",
                    "business_address": "",
                    "business_area": "0.00",
                    "created_staff_id": 0,
                    "created_at": "1564648796",
                    "updated_at": "1564648796",
                    "deleted_at": null,
                    "user": {
                        "name": "蒋",
                        "mobile": "18808054854",
                    }
                ]
     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function list(ListEnterpriseRequest $request)
    {

        $params = $this->filter($request);

        $params['order_by'] = [
            'id' => 'DESC'
        ];
        $data = $this->repository->search($params);

        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }

        foreach ($data['data'] as $key => $value) {

            if (isset($value['user'])) {
                $value['user'] = empty($value['user'][0]) ? [] : $value['user'][0];
                $data['data'][$key] = arr2obj($value,['user']);
            }
        }

        return codeRender(Code::OK, $data);
    }

    /**
     * FUNCTION_NAME : sendMessage
     * author : jp
     * 发送站内信
     * @param SendMessageRequest $request
     * @param EnterpriseSendRecordRepository $enterpriseSendRecordRepository
     * @param UserEnterpriseRelationRepository $userEnterpriseRelationRepository
     * @param UserMessageRepository $userMessageRepository
     * @return mixed
     * @throws QueryException
     */
    public function sendMessage(SendMessageRequest $request,
                                EnterpriseSendRecordRepository $enterpriseSendRecordRepository,
                                UserEnterpriseRelationRepository $userEnterpriseRelationRepository,
                                UserMessageRepository $userMessageRepository)
    {
        $white = [
            'title',
            'content'
        ];
        $data = Collection::filter($white, $request->all());

        $ids = $request->input('enterprise_ids');
        // 校验是否有可用的用户
        $users = $userEnterpriseRelationRepository->getByEnterpriseIds($ids, ['user_id', 'enterprise_id']);
        if (empty($users)) {
            return codeRender(Code::OK);
        }

        $users = array_column($users, 'user_id');

        $insertData = [];
        $saveData = $data;
        $saveData['created_staff_id'] = (int)getLoginStaff('id');
        $saveData['enterprise_ids'] = json_encode($request->input('enterprise_ids'));

        try {
            DB::beginTransaction();
            $res = $enterpriseSendRecordRepository->storeRepository($saveData);

            $timeArr = returnCreatedUpdatedAt();
            foreach ($users as $value) {
                $tmp = $data;
                $tmp['user_id'] = $value;
//                $tmp['target_id'] = $res['id'];
                $tmp['source_type_id'] = USER_MESSAGE_SOURCE['system'];
                $tmp = array_merge($tmp, $timeArr);
                $insertData[] = $tmp;
            }
            $userMessageRepository->storeBatch($insertData);

            DB::commit();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->message());
        } catch (\Exception $e) {
            DB::rollBack();
            throw new QueryException(Code::FAIL, $e->getMessage());
        }

        return codeRender(Code::OK);
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

    public function initValue($data)
    {
        $init = [
            'business_area' => 0,
            'business_address' => '',
        ];
        foreach ($init as $key => $value) {
            $data[$key] = empty($data[$key]) ? $value : $data[$key];
        }
        return $data;
    }

    /**
     *
     * @api {get|post} /api/enterprise/saveLicense 保存营业执照
     * @apiVersion 1.0.0
     * @apiName SaveLicense
     * @apiGroup 运营端--企业用户管理
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {Number} id
     * @apiParam {String} business_license_url
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
    public function saveLicense(SaveLicenseRequest $request)
    {
        $data = [
            'id' => $request->input('id'),
            'business_license_url' => $request->input('business_license_url'),
        ];

        $this->repository->updateRepository($data);

        return codeRender(Code::OK);
    }


}