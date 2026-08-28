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
use App\Http\Requests\Enterprise\DetailRequest;
use App\Repositories\Enterprise\EnterpriseBusinessRepository;

class EnterpriseBusinessController extends Controller
{

    protected $enterpriseBusinessRepository;

    public function __construct(EnterpriseBusinessRepository $enterpriseBusinessRepository)
    {
        $this->enterpriseBusinessRepository = $enterpriseBusinessRepository;
    }

    public function list(DetailRequest $request)
    {
        $params = $this->filter($request);
        $params['enterprise_id'] = $request['id'];
        $params = array_except($params, 'id');
        $params['order_by'] =  [
                'id' => 'DESC'
        ];

        $data = $this->enterpriseBusinessRepository->search($params);

        return codeRender(Code::OK, $data);
    }
}