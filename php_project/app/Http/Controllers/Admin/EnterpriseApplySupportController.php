<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/21
 * Time: 17:34
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeOverview\ListRequest;
use App\Http\Requests\EmployeeOverview\SupportOverviewRequest;
use App\Repositories\Apply\ApplyChartRepository;
use App\Repositories\Enterprise\EnterpriseApplySupportRepository;

class EnterpriseApplySupportController extends Controller
{

    protected $enterpriseApplySupportRepository;

    public function __construct(EnterpriseApplySupportRepository $enterpriseApplySupportRepository)
    {
        $this->enterpriseApplySupportRepository = $enterpriseApplySupportRepository;
    }

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        $params['enterprise_id'] = $params['id'];
        $params = array_except($params,'id');
        $params['select_type'] = ENTERPRISE_CENTER_APPLY_LIST['support'];
        $data = app(ApplyChartRepository::class)->getApplyByEnterpriseId($params);

        return codeRender(Code::OK, $data);
    }

    public function overview(SupportOverviewRequest $request)
    {
        $params = ['enterprise_id' => $request->input('id')];
        $list =app(ApplyChartRepository::class)->list($params);
        unset($list['list']);
        return codeRender(Code::OK, $list);

    }
}