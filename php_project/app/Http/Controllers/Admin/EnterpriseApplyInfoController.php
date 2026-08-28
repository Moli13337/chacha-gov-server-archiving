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
use App\Repositories\Enterprise\EnterpriseApplyInfoRepository;

class EnterpriseApplyInfoController extends Controller
{

    protected $enterpriseApplyInfoRepository;

    public function __construct(EnterpriseApplyInfoRepository $enterpriseApplyInfoRepository)
    {
        $this->enterpriseApplyInfoRepository = $enterpriseApplyInfoRepository;
    }

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        $params['enterprise_id'] = $params['id'];
        $params = array_except($params,'id');
        $params['select_type'] = ENTERPRISE_CENTER_APPLY_LIST['info'];
        $data = app(ApplyChartRepository::class)->getApplyByEnterpriseId($params);

        return codeRender(Code::OK, $data);
    }
}