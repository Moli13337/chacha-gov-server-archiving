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
use App\Repositories\Enterprise\EnterpriseEmployeeOverviewRepository;

class EnterpriseEmployeeOverviewController extends Controller
{

    protected $enterpriseEmployeeOverviewRepository;

    public function __construct(EnterpriseEmployeeOverviewRepository $enterpriseEmployeeOverviewRepository)
    {
        $this->enterpriseEmployeeOverviewRepository = $enterpriseEmployeeOverviewRepository;
    }

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        $params['enterprise_id'] = $params['id'];
        $params = array_except($params,'id');
        $params['order_by'] = [
            'id' => 'DESC'
        ];

        $data = $this->enterpriseEmployeeOverviewRepository->search($params);

        return codeRender(Code::OK, $data);
    }
}