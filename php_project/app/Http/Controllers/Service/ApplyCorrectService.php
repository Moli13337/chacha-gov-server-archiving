<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/15
 * Time: 16:48
 */

namespace App\Http\Controllers\Service;


use App\Common\Code;
use App\Events\ApplyFormPdfCreate;
use App\Events\ApplyPdfCreate;
use App\Events\ZipCreate;
use App\Exceptions\CodeException;
use App\Exceptions\QueryException;
use App\Models\ApplyCorrectFileModel;
use App\Models\ApplyEconomyModel;
use App\Repositories\Apply\ApplyCorrectContentRepository;
use App\Repositories\Apply\ApplyCorrectFileRepository;
use App\Repositories\Apply\ApplyCorrectRepository;
use App\Repositories\Apply\ApplyFileExceptionRepository;
use App\Repositories\Apply\ApplyFileRepository;
use App\Repositories\Apply\ApplyPdfRepository;
use App\Repositories\Apply\ApplyRepository;
use App\Repositories\Apply\ApprovalDepartmentRepository;
use App\Repositories\Apply\ApprovalRepository;
use App\Repositories\Staff\StaffDepartmentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApplyCorrectService extends BaseService
{

    protected  $repository;
    protected  $correctContentRepository;
    protected  $correctFileRepository;

    public $industry_segmenter = '|';

    public function __construct(ApplyCorrectRepository $repository,
                                ApplyCorrectContentRepository $correctContentRepository,
                                ApplyCorrectFileRepository $correctFileRepository)
    {
        $this->repository = $repository;
        $this->correctContentRepository = $correctContentRepository;
        $this->correctFileRepository = $correctFileRepository;
    }

    /**
     * FUNCTION_NAME : saveCorrect
     * author : jp
     * 主审部门发起订正资料
     * @param $params
     * @return mixed
     * @throws CodeException
     * @throws QueryException
     */
    public function saveCorrect($params)
    {
        $staff = app(ApprovalDepartmentRepository::class)->getStaff(DEPARTMENT_TYPE['one'], STAFF_OPERTOR_TYPE['one']);
        if (empty($staff)) {
            throw new CodeException(Code::APPROVAL_STAFF_EXIST_ERROR);
        }

        $origin = app(ApplyRepository::class)->detailApply(['id' => $params['apply_id']]);

        $saveData = [
            'apply_id' => $params['apply_id'],
            'approval_id' => $params['approval_id'],
            'department_id' => $params['approval_department_id'],
            CREATED_STAFF_ID => (int)getLoginStaff('id'),
            'origin_content' => json_encode($origin, JSON_UNESCAPED_UNICODE),
            'mark' => $params['mark']

        ];
        // 查询是否有补充资料， 如果有 需要在订正资料的时候 可以修改
//        $hasMaterial = app(ApplyRepository::class)->hasMaterial($params['apply_id']);
        $hasMaterial = app(ApplyFileRepository::class)->haveDefault($params['apply_id']);
        $saveData['has_material'] = empty($hasMaterial) ? 0 : 1;
        $params['department_id'] = $params['approval_department_id'];
        $params['department_name'] = $params['approval_department_name'];
        DB::beginTransaction();
        try {
            $res = $this->repository->saveCorrect($saveData);
            $params['approval_id'] = $res['id'];
            $this->sendMessage($params, APPLY_CORRECT_STATUS['one'], $staff);
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }

        $this->sendSms($params, $staff, SMS_TEMPLATE['twentyseven']);
        return $res;
    }

    /**
     * FUNCTION_NAME : agree
     * author : jp
     * 同意
     * @param $params
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws CodeException
     * @throws QueryException
     */
    public function agree($params)
    {
        $data = [
            'status' => APPLY_CORRECT_STATUS['three'],
            'agree_time' => time(),
            'id' => $params['id']
        ];

        $correct = $this->repository->detail($params['id']);
        $department_id = $correct['department_id'];

        // 提出申请部门操作员
        $staff = app(ApprovalDepartmentRepository::class)->getStaffByDepartment($department_id);
        if (empty($staff)) {
            throw new CodeException(Code::APPROVAL_STAFF_EXIST_ERROR);
        }

        if ($correct['status'] != APPLY_CORRECT_STATUS['one']) {
            throw new CodeException(Code::APPLY_CORRECT_OPERATOR_STATUS_ERROR);
        }

        DB::beginTransaction();
        try {
            $res = $this->repository->updateRepository($data);
            // 部门
            $staffMessage = $correct['apply'];
            $staffMessage['approval_id'] = $params['id'];
            $staffMessage['department_name'] = $correct['department']['name'];
            $this->sendMessage($staffMessage, APPLY_CORRECT_STATUS['three'], $staff);
            // 用户
            $userMessage = $correct['apply'];
            $userMessage['mark'] = $correct['mark'];
            $userMessage['apply_id'] = $userMessage['id'];
            $this->sendMessageUser($userMessage, APPLY_CORRECT_STATUS['three'], []);
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }

        $this->sendSms($staffMessage, $staff, SMS_TEMPLATE['twentyeight']);
        $user = array_get($correct, 'user', []);
        $this->sendSms($userMessage,  $user, SMS_TEMPLATE['thirtyone']);
        return $res;
    }

    /**
     * FUNCTION_NAME : disagree
     * author : jp
     * 不批准
     * @param $params
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws CodeException
     * @throws QueryException
     */
    public function disagree($params)
    {
        $data = [
            'status' => APPLY_CORRECT_STATUS['two'],
            'id' => $params['id']
        ];

        $correct = $this->repository->detail($params['id']);
        $department_id = $correct['department_id'];
        // 提出申请部门操作员
        $staff = app(ApprovalDepartmentRepository::class)->getStaffByDepartment($department_id);
        if (empty($staff)) {
            throw new CodeException(Code::APPROVAL_STAFF_EXIST_ERROR);
        }

        if ($correct['status'] != APPLY_CORRECT_STATUS['one']) {
            throw new CodeException(Code::APPLY_CORRECT_OPERATOR_STATUS_ERROR);
        }

        DB::beginTransaction();
        try {
            $res = $this->repository->updateRepository($data);
            // 部门
            $staffMessage = $correct['apply'];
            $staffMessage['approval_id'] = $params['id'];
            $staffMessage['department_name'] = $correct['department']['name'];
            $this->sendMessage($staffMessage, APPLY_CORRECT_STATUS['two'], $staff);
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }

        $this->sendSms($staffMessage, $staff, SMS_TEMPLATE['twentyeight']);
        return $res;
    }

    // 审核通过
    public function pass($params)
    {
        $data = [
            'audit_time' => time(),
            'status' => APPLY_CORRECT_STATUS['seven'],
            'audit_staff_id' => (int)getLoginStaff('id'),
            'id' => $params['id']
        ];

        $correct = $this->correctDetail($params['id']);
        if (empty($correct)) {
            throw new CodeException(Code::PARAM_ERROR);
        }

        if ($correct['status'] == APPLY_CORRECT_STATUS['three']) {
            throw new CodeException(Code::APPLY_CORRECT_OPERATOR_PASS_ERROR);
        } elseif ($correct['status'] != APPLY_CORRECT_STATUS['four']) {
            throw new CodeException(Code::APPLY_CORRECT_OPERATOR_STATUS_ERROR);
        }

        $department_id = $correct['department_id'];
        $apply_id = $correct['apply_id'];
        // 提出申请部门操作员
        $staff = app(ApprovalDepartmentRepository::class)->getStaffByDepartment($department_id);
        if (empty($staff)) {
            throw new CodeException(Code::APPROVAL_STAFF_EXIST_ERROR);
        }

        // 找出变更的
        $baseChange = array_only($correct['change'], $this->getChangeColumn());
        $economyChange = array_get($correct['change'], 'economy_list', []);
        $fileChange = array_get($correct, 'change_file', []);

        $saveFile = [];
        $deleteFile = [];
        $is_check = APPLY_CORRECT_IS_CHECK['no'];
        if (!empty($fileChange)) {
            $time = time();
            $onlyFileColumn = [
                'apply_id',
                'file_name',
                'file_url',
                'file_type',
                'project_materials_id',
                'created_at',
                'created_at',
            ];
            foreach ($fileChange as $kf =>$vf) {
                if ($vf['correct_type'] == APPLY_CORRECT_FILE_TYPE['deleted']) {
                    $deleteFile[] = $vf['file_id'];
                } elseif ($vf['correct_type'] == APPLY_CORRECT_FILE_TYPE['created']) {
                    $saveFile[] = array_only($vf, $onlyFileColumn);
                    if ($vf['file_type'] == MATERIALS_TYPE['invoice']) {
                        // 发票需要做检查
                        $is_check = APPLY_CORRECT_IS_CHECK['yes'];
                    }
                }
            }
        }

        $data['is_check'] = $is_check;

        // 添加pdf 的变更
        $baseChange['business_id'] = businessId();
        $baseChange['pdf_url'] = '';

        DB::beginTransaction();
        try {
            $res = $this->repository->updateRepository($data);
            if (!empty($baseChange)) {
                app(ApplyRepository::class)->updateApplyById($apply_id, $baseChange);
            }

            if (!empty($deleteFile)) {
                app(ApplyFileRepository::class)->deleteByIdsAndApply($apply_id, $deleteFile);
                app(ApplyFileExceptionRepository::class)->refreshApplyFile($deleteFile);
            }

            if (!empty($saveFile)) {
                app(ApplyFileRepository::class)->storeBatchRepository($saveFile);
            }

            if (!empty($economyChange)) {
                app(ApplyEconomyModel::class)->where('apply_id', $apply_id)->delete();
                app(ApplyEconomyModel::class)->insert($economyChange);
            }

            // 部门
            $staffMessage = $correct['apply'];
            $staffMessage['approval_id'] = $params['id'];
            $staffMessage['department_name'] = $correct['department']['name'];
            $this->sendMessage($staffMessage, APPLY_CORRECT_STATUS['seven'], $staff);
            // 用户
            $userMessage = $correct['apply'];
            $userMessage['apply_id'] = $userMessage['id'];
            $this->sendMessageUser($userMessage, APPLY_CORRECT_STATUS['seven'], []);
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }

        $this->sendSms($staffMessage, $staff, SMS_TEMPLATE['twentynine']);
        $user = array_get($correct, 'user', []);
        $this->sendSms($userMessage, $user, SMS_TEMPLATE['thirtythree']);

        $detail = app(ApprovalRepository::class)->detail(['id' => $apply_id]);

        app(ApplyPdfRepository::class)->pdfCreate($detail);
        app(ApplyRepository::class)->zipCreate($detail);
        event(new ApplyFormPdfCreate($detail));

        return $res;
    }

    // 重新订正
    public function again($params)
    {
        $data = [
            'audit_time' => time(),
            'audit_staff_id' => (int)getLoginStaff('id'),
            'status' => APPLY_CORRECT_STATUS['five'],
            'invalid_mark' => array_get($params, 'mark', ''),
            'id' => $params['id']
        ];

        $correct = $this->repository->detail($params['id']);

        if ($correct['status'] == APPLY_CORRECT_STATUS['three']) {
            throw new CodeException(Code::APPLY_CORRECT_OPERATOR_PASS_ERROR);
        } elseif ($correct['status'] != APPLY_CORRECT_STATUS['four']) {
            throw new CodeException(Code::APPLY_CORRECT_OPERATOR_STATUS_ERROR);
        }
        $department_id = $correct['department_id'];
        // 提出申请部门操作员
        $staff = app(ApprovalDepartmentRepository::class)->getStaffByDepartment($department_id);
        if (empty($staff)) {
            throw new CodeException(Code::APPROVAL_STAFF_EXIST_ERROR);
        }

        // 重新订正 需要将旧的作废 新生成一条新的
        $keys = [
            'apply_id',
            'approval_id',
            'department_id',
        ];
        $new = array_only($correct, $keys);
        $new['mark'] = $params['mark'];
        $new['source_id'] = $params['id'];
        $new['status'] = APPLY_CORRECT_STATUS['three'];
        $new[CREATED_STAFF_ID] = (int)getLoginStaff('id');

        DB::beginTransaction();
        try {
            $this->repository->updateRepository($data);
            $res = $this->repository->saveCorrect($new);
            // 用户
            $userMessage = $correct['apply'];
            $userMessage['apply_id'] = $userMessage['id'];
            $userMessage['mark'] = $params['mark'];
            $this->sendMessageUser($userMessage, APPLY_CORRECT_STATUS['six'], []);
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }

        $user = array_get($correct, 'user', []);
        $this->sendSms($userMessage, $user, SMS_TEMPLATE['thirtytwo']);
    }

    // 作废
    public function invalid($params)
    {
        $data = [
            'audit_time' => time(),
            'audit_staff_id' => (int)getLoginStaff('id'),
            'status' => APPLY_CORRECT_STATUS['five'],
            'invalid_mark' => array_get($params, 'mark'),
            'id' => $params['id']
        ];

        $correct = $this->repository->detail($params['id']);

        if (!in_array($correct['status'], [APPLY_CORRECT_STATUS['three'], APPLY_CORRECT_STATUS['four']])) {
            throw new CodeException(Code::APPLY_CORRECT_OPERATOR_STATUS_ERROR);
        }
        $department_id = $correct['department_id'];
        // 提出申请部门操作员
        $staff = app(ApprovalDepartmentRepository::class)->getStaffByDepartment($department_id);
        if (empty($staff)) {
            throw new CodeException(Code::APPROVAL_STAFF_EXIST_ERROR);
        }

        DB::beginTransaction();
        try {
            $res = $this->repository->updateRepository($data);
            // 部门
            $staffMessage = $correct['apply'];
            $staffMessage['approval_id'] = $params['id'];
            $staffMessage['department_name'] = $correct['department']['name'];
            $this->sendMessage($staffMessage, APPLY_CORRECT_STATUS['eight'], $staff);
            // 用户
            $userMessage = $correct['apply'];
            $userMessage['apply_id'] = $userMessage['id'];
            $this->sendMessageUser($userMessage, APPLY_CORRECT_STATUS['eight'], []);
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }

        $this->sendSms($staffMessage, $staff, SMS_TEMPLATE['thirtyfour']);
        $user = array_get($correct, 'user', []);
        $this->sendSms($userMessage, $user, SMS_TEMPLATE['thirtyfive']);
    }

    /**
     * FUNCTION_NAME : sendMessage
     * author : jp
     * 发送站内信 部门
     * @param $arr
     * @param $setup
     * @param $user
     */
    protected function sendMessage($arr, $setup, $user)
    {
        $type = 0;
        $data = [
            'enterprise_name' => $arr['enterprise_name'],
            'project_name' => $arr['project_name'],
            'department_name' => array_get($arr, 'department_name', ''),
            'approval_id' => $arr['approval_id'],
        ];
        switch ($setup) {
            case APPLY_CORRECT_STATUS['one']:
                $type = APPROVAL_MESSAGE_CONTENT['twentyseven'];
                break;
            case APPLY_CORRECT_STATUS['two']:
                $data['audit_name'] = array_get(trans('constant.apply_correct_agree_status'), APPLY_CORRECT_STATUS['two'],'');
                $type = APPROVAL_MESSAGE_CONTENT['twentyeight'];
                break;
            case APPLY_CORRECT_STATUS['three']:
                $data['audit_name'] = array_get(trans('constant.apply_correct_agree_status'), APPLY_CORRECT_STATUS['three'],'');
                $type = APPROVAL_MESSAGE_CONTENT['twentyeight'];
                break;
            case APPLY_CORRECT_STATUS['four']:
                $type = APPROVAL_MESSAGE_CONTENT['thirtysix'];
                break;
            case APPLY_CORRECT_STATUS['eight']:
                $type = APPROVAL_MESSAGE_CONTENT['thirtyfour'];
                break;
            case APPLY_CORRECT_STATUS['seven']:
                $type = APPROVAL_MESSAGE_CONTENT['twentynine'];
                break;
        }
        $data = array_merge($data, $user);

        app(ApprovalRepository::class)->sendMessage($data, $type);

    }

    protected function sendMessageUser($arr, $setup, $user = [])
    {
        $type = 0;
        $data = $arr;
        switch ($setup) {
            case APPLY_CORRECT_STATUS['three']:
                $type = APPROVAL_MESSAGE_CONTENT['thirtyone'];
                break;
            case APPLY_CORRECT_STATUS['eight']:
                $type = APPROVAL_MESSAGE_CONTENT['thirtyfive'];
                break;
            case APPLY_CORRECT_STATUS['six']:
                $type = APPROVAL_MESSAGE_CONTENT['thirtytwo'];
                break;
            case APPLY_CORRECT_STATUS['seven']:
                $type = APPROVAL_MESSAGE_CONTENT['thirtythree'];
                break;
        }
        app(ApprovalRepository::class)->sendMessage($data, $type);

    }

    /**
     * FUNCTION_NAME : sendSms
     * author : jp
     * 发送短信
     * @param $arr
     * @param $user
     * @param $type
     */
    protected function sendSms($arr, $user, $type)
    {
        $data = [
            'enterprise_name' => array_get($arr,'enterprise_name',''),
            'project_name' => array_get($arr,'project_name',''),
            'department_name' => array_get($arr,'approval_department_name',''),
        ];
        $data['mobile'] = $user['mobile']??'';
        app(ApprovalRepository::class)->sendSms($data, $type);
    }

    /***用户订正**/

    public function clientDetail($arr)
    {
        $detail = app(ApplyRepository::class)->detail(['id' => $arr['id']]);
        if (empty($detail)) {
            return [];
        }
        $correct = $this->repository->getCorrectWaitByApply([$detail['id']]);
        if (empty($correct)) {
            return [];
        }

        $hasCorrect = empty($correct) ? false : true;
        $detail['has_correct'] = $hasCorrect;
        if ($hasCorrect) {
            $hasMaterial = empty( $correct['has_material']) ? false : true;
        } else {
            $hasMaterial = empty( $detail['has_material']) ? false : true;;
        }

        // 附件
        $detail['has_material'] = $hasMaterial;
        foreach ($detail['config'] as $key => $value) {
            if ($value['type'] == MATERIALS_TYPE['default'] && !$hasMaterial) {
                $value['file_list'] = [];
                $detail['config'][$key] = $value;
                break;
            }
        }

        $where = [
            'apply_id' => $detail['id'],
            'correct_id' => $correct['id'],
        ];
        $contents = app(ApplyCorrectContentRepository::class)->getContent($where);
        $files = app(ApplyCorrectFileRepository::class)->getContent($where);
        $applyId = $detail['id'];

        foreach ($contents as $kc => $vc) {
            if ($vc['setup'] == APPLY_SUBMIT_SETUP['two']) {
                $vc['content']['economy_list'] = $this->dealEconomy(array_get($vc['content'], 'economy_list', []), $applyId);
                $detail = array_merge($detail, $vc['content']);
            } elseif ($vc['setup'] == APPLY_SUBMIT_SETUP['three']) {
                $detail = array_merge($detail, $vc['content']);
            } elseif ($vc['setup'] == APPLY_SUBMIT_SETUP['four']) {
                $detail['config'] = $this->dealDetailFile($detail['config'], $files);
            }
        }

        return $detail;
    }

    /**
     * FUNCTION_NAME : dealDetailFile
     * author : jp
     * 处理详情的附件显示
     * @param $config
     * @param $files
     * @return mixed
     */
    public function dealDetailFile($config, $files)
    {
        if (empty($files)) {
            return $config;
        }

        $fileConfig = $config;
        foreach ($fileConfig as $kf => $vf) {
            if (!empty($vf['file_list'])) {
                foreach ($vf['file_list'] as $kfl => $vfl) {
                    if (isset($vfl['create_at'])) {
                        $fileConfig[$kf]['file_list'][$kfl]['created_at'] = $vfl['create_at'];
                        unset( $fileConfig[$kf]['file_list'][$kfl]['create_at']);
                    }
                }
            }
        }

        $resultFile = [];
        $tmpKey = [
            'file_id',
            'apply_id',
            'correct_id',
            'file_name',
            'file_url',
            'file_type',
            'file_type',
            'project_materials_id',
        ];
        foreach ($files as $k => $v) {
            if ($v['correct_type'] ==  APPLY_CORRECT_FILE_TYPE['created'] || $v['correct_type'] ==  APPLY_CORRECT_FILE_TYPE['no_change']) {
                $tmp = array_only($v, $tmpKey);
                $tmp['id'] = $tmp['file_id'];
                unset($tmpKey['file_id']);
                $resultFile[] = $tmp;
            }
        }

        if (!empty($resultFile)) {
            foreach ($fileConfig as $key => $value) {
                $tmpList = [];
                foreach ($resultFile as $key2 => $value2) {
                    if ($value['id'] === $value2['project_materials_id']) {
                        $tmpList[] = $value2;
                        unset($resultFile[$key2]);
                    }
                }

                $value['file_list'] = $tmpList;
                $fileConfig[$key] = $value;
            }
        }

        return $fileConfig;
    }

    public function dealEconomy($economy, $applyId)
    {
        if (empty($economy)) {
            return [];
        }
        $year = array_unique(array_filter(array_column($economy, 'year')));
        $year = array_sort($year);
        $year = array_flip($year);
        $data = [];
        foreach ($economy as $key => $v) {
            $tmpKey = array_get($year, $v['year'], null);
            if (blank($tmpKey) || !is_numeric($tmpKey)) {
                continue;
            }
            $value['apply_id'] = $applyId;
            $data[$tmpKey]['year'] = $v['year'];
            $data[$tmpKey]['content_list'][] = $v;
        }
        return $data;
    }

    // 保存订正内容
    public function correctContent($arr, $type)
    {
        // 判断是否能进行保存的操作
        $applyId = $arr['id'];
        $flag = $this->repository->allowSubmit($applyId);
        if (!$flag) {
            throw new CodeException(Code::APPLY_CORRECT_USER_SAVE_ERROR);
        }
        $correct = $this->repository->detailByApply($applyId);
        $has_material = $correct['has_material'];
        $correct_id = $correct['id'];
        $staff = app(ApprovalDepartmentRepository::class)->getStaff(DEPARTMENT_TYPE['one'], STAFF_OPERTOR_TYPE['one']);

        try {
            DB::beginTransaction();
            switch ($type) {
                case APPLY_SUBMIT_SETUP['two']:
                    $this->storeContent($arr,$correct_id, $type);
                    break;
                case APPLY_SUBMIT_SETUP['three']:
                    $this->storeContent($arr, $correct_id,$type);
                    break;
                case APPLY_SUBMIT_SETUP['four']:
                    $this->storeFile($arr,$applyId,$correct_id, $type, $has_material);
                    break;
            }
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            throw new CodeException(Code::DB_ERROR, $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            throw new CodeException(Code::DB_ERROR, $e->getMessage());
        }

        if (in_array($type, [APPLY_SUBMIT_SETUP['four']]) && !empty($staff)) {
            $staffMessage = $correct['apply'];
            $staffMessage['approval_id'] = $correct_id;
            $staffMessage['department_name'] = $correct['department']['name'];
            try {
                $this->sendMessage($staffMessage, APPLY_CORRECT_STATUS['seven'], $staff);
                $this->sendSms($staffMessage, $staff, SMS_TEMPLATE['thirtysix']);
            } catch (\Exception $e) {
                Log::error('user correct content file error: '.$e->getMessage());
            }
        }
        return true;
    }

    // 企业基本信息
    protected function storeContent($arr,$correct_id, $setup)
    {
        $content = json_encode($arr, JSON_UNESCAPED_UNICODE);
        $data = [
            'content' => $content,
            'user_id' => (int)getLoginHome('id'),
            'setup' => $setup,
        ];

        $where = [
            'apply_id' => $arr['id'],
            'correct_id' => $correct_id,
            'setup' => $setup,
        ];
        $this->correctContentRepository->selfUpdateOrCreate($where, $data);
    }

    // 存储附件
    protected function storeFile($arr, $applyId, $correct_id, $setup, $has_material)
    {
        $fileList = empty($arr['file_list']) ? [] : $arr['file_list'];
        list($tmpArr, $hasInvoice) = $this->dealFile($fileList, $applyId, $correct_id, $has_material);
        $fileList = $tmpArr;
//        (ApplyCorrectFileRepository::class)->storeBatchRepository($fileList);
        $where = [
            'apply_id' => $applyId,
            'correct_id' => $correct_id,
            'setup' => $setup,
        ];
        $update = [
            'user_id' => (int)getLoginHome('id')
        ];
        $this->correctContentRepository->selfUpdateOrCreate($where, $update);
        $whereFile = [
            'apply_id' => $applyId,
            'correct_id' => $correct_id,
        ];
        app(ApplyCorrectFileRepository::class)->deleteFile($whereFile);
        if (!empty($fileList)) {
            ApplyCorrectFileModel::insert($fileList);
        }

        $new = [
            'id' => $correct_id,
            'status' => APPLY_CORRECT_STATUS['four'],
            'submit_time' => time(),
        ];
        app(ApplyCorrectRepository::class)->updateRepository($new);
    }


    // 处理附件
    protected function dealFile($fileList, $applyId, $correct_id,$has_material)
    {
        $tmpArr = [];
        $hasInvoice = false;
        $time = time();
        $existFile = app(ApplyFileRepository::class)->getByApply($applyId);

        if (!empty($fileList)) {
            foreach ($fileList as $key => $value) {
                // 只需要判断一次即可
                if (!$hasInvoice && $value['file_type'] == MATERIALS_TYPE['invoice']) {
                    $hasInvoice = true;
                }
                $file_id = empty($value['id'])?0:(int)$value['id'];
                if ($file_id) {
                    $correct_type = APPLY_CORRECT_FILE_TYPE['no_change'];
                } else {
                    $correct_type = APPLY_CORRECT_FILE_TYPE['created'];
                }
                $tmpArr[]= [
                    'file_id' => $file_id,
                    'apply_id' => $applyId,
                    'correct_type' => $correct_type,
                    'correct_id' => $correct_id,
                    'created_at' => $time,
                    'updated_at' => $time,
                    'file_name' => $value['file_name'] ?? '',
                    'file_url' => $value['file_url'] ?? '',
                    'file_type' => $value['file_type'] ?? 0,
                    'check_status' => APPLY_CHECK_STATUS['init'],
                    'project_materials_id' => $value['project_materials_id'] ?? 0,
                ];
            }
        }

        $have = array_column($tmpArr, 'file_id');
        // 这里找出删除的附件

        foreach ($existFile as $k => $v) {
            // 附件是补充材料， 订正的时候不包含时 剔除掉
            if ($v['file_type'] == MATERIALS_TYPE['default'] && empty($has_material)) {
                continue;
            }
            if (!in_array($v['id'], $have)) {
                $tmpArr[]= [
                    'file_id' => $v['id'],
                    'apply_id' => $applyId,
                    'correct_type' => APPLY_CORRECT_FILE_TYPE['deleted'],
                    'correct_id' => $correct_id,
                    'created_at' => $time,
                    'updated_at' => $time,
                    'file_name' => $v['file_name'] ?? '',
                    'file_url' => $v['file_url'] ?? '',
                    'file_type' => $v['file_type'] ?? 0,
                    'check_status' => $v['check_status'],
                    'project_materials_id' => $v['project_materials_id'] ?? 0,
                ];
            }
        }
        return [$tmpArr, $hasInvoice];
    }

    // 订正记录详情
    public function correctDetail($id)
    {
        $detail = $this->repository->detail($id);
        if (empty($detail)) {
            return [];
        }
        $where = [
            'apply_id' => $detail['apply_id'],
            'correct_id' => $detail['id'],
        ];
        $file = app(ApplyCorrectFileRepository::class)->getChangeContent($where);
        $detail['change_file'] = $file;

        $submit = app(ApplyCorrectContentRepository::class)->getContent($where);
        $origin = $detail['origin_content'];
        if (empty($origin)) {
            $origin = app(ApplyRepository::class)->detailApply(['id' => $detail['apply_id']]);
        }
        list($changeContent, $change) = $this->getChangeContent($origin, $submit);
        $detail['change_content'] = $changeContent;
        $detail['change'] = $change;

        return $detail;
    }

    // 变更内容
    public function getChangeContent($origin, $newContentList)
    {
        $applyColumnName = trans('correct.apply');
        $setupTwo = [];
        $setupThree = [];
        $change = [];
        $changeContent = [];

        foreach ($newContentList as $key => $value) {
            if ($value['setup'] == APPLY_SUBMIT_SETUP['two']) {
                $setupTwo = $value['content'];
            } elseif ($value['setup'] == APPLY_SUBMIT_SETUP['three']) {
                $setupThree = $value['content'];
            }
        }
        $submit = array_merge($setupTwo, $setupThree);
        if (empty($submit)) {
            return [
                $change,
                $changeContent,
            ];
        }

        foreach ($this->getChangeColumn() as $k => $v) {
            $tmpOld = empty($origin[$v]) ?'':$origin[$v];
            $tmpNew = empty($submit[$v]) ?'':$submit[$v];
            $columnName = array_get($applyColumnName, $v, '');
            if ($tmpNew != $tmpOld) {
                $changeContent[] = trans('correct.apply_change_desc', [
                    'column' => $columnName,
                    'old' => $tmpOld,
                    'new' => $tmpNew,
                ]);
                $change[$v] = $tmpNew;
            }
        }

        // 单独处理行业
        $industry_id = '';
        $industry_text = '';
        if (!empty($submit['industry_id'])) {
            // 行业类别处理
            $industryId = $submit['industry_id'];
            $industryList = app(IndustryService::class)->getIndustry([
                'first_industry_id' => empty($industryId[0]) ? '' : $industryId[0],
                'second_industry_id' => empty($industryId[1]) ? '' : $industryId[1],
                'third_industry_id' => empty($industryId[2]) ? '' : $industryId[2],
                'fourth_industry_id' => empty($industryId[3]) ? '' : $industryId[3],
            ]);
            $industry_id = implode($this->industry_segmenter, $industryId);
            $industry_text = implode($this->industry_segmenter, $industryList);
        }

        $oldIndustry = implode($this->industry_segmenter, $origin['industry_text']);
        if ($oldIndustry != $industry_text) {
            $change['industry_id'] = $industry_id;
            $change['industry_text'] = $industry_text;
            $columnName = array_get($applyColumnName, 'industry_text', '');
            $changeContent[] = trans('correct.apply_change_desc', [
                'column' => $columnName,
                'old' => $oldIndustry,
                'new' => $industry_text,
            ]);
            $change['industry_id'] = $industry_id;
            $change['industry_text'] = $industry_text;
        }

        // 经济类型
        $newEconomy = empty($submit['economy_list']) ? [] : $submit['economy_list'];
        $tmpEconomy = [];

        foreach ($newEconomy as $ke => $ve) {
            $tmpKey = $ve['year'].'-'.$ve['type'];
            $tmpEconomy[$tmpKey] = $ve['content'];
        }

        $economyType = trans('constant.apply_economy_type');
        $economyFlag = false;
        foreach ($origin['economy_list'] as $ken => $ven) {
            foreach ($ven['content_list'] as $kv => $vv) {
                $tmpKeyN = $vv['year'].'-'.$vv['type'];
                $new = empty($tmpEconomy[$tmpKeyN]) ? 0 :  $tmpEconomy[$tmpKeyN];
                if ($new != $vv['content']) {
                    $economyFlag = true;
                    $changeContent[] = trans('correct.apply_economy_desc', [
                        'year' => $vv['year'],
                        'type' => array_get($economyType, $vv['type'], ''),
                        'old' => $vv['content'],
                        'new' => $new,
                    ]);
                }
            }

        }

        if ($economyFlag == true) {
            foreach ($newEconomy as $kne => $vne) {
                $newEconomy[$kne]['apply_id'] = $origin['id'];
            }
            $change['economy_list'] = $newEconomy;
        }

        return [
            $changeContent,
            $change,
        ];
    }

    // 有可能变更的字段
    protected function getChangeColumn()
    {
        $columns = [
            'policy_name',
            'project_name',
            'enterprise_name',
            'regist_address',
            'regist_time',
            'regist_capital',
            'business_address',
            'business_area',
            'unified_credit_code',
            'organization_code',
            'employee_number',
            'employee_degree',
            'employee_junior',
            'employee_other',
            'legal_name',
            'legal_phone',
            'legal_wechat',
            'charge_name',
            'charge_phone',
            'charge_wechat',
            'contact_name',
            'contact_phone',
            'contact_wechat',
            'business_content',
            'plan_content',
            'approval_organ',
            'approval_number',
            'qualifications',
            'provisions',
            'apply_criteria',
            'apply_money',
            'other_notes',
        ];

        return $columns;
    }

    // 给出部门 是否有权限提交申报
    public function authDepartmentSubmit($department_id)
    {
        $department = app(StaffDepartmentRepository::class)->departmentDetail(['id' => $department_id]);

        if (empty($department_id) || !in_array($department['type'], [DEPARTMENT_TYPE['one'], DEPARTMENT_TYPE['three']])) {
            return false;
        }
        return true;
    }

}