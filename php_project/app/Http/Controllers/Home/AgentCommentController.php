<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/23
 * Time: 15:48
 */

namespace App\Http\Controllers\Home;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Requests\AgentComment\Home\ListRequest;
use App\Http\Requests\AgentComment\Home\SaveRequest;
use App\Repositories\Agent\AgentCommentRepository;
use App\Repositories\Agent\AgentTypeRepository;
use App\Repositories\Staff\StaffRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;

class AgentCommentController extends Controller
{

    protected $agentCommentRepository;

    public function __construct(AgentCommentRepository $agentCommentRepository)
    {
        $this->agentCommentRepository = $agentCommentRepository;
    }

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        $params['order_by'] = ['id'=> 'DESC'];
        $params['is_show'] = IS_SHOW['yes'];
        $column = [
            'stars',
            'content',
            'user_id',
            'user_type',
            'created_at',
        ];
        $data = $this->agentCommentRepository->clientList($params, $column);

        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }

        $staff_id = [];
        $staff = [];
        $user_id = [];
        $user = [];

        foreach ($data['data'] as $key => $value) {
            $value['stars'] = (float)$value['stars'];
            if ($value['user_type'] == MESSAGE_USER_TYPE['user']) {
                $user_id[] = $value['user_id'];
            } elseif ($value['user_type'] == MESSAGE_USER_TYPE['staff']) {
                $staff_id[] = $value['user_id'];
            }
        }
        if ($staff_id) {
            $staff = app(StaffRepository::class)->getByIds($staff_id, ['id', 'name'], QUERY_TRASHED);
            $staff = array_column($staff, 'name', 'id');
        }
        if ($user_id) {
            $user = app(UserRepository::class)->getByIds($user_id, ['id', 'name'], QUERY_TRASHED);
            $user = array_column($user, 'name', 'id');
        }

        foreach ($data['data'] as $key => &$value) {

            if ($value['user_type'] == MESSAGE_USER_TYPE['user']) {
                $value['user_name'] = array_get($user, $value['user_id'], '');
            } elseif ($value['user_type'] == MESSAGE_USER_TYPE['staff']) {
                $value['user_name'] = array_get($staff, $value['user_id'], '');
            } else {
                $value['user_name'] = '';
            }
        }

        return codeRender(Code::OK, $data);
    }

    public function store(SaveRequest $request)
    {
        $keys = [
            'agent_id',
            'stars',
            'content',
        ];
        $params = $request->only($keys);
        $params['user_id'] = (int)getLoginHome('id');
        $params['user_type'] = MESSAGE_USER_TYPE['user'];
        $this->agentCommentRepository->storeRepository($params);
        return codeRender(Code::OK);
    }
}