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
use App\Repositories\Agent\AgentSetupRepository;
use Illuminate\Http\Request;

class AgentSetupController extends Controller
{

    protected $agentSetupRepository;

    public function __construct(AgentSetupRepository $agentSetupRepository)
    {
        $this->agentSetupRepository = $agentSetupRepository;
    }

    public function guide(Request $request)
    {
        $where = [
            ['publish_status', '=', PUBLISH_STATUS['yes']]
        ];
        $column = ['id', 'title', 'content', 'source_name', 'type', 'publish_status', 'publish_time'];
        $data = $this->agentSetupRepository->getAll($where, $column);
        return codeRender(Code::OK, $data);
    }
}