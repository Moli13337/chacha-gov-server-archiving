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
use App\Http\Requests\Publicity\DetailRequest;
use App\Http\Requests\Publicity\ListRequest;
use App\Http\Requests\Publicity\SaveRequest;
use App\Http\Requests\Publicity\UpdateRequest;
use App\Repositories\Policy\PolicyRepository;
use App\Support\Collection;
use Illuminate\Support\Facades\DB;

class PublicityController extends Controller
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
        $data['obj_type'] = OBJ_TYPE['publicity'];

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
        $this->policyService->govAgenUpdate($data['gov_agen']??[], $id, OBJ_TYPE['publicity']);
    }

    public function list(ListRequest $request)
    {
        $param = $this->filter($request);
        $param['obj_type'] = OBJ_TYPE['publicity'];

        $data = $this->policyService->list($param);

        return codeRender(Code::OK, $data);
    }

    public function detail(DetailRequest $request)
    {
        $data = $this->policyService->publicityDetail($request->input('id'));

        return codeRender(Code::OK, $data);
    }


}