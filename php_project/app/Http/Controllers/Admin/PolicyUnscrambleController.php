<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/13
 * Time: 17:59
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Events\PolicyRelation;
use App\Events\PolicyUnscrambleChange;
use App\Exceptions\QueryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Policy\LogRequest;
use App\Http\Requests\Unscramble\DeleteBatchRequest;
use App\Http\Requests\Unscramble\DeleteRequest;
use App\Http\Requests\Unscramble\DetailRequest;
use App\Http\Requests\Unscramble\ListRequest;
use App\Http\Requests\Unscramble\SaveRequest;
use App\Http\Requests\Unscramble\UpdatePublishRequest;
use App\Http\Requests\Unscramble\UpdateRequest;
use App\Models\PolicyUnscrambleModel;
use App\Repositories\ActivityLogRepository;
use App\Repositories\Policy\PolicyUnscrambleRelationRepository;
use App\Repositories\Policy\PolicyUnscrambleRepository;
use App\Support\Collection;
use Illuminate\Support\Facades\DB;

class PolicyUnscrambleController extends Controller
{

    protected $repository;
    protected $policyUnscrambleRelationRepository;

    public function __construct(PolicyUnscrambleRepository $repository,
                                PolicyUnscrambleRelationRepository $policyUnscrambleRelationRepository)
    {
        $this->repository = $repository;
        $this->policyUnscrambleRelationRepository = $policyUnscrambleRelationRepository;
    }

    public function store(SaveRequest $request)
    {
        // 检查是否有重复的政策
        $policy_ids = array_column($request->input('policy'), 'policy_id');
        $has = $this->policyUnscrambleRelationRepository->has($policy_ids);
        if ($has) {
            return codeRender(Code::UNSCRAMBLE_POLICY_EXIST_ERROR);
        }

        $white = [
            'name',
            'source_name',
            'content_url',
            'content_name',
            'publish_status'
        ];

        $data = Collection::filter($white, $request->all());
        $data['created_staff_id'] = getLoginStaff('id');
        $data['code'] = $this->createCode();
        $data['enc_id'] = $this->getEncId();

        try {
            DB::beginTransaction();
            $res = $this->repository->storeRepository($data);
            $this->relationInsert($request->all(), $res['id']);
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

        return codeRender(Code::OK, $res);
    }

    public function relationInsert($data, $id)
    {
        $this->policyInsert($data['policy'], $id);
    }

    public function policyInsert($data,$id)
    {
        $white = [
            'obj_type',
            'policy_id',
        ];
        foreach ($data as $key => &$v) {
            $v = Collection::filter($white, $v);
            $v['unscramble_id'] = $id;
            $v = array_merge($v, returnCreatedUpdatedAt());
        }
        $this->policyUnscrambleRelationRepository->storeBatch($data);
    }

    public function update(UpdateRequest $request)
    {
        // 检查是否有重复的政策
        $policy_ids = array_column($request->input('policy'), 'policy_id');
        $has = $this->policyUnscrambleRelationRepository->hasIgnore($policy_ids, $request->input('id'));
        if ($has) {
            return codeRender(Code::UNSCRAMBLE_POLICY_EXIST_ERROR);
        }

        $white = [
            'id',
            'name',
            'source_name',
            'content_url',
            'content_name',
            'publish_status'
        ];

        $data = Collection::filter($white, $request->all());

        try {
            DB::beginTransaction();
            $res = $this->repository->updateRepository($data);
            $this->relationUpdate($request->all(), $res['id']);
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

        return codeRender(Code::OK, $res);

    }

    public function relationUpdate($data, $id)
    {
        $this->policyUpdate($data['policy'], $id);
    }

    public function policyUpdate($data, $id)
    {
        $params = [
            'data' => $data,
            'id' => $id,
            'type' => ACTIVITY_TYPE['updated'],
            'subject_type_id' => ACTIVITY_SUBJECT_TYPE['unscramble'],
        ];

        event(new PolicyUnscrambleChange($params));
        $this->policyUnscrambleRelationRepository->deleteByUnscrambleId($id);
        $this->policyInsert($data, $id);
    }

    public function detail(DetailRequest $request)
    {
        $data = $this->repository->detail($request->input('id'));
        $tmpData = $data['policy'] ?? [];
        foreach ($tmpData as $key => $value) {
            $tmp  = $value['pivot'];
            $tmp['name'] = $value['name'];
            $tmpData[$key] = $tmp;
        }

        $data['policy'] = $tmpData;

        return codeRender(Code::OK, $data);
    }

    public function delete(DeleteRequest $request)
    {
        $this->repository->deleteRepository($request->input('id'));
        return codeRender(Code::OK);
    }

    public function updatePublish(UpdatePublishRequest $request)
    {
        $this->repository->updateRepository($this->filter($request));
        return codeRender(Code::OK);
    }

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        if (!empty($params['sort']) && $params['sort'] == SORT['asc']) {
            $params['order_by']['id'] = 'ASC';
        } else {
            $params['order_by']['id'] = 'DESC';
        }

        $data = $this->repository->search($params);


        return codeRender(Code::OK, $data);
    }

    private function createCode(){
        $prefix = date('Ymd');
        $code_data = $this->repository->getByLikeCode($prefix, ['code']);
        $code = empty($code_data) ? 1 : substr($code_data['code'], 8) + 1;
        return $prefix.str_pad($code, 6, 0, STR_PAD_LEFT);
    }

    private function getEncId(){
        $enc_id = substr(md5(time().rand()), 0, 20);

        $data = $this->repository->getByEncId($enc_id, ['id']);

        if (!empty($data)) {
            return $this->getEncId();
        }
        return $enc_id;
    }

    public function deleteBatch(DeleteBatchRequest $request)
    {
        $this->repository->deleteBatch($request->input('ids'));

        return codeRender(Code::OK);
    }

    public function log(LogRequest $request, ActivityLogRepository $activityLogRepository)
    {
        $tmp = [
            'subject_id' => $request->input('id'),
            'subject_type_id' => ACTIVITY_SUBJECT_TYPE['unscramble'],
        ];
        $params = $this->filter($request);
        $params = array_merge($params, $tmp);
        $data = $activityLogRepository->getList($params);
        return codeRender(Code::OK, $data);
    }

}