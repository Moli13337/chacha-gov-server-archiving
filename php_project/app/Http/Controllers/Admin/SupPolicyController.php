<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/12
 * Time: 17:20
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Exceptions\QueryException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\PolicyService;
use App\Http\Controllers\Service\SummarizeService;
use App\Http\Requests\SupPolicy\DetailRequest;
use App\Http\Requests\SupPolicy\ListRequest;
use App\Http\Requests\SupPolicy\SaveRequest;
use App\Http\Requests\SupPolicy\UpdateRequest;
use App\Repositories\Policy\PolicyRepository;
use App\Support\Collection;
use Illuminate\Support\Facades\DB;

class SupPolicyController extends Controller
{

    protected $repository;
    protected $policyService;
    protected $summarizeService;


    public function __construct(PolicyRepository $repository,
                                PolicyService $policyService,
                                SummarizeService $summarizeService)
    {
        $this->repository = $repository;
        $this->policyService = $policyService;
        $this->summarizeService = $summarizeService;
    }

    public function store(SaveRequest $request)
    {

        $white = [
            'obj_type',
            'name',
            'doc_num',
            'content',
            'pub_time',
            'province_code',
            'city_code',
            'district_code',
            'validity_sdate',
            'validity_edate',
            'publish_status',
            'source',
            'source_web',
            'source_url',
            'is_handel',
            'big_data_id'
        ];
        $data = Collection::filter($white, $request->all());
        $data['obj_type'] = OBJ_TYPE['sup_policy'];

       try {

           DB::beginTransaction();
           $res = $this->policyService->store($data);
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

       return codeRender(Code::OK, ['id' => $res['id']]);
    }

    protected function relationInsert($data, $id)
    {
        if (!empty($data['industry'])) {
            $this->policyService->relationIndustryInsert($data['industry'], $id);
        }

        $this->policyService->conclusionInsert($data['conclusion'], $id);
        $this->policyService->itemInsert($data['item'], $id);

        if (!empty($data['gov_agen'])) {
            $this->policyService->govAgenInsert($data['gov_agen'], $id);
        }

        if (!empty($data['summarize'])) {
            $this->summarizeService->relationSummarizeInsert($data['summarize'], $id);
        }

        if (!empty($data['file'])) {
            $this->policyService->relationFileInsert($data['file'], $id);
        }

        $this->relationPolicyInsert($data, $id);
    }

    public function relationPolicyInsert($data, $id)
    {
        $relation = [];

        $type = $this->policyService->typeHasRelation(OBJ_TYPE['sup_policy']);

        foreach ($type as $k => $v) {
            if (!empty($data[$v])) {
                $relation = array_merge($relation, $data[$v]);
            }
        }

        if (!empty($relation)) {
            $this->policyService->relationRelationInsert($relation, $id, OBJ_TYPE['sup_policy']);
        }
    }

    public function update(UpdateRequest $request)
    {
        $white = [
            'id',
            'name',
            'doc_num',
            'content',
            'pub_time',
            'province_code',
            'city_code',
            'district_code',
            'validity_sdate',
            'validity_edate',
            'publish_status',
            'source',
            'source_web',
            'source_url',
        ];
        $macro_data = Collection::filter($white, $request->all());


        try {
            DB::beginTransaction();
            $res = $this->policyService->update($macro_data);
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

        return codeRender(Code::OK);
    }

    public function relationUpdate($data, $id)
    {
        $this->policyService->relationIndustryUpdate($data['industry']??[], $id, OBJ_TYPE['sup_policy']);
        $this->policyService->conclusionUpdate($data['conclusion'], $id);
        $this->policyService->itemUpdate($data['item'], $id);

        $this->policyService->govAgenUpdate($data['gov_agen']??[], $id,OBJ_TYPE['sup_policy']);
        $this->summarizeService->relationSummarizeUpdate($data['summarize']??[], $id);
        $this->policyService->relationFileUpdate($data['file']??[], $id, OBJ_TYPE['sup_policy']);
        $this->relationPolicyUpdate($data, $id);
    }

    public function relationPolicyUpdate($data,$id)
    {
        $relation = [];

        $type = $this->policyService->typeHasRelation(OBJ_TYPE['sup_policy']);

        foreach ($type as $k => $v) {
            if (!empty($data[$v])) {
                $relation = array_merge($relation, $data[$v]);
            }
        }

        $this->policyService->relationRelationUpdate($relation, $id, OBJ_TYPE['sup_policy']);

    }

    public function list(ListRequest $request)
    {
        $param = $this->filter($request);
        $param['obj_type'] = OBJ_TYPE['sup_policy'];

        $data = $this->policyService->list($param);

        return codeRender(Code::OK, $data);
    }

    public function detail(DetailRequest $request)
    {
        $data = $this->policyService->supPolicyDetail($request->input('id'));

        return codeRender(Code::OK, $data);
    }


}