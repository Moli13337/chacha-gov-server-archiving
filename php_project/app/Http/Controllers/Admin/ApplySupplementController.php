<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/25
 * Time: 15:14
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Exceptions\QueryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApplySupplement\DeleteRequest;
use App\Http\Requests\ApplySupplement\InvoiceDeleteRequest;
use App\Http\Requests\ApplySupplement\InvoiceListRequest;
use App\Http\Requests\ApplySupplement\InvoiceUpdateRequest;
use App\Http\Requests\ApplySupplement\ListRequest;
use App\Http\Requests\ApplySupplement\SaveRequest;
use App\Http\Requests\ApplySupplement\StoreRequest;
use App\Http\Requests\ApplySupplement\UpdateRequest;
use App\Repositories\Apply\ApplyFileExceptionRepository;
use App\Repositories\Apply\ApplyFileRepository;
use App\Repositories\Apply\ApplyRepository;
use App\Repositories\Enterprise\EnterpriseRepository;
use App\Repositories\Policy\ProjectRepository;
use Illuminate\Support\Facades\DB;

class ApplySupplementController extends Controller
{

    protected $applyRepository;
    public function __construct(ApplyRepository $applyRepository)
    {
        $this->applyRepository = $applyRepository;
    }

    /**
     * FUNCTION_NAME : store
     *
     * 补录的申报值保留基本信息
     * @param StoreRequest $request
     */
    public function store(StoreRequest $request)
    {
        $params = $this->filter($request);
        $params['is_supplement'] = APPLY_SUPPLEMENT['yes'];
        $params['number'] = $this->applyRepository->getMaxCode();
        $params['apply_status'] = APPLY_STATUS['nine'];
        $params['audit_status'] = PRE_AUDIT_STATUS['wait'];
        // 组装部分数据
        $project = app(ProjectRepository::class)->detail($params['project_id']);
        $params['project_name'] = $project['name'];
        $params['mold_id'] = $project['mold_id'];
        $params['policy_name'] = array_get($project['mold']??[], 'name', '');
        $enterprise = app(EnterpriseRepository::class)->detail($params['enterprise_id']);
        $params['enterprise_name'] = $enterprise['name'];
        $params['created_staff_id'] = (int)getLoginStaff('id');
        $this->applyRepository->storeRepository($params);
        return codeRender(Code::OK);
    }

    public function update(UpdateRequest $request)
    {
        $params = $this->filter($request);
        $detail = $this->applyRepository->getSupplementById($request->input('id'), ['id']);
        if (empty($detail)) {
            return codeRender(Code::APPLY_SUPPLEMENT_EXIST_ERROR);
        }

        // 组装部分数据
        $project = app(ProjectRepository::class)->detail($params['project_id']);
        $params['project_name'] = $project['name'];
        $params['mold_id'] = $project['mold_id'];
        $params['policy_name'] = array_get($project['mold']??[], 'name', '');
        $enterprise = app(EnterpriseRepository::class)->detail($params['enterprise_id']);
        $params['enterprise_name'] = $enterprise['name'];
        $this->applyRepository->updateSupplement($params);
        return codeRender(Code::OK);
    }

    public function delete(DeleteRequest $request)
    {
        $params = $this->filter($request);
        $detail = $this->applyRepository->getSupplementById($request->input('id'), ['id']);
        if (empty($detail)) {
            return codeRender(Code::APPLY_SUPPLEMENT_EXIST_ERROR);
        }

        try {
            DB::beginTransaction();
            $this->applyRepository->deleteSupplement($params['id']);
            app(ApplyFileRepository::class)->deleteByApply($params['id']);
            app(ApplyFileExceptionRepository::class)->refreshApply($params['id']);
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

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        $params['order_by'] = ['id' => 'DESC'];
        $column = ['id', 'enterprise_id','enterprise_name', 'project_name', 'number',
            'project_id',
            'policy_name',
            'apply_money',
            'support_content',
            'allocation_time',
            'submit_time',
            'created_at',
            'created_staff_id',
        ];
        $data = $this->applyRepository->supplementList($params, $column);
        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }

        foreach ($data['data'] as $key => &$value) {
            $value['staff_name'] = array_get($value['staff'], 'name', '');
            unset($value['staff']);
        }
        return codeRender(Code::OK, $data);
    }

    public function saveInvoice(SaveRequest $request)
    {
        $detail = $this->applyRepository->getSupplementById($request->input('apply_id'), ['id']);
        if (empty($detail)) {
            return codeRender(Code::APPLY_SUPPLEMENT_EXIST_ERROR);
        }
//        $file = app(ApplyFileRepository::class)->getByApply($request->input('apply_id'), ['id']);
        $invoice = $request->input('invoice', []);
//        $delete = array_diff($has, array_column($invoice, 'id'));
        $store = [];
        $column = ['file_name', 'file_url'];
        foreach ($invoice as $k => $v) {
            if (empty($v['id'])) {
                $store[] = array_merge(
                    array_only($v, $column),
                    [
                        'apply_id' => $request->input('apply_id'),
                        'file_type' => MATERIALS_TYPE['invoice'],
                    ],
                    returnCreatedUpdatedAt()
                );
            }
        }
        try {
            DB::beginTransaction();
            if (!empty($store)) {
                $update_data = [
                    'id' => $request->input('apply_id'),
                    'audit_status' => PRE_AUDIT_STATUS['wait'],
                ];
                $this->applyRepository->updateSupplement($update_data);
                app(ApplyFileRepository::class)->storeBatchRepository($store);
            }
//            if (!empty($delete)) {
//                app(ApplyFileRepository::class)->deleteRepository($delete);
//            }

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

    public function invoiceList(InvoiceListRequest $request)
    {
        $param = $this->filter($request);
        $data = app(ApplyFileRepository::class)->supplementList($param);
        return codeRender(Code::OK, $data);
    }

    public function deleteInvoice(InvoiceDeleteRequest $request)
    {
        $detail = $this->applyRepository->getSupplementById($request->input('apply_id'), ['id']);
        if (empty($detail)) {
            return codeRender(Code::APPLY_SUPPLEMENT_EXIST_ERROR);
        }
        app(ApplyFileRepository::class)->deleteRepository($request->input('file_id'));
        return codeRender(Code::OK);
    }

    public function updateInvoice(InvoiceUpdateRequest $request)
    {
        $param = $this->filter($request);
        $detail = $this->applyRepository->getSupplementById($request->input('apply_id'), ['id']);
        if (empty($detail)) {
            return codeRender(Code::APPLY_SUPPLEMENT_EXIST_ERROR);
        }
        $param = array_except($param, ['apply_id', 'file_id']);
        $param['id'] = $request->input('file_id');

        $keys =  ['invoice_money' => ''];
        foreach ($keys as $key => $value) {
            if (empty($param[$key])) {
                $param[$key] = $value;
            }
        }
        $param['invoice_billing_date'] = empty($param['invoice_billing_date']) ? '' : date('Y-m-d', $param['invoice_billing_date']);
        app(ApplyFileRepository::class)->updateRepository($param);
        return codeRender(Code::OK);
    }
}