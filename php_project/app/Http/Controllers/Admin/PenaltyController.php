<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/8
 * Time: 17:28
 * 处罚 从enterprise库迁移数据到policy库
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\QiChaChaService;
use App\Models\Penalty\EnterpriseBaseModel;
use App\Repositories\Enterprise\CreditDepartmentRepository;
use App\Repositories\Enterprise\EnterpriseCreditRepository;
use App\Repositories\Enterprise\EnterpriseIndustryRepository;
use App\Repositories\Enterprise\EnterpriseRepository;
use App\Repositories\Enterprise\Penalty\EnterpriseBaseRepository;
use App\Repositories\Enterprise\Penalty\MigrateRepository;
use App\Repositories\Enterprise\Penalty\AjjPenaltyRepository;
use App\Repositories\Enterprise\Penalty\DsjPenaltyRepository;
use App\Repositories\Enterprise\Penalty\FgwPenaltyRepository;
use App\Repositories\Enterprise\Penalty\GajPenaltyRepository;
use App\Repositories\Enterprise\Penalty\GsjPenaltyRepository;
use App\Repositories\Enterprise\Penalty\HbjPenaltyRepository;
use App\Repositories\Enterprise\Penalty\JwPenaltyRepository;
use App\Repositories\Enterprise\Penalty\JxwPenaltyRepository;
use App\Repositories\Enterprise\Penalty\LyjPenaltyRepository;
use App\Repositories\Enterprise\Penalty\MzjPenaltyRepository;
use App\Repositories\Enterprise\Penalty\SfjPenaltyRepository;
use App\Repositories\Enterprise\Penalty\SjwPenaltyRepository;
use App\Repositories\Enterprise\Penalty\WsjPenaltyRepository;
use App\Repositories\IndustryRepository;
use Illuminate\Support\Facades\Log;

class PenaltyController extends Controller
{
    protected $enterpriseRepository;
    protected $enterpriseCreditRepository;
    protected $creditDepartmentRepository;
    protected $ajjPenaltyRepository;
    protected $dsjPenaltyRepository;
    protected $fgwPenaltyRepository;
    protected $gajPenaltyRepository;
    protected $gsjPenaltyRepository;
    protected $hbjPenaltyRepository;
    protected $jwPenaltyRepository;
    protected $jxwPenaltyRepository;
    protected $lyjPenaltyRepository;
    protected $mzjPenaltyRepository;
    protected $sfjPenaltyRepository;
    protected $sjwPenaltyRepository;
    protected $wsjPenaltyRepository;
    protected $migrateRepository;
    protected $class_first_id;
    protected $class_second_id;
    protected $qiChaChaService;

    // 行政处理迁移标识
    protected $flagArr = [];
    // 行政部门
    protected $department = [];
    // 一级行业
    protected $industry = [];

    public function __construct(EnterpriseRepository $enterpriseRepository,
                                EnterpriseCreditRepository $enterpriseCreditRepository,
                                CreditDepartmentRepository $creditDepartmentRepository,
                                MigrateRepository $migrateRepository, QiChaChaService $qiChaChaService)
    {
        $this->enterpriseRepository = $enterpriseRepository;
        $this->enterpriseCreditRepository = $enterpriseCreditRepository;
        $this->creditDepartmentRepository = $creditDepartmentRepository;
        $this->migrateRepository = $migrateRepository;
        $this->qiChaChaService = $qiChaChaService;

        $this->class_first_id = CREDIT_CLASS_FIRST_ID_DEFAULT;
        $this->class_second_id = CREDIT_CLASS_SECOND_ID_DEFAULT;
    }

    /**
     * FUNCTION_NAME : migrate
     * author : jp
     * 迁移
     */
    public function migrate()
    {
        // 取出一级行业

        $industry = app(IndustryRepository::class)->categoryIndustry(['id','category']);

        $this->industry = array_column($industry, 'id', 'category');

        // 这里的分类 只涉及行政处罚， 所以直接用数据的值 1-信用行为 2-行政处罚信息
        // 这里需要逐个扫描
        // 这里定义一个来保存要处理的行政处罚 其中 0 是企业基本信息 其余是 处罚信息
        $typeArr = [
            1 => app(AjjPenaltyRepository::class),
            2 => app(DsjPenaltyRepository::class),
            3 => app(FgwPenaltyRepository::class),
            4 => app(GajPenaltyRepository::class),
            5 => app(GsjPenaltyRepository::class),
            6 => app(HbjPenaltyRepository::class),
            7 => app(JwPenaltyRepository::class),
            8 => app(JxwPenaltyRepository::class),
            9 => app(LyjPenaltyRepository::class),
            10 => app(MzjPenaltyRepository::class),
            11 => app(SfjPenaltyRepository::class),
            12 => app(SjwPenaltyRepository::class),
            13 => app(WsjPenaltyRepository::class),
        ];

        // 对应的部门id
        $this->department = [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
            6 => 6,
            7 => 7,
            8 => 8,
            9 => 9,
            10 => 10,
            11 => 11,
            12 => 12,
            13 => 13,
        ];
        activity()->disableLogging();

        // 扫描企业
        $this->migrateEnterprise();

        foreach ($typeArr as $k => $v) {
            $this->flagArr[$k] = true;
        }
        foreach ($typeArr as $key => $value) {
            do {
                $this->penalty($key, $value);
            } while($this->flagArr[$key]);
        }
    }

    /**
     * FUNCTION_NAME : penalty
     * author : jp
     * 行政处罚
     * @param $type
     * @param $penalty
     */
    public function penalty($type, $penalty)
    {
        $limit = 100;
        $data = $penalty->listPenalty($type, $limit);
        if (empty($data)) {
            $this->flagArr[$type] = false;
            return;
        }

        if ($type == 5) {
            $idnoName = 'CF_XDR_SHXYM';
            // 工商局需要特殊处理 字段不一致, 工商局的是5
            $column = [
                'REGISTER_NO' => 'register_no', // 关联原数据的唯一编号
                'CF_WSH' => 'punish_number', // 文号
                'CF_JDRQ' => 'decision_date', // 原数据是时间格式 转成时间戳
                'CF_WFXW' => 'item', // 处罚事项 / 违法行为
                'CF_NR' => 'content', // 处罚内容
            ];
        } else {
            $idnoName = 'IDNO';
            // 工商局需要特殊处理 字段不一致, 工商局的是5
            $column = [
                'REGISTER_NO' => 'register_no', // 关联原数据的唯一编号
                'WRIT_NO' => 'punish_number', // 文号
                'PENALIZE_DATE' => 'decision_date', // 原数据是时间格式 转成时间戳
                'PUNISH_NAME' => 'item', // 处罚事项
                'PENALIZE_CONTENT' => 'content', // 处罚内容
            ];
        }

        // 这里因为必须拿先处理企业基础表，所以这里不再处理企业的新增
        // 处理存在的企业
        $id_no = array_filter(array_column($data, $idnoName));
        $enterprise = $this->enterpriseRepository->getByUnified($id_no);

        $enterprise = array_column($enterprise, 'id', 'unified_credit_code');

        $insert = [];
        $migrate = [];
        $time = returnCreatedUpdatedAt();


        foreach ($data as $key => $value) {
            $tmp = [];
            // 处理特殊 有可能有个人
            if (empty($value['REGISTER_NO']) || empty($value['IDNO']) || empty($enterprise[$value['IDNO']])) {
                $migrate[] = array_merge([
                    'register_no' => $value['REGISTER_NO'],
                    'type' => $type,
                ], $time);
                continue;
            }
            $migrate[] = array_merge([
                'register_no' => $value['REGISTER_NO'],
                'type' => $type,
            ], $time);
            $tmp['enterprise_id'] = $enterprise[$value[$idnoName]];
            $tmp['class_first_id'] = $this->class_first_id;
            $tmp['class_second_id'] = $this->class_second_id;
            $tmp['department_id'] = $this->department[$type];
            $tmp = array_merge($tmp, $time);
            foreach ($column as $k => $v) {
                $tmp[$v] = $value[$k]??'';
            }
            $tmp['decision_date'] = empty($tmp['decision_date']) ? 0 :  strtotime(str_replace('.000000000', '', $tmp['decision_date']));
            $insert[] = $tmp;
        }

        if (!empty($insert)) {
            $this->enterpriseCreditRepository->storeBatch($insert);
        }
        if (!empty($migrate)) {
            $this->migrateRepository->storeBatch($migrate);
        }

    }

    /**
     * FUNCTION_NAME : createEnterprise
     * author : jp
     * 创建企业
     * @param $data
     */
    public function createEnterprise($data, $type)
    {
        // 工商局的不一致
        if ($type == 5) {

            $idnoName = 'CF_XDR_SHXYM';
            $id_no = array_column($data, $idnoName);
             $column = [
                 'CF_XDR_MC' => 'name', // 名称
                 'CF_XDR_SHXYM' => 'unified_credit_code', // 统一信用代码
                 'CF_XDR_ZZJG' => 'organization_code', // 组织机构代码
                 'CF_FRDB' => 'legal_represent', // 法人
             ];
        } else {
            $idnoName = 'IDNO';
            $id_no = array_column($data, $idnoName);
            $column = [
                'PT_NAME' => 'name', // 名称
                'IDNO' => 'unified_credit_code', // 统一信用代码
                'INST_CODE' => 'organization_code', // 组织机构代码
                'GUOSJ_TAXPAYER_NO' => 'tax_number', // 税号
                'REPRESENTATIVE_NAME' => 'legal_represent', // 法人
            ];
        }
        $enterprise = $this->enterpriseRepository->getByUnified($id_no);
        $insert = [];
        $exist = array_column($enterprise,'unified_credit_code');
        $time = returnCreatedUpdatedAt();
        $already = [];
        foreach ($data as $key => $value) {
            if (empty($value[$idnoName]) || in_array($value[$idnoName], $exist)) {
                continue;
            }

            if (in_array($value[$idnoName], $already)) {
                continue;
            }
            $tmp = [];

            foreach ($column as $k => $v) {
                $tmp[$v] = $value[$k]??'';
            }

            if (empty($tmp)) {
                continue;
            }
            // 处理三税号 组织机构代码
            if (empty($tmp['tax_number'])) {
                $tmp['tax_number'] = $tmp['unified_credit_code'];
            }

            if (empty($tmp['organization_code'])) {
                $tmp['organization_code'] = substr($tmp['unified_credit_code'], 8, 9);
            }

            $insert[] = array_merge($tmp, $time);
            $already[] = $value['IDNO'];
        }

        if (empty($insert)){
            return;
        }
        $this->enterpriseRepository->storeBatch($insert);
    }

    /**
     * FUNCTION_NAME : migrateEnterprise
     * author : jp
     * 迁移企业
     */
    public function migrateEnterprise()
    {
        do {
            $flag = $this->deal();
        } while($flag);
    }

    /**
     * FUNCTION_NAME : deal
     * author : jp
     * 处理企业
     * @return bool
     * @throws \App\Exceptions\QueryException
     */
    protected function deal()
    {
        // 处理10条
        $limit = 10;

        $data = app(EnterpriseBaseRepository::class)->list(0, $limit);
        if (empty($data)) {
            return false;
        }
        $time = returnCreatedUpdatedAt();
        $this->createEnterpriseAndIndustry($data);

        $migrate = [];
        foreach ($data as $key => $value)
        {
            $migrate[] = array_merge([
                'register_no' => $value['REGISTER_NO'],
                'type' => 0,
            ], $time);
        }
        if (!empty($migrate)) {
            $this->migrateRepository->storeBatch($migrate);
        }
        return true;
    }

    /**
     * FUNCTION_NAME : createEnterpriseAndIndustry
     * author : jp
     * 单独处理因为这里需要新增 企业 和行业的关系
     * @param $data
     * @throws \App\Exceptions\QueryException
     */
    public function createEnterpriseAndIndustry($data)
    {
        $id_no = array_column($data, 'IDNO');

        $enterprise = $this->enterpriseRepository->getByUnified($id_no);

        $exist = array_column($enterprise,'unified_credit_code');
        $column = [
            'PT_NAME' => 'name', // 名称
            'IDNO' => 'unified_credit_code', // 统一信用代码
            'INST_CODE' => 'organization_code', // 组织机构代码
            'GUOSJ_TAXPAYER_NO' => 'tax_number', // 税号
            'REPRESENTATIVE_NAME' => 'legal_represent', // 法人
            'EST_DATE' => 'regist_time', // 注册时间
            'DOMICILE' => 'regist_address', // 注册地址
            'REG_CAP' => 'regist_capital', // 注册资本 单位万元
            'IND_CLASS' => 'industry', // 行业 一级分类
        ];
        $time = returnCreatedUpdatedAt();
        $already = [];
        $insert = [];
        $tmp_industry = [];
        foreach ($data as $key => $value) {
            // 统一信用代码 不存在，存在
            if (empty($value['IDNO']) || in_array($value['IDNO'], $exist)) {
                continue;
            }
            if (in_array($value['IDNO'], $already)) {
                continue;
            }
            $tmp = [];

            foreach ($column as $k => $v) {
                $tmp[$v] = $value[$k]??'';
            }

            if (empty($tmp)) {
                continue;
            }
            // 处理三税号 组织机构代码
            if (empty($tmp['tax_number'])) {
                $tmp['tax_number'] = $tmp['unified_credit_code'];
            }
            $tmp['organization_code'] = substr($tmp['unified_credit_code'], 8, 9);

            if (empty($tmp['regist_capital'])) {
                $tmp['regist_capital'] = 0;
            }

            if (!empty($tmp['regist_time'])) {
                $tmp['regist_time'] = strtotime($tmp['regist_time']);
            } else {
                $tmp['regist_time'] = 0;
            }

            // TODO 企查查接口的 频率限制 ，联系技术在做决断
            // 处理法人的问题 只有生产环境才进行请求企查查
            if (empty($tmp['legal_represent']) && env('APP_ENV', '') == 'production') {
                $org = $this->qiChaChaService->getOrgDetail($tmp['name']);
                $tmp['legal_represent'] = array_get($org, 'legal_represent', '');
            }

            $already[] = $value['IDNO'];
            $tmp_industry[] = ['unified_credit_code' => $tmp['unified_credit_code'] , 'industry' => $tmp['industry']];
            unset($tmp['industry']);
            $insert[] = array_merge($tmp, $time);
        }
        if (empty($insert)) {
            return;
        }
        $res = $this->enterpriseRepository->storeBatch($insert);
        $id_no = array_column($tmp_industry, 'unified_credit_code');
        $enterprise = $this->enterpriseRepository->getByUnified($id_no, ['id', 'unified_credit_code']);
        $exist = array_column($enterprise,'id','unified_credit_code');

        // 处理企业的行业 只处理第一级
        $insertIndustry = [];
        foreach ($tmp_industry as $k => $v) {
            $enterprise_id = array_get($exist, $v['unified_credit_code'], 0);
            if (!empty($enterprise_id) && !empty($tmp_industry) && !empty($this->industry[$v['industry']])) {
                $insertIndustry[] = array_merge( [
                    'enterprise_id' => $enterprise_id,
                    'first_industry_id' => $this->industry[$v['industry']]
                ], $time);
            }
        }

        if (!empty($insertIndustry)) {
            app(EnterpriseIndustryRepository::class)->storeBatch($insertIndustry);
        }
    }
}