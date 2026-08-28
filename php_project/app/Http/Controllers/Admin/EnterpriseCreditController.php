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
use App\Http\Requests\Credit\ListRequest;
use App\Repositories\Enterprise\EnterpriseCreditRepository;

class EnterpriseCreditController extends Controller
{

    protected $enterpriseCreditRepository;

    public function __construct(EnterpriseCreditRepository $enterpriseCreditRepository)
    {
        $this->enterpriseCreditRepository = $enterpriseCreditRepository;
    }

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        $params['enterprise_id'] = $params['id'];
        $params = array_except($params,'id');
        $params['order_by'] = [
            'id' => 'DESC'
        ];
        $data = $this->enterpriseCreditRepository->list($params);

        if (empty($data)) {
            return codeRender(Code::OK, $data);
        }

        foreach ($data['data'] as $key => &$value) {
            $value['department_name'] = array_get($value['department']??[], 'name','');
            $value['class_first_name'] = array_get($value['class_first']??[], 'name','');
            $value['class_second_name'] = array_get($value['class_second']??[], 'name','');
            unset($value['department']);
            unset($value['class_first']);
            unset($value['class_second']);
        }

        return codeRender(Code::OK, $data);
    }
}