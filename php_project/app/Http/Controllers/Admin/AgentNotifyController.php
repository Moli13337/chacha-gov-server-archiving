<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/17
 * Time: 19:24
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Events\FileChange;
use App\Exceptions\QueryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AgentNotify\DetailRequest;
use App\Http\Requests\AgentNotify\ListRequest;
use App\Http\Requests\AgentNotify\SaveRequest;
use App\Http\Requests\AgentNotify\UpdateRequest;
use App\Repositories\Agent\AgentNotifyFileRepository;
use App\Repositories\Agent\AgentNotifyRepository;
use Illuminate\Support\Facades\DB;

class AgentNotifyController extends Controller
{

    protected $agentNotifyRepository;
    protected $agentNotifyFileRepository;

    public function __construct(AgentNotifyRepository $agentNotifyRepository,
                                AgentNotifyFileRepository $agentNotifyFileRepository)
    {
        $this->agentNotifyRepository = $agentNotifyRepository;
        $this->agentNotifyFileRepository = $agentNotifyFileRepository;
    }

    public function store(SaveRequest $request)
    {
        $white = [
            'title',
            'content',
            'source_name',
            'publish_status',
            'type',
        ];

        $params = $request->only($white);
        $params[CREATED_STAFF_ID] = (int)getLoginStaff('id');
        if ($params['publish_status'] == PUBLISH_STATUS['yes']) {
            $params['publish_time'] = time();
        }
        $params['enc_id'] = $this->getEncId();
        try {
            DB::beginTransaction();
            $res = $this->agentNotifyRepository->storeRepository($params);
            $this->storeFile($request, $res['id']);
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

    public function storeFile($request, $id)
    {
        $file = $request->input('file', []);

        $column = ['name', 'save_url'];
        $file = array_map(function ($v) use ($column) {
            return array_only($v, $column);
        }, $file);
        if (!empty($file)) {
            foreach ($file as $key => $value) {
                $file[$key]['agent_notify_id'] = $id;
                $file[$key] = array_merge($file[$key], returnCreatedUpdatedAt());
            }
            $this->agentNotifyFileRepository->storeBatchRepository($file);
        }
    }

    public function detail(DetailRequest $request)
    {
        $where = [
            'id' => $request->input('id')
        ];
        $data = $this->agentNotifyRepository->detail($where);
        return codeRender(Code::OK, $data);
    }

    public function update(UpdateRequest $request)
    {
        $white = [
            'id',
            'title',
            'content',
            'source_name',
            'publish_status',
        ];
        $params = $request->only($white);

        $detail = $this->agentNotifyRepository->findRepository($params['id']);

        if ($params['publish_status'] == PUBLISH_STATUS['yes'] && $detail['publish_status'] == PUBLISH_STATUS['no']) {
            $params['publish_time'] = time();
        }
        try {
            DB::beginTransaction();
            $res = $this->agentNotifyRepository->updateRepository($params);
            $this->updateFile($request, $res['id']);
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

    public function updateFile($request, $id)
    {
        $file = $request->input('file', []);
        $column = ['id','name', 'save_url'];
        $file = array_map(function ($v) use ($column) {
            return array_only($v, $column);
        }, $file);

        $list = $this->agentNotifyFileRepository->getList($id, ['id']);
        $exist = array_column($list, 'id');

        $deletes = array_diff($exist, array_column($file, 'id'));

        if (!empty($file)) {
            foreach ($file as $key => $value) {
                if (!empty($value['id'])) {
                    unset($file[$key]);
                    continue;
                }
                unset($file[$key]['id']);
                $file[$key]['agent_notify_id'] = $id;
                $file[$key] = array_merge($file[$key], returnCreatedUpdatedAt());
            }
            $this->agentNotifyFileRepository->storeBatchRepository($file);
            if (!empty($file)) {
                $log = [
                    'type' => ACTIVITY_TYPE['created'],
                    'subject_id' => $id,
                    'subject_type_id' => ACTIVITY_SUBJECT_TYPE['agent_notify'],
                    'properties' => json_encode(['attributes' => $file, 'old' => []]),
                ];
                event(new FileChange($log));
            }

        }

        if ($deletes) {
            $this->agentNotifyFileRepository->deleteRepository($deletes);
            $log = [
                'type' => ACTIVITY_TYPE['deleted'],
                'subject_id' => $id,
                'subject_type_id' => ACTIVITY_SUBJECT_TYPE['agent_notify'],
                'properties' => json_encode(['attributes' => $deletes, 'old' => []]),
            ];
            event(new FileChange($log));
        }
    }

    public function delete(DetailRequest $request)
    {
        $this->agentNotifyRepository->deleteRepository($request->input('id'));
        return codeRender(Code::OK);
    }

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        $params['order_by'] = ['id' => 'DESC'];
        $data = $this->agentNotifyRepository->list($params);
        return codeRender(Code::OK, $data);
    }

    private function getEncId(){
        $enc_id = substr(md5(time().rand()), 0, 20);

        $data = $this->agentNotifyRepository->getByEncId($enc_id, ['id']);

        if (!empty($data)) {
            return $this->getEncId();
        }
        return $enc_id;
    }
}