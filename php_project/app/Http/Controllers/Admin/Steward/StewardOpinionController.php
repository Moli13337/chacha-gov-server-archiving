<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/4
 * Time: 9:58
 */

namespace App\Http\Controllers\Admin\Steward;


use App\Common\Code;
use App\Events\FileChange;
use App\Exceptions\QueryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StewardOpinion\DeleteRequest;
use App\Http\Requests\StewardOpinion\DetailRequest;
use App\Http\Requests\StewardOpinion\ListRequest;
use App\Http\Requests\StewardOpinion\SaveRequest;
use App\Http\Requests\StewardOpinion\UpdateRequest;
use App\Repositories\Steward\StewardOpinionFileRepository;
use App\Repositories\Steward\StewardOpinionRepository;
use Illuminate\Support\Facades\DB;

class StewardOpinionController extends Controller
{

    protected $repository;
    protected $stewardOpinionFileRepository;

    public function __construct(StewardOpinionRepository $repository,
                                StewardOpinionFileRepository $stewardOpinionFileRepository)
    {
        $this->repository = $repository;
        $this->stewardOpinionFileRepository = $stewardOpinionFileRepository;
    }

    public function save(SaveRequest $request)
    {
        $fileColumn = [
            'file',
            'file.*.name',
            'file.*.save_url',
        ];

        $white = array_diff(array_keys($request->rules()), $fileColumn);
        $params = $request->only($white);
        $params[CREATED_STAFF_ID] = (int)getLoginStaff('id');
        $params['enc_id'] = $this->getEncId();
        $params = $this->initValue($params);
        if ($params['publish_status'] == PUBLISH_STATUS['yes']) {
            $params['publish_time'] = time();
            $params[PUBLISH_STAFF_ID] = (int)getLoginStaff('id');
        }
        if ($params['type'] != STEWARD_OPINION_TYPE['question']) {
            $params['link'] = '';
        }

        try {
            DB::beginTransaction();
            $res = $this->repository->storeRepository($params);
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
                $file[$key]['steward_opinion_id'] = $id;
                $file[$key] = array_merge($file[$key], returnCreatedUpdatedAt());
            }
            $this->stewardOpinionFileRepository->storeBatchRepository($file);
        }
    }

    public function update(UpdateRequest $request)
    {
        $fileColumn = [
            'file',
            'file.*.name',
            'file.*.save_url',
        ];

        $white = array_diff(array_keys($request->rules()), $fileColumn);
        $params = $request->only($white);
        $params[CREATED_STAFF_ID] = (int)getLoginStaff('id');
        $params = $this->initValue($params);

        $origin = $this->repository->simpleDetail(['id' => $params['id']], ['publish_status']);
        if ($params['publish_status'] == PUBLISH_STATUS['yes'] && $origin['publish_status'] !=  PUBLISH_STATUS['yes']) {
            $params['publish_time'] = time();
            $params[PUBLISH_STAFF_ID] = (int)getLoginStaff('id');
        } elseif ($params['publish_status'] == PUBLISH_STATUS['no'] && $origin['publish_status'] !=  PUBLISH_STATUS['no']) {
            $params['publish_time'] = 0;
            $params[PUBLISH_STAFF_ID] = 0;
        }
        if ($params['type'] != STEWARD_OPINION_TYPE['question']) {
            $params['link'] = '';
        }

        try {
            DB::beginTransaction();
            $res = $this->repository->updateRepository($params);
            $this->updateFile($request, $params['id']);
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

        $list = $this->stewardOpinionFileRepository->getList($id, ['id']);
        $exist = array_column($list, 'id');

        $deletes = array_diff($exist, array_column($file, 'id'));

        if (!empty($file)) {
            foreach ($file as $key => $value) {
                if (!empty($value['id'])) {
                    unset($file[$key]);
                    continue;
                }
                unset($file[$key]['id']);
                $file[$key]['steward_opinion_id'] = $id;
                $file[$key] = array_merge($file[$key], returnCreatedUpdatedAt());
            }
            $this->stewardOpinionFileRepository->storeBatchRepository($file);
            if (!empty($file)) {
                $log = [
                    'type' => ACTIVITY_TYPE['created'],
                    'subject_id' => $id,
                    'subject_type_id' => ACTIVITY_SUBJECT_TYPE['steward_opinion'],
                    'properties' => json_encode(['attributes' => $file, 'old' => []]),
                ];
                event(new FileChange($log));
            }

        }

        if ($deletes) {
            $this->stewardOpinionFileRepository->deleteRepository($deletes);
            $log = [
                'type' => ACTIVITY_TYPE['deleted'],
                'subject_id' => $id,
                'subject_type_id' => ACTIVITY_SUBJECT_TYPE['steward_opinion'],
                'properties' => json_encode(['attributes' => $deletes, 'old' => []]),
            ];
            event(new FileChange($log));
        }
    }

    public function detail(DetailRequest $request)
    {
        $where = [
            'id' => $request->input('id')
        ];
        $data = $this->repository->detail($where);
        return codeRender(Code::OK, $data);
    }
    
    public function list(ListRequest $request)
    {

        $params = $this->filter($request);
        $params['order_by'] = ['id' => 'DESC'];
        $data = $this->repository->list($params);

        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }

        foreach ($data['data'] as $key => &$value) {
            $value['publish_staff_name'] = array_get($value['publish_staff'], 'name', '');
            unset($value['publish_staff']);
        }
        return codeRender(Code::OK, $data);
    }

    public function delete(DeleteRequest $request){
        $res = $this->repository->deleteRepository($request->input('id'));
        return codeRender(Code::OK, $res);
    }


    private function getEncId(){
        $enc_id = substr(md5(time().rand()), 0, 20);

        $data = $this->repository->getByEncId($enc_id, ['id']);

        if (!empty($data)) {
            return $this->getEncId();
        }
        return $enc_id;
    }

    public function initValue($data)
    {
        $keys = [
            'link' => '',
        ];
        foreach ($keys as $key => $value) {
            $data[$key] = empty($data[$key]) ? $value : $data[$key];
        }
        return $data;
    }

}