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
use App\Repositories\Apply\ApplyChartRepository;
use App\Repositories\Enterprise\EnterpriseApplyRepository;

class EnterpriseApplyController extends Controller
{

    protected $enterpriseApplyRepository;

    public function __construct(EnterpriseApplyRepository $enterpriseApplyRepository)
    {
        $this->enterpriseApplyRepository = $enterpriseApplyRepository;
    }

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        $params['enterprise_id'] = $params['id'];
        $params = array_except($params,'id');
        $params['select_type'] = ENTERPRISE_CENTER_APPLY_LIST['apply'];
        $data = app(ApplyChartRepository::class)->getApplyByEnterpriseId($params);

        return codeRender(Code::OK, $data);
    }
}