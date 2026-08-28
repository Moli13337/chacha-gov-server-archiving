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
use App\Http\Requests\AgentComplaint\Home\SaveRequest;
use App\Repositories\Agent\AgentComplaintRepository;
use Illuminate\Http\Request;

class AgentComplaintController extends Controller
{

    protected $agentComplaintRepository;

    public function __construct(AgentComplaintRepository $agentComplaintRepository)
    {
        $this->agentComplaintRepository = $agentComplaintRepository;
    }

    public function store(SaveRequest $request)
    {
        $keys = [
            'agent_id',
            'content',
        ];
        $params = $request->only($keys);
        $params['user_id'] = (int)getLoginHome('id');
        $params['code'] = $this->agentComplaintRepository->getMaxCode();
        $params['enterprise_id'] = $request->input('enterprise_id');
        $this->agentComplaintRepository->storeRepository($params);
        return codeRender(Code::OK);
    }
}