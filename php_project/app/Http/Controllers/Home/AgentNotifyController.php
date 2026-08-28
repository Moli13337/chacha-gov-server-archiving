<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/23
 * Time: 14:49
 */

namespace App\Http\Controllers\Home;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Requests\AgentNotify\Home\DetailRequest;
use App\Http\Requests\AgentNotify\Home\ListRequest;
use App\Repositories\Agent\AgentNotifyRepository;
use App\Repositories\Apply\YoutuRepository;
use Illuminate\Http\Request;

class AgentNotifyController extends Controller
{

    protected $agentNotifyRepository;
    public function __construct(AgentNotifyRepository $agentNotifyRepository)
    {
        $this->agentNotifyRepository = $agentNotifyRepository;
    }

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        $params['publish_status'] = PUBLISH_STATUS['yes'];
        $params['order_by'] = ['id' => 'DESC'];
        $column = ['id', 'enc_id', 'title', 'content', 'source_name', 'publish_status','publish_time', 'created_at'];
        $res = $this->agentNotifyRepository->clientList($params, $column);

        if (empty($res['data'])) {
            return codeRender(Code::OK, $res);
        }
        foreach ($res['data'] as $key => $value) {
            $res['data'][$key]['id'] = $value['enc_id'];
            $res['data'][$key]['is_new'] = ($value['created_at'] < (time() -  7*24*60*60)) ? 0 : 1;
        }
        return codeRender(Code::OK, $res);
    }

    public function detail(DetailRequest $request)
    {
        $where = [
            'enc_id' => $request->input('id'),
            'publish_status' => PUBLISH_STATUS['yes'],
        ];
        $column = ['id', 'enc_id', 'title', 'content', 'source_name', 'publish_status','publish_time', 'created_at'];
        $data = $this->agentNotifyRepository->detailByClient($where, $column);
        if (empty($data) ) {
            return codeRender(Code::OK, $data);
        }
        $data['id'] = $data['enc_id'];
        return codeRender(Code::OK, $data);
    }

}