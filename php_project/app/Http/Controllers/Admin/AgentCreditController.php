<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/19
 * Time: 16:44
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Events\ComputeStars;
use App\Exceptions\QueryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AgentCredit\DeleteRequest;
use App\Http\Requests\AgentCredit\ListRequest;
use App\Http\Requests\AgentCredit\SaveRequest;
use App\Http\Requests\AgentCredit\UpdateAuditRequest;
use App\Http\Requests\AgentCredit\UpdateShowRequest;
use App\Http\Requests\User\DetailRequest;
use App\Repositories\Agent\AgentCreditRepository;
use App\Repositories\Agent\AgentRepository;
use App\Repositories\Staff\StaffDepartmentRepository;
use App\Repositories\Staff\StaffRepository;
use Illuminate\Support\Facades\DB;

class AgentCreditController extends Controller
{

    protected $agentCreditRepository;

    public function __construct(AgentCreditRepository $agentCreditRepository)
    {
        $this->agentCreditRepository = $agentCreditRepository;
    }

    public function store(SaveRequest $request)
    {
        $params = $this->filter($request);
        $params['department_id'] = (int)getLoginDepartment('id');
        $params[CREATED_STAFF_ID] = (int)getLoginStaff('id');

        $this->agentCreditRepository->storeRepository($params);
        return codeRender(Code::OK);
    }

    public function detail(DetailRequest $request)
    {
        $data = $this->agentCreditRepository->findRepository($request->input('id'));
        return codeRender(Code::OK, $data);
    }

    public function delete(DeleteRequest $request)
    {
        $this->agentCreditRepository->deleteRepository($request->input('ids'));
        return codeRender(Code::OK);
    }

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        $params['order_by'] = ['id' => 'DESC'];
        $data = $this->agentCreditRepository->list($params);
        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }

        $staff_id = array_column($data['data'],CREATED_STAFF_ID);
        $staffDepartment = [];
        $department_id = array_column($data['data'],'department_id');


        if ($staff_id) {
            $staffDepartment = app(StaffDepartmentRepository::class)->getByIds($department_id,['id', 'name'],QUERY_TRASHED);
            $staffDepartment = array_column($staffDepartment, 'name', 'id');
        }

        foreach ($data['data'] as $key => &$value) {
            $value['agent_name'] = empty($value['agent'][0]) ? '' : array_get($value['agent'][0], 'name', '');
            unset($value['agent']);
            $value['agent_type_name'] = empty($value['agent_type'][0]) ? '' : array_get($value['agent_type'][0], 'name', '');
            unset($value['agent_type']);

            $value['staff_name'] = array_get($value['staff'], 'name', '');
            unset($value['staff']);
            $value['department_name'] = array_get($staffDepartment, $value['department_id'], '');

        }

        return codeRender(Code::OK, $data);
    }

    public function show(UpdateShowRequest $request)
    {
        $param = $this->filter($request);

        if ($param['is_show'] == IS_SHOW['yes']) {
            $arr = $this->agentCreditRepository->findRepository($param['id'])->toArray();
            if (!isset($arr['is_audit']) || $arr['is_audit'] != IS_AUDIT['yes']) {
                return codeRender(Code::AGENT_CREDIT_SHOW_NOT_AUDIT_ERROR);
            }
        }
        $this->agentCreditRepository->updateRepository($param);
        return codeRender(Code::OK);
    }

    public function audit(UpdateAuditRequest $request)
    {
        $param = $this->filter($request);
        $update = [];
        $credit = $this->agentCreditRepository->findRepository($param['id'])->toArray();
        $agent = app(AgentRepository::class)->findRepository($credit['agent_id']);
        if (empty($agent)) {
            return codeRender(Code::AGENT_NOT_EXIST_ERROR);
        }
        if ($param['is_audit'] == IS_AUDIT['yes']) {
//            $credit = $this->agentCreditRepository->findRepository($param['id'])->toArray();
//            $agent = app(AgentRepository::class)->findRepository($credit['agent_id']);
//            if (empty($agent)) {
//                return codeRender(Code::AGENT_NOT_EXIST_ERROR);
//            }
            $agent = $agent->toArray();
            $param['old_type'] = $agent['credit_type'];

            $new = ($credit['type'] != AGENT_CREDIT_TYPE['serious']) ? $credit['type'] : $credit['type'] ;

            $update = [
                'id' => $agent['id'],
                'credit_type' => $new,
            ];

            if ($agent['credit_type'] == AGENT_CREDIT_TYPE['serious']) {
                unset($update['credit_type']);
            }
            if ($credit['type'] == AGENT_CREDIT_TYPE['serious']) {
                $update['publish_status'] = PUBLISH_STATUS['no'];
            }
        }

        try {
            DB::beginTransaction();
            $this->agentCreditRepository->updateRepository($param);
            if (!empty($update)) {
                app(AgentRepository::class)->updateRepository($update);
            }
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

        event(new ComputeStars([$credit['agent_id']]));

        return codeRender(Code::OK);
    }


}