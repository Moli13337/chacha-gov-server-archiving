<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/19
 * Time: 16:44
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Exceptions\QueryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AgentComplaint\DeleteRequest;
use App\Http\Requests\AgentComplaint\DetailRequest;
use App\Http\Requests\AgentComplaint\DisposeRequest;
use App\Http\Requests\AgentComplaint\ListRequest;
use App\Http\Requests\AgentComplaint\UpdateShowRequest;
use App\Models\AgentCommentModel;
use App\Repositories\Agent\AgentCommentRepository;
use App\Repositories\Agent\AgentComplaintRepository;
use App\Repositories\User\UserMessageRepository;
use Illuminate\Support\Facades\DB;

class AgentComplaintController extends Controller
{

    protected $agentComplaintRepository;

    public function __construct(AgentComplaintRepository $agentComplaintRepository)
    {
        $this->agentComplaintRepository = $agentComplaintRepository;
    }


    public function detail(DetailRequest $request)
    {
        $data = $this->agentComplaintRepository->detail($request->input('id'));
        return codeRender(Code::OK, $data);
    }

    public function delete(DeleteRequest $request)
    {
//        $this->agentComplaintRepository->deleteRepository($request->input('ids'));
        $this->agentComplaintRepository->deleteRepository($request->input('id'));
        return codeRender(Code::OK);
    }

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        $params['order_by'] = ['id' => 'DESC'];
        $params['is_dispose'] = IS_DISPOSE['user'];
        $data = $this->agentComplaintRepository->list($params);
        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }

        foreach ($data['data'] as $key => &$value) {
            $value['agent_name'] = empty($value['agent'][0]) ? '' : array_get($value['agent'][0], 'name', '');
            unset($value['agent']);
            $value['agent_type_name'] = empty($value['agent_type'][0]) ? '' : array_get($value['agent_type'][0], 'name', '');
            unset($value['agent_type']);
            $value['user_name'] = array_get($value['user'], 'name', '');
            $value['user_mobile'] = array_get($value['user'], 'mobile', '');
            $value['enterprise_name'] = array_get($value['enterprise'], 'name', '');
            unset($value['user']);
            unset($value['enterprise']);
        }

        return codeRender(Code::OK, $data);
    }

    /**
     * FUNCTION_NAME : dispose
     * 审核
     *
     * @param DisposeRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws QueryException
     */
    public function dispose(DisposeRequest $request)
    {
        $param = $this->filter($request);

        $update = array_only($param, ['id', 'type']);
        $update['status'] = AGENT_COMPLAINT_STATUS['success'];

        $dispose = array_only($param, ['content', 'type']);
        $dispose['status'] = AGENT_COMPLAINT_STATUS['success'];
        $dispose['source_id'] = $param['id'];
        $dispose['user_id'] = (int)getLoginStaff('id');
        $dispose['is_dispose'] = IS_DISPOSE['staff'];

        $detail = $this->agentComplaintRepository->detail($param['id']);
        if ($detail['status'] != AGENT_COMPLAINT_STATUS['wait']) {
            return codeRender(Code::AGENT_COMPLAINT_STATUS_WAIT_ERROR);
        }

        try {
            DB::beginTransaction();
            $this->agentComplaintRepository->updateRepository($update);
            $this->agentComplaintRepository->storeRepository($dispose);
            $this->message($detail, $param);
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

        return codeRender(Code::OK);
    }


    /**
     * FUNCTION_NAME : message
     * 发消息
     *
     * @param $detail
     * @param $param
     * @throws QueryException
     */
    protected function message($detail, $param)
    {
        $type_name = array_get(trans('constant.agent_complaint_type'), $param['type']);
        $content = trans('message.agent_comment', [
            "enterprise_name" => $detail['enterprise_name'],
            "agent_name"       => $detail['agent_name'],
            "type_name"       => $type_name
        ]);
        $arr = [
            'content' => $content,
            'user_id' => $detail['user_id'],
            'user_type' => MESSAGE_USER_TYPE['user'],
            'source_type_id' => USER_MESSAGE_SOURCE['agent_complaint'],
            'target_id' => $detail['id'],
        ];
        app(UserMessageRepository::class)->storeRepository($arr);
    }

}