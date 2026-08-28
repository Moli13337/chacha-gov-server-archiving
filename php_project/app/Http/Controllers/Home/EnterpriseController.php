<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/6
 * Time: 11:00
 */

namespace App\Http\Controllers\Home;


use App\Common\Code;
use App\Exceptions\CodeException;
use App\Exceptions\QrCodeException;
use App\Exceptions\QueryException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\IndustryService;
use App\Http\Controllers\Service\QiChaChaService;
use App\Http\Controllers\Service\QrService;
use App\Http\Controllers\Service\TianYanService;
use App\Http\Requests\EmployeeOverview\HomeSupportListRequest;
use App\Http\Requests\Enterprise\SaveEnterpriseRequest;
use App\Repositories\Apply\ApplyChartRepository;
use App\Repositories\Apply\ApprovalRepository;
use App\Repositories\Enterprise\EnterpriseBusinessRepository;
use App\Repositories\Enterprise\EnterpriseIndustryRepository;
use App\Repositories\Enterprise\EnterpriseRepository;
use App\Repositories\Policy\PolicyRepository;
use App\Repositories\Steward\StewardUserRepository;
use App\Repositories\User\UserEnterpriseRelationRepository;
use App\Repositories\User\UserRepository;
use App\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Hcm\V20181106\Models\EvaluationRequest;
use TencentCloud\Ocr\V20181119\Models\BizLicenseOCRRequest;
use TencentCloud\Ocr\V20181119\OcrClient;
use Xkd\BusinessLicenseTransform\BusinessLicense;
use App\Repositories\Apply\ApplyRepository;

class EnterpriseController extends Controller
{

    protected $repository;
    protected $userRepository;
    protected $enterpriseIndustryRepository;
    protected $industryService;
    protected $enterpriseBusinessRepository;
    protected $userEnterpriseRelationRepository;
    protected $qiChaChaService;
    // 腾讯ocr这一步校验通过的是哪一个
    protected $passBiz = ['name' => 0, 'unified_credit_code' => 0];

    public function __construct(EnterpriseRepository $repository,
                                UserRepository $userRepository,
                                EnterpriseIndustryRepository $enterpriseIndustryRepository,
                                IndustryService $industryService,
                                EnterpriseBusinessRepository $enterpriseBusinessRepository,
                                UserEnterpriseRelationRepository $userEnterpriseRelationRepository,
                                QiChaChaService $qiChaChaService)
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->enterpriseIndustryRepository = $enterpriseIndustryRepository;
        $this->industryService = $industryService;
        $this->enterpriseBusinessRepository = $enterpriseBusinessRepository;
        $this->userEnterpriseRelationRepository = $userEnterpriseRelationRepository;
        $this->qiChaChaService = $qiChaChaService;
    }

    /**
     * FUNCTION_NAME : store
     * author : jp
     * 认证企业保存关联关系
     * @param SaveEnterpriseRequest $request
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws QueryException
     * @throws \Xkd\BusinessLicenseTransform\Exceptions\ClientException
     */
    public function store(SaveEnterpriseRequest $request)
    {
        $a = memory_get_usage();

        $param = $this->filter($request);
//        $param = $this->tencent($param);
        $param = $this->tencentV2($param);
//        $param = $this->qrReader($param);
        $enterprise = $this->check($param);
        $enterprise_id = $enterprise['id'];
        $update_enterprise = [
            'id' => $enterprise_id,
            'business_license_url' => $param['business_license_url'],
        ];
        if (empty($enterprise['tax_number'])) {
            $update_enterprise['tax_number'] = $param['unified_credit_code'];
        }
//        $b = memory_get_usage();
//        var_dump($b - $a);
//        exit;

        try {
            DB::beginTransaction();
            $this->repository->updateRepository($update_enterprise);
            $relation_data = [
                'user_id' => getLoginHome('id'),
                'enterprise_id' => $enterprise_id
            ];
            $this->userEnterpriseRelationRepository->storeRepository($relation_data);
            DB::commit();

        } catch (\Illuminate\Database\QueryException $e) {
            DB::roollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (QueryException $e) {
            DB::roollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }
        return codeRender(Code::OK);
    }

    /**
     * FUNCTION_NAME : check
     * author : jp
     * 检查企业认证的逻辑 并返回了企业信息
     * @param $param
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|mixed|\Symfony\Component\HttpFoundation\Response
     * @throws CodeException
     * @throws QueryException
     */
    public function check($param)
    {
        if ($this->passBiz['unified_credit_code']) {
            $enterprise = $this->repository->exist_credit($param['unified_credit_code']);
        } else {
            $enterprise = $this->repository->exist_name($param['name']);
        }
        if (empty($enterprise)) {
            $name = $this->passBiz['unified_credit_code'] ? $param['unified_credit_code'] : $param['name'];
            $enterprise = $this->saveEnterprise($name);
        }

        $err = [
            'name' => Code::ENTERPRISE_NAME_ERROR,
            'unified_credit_code' => Code::ENTERPRISE_CREDIT_ERROR,
            'legal_represent' => Code::ENTERPRISE_LEGAL_ERROR,
        ];

        foreach ($err as $k => $v) {
            if ($param[$k] != $enterprise[$k]) {
                throw new CodeException($v);
            }
        }
        $user_id = (int)getLoginHome('id');
        $relationExist = $this->userEnterpriseRelationRepository->relationByUser(['user_id' => $user_id]);
        if (!empty($relationExist)) {
            throw new CodeException(Code::ENTERPRISE_USER_EXIST_ERROR);
        }
        $enterprise_id = $enterprise['id'];
        $relationExist = $this->userEnterpriseRelationRepository->relationByEnterprise(['enterprise_id' => $enterprise_id]);
        if (!empty($relationExist)) {
            throw new CodeException(Code::ENTERPRISE_AUTH_FAIL_ERROR);
        }
        return $enterprise;
    }

    /**
     * FUNCTION_NAME : saveEnterprise
     * author : jp
     * 保存企业
     * @param $name
     * @return mixed
     * @throws CodeException
     * @throws QueryException
     */
    public function saveEnterprise($name)
    {
        $org = $this->qiChaChaService->getOrgDetail($name);
        if (empty($org)) {
            throw new CodeException(Code::ENTERPRISE_NOT_EXIST_ERROR);
        }
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
        $enterprise = array_only($org, $white);
        $res = $this->repository->storeRepository($enterprise);
        return $res->toArray();
    }

    public function applyDetail(Request $request)
    {
        $column = [
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
        ];
        $data = $this->userRepository->enterpriseDetail(getLoginHome('id'));

        if (empty($data)) {
            return codeRender(Code::OK, $data);
        }

        $data = Collection::filter($column, $data);
        $industry = $this->enterpriseIndustryRepository->getByEnterprise($data['id']);

        $industry_name = $this->industryService->getIndustryName($industry);

        $industry_key = [
            "first_industry_id",
            "second_industry_id",
            "third_industry_id",
            "fourth_industry_id",
            "first_industry_name",
            "second_industry_name",
            "third_industry_name",
            "fourth_industry_name",
        ];
        $data['industry'] = Collection::filter($industry_key,array_merge($industry, $industry_name));
        $data = arr2obj($data,'industry');
        unset($data['pivot']);
        // 合并申请表的信息
        $applyInfo = app(ApplyRepository::class)->detailByEnterpriseId([
        	'enterprise_id' => $data['id']
        ]);
        
        $data = array_merge($data, $applyInfo);
        return codeRender(Code::OK, $data);
    }

    public function tencentAI($param)
    {
        try {
            $content = BusinessLicense::getInfo('tencentai', $param['business_license_url']);
        } catch (\Exception $e) {
            throw new CodeException(Code::ENTERPRISE_BIZ_ERROR);

        }
        if (empty($content) || empty($content['data']['item_list'])) {
            throw new CodeException(Code::ENTERPRISE_BIZ_ERROR);
        }

        if (empty($content['data']['item_list'][2]['itemstring']) || $param['name'] != $content['data']['item_list'][2]['itemstring']) {
            throw new CodeException(Code::ENTERPRISE_NAME_ERROR);
        }

        if (empty($content['data']['item_list'][0]['itemstring']) || $param['unified_credit_code'] != $content['data']['item_list'][0]['itemstring']) {
            throw new CodeException(Code::ENTERPRISE_CREDIT_ERROR);
        }

        if (empty($content['data']['item_list'][1]['itemstring']) || $param['legal_represent'] != $content['data']['item_list'][1]['itemstring']) {
            throw new CodeException(Code::ENTERPRISE_LEGAL_ERROR);
        }
        $param['regist_address'] = empty($content['data']['item_list'][3]['itemstring']) ? '' : $content['data']['item_list'][3]['itemstring'];
        $param['business_term'] = empty($content['data']['item_list'][4]['itemstring']) ? '' : $content['data']['item_list'][4]['itemstring'];
        return $param;
    }

    public function tencent($param){
        try {
            $content = BusinessLicense::getInfo('tencent', $param['business_license_url']);

        } catch (\Exception $e) {
//            Log::error('tenxun ocr biz error.' . (array)$);
            throw new CodeException(Code::ENTERPRISE_BIZ_ERROR);
        }
        $key = 'items';
        if (empty($content) || empty($content['data'][$key])) {
            throw new CodeException(Code::ENTERPRISE_BIZ_ERROR);
        }
        $keyValue = [
            '公司名称' => 'name',
//            '法定代表人' => 'legal_represent'
            '注册号' => 'unified_credit_code'
        ];
        $qcCloud = [];
        foreach ($content['data'][$key] as $k => $v) {
            if (!empty($keyValue[$v['item']])) {
                $qcCloud[$keyValue[$v['item']]] = $v['itemstring'];
            }
        }

        if (empty($qcCloud['name']) || $param['name'] == $qcCloud['name']) {
            return $param;
        }

        if (empty($qcCloud['unified_credit_code']) || $param['unified_credit_code'] != $qcCloud['unified_credit_code']) {
            throw new CodeException(Code::ENTERPRISE_CREDIT_ERROR);
        }

//        if (empty($qcCloud['legal_represent']) || $param['legal_represent'] != $qcCloud['legal_represent']) {
//            throw new CodeException(Code::ENTERPRISE_LEGAL_ERROR);
//        }
        return $param;
    }

    /**
     * FUNCTION_NAME : tencentV2
     * author : jp
     * 暂未完成
     * @param $param
     */
    public function tencentV2($param)
    {
        $cred = new Credential(getenv('TENCENT_SECRET_ID'), getenv('TENCENT_SECRET_KEY'));
        $client = new OcrClient($cred, 'ap-shanghai');
        $req = new BizLicenseOCRRequest();
        $req->ImageUrl =  $param['business_license_url'];
        try {
            $resp = $client->BizLicenseOCR($req);
        } catch (\Exception $e) {
            Log::error('tencent biz error.' . $e->getMessage());
            throw new CodeException(Code::ENTERPRISE_BIZ_ERROR);
        }

        $qcCloud = [
            'name' => $resp->Name,
            'unified_credit_code' => $resp->RegNum,
        ];

        if (empty($qcCloud['name']) || $param['name'] == $qcCloud['name']) {
            $this->passBiz['name'] = 1;
            return $param;
        }

        if (empty($qcCloud['unified_credit_code']) || $param['unified_credit_code'] != $qcCloud['unified_credit_code']) {
            throw new CodeException(Code::ENTERPRISE_CREDIT_ERROR);
        }
        $this->passBiz['unified_credit_code'] = 1;
        return $param;
    }

    /**
     * FUNCTION_NAME : qrReader
     * author : jp
     * 解析营业执照二维码
     * @param $params
     * @return mixed
     * @throws CodeException
     */
    public function qrReader($params)
    {
        try {
            $text = app(QrService::class)->reader($params['business_license_url']);
        } catch (QrCodeException $e) {
            throw new CodeException(Code::ENTERPRISE_QR_READER_ERROR);
        }
        $name_reg = '/(名称：(.*)\r\n?)/u';
        $arr = [];
        preg_match($name_reg, $text, $arr);
        if (empty($arr[2]) || $params['name'] != $arr[2]) {
            throw new CodeException(Code::ENTERPRISE_NAME_ERROR);
        }
        return $params;
    }

    public function bindOfEnterprise(Request $request)
    {
        $column = [
            'id',
            'name',
            'unified_credit_code',
            'organization_code',
            'tax_number',
            'legal_represent',
            'business_license_url',
            'regist_capital',
            'regist_address',
            'regist_time',
            'business_address',
            'business_area',
        ];
        $user_id = (int)getLoginHome('id');
        $enterprise = $this->userRepository->enterpriseDetail($user_id);
        $enterprise = Collection::filter($column, $enterprise);

        if (!empty($enterprise)) {
            $industry = app(EnterpriseIndustryRepository::class)->getByEnterprise($enterprise['id']);
            $industry_name = app(IndustryService::class)->getIndustryName($industry);
            unset($enterprise['id']);
            $enterprise = array_merge($enterprise, $industry_name);
        }

        $steward = app(StewardUserRepository::class)->getSteward($user_id);

        $data = [
            'enterprise' => (object)$enterprise,
            'steward' => (object)$steward,
        ];
        return codeRender(Code::OK, $data);
    }

    public function overview(Request $request)
    {
        $enterprise = $this->userRepository->enterpriseDetail(getLoginHome('id'));
        if (empty($enterprise)) {
            return codeRender(Code::OK, []);
        }
        $params = ['enterprise_id' => $enterprise['id']];

        $currentYear = strtotime(date('Y').'0101');
        $currentEndYear = strtotime('+1 year', $currentYear)-1;
        $preYear = strtotime('-1 year', $currentYear);
        $preEndYear = $currentYear-1;
        $pre2Year = strtotime('-2 year', $currentYear);
        $pre2EndYear = $preYear - 1;
        // 今年
        $params['start_time'] = $currentYear;
        $params['end_time'] = $currentEndYear;
        $list = app(ApplyChartRepository::class)->list($params);
        unset($list['list']);
        $list['year'] = date('Y', $currentYear);
        $data[] = $list;

        // 上年
        $params['start_time'] = $preYear;
        $params['end_time'] = $preEndYear;
        $list = app(ApplyChartRepository::class)->list($params);
        unset($list['list']);
        $list['year'] = date('Y', $preYear);

        $data[] = $list;
        // 去年
        $params['start_time'] = $pre2Year;
        $params['end_time'] = $pre2EndYear;
        $list = app(ApplyChartRepository::class)->list($params);
        unset($list['list']);
        $list['year'] = date('Y', $pre2Year);
        $data[] = $list;
        return codeRender(Code::OK, $data);
    }

    public function supportList(HomeSupportListRequest $request)
    {
        $enterprise = $this->userRepository->enterpriseDetail(getLoginHome('id'));
        if (empty($enterprise)) {
            return codeRender(Code::OK, []);
        }
        $params = $this->filter($request);
        $params['enterprise_id'] = $enterprise['id'];
        $params['select_type'] = ENTERPRISE_CENTER_APPLY_LIST['support'];
        $data = app(ApplyChartRepository::class)->getApplyByEnterpriseIdV2($params);

        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }
        // 找拨款的公示
        $applyIds = array_column($data['data'], 'apply_id');
        $approval = app(ApprovalRepository::class)->getApprovalByApplyId($applyIds);
        $approvalId = array_column($approval, 'id');
        $approvalPolicy = [];
        if (!empty($approvalId)) {
            $policy = app(PolicyRepository::class)->getPolicyByTarget($approvalId, ['id', 'enc_id', 'target_id']);
            if (!empty($policy)) {
                $policy = array_column($policy, 'enc_id', 'target_id');
                foreach ($approval as $k => $v) {
                    $approvalPolicy[$v['apply_id']] = array_get($policy, $v['id'], '');
                }
            }
        }

        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]['id'] = array_get($approvalPolicy, $value['apply_id'], '');
        }

        return codeRender(Code::OK, $data);
    }

}