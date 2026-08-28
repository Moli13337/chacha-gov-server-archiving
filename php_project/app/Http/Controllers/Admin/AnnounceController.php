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
use App\Http\Requests\Announce\DetailRequest;
use App\Http\Requests\Announce\ListRequest;
use App\Http\Requests\Announce\SaveRequest;
use App\Http\Requests\Announce\UpdateRequest;
use App\Repositories\Policy\PolicyRepository;
use App\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnnounceController extends Controller
{

    protected $repository;
    protected $policyService;

    public function __construct(PolicyRepository $repository,
                                PolicyService $policyService)
    {
        $this->repository = $repository;
        $this->policyService = $policyService;
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
        $data['obj_type'] = OBJ_TYPE['announce'];

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
        if (!empty($data['gov_agen'])) {
            $this->policyService->govAgenInsert($data['gov_agen'], $id);
        }

        if (!empty($data['mold']['mold_id'])) {
            $this->policyService->moldInsert($data['mold'], $id);
        }

        if (!empty($data['file'])) {
            $this->policyService->relationFileInsert($data['file'], $id);
        }
        $this->relationPolicyInsert($data, $id);

    }

    public function relationPolicyInsert($data, $id)
    {
        $relation = [];

        $type = $this->policyService->typeHasRelation(OBJ_TYPE['announce']);

        foreach ($type as $k => $v) {
            if (!empty($data[$v])) {
                $relation = array_merge($relation, $data[$v]);
            }
        }

        if (!empty($relation)) {
            $this->policyService->relationRelationInsert($relation, $id, OBJ_TYPE['announce']);
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
        $this->policyService->moldUpdate($data['mold']??[], $id);
        $this->policyService->govAgenUpdate($data['gov_agen']??[], $id, OBJ_TYPE['announce']);
        $this->policyService->relationFileUpdate($data['file']??[], $id, OBJ_TYPE['announce']);
        $this->relationPolicyUpdate($data, $id);
    }

    public function relationPolicyUpdate($data,$id)
    {
        $relation = [];

        $type = $this->policyService->typeHasRelation(OBJ_TYPE['announce']);

        foreach ($type as $k => $v) {
            if (!empty($data[$v])) {
                $relation = array_merge($relation, $data[$v]);
            }
        }
        $this->policyService->relationRelationUpdate($relation, $id, OBJ_TYPE['announce']);
    }

    public function list(ListRequest $request)
    {
        $param = $this->filter($request);
        $param['obj_type'] = OBJ_TYPE['announce'];

        $data = $this->policyService->list($param);

        return codeRender(Code::OK, $data);
    }

    public function detail(DetailRequest $request)
    {
        $data = $this->policyService->announceDetail($request->input('id'));

        $data['mold'] = empty($data['mold']) ? (object)[] : $data['mold'];
        return codeRender(Code::OK, $data);
    }


}