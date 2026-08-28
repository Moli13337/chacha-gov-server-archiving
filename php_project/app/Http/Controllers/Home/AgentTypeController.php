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
use App\Repositories\Agent\AgentTypeRepository;
use Illuminate\Http\Request;

class AgentTypeController extends Controller
{

    protected $agentTypeRepository;

    public function __construct(AgentTypeRepository $agentTypeRepository)
    {
        $this->agentTypeRepository = $agentTypeRepository;
    }

    public function all(Request $request)
    {
        return codeRender(Code::OK, $this->agentTypeRepository->getAll(['id', 'name']));
    }

    public function firstClass(Request $request)
    {
        return codeRender(Code::OK, $this->agentTypeRepository->firstClass(['id', 'name']));
    }

    public function reserved(Request $request)
    {
        return codeRender(Code::OK, $this->agentTypeRepository->reserved(['id', 'name']));
    }
}