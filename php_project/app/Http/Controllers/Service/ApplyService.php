<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/28
 * Time: 19:01
 */

namespace App\Http\Controllers\Service;


use App\Common\Code;
use App\Exceptions\QueryException;
use App\Repositories\Apply\ApplyRepository;
use App\Repositories\Apply\ApprovalDepartmentRepository;
use App\Repositories\Apply\ApprovalRepository;
use App\Repositories\User\UserMessageRepository;
use Illuminate\Support\Facades\DB;

class ApplyService extends BaseService
{

    protected  $repository;
    public function __construct(ApplyRepository $repository)
    {
        $this->repository = $repository;
    }

    public function revocation($id, $data)
    {
        // 查询企业服务的审批
        $approval = app(ApprovalRepository::class)->enterpriseApprovalByApply($id);
        // 查询区业务服务部门操作员
        $staff = app(ApprovalDepartmentRepository::class)->getStaff();

        $approvalId = array_get($approval, 'id', 0);

        try {
            DB::beginTransaction();
            $this->repository->revocation($id);
            app(ApprovalRepository::class)->deleteById($approvalId);
            if (!empty($staff)) {
                app(ApprovalRepository::class)->sendMessage([
                    'policy_name' => $data['policy_name'],
                    'project_name' => $data['project_name'],
                    'enterprise_name' => $data['enterprise_name'],
                    'staff_id' => $staff['staff_id'],
                ], APPROVAL_MESSAGE_CONTENT['thirty']);

                $where = [
                    'source_type_id' => USER_MESSAGE_SOURCE['two'],
                    'target_id' => array_get($approval, 'id', 0)
                ];
                app(UserMessageRepository::class)->read($where);
            }
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
        /** 短信 **/
        if (empty($staff)) {
            app(ApprovalRepository::class)->sendSms([
                'policy_name' => $data['policy_name'],
                'project_name' => $data['project_name'],
                'mobile' => $staff['mobile']
            ], SMS_TEMPLATE['thirty']);
        }

        return true;
    }
}