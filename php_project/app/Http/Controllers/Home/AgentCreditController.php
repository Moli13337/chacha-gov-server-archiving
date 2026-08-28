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
use App\Http\Requests\Agent\Home\CreditListRequest;
use App\Http\Requests\AgentComment\Home\ListRequest;
use App\Repositories\Agent\AgentCommentRepository;
use App\Repositories\Agent\AgentCreditRepository;
use App\Repositories\Agent\AgentTypeRepository;
use App\Repositories\Staff\StaffRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;

class AgentCreditController extends Controller
{

    protected $agentCreditRepository;

    public function __construct(AgentCreditRepository $agentCreditRepository)
    {
        $this->agentCreditRepository = $agentCreditRepository;
    }

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        $params['order_by'] = ['id'=> 'DESC'];
        $params['is_show'] = IS_SHOW['yes'];
        $params['is_audit'] = IS_AUDIT['yes'];
        $column = [
            'type',
            'project_name',
            'content',
            'content',
            'created_at',
        ];
        $data = $this->agentCreditRepository->clientList($params, $column);

        return codeRender(Code::OK, $data);
    }

    /**
     * FUNCTION_NAME : creditList
     * 信用异常列表
     *
     * @param CreditListRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \App\Exceptions\QueryException
     */
    public function creditList(CreditListRequest $request)
    {
        $params = $this->filter($request);
        $params['type'] = $params['credit_type'];
        $params['order_by'] = ['id'=> 'DESC'];
        $params['is_show'] = IS_SHOW['yes'];
        $params['is_audit'] = IS_AUDIT['yes'];
        $column = [
            'agent_id',
            'type',
            'created_at',
        ];
        $data = $this->agentCreditRepository->creditList($params, $column);

        if (empty($data['data'])) {
            return codeRender(Code::OK,  []);
        }

        foreach ($data['data'] as $key => &$value) {
            $value['agent_name'] = array_get($value['agent'][0], 'name');
            $value['id'] = array_get($value['agent'][0], 'enc_id');
            $value['enc_id'] = array_get($value['agent'][0], 'enc_id');
            $value['publish_status'] = array_get($value['agent'][0], 'publish_status');
            unset($value['agent_id']);
            unset($value['agent']);
        }
        return codeRender(Code::OK, $data);
    }
}