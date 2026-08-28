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
use App\Repositories\Enterprise\EnterpriseLinkmanRepository;

class EnterpriseLinkmanController extends Controller
{

    protected $enterpriseLinkmanRepository;

    public function __construct(EnterpriseLinkmanRepository $enterpriseLinkmanRepository)
    {
        $this->enterpriseLinkmanRepository = $enterpriseLinkmanRepository;
    }

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        $params['enterprise_id'] = $params['id'];
        $params = array_except($params,'id');
        $params['order_by'] = [
            'created_at' => 'DESC'
        ];

        $data = $this->enterpriseLinkmanRepository->search($params);

        return codeRender(Code::OK, $data);
    }
}