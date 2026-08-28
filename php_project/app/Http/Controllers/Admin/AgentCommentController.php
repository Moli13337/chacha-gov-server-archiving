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
use App\Events\MaterialsChange;
use App\Http\Controllers\Controller;
use App\Http\Requests\AgentComment\DeleteRequest;
use App\Http\Requests\AgentComment\DetailRequest;
use App\Http\Requests\AgentComment\ListRequest;
use App\Http\Requests\AgentComment\SaveRequest;
use App\Http\Requests\AgentComment\UpdateCalculateRequest;
use App\Http\Requests\AgentComment\UpdateRequest;
use App\Http\Requests\AgentComment\UpdateShowRequest;
use App\Repositories\Agent\AgentCommentRepository;
use App\Repositories\Agent\AgentRepository;
use App\Repositories\Staff\StaffRepository;
use App\Repositories\User\UserRepository;

class AgentCommentController extends Controller
{

    protected $agentCommentRepository;

    public function __construct(AgentCommentRepository $agentCommentRepository)
    {
        $this->agentCommentRepository = $agentCommentRepository;
    }

    public function store(SaveRequest $request)
    {
        $params = $this->filter($request);
        $params['user_id'] = (int)getLoginStaff('id');
        $params['is_show'] = IS_SHOW['yes'];
        $params['user_type'] = MESSAGE_USER_TYPE['staff'];
        $params['is_calculate'] = IS_CALCULATE['yes'];
        $params['department_id'] = (int)getLoginDepartment('id');

        $this->agentCommentRepository->storeRepository($params);
        event(new ComputeStars([$params['agent_id']]));

        return codeRender(Code::OK);
    }

    public function update(UpdateRequest $request)
    {
        $param = $this->filter($request);
        $this->agentCommentRepository->updateRepository($param);

        return codeRender(Code::OK);
    }

    public function detail(DetailRequest $request)
    {
        $data = $this->agentCommentRepository->findRepository($request->input('id'));
        return codeRender(Code::OK, $data);
    }

    public function delete(DeleteRequest $request)
    {
        // 先查出agent_id
        $agent = $this->agentCommentRepository->getByIds($request->input('ids'), ['agent_id']);
        if (empty($agent)) {
            return codeRender(Code::OK);
        }

        $this->agentCommentRepository->deleteRepository($request->input('ids'));
        event(new ComputeStars(array_column($agent, 'agent_id')));
        return codeRender(Code::OK);
    }

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        $params['order_by'] = ['id' => 'DESC'];
        $data = $this->agentCommentRepository->list($params);
        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }

        $staff_id = [];
        $staff = [];
        $user_id = [];
        $user = [];

        foreach ($data['data'] as $key => $value) {
            if ($value['user_type'] == MESSAGE_USER_TYPE['user']) {
                $user_id[] = $value['user_id'];
            } elseif ($value['user_type'] == MESSAGE_USER_TYPE['staff']) {
                $staff_id[] = $value['user_id'];
            }
        }
        $staffDepartment = [];
        if ($staff_id) {
            $staff = app(StaffRepository::class)->getByIds($staff_id, ['id', 'name'], QUERY_TRASHED);
            $staff = array_column($staff, 'name', 'id');
            $staffDepartment = app(StaffRepository::class)->getDepartmentByIds($staff_id);
            $staffDepartment = array_column($staffDepartment, 'department', 'id');
        }
        $userEnterprise = [];
        if ($user_id) {
            $user = app(UserRepository::class)->getByIds($user_id, ['id', 'name'], QUERY_TRASHED);
            $user = array_column($user, 'name', 'id');
            $userEnterprise = app(UserRepository::class)->getEnterpriseByIds($user_id);
            $userEnterprise = array_column($userEnterprise, 'enterprise', 'id');
        }

        foreach ($data['data'] as $key => &$value) {
            $value['agent_name'] = empty($value['agent'][0]) ? '' : array_get($value['agent'][0], 'name', '');
            unset($value['agent']);
            $value['agent_type_name'] = empty($value['agent_type'][0]) ? '' : array_get($value['agent_type'][0], 'name', '');
            unset($value['agent_type']);

            if ($value['user_type'] == MESSAGE_USER_TYPE['user']) {
                $value['user_name'] = array_get($user, $value['user_id'], '');
                $value['department_enterprise_name'] = array_get($userEnterprise[$value['user_id']][0]??[], 'name', '');
            } elseif ($value['user_type'] == MESSAGE_USER_TYPE['staff']) {
                $value['user_name'] = array_get($staff, $value['user_id'], '');
                $value['department_enterprise_name'] = array_get($staffDepartment[$value['user_id']][0]??[], 'name', '');
            } else {
                $value['user_name'] = '';
                $value['department_enterprise_name'] = '';
            }
        }

        return codeRender(Code::OK, $data);
    }

    public function updateShow(UpdateShowRequest $request)
    {
        $param = $this->filter($request);
        $this->agentCommentRepository->updateRepository($param);
        return codeRender(Code::OK);
    }

    public function updateCalculate(UpdateCalculateRequest $request)
    {
        $param = $this->filter($request);
        $agent = $this->agentCommentRepository->getByIds([$request->input('id')], ['agent_id']);
        if (empty($agent)) {
            return codeRender(Code::OK);
        }
        $this->agentCommentRepository->updateRepository($param);
        event(new ComputeStars(array_column($agent, 'agent_id')));
        return codeRender(Code::OK);
    }

}